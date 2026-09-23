<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\FoodItem;
use App\Models\Beverage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load('items');
        return view('customer.orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'payment_status' => 'required|in:cash_on_delivery,paid',
            'notes' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function() use ($cart, $validated) {
            $totalPrice = 0;
            foreach ($cart as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }

            // Generate unique Order Number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_status' => $validated['payment_status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($cart as $item) {
                $subtotal = $item['price'] * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => $item['item_type'],
                    'item_id' => $item['item_id'],
                    'item_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);

                // Decrement stock
                if ($item['item_type'] === 'food') {
                    FoodItem::where('id', $item['item_id'])->decrement('available_quantity', min($item['quantity'], 100));
                } else {
                    Beverage::where('id', $item['item_id'])->decrement('available_quantity', min($item['quantity'], 100));
                }
            }

            // Clear Cart
            session()->forget('cart');

            return redirect()->route('orders.show', $order->id)->with('success', 'Your order was successfully placed! Track your preparation progress below.');
        });
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to cancel this order.');
        }

        if (!$order->canBeCancelled()) {
            return back()->with('error', 'Order cannot be cancelled once preparation has started.');
        }

        $validated = $request->validate([
            'cancelled_reason' => 'nullable|string|max:255',
        ]);

        $order->update([
            'status' => 'cancelled',
            'cancelled_reason' => $validated['cancelled_reason'] ?? 'Cancelled by customer',
        ]);

        // Restore stock
        foreach ($order->items as $item) {
            if ($item->item_type === 'food') {
                FoodItem::where('id', $item->item_id)->increment('available_quantity', $item->quantity);
            } else {
                Beverage::where('id', $item->item_id)->increment('available_quantity', $item->quantity);
            }
        }

        return back()->with('success', 'Order has been successfully cancelled.');
    }
}
