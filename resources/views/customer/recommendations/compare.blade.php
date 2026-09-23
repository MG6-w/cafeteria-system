@extends('layouts.app')

@section('title', 'AI Food Comparison')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700 rounded-2xl p-6 sm:p-8 text-white shadow-md">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center">
                <i data-lucide="scale" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-200">Head-to-Head Analytics</span>
                <h1 class="text-2xl sm:text-3xl font-black">AI Meal Comparison Tool</h1>
                <p class="text-xs sm:text-sm text-indigo-100 mt-0.5">Compare price, nutrition, spiciness, ingredients, and personal match percentage side-by-side.</p>
            </div>
        </div>
    </div>

    <!-- Selection Bar -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <form action="{{ route('recommendations.compare') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Select First Dish</label>
                <select name="item1" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 focus:outline-none focus:border-indigo-500">
                    @foreach($availableFoods as $food)
                        <option value="{{ $food->id }}" {{ $item1 && $item1->id == $food->id ? 'selected' : '' }}>
                            {{ $food->name }} ({{ $food->price }} EGP)
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Select Second Dish</label>
                <select name="item2" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 focus:outline-none focus:border-indigo-500">
                    @foreach($availableFoods as $food)
                        <option value="{{ $food->id }}" {{ $item2 && $item2->id == $food->id ? 'selected' : '' }}>
                            {{ $food->name }} ({{ $food->price }} EGP)
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if($item1 && $item2)
        <!-- Comparison Table & Verdict -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 bg-slate-50 border-b border-slate-200 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-bold">
                    <i data-lucide="bot" class="w-4 h-4"></i> AI Comparative Verdict
                </div>
                <h3 class="text-lg font-black text-slate-900 mt-2">
                    @if($match1['score'] > $match2['score'])
                        🏆 <span class="text-indigo-600">{{ $item1->name }}</span> is your top recommended choice!
                    @elseif($match2['score'] > $match1['score'])
                        🏆 <span class="text-indigo-600">{{ $item2->name }}</span> is your top recommended choice!
                    @else
                        ⚖️ Both dishes are equally matched for your current profile!
                    @endif
                </h3>
                <p class="text-xs text-slate-500 max-w-xl mx-auto mt-1">
                    @if($match1['score'] >= $match2['score'])
                        {{ $item1->name }} delivers a {{ $match1['score'] }}% match with {{ $item1->calories }} kcal, compared to {{ $match2['score'] }}% for {{ $item2->name }}.
                    @else
                        {{ $item2->name }} delivers a {{ $match2['score'] }}% match with {{ $item2->calories }} kcal, compared to {{ $match1['score'] }}% for {{ $item1->name }}.
                    @endif
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-white">
                            <th class="py-4 px-6 text-left font-bold text-slate-400 uppercase tracking-wider w-1/3">Feature</th>
                            <th class="py-4 px-6 text-center font-black text-slate-900 text-sm w-1/3">
                                <img src="{{ $item1->image }}" class="w-16 h-16 rounded-xl object-cover mx-auto mb-2 border border-slate-200">
                                <span>{{ $item1->name }}</span>
                            </th>
                            <th class="py-4 px-6 text-center font-black text-slate-900 text-sm w-1/3">
                                <img src="{{ $item2->image }}" class="w-16 h-16 rounded-xl object-cover mx-auto mb-2 border border-slate-200">
                                <span>{{ $item2->name }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <!-- Match Score -->
                        <tr class="bg-indigo-50/50">
                            <td class="py-3.5 px-6 font-bold text-indigo-900">Your AI Match %</td>
                            <td class="py-3.5 px-6 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-500 text-white shadow-2xs">
                                    {{ $match1['score'] }}%
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-500 text-white shadow-2xs">
                                    {{ $match2['score'] }}%
                                </span>
                            </td>
                        </tr>

                        <!-- Price -->
                        <tr>
                            <td class="py-3.5 px-6 font-bold text-slate-600">Price</td>
                            <td class="py-3.5 px-6 text-center font-black text-slate-900 text-sm">{{ $item1->price }} EGP</td>
                            <td class="py-3.5 px-6 text-center font-black text-slate-900 text-sm">{{ $item2->price }} EGP</td>
                        </tr>

                        <!-- Calories -->
                        <tr>
                            <td class="py-3.5 px-6 font-bold text-slate-600">Calories</td>
                            <td class="py-3.5 px-6 text-center font-semibold text-slate-800">{{ $item1->calories }} kcal</td>
                            <td class="py-3.5 px-6 text-center font-semibold text-slate-800">{{ $item2->calories }} kcal</td>
                        </tr>

                        <!-- Spicy Level -->
                        <tr>
                            <td class="py-3.5 px-6 font-bold text-slate-600">Spicy Level</td>
                            <td class="py-3.5 px-6 text-center">
                                {{ str_repeat('🌶️', max(1, $item1->spicy_level)) }} (Level {{ $item1->spicy_level }}/3)
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                {{ str_repeat('🌶️', max(1, $item2->spicy_level)) }} (Level {{ $item2->spicy_level }}/3)
                            </td>
                        </tr>

                        <!-- Category -->
                        <tr>
                            <td class="py-3.5 px-6 font-bold text-slate-600">Category</td>
                            <td class="py-3.5 px-6 text-center text-slate-700 font-medium">{{ $item1->category->name }}</td>
                            <td class="py-3.5 px-6 text-center text-slate-700 font-medium">{{ $item2->category->name }}</td>
                        </tr>

                        <!-- Ingredients -->
                        <tr>
                            <td class="py-3.5 px-6 font-bold text-slate-600">Ingredients</td>
                            <td class="py-3.5 px-6 text-center text-slate-500 italic">{{ $item1->ingredients }}</td>
                            <td class="py-3.5 px-6 text-center text-slate-500 italic">{{ $item2->ingredients }}</td>
                        </tr>

                        <!-- Action buttons -->
                        <tr class="bg-slate-50">
                            <td class="py-4 px-6 font-bold text-slate-600">Order</td>
                            <td class="py-4 px-6 text-center">
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="item_type" value="food">
                                    <input type="hidden" name="item_id" value="{{ $item1->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl text-xs transition-colors">
                                        Choose {{ $item1->name }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="item_type" value="food">
                                    <input type="hidden" name="item_id" value="{{ $item2->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl text-xs transition-colors">
                                        Choose {{ $item2->name }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
