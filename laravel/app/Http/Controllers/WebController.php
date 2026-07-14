<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestQuoteRequest;
use App\Http\Requests\SendFeedbackRequest;
use App\Mail\WebFeedback;
use App\Models\Element;
use App\Models\NewsArticle;
use App\Models\PageSection;
use App\Models\Picture;
use App\Services\QuoteService;
use Gogilo\Products\Models\Product;
use Gogilo\Products\Services\ProductCategoryService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

// use Illuminate\Http\Request;

class WebController extends Controller
{
    protected $pageSectionService;

    protected $productCategoryService;

    protected $downloadService;

    protected QuoteService $quoteService;

    protected DownloadCategoryService $downloadCategoryService;

    public function __construct(
        PageSectionService $pageSectionService,
        ProductCategoryService $productCategoryService
    ) {
        $this->pageSectionService = $pageSectionService;
        $this->productCategoryService = $productCategoryService;
        $this->downloadCategoryService = app(DownloadCategoryService::class);
        $this->downloadService = app(DownloadService::class);
        $this->quoteService = app(QuoteService::class);
    }

    public function home()
    {
        $products = Product::where('published', 1)
            ->where('front', 1)
            ->orderBy('created_at', 'DESC')->get()->map(fn ($item) => [
                'id' => $item->id,
                'title' => $item->title,
                'url' => route('product', $item->slug),
                'picture' => $item->pictures->count() ? Util::pictureUrl($item->pictures()->first()->name) : null,
                'summary' => $item->summary,
            ]);

        $product_categories = $this->productCategoryService->getAllProductCategories(['filters' => ['published' => 1, 'promoted' => 1]], true);

        $slides = Slide::where('published', 1)->get()->map(fn ($item) => [
            'id' => $item->id,
            'title' => $item->title ?? config('app.name'),
            'picture' => Util::pictureUrl($item->picture),
            'media_type' => $item->media_type,
            'caption' => $item->caption ?? 'Rooted in Values, Growing with Excellence.',
            'published' => $item->published,
        ]);

        $product_intro = ($intro = Element::where('name', 'product-intro')
            ->first()) ? $intro->content : '';
        $project_intro = ($intro = Element::where('name', 'project-intro')
            ->first()) ? $intro->content : '';

        $partners = Partner::where('published', 1)->where('front', 1)->get()->map(fn ($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'logo' => Util::pictureUrl($item->logo),
            'website' => $item->website,
            'description' => $item->description,
        ]);

        $welcome = Element::where('name', 'welcome')->where('published', 1)->first();

        $industriesSection = PageSection::where(
            'name',
            'like',
            'industries-we-serve'
        )
            ->with(['elements' => function ($query) {
                $query->where('published', 1);
            }])
            ->first();

        $customers = $this->pageSectionService->getByPageSectionName('featured-customers', ['elements'], true);
        $categories = $this->productCategoryService->getAllProductCategories(['filters' => ['published' => 1, 'promoted' => 1]], true);

        return Inertia::render('Home', [
            'products' => $products,
            'product_categories' => $product_categories,
            'slides' => $slides,
            'product_intro' => $product_intro,
            'project_intro' => $project_intro,
            'partners' => $partners,
            'customers' => $customers,
            'categories' => $categories,
            'welcome' => [
                'id' => $welcome->id,
                'title' => $welcome->title,
                'content' => $welcome->content,
                'icon' => $welcome->icon,
                'photo' => Util::pictureUrl($welcome->photo),
            ],
            'industriesSection' => $industriesSection ? (object) [
                'id' => $industriesSection->id,
                'title' => $industriesSection->title,
                'description' => $industriesSection->description,
                'elements' => $industriesSection->elements->map(fn (Element $element) => [
                    'id' => $element->id,
                    'title' => $element->title,
                    'content' => $element->content,
                    'icon' => $element->icon,
                    'photo' => Util::pictureUrl($element->photo),
                ]),
            ] : null,
        ]);
    }

