<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CustomerPreference;
use App\Models\Category;
use App\Models\FoodItem;
use App\Models\Beverage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Favorite;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'name' => 'Cafeteria Admin',
            'email' => 'admin@cafeteria.com',
            'phone' => '+20 100 000 0001',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $customer = User::create([
            'name' => 'Mostafa Ahmed',
            'email' => 'customer@cafeteria.com',
            'phone' => '+20 111 222 3333',
            'role' => 'customer',
            'password' => Hash::make('password'),
        ]);

        $customer2 = User::create([
            'name' => 'Sara Mahmoud',
            'email' => 'sara@cafeteria.com',
            'phone' => '+20 122 333 4444',
            'role' => 'customer',
            'password' => Hash::make('password'),
        ]);

        // 2. Customer Preferences (Configured to mirror prompt section 11!)
        CustomerPreference::create([
            'user_id' => $customer->id,
            'age' => 24,
            'favorite_categories' => ['Pizza', 'Sandwiches', 'Pasta', 'Coffee'],
            'favorite_food_types' => ['Chicken', 'Spicy Food', 'Pizza'],
            'favorite_beverages' => ['Cappuccino', 'Fresh Orange Juice'],
            'preferred_taste' => 'spicy',
            'dietary_preferences' => [],
            'price_preference' => 150.00,
            'spicy_level' => 2, // Medium to Hot
            'favorite_ingredients' => ['chicken', 'cheese', 'jalapeno', 'garlic'],
            'disliked_ingredients' => ['mushrooms', 'olives'],
        ]);

        CustomerPreference::create([
            'user_id' => $customer2->id,
            'age' => 27,
            'favorite_categories' => ['Desserts', 'Juices', 'Sandwiches'],
            'favorite_food_types' => ['Salads', 'Desserts'],
            'favorite_beverages' => ['Fresh Orange Juice', 'Smoothies'],
            'preferred_taste' => 'sweet',
            'dietary_preferences' => ['vegetarian'],
            'price_preference' => 120.00,
            'spicy_level' => 0,
            'favorite_ingredients' => ['chocolate', 'honey', 'strawberries'],
            'disliked_ingredients' => ['beef', 'bacon'],
        ]);

        // 3. Categories
        $categoriesData = [
            ['name' => 'Pizza', 'slug' => 'pizza', 'type' => 'food', 'icon' => 'pizza', 'description' => 'Stone-baked artisanal pizzas with fresh mozzarella & toppings.'],
            ['name' => 'Sandwiches', 'slug' => 'sandwiches', 'type' => 'food', 'icon' => 'sandwich', 'description' => 'Crispy and grilled gourmet sandwiches.'],
            ['name' => 'Burgers', 'slug' => 'burgers', 'type' => 'food', 'icon' => 'burger', 'description' => 'Juicy handcrafted flame-grilled burgers.'],
            ['name' => 'Pasta', 'slug' => 'pasta', 'type' => 'food', 'icon' => 'utensils', 'description' => 'Freshly tossed Italian pastas with rich sauces.'],
            ['name' => 'Desserts', 'slug' => 'desserts', 'type' => 'food', 'icon' => 'cake', 'description' => 'Decadent sweet treats, cakes, and pastries.'],
            ['name' => 'Coffee', 'slug' => 'coffee', 'type' => 'beverage', 'icon' => 'coffee', 'description' => 'Single origin espresso, lattes, and hot brews.'],
            ['name' => 'Juices', 'slug' => 'juices', 'type' => 'beverage', 'icon' => 'citrus', 'description' => 'Freshly squeezed natural fruit juices.'],
            ['name' => 'Soft Drinks', 'slug' => 'soft-drinks', 'type' => 'beverage', 'icon' => 'sparkles', 'description' => 'Chilled sodas and sparkling refreshments.'],
            ['name' => 'Hot Drinks', 'slug' => 'hot-drinks', 'type' => 'beverage', 'icon' => 'flame', 'description' => 'Soothing organic herbal and green teas.'],
            ['name' => 'Cold Drinks', 'slug' => 'cold-drinks', 'type' => 'beverage', 'icon' => 'snowflake', 'description' => 'Iced lattes, smoothies, and frappes.'],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['slug']] = Category::create($c);
        }

        // 4. Food Items
        $foodItemsData = [
            [
                'category_id' => $categories['pizza']->id,
                'name' => 'Spicy Chicken Pizza',
                'description' => 'Hand-stretched crust loaded with spiced grilled chicken, fiery jalapenos, melted mozzarella, and fresh tomato basil sauce.',
                'price' => 140.00,
                'ingredients' => 'grilled chicken, mozzarella cheese, jalapeno, tomato sauce, oregano, chili flakes',
                'calories' => 780,
                'spicy_level' => 2,
                'available_quantity' => 25,
                'preparation_time' => 18,
                'image' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['sandwiches']->id,
                'name' => 'Chicken Sandwich',
                'description' => 'Crispy golden chicken fillet topped with melted cheddar cheese, fresh lettuce, and garlic herb mayo in a toasted brioche bun.',
                'price' => 120.00,
                'ingredients' => 'crispy chicken, cheddar cheese, lettuce, garlic mayo, brioche bun',
                'calories' => 550,
                'spicy_level' => 1,
                'available_quantity' => 30,
                'preparation_time' => 12,
                'image' => 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['pasta']->id,
                'name' => 'Spicy Chicken Pasta',
                'description' => 'Penne tossed in fiery arrabbiata tomato sauce with tender chicken cubes, crushed garlic, and grated aged parmesan.',
                'price' => 150.00,
                'ingredients' => 'penne pasta, grilled chicken, arrabbiata sauce, garlic, parmesan, chili',
                'calories' => 680,
                'spicy_level' => 2,
                'available_quantity' => 20,
                'preparation_time' => 15,
                'image' => 'https://images.unsplash.com/photo-1621996346565-e3d5d6281691?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['burgers']->id,
                'name' => 'Beef Burger',
                'description' => 'Classic 100% Angus beef patty flame-grilled to perfection, layered with cheddar, crisp lettuce, tomato, and secret cafeteria burger sauce.',
                'price' => 160.00,
                'ingredients' => 'beef patty, cheddar cheese, lettuce, tomato, pickles, secret sauce',
                'calories' => 740,
                'spicy_level' => 0,
                'available_quantity' => 22,
                'preparation_time' => 15,
                'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['pizza']->id,
                'name' => 'Mushroom Pizza',
                'description' => 'Earthy sauteed button mushrooms, Kalamata black olives, rich tomato base, and bubbly mozzarella on crispy crust.',
                'price' => 130.00,
                'ingredients' => 'mushrooms, black olives, mozzarella cheese, tomato sauce, thyme',
                'calories' => 620,
                'spicy_level' => 0,
                'available_quantity' => 18,
                'preparation_time' => 18,
                'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['desserts']->id,
                'name' => 'Chocolate Lava Cake',
                'description' => 'Warm dark chocolate cake with a molten fudge core, dusted with powdered sugar and cocoa.',
                'price' => 85.00,
                'ingredients' => 'belgian dark chocolate, butter, cocoa, sugar, vanilla',
                'calories' => 430,
                'spicy_level' => 0,
                'available_quantity' => 15,
                'preparation_time' => 8,
                'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['sandwiches']->id,
                'name' => 'Grilled Chicken Caesar Wrap',
                'description' => 'Tender chicken breast, crisp romaine lettuce, parmesan shavings, and light caesar dressing wrapped in a toasted tortilla.',
                'price' => 110.00,
                'ingredients' => 'grilled chicken, romaine lettuce, parmesan, light caesar dressing, tortilla wrap',
                'calories' => 410,
                'spicy_level' => 0,
                'available_quantity' => 20,
                'preparation_time' => 10,
                'image' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['burgers']->id,
                'name' => 'Firehouse Volcano Burger',
                'description' => 'Spicy beef patty crusted with crushed peppercorns, pepper jack cheese, habanero glaze, and grilled jalapenos.',
                'price' => 175.00,
                'ingredients' => 'spicy beef patty, pepper jack cheese, jalapeno, habanero glaze, red onions',
                'calories' => 810,
                'spicy_level' => 3,
                'available_quantity' => 12,
                'preparation_time' => 16,
                'image' => 'https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['sandwiches']->id,
                'name' => 'Smoked Turkey & Cheese Club',
                'description' => 'Triple-decker toasted bread filled with smoked turkey breast, Swiss cheese, sliced hardboiled egg, and honey mustard.',
                'price' => 115.00,
                'ingredients' => 'smoked turkey, swiss cheese, egg, tomato, honey mustard',
                'calories' => 490,
                'spicy_level' => 0,
                'available_quantity' => 3, // Low stock sample!
                'preparation_time' => 10,
                'image' => 'https://images.unsplash.com/photo-1553909489-cd47e0907980?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
        ];

        $createdFoods = [];
        foreach ($foodItemsData as $item) {
            $createdFoods[] = FoodItem::create($item);
        }

        // 5. Beverages
        $beveragesData = [
            [
                'category_id' => $categories['coffee']->id,
                'name' => 'Cappuccino',
                'description' => 'Rich double shot espresso crowned with velvety steamed milk and a thick layer of microfoam.',
                'price' => 80.00,
                'size' => 'medium',
                'ingredients' => 'espresso coffee beans, steamed whole milk, cocoa dust',
                'calories' => 120,
                'temperature' => 'hot',
                'available_quantity' => 40,
                'image' => 'https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['juices']->id,
                'name' => 'Fresh Orange Juice',
                'description' => '100% natural, freshly squeezed Valencia oranges. Rich in vitamin C with zero added sugar.',
                'price' => 60.00,
                'size' => 'medium',
                'ingredients' => 'fresh valencia oranges',
                'calories' => 110,
                'temperature' => 'cold',
                'available_quantity' => 35,
                'image' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['cold-drinks']->id,
                'name' => 'Iced Spanish Latte',
                'description' => 'Creamy cold beverage crafted with espresso, condensed milk, whole milk, and served over clear ice blocks.',
                'price' => 95.00,
                'size' => 'large',
                'ingredients' => 'espresso, condensed milk, fresh milk, ice',
                'calories' => 240,
                'temperature' => 'cold',
                'available_quantity' => 28,
                'image' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['coffee']->id,
                'name' => 'Espresso Doppio',
                'description' => 'Intense double extraction of premium dark roasted Arabica beans with a thick hazelnut crema.',
                'price' => 50.00,
                'size' => 'small',
                'ingredients' => 'dark roasted arabica coffee',
                'calories' => 5,
                'temperature' => 'hot',
                'available_quantity' => 50,
                'image' => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['soft-drinks']->id,
                'name' => 'Coca Cola Can',
                'description' => 'Ice-cold refreshing classic cola.',
                'price' => 30.00,
                'size' => 'medium',
                'ingredients' => 'carbonated water, sugar, caramel',
                'calories' => 140,
                'temperature' => 'cold',
                'available_quantity' => 60,
                'image' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['cold-drinks']->id,
                'name' => 'Tropical Mango Smoothie',
                'description' => 'Sweet ripe Egyptian mangoes blended with Greek yogurt, honey, and crushed ice.',
                'price' => 75.00,
                'size' => 'large',
                'ingredients' => 'fresh mango, greek yogurt, pure honey, ice',
                'calories' => 195,
                'temperature' => 'cold',
                'available_quantity' => 2, // Low stock sample!
                'image' => 'https://images.unsplash.com/photo-1546173159-315724a31696?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
            [
                'category_id' => $categories['hot-drinks']->id,
                'name' => 'Organic Mint Green Tea',
                'description' => 'Whole green tea leaves infused with fresh garden spearmint leaves.',
                'price' => 45.00,
                'size' => 'medium',
                'ingredients' => 'organic green tea leaves, spearmint, hot water',
                'calories' => 0,
                'temperature' => 'hot',
                'available_quantity' => 45,
                'image' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?auto=format&fit=crop&w=600&q=80',
                'status' => 'available',
            ],
        ];

        $createdDrinks = [];
        foreach ($beveragesData as $drink) {
            $createdDrinks[] = Beverage::create($drink);
        }

        // 6. Sample Orders (Enabling history analytics & signals)
        $order1 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-00101',
            'user_id' => $customer->id,
            'total_price' => 220.00,
            'status' => 'completed',
            'payment_status' => 'paid',
            'notes' => 'Please make the pizza extra crispy.',
            'created_at' => now()->subHours(5),
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'item_type' => 'food',
            'item_id' => $createdFoods[0]->id, // Spicy Chicken Pizza
            'item_name' => 'Spicy Chicken Pizza',
            'quantity' => 1,
            'price' => 140.00,
            'subtotal' => 140.00,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'item_type' => 'beverage',
            'item_id' => $createdDrinks[0]->id, // Cappuccino
            'item_name' => 'Cappuccino',
            'quantity' => 1,
            'price' => 80.00,
            'subtotal' => 80.00,
        ]);

        $order2 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-00102',
            'user_id' => $customer->id,
            'total_price' => 180.00,
            'status' => 'preparing',
            'payment_status' => 'cash_on_delivery',
            'notes' => 'Deliver to table 4.',
            'created_at' => now()->subMinutes(25),
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'item_type' => 'food',
            'item_id' => $createdFoods[1]->id, // Chicken Sandwich
            'item_name' => 'Chicken Sandwich',
            'quantity' => 1,
            'price' => 120.00,
            'subtotal' => 120.00,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'item_type' => 'beverage',
            'item_id' => $createdDrinks[1]->id, // Fresh Orange Juice
            'item_name' => 'Fresh Orange Juice',
            'quantity' => 1,
            'price' => 60.00,
            'subtotal' => 60.00,
        ]);

        // Reviews
        Review::create([
            'user_id' => $customer->id,
            'item_type' => 'food',
            'item_id' => $createdFoods[0]->id,
            'rating' => 5,
            'comment' => 'The spicy chicken pizza is by far the best item in the cafeteria! Perfectly spiced.',
        ]);

        Review::create([
            'user_id' => $customer->id,
            'item_type' => 'beverage',
            'item_id' => $createdDrinks[0]->id,
            'rating' => 5,
            'comment' => 'Great cappuccino, creamy milk foam and strong coffee aroma.',
        ]);

    // Favorites

Favorite::create([
    'user_id' => $customer->id,
    'item_type' => 'food',
    'item_id' => $createdFoods[0]->id,
]);

Favorite::create([
    'user_id' => $customer->id,
    'item_type' => 'beverage',
    'item_id' => $createdDrinks[0]->id,
]);

Favorite::create([
    'user_id' => $customer2->id,
    'item_type' => 'food',
    'item_id' => $createdFoods[5]->id,
]);

Favorite::create([
    'user_id' => $customer2->id,
    'item_type' => 'beverage',
    'item_id' => $createdDrinks[1]->id,
]);
    }
}
