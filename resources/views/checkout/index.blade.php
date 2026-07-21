@extends('layouts.app')

@section('title', 'Checkout - IntegraMart')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Checkout</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Lengkapi Detail Pengiriman</h1>
            <p class="mt-1 text-sm text-slate-400">Pastikan alamat dan nomor telepon sudah benar sebelum membuat pesanan.</p>
        </div>
        <a href="{{ route('cart.index') }}" class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50">Kembali ke Keranjang</a>
    </div>

    <form method="POST" action="{{ route('checkout.process') }}" class="mt-6 grid gap-6 lg:grid-cols-[1fr_360px]">
        @csrf

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Informasi Penerima</h2>

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="recipient_name" class="text-sm font-medium text-slate-700">Nama penerima</label>
                    <input id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $user->name) }}" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                    @error('recipient_name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="text-sm font-medium text-slate-700">Nomor telepon</label>
                    <input id="phone" name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                    @error('phone') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="shipping_address" class="text-sm font-medium text-slate-700">Alamat lengkap</label>
                    <textarea id="shipping_address" name="shipping_address" rows="4" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">{{ old('shipping_address') }}</textarea>
                    @error('shipping_address') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="shipping_city" class="text-sm font-medium text-slate-700">Kota</label>
                    <input id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                </div>

                <div>
                    <label for="shipping_province" class="text-sm font-medium text-slate-700">Provinsi</label>
                    <input id="shipping_province" name="shipping_province" value="{{ old('shipping_province') }}" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                </div>

                <div>
                    <label for="shipping_postal_code" class="text-sm font-medium text-slate-700">Kode pos</label>
                    <input id="shipping_postal_code" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                </div>

                <div>
                    <label for="payment_method" class="text-sm font-medium text-slate-700">Metode pembayaran</label>
                    <select id="payment_method" name="payment_method" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                        <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Bank Transfer</option>
                        <option value="cod" @selected(old('payment_method') === 'cod')>COD</option>
                        <option value="ewallet" @selected(old('payment_method') === 'ewallet')>E-Wallet</option>
                    </select>
                    @error('payment_method') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="notes" class="text-sm font-medium text-slate-700">Catatan</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">{{ old('notes') }}</textarea>
                </div>
            </div>
        </section>

        <aside class="h-fit rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Ringkasan Pesanan</h2>
            <div class="mt-4 divide-y divide-slate-100">
                @foreach ($cartItems as $item)
                    @php($product = $item['product'])
                    <div class="flex gap-3 py-3">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-14 w-14 rounded-md object-cover">
                        @else
                            <div class="flex h-14 w-14 items-center justify-center rounded-md bg-slate-100 text-xs text-slate-400">No Img</div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $item['qty'] }} x Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="text-sm font-semibold text-slate-900">Rp {{ number_format((float) $item['line_total'], 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 border-t border-slate-200 pt-4">
                <div class="flex justify-between text-sm text-slate-600">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format((float) $subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="mt-3 flex justify-between text-base font-semibold text-slate-900">
                    <span>Total</span>
                    <span>Rp {{ number_format((float) $total, 0, ',', '.') }}</span>
                </div>
            </div>
            <button type="submit" class="mt-5 w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Buat Pesanan</button>
        </aside>
    </form>
@endsection
