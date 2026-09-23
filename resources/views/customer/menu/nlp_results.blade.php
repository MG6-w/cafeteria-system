@extends('layouts.app')

@section('title', 'AI Search Results')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-orange-600 bg-orange-50 px-2.5 py-1 rounded-md">AI Natural-Language Search</span>
            <h1 class="text-xl font-black text-slate-900 mt-2">Results for: "<span class="italic text-orange-600">{{ $query }}</span>"</h1>
            <p class="text-xs text-slate-500 mt-1">Found {{ $results->count() }} items matching your criteria.</p>
        </div>
        <a href="{{ route('menu.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">
            &larr; Back to Full Menu
        </a>
    </div>

    @if($results->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200">
            <i data-lucide="sparkles" class="w-12 h-12 text-amber-400 mx-auto mb-3"></i>
            <h3 class="text-base font-bold text-slate-700">No items directly matched that phrase</h3>
            <p class="text-xs text-slate-500 mt-1">Try querying a different craving, e.g. "something spicy under 150" or "cold drinks".</p>
            <a href="{{ route('menu.index') }}" class="inline-block mt-4 px-4 py-2 bg-orange-500 text-white rounded-xl text-xs font-bold">Browse All Menu</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($results as $item)
                @php
                    $isFood = $item['type'] === 'food';
                    $showRoute = $isFood ? route('food.show', $item['id']) : route('beverage.show', $item['id']);
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col overflow-hidden">
                    <div class="relative h-40 bg-slate-100 overflow-hidden">
                        <a href="{{ $showRoute }}">
                            <img src="{{ $item['image'] ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600' }}"
                                 alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                        </a>
                        <span class="absolute top-2.5 left-2.5 bg-indigo-600 text-white px-2 py-0.5 rounded-full text-[10px] font-bold">
                            Match Score: {{ $item['relevance'] }}
                        </span>
                        <span class="absolute bottom-2.5 left-2.5 px-2 py-0.5 rounded-md bg-slate-900/70 text-white text-[10px] font-semibold">
                            {{ $item['category_name'] }}
                        </span>
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-1">
                            <a href="{{ $showRoute }}" class="text-sm font-black text-slate-900 hover:text-orange-600">{{ $item['name'] }}</a>
                            <span class="text-sm font-black text-orange-600">{{ $item['price'] }} EGP</span>
                        </div>
                        <p class="text-xs text-slate-500 mb-3 line-clamp-2">{{ $item['ingredients'] }}</p>
                        <div class="mt-auto pt-2 border-t border-slate-100">
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="item_type" value="{{ $isFood ? 'food' : 'beverage' }}">
                                <input type="hidden" name="item_id" value="{{ $item['id'] }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-full py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition-colors">
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
