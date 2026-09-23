@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-black text-slate-900">Edit Category: {{ $category->name }}</h1>
        <a href="{{ route('admin.categories.index') }}" class="text-xs font-bold text-slate-500 hover:text-orange-600">&larr; Back to list</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Category Name</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Type</label>
                <select name="type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
                    <option value="food" {{ $category->type === 'food' ? 'selected' : '' }}>Food</option>
                    <option value="beverage" {{ $category->type === 'beverage' ? 'selected' : '' }}>Beverage</option>
                    <option value="both" {{ $category->type === 'both' ? 'selected' : '' }}>Both</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description (Optional)</label>
                <textarea name="description" rows="3"
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
