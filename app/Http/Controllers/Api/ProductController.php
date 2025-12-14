<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductImage;
use App\Helpers\ApiResponse;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('company_id', Auth::user()->company_id)->with('images')->get();
        return ApiResponse::success($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'cost_price' => 'required|numeric|min:0',
            'retail_price' => 'required|numeric|min:0',
            'margin' => 'required|numeric',
            'discounted_price' => 'sometimes|numeric|min:0',
            'net_weight' => 'sometimes|string',
            'category' => 'sometimes|string',
            'note' => 'sometimes|string|nullable',
            'description' => 'nullable|string',
            'type' => 'required|in:1,2',
            'sku' => 'sometimes|string|unique:products,sku',
            'barcode' => 'sometimes|string|unique:products,barcode',
            'upc' => 'sometimes|string',
            'reorder_point' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);
        $validated['company_id'] = Auth::user()->company_id;
        $product = Product::create($validated);

        // Handle multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image_url' => Storage::url($path)]);
            }
        }

        return ApiResponse::success($product->load('images'), 'Product created', 201);
    }

    public function show($id)
    {
        $product = Product::where('company_id', Auth::user()->company_id)->with('images')->findOrFail($id);
        return ApiResponse::success($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'quantity' => 'sometimes|integer|min:0',
            'cost_price' => 'sometimes|numeric|min:0',
            'retail_price' => 'sometimes|numeric|min:0',
            'margin' => 'sometimes|numeric',
            'discounted_price' => 'sometimes|numeric|min:0',
            'net_weight' => 'sometimes|string',
            'category' => 'sometimes|string',
            'note' => 'sometimes|string|nullable',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:1,2',
            'sku' => 'sometimes|string|unique:products,sku,' . $id,
            'barcode' => 'sometimes|string|unique:products,barcode,' . $id,
            'upc' => 'sometimes|string',
            'reorder_point' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);
        $product->update($validated);
        return ApiResponse::success($product->load('images'));
    }

    public function destroy($id)
    {
        $product = Product::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $product->delete();
        return ApiResponse::success(null, 'Deleted');
    }

    public function uploadImages(Request $request, $id)
    {
        $product = Product::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:2048',
        ]);
        $urls = [];
        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $product->images()->create(['image_url' => Storage::url($path)]);
            $urls[] = Storage::url($path);
        }
        return ApiResponse::success(['image_urls' => $urls], 'Images uploaded', 201);
    }

    public function findByBarcode(Request $request)
    {
        $barcode = $request->input('barcode');
        
        $product = Product::where('company_id', Auth::user()->company_id)
                          ->where('is_active', true)
                          ->where(function($q) use ($barcode) {
                              $q->where('barcode', $barcode)
                                ->orWhere('sku', $barcode)
                                ->orWhere('upc', $barcode);
                          })
                          ->with('images')
                          ->firstOrFail();
        
        return ApiResponse::success($product);
    }

    protected function authorizeCompany(Product $product)
    {
        if ($product->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
    }
} 