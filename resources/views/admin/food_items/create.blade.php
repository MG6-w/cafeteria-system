@extends('layouts.app')

@section('title', 'Add Food Item')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-black text-slate-900">Add New Food Item</h1>
        <a href="{{ route('admin.food-items.index') }}" class="text-xs font-bold text-slate-500 hover:text-orange-600">&larr; Back to list</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('admin.food-items.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Food Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Spicy Chicken Pizza" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Category</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Tasting notes, seasoning, and texture..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Price (EGP)</label>
                    <input type="number" step="0.5" name="price" value="{{ old('price') }}" placeholder="140" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Calories (kcal)</label>
                    <input type="number" name="calories" value="{{ old('calories', 650) }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Spicy Level (0-3)</label>
                    <select name="spicy_level" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                        <option value="0">0: None</option>
                        <option value="1">1: Mild</option>
                        <option value="2">2: Medium</option>
                        <option value="3">3: Hot/Extra Spicy</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Stock Quantity</label>
                    <input type="number" name="available_quantity" value="{{ old('available_quantity', 25) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Prep Time (mins)</label>
                    <input type="number" name="preparation_time" value="{{ old('preparation_time', 15) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ingredients (Comma-separated)</label>
                <input type="text" name="ingredients" value="{{ old('ingredients') }}" placeholder="grilled chicken, mozzarella, tomato sauce, jalapeno, oregano"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Image URL</label>
                <input type="url" name="image" value="{{ old('image') }}" placeholder="https://..."
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                    Create Food Item
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
