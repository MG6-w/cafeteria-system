<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FoodItem;
use App\Models\Beverage;
use App\Services\RecommendationService;
use App\Services\AiChatbotService;

class CafeteriaAiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_customer_recommendation_match_percentage_and_disliked_penalty(): void
    {
        $recService = app(RecommendationService::class);
        $customer = User::where('email', 'customer@cafeteria.com')->first();
        $this->assertNotNull($customer, 'Customer user should exist from seeding.');

        $spicyPizza = FoodItem::where('name', 'Spicy Chicken Pizza')->first();
        $this->assertNotNull($spicyPizza);
        $scoreSpicy = $recService->calculateFoodMatch($spicyPizza, $customer->preference);

        // Spicy chicken pizza matches customer's preference for spicy chicken and budget
        $this->assertGreaterThanOrEqual(85, $scoreSpicy['score'], 'Spicy chicken pizza should have a high match score (>85%).');

        $mushroomPizza = FoodItem::where('name', 'Mushroom Pizza')->first();
        $this->assertNotNull($mushroomPizza);
        $scoreMushroom = $recService->calculateFoodMatch($mushroomPizza, $customer->preference);

        // Mushroom pizza contains disliked ingredients (mushrooms & olives)
        $this->assertLessThanOrEqual(40, $scoreMushroom['score'], 'Mushroom pizza should have a low match score due to disliked ingredients.');
    }

    public function test_ai_chatbot_security_pipeline_blocks_unauthorized_customer(): void
    {
        $aiService = app(AiChatbotService::class);
        $customer = User::where('email', 'customer@cafeteria.com')->first();
        $this->assertNotNull($customer);

        // Customer attempts to view cafeteria sales
        $response = $aiService->handleUserMessage($customer, 'What are today\'s total sales amount?');

        $this->assertEquals('forbidden', $response['status']);
        $this->assertStringContainsString('Access Denied', $response['message']);
    }

    public function test_ai_chatbot_admin_authorized_inquiry(): void
    {
        $aiService = app(AiChatbotService::class);
        $admin = User::where('email', 'admin@cafeteria.com')->first();
        $this->assertNotNull($admin);

        // Admin asks for today's sales
        $response = $aiService->handleUserMessage($admin, 'What are today\'s total sales?');

        $this->assertEquals('success', $response['status']);
        $this->assertEquals('admin', $response['role']);
        $this->assertStringContainsString('Financial Summary', $response['message']);
    }

    public function test_role_middleware_guards_admin_routes(): void
    {
        $customer = User::where('email', 'customer@cafeteria.com')->first();

        // Customer attempting to access admin dashboard should receive 403 Forbidden
        $response = $this->actingAs($customer)->get('/admin/dashboard');
        $response->assertStatus(403);
    }
}
