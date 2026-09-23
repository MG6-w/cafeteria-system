@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <div class="flex items-center justify-between pb-6 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 text-white flex items-center justify-center font-black text-xl shadow-md">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-900">{{ $user->name }}</h1>
                    <p class="text-xs text-slate-500">{{ $user->email }} &bull; Role: <span class="capitalize font-semibold text-orange-600">{{ $user->role }}</span></p>
                </div>
            </div>
            <a href="{{ route('customer.preferences') }}" class="px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5">
                <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> AI Preferences
            </a>
        </div>

        <form action="{{ route('customer.profile.update') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address</label>
                <input type="email" value="{{ $user->email }}" disabled
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
                <p class="text-[11px] text-slate-400 mt-1">Email cannot be changed.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+20 100 000 0000"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500">
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
