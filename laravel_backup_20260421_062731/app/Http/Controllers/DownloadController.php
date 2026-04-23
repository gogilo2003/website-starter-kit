<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\DownloadService;
use App\Services\FileUploadService;
use App\Services\DownloadCategoryService;

class DownloadController extends Controller
{
    protected DownloadService $downloadService;
    protected DownloadCategoryService $categoryService;
    protected FileUploadService $fileUploadService;

    public function __construct(DownloadService $downloadService, DownloadCategoryService $categoryService, FileUploadService $fileUploadService)
    {
        $this->downloadService = $downloadService;
        $this->categoryService = $categoryService;
        $this->fileUploadService = $fileUploadService;
    }

    public function index(Request $request, int $category_id = null)
    {
        // Define parameters for the `all()` method
        $params = [
            'per_page' => $request->input('per_page', 10), // Default to 10 items per page
            'search' => $request->input('search', ''), // Search term
            'paginate' => true, // Always paginate for the index page
        ];
        $data = [];
        if ($category_id) {
            $params['category_id'] = $category_id;
            $data['category'] = $this->categoryService->find($category_id);
        }
        // Fetch downloads using the service
        $data['downloads'] = $this->downloadService->all($params);

        // Return the Inertia response with downloads
        return Inertia::render('Dashboard/Downloads/Index', $data);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file',
            'category' => 'required|exists:download_categories,id',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('file')) {
            $file = $this->fileUploadService->handle($request->file('file'));
            $data['file_path'] = $file['path'];
            $data['file_name'] = $file['name'];
            $data['file_type'] = $file['type'];
            $data['file_size'] = $file['size'];
        }

        // Create the download using the service
        $download = $this->downloadService->create($data);

        if ($download) {
            return redirect()->back()->with('success', 'Download created successfully.');
        }

        return redirect()->back()->with('error', 'Failed to create download.');
    }

    public function update(Request $request, $id)
    {
        $request->merge(['id' => $id]);
        // Validate the request data
        $data = $request->validate([
            'id' => 'required|integer|exists:downloads,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file',
            'category' => 'nullable|exists:download_categories,id',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('file')) {
            $file = $this->fileUploadService->handle($request->file('file'));
            $data['file_path'] = $file['path'];
            $data['file_name'] = $file['name'];
            $data['file_type'] = $file['type'];
            $data['file_size'] = $file['size'];
        }

        // Update the download using the service
        $updatedDownload = $this->downloadService->update($id, $data);

        if ($updatedDownload) {
            return redirect()->back()->with('success', 'Download updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update download.');
    }

    public function destroy($id)
    {
        // Delete the download using the service
        $success = $this->downloadService->delete($id);

        if ($success) {
            return redirect()->back()->with('success', 'Download deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete download.');
    }

    public function activate($id)
    {
        // Toggle the is_active field using the service
        $success = $this->downloadService->activate($id);
        $message = $success ? 'Download activated successfully.' : 'Download deactivated successfully.';
        return redirect()->back()->with('success', $message);
    }

    public function feature($id)
    {
        // Toggle the is_featured field using the service
        $success = $this->downloadService->feature($id);

        $message = $success ? 'Download featured successfully.' : 'Download un-featured successfully.';
        return redirect()->back()->with('success', $message);
    }
}
