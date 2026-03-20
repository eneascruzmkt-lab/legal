<?php

namespace App\Http\Controllers;

use App\Models\Analytics;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function index()
    {
        $restaurant = auth()->user()->restaurant;

        if (!$restaurant) {
            return Inertia::render('Analytics/Index', [
                'dailyViews' => [],
                'topItems' => [],
                'topVideos' => [],
            ]);
        }

        $dailyViews = Analytics::where('restaurant_id', $restaurant->id)
            ->where('event_type', 'view')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topItems = $restaurant->menuItems()
            ->orderByDesc('views_count')
            ->take(10)
            ->get(['id', 'name', 'views_count', 'image']);

        $topVideos = $restaurant->menuItems()
            ->whereNotNull('video_url')
            ->orderByDesc('video_plays_count')
            ->take(10)
            ->get(['id', 'name', 'video_plays_count', 'image']);

        return Inertia::render('Analytics/Index', [
            'dailyViews' => $dailyViews,
            'topItems' => $topItems,
            'topVideos' => $topVideos,
        ]);
    }
}
