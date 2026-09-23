<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CustomerPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('menu.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back, Admin!');
            }
            return redirect()->intended(route('menu.index'))->with('success', 'Welcome back to AI Cafeteria!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('menu.index');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'customer',
            'password' => Hash::make($validated['password']),
        ]);

        // Create default preference record
        CustomerPreference::create([
            'user_id' => $user->id,
            'favorite_categories' => ['Burgers', 'Pizza', 'Coffee'],
            'favorite_food_types' => ['Chicken', 'Burger'],
            'favorite_beverages' => ['Iced Latte', 'Fresh Orange Juice'],
            'preferred_taste' => 'savory',
            'dietary_preferences' => [],
            'price_preference' => 150,
            'spicy_level' => 1,
            'favorite_ingredients' => ['cheese', 'chicken'],
            'disliked_ingredients' => [],
        ]);

        Auth::login($user);

        return redirect()->route('customer.preferences')->with('success', 'Registration successful! Customize your food preferences to get personalized AI recommendations.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
