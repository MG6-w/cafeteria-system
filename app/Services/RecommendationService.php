<?php

namespace App\Services;

use App\Models\User;
use App\Models\FoodItem;
use App\Models\Beverage;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Support\Collection;

class RecommendationService
{
    /**
     * Get personalized recommendations for a user with match percentages.
     */
    public function getPersonalizedRecommendations(?User $user, int $limit = 12): Collection
    {
        $foods = FoodItem::with('category')->where('status', 'available')->get();
        $beverages = Beverage::with('category')->where('status', 'available')->get();

        $preferences = $user ? $user->preference : null;
        $orderHistory = $user ? $this->getUserOrderStats($user) : [];

        $scoredItems = collect();

        foreach ($foods as $food) {
            $scoreData = $this->calculateFoodMatch($food, $preferences, $orderHistory);
            $scoredItems->push([
                'id' => $food->id,
                'name' => $food->name,
                'type' => 'food',
                'category_name' => $food->category->name ?? 'Uncategorized',
                'price' => (float) $food->price,
                'calories' => $food->calories,
                'spicy_level' => $food->spicy_level,
                'ingredients' => $food->ingredients,
                'image' => $food->image,
                'match_percentage' => $scoreData['score'],
                'explanation' => $scoreData['explanation'],
                'reasons' => $scoreData['reasons'],
                'model' => $food,
            ]);
        }

        foreach ($beverages as $beverage) {
            $scoreData = $this->calculateBeverageMatch($beverage, $preferences, $orderHistory);
            $scoredItems->push([
                'id' => $beverage->id,
                'name' => $beverage->name,
                'type' => 'drink',
                'category_name' => $beverage->category->name ?? 'Beverage',
                'price' => (float) $beverage->price,
                'calories' => $beverage->calories,
                'temperature' => $beverage->temperature,
                'size' => $beverage->size,
                'ingredients' => $beverage->ingredients,
                'image' => $beverage->image,
                'match_percentage' => $scoreData['score'],
                'explanation' => $scoreData['explanation'],
                'reasons' => $scoreData['reasons'],
                'model' => $beverage,
            ]);
        }

        return $scoredItems->sortByDesc('match_percentage')->values()->take($limit);
    }

    /**
     * Calculate food match percentage & explanation.
     */
    public function calculateFoodMatch(FoodItem $food, $preferences, array $orderHistory = []): array
    {
        if (!$preferences) {
            // Default generic score based on popularity/rating
            $score = 75;
            return [
                'score' => $score,
                'explanation' => 'Popular selection recommended for all cafeteria guests.',
                'reasons' => ['Popular cafeteria favorite'],
            ];
        }

        $score = 50; // base score
        $reasons = [];

        $disliked = array_map('strtolower', array_map('trim', $preferences->disliked_ingredients ?? []));
        $favoriteIngredients = array_map('strtolower', array_map('trim', $preferences->favorite_ingredients ?? []));
        $dietary = array_map('strtolower', $preferences->dietary_preferences ?? []);
        $favCategories = array_map('strtolower', $preferences->favorite_categories ?? []);
        $favFoodTypes = array_map('strtolower', $preferences->favorite_food_types ?? []);

        $foodIngredients = array_map('strtolower', $food->ingredients_list);
        $foodCategory = strtolower($food->category->name ?? '');
        $foodDesc = strtolower($food->description . ' ' . $food->name);

        // 1. Check disliked ingredients (strict penalty / deal breaker)
        $hasDisliked = false;
        foreach ($disliked as $bad) {
            if ($bad && (in_array($bad, $foodIngredients) || str_contains($foodDesc, $bad))) {
                $hasDisliked = true;
                $score -= 40;
                $reasons[] = "Contains disliked ingredient: {$bad}";
                break;
            }
        }

        // 2. Check dietary restrictions
        if (in_array('vegan', $dietary) || in_array('vegetarian', $dietary)) {
            $meatWords = ['beef', 'chicken', 'meat', 'bacon', 'turkey', 'pepperoni', 'sausage'];
            foreach ($meatWords as $meat) {
                if (str_contains($foodDesc, $meat) || in_array($meat, $foodIngredients)) {
                    $score -= 45;
                    $reasons[] = "Not compatible with vegetarian/vegan preference";
                    break;
                }
            }
        }

        // 3. Category match (+20%)
        if (in_array($foodCategory, $favCategories) || in_array(strtolower($food->name), $favFoodTypes)) {
            $score += 20;
            $reasons[] = "Matches your favorite category: " . ($food->category->name ?? '');
        }

        // 4. Spicy level match (+15%)
        $prefSpicy = (int) $preferences->spicy_level;
        $diffSpicy = abs($prefSpicy - (int) $food->spicy_level);
        if ($diffSpicy === 0) {
            $score += 15;
            $reasons[] = "Perfect spicy level match (Level {$food->spicy_level})";
        } elseif ($diffSpicy === 1) {
            $score += 7;
        } else {
            $score -= 10;
        }

        // 5. Favorite ingredients match (+5% each, up to +15%)
        $matchedIngredients = [];
        foreach ($favoriteIngredients as $fav) {
            if ($fav && (in_array($fav, $foodIngredients) || str_contains($foodDesc, $fav))) {
                $matchedIngredients[] = $fav;
            }
        }
        if (!empty($matchedIngredients)) {
            $score += min(count($matchedIngredients) * 5, 15);
            $reasons[] = "Contains favorite ingredients: " . implode(', ', $matchedIngredients);
        }

        // 6. Price / Budget match (+10%)
        if ($preferences->price_preference && $preferences->price_preference > 0) {
            if ($food->price <= $preferences->price_preference) {
                $score += 10;
                $reasons[] = "Within your budget limit of {$preferences->price_preference} EGP";
            } else {
                $score -= 10;
            }
        }

        // 7. Order history signal (+10%)
        if (isset($orderHistory['food'][$food->id])) {
            $times = $orderHistory['food'][$food->id];
            $score += min($times * 4, 12);
            $reasons[] = "You ordered this {$times} time(s) before";
        }

        // Clamp score between 15% and 98%
        $finalScore = max(15, min(98, (int) round($score)));

        $explanation = !empty($reasons)
            ? implode('. ', $reasons) . '.'
            : 'Well-balanced dish aligned with current offerings.';

        return [
            'score' => $finalScore,
            'explanation' => $explanation,
            'reasons' => $reasons,
        ];
    }

