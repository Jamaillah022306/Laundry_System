<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BubbleBee Laundry - Forgot Password</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; }
        .login-wrapper { display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; }

        .login-left { background-color: #f5e642; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; gap: 30px; }
        .login-left img.logo { width: 340px; max-width: 100%; }
        .tagline-box { background-color: #f0d800; border-radius: 12px; padding: 30px 40px; text-align: center; max-width: 400px; width: 100%; }
        .tagline-box p { font-family: 'Georgia', serif; font-style: italic; font-size: 24px; color: #2c3e7a; line-height: 1.5; }

        .login-right { background-color: #4a90d9; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 50px 80px; }
        .login-right h1 { font-family: 'Georgia', serif; font-size: 42px; font-weight: 900; color: #1a1a2e; margin-bottom: 8px; text-align: center; }
        .subtitle { color: #1a1a2e; font-size: 14px; margin-bottom: 35px; text-align: center; }

        .login-form { width: 100%; max-width: 500px; display: flex; flex-direction: column; gap: 18px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 16px; font-weight: 700; color: #1a1a2e; }
        .form-group input { background-color: #a8c8e8; border: none; border-radius: 8px; padding: 14px 16px; font-size: 16px; width: 100%; outline: none; color: #1a1a2e; }
        .form-group input:focus { background-color: #bcd5ee; box-shadow: 0 0 0 3px rgba(255,255,255,0.4); }
        .btn-login { background-color: #2ecc71; color: #1a1a2e; border: none; padding: 16px; border-radius: 8px; font-size: 18px; font-weight: 700; cursor: pointer; width: 100%; margin-top: 5px; }
        .btn-login:hover { background-color: #27ae60; }
        .back-link { text-align: center; margin-top: 16px; }
        .back-link a { color: #1a1a2e; font-size: 14px; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
        .alert-success { background: #d4edda; color: #155724; padding: 12px 18px; border-radius: 8px; font-size: 14px; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 12px 18px; border-radius: 8px; font-size: 14px; }
        .error-text { color: #7a1a1a; font-size: 12px; margin-top: 2px; }
        .info-box { background-color: #a8c8e8; border-radius: 10px; padding: 20px 24px; text-align: center; color: #1a1a2e; font-size: 15px; line-height: 1.6; }

        @media (max-width: 768px) { .login-wrapper { grid-template-columns: 1fr; } .login-left { display: none; } .login-right { padding: 40px 30px; } }
    </style>
</head>
<body>
<div class="login-wrapper">

    <!-- LEFT -->
    <div class="login-left">
        <img class="logo" src="{{ asset('images/Bubble_Bee_Laundry_logo_design-removebg-preview.png') }}" alt="Bubble Bee Laundry">
        <div class="tagline-box">
            <p>Bubbles that care, service that's smart — laundry made effortless.</p>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="login-right">
        <h1>FORGOT PASSWORD?</h1>
        <p class="subtitle">Please contact the admin to reset your password.</p>

        <div class="login-form">
            @if(session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif

            <div class="info-box">
                🔒 Password reset is managed by the system administrator.<br><br>
                Please approach the admin or cashier to have your password reset.
            </div>

            <div class="back-link">
                <a href="{{ route('login') }}">← Back to Login</a>
            </div>
        </div>
    </div>

</div>
</body>
</html>