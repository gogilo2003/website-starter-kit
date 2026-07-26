<?php

namespace App\Http\Controllers;

use MeaCms\Menu\Services\VisitTracker;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * @var VisitTracker $visitTracker
     */
    protected $visitTracker;
    function __construct(VisitTracker $visitTracker)
    {
        $this->visitTracker = $visitTracker;
    }
    function dashboard()
    {
        $routeStats = $this->visitTracker->getPageVisitStats()->map(function ($stat) {
            return [
                'route_name' => $stat->route_name,
                'total_unique_visits' => (int)$stat->total_unique_visits,
                'total_visits' => (int)$stat->total_visits,
                'last_updated' => $stat->last_updated,
                'repeat_visits' => (int)$stat->total_visits - (int)$stat->total_unique_visits
            ];
        });
        return Inertia::render('Dashboard', [
            'pageVisits' => $routeStats,
            'summary' => [
                'totalRoutes' => $routeStats->count(),
                'totalUniqueVisits' => $routeStats->sum('total_unique_visits'),
                'totalVisits' => $routeStats->sum('total_visits'),
                'mostVisited' => $routeStats->sortByDesc('total_visits')->first()
            ]
        ]);
    }
}
