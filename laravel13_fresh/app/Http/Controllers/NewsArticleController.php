<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Support\Util;
use App\Models\Picture;
use App\Models\NewsArticle;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreNewsArticleRequest;
use App\Http\Requests\UpdateNewsArticleRequest;

class NewsArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $news_articles = NewsArticle::orderBy('created_at', 'DESC')->get()->map(fn ($item) => [
            "id" => $item->id,
            "title" => $item->title,
            "picture" => $item->pictures->count() ? Util::pictureUrl($item->pictures->first()->name) : null,
            "content" => $item->content,
            "published" => $item->published,
            "front" => $item->front,
        ]);
        return Inertia::render('Dashboard/NewsArticles/Index', ['news_articles' => $news_articles]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNewsArticleRequest $request)
    {
        $news_article = new NewsArticle();
        $news_article->title = $request->title;
        $news_article->slug = Str::slug($request->title);
        $news_article->content = $request->content;
        $news_article->published = false;
        $news_article->front = false;
        $news_article->save();

        if ($request->hasFile('picture')) {
            $picture = new Picture();
            $picture->name = $request->picture->storePublicly('news_articles', ['disk' => 'public']);
            $picture->caption = $news_article->title;
            $news_article->pictures()->save($picture);
        }
        return redirect()->back()->with('success', 'News Article has been stored');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNewsArticleRequest $request, NewsArticle $news_article)
    {
        $news_article->title = $request->title;
        $news_article->slug = Str::slug($request->title);
        $news_article->content = $request->content;
        $news_article->save();

        if ($request->hasFile('picture')) {
            $picture = $news_article->pictures->count() ? $news_article->pictures()->first() : null;
            if ($picture) {
                if (Storage::disk('public')->exists($picture->name)) {
                    Storage::disk('public')->delete($picture->name);
                }
            } else {
                $picture = new Picture();
            }
            $picture->name = $request->picture->storePublicly('news_articles', ['disk' => 'public']);
            $picture->caption = $news_article->title;
            if ($picture->id) {
                $picture->save();
            } else {
                $news_article->pictures()->save($picture);
            }
        }
        return redirect()->back()->with('success', 'News Article has been updated');
    }

    /**
     * Publish/Unpublish the specified resource from storage.
     */
    public function publish(NewsArticle $news_article)
    {
        $news_article->published = $news_article->published ? 0 : 1;
        $news_article->save();
        return redirect()->back()->with('success', sprintf('News Article has been %s', $news_article->published ? 'Published' : 'Unpublished'));
    }

    /**
     * Promote/Demote the specified resource from storage.
     */
    public function promote(NewsArticle $news_article)
    {
        $news_article->front = $news_article->front ? 0 : 1;
        $news_article->save();
        return redirect()->back()->with('success', sprintf('News Article has been %s', $news_article->front ? 'Promoted' : 'Demoted'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewsArticle $news_article)
    {
        if ($news_article->pictures) {
            foreach ($news_article->pictures as $picture) {
                if (Storage::disk('public')->exists($picture->name)) {
                    Storage::disk('public')->delete($picture->name);
                }
            }
        }
        $news_article->delete();
        return redirect()->back()->with('success', 'News Article has been deleted');
    }
}
