<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RestaurantSettingsController extends Controller
{
    public function edit()
    {
        $restaurant = auth()->user()->restaurant;

        return Inertia::render('Settings/Edit', [
            'restaurant' => $restaurant,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'instagram' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'working_hours' => 'nullable|array',
        ]);

        $restaurant = auth()->user()->restaurant;

        $data = $request->only([
            'name', 'description', 'primary_color', 'secondary_color',
            'address', 'phone', 'instagram', 'whatsapp', 'working_hours',
        ]);

        if ($request->hasFile('logo')) {
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $restaurant->update($data);

        return redirect()->back()->with('success', 'Configurações atualizadas com sucesso.');
    }
}
