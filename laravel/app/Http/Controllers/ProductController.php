<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Support\Util;
use App\Models\Picture;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Services\ProductService;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductCategoryService;

class ProductController extends Controller
{
    protected ProductService $productService;
    protected ProductCategoryService $productCategoryService;
    public function __construct()
    {
        $this->productService = app(ProductService::class);
        $this->productCategoryService = app(ProductCategoryService::class);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(int $category = null)
    {
        $params = ['paginate' => true, "per_page" => 5];
        $productCategory = null;
        if ($category) {
            $params['filters'] = [
                'category_id' => $category
            ];
            $productCategory = $this->productCategoryService->getProductCategoryById($category, true);
        }
        $products = $this->productService->getAllProducts($params, true);
        return Inertia::render('Dashboard/Products/Index', ['products' => $products, 'category' => $productCategory]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        dd($request->all());
        $data = [
            "title" => $request->title,
            "slug" => Str::slug($request->title),
            "summary" => $request->summary,
            "content" => $request->description,
            "category" => $request->category,
            "brand" => $request->brand,
            "features" => $request->input('features'),
            "published" => $request->published ? true : false,
            "front" => $request->front ? true : false,
        ];
        if ($request->hasFile('picture')) {
            $data['picture'] = $request->picture;
        }
        $this->productService->createProduct($data);

        return redirect()->back()->with('success', 'Product has been stored');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, int $id)
    {
        $data = [
            "title" => $request->title,
            "slug" => Str::slug($request->title),
            "summary" => $request->summary,
            "content" => $request->description,
            "category" => $request->category,
            "brand" => $request->brand,
            "features" => $request->features,
        ];

        if ($request->hasFile('picture')) {
            $data['picture'] = $request->picture;
        }

        $this->productService->updateProduct($id, $data);
        return redirect()->back()->with('success', 'Product has been updated');
    }

    /**
     * Publish/Unpublish the specified resource from storage.
     */
    public function publish(Product $product)
    {
        $product->published = $product->published ? 0 : 1;
        $product->save();
        return redirect()->back()->with('success', sprintf('Product has been %s', $product->published ? 'Published' : 'Unpublished'));
    }

    /**
     * Promote/Demote the specified resource from storage.
     */
    public function promote(Product $product)
    {
        $product->front = $product->front ? 0 : 1;
        $product->save();
        return redirect()->back()->with('success', sprintf('Product has been %s', $product->front ? 'Promoted' : 'Demoted'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->pictures->count()) {
            foreach ($product->pictures as $picture) {
                if (Storage::disk('public')->exists($picture->name)) {
                    Storage::disk('public')->delete($picture->name);
                }
            }
        }
        $product->delete();
        return redirect()->back()->with('success', 'Product has been deleted');
    }
}
