<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('user_id', Auth::id())
        ->with( 'brand')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            $brands = Brand::all();

        return Inertia::render('admin/product/Index', compact('products', 'brands'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->whereNull('deleted_at'),
            ],
            'description' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
        ]);
        DB::beginTransaction();

        try {
           $product= Product::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'brand_id' => $request->brand_id,
                'user_id' => Auth::id(),

            ]);

            if ($product) {
                $user = Auth::user();
                $note = 'Brand "' . $product->name . '" created by ' . ($user->name ?? 'Unknown User');

                ProductLog::create([
                    'note' => $note,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'user_id' => Auth::id(),
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            // 🛠 Debugging: Log error
            Log::error('Brand creation failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function related_product_list($slug)
    {
        $brand = Brand::where('slug', $slug)->first();

        if ($brand) {
            $products = $brand->products()
                ->with(['brand'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return Inertia::render('admin/product/BrandProductList', compact('products', 'brand'));
        } else {
            return redirect()->back()->with('error', 'Record Not Found');
        }
    }
    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $user = Auth::user();
            $note = 'Product "' . $product->name . '" Deleted by ' . ($user->name ?? 'Unknown User');
            ProductLog::create([
                'note' => $note,
                'product_name' => $product->name,
                'product_id' => $product->id,
                'user_id' => Auth::id(),
            ]);
            $product->delete();
            $product->purchaseProducts()->delete();
            return redirect()->back()->with('success', 'Product deleted successfully.');
        }
        return redirect()->back()->with('error', 'Product not found.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('products', 'name')
                    ->where('user_id', Auth::id())
                    ->whereNull('deleted_at')
                    ->ignore($id),
            ],
            'description' => 'nullable|string',
        ]);
        $product = Product::find($id);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        DB::beginTransaction();
        try {
            $oldName = $product->name;
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'slug' => Str::slug($request->name),
            ]);

            $user = Auth::user();
            $note = 'Product "' . $oldName . '" updated to "' . $product->name . '" by ' . ($user->name ?? 'Unknown User');

            ProductLog::create([
                'note' => $note,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'user_id' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Product updated successfully.');

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Brand update failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }
    public function product_log()
    {
        $productLogs = ProductLog::with('user')->where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(10);
        return Inertia::render('admin/product/ProductLog', [
            'productLog' => $productLogs,
        ]);
    }

}
