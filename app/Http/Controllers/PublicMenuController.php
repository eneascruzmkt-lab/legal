<?php

namespace App\Http\Controllers;

use App\Models\Analytics;
use App\Models\Restaurant;
use Inertia\Inertia;

class PublicMenuController extends Controller
{
    public function show(string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();

        $categories = $restaurant->categories()
            ->where('active', true)
            ->with(['menuItems' => function ($query) {
                $query->where('available', true)->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        Analytics::create([
            'restaurant_id' => $restaurant->id,
            'event_type' => 'view',
            'ip' => preg_replace('/\.\d+$/', '.0', request()->ip()),
            'user_agent' => request()->userAgent(),
        ]);

        $featured = $restaurant->menuItems()
            ->where('featured', true)
            ->where('available', true)
            ->whereNotNull('video_url')
            ->get();

        return Inertia::render('PublicMenu', [
            'restaurant' => $restaurant,
            'categories' => $categories,
            'featured' => $featured,
        ]);
    }

    public function trackEvent(string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        $validated = request()->validate([
            'event_type' => 'required|in:click,video_play',
            'menu_item_id' => 'required|exists:menu_items,id',
        ]);

        Analytics::create([
            'restaurant_id' => $restaurant->id,
            'menu_item_id' => $validated['menu_item_id'],
            'event_type' => $validated['event_type'],
            'ip' => preg_replace('/\.\d+$/', '.0', request()->ip()),
            'user_agent' => request()->userAgent(),
        ]);

        if ($validated['event_type'] === 'video_play') {
            \App\Models\MenuItem::where('id', $validated['menu_item_id'])->increment('video_plays_count');
        } else {
            \App\Models\MenuItem::where('id', $validated['menu_item_id'])->increment('views_count');
        }

        return response()->json(['ok' => true]);
    }
}
