<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - IntegraMart</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-screen bg-slate-50 text-slate-600 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <p class="text-sm font-semibold text-indigo-600">IntegraMart</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">Masuk ke dashboard</h1>
                <p class="mt-2 text-sm leading-6 text-slate-400">Gunakan akun Google untuk mengakses inventory, order, payment, shipping, dan CRM.</p>
            </div>

            @if (session('status'))
                <div class="mb-4 rounded-md bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-600/20">
                    {{ session('status') }}
                </div>
            @endif

            <a href="{{ route('auth.google.redirect') }}" class="flex min-h-11 w-full items-center justify-center gap-3 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white text-sm font-bold text-indigo-600">G</span>
                Login dengan Google
            </a>

            <p class="mt-5 text-center text-xs leading-5 text-slate-400">Pastikan Google OAuth Client ID dan Client Secret sudah diatur di file environment.</p>
        </section>
    </main>
</body>
</html>
