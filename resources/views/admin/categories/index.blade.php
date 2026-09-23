@extends('layouts.app')

@section('title', 'Manage Categories')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Categories Management</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage food and beverage catalog classifications.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Add New Category
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Name</th>
                        <th class="py-3.5 px-4">Slug</th>
                        <th class="py-3.5 px-4">Type</th>
                        <th class="py-3.5 px-4">Food Items</th>
                        <th class="py-3.5 px-4">Beverages</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($categories as $category)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-5 font-bold text-slate-900">{{ $category->name }}</td>
                            <td class="py-3.5 px-4 text-slate-400">{{ $category->slug }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $category->type === 'food' ? 'bg-orange-50 text-orange-700' : ($category->type === 'beverage' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700') }}">
                                    {{ $category->type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-700">{{ $category->food_items_count }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-700">{{ $category->beverages_count }}</td>
                            <td class="py-3.5 px-5 text-right flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="p-1.5 text-slate-400 hover:text-orange-600 transition-colors">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
