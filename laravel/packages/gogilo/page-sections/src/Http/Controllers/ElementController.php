<?php

namespace Gogilo\PageSections\Http\Controllers;

use Inertia\Inertia;
use App\Support\Util;
use Gogilo\PageSections\Models\Element;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Gogilo\PageSections\Http\Requests\StoreElementRequest;
use Gogilo\PageSections\Http\Requests\UpdateElementRequest;
use Illuminate\Routing\Controller;

class ElementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->input('search');
        $elements = Element::when($search, function ($query) use ($search) {
            $query->where('title', 'LIKE', "%$search%");
        })
            ->paginate(9)
            ->through(fn ($item) => [
                "id" => $item->id,
                "name" => $item->name,
                "title" => $item->title,
                "content" => $item->content,
                "type" => $item->type,
                "photo" => Util::pictureUrl($item->photo),
                "icon" => $item->icon,
                "published" => $item->published,
            ]);
        return Inertia::render('Dashboard/Elements/Index', ['elements' => $elements, 'searchVal' => $search]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreElementRequest $request)
    {
        $element = new Element();
        $element->name = Str::slug($request->title);
        $element->title = $request->title;
        $element->content = $request->content;
        $element->icon = $request->icon;
        $element->type = $request->type;
        if ($request->hasFile('picture')) {
            $element->photo = $request->picture->storePublicly('elements/pictures', ['disk' => 'public']);
        }
        $element->save();
        return redirect()->back()->with('success', 'Element created');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateElementRequest $request, Element $element)
    {
        $element->name = Str::slug($request->title);
        $element->title = $request->title;
        $element->content = $request->content;
        $element->icon = $request->icon;
        $element->type = $request->type;
        if ($request->hasFile('picture')) {
            $element->photo = $request->picture->storePublicly('elements/pictures', ['disk' => 'public']);
        }
        $element->save();
        return redirect()->back()->with('success', 'Element updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Element $element)
    {
        $element->delete();
        return redirect()->back()->with('success', 'Element Deleted');
    }

    /**
     * Publish the specified resource from storage.
     */
    public function publish(Element $element)
    {
        $element->published = !$element->published;
        $element->save();
        return redirect()->back()->with('success', sprintf('Element %s', $element->published ? 'Published' : 'Unpublished'));
    }
}
