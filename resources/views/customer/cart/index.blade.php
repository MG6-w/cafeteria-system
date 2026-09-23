@extends('layouts.app')

@section('title', 'My Cart')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Your Cafeteria Cart</h1>
            <p class="text-xs text-slate-500 mt-0.5">Review items before placing your order.</p>
        </div>
        @if(!empty($cart))
            <form action="{{ route('cart.clear') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-700">Clear All Items</button>
            </form>
        @endif
    </div>

    @if(empty($cart))
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm">
            <div class="w-14 h-14 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <i data-lucide="shopping-bag" class="w-7 h-7"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">Your cart is empty</h3>
            <p class="text-xs text-slate-500 mt-1">Explore our chef-crafted menu and AI recommendations!</p>
            <div class="mt-5 flex justify-center gap-3">
                <a href="{{ route('menu.index') }}" class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold shadow-sm transition-colors">
                    Browse Menu
                </a>
                <a href="{{ route('recommendations.index') }}" class="px-5 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-xl text-xs font-bold transition-colors">
                    AI Recommendations
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Left: Cart Items List -->
            <div class="lg:col-span-2 space-y-3">
                @foreach($cart as $item)
                    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex items-center gap-4">
                        <img src="{{ $item['image'] ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100' }}"
                             class="w-16 h-16 rounded-xl object-cover shrink-0 border border-slate-100">

                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-black text-slate-900 truncate">{{ $item['name'] }}</h4>
                            <span class="text-xs font-bold text-orange-600">{{ $item['price'] }} EGP each</span>
                        </div>

                        <!-- Quantity form -->
                        <form action="{{ route('cart.update') }}" method="POST" class="flex items-center gap-1.5">
                            @csrf
                            <input type="hidden" name="key" value="{{ $item['key'] }}">
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="20"
                                   onchange="this.form.submit()"
                                   class="w-14 px-2 py-1 rounded-lg border border-slate-300 text-xs font-bold text-center focus:outline-none focus:border-orange-500">
                        </form>

                        <!-- Line subtotal -->
                        <div class="text-right w-20">
                            <span class="text-sm font-black text-slate-900">{{ number_format($item['price'] * $item['quantity'], 2) }} EGP</span>
                        </div>

                        <!-- Remove button -->
                        <form action="{{ route('cart.remove', $item['key']) }}" method="POST">
                            @csrf
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <!-- Right: Order Summary & Checkout -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-black text-slate-900 border-b border-slate-100 pb-3">Order Summary</h3>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Items Subtotal:</span>
                        <span class="font-bold">{{ number_format($total, 2) }} EGP</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Service / Prep:</span>
                        <span class="font-bold text-emerald-600">FREE</span>
                    </div>
                    <div class="flex justify-between text-sm font-black text-slate-900 pt-3 border-t border-slate-100">
                        <span>Total Amount:</span>
                        <span class="text-orange-600">{{ number_format($total, 2) }} EGP</span>
                    </div>
                </div>

                <form action="{{ route('orders.store') }}" method="POST" class="space-y-4 pt-2">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Payment Method</label>
                        <select name="payment_status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold focus:outline-none focus:border-orange-500">
                            <option value="cash_on_delivery">💵 Cash on Pickup / Delivery</option>
                            <option value="paid">💳 Pre-paid (Account Balance / Card)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kitchen Notes (Optional)</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Extra napkins, table 4, dressing on side..."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-orange-500"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Confirm & Place Order ({{ number_format($total, 2) }} EGP)
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
