<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Slide;
use App\Support\Util;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreSlideRequest;
use App\Http\Requests\UpdateSlideRequest;

class SlideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $slides = Slide::orderBy('created_at', 'DESC')->paginate()->through(fn($item) => [
            "id" => $item->id,
            "title" => $item->title,
            "picture" => Util::pictureUrl($item->picture),
            "media_type" => $item->media_type,
            "caption" => $item->caption,
            "published" => $item->published,
        ]);
        return Inertia::render('Dashboard/Slides/Index', ['slides' => $slides]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSlideRequest $request)
    {
        $slide = new Slide();
        $slide->title = $request->title;
        $slide->caption = $request->caption;
        $slide->published = $request->published ? true : false;
        if ($request->hasFile('picture')) {
            $slide->picture = $request->picture->storePublicly('slides', ['disk' => 'public']);
            $file = $request->picture;
            $slide->picture = $file->storePublicly('slides', ['disk' => 'public']);
            $mimeType = $file->getMimeType();
            $slide->media_type = Str::startsWith($mimeType, 'image/') ? 'picture' : (Str::startsWith($mimeType, 'video/') ? 'video' : null);
        }
        $slide->save();
        return redirect()->back()->with('success', 'Slide has been stored');
    }

    /**
     * Display the specified resource.
     */
    public function show(Slide $slide)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Slide $slide)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSlideRequest $request, Slide $slide)
    {
        $slide->title = $request->title;
        $slide->caption = $request->caption;
        if ($request->hasFile('picture')) {
            if (Storage::disk('public')->exists($slide->picture)) {
                Storage::disk('public')->delete($slide->picture);
            }
            $file = $request->picture;
            $slide->picture = $file->storePublicly('slides', ['disk' => 'public']);
            $mimeType = $file->getMimeType();
            $slide->media_type = Str::startsWith($mimeType, 'image/') ? 'picture' : (Str::startsWith($mimeType, 'video/') ? 'video' : null);
        }
        $slide->save();
        return redirect()->back()->with('success', 'Slide has been updated');
    }

    /**
     * Publish/Unpublish the specified resource from storage.
     */
    public function publish(Slide $slide)
    {
        $slide->published = $slide->published ? 0 : 1;
        $slide->save();
        return redirect()->back()->with('success', sprintf('Slide has been %s', $slide->published ? 'Published' : 'Unpublished'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slide $slide)
    {
        if (Storage::disk('public')->exists($slide->picture)) {
            Storage::disk('public')->delete($slide->picture);
        }
        $slide->delete();
        return redirect()->back()->with('success', 'Slide has been deleted');
    }
}
