<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomerPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $preference = $user->preference ?? new CustomerPreference();
        $categories = Category::all();

        return view('customer.profile', compact('user', 'preference', 'categories'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function showPreferences()
    {
        $user = Auth::user();
        $preference = $user->preference ?? CustomerPreference::firstOrCreate(['user_id' => $user->id]);
        $categories = Category::all();

        return view('customer.preferences', compact('user', 'preference', 'categories'));
    }

    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'age' => 'nullable|integer|min:10|max:120',
            'favorite_categories' => 'nullable|array',
            'favorite_food_types' => 'nullable|array',
            'favorite_beverages' => 'nullable|array',
            'preferred_taste' => 'nullable|string',
            'dietary_preferences' => 'nullable|array',
            'price_preference' => 'nullable|numeric|min:10',
            'spicy_level' => 'required|integer|min:0|max:3',
            'favorite_ingredients' => 'nullable|string',
            'disliked_ingredients' => 'nullable|string',
        ]);

        // Helper to convert comma-separated string to clean array
        $favIngs = !empty($validated['favorite_ingredients'])
            ? array_values(array_filter(array_map('trim', explode(',', $validated['favorite_ingredients']))))
            : [];

        $disIngs = !empty($validated['disliked_ingredients'])
            ? array_values(array_filter(array_map('trim', explode(',', $validated['disliked_ingredients']))))
            : [];

        CustomerPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'age' => $validated['age'] ?? null,
                'favorite_categories' => $validated['favorite_categories'] ?? [],
                'favorite_food_types' => $validated['favorite_food_types'] ?? [],
                'favorite_beverages' => $validated['favorite_beverages'] ?? [],
                'preferred_taste' => $validated['preferred_taste'] ?? 'savory',
                'dietary_preferences' => $validated['dietary_preferences'] ?? [],
                'price_preference' => $validated['price_preference'] ?? null,
                'spicy_level' => (int) $validated['spicy_level'],
                'favorite_ingredients' => $favIngs,
                'disliked_ingredients' => $disIngs,
            ]
        );

        return redirect()->route('recommendations.index')->with('success', 'Preferences updated! AI recommendations refreshed based on your new tastes.');
    }
}
