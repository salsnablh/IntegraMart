@extends('layouts.app')

@section('title', 'Keranjang Belanja - IntegraMart')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Shopping Cart</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Keranjang Belanja</h1>
            <p class="mt-1 text-sm text-slate-400">Atur kuantitas produk sebelum masuk ke checkout.</p>
        </div>
        <a href="{{ route('shop.index') }}" class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-900">Lanjut Belanja</a>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_340px]">
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Item Keranjang</h2>
                @if ($cartItems->isNotEmpty())
                    <form method="POST" action="{{ route('cart.clear') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-semibold text-rose-700 hover:text-rose-800">Kosongkan</button>
                    </form>
                @endif
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($cartItems as $item)
                    @php($product = $item['product'])
                    <div class="grid gap-4 p-5 lg:grid-cols-[80px_1fr_140px_140px_110px] lg:items-center">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-md object-cover">
                        @else
                            <div class="flex h-20 w-20 items-center justify-center rounded-md bg-slate-100 text-xs font-semibold text-slate-400">No Img</div>
                        @endif

                        <div>
                            <h2 class="font-semibold text-slate-900">{{ $product->name }}</h2>
                            <p class="mt-1 text-sm text-slate-400">{{ $product->sku }} · Stok {{ $product->stock }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase text-slate-400">Harga</p>
                            <p class="mt-1 font-semibold text-slate-900">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase text-slate-400">Qty</p>
                            <form method="POST" action="{{ route('cart.update', $product->id) }}" class="mt-1 flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input name="qty" type="number" min="1" max="{{ $product->stock }}" value="{{ $item['qty'] }}" class="w-20 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">OK</button>
                            </form>

                        </div>

                        <div class="lg:text-right">
                            <p class="text-xs font-medium uppercase text-slate-400">Subtotal</p>
                            <p class="mt-1 font-semibold text-slate-900">Rp {{ number_format((float) $item['line_total'], 0, ',', '.') }}</p>
                            <form method="POST" action="{{ route('cart.remove', $product->id) }}" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-rose-700 hover:text-rose-800">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <p class="text-sm text-slate-400">Keranjang masih kosong.</p>
                        <a href="{{ route('shop.index') }}" class="mt-4 inline-flex rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Mulai Belanja</a>
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="h-fit rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Order Summary</h2>
            <div class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format((float) $subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-3 text-base font-semibold text-slate-900">
                    <span>Total Harga</span>
                    <span>Rp {{ number_format((float) $total, 0, ',', '.') }}</span>
                </div>
            </div>
            <a href="#" class="mt-5 flex w-full items-center justify-center rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-600 {{ $cartItems->isEmpty() ? 'pointer-events-none opacity-50' : '' }}">Lanjut ke Checkout</a>
        </aside>
    </div>
@endsection
