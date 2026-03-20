<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MenuItemController extends Controller
{
    public function index()
    {
        $restaurant = auth()->user()->restaurant;

        $menuItems = $restaurant
            ? $restaurant->menuItems()->with('category')->orderBy('order')->get()
            : collect();

        $categories = $restaurant
            ? $restaurant->categories()->orderBy('order')->get()
            : collect();

        return Inertia::render('MenuItems/Index', [
            'menuItems' => $menuItems,
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $restaurant = auth()->user()->restaurant;
        $categories = $restaurant ? $restaurant->categories()->orderBy('order')->get() : collect();

        return Inertia::render('MenuItems/Form', [
            'categories' => $categories,
        ]);
    }

    public function edit(MenuItem $menuItem)
    {
        $restaurant = auth()->user()->restaurant;
        abort_if($menuItem->restaurant_id !== $restaurant->id, 403);
        $categories = $restaurant->categories()->orderBy('order')->get();

        return Inertia::render('MenuItems/Form', [
            'item' => $menuItem,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'video' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime|max:51200',
            'featured' => 'nullable|boolean',
            'available' => 'nullable|boolean',
        ]);

        $restaurant = auth()->user()->restaurant;

        $data = [
            'restaurant_id' => $restaurant->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'featured' => $request->boolean('featured', false),
            'available' => $request->boolean('available', true),
            'order' => $restaurant->menuItems()->count(),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu-items', 'public');
        }

        if ($request->hasFile('video')) {
            $data['video_url'] = $request->file('video')->store('videos', 'public');
        }

        MenuItem::create($data);

        return redirect()->route('menu-items.index')->with('success', 'Item criado com sucesso.');
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $restaurant = auth()->user()->restaurant;
        abort_if($menuItem->restaurant_id !== $restaurant->id, 403);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'video' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime|max:51200',
            'featured' => 'nullable|boolean',
            'available' => 'nullable|boolean',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'featured' => $request->boolean('featured', $menuItem->featured),
            'available' => $request->boolean('available', $menuItem->available),
        ];

        if ($request->hasFile('image')) {
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $data['image'] = $request->file('image')->store('menu-items', 'public');
        }

        if ($request->hasFile('video')) {
            if ($menuItem->video_url) {
                Storage::disk('public')->delete($menuItem->video_url);
            }
            $data['video_url'] = $request->file('video')->store('videos', 'public');
        }

        $menuItem->update($data);

        return redirect()->route('menu-items.index')->with('success', 'Item atualizado com sucesso.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $restaurant = auth()->user()->restaurant;
        abort_if($menuItem->restaurant_id !== $restaurant->id, 403);

        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }
        if ($menuItem->video_url) {
            Storage::disk('public')->delete($menuItem->video_url);
        }

        $menuItem->delete();

        return redirect()->back()->with('success', 'Item excluído com sucesso.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:menu_items,id',
            'items.*.order' => 'required|integer',
        ]);

        $restaurant = auth()->user()->restaurant;

        foreach ($request->items as $item) {
            MenuItem::where('id', $item['id'])
                ->where('restaurant_id', $restaurant->id)
                ->update(['order' => $item['order']]);
        }

        return redirect()->back()->with('success', 'Ordem atualizada com sucesso.');
    }
}
