@extends('layouts.app')

@section('title', 'Manage Beverages')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Beverages Management</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage coffee, cold brews, smoothies, sodas, and fresh juices.</p>
        </div>
        <a href="{{ route('admin.beverages.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Beverage
        </a>
    </div>

    <!-- Filter bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
        <form action="{{ route('admin.beverages.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search beverages..."
                       class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-indigo-500">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
            </div>
            <div class="w-full sm:w-48">
                <select name="category_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Beverage</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Price</th>
                        <th class="py-3.5 px-4">Size</th>
                        <th class="py-3.5 px-4">Temperature</th>
                        <th class="py-3.5 px-4">In Stock</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($beverages as $drink)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-5 font-bold text-slate-900 flex items-center gap-3">
                                <img src="{{ $drink->image ?: 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=80' }}"
                                     class="w-10 h-10 rounded-lg object-cover border border-slate-100 shrink-0">
                                <div>
                                    <span class="block text-slate-900">{{ $drink->name }}</span>
                                    <span class="block text-[10px] text-slate-400 font-normal truncate max-w-xs">{{ $drink->ingredients }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">{{ $drink->category->name ?? 'None' }}</td>
                            <td class="py-3.5 px-4 font-black text-slate-900">{{ $drink->price }} EGP</td>
                            <td class="py-3.5 px-4 capitalize font-semibold text-slate-700">{{ $drink->size }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $drink->temperature === 'cold' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-800' }}">
                                    {{ $drink->temperature === 'cold' ? '❄️ Cold' : '☕ Hot' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded font-black text-[10px] {{ $drink->available_quantity <= 5 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $drink->available_quantity }} units
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $drink->status === 'available' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $drink->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.beverages.edit', $drink->id) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 transition-colors">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.beverages.destroy', $drink->id) }}" method="POST" onsubmit="return confirm('Delete this beverage?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition-colors">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $beverages->links() }}
        </div>
    </div>
</div>
@endsection
