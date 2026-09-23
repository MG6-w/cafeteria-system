@extends('layouts.app')

@section('title', 'Admin - Order Details')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-orange-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Orders
        </a>
        <span class="text-xs text-slate-400">Placed on {{ $order->created_at->format('M d, Y h:i A') }}</span>
    </div>

    <!-- Main Order Details Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Order Reference</span>
                <h1 class="text-2xl font-black text-slate-900">{{ $order->order_number }}</h1>
                <p class="text-xs text-slate-500 mt-1">Customer: <strong class="text-slate-800">{{ $order->user->name ?? 'Guest' }}</strong> ({{ $order->user->email ?? 'No email' }} &bull; {{ $order->user->phone ?? 'No phone' }})</p>
            </div>

            <!-- Status Transition Form -->
            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <label class="text-xs font-bold text-slate-600">Status:</label>
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-bold focus:outline-none focus:border-orange-500">
                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </form>
        </div>

        <!-- Items Table -->
        <div>
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Order Items</h3>
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($order->items as $item)
                    <div class="py-3 flex justify-between items-center">
                        <div>
                            <span class="font-bold text-slate-900 text-sm">{{ $item->item_name }}</span>
                            <span class="text-[11px] text-slate-400 block">{{ $item->quantity }} x {{ $item->price }} EGP &bull; Type: {{ ucfirst($item->item_type) }}</span>
                        </div>
                        <span class="font-black text-slate-900 text-sm">{{ $item->subtotal }} EGP</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Summary & Notes -->
        <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
            <div class="flex justify-between text-slate-600">
                <span>Payment Method:</span>
                <span class="font-bold uppercase tracking-wider">{{ str_replace('_', ' ', $order->payment_status) }}</span>
            </div>
            @if($order->notes)
                <div class="flex justify-between text-slate-600">
                    <span>Customer Notes:</span>
                    <span class="italic text-slate-800">{{ $order->notes }}</span>
                </div>
            @endif
            @if($order->cancelled_reason)
                <div class="flex justify-between text-rose-600 font-bold">
                    <span>Cancellation Reason:</span>
                    <span>{{ $order->cancelled_reason }}</span>
                </div>
            @endif
            <div class="flex justify-between text-lg font-black text-slate-900 pt-3 border-t border-slate-100">
                <span>Total Amount:</span>
                <span class="text-orange-600">{{ $order->total_price }} EGP</span>
            </div>
        </div>
    </div>
</div>
@endsection
