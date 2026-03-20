<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscription = auth()->user()->subscription;

        return Inertia::render('Subscription/Index', [
            'subscription' => $subscription,
        ]);
    }

    public function upgrade(Request $request)
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        if ($subscription) {
            $subscription->update([
                'plan' => 'pro',
                'status' => 'active',
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addMonth(),
                'trial_ends_at' => null,
            ]);
        } else {
            $user->subscription()->create([
                'plan' => 'pro',
                'status' => 'active',
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addMonth(),
            ]);
        }

        return redirect()->back()->with('success', 'Plano atualizado para Pro com sucesso.');
    }

    public function cancel()
    {
        $subscription = auth()->user()->subscription;

        if ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'ends_at' => Carbon::now(),
            ]);
        }

        return redirect()->back()->with('success', 'Assinatura cancelada com sucesso.');
    }
}
