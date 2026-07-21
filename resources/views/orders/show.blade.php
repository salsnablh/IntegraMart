@extends('layouts.app')

@section('title', 'Invoice Pesanan - IntegraMart')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Order Detail</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Invoice {{ $order->order_number ?? $order->order_code }}</h1>
            <p class="mt-1 text-sm text-slate-400">{{ $order->created_at->format('d M Y H:i') }}</p>
        </div>
        <a href="{{ route('orders.index') }}" class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50">Kembali</a>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_320px]">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Item Pesanan</h2>
            <div class="mt-4 divide-y divide-slate-100">
                @foreach ($order->items as $item)
                    <div class="flex items-center justify-between py-4">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $item->product->name ?? 'Produk terhapus' }}</p>
                            <p class="mt-1 text-sm text-slate-400">Qty {{ $item->quantity }} x Rp {{ number_format((float) $item->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-semibold text-slate-900">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <aside class="h-fit rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Ringkasan</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between text-slate-600"><dt>Status</dt><dd>{{ ucfirst($order->status) }}</dd></div>
                <div class="flex justify-between text-slate-600"><dt>Pembayaran</dt><dd>{{ ucfirst($order->payment_status) }}</dd></div>
                <div class="flex justify-between text-slate-600"><dt>Pengiriman</dt><dd>{{ ucfirst($order->shipping_status) }}</dd></div>
                <div class="flex justify-between border-t border-slate-200 pt-3 text-base font-semibold text-slate-900"><dt>Total</dt><dd>Rp {{ number_format((float) ($order->total_amount ?? $order->grand_total), 0, ',', '.') }}</dd></div>
            </dl>

            <div class="mt-5 rounded-md bg-slate-50 p-4 text-sm text-slate-600">
                <p class="font-semibold text-slate-900">Alamat Pengiriman</p>
                <p class="mt-2">{{ $order->shipping_address }}</p>
                <p class="mt-2">{{ $order->recipient_name }} - {{ $order->phone ?? $order->recipient_phone }}</p>
            </div>
        </aside>
    </div>
@endsection
