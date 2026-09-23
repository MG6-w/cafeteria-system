<?php

namespace App\Services;

use App\Models\FoodItem;
use App\Models\Beverage;
use App\Models\Category;
use Illuminate\Support\Collection;

class NaturalLanguageSearchService
{
    /**
     * Parse natural language search queries and return relevant items.
     */
    public function search(string $query): Collection
    {
        $cleanQuery = strtolower(trim($query));
        $results = collect();

        // 1. Detect Budget constraints (e.g. "under 150", "less than 100", "50 egp")
        $maxPrice = null;
        if (preg_match('/(?:under|less than|budget|below|up to)\s*(\d+)/i', $cleanQuery, $matches)) {
            $maxPrice = (float) $matches[1];
        } elseif (preg_match('/(\d+)\s*(?:egp|pounds|le)/i', $cleanQuery, $matches)) {
            $maxPrice = (float) $matches[1];
        }

        // 2. Detect Spicy constraint
        $wantSpicy = str_contains($cleanQuery, 'spicy') || str_contains($cleanQuery, 'hot') || str_contains($cleanQuery, 'chili');

        // 3. Detect Light / Healthy constraint
        $wantLight = str_contains($cleanQuery, 'light') || str_contains($cleanQuery, 'healthy') || str_contains($cleanQuery, 'diet') || str_contains($cleanQuery, 'low calorie');

        // 4. Detect Temperature for drinks
        $wantCold = str_contains($cleanQuery, 'cold') || str_contains($cleanQuery, 'iced');
        $wantHot = str_contains($cleanQuery, 'hot') || str_contains($cleanQuery, 'warm');

        // 5. Detect Drink vs Food only
        $isDrinkOnly = str_contains($cleanQuery, 'drink') || str_contains($cleanQuery, 'beverage') || str_contains($cleanQuery, 'juice') || str_contains($cleanQuery, 'coffee');
        $isFoodOnly = str_contains($cleanQuery, 'meal') || str_contains($cleanQuery, 'food') || str_contains($cleanQuery, 'dish') || str_contains($cleanQuery, 'sandwich') || str_contains($cleanQuery, 'burger') || str_contains($cleanQuery, 'pizza');

        // Keywords extraction
        $keywords = preg_split('/\s+/', preg_replace('/[^\w\s]/', '', $cleanQuery));
        $stopWords = ['i', 'want', 'something', 'a', 'an', 'the', 'for', 'with', 'under', 'in', 'and', 'or', 'show', 'me', 'please', 'food', 'meal', 'drink'];
        $meaningfulKeywords = array_diff($keywords, $stopWords);

        // Search Food Items
        if (!$isDrinkOnly) {
            $foodQuery = FoodItem::with('category')->where('status', 'available');

            if ($maxPrice) {
                $foodQuery->where('price', '<=', $maxPrice);
            }
            if ($wantSpicy) {
                $foodQuery->where('spicy_level', '>=', 1);
            }
            if ($wantLight) {
                $foodQuery->where('calories', '<=', 500);
            }

            $foods = $foodQuery->get();

            foreach ($foods as $f) {
                $score = 0;
                $haystack = strtolower($f->name . ' ' . $f->description . ' ' . $f->ingredients . ' ' . ($f->category->name ?? ''));

                foreach ($meaningfulKeywords as $kw) {
                    if (strlen($kw) > 2 && str_contains($haystack, $kw)) {
                        $score += 10;
                    }
                }

                if ($wantSpicy && $f->spicy_level > 0) $score += 15;
                if ($wantLight && $f->calories < 450) $score += 15;

                if ($score > 0 || empty($meaningfulKeywords)) {
                    $results->push([
                        'id' => $f->id,
                        'name' => $f->name,
                        'type' => 'food',
                        'category_name' => $f->category->name ?? 'Food',
                        'price' => (float) $f->price,
                        'calories' => $f->calories,
                        'spicy_level' => $f->spicy_level,
                        'ingredients' => $f->ingredients,
                        'image' => $f->image,
                        'relevance' => $score + ($maxPrice ? 10 : 0),
                        'model' => $f,
                    ]);
                }
            }
        }

        // Search Beverages
        if (!$isFoodOnly) {
            $drinkQuery = Beverage::with('category')->where('status', 'available');

            if ($maxPrice) {
                $drinkQuery->where('price', '<=', $maxPrice);
            }
            if ($wantCold) {
                $drinkQuery->where('temperature', 'cold');
            }
            if ($wantHot) {
                $drinkQuery->where('temperature', 'hot');
            }
            if ($wantLight) {
                $drinkQuery->where('calories', '<=', 150);
            }

            $drinks = $drinkQuery->get();

            foreach ($drinks as $d) {
                $score = 0;
                $haystack = strtolower($d->name . ' ' . $d->description . ' ' . $d->ingredients . ' ' . ($d->category->name ?? ''));

                foreach ($meaningfulKeywords as $kw) {
                    if (strlen($kw) > 2 && str_contains($haystack, $kw)) {
                        $score += 10;
                    }
                }

                if ($wantCold && $d->temperature === 'cold') $score += 15;
                if ($wantHot && $d->temperature === 'hot') $score += 15;

                if ($score > 0 || empty($meaningfulKeywords)) {
                    $results->push([
                        'id' => $d->id,
                        'name' => $d->name,
                        'type' => 'drink',
                        'category_name' => $d->category->name ?? 'Beverage',
                        'price' => (float) $d->price,
                        'calories' => $d->calories,
                        'temperature' => $d->temperature,
                        'size' => $d->size,
                        'ingredients' => $d->ingredients,
                        'image' => $d->image,
                        'relevance' => $score + ($maxPrice ? 10 : 0),
                        'model' => $d,
                    ]);
                }
            }
        }

        return $results->sortByDesc('relevance')->values();
    }
}
