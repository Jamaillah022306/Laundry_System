<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BubbleBee Laundry - Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; }
        .login-wrapper { display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; }

        /* LEFT - YELLOW */
        .login-left { background-color: #f5e642; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; gap: 30px; }
        .login-left img.logo { width: 340px; max-width: 100%; }
        .tagline-box { background-color: #f0d800; border-radius: 12px; padding: 30px 40px; text-align: center; max-width: 400px; width: 100%; }
        .tagline-box p { font-family: 'Georgia', serif; font-style: italic; font-size: 24px; color: #2c3e7a; line-height: 1.5; }

        /* RIGHT - BLUE */
        .login-right { background-color: #4a90d9; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 50px 80px; }
        .login-right h1 { font-family: 'Georgia', serif; font-size: 42px; font-weight: 900; color: #1a1a2e; margin-bottom: 8px; text-align: center; }
        .subtitle { color: #1a1a2e; font-size: 14px; margin-bottom: 35px; text-align: center; }

        /* FORM */
        .login-form { width: 100%; max-width: 500px; display: flex; flex-direction: column; gap: 18px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 16px; font-weight: 700; color: #1a1a2e; }
        .form-group input { background-color: #a8c8e8; border: none; border-radius: 8px; padding: 14px 16px; font-size: 16px; width: 100%; outline: none; color: #1a1a2e; }
        .form-group input:focus { background-color: #bcd5ee; box-shadow: 0 0 0 3px rgba(255,255,255,0.4); }
        .forgot-password { color: #1a1a2e; font-size: 13px; text-decoration: none; text-align: right; display: block; margin-top: 4px; }
        .forgot-password:hover { text-decoration: underline; }
        .btn-login { background-color: #2ecc71; color: #1a1a2e; border: none; padding: 16px; border-radius: 8px; font-size: 18px; font-weight: 700; cursor: pointer; width: 100%; margin-top: 5px; }
        .btn-login:hover { background-color: #27ae60; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 12px 18px; border-radius: 8px; width: 100%; max-width: 500px; margin-bottom: 15px; font-size: 14px; }
        @media (max-width: 768px) { .login-wrapper { grid-template-columns: 1fr; } .login-left { display: none; } .login-right { padding: 40px 30px; } }
    </style>
</head>
<body>
<div class="login-wrapper">

    <!-- LEFT - YELLOW -->
    <div class="login-left">
        <img src="{{ asset('images/Bubble_Bee_Laundry_logo_design-removebg-preview.png') }}" ...>
        <div class="tagline-box">
            <p>Bubbles that care, service that's smart — laundry made effortless.</p>
        </div>
    </div>

    <!-- RIGHT - BLUE -->
    <div class="login-right">
        <h1>WELCOME BACK!</h1>
        <p class="subtitle">Please sign in your account to continue</p>

        @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="login-form">
            @csrf
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autocomplete="username">
            </div>
           <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <a href="/forgot-password" class="forgot-password">Forgot Password?</a>
            </div>
            <button type="submit" class="btn-login">Log in</button>
        </form>
    </div>

</div>
</body>
</html>