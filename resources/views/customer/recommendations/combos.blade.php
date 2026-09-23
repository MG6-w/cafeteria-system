@extends('layouts.app')

@section('title', 'Smart Meal Combos')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-orange-500 via-amber-500 to-amber-600 rounded-2xl p-6 sm:p-8 text-white shadow-md">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-200">AI Meal Pairing Engine</span>
                <h1 class="text-2xl sm:text-3xl font-black">Smart Combos & Bundles</h1>
                <p class="text-xs sm:text-sm text-orange-100 mt-0.5">Algorithmically paired main dishes and beverages with 12% to 15% discount savings.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($combos as $combo)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
                <!-- Combo Header -->
                <div class="p-5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded bg-orange-100 text-orange-700">
                            {{ count($combo['items']) === 3 ? 'Trio Feast Combo' : 'Power Duo' }}
                        </span>
                        <h3 class="text-sm font-black text-slate-900 mt-1">{{ $combo['title'] }}</h3>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-slate-400 line-through block">{{ $combo['regular_price'] }} EGP</span>
                        <span class="text-base font-black text-orange-600">{{ $combo['combo_price'] }} EGP</span>
                    </div>
                </div>

                <!-- Combo Items Visual Display -->
                <div class="p-5 space-y-4">
                    <div class="space-y-3">
                        @foreach($combo['items'] as $item)
                            <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-50 border border-slate-100">
                                <img src="{{ $item['image'] ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100' }}"
                                     class="w-12 h-12 rounded-lg object-cover">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-bold text-slate-800 truncate">{{ $item['name'] }}</h4>
                                    <span class="text-[10px] text-slate-400 uppercase capitalize">{{ $item['type'] }} &bull; {{ $item['price'] }} EGP</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Synergy & Savings Box -->
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-xs">
                        <div class="flex items-center justify-between font-bold text-emerald-900 mb-1">
                            <span class="flex items-center gap-1"><i data-lucide="zap" class="w-3.5 h-3.5 text-emerald-600"></i> Flavor Synergy Score</span>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-900 font-black text-[11px]">{{ $combo['synergy_score'] }}%</span>
                        </div>
                        <p class="text-emerald-800 text-[11px] italic">{{ $combo['reason'] }}</p>
                        <span class="mt-2 block font-black text-emerald-700 text-xs">💰 You save {{ $combo['savings'] }} EGP with this deal!</span>
                    </div>
                </div>

                <!-- Footer / Add to Cart -->
                <div class="p-5 pt-0">
                    <form action="{{ route('cart.addCombo') }}" method="POST">
                        @csrf
                        <input type="hidden" name="food_id" value="{{ $combo['food_id'] }}">
                        <input type="hidden" name="drink_id" value="{{ $combo['drink_id'] }}">
                        <input type="hidden" name="dessert_id" value="{{ $combo['dessert_id'] ?? '' }}">
                        <input type="hidden" name="combo_price" value="{{ $combo['combo_price'] }}">
                        <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white rounded-xl text-xs font-bold shadow-md transition-all flex items-center justify-center gap-2">
                            <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                            Order Combo Bundle ({{ $combo['combo_price'] }} EGP)
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
