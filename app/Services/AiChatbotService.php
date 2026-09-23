<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\FoodItem;
use App\Models\Beverage;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatbotService
{
    protected RecommendationService $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Main entry point: Authenticated request processing pipeline:
     * Authentication -> Authorization -> User Role -> Allowed Data Scope -> AI -> Response
     */
    public function handleUserMessage(User $user, string $message): array
    {
        $role = $user->role;
        $lowerMsg = strtolower($message);

        // Security Barrier: Check if Customer attempts to access Admin-Only inquiries
        if ($role === 'customer' && $this->isAdminOnlyQuery($lowerMsg)) {
            return [
                'role' => 'system',
                'status' => 'forbidden',
                'message' => '⛔ Access Denied: As a customer, you do not have permission to access cafeteria administrative data, sales records, customer lists, or stock metrics. Please feel free to ask about our food, drinks, recommendations, ingredients, or prices!',
                'suggestions' => [
                    'What food do you recommend for me?',
                    'Show me spicy meals under 150 EGP',
                    'What drinks do you have?',
                    'What is the healthiest meal available?',
                ],
            ];
        }

        if ($role === 'admin') {
            return $this->handleAdminInquiry($user, $message, $lowerMsg);
        } else {
            return $this->handleCustomerInquiry($user, $message, $lowerMsg);
        }
    }

    /**
     * Check if a prompt contains administrative or financial requests.
     */
    protected function isAdminOnlyQuery(string $msg): bool
    {
        $adminKeywords = [
            'how many customer', 'all customer', 'list customer', 'total sale', 'total revenue',
            'today sales', "today's sale", 'how much revenue', 'cafeteria statistic', 'low stock',
            'stock level', 'inventory', 'never been ordered', 'unauthorized', 'admin', 'how much money',
            'profit', 'orders were placed today', 'order count today', 'sales amount'
        ];

        foreach ($adminKeywords as $kw) {
            if (str_contains($msg, $kw)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Admin AI Chatbot logic: Scoped to authorized analytics only.
     */
    protected function handleAdminInquiry(User $admin, string $rawMessage, string $lowerMsg): array
    {
        // 1. Prepare Authorized Data Aggregations
        $customerCount = User::where('role', 'customer')->count();
        $totalFoodItems = FoodItem::count();
        $availableFoodCount = FoodItem::where('status', 'available')->where('available_quantity', '>', 0)->count();
        $totalBeverages = Beverage::count();
        $availableBeverages = Beverage::where('status', 'available')->where('available_quantity', '>', 0)->count();

        $todayOrdersCount = Order::whereDate('created_at', today())->count();
        $todaySales = (float) Order::whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        $allTimeSales = (float) Order::where('status', '!=', 'cancelled')->sum('total_price');

        // Low stock items (quantity <= 5)
        $lowStockFood = FoodItem::where('available_quantity', '<=', 5)->get(['id', 'name', 'available_quantity']);
        $lowStockDrinks = Beverage::where('available_quantity', '<=', 5)->get(['id', 'name', 'available_quantity']);

        // Most ordered food
        $topFoodId = OrderItem::where('item_type', 'food')
            ->selectRaw('item_id, sum(quantity) as total_qty')
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->first();
        $topFood = $topFoodId ? FoodItem::find($topFoodId->item_id) : null;
        $topFoodName = $topFood ? "{$topFood->name} ({$topFoodId->total_qty} ordered)" : "None yet";

        // Most popular beverage
        $topDrinkId = OrderItem::where('item_type', 'beverage')
            ->selectRaw('item_id, sum(quantity) as total_qty')
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->first();
        $topDrink = $topDrinkId ? Beverage::find($topDrinkId->item_id) : null;
        $topDrinkName = $topDrink ? "{$topDrink->name} ({$topDrinkId->total_qty} ordered)" : "None yet";

        // Top 5 selling items
        $top5Items = OrderItem::selectRaw('item_name, item_type, sum(quantity) as total_qty, sum(subtotal) as total_rev')
            ->groupBy('item_name', 'item_type')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Items never ordered
        $orderedFoodIds = OrderItem::where('item_type', 'food')->pluck('item_id')->toArray();
        $neverOrderedFood = FoodItem::whereNotIn('id', $orderedFoodIds)->pluck('name')->toArray();

        // Category with most items
        $categoryCounts = Category::withCount(['foodItems', 'beverages'])->get()->map(function($c) {
            return ['name' => $c->name, 'count' => $c->food_items_count + $c->beverages_count];
        })->sortByDesc('count')->first();

        // Check if user has Gemini API key configured
        $geminiResponse = $this->callGeminiIfConfigured($rawMessage, "You are the AI Assistant for the Cafeteria Administrator. You are given authorized administrative aggregates and statistics. Answer accurately and professionally in a friendly tone using the provided data only.\nData:\n- Total Customers: {$customerCount}\n- Available Food: {$availableFoodCount} (Total: {$totalFoodItems})\n- Available Beverages: {$availableBeverages} (Total: {$totalBeverages})\n- Today's Orders: {$todayOrdersCount}\n- Today's Total Sales: {$todaySales} EGP\n- All-time Sales: {$allTimeSales} EGP\n- Most Ordered Food: {$topFoodName}\n- Most Popular Drink: {$topDrinkName}\n- Category with most items: " . ($categoryCounts['name'] ?? 'N/A') . " ({$categoryCounts['count']} items)\n- Low Stock Items: " . $lowStockFood->pluck('name')->merge($lowStockDrinks->pluck('name'))->implode(', ') . "\n- Never Ordered: " . implode(', ', $neverOrderedFood));

        if ($geminiResponse) {
            return [
                'role' => 'admin',
                'status' => 'success',
                'message' => $geminiResponse,
                'is_ai' => true,
            ];
        }

        // Local Semantic & Rule-Based Intelligence
        if (str_contains($lowerMsg, 'customer')) {
            $msg = "📊 **Customer Analytics:** Currently, there are **{$customerCount} registered customers** in the cafeteria system.";
        } elseif (str_contains($lowerMsg, 'food item') && (str_contains($lowerMsg, 'available') || str_contains($lowerMsg, 'how many'))) {
            $msg = "🍱 **Food Inventory:** We currently have **{$availableFoodCount} available food items** ready to serve (out of {$totalFoodItems} total catalog items).";
        } elseif (str_contains($lowerMsg, 'category') && (str_contains($lowerMsg, 'most') || str_contains($lowerMsg, 'popular'))) {
            $catName = $categoryCounts['name'] ?? 'None';
            $catNum = $categoryCounts['count'] ?? 0;
            $msg = "📂 **Category Distribution:** The category with the most menu items is **{$catName}** with **{$catNum} items**.";
        } elseif (str_contains($lowerMsg, 'most ordered food') || (str_contains($lowerMsg, 'food') && str_contains($lowerMsg, 'most'))) {
            $msg = "🥇 **Best-Selling Food:** The most ordered food item is **{$topFoodName}**.";
        } elseif (str_contains($lowerMsg, 'popular drink') || str_contains($lowerMsg, 'beverage')) {
            $msg = "🥤 **Best-Selling Beverage:** The most popular drink is **{$topDrinkName}**.";
        } elseif (str_contains($lowerMsg, 'order') && str_contains($lowerMsg, 'today')) {
            $msg = "📋 **Today's Orders:** A total of **{$todayOrdersCount} orders** have been placed today.";
        } elseif (str_contains($lowerMsg, 'sales') || str_contains($lowerMsg, 'revenue')) {
            $msg = "💰 **Financial Summary:**\n- **Today's Sales:** " . number_format($todaySales, 2) . " EGP\n- **All-Time Sales:** " . number_format($allTimeSales, 2) . " EGP from completed and active orders.";
        } elseif (str_contains($lowerMsg, 'low') || str_contains($lowerMsg, 'stock')) {
            if ($lowStockFood->isEmpty() && $lowStockDrinks->isEmpty()) {
                $msg = "✅ **Inventory Status:** All items have healthy stock levels (above 5 units).";
            } else {
                $itemsList = [];
                foreach ($lowStockFood as $f) $itemsList[] = "{$f->name} ({$f->available_quantity} left)";
                foreach ($lowStockDrinks as $d) $itemsList[] = "{$d->name} ({$d->available_quantity} left)";
                $msg = "⚠️ **Low Stock Alert (< 5 units):**\n- " . implode("\n- ", $itemsList);
            }
        } elseif (str_contains($lowerMsg, 'top 5') || str_contains($lowerMsg, 'selling')) {
            if ($top5Items->isEmpty()) {
                $msg = "No order data available yet to determine top sellers.";
            } else {
                $lines = ["🏆 **Top 5 Best Selling Items:**"];
                foreach ($top5Items as $idx => $item) {
                    $lines[] = ($idx + 1) . ". **{$item->item_name}** ({$item->item_type}) - {$item->total_qty} units sold (" . number_format($item->total_rev, 2) . " EGP)";
                }
                $msg = implode("\n", $lines);
            }
        } elseif (str_contains($lowerMsg, 'never')) {
            if (empty($neverOrderedFood)) {
                $msg = "🎉 Great news! All food items on the menu have been ordered at least once.";
            } else {
                $msg = "ℹ️ **Food items that have never been ordered:**\n- " . implode("\n- ", array_slice($neverOrderedFood, 0, 8));
            }
        } else {
            $msg = "👨‍💼 **Cafeteria Executive Briefing:**\n"
                . "• **Active Customers:** {$customerCount}\n"
                . "• **Today's Orders:** {$todayOrdersCount} orders (" . number_format($todaySales, 2) . " EGP)\n"
                . "• **Top Food:** {$topFoodName}\n"
                . "• **Top Drink:** {$topDrinkName}\n"
                . "• **Low Stock Items:** " . ($lowStockFood->count() + $lowStockDrinks->count()) . " item(s)\n"
                . "You can ask for specific statistics like low stock, category breakdowns, or sales reports.";
        }

        return [
            'role' => 'admin',
            'status' => 'success',
            'message' => $msg,
            'is_ai' => false,
        ];
    }

    /**
     * Customer AI Chatbot logic: Scoped to menu, recommendations, diet, and user preferences.
     */
    protected function handleCustomerInquiry(User $customer, string $rawMessage, string $lowerMsg): array
    {
        $preferences = $customer->preference;

        // Extract budget if mentioned (e.g., "under 100", "150 egp", "less than 80")
        $budget = null;
        if (preg_match('/(?:under|less than|budget|below|up to)\s*(\d+)/i', $rawMessage, $matches)) {
            $budget = (float) $matches[1];
        } elseif (preg_match('/(\d+)\s*(?:egp|pounds|le)/i', $rawMessage, $matches)) {
            $budget = (float) $matches[1];
        }

        // Check if user is asking for recommendations
        if (str_contains($lowerMsg, 'recommend') || str_contains($lowerMsg, 'match my preferences') || str_contains($lowerMsg, 'what should i eat')) {
            $recs = $this->recommendationService->getPersonalizedRecommendations($customer, 4);

            $lines = ["✨ **AI Recommendations for you:**"];
            foreach ($recs as $rec) {
                $lines[] = "• **{$rec['name']}** ({$rec['category_name']}) - **{$rec['price']} EGP** | **{$rec['match_percentage']}% Match**\n  _{$rec['explanation']}_";
            }
            $lines[] = "\n💡 _Tip: You can update your food profile preferences anytime to adjust your match results!_";

            return [
                'role' => 'customer',
                'status' => 'success',
                'message' => implode("\n", $lines),
            ];
        }

        // Check for Spicy request
        if (str_contains($lowerMsg, 'spicy')) {
            $spicyQuery = FoodItem::where('spicy_level', '>=', 1)->where('status', 'available');
            if ($budget) $spicyQuery->where('price', '<=', $budget);
            $spicyFoods = $spicyQuery->orderByDesc('spicy_level')->take(4)->get();

            if ($spicyFoods->isEmpty()) {
                return [
                    'role' => 'customer',
                    'status' => 'success',
                    'message' => "I couldn't find spicy items matching that exact price. Try increasing your budget or checking our Spicy Chicken Pizza & Pasta!",
                ];
            }

            $lines = ["🌶️ **Spicy Recommendations:**"];
            foreach ($spicyFoods as $f) {
                $flame = str_repeat('🔥', max(1, $f->spicy_level));
                $lines[] = "• **{$f->name}** ({$f->price} EGP) {$flame} (Spiciness: Level {$f->spicy_level}/3)\n  _Ingredients: {$f->ingredients}_";
            }
            return [
                'role' => 'customer',
                'status' => 'success',
                'message' => implode("\n", $lines),
            ];
        }

        // Check for Drinks request
        if (str_contains($lowerMsg, 'drink') || str_contains($lowerMsg, 'beverage') || str_contains($lowerMsg, 'coffee') || str_contains($lowerMsg, 'juice')) {
            $isCold = str_contains($lowerMsg, 'cold') || str_contains($lowerMsg, 'iced');
            $isHot = str_contains($lowerMsg, 'hot') || str_contains($lowerMsg, 'warm');

            $query = Beverage::where('status', 'available');
            if ($isCold) $query->where('temperature', 'cold');
            if ($isHot) $query->where('temperature', 'hot');
            if ($budget) $query->where('price', '<=', $budget);

            $drinks = $query->take(4)->get();

            $lines = ["🥤 **Recommended Beverages:**"];
            foreach ($drinks as $d) {
                $tempIcon = $d->temperature === 'cold' ? '❄️' : '☕';
                $lines[] = "• **{$d->name}** ({$d->price} EGP) {$tempIcon} ({$d->temperature}, size {$d->size}) - {$d->calories} cal";
            }
            return [
                'role' => 'customer',
                'status' => 'success',
                'message' => implode("\n", $lines),
            ];
        }

        // Check for Budget inquiry
        if ($budget) {
            $budgetItems = $this->recommendationService->getBudgetRecommendations($budget, 0, $customer)->take(5);

            if ($budgetItems->isEmpty()) {
                return [
                    'role' => 'customer',
                    'status' => 'success',
                    'message' => "We don't currently have items under {$budget} EGP. Our lowest priced refreshments start around 30-50 EGP!",
                ];
            }

            $lines = ["💵 **Best Items Under {$budget} EGP:**"];
            foreach ($budgetItems as $item) {
                $lines[] = "• **{$item['name']}** ({$item['type']}) - **{$item['price']} EGP** ({$item['match_percentage']}% Match)";
            }
            return [
                'role' => 'customer',
                'status' => 'success',
                'message' => implode("\n", $lines),
            ];
        }

        // Check for Healthiest / low calories
        if (str_contains($lowerMsg, 'healthy') || str_contains($lowerMsg, 'calorie') || str_contains($lowerMsg, 'light')) {
            $healthiestFood = FoodItem::where('status', 'available')->orderBy('calories', 'asc')->take(3)->get();
            $healthiestDrink = Beverage::where('status', 'available')->orderBy('calories', 'asc')->take(2)->get();

            $lines = ["🥗 **Healthiest Low-Calorie Selections:**"];
            foreach ($healthiestFood as $f) {
                $lines[] = "• **{$f->name}** - {$f->calories} kcal | {$f->price} EGP\n  _Ingredients: {$f->ingredients}_";
            }
            $lines[] = "\n**Guilt-Free Drinks:**";
            foreach ($healthiestDrink as $d) {
                $lines[] = "• **{$d->name}** - {$d->calories} kcal | {$d->price} EGP";
            }
            return [
                'role' => 'customer',
                'status' => 'success',
                'message' => implode("\n", $lines),
            ];
        }

        // Check for Ingredients in a meal
        if (str_contains($lowerMsg, 'ingredient') || str_contains($lowerMsg, 'what is in') || str_contains($lowerMsg, 'contains')) {
            $foods = FoodItem::all();
            $matched = null;
            foreach ($foods as $food) {
                if (str_contains($lowerMsg, strtolower($food->name))) {
                    $matched = $food;
                    break;
                }
            }

            if ($matched) {
                return [
                    'role' => 'customer',
                    'status' => 'success',
                    'message' => "🍽️ **{$matched->name} Details:**\n- **Ingredients:** {$matched->ingredients}\n- **Calories:** {$matched->calories} kcal\n- **Spiciness:** Level {$matched->spicy_level}/3\n- **Prep Time:** ~{$matched->preparation_time} minutes\n- **Price:** {$matched->price} EGP",
                ];
            }
        }

        // Check for Meal Comparison (e.g. "compare Pizza and Burger")
        if (str_contains($lowerMsg, 'compare')) {
            $availableFoods = FoodItem::where('status', 'available')->get();
            $found = [];
            foreach ($availableFoods as $item) {
                if (str_contains($lowerMsg, strtolower($item->name)) || str_contains($lowerMsg, strtolower(explode(' ', $item->name)[0]))) {
                    $found[] = $item;
                    if (count($found) >= 2) break;
                }
            }

            if (count($found) >= 2) {
                $f1 = $found[0];
                $f2 = $found[1];
                $match1 = $this->recommendationService->calculateFoodMatch($f1, $preferences);
                $match2 = $this->recommendationService->calculateFoodMatch($f2, $preferences);

                $msg = "⚖️ **AI Comparison:**\n\n"
                    . "| Metric | **{$f1->name}** | **{$f2->name}** |\n"
                    . "|---|---|---|\n"
                    . "| **Price** | {$f1->price} EGP | {$f2->price} EGP |\n"
                    . "| **Calories** | {$f1->calories} kcal | {$f2->calories} kcal |\n"
                    . "| **Spiciness** | Level {$f1->spicy_level} | Level {$f2->spicy_level} |\n"
                    . "| **Your Match** | **{$match1['score']}%** | **{$match2['score']}%** |\n\n"
                    . "💡 **Verdict:** " . ($match1['score'] >= $match2['score'] ? "{$f1->name} aligns closer with your profile tastes!" : "{$f2->name} is a stronger match for your current preferences!");

                return [
                    'role' => 'customer',
                    'status' => 'success',
                    'message' => $msg,
                ];
            }
        }

        // Fallback or Gemini LLM
        $geminiResponse = $this->callGeminiIfConfigured($rawMessage, "You are a friendly cafeteria assistant helping a customer. You only recommend food and drinks from our menu. Do not share administrative or financial information.\nCustomer Preferences: " . json_encode($preferences));
        if ($geminiResponse) {
            return [
                'role' => 'customer',
                'status' => 'success',
                'message' => $geminiResponse,
                'is_ai' => true,
            ];
        }

        return [
            'role' => 'customer',
            'status' => 'success',
            'message' => "👋 Hello! I can help you find meals that match your taste preferences, suggest cold/hot drinks, find meals under a specific budget (e.g. 'under 100 EGP'), recommend spicy dishes, or compare two items. What would you like to explore today?",
            'suggestions' => [
                'What food do you recommend for me?',
                'I want something spicy under 150 EGP',
                'What drinks are suitable for me?',
                'What is the healthiest meal available?',
            ],
        ];
    }

    /**
     * Optional Gemini API call if key is provided in .env
     */
    protected function callGeminiIfConfigured(string $prompt, string $systemInstruction): ?string
    {
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
            ]);

            if ($response->successful()) {
                $json = $response->json();
                return $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini API call failed: ' . $e->getMessage());
        }

        return null;
    }
}