    public function about()
    {
        $welcome = Element::where('published', 1)
            ->where('name', 'welcome')->first();
        $welcome = $welcome ? [
            'id' => $welcome->id,
            'name' => $welcome->name,
            'title' => $welcome->title,
            'content' => $welcome->content,
            'type' => $welcome->type,
            'photo' => Util::pictureUrl($welcome->photo),
        ] : null;

        $numbers = [];
        $numbers = $this->pageSectionService->getByPageSectionName(
            'numbers',
            [
                'elements' => function ($query) {
                    $query->where('published', 1);
                },
            ],
            true
        );

        $numbers = $numbers ? $numbers->elements : [];

        $whoWeAre = null;
        $partners = [];

        $whoWeAre = Element::where('published', 1)
            ->where('name', 'who-we-are')
            ->first();

        $whoWeAre = $whoWeAre ? [
            'id' => $whoWeAre->id,
            'name' => $whoWeAre->name,
            'title' => $whoWeAre->title,
            'content' => $whoWeAre->content,
            'type' => $whoWeAre->type,
            'photo' => Util::pictureUrl($whoWeAre->photo),
        ] : null;

        $partners = [];
        $partners = Partner::where('published', 1)->get()->map(fn ($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'logo' => Util::pictureUrl($item->logo),
            'website' => $item->website,
            'description' => $item->description,
        ]);

        $coreValues = [];

        $coreValues = $this->pageSectionService->getByPageSectionName(
            'core-values',
            ['elements' => function ($query) {
                $query->where('published', 1);
            }],
            true
        )->elements;

        return Inertia::render('About', [
            'welcome' => $welcome,
            'whoWeAre' => $whoWeAre,
            'partners' => $partners,
            'coreValues' => $coreValues,
            'numbers' => $numbers,
        ]);
    }

