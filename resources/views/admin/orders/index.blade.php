@extends('layouts.admin')

@section('title', 'Admin Orders - IntegraMart')

@section('content')
    <div>
        <p class="text-sm font-semibold text-indigo-600">Order Management</p>
        <h1 class="mt-2 text-2xl font-semibold text-slate-900">Daftar Transaksi Masuk</h1>
        <p class="mt-1 text-sm text-slate-400">Kelola status pesanan customer dari sini.</p>
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                <tr>
                    <th class="px-5 py-3">Order</th>
                    <th class="px-5 py-3">Customer</th>
                    <th class="px-5 py-3">Total</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Update</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($orders as $order)
                    <tr>
                        <td class="px-5 py-4 font-semibold text-slate-900">{{ $order->order_number ?? $order->order_code }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $order->customer->name ?? $order->recipient_name }}</td>
                        <td class="px-5 py-4 text-slate-600">Rp {{ number_format((float) ($order->total_amount ?? $order->grand_total), 0, ',', '.') }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                                    <option value="pending" @selected($order->status === 'pending')>Pending</option>
                                    <option value="processing" @selected($order->status === 'processing')>Processing</option>
                                    <option value="completed" @selected($order->status === 'completed')>Completed</option>
                                    <option value="cancelled" @selected($order->status === 'cancelled')>Cancelled</option>
                                </select>
                                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Simpan</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-400">Belum ada order masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $orders->links() }}
    </div>
@endsection