    /**
     * Calculate beverage match percentage & explanation.
     */
    public function calculateBeverageMatch(Beverage $drink, $preferences, array $orderHistory = []): array
    {
        if (!$preferences) {
            return [
                'score' => 70,
                'explanation' => 'Refreshing beverage recommended for everyone.',
                'reasons' => ['Popular beverage'],
            ];
        }

        $score = 50;
        $reasons = [];

        $favBeverages = array_map('strtolower', $preferences->favorite_beverages ?? []);
        $favCategories = array_map('strtolower', $preferences->favorite_categories ?? []);
        $disliked = array_map('strtolower', array_map('trim', $preferences->disliked_ingredients ?? []));

        $drinkCategory = strtolower($drink->category->name ?? '');
        $drinkDesc = strtolower($drink->description . ' ' . $drink->name);
        $ingredients = array_map('strtolower', $drink->ingredients_list);

        // 1. Disliked ingredients
        foreach ($disliked as $bad) {
            if ($bad && (in_array($bad, $ingredients) || str_contains($drinkDesc, $bad))) {
                $score -= 35;
                $reasons[] = "Contains disliked ingredient: {$bad}";
                break;
            }
        }

        // 2. Favorite beverage or category match (+25%)
        if (in_array($drinkCategory, $favCategories) || in_array(strtolower($drink->name), $favBeverages)) {
            $score += 25;
            $reasons[] = "Matches your favorite drink category: " . ($drink->category->name ?? '');
        }

        // 3. Price preference (+10%)
        if ($preferences->price_preference && $drink->price <= ($preferences->price_preference * 0.6)) {
            $score += 10;
            $reasons[] = "Fits comfortably within your drink budget";
        }

        // 4. Taste preference
        if ($preferences->preferred_taste === 'sweet' && (str_contains($drinkDesc, 'chocolate') || str_contains($drinkDesc, 'sweet') || str_contains($drinkDesc, 'juice'))) {
            $score += 10;
            $reasons[] = "Matches sweet taste preference";
        }

        // 5. Order history
        if (isset($orderHistory['beverage'][$drink->id])) {
            $times = $orderHistory['beverage'][$drink->id];
            $score += min($times * 4, 12);
            $reasons[] = "You ordered this beverage {$times} time(s) previously";
        }

        $finalScore = max(20, min(97, (int) round($score)));

        $explanation = !empty($reasons)
            ? implode('. ', $reasons) . '.'
            : 'Refreshing cafeteria drink that complements many meals.';

        return [
            'score' => $finalScore,
            'explanation' => $explanation,
            'reasons' => $reasons,
        ];
    }

