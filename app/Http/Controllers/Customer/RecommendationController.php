<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use App\Models\Beverage;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    protected RecommendationService $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function index()
    {
        $user = Auth::user();
        $recommendations = $this->recommendationService->getPersonalizedRecommendations($user, 12);
        $combos = $this->recommendationService->getSmartCombos($user);
        $surprise = $this->recommendationService->getSurpriseMe($user);

        return view('customer.recommendations.index', compact('recommendations', 'combos', 'surprise', 'user'));
    }

    public function combos()
    {
        $user = Auth::user();
        $combos = $this->recommendationService->getSmartCombos($user);

        return view('customer.recommendations.combos', compact('combos', 'user'));
    }

    public function budget(Request $request)
    {
        $user = Auth::user();
        $maxBudget = (float) $request->get('max_budget', 100);
        $minBudget = (float) $request->get('min_budget', 0);

        $items = $this->recommendationService->getBudgetRecommendations($maxBudget, $minBudget, $user);

        return view('customer.recommendations.budget', compact('items', 'maxBudget', 'minBudget', 'user'));
    }

    public function compare(Request $request)
    {
        $user = Auth::user();
        $item1Id = $request->get('item1');
        $item2Id = $request->get('item2');

        $availableFoods = FoodItem::where('status', 'available')->get();

        $item1 = $item1Id ? FoodItem::find($item1Id) : $availableFoods->first();
        $item2 = $item2Id ? FoodItem::find($item2Id) : ($availableFoods->count() > 1 ? $availableFoods->get(1) : null);

        $match1 = $item1 ? $this->recommendationService->calculateFoodMatch($item1, $user?->preference) : null;
        $match2 = $item2 ? $this->recommendationService->calculateFoodMatch($item2, $user?->preference) : null;

        return view('customer.recommendations.compare', compact('availableFoods', 'item1', 'item2', 'match1', 'match2'));
    }

    public function surpriseMe()
    {
        $user = Auth::user();
        $surprise = $this->recommendationService->getSurpriseMe($user);

        return response()->json($surprise);
    }
}
