@extends('layouts.app')

@section('title', 'Manage Users & Customers')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">User & Customer Management</h1>
            <p class="text-xs text-slate-500 mt-0.5">Inspect customer food preference profiles and ordering activity.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or phone..."
                       class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-orange-500">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
            </div>
            <div class="w-full sm:w-48">
                <select name="role" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-orange-500">
                    <option value="">All Roles</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">User</th>
                        <th class="py-3.5 px-4">Role</th>
                        <th class="py-3.5 px-4">Phone</th>
                        <th class="py-3.5 px-4">Taste Preference</th>
                        <th class="py-3.5 px-4">Spiciness</th>
                        <th class="py-3.5 px-4">Orders Placed</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-5">
                                <span class="font-bold text-slate-900 block">{{ $u->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $u->email }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider {{ $u->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $u->phone ?? 'N/A' }}</td>
                            <td class="py-3.5 px-4 capitalize font-semibold text-slate-700">{{ $u->preference->preferred_taste ?? 'Not set' }}</td>
                            <td class="py-3.5 px-4">
                                @if(isset($u->preference->spicy_level))
                                    {{ str_repeat('🌶️', max(1, $u->preference->spicy_level)) }} (Level {{ $u->preference->spicy_level }})
                                @else
                                    <span class="text-slate-400">N/A</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800">{{ $u->orders->count() }}</td>
                            <td class="py-3.5 px-5 text-right flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $u->id) }}" class="p-1.5 text-slate-400 hover:text-orange-600 transition-colors" title="View Full Profile">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Delete this user account?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition-colors">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
