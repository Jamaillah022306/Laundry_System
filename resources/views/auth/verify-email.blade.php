<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bubble Bee Laundry - Verify Email</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FFEF91;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .verify-card {
            background-color: #4a90d9;
            border-radius: 16px;
            padding: 50px 45px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 4px 4px 16px rgba(0,0,0,0.15);
        }

        .verify-card .logo {
            width: 90px;
            margin-bottom: 20px;
        }

        .verify-card h2 {
            font-size: 28px;
            font-weight: 900;
            color: #1a1a2e;
            text-transform: uppercase;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }

        .verify-card p {
            color: #1a1a2e;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .verify-card p.sub {
            font-size: 13px;
            color: #2c3e7a;
            margin-bottom: 28px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .btn-resend {
            background-color: #2ecc71;
            color: #1a1a2e;
            border: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }

        .btn-resend:hover {
            background-color: #27ae60;
            transform: translateY(-1px);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #1a1a2e;
            font-size: 14px;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="verify-card">
    <img src="{{ asset('images/Bubble_Bee_Laundry_logo_design-removebg-preview.png') }}" alt="Bubble Bee Logo" class="logo">

    <h2>Check Your Email</h2>

    <p>A verification link has been sent to your email address. Please click the link to activate your account before logging in.</p>
    <p class="sub">Didn't receive the email? Check your spam folder or click below to resend.</p>

    @if (session('message'))
        <div class="alert-success">{{ session('message') }}</div>
    @endif

    @if (session('warning'))
        <div class="alert-warning">{{ session('warning') }}</div>
    @endif

    @if (Auth::check())
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-resend">Resend Verification Email</button>
        </form>
    @endif

    <a href="{{ route('login') }}" class="back-link">← Back to login</a>
</div>

</body>
</html>