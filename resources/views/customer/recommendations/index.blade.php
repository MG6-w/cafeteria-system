@extends('layouts.app')

@section('title', 'AI Personalized Recommendations')

@section('content')
<div class="space-y-8">

    <!-- Hero Header with Surprise Me trigger -->
    <div class="bg-gradient-to-r from-orange-500 via-amber-500 to-amber-600 rounded-2xl p-6 sm:p-8 text-white shadow-md flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider backdrop-blur-md mb-2">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Generative AI & Semantic Matching
            </span>
            <h1 class="text-2xl sm:text-3xl font-black">Personalized For You</h1>
            <p class="text-xs sm:text-sm text-orange-100 mt-1 max-w-xl">
                Our AI analyzes your taste profile, favorite ingredients, spiciness preferences, budget, and past orders to score every available cafeteria item.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button @click="
                fetch('{{ route('recommendations.surprise') }}')
                    .then(res => res.json())
                    .then(data => { surpriseData = data; surpriseModal = true; })
            " class="px-4 py-2.5 bg-white text-orange-600 hover:bg-orange-50 font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-2">
                <i data-lucide="party-popper" class="w-4 h-4 text-amber-500"></i>
                Surprise Me!
            </button>
            <a href="{{ route('customer.preferences') }}" class="px-4 py-2.5 bg-slate-900/30 hover:bg-slate-900/40 text-white font-bold text-xs rounded-xl backdrop-blur-md transition-all flex items-center gap-1.5">
                <i data-lucide="sliders" class="w-4 h-4"></i> Edit Taste Profile
            </a>
        </div>
    </div>

    <!-- Quick Navigation Hub -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('recommendations.combos') }}" class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs hover:border-orange-300 hover:shadow-md transition-all flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <i data-lucide="layers" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-xs font-black text-slate-800 group-hover:text-orange-600">Smart Combos</h3>
                <p class="text-[11px] text-slate-500">Meal + Drink bundles with 12-15% savings</p>
            </div>
        </a>

        <a href="{{ route('recommendations.budget') }}" class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs hover:border-orange-300 hover:shadow-md transition-all flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <i data-lucide="wallet" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-xs font-black text-slate-800 group-hover:text-emerald-600">Budget Finder</h3>
                <p class="text-[11px] text-slate-500">Find delicious meals under 100 or 150 EGP</p>
            </div>
        </a>

        <a href="{{ route('recommendations.compare') }}" class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs hover:border-orange-300 hover:shadow-md transition-all flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                <i data-lucide="scale" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-xs font-black text-slate-800 group-hover:text-indigo-600">AI Food Comparison</h3>
                <p class="text-[11px] text-slate-500">Compare price, calories & match side-by-side</p>
            </div>
        </a>
    </div>

    <!-- AI Match Ranking Table (As described in Section 11 of the specification) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-black text-slate-900 flex items-center gap-2">
                <i data-lucide="award" class="w-4 h-4 text-orange-500"></i> Top AI Match Ranking Table
            </h2>
            <span class="text-xs text-slate-400">Sorted by relevance percentage</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Item</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Price</th>
                        <th class="py-3 px-4">Match %</th>
                        <th class="py-3 px-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recommendations->take(8) as $rec)
                        @php
                            $score = $rec['match_percentage'];
                            $badgeColor = $score >= 85 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' :
                                         ($score >= 60 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-100 text-slate-600 border-slate-200');
                            $barColor = $score >= 85 ? 'bg-emerald-500' : ($score >= 60 ? 'bg-amber-500' : 'bg-slate-400');
                            $showUrl = $rec['type'] === 'food' ? route('food.show', $rec['id']) : route('beverage.show', $rec['id']);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-5 font-black text-slate-900 flex items-center gap-3">
                                <img src="{{ $rec['image'] ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100' }}"
                                     class="w-9 h-9 rounded-lg object-cover border border-slate-100">
                                <div>
                                    <a href="{{ $showUrl }}" class="hover:text-orange-600 transition-colors">{{ $rec['name'] }}</a>
                                    <span class="block text-[10px] font-normal text-slate-400 italic line-clamp-1">{{ $rec['explanation'] }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="capitalize px-2 py-0.5 rounded text-[10px] font-bold {{ $rec['type'] === 'food' ? 'bg-orange-50 text-orange-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ $rec['type'] === 'food' ? 'Food' : 'Drink' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">{{ $rec['category_name'] }}</td>
                            <td class="py-3.5 px-4 font-black text-slate-900">{{ $rec['price'] }} EGP</td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-full border text-xs font-black {{ $badgeColor }}">
                                        {{ $score }}%
                                    </span>
                                    <div class="w-16 bg-slate-100 h-2 rounded-full overflow-hidden hidden sm:block">
                                        <div class="{{ $barColor }} h-full rounded-full" style="width: {{ $score }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <form action="{{ route('cart.add') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="item_type" value="{{ $rec['type'] === 'food' ? 'food' : 'beverage' }}">
                                    <input type="hidden" name="item_id" value="{{ $rec['id'] }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-xs font-bold transition-colors shadow-2xs">
                                        + Order
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Visual Recommendation Cards Grid -->
    <div class="space-y-4">
        <h2 class="text-sm font-black text-slate-900 flex items-center gap-2">
            <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i> AI Detailed Recommendation Breakdown
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($recommendations->take(6) as $item)
                @php
                    $isFood = $item['type'] === 'food';
                    $showRoute = $isFood ? route('food.show', $item['id']) : route('beverage.show', $item['id']);
                    $score = $item['match_percentage'];
                    $badgeColor = $score >= 85 ? 'bg-emerald-500 text-white' : ($score >= 60 ? 'bg-amber-500 text-white' : 'bg-slate-500 text-white');
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col overflow-hidden hover:shadow-md transition-shadow">
                    <div class="relative h-44 bg-slate-100">
                        <img src="{{ $item['image'] ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600' }}"
                             alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                        <div class="absolute top-3 left-3 {{ $badgeColor }} px-2.5 py-1 rounded-full text-xs font-black shadow-md flex items-center gap-1">
                            <i data-lucide="sparkles" class="w-3 h-3"></i>
                            <span>{{ $score }}% Match</span>
                        </div>
                        <span class="absolute bottom-3 left-3 px-2 py-0.5 rounded bg-slate-900/80 text-white text-[10px] font-bold uppercase">
                            {{ $item['category_name'] }}
                        </span>
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-1">
                                <a href="{{ $showRoute }}" class="text-base font-black text-slate-900 hover:text-orange-600 transition-colors">
                                    {{ $item['name'] }}
                                </a>
                                <span class="text-base font-black text-orange-600">{{ $item['price'] }} EGP</span>
                            </div>

                            <!-- Explanation Box -->
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 my-3 text-xs">
                                <span class="font-bold text-amber-900 block mb-1 flex items-center gap-1">
                                    <i data-lucide="message-square" class="w-3.5 h-3.5 text-amber-600"></i> Why AI Chose This:
                                </span>
                                <p class="text-amber-800 italic leading-relaxed text-[11px]">{{ $item['explanation'] }}</p>
                            </div>

                            <!-- Match Factors List -->
                            <div class="space-y-1 my-3">
                                @foreach($item['reasons'] as $r)
                                    <div class="flex items-center gap-1.5 text-[11px] {{ str_contains(strtolower($r), 'disliked') ? 'text-rose-600' : 'text-slate-600' }}">
                                        <i data-lucide="{{ str_contains(strtolower($r), 'disliked') ? 'alert-triangle' : 'check' }}" class="w-3 h-3 shrink-0 {{ str_contains(strtolower($r), 'disliked') ? 'text-rose-500' : 'text-emerald-500' }}"></i>
                                        <span>{{ $r }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex gap-2">
                            <a href="{{ $showRoute }}" class="flex-1 py-2 text-center text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                                Details
                            </a>
                            <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="item_type" value="{{ $isFood ? 'food' : 'beverage' }}">
                                <input type="hidden" name="item_id" value="{{ $item['id'] }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-full py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition-colors shadow-2xs">
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
