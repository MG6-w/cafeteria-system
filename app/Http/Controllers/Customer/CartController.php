<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use App\Models\Beverage;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('customer.cart.index', compact('cart', 'total'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:food,beverage',
            'item_id' => 'required|integer',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $quantity = (int) ($validated['quantity'] ?? 1);
        $type = $validated['item_type'];
        $id = $validated['item_id'];

        if ($type === 'food') {
            $item = FoodItem::findOrFail($id);
        } else {
            $item = Beverage::findOrFail($id);
        }

        if (!$item->isAvailable()) {
            return back()->with('error', 'Sorry, this item is currently out of stock.');
        }

        $cartKey = "{$type}_{$id}";
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'key' => $cartKey,
                'item_id' => $item->id,
                'item_type' => $type,
                'name' => $item->name,
                'price' => (float) $item->price,
                'image' => $item->image,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        if ($request->ajax() || $request->wantsJson()) {
            $totalCount = array_sum(array_column($cart, 'quantity'));
            return response()->json([
                'status' => 'success',
                'message' => "{$item->name} added to cart!",
                'cart_count' => $totalCount,
            ]);
        }

        return back()->with('success', "{$item->name} added to cart!");
    }

    public function addCombo(Request $request)
    {
        $request->validate([
            'food_id' => 'required|integer|exists:food_items,id',
            'drink_id' => 'required|integer|exists:beverages,id',
            'dessert_id' => 'nullable|integer|exists:food_items,id',
            'combo_price' => 'required|numeric',
        ]);

        $food = FoodItem::find($request->food_id);
        $drink = Beverage::find($request->drink_id);
        $dessert = $request->dessert_id ? FoodItem::find($request->dessert_id) : null;

        $cart = session()->get('cart', []);

        // Add combo as a bundled package
        $comboKey = 'combo_' . uniqid();
        $title = $dessert
            ? "Trio Feast Combo: {$food->name} + {$drink->name} + {$dessert->name}"
            : "Power Duo Combo: {$food->name} + {$drink->name}";

        $cart[$comboKey] = [
            'key' => $comboKey,
            'item_id' => $food->id,
            'item_type' => 'food',
            'name' => $title,
            'price' => (float) $request->combo_price,
            'image' => $food->image,
            'quantity' => 1,
            'is_combo' => true,
        ];

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Smart Combo deal added to your cart with discount!');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'quantity' => 'required|integer|min:1|max:50',
        ]);

        $cart = session()->get('cart', []);
        if (isset($cart[$validated['key']])) {
            $cart[$validated['key']]['quantity'] = $validated['quantity'];
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Cart updated successfully.');
    }

    public function remove($key)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Cart cleared.');
    }
}
