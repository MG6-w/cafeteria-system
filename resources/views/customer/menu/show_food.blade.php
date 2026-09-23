@extends('layouts.app')

@section('title', $foodItem->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <a href="{{ route('menu.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-orange-600 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Menu
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <!-- Left Image -->
            <div class="relative h-72 md:h-full bg-slate-100">
                <img src="{{ $foodItem->image ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600' }}"
                     alt="{{ $foodItem->name }}"
                     class="w-full h-full object-cover">
                <div class="absolute top-4 left-4 bg-emerald-500 text-white px-3 py-1 rounded-full text-xs font-black shadow-md flex items-center gap-1">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                    <span>{{ $foodItem->match_percentage }}% Match</span>
                </div>
            </div>

            <!-- Right Details -->
            <div class="p-6 sm:p-8 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="px-2.5 py-1 rounded-md bg-orange-50 text-orange-700 text-xs font-bold uppercase tracking-wider">
                            {{ $foodItem->category->name ?? 'Food' }}
                        </span>
                        <span class="text-xl font-black text-orange-600">{{ $foodItem->price }} EGP</span>
                    </div>

                    <h1 class="text-2xl font-black text-slate-900 mb-2">{{ $foodItem->name }}</h1>
                    <p class="text-xs text-slate-600 leading-relaxed mb-4">{{ $foodItem->description }}</p>

                    <!-- AI Explanation Card -->
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3.5 mb-6 text-xs">
                        <div class="flex items-center gap-1.5 font-bold text-amber-900 mb-1">
                            <i data-lucide="bot" class="w-4 h-4 text-amber-600"></i> AI Match Insights:
                        </div>
                        <p class="text-amber-800 italic leading-relaxed">{{ $foodItem->match_explanation }}</p>
                    </div>

                    <!-- Specs Breakdown -->
                    <div class="grid grid-cols-3 gap-2 text-center text-xs mb-6">
                        <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <span class="text-[10px] text-slate-400 block uppercase font-bold">Calories</span>
                            <span class="font-black text-slate-800">{{ $foodItem->calories ?? 'N/A' }} kcal</span>
                        </div>
                        <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <span class="text-[10px] text-slate-400 block uppercase font-bold">Spicy Level</span>
                            <span class="font-black text-rose-600">{{ str_repeat('🌶️', max(1, $foodItem->spicy_level)) }} ({{ $foodItem->spicy_level }}/3)</span>
                        </div>
                        <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <span class="text-[10px] text-slate-400 block uppercase font-bold">Prep Time</span>
                            <span class="font-black text-slate-800">{{ $foodItem->preparation_time }} mins</span>
                        </div>
                    </div>

                    <!-- Ingredients -->
                    <div class="mb-6">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ingredients:</h4>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($foodItem->ingredients_list as $ing)
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium">
                                    {{ $ing }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Add to Cart Form -->
                <form action="{{ route('cart.add') }}" method="POST" class="flex gap-3 pt-4 border-t border-slate-100">
                    @csrf
                    <input type="hidden" name="item_type" value="food">
                    <input type="hidden" name="item_id" value="{{ $foodItem->id }}">
                    <div class="w-24">
                        <input type="number" name="quantity" value="1" min="1" max="{{ max(1, $foodItem->available_quantity) }}"
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-center focus:outline-none focus:border-orange-500">
                    </div>
                    <button type="submit"
                            {{ !$foodItem->isAvailable() ? 'disabled' : '' }}
                            class="flex-1 py-2.5 px-4 bg-orange-500 hover:bg-orange-600 disabled:bg-slate-200 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                        <span>{{ $foodItem->isAvailable() ? 'Add to Cart' : 'Out of Stock' }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Suggested Drinks Pairing -->
    @if($drinkPairings->isNotEmpty())
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
            <h3 class="text-sm font-black text-slate-900 mb-3 flex items-center gap-2">
                <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i> Recommended Drinks to Pair With This Meal:
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach($drinkPairings as $drink)
                    <div class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-100 hover:border-orange-200 transition-colors">
                        <img src="{{ $drink->image }}" class="w-12 h-12 rounded-lg object-cover">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('beverage.show', $drink->id) }}" class="text-xs font-bold text-slate-800 hover:text-orange-600 truncate block">{{ $drink->name }}</a>
                            <span class="text-xs font-black text-orange-600">{{ $drink->price }} EGP</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Reviews Section -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <h3 class="text-base font-black text-slate-900">Customer Ratings & Reviews</h3>

        <!-- Add Review Form -->
        <form action="{{ route('review.store') }}" method="POST" class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
            @csrf
            <input type="hidden" name="item_type" value="food">
            <input type="hidden" name="item_id" value="{{ $foodItem->id }}">
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-slate-700">Your Rating:</span>
                <select name="rating" class="px-2.5 py-1 rounded-lg border border-slate-300 text-xs font-bold">
                    <option value="5">⭐⭐⭐⭐⭐ (5/5 Excellent)</option>
                    <option value="4">⭐⭐⭐⭐ (4/5 Good)</option>
                    <option value="3">⭐⭐⭐ (3/5 Average)</option>
                    <option value="2">⭐⭐ (2/5 Poor)</option>
                    <option value="1">⭐ (1/5 Bad)</option>
                </select>
            </div>
            <textarea name="comment" rows="2" placeholder="Write your thoughts on taste, quality, portion..."
                      class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:border-orange-500"></textarea>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold">Submit Review</button>
            </div>
        </form>

        <!-- Existing Reviews List -->
        <div class="space-y-3">
            @forelse($foodItem->reviews as $review)
                <div class="p-3.5 rounded-xl border border-slate-100 bg-white">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold text-slate-800">{{ $review->user->name ?? 'Guest' }}</span>
                        <span class="text-xs font-bold text-amber-500">{{ str_repeat('★', $review->rating) }}</span>
                    </div>
                    <p class="text-xs text-slate-600">{{ $review->comment }}</p>
                    <span class="text-[10px] text-slate-400 block mt-1">{{ $review->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-xs text-slate-400 italic">No reviews yet. Be the first to review this dish!</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
