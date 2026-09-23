@extends('layouts.app')

@section('title', 'Manage Orders')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Orders Management</h1>
            <p class="text-xs text-slate-500 mt-0.5">Control live order statuses, monitor kitchen fulfillment, and review history.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex-1 flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order # or customer name..."
                       class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-orange-500">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
            </div>
            <div class="w-full sm:w-48">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-orange-500">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Order #</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4">Date & Time</th>
                        <th class="py-3.5 px-4">Total</th>
                        <th class="py-3.5 px-4">Payment</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-5 text-right">Update Workflow</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($orders as $order)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-5 font-black text-slate-900">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="hover:text-orange-600">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ $order->user->name ?? 'Guest' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $order->user->email ?? '' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">{{ $order->created_at->format('M d, Y &bull; h:i A') }}</td>
                            <td class="py-3.5 px-4 font-black text-orange-600">{{ $order->total_price }} EGP</td>
                            <td class="py-3.5 px-4 capitalize font-medium text-slate-700">{{ str_replace('_', ' ', $order->payment_status) }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $order->status_badge_class }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="inline-flex items-center gap-2">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="px-2 py-1 rounded-lg border border-slate-300 text-[11px] font-bold focus:outline-none">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                        <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="p-1.5 text-slate-400 hover:text-orange-600">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
