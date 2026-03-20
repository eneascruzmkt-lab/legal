<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Analytics;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $restaurant = auth()->user()->restaurant;

        if (!$restaurant) {
            return Inertia::render('Dashboard', [
                'stats' => null,
            ]);
        }

        $totalViews = Analytics::where('restaurant_id', $restaurant->id)
            ->where('event_type', 'view')
            ->count();

        $topItems = $restaurant->menuItems()
            ->orderByDesc('views_count')
            ->take(5)
            ->get(['id', 'name', 'views_count', 'image']);

        $topVideos = $restaurant->menuItems()
            ->whereNotNull('video_url')
            ->orderByDesc('video_plays_count')
            ->take(5)
            ->get(['id', 'name', 'video_plays_count', 'image']);

        $avgTime = Analytics::where('restaurant_id', $restaurant->id)
            ->where('event_type', 'view')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalViews' => $totalViews,
                'topItems' => $topItems,
                'topVideos' => $topVideos,
                'monthlyViews' => $avgTime,
            ],
            'restaurant' => $restaurant,
        ]);
    }
}
