<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Services\BrandService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct(
        private BrandService $brandService
    ) {}

    /**
     * Display a listing of brands
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 15);

        $params = [
            'paginate' => true,
            'per_page' => $request->input('per_page', 15),
            'search' => $request->input('search'),
            'order_by' => $request->input('sort_by', 'created_at'),
            'order_direction' => $request->input('sort_order', 'desc'),
        ];

        $brands = $this->brandService->fetchAllBrands($params);

        return Inertia::render('Dashboard/Brands/Index', [
            'brands' => $brands,
            'filters' => [
                'search' => $search,
                'perPage' => $perPage,
            ],
        ]);
    }

    /**
     * Store a newly created brand
     */
    public function store(StoreBrandRequest $request)
    {
        try {
            $validated = $this->brandService->validateBrandData($request->validated());
            $this->brandService->createNewBrand($validated);

            return redirect()->route('brands.index')
                ->with('success', 'Brand created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create brand: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified brand
     */
    public function update(UpdateBrandRequest $request, $id)
    {
        try {
            $validated = $this->brandService->validateBrandData($request->validated());
            $this->brandService->updateExistingBrand($id, $validated);

            return redirect()->route('brands.index')
                ->with('success', 'Brand updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update brand: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified brand
     */
    public function destroy($id)
    {
        try {
            $this->brandService->deleteBrandPermanently($id);

            return redirect()->route('brands.index')
                ->with('success', 'Brand deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete brand: ' . $e->getMessage());
        }
    }
}
