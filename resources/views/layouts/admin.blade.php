<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin - IntegraMart')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <x-flash-message />

    <div class="flex min-h-screen">
        <aside class="hidden w-64 shrink-0 border-r border-slate-200 bg-white lg:flex lg:flex-col">
            <div class="border-b border-slate-200 px-6 py-5">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold text-indigo-600">IntegraMart</a>
                <p class="mt-1 text-xs text-slate-400">Admin Panel</p>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-4 text-sm font-medium">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">Dashboard</a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-indigo-600">Kelola Produk</a>
                <a href="{{ route('admin.products.index') }}#stok" class="flex items-center rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-indigo-600">Stok</a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-indigo-600">Orders</a>
            </nav>

            <div class="border-t border-slate-200 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Logout</button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="border-b border-slate-200 bg-white lg:hidden">
                <div class="flex items-center justify-between px-4 py-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold text-indigo-600">IntegraMart</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white">Logout</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 px-4 py-8 sm:px-6 lg:px-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
