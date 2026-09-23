<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\RecommendationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FoodItemController;
use App\Http\Controllers\Admin\BeverageController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AiChatController;

// Landing / Redirect
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('menu.index');
    }
    return redirect()->route('login');
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated Routes (Customer & General)
Route::middleware('auth')->group(function () {
    // Menu & Browsing
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/food/{foodItem}', [MenuController::class, 'showFood'])->name('food.show');
    Route::get('/beverage/{beverage}', [MenuController::class, 'showBeverage'])->name('beverage.show');
    Route::post('/favorite/toggle', [MenuController::class, 'toggleFavorite'])->name('favorite.toggle');
    Route::post('/review/store', [MenuController::class, 'storeReview'])->name('review.store');

    // Customer Profile & Preferences
    Route::get('/profile', [ProfileController::class, 'show'])->name('customer.profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('customer.profile.update');
    Route::get('/preferences', [ProfileController::class, 'showPreferences'])->name('customer.preferences');
    Route::post('/preferences', [ProfileController::class, 'updatePreferences'])->name('customer.preferences.update');

    // AI Recommendations & Features
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
    Route::get('/combos', [RecommendationController::class, 'combos'])->name('recommendations.combos');
    Route::get('/budget-explorer', [RecommendationController::class, 'budget'])->name('recommendations.budget');
    Route::get('/compare', [RecommendationController::class, 'compare'])->name('recommendations.compare');
    Route::get('/surprise-me', [RecommendationController::class, 'surpriseMe'])->name('recommendations.surprise');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/add-combo', [CartController::class, 'addCombo'])->name('cart.addCombo');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{key}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Role-Aware AI Assistant
    Route::get('/ai-assistant', [AiChatController::class, 'index'])->name('ai.index');
    Route::post('/ai/chat', [AiChatController::class, 'chat'])->name('ai.chat');
});

// Admin Routes (RBAC Protected)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Catalog CRUD
    Route::resource('categories', CategoryController::class);
    Route::resource('food-items', FoodItemController::class);
    Route::resource('beverages', BeverageController::class);

    // Order Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
