<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Brand;
use App\Models\BrandLog;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class BrandController extends Controller
{

    public function index()
    {
        $brands = Brand::where('user_id', Auth::id())
        ->with( 'category')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            $categories = Category::all();

            return Inertia::render('admin/brand/Index', compact('brands', 'categories'));
    }
    public function related_brand_list($slug)
    {
        $category = Category::where('slug', $slug)->first();

        if ($category) {
            $brands = $category->brands()
                ->with(['user', 'category'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return Inertia::render('admin/brand/CategoryBrandList', compact('brands', 'category'));
        } else {
            return redirect()->back()->with('error', 'Record Not Found');
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('brands', 'name')->whereNull('deleted_at'),
            ],
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);
        DB::beginTransaction();

        try {
            $brand = Brand::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'category_id' => $request->category_id,

            ]);

            if ($brand) {
                $user = Auth::user();
                $note = 'Brand "' . $brand->name . '" created by ' . ($user->name ?? 'Unknown User');

                BrandLog::create([
                    'note' => $note,
                    'brand_id' => $brand->id,
                    'brand_name' => $brand->name,
                    'user_id' => Auth::id(),
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Brand created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            // 🛠 Debugging: Log error
            Log::error('Brand creation failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('brands', 'name')
                    ->where('user_id', Auth::id())
                    ->whereNull('deleted_at')
                    ->ignore($id),
            ],
            'description' => 'nullable|string',
        ]);
        $brand = Brand::find($id);

        if (!$brand) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        DB::beginTransaction();
        try {
            $oldName = $brand->name; // ✅ Save old name before updating
            $brand->update([
                'name' => $request->name,
                'description' => $request->description,
                'slug' => Str::slug($request->name),
            ]);

            $user = Auth::user();
            $note = 'Brand "' . $oldName . '" updated to "' . $brand->name . '" by ' . ($user->name ?? 'Unknown User');

            BrandLog::create([
                'note' => $note,
                'brand_id' => $brand->id,
                'brand_name' => $brand->name, // ✅ Store updated name
                'user_id' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Brand updated successfully.');

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Brand update failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function destroy($id)
    {
        $brand = Brand::find($id);
        if ($brand) {
            $user = Auth::user();
            $note = 'Brand "' . $brand->name . '" Deleted by ' . ($user->name ?? 'Unknown User');
            BrandLog::create([
                'note' => $note,
                'brand_name' => $brand->name,
                'brand_id' => $brand->id,
                'user_id' => Auth::id(),
            ]);
            $brand->delete();
            $brand->products()->delete();
            return redirect()->back()->with('success', 'Brand deleted successfully.');
        }
        return redirect()->back()->with('error', 'Brand not found.');
    }
    public function brand_log(){

        $brandLog = BrandLog::with('user') // Eager load user relation first
        ->where('user_id', Auth::id()) // Filter for logged-in user
        ->latest() // Same as orderBy('created_at', 'desc')
        ->paginate(10);

        return Inertia::render('admin/brand/BrandLog', [
            'BrandLog' => $brandLog,
        ]);
    }
}
