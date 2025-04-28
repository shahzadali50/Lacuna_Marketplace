<?php

namespace App\Http\Controllers;

use Str;
use Exception;
use Inertia\Inertia;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $locale = session('locale', App::getLocale());

            // Load products with brand, category, and their translations for current locale
            $products = Product::where('user_id', Auth::id())
                ->with([
                    'brand' => fn($q) => $q->with(['brand_translations' => fn($q) => $q->where('lang', $locale)]),
                    'category' => fn($q) => $q->with(['category_translations' => fn($q) => $q->where('lang', $locale)]),
                    'product_translations' => fn($q) => $q->where('lang', $locale),
                ])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Transform products to include translated fields
            $products->getCollection()->transform(function ($product) use ($locale) {
                return [
                    'id' => $product->id,
                    'slug' => $product->slug,
                    'name' => $product->product_translations->first()?->name ?? $product->name,
                    'description' => $product->product_translations->first()?->description ?? $product->description,
                    'stock' => $product->stock,
                    'purchase_price' => $product->purchase_price,
                    'sale_price' => $product->sale_price,
                    'brand_id' => $product->brand_id,
                    'category_id' => $product->category_id,
                    'created_at' => $product->created_at->format('Y-m-d H:i'),
                    'brand_name' => $product->brand?->brand_translations->first()?->name ?? $product->brand?->name ?? 'N/A',
                    'category_name' => $product->category?->category_translations->first()?->name ?? $product->category?->name ?? 'N/A',
                ];
            });

            // Load brands with translations
            $brands = Brand::with(['brand_translations' => fn($q) => $q->where('lang', $locale)])
                ->get()
                ->map(function ($brand) use ($locale) {
                    return [
                        'id' => $brand->id,
                        'category_id' => $brand->category_id,
                        'name' => $brand->brand_translations->first()?->name ?? $brand->name,
                    ];
                });

            // Load categories with translations
            $categories = Category::with(['category_translations' => fn($q) => $q->where('lang', $locale)])
                ->get()
                ->map(function ($category) use ($locale) {
                    return [
                        'id' => $category->id,
                        'name' => $category->category_translations->first()?->name ?? $category->name,
                    ];
                });

            return Inertia::render('admin/product/Index', [
                'products' => $products,
                'brands' => $brands,
                'categories' => $categories,
                'translations' => __('messages'),
                'locale' => $locale,
            ]);

        } catch (\Throwable $e) {
            \Log::error('Failed to load products in index(): ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while loading products.');
        }
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
            'description' => 'required|string',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'thumnail_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallary_img' => 'required',
            'gallary_img.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'discount' => 'nullable|integer|min:0|max:100',
            'final_price' => 'required|numeric|min:0',
            'feature' => 'nullable|boolean',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
        ]);

        DB::beginTransaction();

        try {
            // Store thumbnail image
            $thumbnailPath = $request->file('thumnail_img')->store('products/thumbnails', 'public');

            // Store gallery images
            $galleryPaths = [];
            if ($request->hasFile('gallary_img')) {
                foreach ($request->file('gallary_img') as $image) {
                    $path = $image->store('products/gallery', 'public');
                    $galleryPaths[] = $path;
                }
            }

            // Create product
            $product = Product::create([
                'name' => $request->name,
                'slug' => \Str::slug($request->name),
                'description' => $request->description,
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'thumnail_img' => $thumbnailPath,
                'gallary_img' => json_encode($galleryPaths),
                'stock' => $request->stock,
                'status' => $request->status,
                'purchase_price' => $request->purchase_price,
                'sale_price' => $request->sale_price,
                'discount' => $request->discount ?? 0,
                'final_price' => $request->final_price,
                'feature' => $request->feature ?? false,
                'barcode' => $request->barcode,
                'user_id' => Auth::id(),
            ]);

            // Create product translation
            $product->product_translations()->create([
                'name' => $request->name,
                'description' => $request->description,
                'lang' => session('locale', App::getLocale()),
                'user_id' => Auth::id(),
            ]);

            // Create product log
            $user = Auth::user();
            $note = 'Product "' . $product->name . '" created by ' . ($user->name ?? 'Unknown User');
            ProductLog::create([
                'note' => $note,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'user_id' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed: ' . $e->getMessage());
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
