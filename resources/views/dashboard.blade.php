@extends('layouts.admin')

@section('title', 'Dashboard - IntegraMart')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-indigo-600">Dashboard Analitik</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Ringkasan Performa Bisnis</h1>
            <p class="mt-1 text-sm text-slate-400">Pantau pendapatan, pesanan, stok, dan aktivitas terbaru IntegraMart.</p>
        </div>
        <div class="text-sm text-slate-400">{{ now()->format('d M Y') }}</div>
    </div>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-400">Pendapatan Bulan Ini</p>
            <p class="mt-3 text-2xl font-semibold text-slate-900">Rp {{ number_format((float) $monthRevenue, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs font-medium text-indigo-600">Total penjualan terbayar</p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-400">Pesanan Baru</p>
            <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $newOrdersCount }}</p>
            <p class="mt-2 text-xs font-medium text-amber-700">Butuh diproses atau dikirim</p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-400">Total Produk</p>
            <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $totalProducts }}</p>
            <p class="mt-2 text-xs font-medium text-rose-700">{{ $lowStockCount }} kritis, {{ $outOfStockCount }} habis</p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-400">Customer Terdaftar</p>
            <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $totalCustomers }}</p>
            <p class="mt-2 text-xs font-medium text-emerald-700">Database customer aktif</p>
        </div>
    </section>

    <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Quick Actions</h2>
                <p class="mt-1 text-sm text-slate-400">Akses cepat untuk pekerjaan operasional harian.</p>
            </div>
            <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.products.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">+ Tambah Produk Baru</a>
                <a href="{{ route('admin.orders.index') }}" class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-900">Kelola Pesanan</a>
                <a href="{{ route('admin.reports.index') }}" class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-900">Lihat Laporan Penjualan</a>
            </div>
        </div>
    </section>

    <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Tren Pendapatan</h2>
                <p class="mt-1 text-sm text-slate-400">Pendapatan dari order non-cancelled selama 30 hari terakhir.</p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">Buka laporan</a>
        </div>
        <div class="mt-5 h-72">
            <canvas id="salesTrendChart" class="h-full w-full"></canvas>
        </div>
    </section>

    <div class="mt-6 grid gap-6 lg:grid-cols-[0.9fr_1.4fr]">
        <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Low Stock Alert</h2>
                <p class="mt-1 text-sm text-slate-400">Produk dengan stok di bawah 5 item.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="px-5 py-3">Produk</th>
                            <th class="px-5 py-3 text-right">Stok</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($lowStockProducts as $product)
                            <tr class="text-slate-600">
                                <td class="px-5 py-3">
                                    <div class="font-semibold text-slate-900">{{ $product->name }}</div>
                                    <div class="mt-1 text-xs text-slate-400">{{ $product->sku }} - {{ $product->category ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @if ($product->stock <= 0)
                                        <span class="inline-flex rounded-full bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-700 ring-1 ring-rose-600/20">Habis</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-600/20">{{ $product->stock }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">Restok</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">Tidak ada produk stok kritis.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section id="recent-orders" class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Recent Orders</h2>
                <p class="mt-1 text-sm text-slate-400">10 transaksi terakhir beserta status pembayaran dan pengiriman.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="px-5 py-3">Order</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3 text-right">Total</th>
                            <th class="px-5 py-3">Payment</th>
                            <th class="px-5 py-3">Shipping</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentOrders as $order)
                            <tr class="text-slate-600">
                                <td class="whitespace-nowrap px-5 py-3">
                                    <div class="font-semibold text-slate-900">{{ $order->order_code }}</div>
                                    <div class="mt-1 text-xs text-slate-400">{{ $order->created_at->format('d M Y H:i') }}</div>
                                </td>
                                <td class="px-5 py-3">{{ $order->customer->name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-5 py-3 text-right font-semibold text-slate-900">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-5 py-3">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold ring-1 {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-3">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold ring-1 {{ in_array($order->shipping_status, ['created', 'shipped', 'delivered'], true) ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-slate-50 text-slate-600 ring-slate-200' }}">
                                        {{ str_replace('_', ' ', ucfirst($order->shipping_status)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-sm text-slate-400">Belum ada order terbaru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <section id="sales-summary" class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Laporan Penjualan</h2>
        <p class="mt-1 text-sm text-slate-400">Ringkasan awal untuk modul laporan. Pendapatan bulan ini dihitung dari order dengan status pembayaran paid.</p>
        <div class="mt-4 rounded-md bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700 ring-1 ring-indigo-600/20">
            Total pendapatan bulan ini: Rp {{ number_format((float) $monthRevenue, 0, ',', '.') }}
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const salesTrendData = @json($salesTrend);
        const salesTrendCanvas = document.getElementById('salesTrendChart');

        if (salesTrendCanvas) {
            new Chart(salesTrendCanvas, {
                type: 'line',
                data: {
                    labels: salesTrendData.map((item) => item.label),
                    datasets: [{
                        label: 'Pendapatan',
                        data: salesTrendData.map((item) => item.value),
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.12)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => 'Rp ' + Number(context.raw || 0).toLocaleString('id-ID'),
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => 'Rp ' + Number(value).toLocaleString('id-ID'),
                            },
                        },
                    },
                },
            });
        }
    </script>
@endsection
