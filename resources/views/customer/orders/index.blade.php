@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">My Orders</h1>
            <p class="text-xs text-slate-500 mt-0.5">Track live preparation progress and view past receipts.</p>
        </div>
        <a href="{{ route('menu.index') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition-colors">
            + New Order
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm">
            <div class="w-14 h-14 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <i data-lucide="receipt" class="w-7 h-7"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">No orders placed yet</h3>
            <p class="text-xs text-slate-500 mt-1">Ready to grab something delicious?</p>
            <a href="{{ route('menu.index') }}" class="inline-block mt-4 px-5 py-2.5 bg-orange-500 text-white rounded-xl text-xs font-bold">Browse Menu</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-slate-900">{{ $order->order_number }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $order->status_badge_class }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                            <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $order->created_at->format('M d, Y &bull; h:i A') }}</span>
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                            <span class="text-base font-black text-orange-600">{{ $order->total_price }} EGP</span>
                            <a href="{{ route('orders.show', $order->id) }}" class="px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5">
                                <i data-lucide="navigation" class="w-3.5 h-3.5"></i> Track
                            </a>
                        </div>
                    </div>

                    <!-- Ordered Items Preview -->
                    <div class="pt-3 flex flex-wrap gap-2 text-xs">
                        @foreach($order->items as $item)
                            <span class="px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-100 text-slate-700 font-medium">
                                {{ $item->quantity }}x {{ $item->item_name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div>
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
