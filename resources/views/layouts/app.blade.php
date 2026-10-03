<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Tasty Food')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <header>
        <nav>
            <a href="{{ url('/') }}">TASTY FOOD</a>

            <div>
                <a href="{{ url('/') }}">HOME</a>
                <a href="{{ url('/tentang') }}">TENTANG</a>
                <a href="{{ url('/berita') }}">BERITA</a>
                <a href="{{ url('/galeri') }}">GALERI</a>
                <a href="{{ url('/kontak') }}">KONTAK</a>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Tasty Food. All Rights Reserved.</p>
    </footer>

</body>
</html>