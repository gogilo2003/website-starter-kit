<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\ElementService;
use App\Services\PageSectionService;
use App\Http\Requests\SyncElementsRequest;
use App\Http\Requests\StorePageSectionRequest;
use App\Http\Requests\UpdatePageSectionRequest;

class PageSectionController extends Controller
{
    protected $pageSectionService;
    protected $elementService;

    public function __construct(PageSectionService $pageSectionService, ElementService $elementService)
    {
        $this->pageSectionService = $pageSectionService;
        $this->elementService = $elementService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 15); // Default to 15 items per page

        $pageSections = $this->pageSectionService->getAllPageSections($perPage, $search);

        return Inertia::render('Dashboard/PageSections/Index', [
            'page_sections' => $pageSections,
            'search' => $search,
            'elements' => $this->elementService->getAllElements(0)
        ]);
    }

    public function store(StorePageSectionRequest $request)
    {
        $validatedData = $request->validated();

        $pageSection = $this->pageSectionService->createPageSection($validatedData);

        return redirect()
            ->back()
            ->with('success', 'Page section created successfully.');
    }

    public function update(UpdatePageSectionRequest $request, $id)
    {
        $validatedData = $request->validated();

        $updated = $this->pageSectionService->updatePageSection($id, $validatedData);

        if (!$updated) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update page section.');
        }

        return redirect()
            ->back()
            ->with('success', 'Page section updated successfully.');
    }

    public function destroy($id)
    {
        $deleted = $this->pageSectionService->deletePageSection($id);

        if (!$deleted) {
            return redirect()
                ->back()->with('error', 'Failed to delete page section.');
        }

        return redirect()
            ->back()
            ->with('success', 'Page section deleted successfully.');
    }

    public function syncElements(SyncElementsRequest $request)
    {
        $this->pageSectionService
            ->syncElementsWithPageSection(
                $request->page_section,
                $request->elements
            );
        return redirect()
            ->back()
            ->with('success', 'Elements synced with page section successfully.');
    }
}
