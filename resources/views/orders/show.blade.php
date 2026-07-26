@extends('layouts.app')

@section('title', 'Invoice Pesanan - IntegraMart')

@section('content')
    @php
        $paymentUrl = data_get($latestPayment?->raw_response, 'response.payment.url');
        $dokuInvoiceNumber = $latestPayment?->external_id ?? ($order->order_number ?? $order->order_code);
        $canPayNow = in_array($order->payment_status, ['unpaid', 'pending'], true);
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Order Detail</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Invoice {{ $order->order_number ?? $order->order_code }}</h1>
            <p class="mt-1 text-sm text-slate-400">{{ $order->created_at->format('d M Y H:i') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($canPayNow)
                <a href="{{ route('orders.pay', $order) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Bayar Sekarang</a>
            @endif
            <a href="{{ route('orders.index') }}" class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50">Kembali</a>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_340px]">
        <div class="space-y-6">
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

            @if ($latestPayment)
                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Tagihan DOKU</h2>
                            <p class="mt-1 text-sm text-slate-400">{{ $dokuInvoiceNumber }}</p>
                        </div>
                        <span class="inline-flex w-fit rounded-full px-2 py-1 text-xs font-semibold ring-1 {{ in_array($latestPayment->status, ['paid', 'success'], true) ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20' }}">
                            {{ ucfirst($latestPayment->status) }}
                        </span>
                    </div>

                    <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-slate-400">Gateway</dt>
                            <dd class="mt-1 font-medium text-slate-700">{{ strtoupper($latestPayment->payment_gateway ?? 'doku') }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Channel</dt>
                            <dd class="mt-1 font-medium text-slate-700">{{ str($latestPayment->payment_channel ?? $latestPayment->payment_method ?? '-')->replace('_', ' ')->title() }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Nominal</dt>
                            <dd class="mt-1 font-medium text-slate-700">Rp {{ number_format((float) $latestPayment->amount, 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Jatuh Tempo</dt>
                            <dd class="mt-1 font-medium text-slate-700">{{ $latestPayment->expired_at?->format('d M Y H:i') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Dibayar Pada</dt>
                            <dd class="mt-1 font-medium text-slate-700">{{ $latestPayment->paid_at?->format('d M Y H:i') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Referensi</dt>
                            <dd class="mt-1 break-all font-medium text-slate-700">{{ $latestPayment->external_id ?? '-' }}</dd>
                        </div>
                    </dl>

                    @if ($canPayNow)
                        <a href="{{ route('orders.pay', $order) }}" class="mt-5 inline-flex w-full justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Bayar Sekarang</a>
                    @endif
                </section>
            @endif
        </div>

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
