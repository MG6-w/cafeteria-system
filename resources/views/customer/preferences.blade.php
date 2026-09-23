@extends('layouts.app')

@section('title', 'AI Food Preferences')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header banner -->
        <div class="bg-gradient-to-r from-orange-500 via-amber-500 to-amber-600 p-6 text-white">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center">
                    <i data-lucide="sparkles" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black">Personalized Taste Profile</h1>
                    <p class="text-xs text-orange-100 mt-0.5">Customize your preferences to power the AI Matching & Recommendation Engine.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('customer.preferences.update') }}" method="POST" class="p-6 sm:p-8 space-y-8">
            @csrf

            <!-- Demographics & Budget -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Your Age</label>
                    <input type="number" name="age" value="{{ old('age', $preference->age) }}" placeholder="e.g. 24"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                    <p class="text-[11px] text-slate-400 mt-1">Used to tailor portion & energy recommendations.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Preferred Meal Budget (EGP)</label>
                    <div class="relative">
                        <input type="number" name="price_preference" value="{{ old('price_preference', $preference->price_preference ?? 150) }}" placeholder="e.g. 150"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                        <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-400">EGP</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">AI will prioritize dishes that fit comfortably within this budget.</p>
                </div>
            </div>

            <!-- Spicy Level Slider / Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Preferred Spicy Level</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                        $curSpicy = (int) old('spicy_level', $preference->spicy_level ?? 1);
                    @endphp
                    <label class="cursor-pointer">
                        <input type="radio" name="spicy_level" value="0" {{ $curSpicy === 0 ? 'checked' : '' }} class="sr-only peer">
                        <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-900 text-center transition-all">
                            <span class="text-lg block mb-1">🌿</span>
                            <span class="text-xs font-bold block">No Spice</span>
                            <span class="text-[10px] text-slate-500">Mild & Gentle</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="spicy_level" value="1" {{ $curSpicy === 1 ? 'checked' : '' }} class="sr-only peer">
                        <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-900 text-center transition-all">
                            <span class="text-lg block mb-1">🌶️</span>
                            <span class="text-xs font-bold block">Mild</span>
                            <span class="text-[10px] text-slate-500">Just a hint</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="spicy_level" value="2" {{ $curSpicy === 2 ? 'checked' : '' }} class="sr-only peer">
                        <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-900 text-center transition-all">
                            <span class="text-lg block mb-1">🌶️🌶️</span>
                            <span class="text-xs font-bold block">Medium Hot</span>
                            <span class="text-[10px] text-slate-500">Noticeable kick</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="spicy_level" value="3" {{ $curSpicy === 3 ? 'checked' : '' }} class="sr-only peer">
                        <div class="p-3 rounded-xl border border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-900 text-center transition-all">
                            <span class="text-lg block mb-1">🔥🌶️🔥</span>
                            <span class="text-xs font-bold block">Extra Hot</span>
                            <span class="text-[10px] text-slate-500">Fiery heat lover</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Preferred Taste -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Preferred Overall Taste</label>
                <div class="flex flex-wrap gap-2">
                    @php
                        $tastes = ['savory' => 'Savory', 'spicy' => 'Spicy', 'sweet' => 'Sweet', 'sour' => 'Sour/Tangy', 'balanced' => 'Balanced'];
                        $curTaste = old('preferred_taste', $preference->preferred_taste ?? 'savory');
                    @endphp
                    @foreach($tastes as $val => $lbl)
                        <label class="cursor-pointer">
                            <input type="radio" name="preferred_taste" value="{{ $val }}" {{ $curTaste === $val ? 'checked' : '' }} class="sr-only peer">
                            <span class="px-4 py-2 rounded-xl text-xs font-bold border border-slate-200 inline-block peer-checked:border-orange-500 peer-checked:bg-orange-500 peer-checked:text-white transition-all">
                                {{ $lbl }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Favorite Categories -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Favorite Menu Categories</label>
                <p class="text-[11px] text-slate-500 mb-3">Select the categories you crave most often:</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2.5">
                    @php
                        $favCats = old('favorite_categories', $preference->favorite_categories ?? []);
                    @endphp
                    @foreach($categories as $cat)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="favorite_categories[]" value="{{ $cat->name }}" {{ in_array($cat->name, $favCats) ? 'checked' : '' }} class="sr-only peer">
                            <div class="p-2.5 rounded-xl border border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-900 text-center transition-all">
                                <span class="text-xs font-bold block">{{ $cat->name }}</span>
                                <span class="text-[10px] text-slate-400 capitalize">{{ $cat->type }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Dietary Preferences -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Dietary Restrictions</label>
                <div class="flex flex-wrap gap-2">
                    @php
                        $diets = ['vegetarian' => 'Vegetarian', 'vegan' => 'Vegan', 'keto' => 'Keto', 'gluten_free' => 'Gluten-Free', 'low_carb' => 'Low Carb', 'dairy_free' => 'Dairy-Free'];
                        $curDiets = old('dietary_preferences', $preference->dietary_preferences ?? []);
                    @endphp
                    @foreach($diets as $key => $label)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="dietary_preferences[]" value="{{ $key }}" {{ in_array($key, $curDiets) ? 'checked' : '' }} class="sr-only peer">
                            <span class="px-3 py-1.5 rounded-xl text-xs font-semibold border border-slate-200 inline-flex items-center gap-1 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-800 transition-all">
                                <i data-lucide="check" class="w-3 h-3 hidden peer-checked:inline"></i>
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Ingredients: Favorite vs Disliked -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1 flex items-center gap-1.5 text-emerald-700">
                        <i data-lucide="heart" class="w-3.5 h-3.5 text-emerald-600"></i> Favorite Ingredients
                    </label>
                    <p class="text-[11px] text-slate-400 mb-2">Comma separated list of ingredients you love (e.g. chicken, cheese, garlic, jalapeno)</p>
                    <input type="text" name="favorite_ingredients"
                           value="{{ old('favorite_ingredients', implode(', ', $preference->favorite_ingredients ?? [])) }}"
                           placeholder="chicken, cheese, jalapeno, garlic"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1 flex items-center gap-1.5 text-rose-700">
                        <i data-lucide="ban" class="w-3.5 h-3.5 text-rose-600"></i> Disliked / Allergen Ingredients
                    </label>
                    <p class="text-[11px] text-slate-400 mb-2">AI will strictly avoid or heavily penalize items with these ingredients (e.g. mushrooms, olives, peanuts)</p>
                    <input type="text" name="disliked_ingredients"
                           value="{{ old('disliked_ingredients', implode(', ', $preference->disliked_ingredients ?? [])) }}"
                           placeholder="mushrooms, olives, peanuts"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-rose-500">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-sm rounded-xl shadow-lg transition-all flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    Save Preferences & Recalculate AI Matches
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
