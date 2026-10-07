<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kairo Ramen')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header{{ request()->is('/') ? ' is-home' : '' }}" id="siteHeader">
        <nav class="site-nav">
            <a href="{{ url('/') }}" class="logo"><span class="seal">&#22238;</span>KAIRO RAMEN</a>
            <button class="nav-toggle" id="navToggle" aria-label="Buka menu navigasi" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="nav-links" id="navLinks">
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">HOME</a>
                <a href="{{ url('/tentang') }}" class="{{ request()->is('tentang') ? 'active' : '' }}">TENTANG</a>
                <a href="{{ url('/berita') }}" class="{{ request()->is('berita*') ? 'active' : '' }}">BERITA</a>
                <a href="{{ url('/galeri') }}" class="{{ request()->is('galeri*') ? 'active' : '' }}">GALERI</a>
                <a href="{{ url('/menu') }}" class="{{ request()->is('menu*') ? 'active' : '' }}">MENU</a>
                <a href="{{ url('/kontak') }}" class="{{ request()->is('kontak*') ? 'active' : '' }}">KONTAK</a>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <h4>Kairo Ramen</h4>
                <p>Japanese comfort food for every moment. Ramen hangat, gyoza, karaage, dan donburi yang dibuat dengan kaldu autentik dan bahan pilihan.</p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook" class="soc-fb">f</a>
                    <a href="#" aria-label="Twitter" class="soc-tw">&#116;</a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Useful links</h4>
                <a href="{{ url('/berita') }}">Berita</a>
                <a href="{{ url('/galeri') }}">Galeri</a>
                <a href="{{ url('/tentang') }}">Tentang Kami</a>
                <a href="{{ url('/kontak') }}">Kontak Kami</a>
            </div>
            <div class="footer-col">
                <h4>Privacy</h4>
                <a href="#">Karir</a>
                <a href="{{ url('/tentang') }}">Tentang Kami</a>
                <a href="{{ url('/kontak') }}">Kontak Kami</a>
                <a href="#">Servis</a>
            </div>
            <div class="footer-col">
                <h4>Contact Info</h4>
                <p>kairoramen@gmail.com</p>
                <p>+62 812 3456 7890</p>
                <p>Kota Bandung, Jawa Barat</p>
            </div>
        </div>
        <p class="footer-copy">Copyright &copy;{{ date('Y') }} Kairo Ramen. All rights reserved &middot; <a href="{{ route('admin.login') }}" style="color:inherit;text-decoration:underline;">Admin</a></p>
    </footer>

    <script>
        (function () {
            var toggle = document.getElementById('navToggle');
            var links = document.getElementById('navLinks');
            var header = document.getElementById('siteHeader');
            if (toggle && links) {
                toggle.addEventListener('click', function () {
                    var open = links.classList.toggle('open');
                    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
                links.addEventListener('click', function (e) {
                    if (e.target.tagName === 'A') links.classList.remove('open');
                });
            }
            var hero = document.querySelector('.page-hero');
            function onScroll() {
                if (!header) return;
                if (hero) {
                    header.classList.toggle('scrolled', window.scrollY > hero.offsetHeight - 80);
                } else {
                    header.classList.toggle('scrolled', window.scrollY > 40);
                }
            }
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        })();
    </script>
    @stack('scripts')
</body>
</html>
