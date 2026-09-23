@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Admin Header & Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <span class="text-[10px] font-black uppercase tracking-wider text-purple-700 bg-purple-50 px-2.5 py-1 rounded-md">Executive Management</span>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Cafeteria Operations Dashboard</h1>
            <p class="text-xs text-slate-500 mt-0.5">Live metrics, catalog management, and AI intelligence console.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.food-items.create') }}" class="px-3.5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-sm">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Food
            </a>
            <a href="{{ route('admin.beverages.create') }}" class="px-3.5 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-sm">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Drink
            </a>
            <a href="{{ route('admin.categories.create') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-sm">
                <i data-lucide="folder-plus" class="w-3.5 h-3.5"></i> Add Category
            </a>
            <button @click="chatOpen = true" class="px-3.5 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                <i data-lucide="bot" class="w-3.5 h-3.5"></i> AI Analytics
            </button>
        </div>
    </div>

    <!-- 4 Key Analytics KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Sales Today -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="dollar-sign" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Today's Sales</span>
                <span class="text-xl font-black text-slate-900">{{ number_format($salesToday, 2) }} EGP</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">All-time: {{ number_format($totalRevenue, 2) }} EGP</span>
            </div>
        </div>

        <!-- Orders Today -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                <i data-lucide="receipt" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Today's Orders</span>
                <span class="text-xl font-black text-slate-900">{{ $ordersToday }}</span>
                <span class="text-[10px] text-blue-600 font-semibold block mt-0.5">Incoming & Active</span>
            </div>
        </div>

        <!-- Customers -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Registered Guests</span>
                <span class="text-xl font-black text-slate-900">{{ $totalCustomers }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">Profiles with AI tastes</span>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl {{ $totalLowStockCount > 0 ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center shrink-0">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Low Stock Items</span>
                <span class="text-xl font-black {{ $totalLowStockCount > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $totalLowStockCount }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">< 5 units remaining</span>
            </div>
        </div>
    </div>

    <!-- Low Stock Table Alert (if any) -->
    @if($totalLowStockCount > 0)
        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 text-xs text-rose-900 shadow-sm">
            <div class="flex items-center gap-2 font-black text-sm text-rose-800 mb-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i> Critical Low Stock Warning (< 5 units)
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($lowStockFoods as $f)
                    <span class="px-3 py-1 rounded-lg bg-white border border-rose-300 font-bold text-rose-800 flex items-center gap-1.5 shadow-2xs">
                        🍱 {{ $f->name }} &bull; <strong class="text-rose-600">{{ $f->available_quantity }} left</strong>
                    </span>
                @endforeach
                @foreach($lowStockBeverages as $b)
                    <span class="px-3 py-1 rounded-lg bg-white border border-rose-300 font-bold text-rose-800 flex items-center gap-1.5 shadow-2xs">
                        🥤 {{ $b->name }} &bull; <strong class="text-rose-600">{{ $b->available_quantity }} left</strong>
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 5 Best Selling Items -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
            <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                <i data-lucide="trophy" class="w-4 h-4 text-amber-500"></i> Top 5 Selling Items
            </h3>
            <div class="divide-y divide-slate-100 text-xs">
                @forelse($topSellingItems as $idx => $item)
                    <div class="py-2.5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center text-[10px]">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <span class="font-bold text-slate-900">{{ $item->item_name }}</span>
                                <span class="block text-[10px] text-slate-400 capitalize">{{ $item->item_type }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-slate-900">{{ $item->total_sold }} units</span>
                            <span class="block text-[10px] text-orange-600 font-bold">{{ number_format($item->total_amount, 2) }} EGP</span>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-400 italic py-2">No completed sales recorded yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Category Item Distribution -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
            <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                <i data-lucide="pie-chart" class="w-4 h-4 text-indigo-500"></i> Category Inventory Breakdown
            </h3>
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($categories as $cat)
                    <div class="py-2.5 flex items-center justify-between">
                        <span class="font-bold text-slate-800">{{ $cat->name }}</span>
                        <div class="flex items-center gap-3">
                            <span class="text-[11px] text-slate-500">{{ $cat->food_items_count }} foods &bull; {{ $cat->beverages_count }} drinks</span>
                            <span class="px-2 py-0.5 rounded bg-slate-100 font-black text-slate-700 text-[10px]">
                                {{ $cat->food_items_count + $cat->beverages_count }} total
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Orders Table with Status Update -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4 text-orange-500"></i> Recent Orders Live Monitor
            </h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-orange-600 hover:underline">View All Orders &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Order #</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Items</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Current Status</th>
                        <th class="py-3 px-5 text-right">Update Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentOrders as $order)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-5 font-black text-slate-900">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="hover:text-orange-600">{{ $order->order_number }}</a>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-700">{{ $order->user->name ?? 'Customer' }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $order->items->count() }} item(s)</td>
                            <td class="py-3 px-4 font-black text-orange-600">{{ $order->total_price }} EGP</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $order->status_badge_class }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="py-3 px-5 text-right">
                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="inline-flex items-center gap-1">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="px-2 py-1 rounded-lg border border-slate-300 text-[11px] font-bold focus:outline-none">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                        <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
