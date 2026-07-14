<?php

namespace Gogilo\Downloads\Http\Controllers;

use Gogilo\Downloads\Http\Requests\StoreDownloadCategoryRequest;
use Gogilo\Downloads\Http\Requests\UpdateDownloadCategoryRequest;
use Gogilo\Downloads\Services\DownloadCategoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DownloadCategoryController
{
    protected DownloadCategoryService $categoryService;

    public function __construct(DownloadCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $params = [
            'per_page' => $request->input('per_page', 10),
            'search' => $request->input('search', ''),
            'paginate' => true,
            'relations' => ['downloads'],
        ];

        $categories = $this->categoryService->all($params);

        return Inertia::render('Dashboard/Downloads/Categories', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreDownloadCategoryRequest $request)
    {
        $category = $this->categoryService->create($request->validated());

        if ($category) {
            return redirect()->back()->with('success', 'Category created successfully.');
        }

        return redirect()->back()->with('error', 'Failed to create category.');
    }

    public function update(UpdateDownloadCategoryRequest $request, $id)
    {
        $updatedCategory = $this->categoryService->update($id, $request->all());

        if ($updatedCategory) {
            return redirect()->back()->with('success', 'Category updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update category.');
    }

    public function destroy($id)
    {
        $success = $this->categoryService->delete($id);

        if ($success) {
            return redirect()->back()->with('success', 'Category deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete category.');
    }

    public function activate($id)
    {
        $category = $this->categoryService->activate($id);

        if ($category) {
            return redirect()->back()->with('success', 'Category activated successfully.');
        }

        return redirect()->back()->with('success', 'Category deactivated successfully.');
    }
}
