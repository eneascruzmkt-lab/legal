<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Analytics;
use App\Models\Restaurant;
use App\Models\Subscription;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::with([
                'user' => fn ($q) => $q->select('id', 'name'),
                'user.subscription' => fn ($q) => $q->select('id', 'user_id', 'plan', 'status'),
            ])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Index', [
            'restaurants' => $restaurants,
        ]);
    }

    public function stats()
    {
        $totalRestaurants = Restaurant::count();
        $totalViews = Analytics::where('event_type', 'view')->count();
        $activeSubscriptions = Subscription::whereIn('status', ['active', 'trial'])->count();

        return Inertia::render('Admin/Stats', [
            'stats' => [
                'totalRestaurants' => $totalRestaurants,
                'totalViews' => $totalViews,
                'activeSubscriptions' => $activeSubscriptions,
            ],
        ]);
    }
}
