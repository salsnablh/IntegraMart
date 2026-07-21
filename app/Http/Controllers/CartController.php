<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cartItems = $this->cartItems();
        $subtotal = $cartItems->sum(fn (array $item) => $item['line_total']);

        return view('cart.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);
    }

    public function add(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        abort_unless($product->is_active, 404);

        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:1', 'max:'.$product->stock],
        ]);

        $cart = session('cart', []);
        $currentQty = $cart[$product->id] ?? 0;
        $newQty = $currentQty + $data['qty'];

        if ($newQty > $product->stock) {
            return back()->withErrors([
                'qty' => 'Jumlah produk di keranjang melebihi stok yang tersedia.',
            ])->withInput();
        }

        $cart[$product->id] = $newQty;
        session(['cart' => $cart]);

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:1', 'max:'.$product->stock],
        ]);

        $cart = session('cart', []);
        $cart[$product->id] = $data['qty'];
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', 'Kuantitas keranjang diperbarui.');
    }

    public function remove(int $id): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$id]);
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', 'Produk dihapus dari keranjang.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return redirect()->route('cart.index')->with('success', 'Keranjang berhasil dikosongkan.');
    }

    private function cartItems(): Collection
    {
        $cart = session('cart', []);

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
}
