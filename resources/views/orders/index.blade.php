@extends('layouts.app')

@section('title', 'Riwayat Pesanan - IntegraMart')

@section('content')
    <div>
        <p class="text-sm font-semibold text-indigo-600">My Orders</p>
        <h1 class="mt-2 text-2xl font-semibold text-slate-900">Riwayat Belanja</h1>
        <p class="mt-1 text-sm text-slate-400">Daftar transaksi yang pernah kamu buat.</p>
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                <tr>
                    <th class="px-5 py-3">Order</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Total</th>
                    <th class="px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($orders as $order)
                    <tr>
                        <td class="px-5 py-4 font-semibold text-slate-900">{{ $order->order_number ?? $order->order_code }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td class="px-5 py-4 text-slate-600">Rp {{ number_format((float) ($order->total_amount ?? $order->grand_total), 0, ',', '.') }}</td>
                        <td class="px-5 py-4">
                            <a href="{{ route('orders.show', $order) }}" class="font-semibold text-indigo-600 hover:text-indigo-700">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-400">Belum ada pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $orders->links() }}
    </div>
@endsection
