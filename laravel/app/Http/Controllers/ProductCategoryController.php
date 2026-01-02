<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Services\ProductCategoryService;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;

class ProductCategoryController extends Controller
{
    protected ProductCategoryService $service;

    public function __construct(ProductCategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the product categories.
     */
    public function index(Request $request): Response
    {
        $params = [
            'filters' => $request->only(['name', 'slug']),
            'sort_by' => $request->get('sort_by', 'name'),
            'sort_dir' => $request->get('sort_dir', 'asc'),
            'per_page' => $request->get('per_page', 6),
            'paginate' => true,
        ];

        $categories = $this->service->getAllProductCategories($params, mapped: true);

        return Inertia::render('Dashboard/Products/Categories', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created product category.
     */
    public function store(StoreProductCategoryRequest $request): RedirectResponse
    {

        $this->service->createProductCategory($request->validated());

        return redirect()->back()->with('success', 'Product category created successfully.');
    }

    /**
     * Update an existing product category.
     */
    public function update(UpdateProductCategoryRequest $request, int $id): RedirectResponse
    {
        $this->service->updateProductCategory($id, $request->validated());

        return redirect()->back()->with('success', 'Product category updated successfully.');
    }

    /**
     * Remove the specified product category.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->deleteProductCategory($id);

        return redirect()->back()->with('success', 'Product category deleted successfully.');
    }

    /**
     * Publish or unpublish a category.
     * @param int $id
     * @return RedirectResponse
     */
    public function publish(int $id): RedirectResponse
    {
        $res = $this->service->publishProductCategory($id);
        return redirect()->back()->with('success', sprintf('Product category %s', $res ? 'published.' : 'unpublished.'));
    }

    /**
     * Promote or demote a category.
     * @param int $id
     * @return RedirectResponse
     */
    public function promote(int $id): RedirectResponse
    {
        $res = $this->service->promoteProductCategory($id);
        return redirect()->back()->with('success', sprintf('Product category %s', $res ? 'promoted.' : 'demoted.'));
    }
}
