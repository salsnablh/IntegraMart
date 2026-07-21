<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with(['customer', 'user'])
            ->withCount('items')
            ->latest()
            ->paginate(12);

        return view('admin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,processing,completed,cancelled'],
        ]);

        $order->update([
            'status' => $data['status'],
            'shipping_status' => match ($data['status']) {
                'processing' => 'pending',
                'completed' => 'delivered',
                'cancelled' => 'cancelled',
                default => $order->shipping_status,
            },
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
