<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\CategoryLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\CategoryTranslation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Stichoza\GoogleTranslate\GoogleTranslate;



class CategoryController extends Controller
{
    public function index()
    {
        $categories= Category::where('user_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->paginate(5);
        return Inertia::render('admin/category/Index', compact('categories'));
    }
    public function store(Request $request)
{
    $request->validate([
        'name' => [
            'required', 'string', 'max:255',
            Rule::unique('categories', 'name')->where('user_id', Auth::id())->whereNull('deleted_at'),
        ],
        'description' => 'required|string',
        'image' => [
            'required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048',
        ],
    ]);

    DB::beginTransaction();
    try {
        // Upload image
        $originalName = $request->file('image')->getClientOriginalName();
        $filename = pathinfo($originalName, PATHINFO_FILENAME) . '_' . substr(md5(uniqid()), 0, 6) . '.' . pathinfo($originalName, PATHINFO_EXTENSION);
        $imagePath = $request->file('image')->storeAs('categories', $filename, 'public');

        // Create category
        $category = Category::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        // Log category
        $user = Auth::user();
        CategoryLog::create([
            'note' => 'Category "' . $category->name . '" created by ' . ($user->name ?? 'Unknown'),
            'category_id' => $category->id,
            'category_name' => $category->name,
            'user_id' => $user->id,
        ]);

        // 🟢 Automatic translation insert using GoogleTranslate (stichoza)
        $tr = new GoogleTranslate(); // auto-detect source language

        foreach (['en', 'es', 'ja'] as $lang) {
            $tr->setTarget($lang);
            CategoryTranslation::updateOrCreate(
                ['lang' => $lang, 'category_id' => $category->id],
                [
                    'name' => $tr->translate($request->name),
                    'description' => $tr->translate($request->description),
                ]
            );
        }

        DB::commit();
        return redirect()->back()->with('success', 'Category created successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Category creation failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Something went wrong! Please try again.');
    }
}




    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category) {
            $user = Auth::user();
            $note = 'Category "' . $category->name . '" Deleted by ' . ($user->name ?? 'Unknown User');
            CategoryLog::create([
                'note' => $note,
                'category_name' => $category->name,
                'category_id' => $category->id,
                'user_id' => Auth::id(),
            ]);
            $category->delete();
            $category->brands()->delete();
            return redirect()->back()->with('success', 'category deleted successfully.');
        }

        return redirect()->back()->with('error', 'category not found.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories', 'name')
                    ->where('user_id', Auth::id())
                    ->whereNull('deleted_at')
                    ->ignore($id),
            ],
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $category = Category::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        DB::beginTransaction();
        try {
            $oldName = $category->name; // ✅ Save old name before updating
            $updateData = [
                'name' => $request->name,
                'description' => $request->description,
                'slug' => Str::slug($request->name),
            ];

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }

                // Get the original file name
                $originalName = $request->file('image')->getClientOriginalName();

                // Create a unique filename by adding a small random string to prevent conflicts
                $filename = pathinfo($originalName, PATHINFO_FILENAME) . '_' . substr(md5(uniqid()), 0, 6) . '.' . pathinfo($originalName, PATHINFO_EXTENSION);

                // Store the file with the custom filename
                $imagePath = $request->file('image')->storeAs('categories', $filename, 'public');
                $updateData['image'] = $imagePath;
            }

            $category->update($updateData);

            $user = Auth::user();
            $note = 'Category "' . $oldName . '" updated to "' . $category->name . '" by ' . ($user->name ?? 'Unknown User');

            CategoryLog::create([
                'note' => $note,
                'category_id' => $category->id,
                'category_name' => $category->name, // ✅ Store updated name
                'user_id' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Category updated successfully.');

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Category update failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function category_log(){

        $CategoryLog = CategoryLog::with('user')
        ->where('user_id', Auth::id())
        ->latest()
        ->paginate(10);

        return Inertia::render('admin/category/CategoryLog', [
            'CategoryLog' => $CategoryLog,
        ]);
    }
}
