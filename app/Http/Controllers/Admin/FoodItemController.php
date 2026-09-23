<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use App\Models\Category;
use Illuminate\Http\Request;

class FoodItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = FoodItem::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $foodItems = $query->latest()->paginate(15);

        return view('admin.food_items.index', compact('foodItems', 'categories'));
    }

    public function create()
    {
        $categories = Category::whereIn('type', ['food', 'both'])->get();
        return view('admin.food_items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'ingredients' => 'nullable|string',
            'calories' => 'nullable|integer|min:0',
            'spicy_level' => 'required|integer|min:0|max:3',
            'available_quantity' => 'required|integer|min:0',
            'preparation_time' => 'required|integer|min:1',
            'image' => 'nullable|string',
            'status' => 'required|in:available,unavailable',
        ]);

        FoodItem::create($validated);

        return redirect()->route('admin.food-items.index')->with('success', 'Food item created successfully.');
    }

    public function edit(FoodItem $foodItem)
    {
        $categories = Category::whereIn('type', ['food', 'both'])->get();
        return view('admin.food_items.edit', compact('foodItem', 'categories'));
    }

    public function update(Request $request, FoodItem $foodItem)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'ingredients' => 'nullable|string',
            'calories' => 'nullable|integer|min:0',
            'spicy_level' => 'required|integer|min:0|max:3',
            'available_quantity' => 'required|integer|min:0',
            'preparation_time' => 'required|integer|min:1',
            'image' => 'nullable|string',
            'status' => 'required|in:available,unavailable',
        ]);

        $foodItem->update($validated);

        return redirect()->route('admin.food-items.index')->with('success', 'Food item updated successfully.');
    }

    public function destroy(FoodItem $foodItem)
    {
        $foodItem->delete();
        return redirect()->route('admin.food-items.index')->with('success', 'Food item deleted successfully.');
    }
}
