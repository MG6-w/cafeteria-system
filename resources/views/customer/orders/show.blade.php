@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-orange-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Orders
        </a>
        <span class="text-xs text-slate-400">Placed on {{ $order->created_at->format('M d, Y h:i A') }}</span>
    </div>

    <!-- Status Tracking Stepper -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Order Number</span>
                <h1 class="text-xl font-black text-slate-900">{{ $order->order_number }}</h1>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border {{ $order->status_badge_class }}">
                {{ $order->status }}
            </span>
        </div>

        @if($order->status === 'cancelled')
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 flex items-start gap-2">
                <i data-lucide="x-circle" class="w-4 h-4 text-rose-600 shrink-0 mt-0.5"></i>
                <div>
                    <span class="font-bold block">Order Cancelled</span>
                    <p class="mt-0.5">{{ $order->cancelled_reason ?? 'Order was cancelled per customer/cafeteria request.' }}</p>
                </div>
            </div>
        @else
            <!-- 4-Step Progress Stepper -->
            @php
                $steps = [
                    'pending' => ['title' => 'Received', 'desc' => 'Order placed', 'icon' => 'receipt'],
                    'preparing' => ['title' => 'Preparing', 'desc' => 'In the kitchen', 'icon' => 'flame'],
                    'ready' => ['title' => 'Ready', 'desc' => 'At pickup counter', 'icon' => 'bell'],
                    'completed' => ['title' => 'Completed', 'desc' => 'Enjoy your meal', 'icon' => 'check-circle'],
                ];
                $stepKeys = array_keys($steps);
                $currentIndex = array_search($order->status, $stepKeys);
                if ($currentIndex === false) $currentIndex = 0;
            @endphp

            <div class="relative py-4">
                <div class="overflow-hidden h-1.5 mb-6 text-xs flex rounded bg-slate-100 relative">
                    <div style="width: {{ ($currentIndex / 3) * 100 }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-orange-500 to-amber-500 transition-all duration-500"></div>
                </div>

                <div class="grid grid-cols-4 text-center">
                    @foreach($steps as $key => $s)
                        @php
                            $stepIndex = array_search($key, $stepKeys);
                            $isDone = $stepIndex <= $currentIndex;
                            $isCurrent = $stepIndex === $currentIndex;
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center mb-2 shadow-xs transition-colors {{ $isDone ? 'bg-orange-500 text-white shadow-orange-500/30' : 'bg-slate-100 text-slate-400' }}">
                                <i data-lucide="{{ $s['icon'] }}" class="w-4 h-4"></i>
                            </div>
                            <span class="text-xs font-black {{ $isCurrent ? 'text-orange-600' : ($isDone ? 'text-slate-800' : 'text-slate-400') }}">
                                {{ $s['title'] }}
                            </span>
                            <span class="text-[10px] text-slate-400 hidden sm:block">{{ $s['desc'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Order Items Details -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-sm font-black text-slate-900 border-b border-slate-100 pb-3">Items in this Order</h3>

        <div class="divide-y divide-slate-100 text-xs">
            @foreach($order->items as $item)
                <div class="py-3 flex justify-between items-center">
                    <div>
                        <h4 class="font-bold text-slate-900">{{ $item->item_name }}</h4>
                        <span class="text-[11px] text-slate-400">{{ $item->quantity }} x {{ $item->price }} EGP &bull; Type: <span class="capitalize">{{ $item->item_type }}</span></span>
                    </div>
                    <span class="font-black text-slate-900 text-sm">{{ $item->subtotal }} EGP</span>
                </div>
            @endforeach
        </div>

        <div class="pt-4 border-t border-slate-100 space-y-1.5 text-xs">
            <div class="flex justify-between text-slate-600">
                <span>Payment Method:</span>
                <span class="font-bold uppercase tracking-wider">{{ str_replace('_', ' ', $order->payment_status) }}</span>
            </div>
            @if($order->notes)
                <div class="flex justify-between text-slate-600">
                    <span>Kitchen Notes:</span>
                    <span class="italic text-slate-800">{{ $order->notes }}</span>
                </div>
            @endif
            <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-slate-100">
                <span>Total:</span>
                <span class="text-orange-600">{{ $order->total_price }} EGP</span>
            </div>
        </div>
    </div>

    <!-- Cancellation Option (Rules: Only if status is 'pending') -->
    @if($order->canBeCancelled())
        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h4 class="text-xs font-black text-slate-800">Need to cancel this order?</h4>
                <p class="text-[11px] text-slate-500 mt-0.5">Orders can be cancelled before kitchen preparation starts.</p>
            </div>
            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition-colors">
                    Cancel Order
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
