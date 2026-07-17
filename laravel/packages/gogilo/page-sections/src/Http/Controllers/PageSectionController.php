<?php

namespace Gogilo\PageSections\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Gogilo\PageSections\Services\ElementService;
use Gogilo\PageSections\Services\PageSectionService;
use Gogilo\PageSections\Http\Requests\SyncElementsRequest;
use Gogilo\PageSections\Http\Requests\StorePageSectionRequest;
use Gogilo\PageSections\Http\Requests\UpdatePageSectionRequest;
use Illuminate\Routing\Controller;

class PageSectionController extends Controller
{
    protected PageSectionService $pageSectionService;
    protected ElementService $elementService;

    public function __construct(PageSectionService $pageSectionService, ElementService $elementService)
    {
        $this->pageSectionService = $pageSectionService;
        $this->elementService = $elementService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 15);

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

        $this->pageSectionService->createPageSection($validatedData);

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
