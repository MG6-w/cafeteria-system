@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 text-white flex items-center justify-center mx-auto shadow-md mb-3">
                <i data-lucide="utensils" class="w-6 h-6"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900">Welcome Back</h2>
            <p class="text-xs text-slate-500 mt-1">Sign in to your Cafeteria account to browse and receive personalized AI recommendations.</p>
        </div>

        <!-- Quick Demo Switcher -->
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3.5 mb-6 text-xs">
            <span class="font-bold text-amber-900 block mb-2 flex items-center gap-1.5">
                <i data-lucide="key" class="w-3.5 h-3.5 text-amber-600"></i> Quick Demo Logins:
            </span>
            <div class="grid grid-cols-2 gap-2">
                <button type="button"
                        onclick="document.getElementById('email').value='admin@cafeteria.com'; document.getElementById('password').value='password';"
                        class="px-2.5 py-1.5 bg-white hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-lg text-left transition-colors">
                    <span class="font-bold block">👨‍💼 Admin</span>
                    <span class="text-[10px] text-slate-500">Full System CRUD & Stats</span>
                </button>
                <button type="button"
                        onclick="document.getElementById('email').value='customer@cafeteria.com'; document.getElementById('password').value='password';"
                        class="px-2.5 py-1.5 bg-white hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-lg text-left transition-colors">
                    <span class="font-bold block">👤 Customer</span>
                    <span class="text-[10px] text-slate-500">Personalized AI & Orders</span>
                </button>
            </div>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email', 'customer@cafeteria.com') }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                <input type="password" name="password" id="password" value="password" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                    <span class="text-slate-600">Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-sm rounded-xl shadow-md transition-all">
                Sign In
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            Don't have an account? <a href="{{ route('register') }}" class="font-bold text-orange-600 hover:underline">Register now</a>
        </div>
    </div>
</div>
@endsection
