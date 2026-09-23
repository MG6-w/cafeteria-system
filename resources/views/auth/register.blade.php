@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 text-white flex items-center justify-center mx-auto shadow-md mb-3">
                <i data-lucide="user-plus" class="w-6 h-6"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900">Create Account</h2>
            <p class="text-xs text-slate-500 mt-1">Join the smart cafeteria and get AI personalized recommendations.</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>

            <div>
                <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone Number</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="+20 100 000 0000"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-sm rounded-xl shadow-md transition-all">
                Register & Setup Preferences
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            Already have an account? <a href="{{ route('login') }}" class="font-bold text-orange-600 hover:underline">Sign in</a>
        </div>
    </div>
</div>
@endsection
