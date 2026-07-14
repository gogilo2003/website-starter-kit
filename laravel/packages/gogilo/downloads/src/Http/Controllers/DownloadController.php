<?php

namespace Gogilo\Downloads\Http\Controllers;

use App\Services\FileUploadService;
use Gogilo\Downloads\Services\DownloadCategoryService;
use Gogilo\Downloads\Services\DownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DownloadController
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

    public function index(Request $request, ?int $category_id = null)
    {
        $params = [
            'per_page' => $request->input('per_page', 10),
            'search' => $request->input('search', ''),
            'paginate' => true,
        ];

        $data = [];

        if ($category_id) {
            $params['category_id'] = $category_id;
            $data['category'] = $this->categoryService->find($category_id);
        }

        $data['downloads'] = $this->downloadService->all($params);

        return Inertia::render('Dashboard/Downloads/Index', $data);
    }

    public function store(Request $request)
    {
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

        $download = $this->downloadService->create($data);

        if ($download) {
            return redirect()->back()->with('success', 'Download created successfully.');
        }

        return redirect()->back()->with('error', 'Failed to create download.');
    }

    public function update(Request $request, $id)
    {
        $request->merge(['id' => $id]);

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

        $updatedDownload = $this->downloadService->update($id, $data);

        if ($updatedDownload) {
            return redirect()->back()->with('success', 'Download updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update download.');
    }

    public function destroy($id)
    {
        $success = $this->downloadService->delete($id);

        if ($success) {
            return redirect()->back()->with('success', 'Download deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete download.');
    }

    public function activate($id)
    {
        $success = $this->downloadService->activate($id);
        $message = $success ? 'Download activated successfully.' : 'Download deactivated successfully.';

        return redirect()->back()->with('success', $message);
    }

    public function feature($id)
    {
        $success = $this->downloadService->feature($id);
        $message = $success ? 'Download featured successfully.' : 'Download un-featured successfully.';

        return redirect()->back()->with('success', $message);
    }
}
