@extends('layouts.app')

@section('title', 'Cafeteria Menu')

@section('content')
<div class="space-y-6">

    <!-- Top AI Natural Language Search Hero -->
    <div class="bg-gradient-to-r from-orange-500 via-amber-500 to-amber-600 rounded-2xl p-6 sm:p-8 text-white shadow-md relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider backdrop-blur-md mb-3">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> AI Natural-Language Search
            </span>
            <h1 class="text-2xl sm:text-3xl font-black leading-tight">What are you craving today?</h1>
            <p class="text-xs sm:text-sm text-orange-100 mt-1 mb-4">
                Ask using natural language — like <span class="italic underline decoration-amber-300">"spicy meal with cheese under 150 EGP"</span> or <span class="italic underline decoration-amber-300">"healthy cold drink"</span>.
            </p>

            <form action="{{ route('menu.index') }}" method="GET" class="flex gap-2">
                <div class="relative flex-1">
                    <input type="text" name="nlp_query" placeholder="e.g., spicy chicken pasta under 160 EGP, or light breakfast..."
                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-white text-slate-900 placeholder-slate-400 text-xs sm:text-sm font-medium focus:outline-none shadow-lg">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                </div>
                <button type="submit" class="px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-lg transition-all flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-4 h-4 text-amber-400"></i>
                    <span>AI Search</span>
                </button>
            </form>
        </div>

        <div class="absolute -right-8 -bottom-10 opacity-15 pointer-events-none">
            <i data-lucide="utensils" class="w-72 h-72 text-white"></i>
        </div>
    </div>

    <!-- Filter & Categories Bar -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm space-y-4">
        <form action="{{ route('menu.index') }}" method="GET" class="space-y-4">

            <!-- Category Pills Row -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                <a href="{{ route('menu.index', array_merge(request()->except('category', 'page'))) }}"
                   class="px-3.5 py-1.5 rounded-xl font-bold whitespace-nowrap transition-colors {{ !request('category') ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All Categories
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('menu.index', array_merge(request()->except('page'), ['category' => $cat->id])) }}"
                       class="px-3.5 py-1.5 rounded-xl font-semibold whitespace-nowrap transition-colors {{ request('category') == $cat->id ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>

            <!-- Multi-Filter Inputs -->
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3 pt-3 border-t border-slate-100 text-xs">
                <!-- Search by name -->
                <div>
                    <label class="block font-bold text-slate-600 uppercase tracking-wider text-[10px] mb-1">Search Name</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Item name..."
                           class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:border-orange-500">
                </div>

                <!-- Food vs Drink -->
                <div>
                    <label class="block font-bold text-slate-600 uppercase tracking-wider text-[10px] mb-1">Item Type</label>
                    <select name="type" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:border-orange-500">
                        <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>All Items</option>
                        <option value="food" {{ request('type') === 'food' ? 'selected' : '' }}>Food Only</option>
                        <option value="beverage" {{ request('type') === 'beverage' ? 'selected' : '' }}>Beverages Only</option>
                    </select>
                </div>

                <!-- Max Price -->
                <div>
                    <label class="block font-bold text-slate-600 uppercase tracking-wider text-[10px] mb-1">Max Price (EGP)</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="e.g. 150"
                           class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:border-orange-500">
                </div>

                <!-- Spicy Level -->
                <div>
                    <label class="block font-bold text-slate-600 uppercase tracking-wider text-[10px] mb-1">Spicy Level</label>
                    <select name="spicy_level" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:border-orange-500">
                        <option value="">Any Heat</option>
                        <option value="0" {{ request('spicy_level') === '0' ? 'selected' : '' }}>🌿 Non-Spicy</option>
                        <option value="1" {{ request('spicy_level') === '1' ? 'selected' : '' }}>🌶️ Mild</option>
                        <option value="2" {{ request('spicy_level') === '2' ? 'selected' : '' }}>🌶️🌶️ Medium</option>
                        <option value="3" {{ request('spicy_level') === '3' ? 'selected' : '' }}>🔥 Extra Hot</option>
                    </select>
                </div>

                <!-- Max Calories -->
                <div>
                    <label class="block font-bold text-slate-600 uppercase tracking-wider text-[10px] mb-1">Max Calories</label>
                    <input type="number" name="max_calories" value="{{ request('max_calories') }}" placeholder="e.g. 600"
                           class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 focus:outline-none focus:border-orange-500">
                </div>

                <!-- Submit / Reset -->
                <div class="flex items-end gap-1.5">
                    <button type="submit" class="flex-1 py-1.5 px-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-bold transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('menu.index') }}" class="py-1.5 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-center font-medium">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Items Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($items as $item)
            @php
                $isFood = $item->item_type === 'food';
                $showRoute = $isFood ? route('food.show', $item->id) : route('beverage.show', $item->id);
                $isFav = isset($userFavorites[$item->item_type]) && in_array($item->id, $userFavorites[$item->item_type]);
                $matchScore = $item->match_percentage ?? 75;

                $matchColor = $matchScore >= 85
                    ? 'bg-emerald-500 text-white'
                    : ($matchScore >= 60 ? 'bg-amber-500 text-white' : 'bg-slate-400 text-white');
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col overflow-hidden group">
                <!-- Image & Badges -->
                <div class="relative h-44 bg-slate-100 overflow-hidden">
                    <a href="{{ $showRoute }}" class="block w-full h-full">
                        <img src="{{ $item->image ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600' }}"
                             alt="{{ $item->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </a>

                    <!-- Match Percentage Badge -->
                    <div class="absolute top-2.5 left-2.5 {{ $matchColor }} px-2.5 py-1 rounded-full text-[11px] font-black shadow-md flex items-center gap-1">
                        <i data-lucide="sparkles" class="w-3 h-3"></i>
                        <span>{{ $matchScore }}% Match</span>
                    </div>

                    <!-- Favorite Button -->
                    <form action="{{ route('favorite.toggle') }}" method="POST" class="absolute top-2.5 right-2.5">
                        @csrf
                        <input type="hidden" name="item_type" value="{{ $item->item_type }}">
                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                        <button type="submit" class="w-8 h-8 rounded-full bg-white/90 backdrop-blur-md flex items-center justify-center text-slate-400 hover:text-rose-500 shadow-sm transition-colors">
                            <i data-lucide="heart" class="w-4 h-4 {{ $isFav ? 'fill-rose-500 text-rose-500' : '' }}"></i>
                        </button>
                    </form>

                    <!-- Type Tag -->
                    <span class="absolute bottom-2.5 left-2.5 px-2 py-0.5 rounded-md bg-slate-900/70 backdrop-blur-md text-white text-[10px] font-semibold uppercase tracking-wider">
                        {{ $item->category->name ?? ($isFood ? 'Food' : 'Drink') }}
                    </span>
                </div>

                <!-- Card Body -->
                <div class="p-4 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-1">
                        <a href="{{ $showRoute }}" class="text-sm font-black text-slate-900 hover:text-orange-600 transition-colors line-clamp-1">
                            {{ $item->name }}
                        </a>
                        <span class="text-sm font-black text-orange-600 ml-2 whitespace-nowrap">{{ $item->price }} EGP</span>
                    </div>

                    <p class="text-xs text-slate-500 line-clamp-2 mb-3">{{ $item->description }}</p>

                    <!-- Meta specs: Calories, Spicy, Temp -->
                    <div class="flex items-center gap-2 text-[11px] text-slate-500 mb-3 flex-wrap">
                        @if($item->calories)
                            <span class="flex items-center gap-1"><i data-lucide="flame" class="w-3 h-3 text-amber-500"></i> {{ $item->calories }} kcal</span>
                        @endif

                        @if($isFood && $item->spicy_level > 0)
                            <span class="flex items-center text-rose-600 font-semibold">
                                {{ str_repeat('🌶️', $item->spicy_level) }}
                            </span>
                        @endif

                        @if(!$isFood && $item->temperature)
                            <span class="flex items-center gap-0.5 text-slate-600 font-medium">
                                {{ $item->temperature === 'cold' ? '❄️ Cold' : '☕ Hot' }}
                            </span>
                        @endif

                        <span class="ml-auto text-[10px] font-semibold px-1.5 py-0.5 rounded {{ $item->available_quantity <= 5 ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-600' }}">
                            {{ $item->available_quantity }} left
                        </span>
                    </div>

                    <!-- AI Explanation snippet -->
                    @if(!empty($item->match_explanation))
                        <div class="bg-amber-50/70 border border-amber-100 rounded-lg p-2 text-[11px] text-amber-900 mb-4 flex items-start gap-1.5">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5"></i>
                            <span class="line-clamp-2 italic">{{ $item->match_explanation }}</span>
                        </div>
                    @endif

                    <!-- Add to Cart Footer -->
                    <div class="mt-auto pt-2 border-t border-slate-100 flex items-center gap-2">
                        <form action="{{ route('cart.add') }}" method="POST" class="w-full">
                            @csrf
                            <input type="hidden" name="item_type" value="{{ $item->item_type }}">
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit"
                                    {{ !$item->isAvailable() ? 'disabled' : '' }}
                                    class="w-full py-2 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all {{ $item->isAvailable() ? 'bg-orange-500 hover:bg-orange-600 text-white shadow-sm' : 'bg-slate-100 text-slate-400 cursor-not-allowed' }}">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>{{ $item->isAvailable() ? 'Add to Cart' : 'Out of Stock' }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-slate-200">
                <i data-lucide="utensils" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <h3 class="text-base font-bold text-slate-700">No menu items match your criteria</h3>
                <p class="text-xs text-slate-500 mt-1">Try relaxing some filters or resetting the search.</p>
                <a href="{{ route('menu.index') }}" class="inline-block mt-4 px-4 py-2 bg-orange-500 text-white rounded-xl text-xs font-bold">Reset Filters</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
