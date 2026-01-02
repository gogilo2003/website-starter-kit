<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDownloadCategoryRequest;
use App\Http\Requests\UpdateDownloadCategoryRequest;
use Illuminate\Http\Request;
use App\Services\DownloadCategoryService;
use Inertia\Inertia;

class DownloadCategoryController extends Controller
{
    protected $categoryService;

    public function __construct(DownloadCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        // Define parameters for the `all()` method
        $params = [
            'per_page' => $request->input('per_page', 10), // Default to 10 items per page
            'search' => $request->input('search', ''), // Search term
            'paginate' => true, // Always paginate for the index page
            'relations' => ['downloads']
        ];

        // Fetch categories using the service
        $categories = $this->categoryService->all($params);

        // Return the Inertia response with categories
        return Inertia::render('Dashboard/Downloads/Categories', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreDownloadCategoryRequest $request)
    {

        // Create the category using the service
        $category = $this->categoryService->create($request->validated());

        if ($category) {
            return redirect()->back()->with('success', 'Category created successfully.');
        }

        return redirect()->back()->with('error', 'Failed to create category.');
    }

    public function update(UpdateDownloadCategoryRequest $request, $id)
    {

        // Update the category using the service
        $updatedCategory = $this->categoryService->update($id, $request->all());

        if ($updatedCategory) {
            return redirect()->back()->with('success', 'Category updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update category.');
    }

    public function destroy($id)
    {
        // Delete the category using the service
        $success = $this->categoryService->delete($id);

        if ($success) {
            return redirect()->back()->with('success', 'Category deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete category.');
    }

    function activate($id)
    {
        // Activate the category using the service
        $category = $this->categoryService->activate($id);

        if ($category) {
            return redirect()->back()->with('success', 'Category activated successfully.');
        }

        return redirect()->back()->with('success', 'Category deactivated successfully.');
    }
}
