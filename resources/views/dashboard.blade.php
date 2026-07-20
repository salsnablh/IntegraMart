<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - IntegraMart</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: #f7f7f2;
            color: #1f2933;
            font-family: Arial, sans-serif;
        }

        main {
            width: min(760px, calc(100vw - 32px));
            margin: 48px auto;
            padding: 28px;
            background: #ffffff;
            border: 1px solid #deded4;
            border-radius: 8px;
        }

        dl {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 12px;
            margin: 24px 0;
        }

        dt {
            color: #667085;
        }

        dd {
            margin: 0;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        button {
            min-height: 40px;
            padding: 0 16px;
            border: 0;
            border-radius: 6px;
            background: #1f2933;
            color: #ffffff;
            cursor: pointer;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <main>
        <h1>Dashboard IntegraMart</h1>
        <p>User Google berhasil tersimpan dan sedang login.</p>

        <dl>
            <dt>Nama</dt>
            <dd>{{ auth()->user()->name }}</dd>
            <dt>Email</dt>
            <dd>{{ auth()->user()->email }}</dd>
            <dt>Google ID</dt>
            <dd>{{ auth()->user()->google_id ?? '-' }}</dd>
        </dl>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </main>
</body>
</html>