    /**
     * Smart Meal / Combo Recommendation (Food + Drink or Food + Drink + Dessert)
     */
    public function getSmartCombos(?User $user): Collection
    {
        $recommendations = $this->getPersonalizedRecommendations($user, 20);
        $topFoods = $recommendations->where('type', 'food')->take(4);
        $topDrinks = $recommendations->where('type', 'drink')->take(4);
        $desserts = FoodItem::whereHas('category', function($q) {
            $q->where('slug', 'desserts')->orWhere('name', 'like', '%Dessert%');
        })->where('status', 'available')->get();

        $combos = collect();

        $i = 0;
        foreach ($topFoods as $food) {
            if ($i >= 3) break;
            $drink = $topDrinks->values()->get($i % max(1, $topDrinks->count()));
            if (!$drink) continue;

            $foodModel = $food['model'];
            $drinkModel = $drink['model'];

            $regularPrice = (float) $foodModel->price + (float) $drinkModel->price;
            $comboDiscount = 0.12; // 12% combo discount
            $comboPrice = round($regularPrice * (1 - $comboDiscount), 2);
            $savings = round($regularPrice - $comboPrice, 2);

            $comboItems = [
                ['name' => $foodModel->name, 'type' => 'food', 'price' => (float) $foodModel->price, 'image' => $foodModel->image],
                ['name' => $drinkModel->name, 'type' => 'beverage', 'price' => (float) $drinkModel->price, 'image' => $drinkModel->image],
            ];

            // For the 3rd combo, add dessert if available!
            if ($i === 2 && $desserts->isNotEmpty()) {
                $dessert = $desserts->first();
                $regularPrice += (float) $dessert->price;
                $comboPrice = round($regularPrice * 0.85, 2); // 15% discount for 3-item combo
                $savings = round($regularPrice - $comboPrice, 2);
                $comboItems[] = [
                    'name' => $dessert->name,
                    'type' => 'food',
                    'price' => (float) $dessert->price,
                    'image' => $dessert->image,
                ];
            }

            $combos->push([
                'id' => 'combo-' . ($i + 1),
                'title' => count($comboItems) === 3
                    ? "Trio Feast: {$foodModel->name} + {$drinkModel->name} + {$dessert->name}"
                    : "Power Duo: {$foodModel->name} + {$drinkModel->name}",
                'items' => $comboItems,
                'food_id' => $foodModel->id,
                'drink_id' => $drinkModel->id,
                'dessert_id' => isset($dessert) ? $dessert->id : null,
                'regular_price' => $regularPrice,
                'combo_price' => $comboPrice,
                'savings' => $savings,
                'synergy_score' => min(99, (int) round(($food['match_percentage'] + $drink['match_percentage']) / 2 + 5)),
                'reason' => count($comboItems) === 3
                    ? "Ultimate complete meal bundle with main dish, refreshing drink, and sweet dessert with 15% savings!"
                    : "Specially paired: savory {$foodModel->name} pairs delightfully with crisp {$drinkModel->name}.",
            ]);

            $i++;
        }

        return $combos;
    }

    /**
     * Budget-based recommendations
     */
    public function getBudgetRecommendations(float $maxBudget, float $minBudget = 0, ?User $user = null): Collection
    {
        $all = $this->getPersonalizedRecommendations($user, 50);

        return $all->filter(function ($item) use ($maxBudget, $minBudget) {
            return $item['price'] >= $minBudget && $item['price'] <= $maxBudget;
        })->values();
    }

    /**
     * Surprise Me recommendation (Time of day + User affinity)
     */
    public function getSurpriseMe(?User $user): array
    {
        $hour = (int) date('H');
        $timeOfDay = 'afternoon';
        if ($hour >= 5 && $hour < 12) {
            $timeOfDay = 'morning';
        } elseif ($hour >= 18 || $hour < 5) {
            $timeOfDay = 'evening';
        }

        $recommendations = $this->getPersonalizedRecommendations($user, 30);

        if ($timeOfDay === 'morning') {
            // Prefer coffee, sandwiches, light bakery
            $candidates = $recommendations->filter(function($i) {
                return str_contains(strtolower($i['category_name']), 'coffee')
                    || str_contains(strtolower($i['category_name']), 'sandwich')
                    || str_contains(strtolower($i['category_name']), 'hot drink');
            });
            if ($candidates->isEmpty()) $candidates = $recommendations;
            $selected = $candidates->random(min(1, $candidates->count()))->first();
            $tagline = "Morning Pick-me-up";
        } elseif ($timeOfDay === 'evening') {
            $selected = $recommendations->take(6)->random();
            $tagline = "Evening Comfort Meal";
        } else {
            $selected = $recommendations->take(5)->random();
            $tagline = "Chef's Lunch Surprise";
        }

        return [
            'item' => $selected,
            'time_of_day' => $timeOfDay,
            'tagline' => $tagline,
            'why' => "Curated for you for {$tagline} based on your preferences and current menu popularity.",
        ];
    }

    /**
     * Get order count map for user
     */
    private function getUserOrderStats(User $user): array
    {
        $stats = ['food' => [], 'beverage' => []];
        $items = OrderItem::whereHas('order', function($q) use ($user) {
            $q->where('user_id', $user->id)->where('status', '!=', 'cancelled');
        })->get();

        foreach ($items as $item) {
            $type = $item->item_type;
            if (isset($stats[$type][$item->item_id])) {
                $stats[$type][$item->item_id] += $item->quantity;
            } else {
                $stats[$type][$item->item_id] = $item->quantity;
            }
        }

        return $stats;
    }
}
