<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beverage;
use App\Models\Category;
use Illuminate\Http\Request;

class BeverageController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Beverage::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $beverages = $query->latest()->paginate(15);

        return view('admin.beverages.index', compact('beverages', 'categories'));
    }

    public function create()
    {
        $categories = Category::whereIn('type', ['beverage', 'both'])->get();
        return view('admin.beverages.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'size' => 'required|in:small,medium,large',
            'ingredients' => 'nullable|string',
            'calories' => 'nullable|integer|min:0',
            'temperature' => 'required|in:hot,cold',
            'available_quantity' => 'required|integer|min:0',
            'image' => 'nullable|string',
            'status' => 'required|in:available,unavailable',
        ]);

        Beverage::create($validated);

        return redirect()->route('admin.beverages.index')->with('success', 'Beverage created successfully.');
    }

    public function edit(Beverage $beverage)
    {
        $categories = Category::whereIn('type', ['beverage', 'both'])->get();
        return view('admin.beverages.edit', compact('beverage', 'categories'));
    }

    public function update(Request $request, Beverage $beverage)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'size' => 'required|in:small,medium,large',
            'ingredients' => 'nullable|string',
            'calories' => 'nullable|integer|min:0',
            'temperature' => 'required|in:hot,cold',
            'available_quantity' => 'required|integer|min:0',
            'image' => 'nullable|string',
            'status' => 'required|in:available,unavailable',
        ]);

        $beverage->update($validated);

        return redirect()->route('admin.beverages.index')->with('success', 'Beverage updated successfully.');
    }

    public function destroy(Beverage $beverage)
    {
        $beverage->delete();
        return redirect()->route('admin.beverages.index')->with('success', 'Beverage deleted successfully.');
    }
}
