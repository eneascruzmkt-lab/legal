<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $restaurant = auth()->user()->restaurant;

        $categories = $restaurant
            ? $restaurant->categories()->orderBy('order')->get()
            : collect();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $restaurant = auth()->user()->restaurant;

        $data = [
            'restaurant_id' => $restaurant->id,
            'name' => $request->name,
            'order' => $restaurant->categories()->count(),
            'active' => true,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()->back()->with('success', 'Categoria criada com sucesso.');
    }

    public function update(Request $request, Category $category)
    {
        $restaurant = auth()->user()->restaurant;
        abort_if($category->restaurant_id !== $restaurant->id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'active' => 'nullable|boolean',
        ]);

        $data = [
            'name' => $request->name,
            'active' => $request->boolean('active', $category->active),
        ];

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->back()->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Category $category)
    {
        $restaurant = auth()->user()->restaurant;
        abort_if($category->restaurant_id !== $restaurant->id, 403);

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->back()->with('success', 'Categoria excluída com sucesso.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:categories,id',
            'items.*.order' => 'required|integer',
        ]);

        $restaurant = auth()->user()->restaurant;

        foreach ($request->items as $item) {
            Category::where('id', $item['id'])
                ->where('restaurant_id', $restaurant->id)
                ->update(['order' => $item['order']]);
        }

        return redirect()->back()->with('success', 'Ordem atualizada com sucesso.');
    }
}
