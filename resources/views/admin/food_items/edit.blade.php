@extends('layouts.app')

@section('title', 'Edit Food Item')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-black text-slate-900">Edit Food: {{ $foodItem->name }}</h1>
        <a href="{{ route('admin.food-items.index') }}" class="text-xs font-bold text-slate-500 hover:text-orange-600">&larr; Back to list</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('admin.food-items.update', $foodItem->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Food Name</label>
                    <input type="text" name="name" value="{{ old('name', $foodItem->name) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Category</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $foodItem->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description</label>
                <textarea name="description" rows="2"
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">{{ old('description', $foodItem->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Price (EGP)</label>
                    <input type="number" step="0.5" name="price" value="{{ old('price', $foodItem->price) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Calories (kcal)</label>
                    <input type="number" name="calories" value="{{ old('calories', $foodItem->calories) }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Spicy Level (0-3)</label>
                    <select name="spicy_level" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                        <option value="0" {{ $foodItem->spicy_level === 0 ? 'selected' : '' }}>0: None</option>
                        <option value="1" {{ $foodItem->spicy_level === 1 ? 'selected' : '' }}>1: Mild</option>
                        <option value="2" {{ $foodItem->spicy_level === 2 ? 'selected' : '' }}>2: Medium</option>
                        <option value="3" {{ $foodItem->spicy_level === 3 ? 'selected' : '' }}>3: Hot/Extra Spicy</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Stock Quantity</label>
                    <input type="number" name="available_quantity" value="{{ old('available_quantity', $foodItem->available_quantity) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Prep Time (mins)</label>
                    <input type="number" name="preparation_time" value="{{ old('preparation_time', $foodItem->preparation_time) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                        <option value="available" {{ $foodItem->status === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ $foodItem->status === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ingredients</label>
                <input type="text" name="ingredients" value="{{ old('ingredients', $foodItem->ingredients) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Image URL</label>
                <input type="url" name="image" value="{{ old('image', $foodItem->image) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                    Update Food Item
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
