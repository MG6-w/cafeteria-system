@extends('layouts.app')

@section('title', 'Add Beverage')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-black text-slate-900">Add New Beverage</h1>
        <a href="{{ route('admin.beverages.index') }}" class="text-xs font-bold text-slate-500 hover:text-indigo-600">&larr; Back to list</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('admin.beverages.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Beverage Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Iced Spanish Latte" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Category</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Flavor profile, beans, or fresh blends..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Price (EGP)</label>
                    <input type="number" step="0.5" name="price" value="{{ old('price') }}" placeholder="80" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Size</label>
                    <select name="size" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="small">Small</option>
                        <option value="medium" selected>Medium</option>
                        <option value="large">Large</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Temperature</label>
                    <select name="temperature" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="cold" selected>❄️ Cold</option>
                        <option value="hot">☕ Hot</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Calories</label>
                    <input type="number" name="calories" value="{{ old('calories', 120) }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Stock Quantity</label>
                    <input type="number" name="available_quantity" value="{{ old('available_quantity', 30) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ingredients</label>
                <input type="text" name="ingredients" value="{{ old('ingredients') }}" placeholder="espresso, fresh milk, condensed milk, ice"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Image URL</label>
                <input type="url" name="image" value="{{ old('image') }}" placeholder="https://..."
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                    Create Beverage
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
