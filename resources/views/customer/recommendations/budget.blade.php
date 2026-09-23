@extends('layouts.app')

@section('title', 'Budget Recommendation Explorer')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-6 sm:p-8 text-white shadow-md">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-200">Affordable Taste Finder</span>
                <h1 class="text-2xl sm:text-3xl font-black">Budget-Smart Recommendations</h1>
                <p class="text-xs sm:text-sm text-emerald-100 mt-0.5">Discover top-rated cafeteria food and drinks strictly within your spending limit.</p>
            </div>
        </div>
    </div>

    <!-- Interactive Budget Selector -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <form action="{{ route('recommendations.budget') }}" method="GET" class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Maximum Budget (EGP)</label>
                    <div class="relative">
                        <input type="number" name="max_budget" value="{{ $maxBudget }}" min="20" max="300" step="5"
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 focus:outline-none focus:border-emerald-500">
                        <span class="absolute right-4 top-3 text-xs font-bold text-slate-400">EGP</span>
                    </div>
                </div>

                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Minimum Budget (Optional)</label>
                    <div class="relative">
                        <input type="number" name="min_budget" value="{{ $minBudget }}" min="0" max="300" step="5"
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 focus:outline-none focus:border-emerald-500">
                        <span class="absolute right-4 top-3 text-xs font-bold text-slate-400">EGP</span>
                    </div>
                </div>

                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i> Apply Budget
                </button>
            </div>

            <!-- Quick Budget Presets -->
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100 text-xs">
                <span class="font-bold text-slate-400 text-[11px] uppercase">Quick Presets:</span>
                <a href="{{ route('recommendations.budget', ['max_budget' => 60, 'min_budget' => 0]) }}"
                   class="px-3 py-1 rounded-lg {{ $maxBudget == 60 ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Under 60 EGP</a>
                <a href="{{ route('recommendations.budget', ['max_budget' => 100, 'min_budget' => 0]) }}"
                   class="px-3 py-1 rounded-lg {{ $maxBudget == 100 ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Under 100 EGP</a>
                <a href="{{ route('recommendations.budget', ['max_budget' => 150, 'min_budget' => 100]) }}"
                   class="px-3 py-1 rounded-lg {{ $maxBudget == 150 && $minBudget == 100 ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">100 - 150 EGP</a>
                <a href="{{ route('recommendations.budget', ['max_budget' => 200, 'min_budget' => 0]) }}"
                   class="px-3 py-1 rounded-lg {{ $maxBudget == 200 ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Under 200 EGP</a>
            </div>
        </form>
    </div>

    <!-- Budget Matching Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($items as $item)
            @php
                $isFood = $item['type'] === 'food';
                $showRoute = $isFood ? route('food.show', $item['id']) : route('beverage.show', $item['id']);
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col overflow-hidden hover:shadow-md transition-shadow">
                <div class="relative h-40 bg-slate-100">
                    <img src="{{ $item['image'] ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600' }}"
                         alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                    <span class="absolute top-2.5 left-2.5 bg-emerald-600 text-white px-2 py-0.5 rounded-full text-[10px] font-black shadow-sm">
                        {{ $item['match_percentage'] }}% Match
                    </span>
                    <span class="absolute bottom-2.5 left-2.5 px-2 py-0.5 rounded bg-slate-900/70 text-white text-[10px] font-semibold">
                        {{ $item['category_name'] }}
                    </span>
                </div>

                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-1">
                            <a href="{{ $showRoute }}" class="text-sm font-black text-slate-900 hover:text-emerald-600 transition-colors">
                                {{ $item['name'] }}
                            </a>
                            <span class="text-sm font-black text-emerald-600">{{ $item['price'] }} EGP</span>
                        </div>
                        <p class="text-xs text-slate-500 line-clamp-2 mb-2">{{ $item['explanation'] }}</p>
                    </div>

                    <form action="{{ route('cart.add') }}" method="POST" class="pt-2 border-t border-slate-100 mt-2">
                        @csrf
                        <input type="hidden" name="item_type" value="{{ $isFood ? 'food' : 'beverage' }}">
                        <input type="hidden" name="item_id" value="{{ $item['id'] }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-colors">
                            + Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-slate-200">
                <i data-lucide="wallet" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <h3 class="text-base font-bold text-slate-700">No items found within {{ $minBudget }} - {{ $maxBudget }} EGP</h3>
                <p class="text-xs text-slate-500 mt-1">Try expanding your budget range.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
