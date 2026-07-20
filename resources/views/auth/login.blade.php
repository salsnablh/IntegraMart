<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - IntegraMart</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f7f7f2;
            color: #1f2933;
            font-family: Arial, sans-serif;
        }

        main {
            width: min(420px, calc(100vw - 32px));
            padding: 28px;
            background: #ffffff;
            border: 1px solid #deded4;
            border-radius: 8px;
            box-shadow: 0 12px 30px rgba(31, 41, 51, 0.08);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 24px;
        }

        p {
            margin: 0 0 24px;
            color: #667085;
            line-height: 1.5;
        }

        a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 44px;
            border-radius: 6px;
            background: #1f2933;
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <main>
        <h1>IntegraMart</h1>
        <p>Masuk menggunakan akun Google untuk mengakses sistem integrasi.</p>
        <a href="{{ route('auth.google.redirect') }}">Login dengan Google</a>
    </main>
</body>
</html>
