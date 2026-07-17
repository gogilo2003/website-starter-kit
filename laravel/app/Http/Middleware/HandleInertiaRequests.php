<?php

namespace App\Http\Middleware;

use Gogilo\PageSections\Models\Element;
use Gogilo\Menu\MenuRegistry;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $props = [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'appName' => config('app.name'),
        ];

        $notification = [];
        if ($request->session()->has('success')) {
            $notification = [
                'success' => $request->session()->get('success'),
            ];
        }
        if ($request->session()->has('error')) {
            $notification = [
                'error' => $request->session()->get('error'),
            ];
        }
        if ($request->session()->has('info')) {
            $notification = [
                'info' => $request->session()->get('info'),
            ];
        }
        if ($request->session()->has('warning')) {
            $notification = [
                'warning' => $request->session()->get('warning'),
            ];
        }
        if (! empty($notification)) {
            $props['notification'] = $notification;
        }

        $intro = ($item = Element::where('name', 'welcome')->where('published', 1)->first()) ? $item->content : null;
        $props['intro'] = $intro;

        $props['menu'] = app(MenuRegistry::class)->resolveAll();

        $socialmedia = Element::where('published', 1)
            ->whereIn(
                'name',
                [
                    'facebook',
                    'twitter',
                    'linkedin',
                    'instagram',
                    'youtube',
                ]
            )
            ->orderBy('name', 'DESC')
            ->get()
            ->map(
                fn ($item) => [
                    'url' => $item->content,
                    'icon' => $item->icon,
                    'title' => $item->title,
                ]
            );

        $props['socialmedia'] = $socialmedia;

        $contact = [];
        $phone = Element::where('name', 'phone')->where('published', 1)->first();
        $email = Element::where('name', 'email')->where('published', 1)->first();
        $address = Element::where('name', 'address')->where('published', 1)->first();
        $location = Element::where('name', 'location')->where('published', 1)->first();

        $contact['phone'] = $phone ? $phone->content : null;
        $contact['email'] = $email ? $email->content : null;
        $contact['address'] = $address ? $address->content : null;
        $contact['location'] = $location ? $location->content : null;
        $props['contact'] = $contact;
        $props['call'] = $phone ? $phone->content : null;

        return $props;
    }
}
