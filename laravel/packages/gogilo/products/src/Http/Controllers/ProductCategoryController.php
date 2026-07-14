<?php

namespace Gogilo\Products\Http\Controllers;

use Gogilo\Products\Http\Requests\StoreProductCategoryRequest;
use Gogilo\Products\Http\Requests\UpdateProductCategoryRequest;
use Gogilo\Products\Services\ProductCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class ProductCategoryController extends Controller
{
    protected ProductCategoryService $service;

    public function __construct(ProductCategoryService $service)
    {
        $this->service = $service;
    }

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

    public function store(StoreProductCategoryRequest $request): RedirectResponse
    {
        $this->service->createProductCategory($request->validated());

        return redirect()->back()->with('success', 'Product category created successfully.');
    }

    public function update(UpdateProductCategoryRequest $request, int $id): RedirectResponse
    {
        $this->service->updateProductCategory($id, $request->validated());

        return redirect()->back()->with('success', 'Product category updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->service->deleteProductCategory($id);

        return redirect()->back()->with('success', 'Product category deleted successfully.');
    }

    public function publish(int $id): RedirectResponse
    {
        $res = $this->service->publishProductCategory($id);

        return redirect()->back()->with('success', sprintf('Product category %s', $res ? 'published.' : 'unpublished.'));
    }

    public function promote(int $id): RedirectResponse
    {
        $res = $this->service->promoteProductCategory($id);

        return redirect()->back()->with('success', sprintf('Product category %s', $res ? 'promoted.' : 'demoted.'));
    }
}
