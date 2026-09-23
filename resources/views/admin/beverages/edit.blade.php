@extends('layouts.app')

@section('title', 'Edit Beverage')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-black text-slate-900">Edit Beverage: {{ $beverage->name }}</h1>
        <a href="{{ route('admin.beverages.index') }}" class="text-xs font-bold text-slate-500 hover:text-indigo-600">&larr; Back to list</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('admin.beverages.update', $beverage->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Beverage Name</label>
                    <input type="text" name="name" value="{{ old('name', $beverage->name) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Category</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $beverage->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description</label>
                <textarea name="description" rows="2"
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">{{ old('description', $beverage->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Price (EGP)</label>
                    <input type="number" step="0.5" name="price" value="{{ old('price', $beverage->price) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Size</label>
                    <select name="size" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="small" {{ $beverage->size === 'small' ? 'selected' : '' }}>Small</option>
                        <option value="medium" {{ $beverage->size === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="large" {{ $beverage->size === 'large' ? 'selected' : '' }}>Large</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Temperature</label>
                    <select name="temperature" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="cold" {{ $beverage->temperature === 'cold' ? 'selected' : '' }}>❄️ Cold</option>
                        <option value="hot" {{ $beverage->temperature === 'hot' ? 'selected' : '' }}>☕ Hot</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Calories</label>
                    <input type="number" name="calories" value="{{ old('calories', $beverage->calories) }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Stock Quantity</label>
                    <input type="number" name="available_quantity" value="{{ old('available_quantity', $beverage->available_quantity) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="available" {{ $beverage->status === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ $beverage->status === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ingredients</label>
                <input type="text" name="ingredients" value="{{ old('ingredients', $beverage->ingredients) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Image URL</label>
                <input type="url" name="image" value="{{ old('image', $beverage->image) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                    Update Beverage
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
