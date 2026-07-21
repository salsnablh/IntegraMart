@extends('layouts.admin')

@section('title', 'Laporan Penjualan - IntegraMart')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between print:hidden">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Sales Report</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Laporan Penjualan</h1>
            <p class="mt-1 text-sm text-slate-400">Analisis pendapatan, transaksi, dan produk terlaris berdasarkan rentang tanggal.</p>
        </div>
        <button type="button" onclick="window.print()" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Print / Export PDF</button>
    </div>

    <div class="hidden print:block">
        <h1 class="text-xl font-semibold text-slate-900">Laporan Penjualan IntegraMart</h1>
        <p class="mt-1 text-sm text-slate-500">Periode {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm print:hidden">
        <div class="grid gap-4 md:grid-cols-[1fr_1fr_auto] md:items-end">
            <div>
                <label for="start_date" class="text-sm font-medium text-slate-700">Tanggal mulai</label>
                <input id="start_date" name="start_date" type="date" value="{{ $startDate->format('Y-m-d') }}" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
            </div>
            <div>
                <label for="end_date" class="text-sm font-medium text-slate-700">Tanggal selesai</label>
                <input id="end_date" name="end_date" type="date" value="{{ $endDate->format('Y-m-d') }}" class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
            </div>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Terapkan Filter</button>
        </div>
        @error('end_date')
            <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </form>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-400">Total Pendapatan</p>
            <p class="mt-3 text-2xl font-semibold text-slate-900">Rp {{ number_format((float) $totalRevenue, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs font-medium text-indigo-600">Order non-cancelled</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-400">Total Transaksi</p>
            <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $totalOrders }}</p>
            <p class="mt-2 text-xs font-medium text-emerald-700">Dalam periode filter</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-400">Rata-rata Order</p>
            <p class="mt-3 text-2xl font-semibold text-slate-900">Rp {{ number_format((float) $averageOrderValue, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs font-medium text-slate-400">Average order value</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-400">Produk Terlaris</p>
            <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $bestSellers->first()->name ?? '-' }}</p>
            <p class="mt-2 text-xs font-medium text-amber-700">{{ (int) ($bestSellers->first()->sold_qty ?? 0) }} item terjual</p>
        </div>
    </section>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1fr_1.1fr]">
        <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Ringkasan Harian</h2>
                <p class="mt-1 text-sm text-slate-400">Pendapatan dan jumlah order per hari.</p>
            </div>
            <div class="max-h-96 overflow-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="sticky top-0 bg-slate-50 text-left text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3 text-right">Order</th>
                            <th class="px-5 py-3 text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($dailySummary as $summary)
                            <tr class="text-slate-600">
                                <td class="px-5 py-3 font-medium text-slate-900">{{ $summary['label'] }}</td>
                                <td class="px-5 py-3 text-right">{{ $summary['orders'] }}</td>
                                <td class="px-5 py-3 text-right font-semibold text-slate-900">Rp {{ number_format((float) $summary['revenue'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Best-Seller Products</h2>
                <p class="mt-1 text-sm text-slate-400">Produk dengan kuantitas penjualan tertinggi.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="px-5 py-3">Produk</th>
                            <th class="px-5 py-3 text-right">Terjual</th>
                            <th class="px-5 py-3 text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($bestSellers as $product)
                            <tr class="text-slate-600">
                                <td class="px-5 py-3">
                                    <div class="font-semibold text-slate-900">{{ $product->name }}</div>
                                    <div class="mt-1 text-xs text-slate-400">{{ $product->sku }}</div>
                                </td>
                                <td class="px-5 py-3 text-right">{{ (int) $product->sold_qty }}</td>
                                <td class="px-5 py-3 text-right font-semibold text-slate-900">Rp {{ number_format((float) $product->revenue, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-slate-400">Belum ada produk terjual pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <section class="mt-6 rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-lg font-semibold text-slate-900">Histori Transaksi</h2>
            <p class="mt-1 text-sm text-slate-400">Daftar transaksi lengkap sesuai rentang tanggal filter.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-600">
                    <tr>
                        <th class="px-5 py-3">Order</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Payment</th>
                        <th class="px-5 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $order)
                        <tr class="text-slate-600">
                            <td class="whitespace-nowrap px-5 py-3 font-semibold text-slate-900">{{ $order->order_number ?? $order->order_code }}</td>
                            <td class="whitespace-nowrap px-5 py-3">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="px-5 py-3">{{ $order->customer->name ?? $order->recipient_name }}</td>
                            <td class="whitespace-nowrap px-5 py-3">
                                <span class="inline-flex rounded-full bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-600/20">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-3">{{ ucfirst($order->payment_status) }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-right font-semibold text-slate-900">Rp {{ number_format((float) ($order->total_amount ?? $order->grand_total), 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">Tidak ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-5 print:hidden">
        {{ $transactions->links() }}
    </div>
@endsection
