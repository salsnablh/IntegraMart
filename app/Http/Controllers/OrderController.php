<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payment\DokuCheckoutService;
use Illuminate\Http\RedirectResponse;
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

    public function show(Order $order, DokuCheckoutService $dokuCheckoutService): View
    {
        abort_unless(
            $order->user_id === auth()->id() || optional($order->customer)->user_id === auth()->id(),
            403
        );

        $order->load(['items.product', 'customer', 'user', 'payments']);

        $latestPayment = $order->payments->sortByDesc('created_at')->first();

        if ($latestPayment && in_array($order->payment_status, ['unpaid', 'pending'], true)) {
            try {
                $payload = $dokuCheckoutService->checkPaymentStatus($latestPayment->external_id ?? ($order->order_number ?? $order->order_code));
                $dokuCheckoutService->applyPaymentStatus($latestPayment, $payload);
                $order->refresh()->load(['items.product', 'customer', 'user', 'payments']);
                $latestPayment = $order->payments->sortByDesc('created_at')->first();
            } catch (\Throwable) {
                // If DOKU status check is temporarily unavailable, keep rendering the local invoice state.
            }
        }

        return view('orders.show', compact('order', 'latestPayment'));
    }

    public function pay(Order $order, DokuCheckoutService $dokuCheckoutService): RedirectResponse
    {
        abort_unless(
            $order->user_id === auth()->id() || optional($order->customer)->user_id === auth()->id(),
            403
        );

        if ($order->payment_status !== 'unpaid' && $order->payment_status !== 'pending') {
            return redirect()->route('orders.show', $order)->with('error', 'Pesanan ini sudah tidak menunggu pembayaran.');
        }

        $order->loadMissing(['items.product', 'customer', 'user', 'payments']);
        $latestPayment = $order->payments->sortByDesc('created_at')->first();

        if ($latestPayment) {
            $storedUrl = data_get($latestPayment->raw_response, 'response.payment.url');
            if ($storedUrl) {
                return redirect()->away($storedUrl);
            }
        }

        try {
            $dokuPayment = $dokuCheckoutService->createCheckoutPayment($order);
        } catch (\Throwable $throwable) {
            return redirect()->route('orders.show', $order)->with('error', 'Gagal membuat ulang tagihan DOKU: '.$throwable->getMessage());
        }

        $paymentUrl = $dokuPayment['payment_url'] ?? null;

        if (! $paymentUrl) {
            return redirect()->route('orders.show', $order)->with('error', 'DOKU tidak mengembalikan payment URL.');
        }

        $order->payments()->create([
            'payment_gateway' => 'doku',
            'payment_method' => 'checkout',
            'payment_channel' => 'doku_checkout',
            'external_id' => $order->order_number ?? $order->order_code,
            'amount' => $order->total_amount ?? $order->grand_total,
            'status' => 'pending',
            'expired_at' => $dokuPayment['expired_at'] ?? null,
            'raw_response' => $dokuPayment,
        ]);

        if ($latestPayment && $latestPayment->status === 'failed') {
            $latestPayment->update([
                'raw_response' => $dokuPayment,
                'expired_at' => $dokuPayment['expired_at'] ?? null,
                'status' => 'pending',
            ]);
        }

        return redirect()->away($paymentUrl);
    }
}

