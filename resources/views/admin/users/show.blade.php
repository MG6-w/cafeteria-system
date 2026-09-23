@extends('layouts.app')

@section('title', 'Customer Profile: ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-orange-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Users
        </a>
    </div>

    <!-- Customer Overview Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-100">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-purple-500 to-indigo-500 text-white font-black text-xl flex items-center justify-center shadow-md">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-black text-slate-900">{{ $user->name }}</h1>
                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider {{ $user->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $user->role }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">{{ $user->email }} &bull; {{ $user->phone ?? 'No phone' }} &bull; Joined {{ $user->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- AI Taste Preferences Detailed Section (Prompt Section 3) -->
        @php
            $pref = $user->preference;
        @endphp
        @if($pref)
            <div class="pt-6 space-y-4">
                <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i> AI Recommendation Profile & Food Preferences
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Customer Age</span>
                        <span class="font-black text-slate-800 text-sm">{{ $pref->age ? $pref->age . ' yrs' : 'Not set' }}</span>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Preferred Taste</span>
                        <span class="font-black text-slate-800 text-sm capitalize">{{ $pref->preferred_taste ?? 'Not set' }}</span>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Spicy Preference</span>
                        <span class="font-black text-rose-600 text-sm">
                            {{ str_repeat('🌶️', max(1, $pref->spicy_level)) }} (Level {{ $pref->spicy_level }})
                        </span>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Target Meal Budget</span>
                        <span class="font-black text-slate-800 text-sm">{{ $pref->price_preference ? $pref->price_preference . ' EGP' : 'Any' }}</span>
                    </div>
                </div>

                <!-- Favorite Categories -->
                <div class="text-xs pt-2">
                    <span class="font-bold text-slate-600 block mb-1.5">Favorite Categories:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($pref->favorite_categories ?? [] as $fc)
                            <span class="px-2.5 py-1 rounded-lg bg-orange-50 text-orange-700 font-semibold border border-orange-200">
                                {{ $fc }}
                            </span>
                        @empty
                            <span class="text-slate-400 italic">None specified</span>
                        @endforelse
                    </div>
                </div>

                <!-- Dietary Preferences -->
                <div class="text-xs">
                    <span class="font-bold text-slate-600 block mb-1.5">Dietary Restrictions:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($pref->dietary_preferences ?? [] as $dp)
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200 uppercase text-[10px]">
                                {{ $dp }}
                            </span>
                        @empty
                            <span class="text-slate-400 italic">No dietary restrictions</span>
                        @endforelse
                    </div>
                </div>

                <!-- Favorite vs Disliked Ingredients -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="bg-emerald-50/70 p-3.5 rounded-xl border border-emerald-200 text-xs">
                        <span class="font-bold text-emerald-900 block mb-1 flex items-center gap-1.5">
                            <i data-lucide="heart" class="w-3.5 h-3.5 text-emerald-600"></i> Loved Ingredients
                        </span>
                        <div class="flex flex-wrap gap-1 mt-2">
                            @forelse($pref->favorite_ingredients ?? [] as $fi)
                                <span class="px-2 py-0.5 rounded bg-white text-emerald-800 font-semibold text-[11px] border border-emerald-200">{{ $fi }}</span>
                            @empty
                                <span class="text-slate-400 italic text-[11px]">None recorded</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-rose-50/70 p-3.5 rounded-xl border border-rose-200 text-xs">
                        <span class="font-bold text-rose-900 block mb-1 flex items-center gap-1.5">
                            <i data-lucide="ban" class="w-3.5 h-3.5 text-rose-600"></i> Disliked / Avoided Ingredients
                        </span>
                        <div class="flex flex-wrap gap-1 mt-2">
                            @forelse($pref->disliked_ingredients ?? [] as $di)
                                <span class="px-2 py-0.5 rounded bg-white text-rose-800 font-semibold text-[11px] border border-rose-200">{{ $di }}</span>
                            @empty
                                <span class="text-slate-400 italic text-[11px]">None recorded</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Customer Past Orders List -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
            <i data-lucide="receipt" class="w-4 h-4 text-orange-500"></i> Order History ({{ $user->orders->count() }} orders)
        </h3>

        <div class="divide-y divide-slate-100 text-xs">
            @forelse($user->orders as $ord)
                <div class="py-3 flex justify-between items-center">
                    <div>
                        <a href="{{ route('admin.orders.show', $ord->id) }}" class="font-bold text-slate-900 hover:text-orange-600">{{ $ord->order_number }}</a>
                        <span class="text-[11px] text-slate-400 block">{{ $ord->created_at->format('M d, Y h:i A') }} &bull; {{ $ord->items->count() }} items</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-black text-slate-900">{{ $ord->total_price }} EGP</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $ord->status_badge_class }}">
                            {{ $ord->status }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-slate-400 italic py-2">No orders placed by this customer yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
