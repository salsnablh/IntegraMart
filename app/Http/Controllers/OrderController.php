<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('user_id', auth()->id()));
            })
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        abort_unless(
            $order->user_id === auth()->id() || optional($order->customer)->user_id === auth()->id(),
            403
        );

        $order->load(['items.product', 'customer', 'user']);

        return view('orders.show', compact('order'));
    }
}
