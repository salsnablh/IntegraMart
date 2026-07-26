<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\Payment\DokuCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $cartItems = $this->cartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong. Tambahkan produk terlebih dahulu.');
        }

        $subtotal = $cartItems->sum(fn (array $item) => $item['line_total']);
        $savedShipping = $this->savedShippingData();
        $prefill = array_merge($savedShipping ?? [], old());

        return view('checkout.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'user' => auth()->user(),
            'savedShipping' => $savedShipping,
            'prefill' => $prefill,
            'editShipping' => request()->boolean('edit_shipping'),
        ]);
    }

    public function process(Request $request, DokuCheckoutService $dokuCheckoutService): RedirectResponse
    {
        $cart = session('cart', []);

        ksort($cart, SORT_NUMERIC);

        if ($cart === []) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $savedShipping = $this->savedShippingData();
        $useSavedShipping = $savedShipping && $request->boolean('use_saved_shipping');

        $rules = [
            'payment_method' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if (! $useSavedShipping) {
            $rules = array_merge($rules, [
                'recipient_name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:30'],
                'shipping_address' => ['required', 'string', 'max:1000'],
                'shipping_city' => ['nullable', 'string', 'max:255'],
                'shipping_province' => ['nullable', 'string', 'max:255'],
                'shipping_postal_code' => ['nullable', 'string', 'max:20'],
            ]);
        }

        $data = $request->validate($rules);

        if ($useSavedShipping) {
            $data = array_merge($savedShipping, $data);
        }

        try {
            $order = DB::transaction(function () use ($cart, $data) {
                $productIds = array_map('intval', array_keys($cart));

                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($products->count() !== count($cart)) {
                    throw new \RuntimeException('Sebagian produk di keranjang tidak ditemukan.');
                }

                $subtotal = 0;

                foreach ($cart as $productId => $qty) {
                    $product = $products->get((int) $productId);
                    $qty = (int) $qty;

                    if (! $product->is_active || $qty < 1 || $qty > $product->stock) {
                        throw new \RuntimeException('Stok produk '.$product->name.' tidak mencukupi.');
                    }

                    $subtotal += $qty * (float) $product->price;
                }

                $user = auth()->user();
                $customer = Customer::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $data['recipient_name'],
                        'email' => $user->email,
                        'phone' => $data['phone'],
                    ]
                );

                $customer->update([
                    'name' => $data['recipient_name'],
                    'phone' => $data['phone'],
                ]);

                $orderNumber = 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));

                $order = Order::create([
                    'user_id' => $user->id,
                    'customer_id' => $customer->id,
                    'order_code' => $orderNumber,
                    'order_number' => $orderNumber,
                    'subtotal' => $subtotal,
                    'shipping_cost' => 0,
                    'admin_fee' => 0,
                    'discount_amount' => 0,
                    'grand_total' => $subtotal,
                    'total_amount' => $subtotal,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'shipping_status' => 'not_created',
                    'recipient_name' => $data['recipient_name'],
                    'recipient_phone' => $data['phone'],
                    'shipping_address' => $data['shipping_address'],
                    'phone' => $data['phone'],
                    'payment_method' => $data['payment_method'],
                    'shipping_city' => $data['shipping_city'] ?? null,
                    'shipping_province' => $data['shipping_province'] ?? null,
                    'shipping_postal_code' => $data['shipping_postal_code'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);

                foreach ($cart as $productId => $qty) {
                    $product = $products->get((int) $productId);
                    $qty = (int) $qty;
                    $lineTotal = $qty * (float) $product->price;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'qty' => $qty,
                        'price' => $product->price,
                        'subtotal' => $lineTotal,
                    ]);

                    Product::query()
                        ->whereKey($product->id)
                        ->update([
                            'stock' => DB::raw('stock - '.(int) $qty),
                        ]);
                }

                return $order;
            });
        } catch (\Throwable $throwable) {
            return back()->withInput()->with('error', $throwable->getMessage());
        }

        $order->load(['items.product', 'customer', 'user']);

        $payment = $order->payments()->create([
            'payment_gateway' => 'doku',
            'payment_method' => 'checkout',
            'payment_channel' => 'doku_checkout',
            'external_id' => $order->order_number ?? $order->order_code,
            'amount' => $order->total_amount ?? $order->grand_total,
            'status' => 'pending',
        ]);

        session()->forget('cart');

        try {
            $dokuPayment = $dokuCheckoutService->createCheckoutPayment($order);
        } catch (\Throwable $throwable) {
            $payment->update([
                'status' => 'failed',
                'raw_response' => [
                    'error' => $throwable->getMessage(),
                ],
            ]);

            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Pesanan berhasil dibuat, tetapi pembayaran DOKU gagal dibuat: '.$throwable->getMessage());
        }

        $paymentUrl = $dokuPayment['payment_url'] ?? null;

        if (! $paymentUrl) {
            $payment->update([
                'status' => 'failed',
                'raw_response' => $dokuPayment,
            ]);

            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Pesanan berhasil dibuat, tetapi DOKU tidak mengembalikan payment URL.');
        }

        $payment->update([
            'status' => 'pending',
            'expired_at' => $dokuPayment['expired_at'] ?? null,
            'raw_response' => $dokuPayment,
        ]);

        return redirect()->away($paymentUrl);
    }

    private function cartItems(): Collection
    {
        $cart = session('cart', []);

        ksort($cart, SORT_NUMERIC);

        if ($cart === []) {
            return collect();
        }

        return Product::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->map(function (Product $product) use ($cart) {
                $qty = min((int) $cart[$product->id], $product->stock);

                return [
                    'product' => $product,
                    'qty' => $qty,
                    'line_total' => $qty * (float) $product->price,
                ];
            });
    }

    private function savedShippingData(): ?array
    {
        $user = auth()->user();

        $latestOrder = Order::query()
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (! $latestOrder) {
            return null;
        }

        return [
            'recipient_name' => $latestOrder->recipient_name,
            'phone' => $latestOrder->phone ?? $latestOrder->recipient_phone,
            'shipping_address' => $latestOrder->shipping_address,
            'shipping_city' => $latestOrder->shipping_city,
            'shipping_province' => $latestOrder->shipping_province,
            'shipping_postal_code' => $latestOrder->shipping_postal_code,
        ];
    }
}
