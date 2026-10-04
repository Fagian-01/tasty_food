<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Kairo Ramen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-body">
    <main class="login-wrap">
        <div class="login-card">
            <a href="{{ url('/') }}" class="logo login-logo"><span class="seal">&#22238;</span>KAIRO RAMEN</a>
            <p class="login-sub">Admin Panel &mdash; masuk untuk mengelola konten</p>

            @if($errors->any())
                <div class="error-box login-error" role="alert">
                    <ul>
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" id="loginForm">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@kairoramen.test" required autofocus autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn-primary login-btn" id="loginBtn">LOGIN</button>
            </form>

            <a href="{{ url('/') }}" class="login-back">&larr; Kembali ke website</a>
        </div>
    </main>

    <script>
        (function () {
            var form = document.getElementById('loginForm');
            var btn = document.getElementById('loginBtn');
            if (form && btn) {
                form.addEventListener('submit', function () {
                    btn.disabled = true;
                    btn.textContent = 'MEMPROSES...';
                });
            }
        })();
    </script>
</body>
</html>