    public function product(string $slug)
    {
        $product = Product::with(['pictures'])
            ->where('slug', $slug)
            ->where('published', 1)
            ->firstOrFail();

        $otherProducts = Product::with('pictures')
            ->where('published', 1)
            ->where('id', '<>', $product->id)
            ->where('product_category_id', $product->product_category_id)
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'title' => $item->title,
                'url' => route('product', $item->slug),
                'picture' => Util::pictureUrl($item->pictures?->first()?->name) ?? url('/images/placeholder-product.png'),
            ]);

        return Inertia::render('Product', [
            'product' => [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'picture' => Util::pictureUrl($product->pictures?->first()?->name) ?? url('/images/placeholder-product.png'),
                'pictures' => $product->pictures,
                'summary' => $product->summary,
                'description' => $product->content,
                'features' => $product->features,
            ],
            'products' => $otherProducts,
        ]);
    }

    // public function products(?string $slug = null)
    // {
    //     $categories = $this->productCategoryService
    //         ->getAllProductCategories(
    //             ['filters' => ['published' => 1]],
    //             true
    //         );

    //     $intro = Element::where('name', 'product_category-introduction')->first();
    //     $product_intro = $intro?->content ?? '';

    //     $category = null;

    //     $productsQuery = Product::with('pictures')
    //         ->where('published', 1)
    //         ->orderByDesc('created_at');

    //     if ($slug) {
    //         $category = $this->productCategoryService
    //             ->getProductCategoryBySlug($slug, true);

    //         $productsQuery->where('product_category_id', $category->id);
    //     }

    //     $products = $productsQuery->get()->map(fn($item) => [
    //         'id'      => $item->id,
    //         'title'   => $item->title,
    //         'slug'    => $item->slug,
    //         'url'     => route('product', $item->slug),
    //         'picture' => Util::pictureUrl($item->pictures?->first()?->name) ?? '/images/placeholder-product.png',
    //         'summary' => $item->summary,
    //     ]);

    //     return Inertia::render('Products', [
    //         'products'       => $products,
    //         'product_intro'  => $product_intro,
    //         'category'       => $category,
    //         'categories'     => $categories,
    //     ]);
    // }

    public function products(?string $slug = null)
    {
        $categories = $this->productCategoryService
            ->getAllProductCategories(
                ['filters' => ['published' => 1]],
                true
            );

        $intro = Element::where('name', 'product_category-introduction')->first();
        $product_intro = $intro?->content ?? '';

        $category = null;

        $productsQuery = Product::with('pictures')
            ->where('published', 1)
            ->orderByDesc('created_at');

        if ($slug) {
            $category = $this->productCategoryService
                ->getProductCategoryBySlug($slug, true);

            if (! $category) {
                abort(404);
            }

            $productsQuery->where('product_category_id', $category->id);
        }

        // Determine per page: 6 for first page, 3 for subsequent (infinite scroll)
        $currentPage = (int) request()->get('page', 1);
        $perPage = $currentPage === 1 ? 6 : 3;

        $products = $productsQuery->paginate($perPage)
            ->withPath(request()->path())
            ->withQueryString() // Important: preserves any future filters/search
            ->through(fn ($item) => [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'url' => route('product', $item->slug),
                'picture' => $item->picture,
                'summary' => $item->summary,
            ]);

        $data = [
            'products' => $products,
            'product_intro' => $product_intro,
            'category' => $category,
            'categories' => $categories,
        ];

        return Inertia::render('Products', $data);
    }

    public function news($slug = null)
    {
        $news_articles = NewsArticle::where('published', 1)
            ->when($slug, function ($query) use ($slug) {
                $query->where('slug', '<>', $slug);
            })
            ->orderBy('created_at', 'ASC')->get()->map(fn ($item) => [
                'id' => $item->id,
                'title' => $item->title,
                'author' => $item->user->name,
                'url' => route('news', $item->slug),
                'picture' => Util::pictureUrl($item->pictures->first()->name),
                'date' => $item->created_at->toFormattedDateString(),
                'content' => $item->content,
            ]);

        $intro = Element::where('name', 'article-introduction')->first();
        $news_article_intro = $intro ? $intro->content : '';

        if ($slug) {
            $news_article = NewsArticle::where('slug', $slug)->first();

            return Inertia::render('NewsArticle', ['news_articles' => $news_articles, 'news_article' => [
                'id' => $news_article->id,
                'title' => $news_article->title,
                'author' => $news_article->user->name,
                'url' => route('news', $news_article->slug),
                'picture' => Util::pictureUrl($news_article->pictures->first()->name),
                'date' => $news_article->created_at->toFormattedDateString(),
                'content' => $news_article->content,
            ]]);
        }

        return Inertia::render('NewsArticles', ['news_articles' => $news_articles, 'news_article_intro' => $news_article_intro]);
    }

    public function contact()
    {
        $contacts = Element::where('name', 'phone')
            ->orWhere('name', 'email')
            ->orWhere('name', 'LIKE', '%address%')
            ->orWhere('name', 'location')->get()->map(fn ($item) => (object) [
                'id' => $item->id,
                'name' => $item->name,
                'title' => $item->title,
                'content' => $item->content,
                'icon' => $item->icon,
            ]);

        return Inertia::render('Contact', ['contacts' => $contacts]);
    }

    public function feedback(SendFeedbackRequest $request)
    {
        Mail::to(env('FEEDBACK_EMAIL'))->send(new WebFeedback($request->all()));

        return redirect()->back()->with('success', 'Message sent');
    }

    public function downloads($slug = null)
    {
        if ($slug) {
            $category = $this->downloadCategoryService
                ->getDownloadCategoryBySlug($slug, ['downloads'], true);

            return Inertia::render('Downloads', [
                'downloads' => $category->downloads->map(fn ($item) => [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'url' => route('download', $item->slug),
                    'category' => $item->category->name,
                    'size' => Util::humanFileSize($item->file_size),
                    'type' => $item->file_type,
                    'name' => $item->file_name,
                    'slug' => $item->slug,
                ]),
                'category' => function () use ($category) {
                    return (object) [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description,
                    ];
                },
            ]);
        }

        $downloads = $this->downloadService->all([], true);

        return Inertia::render('Downloads', ['downloads' => $downloads]);
    }

    public function download($slug)
    {
        $download = Download::where('slug', $slug)->first();
        $file = Storage::path($download->file_path.'/'.$download->file_name);

        return response()->download($file);
    }

    public function quote(?string $code = null)
    {
        $quote = $code ? $this->quoteService->getQuoteByCode($code) : null;

        return Inertia::render('Quote/View', ['quote' => $quote]);
    }

    public function quoteRequest(RequestQuoteRequest $request)
    {
        $quoteRequestService = app()->make(QuoteRequestService::class);
        try {
            $validated = $request->validated();

            // Add products from request if any
            $products = $request->input('products', []);
            if (! empty($products)) {
                $validated['products'] = $products;
            }

            $quote = $quoteRequestService->requestQuote($validated);

            return redirect()->route('quote-track', $quote->code)
                ->with('success', 'Your quote request has been submitted successfully. You can track your quote using the code: '.$quote->code);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit quote request. Please try again.'.$e->getMessage());
        }
    }

    public function quoteTrack(string $code)
    {
        $quote = $this->quoteService->getQuoteByCode(
            $code,
            [
                'relations' => ['items.product'],
                'mapped' => true,
            ]
        );

        $this->quoteService->updateLastView($quote->id);

        if (! $quote) {
            abort(404, 'Quote not found.');
        }

        return Inertia::render('Quote/Track', [
            'quote' => $quote,
            'trackingCode' => $code,
        ]);
    }

    public function quoteDownload(string $code): Response
    {
        $pdfData = $this->quoteService->downloadQuotePdf($code);

        if (! $pdfData) {
            abort(404, 'Quote not found.');
        }

        return response($pdfData['content'])
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$pdfData['filename'].'"');
    }

    public function quoteView(string $code)
    {
        $quote = $this->quoteService->getQuoteByCode($code);

        if (! $quote) {
            abort(404, 'Quote not found.');
        }

        if (request()->has('download')) {
            return $this->quoteDownload($code);
        }

        return Inertia::render('Quote/View', [
            'quote' => $quote,
            'canDownload' => $quote->status === 'sent' || $quote->status === 'completed',
        ]);
    }

    public function wishlistAdd()
    {
        return Inertia::render();
    }

    public function wishlistRemove()
    {
        return Inertia::render();
    }
}
