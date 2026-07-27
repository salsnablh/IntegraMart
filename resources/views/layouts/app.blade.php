<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'IntegraMart')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <x-flash-message />

    @php($cartCount = collect(session('cart', []))->sum())

    <header class="border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:px-8 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('shop.index') }}" class="text-lg font-semibold tracking-normal text-indigo-600">IntegraMart</a>

                <form method="GET" action="{{ route('shop.index') }}" class="hidden flex-1 sm:block">
                    <input name="search" value="{{ request('search') }}" type="search" placeholder="Cari produk" class="w-full min-w-[280px] rounded-md border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20">
                </form>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-sm">
                <a href="{{ route('shop.index') }}" class="rounded-md bg-slate-100 px-3 py-2 font-medium text-slate-700 hover:bg-slate-200">Katalog</a>
                <a href="{{ route('cart.index') }}" class="relative rounded-md px-3 py-2 font-medium text-slate-600 hover:bg-slate-100 hover:text-indigo-600">
                    Cart
                    @if ($cartCount > 0)
                        <span class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-emerald-500 px-1 text-xs font-bold text-white">{{ $cartCount }}</span>
                    @endif
                </a>
                @auth
                    <a href="{{ route('orders.index') }}" class="rounded-md px-3 py-2 font-medium text-slate-600 hover:bg-slate-100 hover:text-indigo-600">
                        Pesanan saya
                    </a>
                    <div class="rounded-md bg-slate-100 px-3 py-2 font-medium text-slate-700">{{ auth()->user()->name }}</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 font-medium text-white hover:bg-indigo-700">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login.portal') }}" class="rounded-md bg-indigo-600 px-3 py-2 font-medium text-white hover:bg-indigo-700">Login</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>
</body>
</html>

