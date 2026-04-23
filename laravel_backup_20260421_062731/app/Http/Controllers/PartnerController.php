<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Partner;
use Illuminate\Support\Str;
use App\Http\Requests\StorePartnerRequest;
use App\Http\Requests\UpdatePartnerRequest;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partners = Partner::all();
        return Inertia::render('Dashboard/Partners/Index', [
            "partners" => $partners
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePartnerRequest $request)
    {
        $partner = new Partner();
        $partner->title = $request->title;
        $partner->slug = Str::slug($request->title);
        $partner->website = $request->website;
        $partner->description = $request->description;
        if ($request->hasFile('logo')) {
            $partner->logo = $request->logo->storePublicly('partners', ['disk' => 'public']);
        }
        $partner->save();
        return redirect()->back()->with('success', 'Partner created');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePartnerRequest $request, Partner $partner)
    {
        $partner->title = $request->title;
        $partner->slug = Str::slug($request->title);
        $partner->website = $request->website;
        $partner->description = $request->description;
        if ($request->hasFile('logo')) {
            $partner->logo = $request->logo->storePublicly('partners', ['disk' => 'public']);
        }
        $partner->save();

        return redirect()->back()->with('success', 'Partner updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partner $partner)
    {
        $partner->delete();
        return redirect()->back()->with('success', 'Partner deleted');
    }
    /**
     * Publish/Unpublish Partner
     *
     * @param Partner $partner
     */
    function publish(Partner $partner)
    {
        $partner->published = !$partner->published;
        $partner->save();
        return redirect()
            ->back()
            ->with('success', sprintf('Partner %s', $partner->published ? 'Published' : 'Un-Published'));
    }
    /**
     * Promote/Demote partner to/from front page
     *
     * @param Partner $partner
     */
    function promote(Partner $partner)
    {
        $partner->front = !$partner->front;
        $partner->save();
        return redirect()->back()->with('success', sprintf('Partner %s front page', $partner->front ? 'promoted to' : 'demoted from'));
    }
}
