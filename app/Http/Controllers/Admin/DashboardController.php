<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\FoodItem;
use App\Models\Beverage;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        // Core KPIs
        $totalCustomers = User::where('role', 'customer')->count();
        $ordersToday = Order::whereDate('created_at', $today)->count();
        $salesToday = (float) Order::whereDate('created_at', $today)->where('status', '!=', 'cancelled')->sum('total_price');
        $totalRevenue = (float) Order::where('status', '!=', 'cancelled')->sum('total_price');

        // Low stock items (< 5 units)
        $lowStockFoods = FoodItem::where('available_quantity', '<=', 5)->get();
        $lowStockBeverages = Beverage::where('available_quantity', '<=', 5)->get();
        $totalLowStockCount = $lowStockFoods->count() + $lowStockBeverages->count();

        // Top 5 selling items
        $topSellingItems = OrderItem::selectRaw('item_name, item_type, sum(quantity) as total_sold, sum(subtotal) as total_amount')
            ->groupBy('item_name', 'item_type')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Category breakdown
        $categories = Category::withCount(['foodItems', 'beverages'])->get();

        // Recent Orders
        $recentOrders = Order::with(['user', 'items'])->latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'totalCustomers',
            'ordersToday',
            'salesToday',
            'totalRevenue',
            'totalLowStockCount',
            'lowStockFoods',
            'lowStockBeverages',
            'topSellingItems',
            'categories',
            'recentOrders'
        ));
    }
}
