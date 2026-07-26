<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Portal - IntegraMart</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="grid w-full max-w-5xl gap-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:grid-cols-[1.1fr_0.9fr] lg:p-8">
            <section class="flex flex-col justify-between rounded-2xl bg-slate-950 p-8 text-white">
                <div>
                    <p class="text-sm font-semibold tracking-wide text-indigo-300">IntegraMart</p>
                    <h1 class="mt-4 text-3xl font-semibold leading-tight text-white sm:text-4xl">Pilih portal masuk sebelum melanjutkan.</h1>
                    <p class="mt-4 max-w-xl text-sm leading-6 text-slate-300">Gunakan jalur pelanggan untuk belanja dan checkout, atau jalur admin untuk mengelola katalog, pesanan, dan laporan.</p>
                </div>

                <div class="mt-10 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Customer</p>
                        <p class="mt-2 text-sm leading-6 text-slate-200">Akses katalog, keranjang, checkout, dan riwayat pesanan.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Admin</p>
                        <p class="mt-2 text-sm leading-6 text-slate-200">Akses dashboard, produk, order, dan laporan penjualan.</p>
                    </div>
                </div>
            </section>

            <section class="flex flex-col justify-center p-2 sm:p-4 lg:p-6">
                <div class="mb-6">
                    <p class="text-sm font-semibold text-indigo-600">Login Portal</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">Masuk ke area yang tepat</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Pilih peran Anda untuk melanjutkan proses login ke akun Google yang sesuai.</p>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('login.customer') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-left shadow-sm transition hover:border-indigo-300 hover:bg-white">
                        <span>
                            <span class="block text-base font-semibold text-slate-900">Masuk sebagai Pelanggan</span>
                            <span class="mt-1 block text-sm text-slate-500">Lanjutkan belanja dan pantau pesanan Anda.</span>
                        </span>
                        <span class="ml-4 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white">&rarr;</span>
                    </a>

                    <a href="{{ route('login.admin') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-5 py-4 text-left shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                        <span>
                            <span class="block text-base font-semibold text-slate-900">Masuk sebagai Admin</span>
                            <span class="mt-1 block text-sm text-slate-500">Kelola operasional toko dari dashboard.</span>
                        </span>
                        <span class="ml-4 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">&rarr;</span>
                    </a>
                </div>

                <a href="{{ route('shop.index') }}" class="mt-6 inline-flex w-fit text-sm font-semibold text-slate-500 hover:text-indigo-600">Kembali ke katalog</a>
            </section>
        </div>
    </main>
</body>
</html>