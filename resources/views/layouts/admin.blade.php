<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Kairo Ramen')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-side" id="adminSide">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                <span class="seal">&#22238;</span>
                <span>KAIRO RAMEN<br><small>Admin Panel</small></span>
            </a>
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.berita.index') }}" class="{{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">Berita</a>
                <a href="{{ route('admin.galeri.index') }}" class="{{ request()->routeIs('admin.galeri.*') ? 'active' : '' }}">Galeri</a>
                <a href="{{ route('admin.menu.index') }}" class="{{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">Menu</a>
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">Pesanan{{ ($pendingOrders ?? 0) > 0 ? ' ('.$pendingOrders.')' : '' }}</a>
                <a href="{{ route('admin.kontak.index') }}" class="{{ request()->routeIs('admin.kontak.*') ? 'active' : '' }}">Pesan</a>
                <a href="{{ url('/') }}" target="_blank" rel="noopener">Lihat Website</a>
            </nav>
            <form action="{{ route('admin.logout') }}" method="POST" class="admin-logout-side">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </aside>

        <div class="admin-main">
            <header class="admin-top">
                <button class="admin-burger" id="adminBurger" aria-label="Buka menu admin" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
                <p class="admin-hello">Halo, <strong>{{ auth()->user()->name ?? 'Admin' }}</strong></p>
                <form action="{{ route('admin.logout') }}" method="POST" class="admin-logout-top">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </header>

            <main class="admin-content">
                @if(session('success'))
                    <div class="success-message" style="max-width:none;">{{ session('success') }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        (function () {
            var burger = document.getElementById('adminBurger');
            var side = document.getElementById('adminSide');
            if (burger && side) {
                burger.addEventListener('click', function () {
                    var open = side.classList.toggle('open');
                    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
                side.addEventListener('click', function (e) {
                    if (e.target.tagName === 'A') side.classList.remove('open');
                });
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>
