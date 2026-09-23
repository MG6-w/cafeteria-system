<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FoodItem;
use App\Models\Beverage;
use App\Models\Favorite;
use App\Models\Review;
use App\Services\RecommendationService;
use App\Services\NaturalLanguageSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    protected RecommendationService $recommendationService;
    protected NaturalLanguageSearchService $nlpSearchService;

    public function __construct(
        RecommendationService $recommendationService,
        NaturalLanguageSearchService $nlpSearchService
    ) {
        $this->recommendationService = $recommendationService;
        $this->nlpSearchService = $nlpSearchService;
    }

    public function index(Request $request)
    {
        $categories = Category::all();
        $user = Auth::user();

        // Check if natural language search was triggered
        if ($request->filled('nlp_query')) {
            $nlpResults = $this->nlpSearchService->search($request->nlp_query);
            return view('customer.menu.nlp_results', [
                'query' => $request->nlp_query,
                'results' => $nlpResults,
                'categories' => $categories,
            ]);
        }

        $type = $request->get('type', 'all'); // 'all', 'food', 'beverage'
        $categoryId = $request->get('category');
        $search = $request->get('search');
        $maxPrice = $request->get('max_price');
        $spicyLevel = $request->get('spicy_level');
        $maxCalories = $request->get('max_calories');
        $ingredient = $request->get('ingredient');
        $availableOnly = $request->boolean('available_only', true);

        // Foods Query
        $foods = collect();
        if ($type === 'all' || $type === 'food') {
            $foodQuery = FoodItem::with('category');
            if ($availableOnly) $foodQuery->where('status', 'available')->where('available_quantity', '>', 0);
            if ($categoryId) $foodQuery->where('category_id', $categoryId);
            if ($search) $foodQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
            });
            if ($maxPrice) $foodQuery->where('price', '<=', $maxPrice);
            if ($spicyLevel !== null && $spicyLevel !== '') $foodQuery->where('spicy_level', $spicyLevel);
            if ($maxCalories) $foodQuery->where('calories', '<=', $maxCalories);
            if ($ingredient) $foodQuery->where('ingredients', 'like', "%{$ingredient}%");

            $foods = $foodQuery->get()->map(function($f) use ($user) {
                $scoreData = $this->recommendationService->calculateFoodMatch($f, $user?->preference);
                $f->match_percentage = $scoreData['score'];
                $f->match_explanation = $scoreData['explanation'];
                $f->item_type = 'food';
                return $f;
            });
        }

        // Beverages Query
        $beverages = collect();
        if ($type === 'all' || $type === 'beverage') {
            $drinkQuery = Beverage::with('category');
            if ($availableOnly) $drinkQuery->where('status', 'available')->where('available_quantity', '>', 0);
            if ($categoryId) $drinkQuery->where('category_id', $categoryId);
            if ($search) $drinkQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
            });
            if ($maxPrice) $drinkQuery->where('price', '<=', $maxPrice);
            if ($maxCalories) $drinkQuery->where('calories', '<=', $maxCalories);
            if ($ingredient) $drinkQuery->where('ingredients', 'like', "%{$ingredient}%");

            $beverages = $drinkQuery->get()->map(function($b) use ($user) {
                $scoreData = $this->recommendationService->calculateBeverageMatch($b, $user?->preference);
                $b->match_percentage = $scoreData['score'];
                $b->match_explanation = $scoreData['explanation'];
                $b->item_type = 'beverage';
                return $b;
            });
        }

        // Combined and optionally sorted
        $items = $foods->concat($beverages)->sortByDesc('match_percentage')->values();

        // User Favorites IDs
        $userFavorites = $user
            ? Favorite::where('user_id', $user->id)->get()->groupBy('item_type')->map(fn($g) => $g->pluck('item_id')->toArray())
            : collect();

        return view('customer.menu.index', compact(
            'categories', 'items', 'type', 'categoryId', 'search',
            'maxPrice', 'spicyLevel', 'maxCalories', 'ingredient', 'availableOnly', 'userFavorites'
        ));
    }

    public function showFood(FoodItem $foodItem)
    {
        $user = Auth::user();
        $foodItem->load(['category', 'reviews.user']);
        $scoreData = $this->recommendationService->calculateFoodMatch($foodItem, $user?->preference);
        $foodItem->match_percentage = $scoreData['score'];
        $foodItem->match_explanation = $scoreData['explanation'];
        $foodItem->match_reasons = $scoreData['reasons'];

        $isFavorite = $user && Favorite::where('user_id', $user->id)->where('item_type', 'food')->where('item_id', $foodItem->id)->exists();

        // Related beverages recommendation
        $drinkPairings = Beverage::where('status', 'available')->take(3)->get();

        return view('customer.menu.show_food', compact('foodItem', 'isFavorite', 'drinkPairings'));
    }

    public function showBeverage(Beverage $beverage)
    {
        $user = Auth::user();
        $beverage->load(['category', 'reviews.user']);
        $scoreData = $this->recommendationService->calculateBeverageMatch($beverage, $user?->preference);
        $beverage->match_percentage = $scoreData['score'];
        $beverage->match_explanation = $scoreData['explanation'];
        $beverage->match_reasons = $scoreData['reasons'];

        $isFavorite = $user && Favorite::where('user_id', $user->id)->where('item_type', 'beverage')->where('item_id', $beverage->id)->exists();

        // Related food pairings
        $foodPairings = FoodItem::where('status', 'available')->take(3)->get();

        return view('customer.menu.show_beverage', compact('beverage', 'isFavorite', 'foodPairings'));
    }

    public function toggleFavorite(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['status' => 'unauthenticated'], 401);

        $request->validate([
            'item_type' => 'required|in:food,beverage',
            'item_id' => 'required|integer',
        ]);

        $fav = Favorite::where('user_id', $user->id)
            ->where('item_type', $request->item_type)
            ->where('item_id', $request->item_id)
            ->first();

        if ($fav) {
            $fav->delete();
            $isFav = false;
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'item_type' => $request->item_type,
                'item_id' => $request->item_id,
            ]);
            $isFav = true;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'is_favorite' => $isFav]);
        }

        return back()->with('success', $isFav ? 'Added to favorites!' : 'Removed from favorites!');
    }

    public function storeReview(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'item_type' => 'required|in:food,beverage',
            'item_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        Review::updateOrCreate(
            ['user_id' => $user->id, 'item_type' => $validated['item_type'], 'item_id' => $validated['item_id']],
            ['rating' => $validated['rating'], 'comment' => $validated['comment']]
        );

        return back()->with('success', 'Thank you for your rating & review!');
    }
}
