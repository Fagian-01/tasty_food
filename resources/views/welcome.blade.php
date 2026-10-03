@extends('layouts.app')

@section('title', 'Home - Tasty Food')

@section('content')

<section class="hero">
    <div class="hero-content">
        <h1>TASTY FOOD</h1>

        <p>
            Makanan yang lezat, sehat, dan dibuat dengan bahan-bahan
            berkualitas untuk menemani setiap momenmu.
        </p>

        <a href="{{ url('/tentang') }}" class="btn-primary">
            TENTANG KAMI
        </a>
    </div>

    <div class="hero-image">
        <img
            src="https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=1000&q=80"
            alt="Tasty Food">
    </div>
</section>

<section class="about-section">
    <div class="about-image">
        <img
            src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?auto=format&fit=crop&w=1000&q=80"
            alt="Tentang Tasty Food">
    </div>

    <div class="about-content">
        <p class="section-label">TENTANG KAMI</p>

        <h2>
            MAKANAN SEHAT<br>
            UNTUK KEHIDUPAN<br>
            YANG LEBIH BAIK
        </h2>

        <p>
            Tasty Food hadir untuk memberikan pengalaman menikmati
            makanan yang lezat sekaligus berkualitas. Kami menggunakan
            bahan-bahan pilihan dan menjaga setiap proses pengolahan
            agar menghasilkan makanan yang sehat dan nikmat.
        </p>

        <a href="{{ url('/tentang') }}" class="btn-primary">
            SELENGKAPNYA
        </a>
    </div>
</section>

<section class="news-section">
    <div class="section-heading">
        <p class="section-label">BERITA</p>
        <h2>BERITA TERBARU</h2>
    </div>

    <div class="news-grid">

        <article class="news-card">
            <img
                src="https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=800&q=80"
                alt="Berita makanan">
            <div class="news-card-content">
                <h3>Makanan Sehat dan Bergizi</h3>
                <p>
                    Pilihan makanan sehat dengan bahan berkualitas
                    untuk mendukung gaya hidup yang lebih baik.
                </p>
                <a href="{{ url('/berita') }}">BACA SELENGKAPNYA →</a>
            </div>
        </article>

        <article class="news-card">
            <img
                src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?auto=format&fit=crop&w=800&q=80"
                alt="Berita kuliner">
            <div class="news-card-content">
                <h3>Tips Memilih Bahan Makanan</h3>
                <p>
                    Kenali cara memilih bahan makanan yang segar
                    dan berkualitas untuk hidangan sehari-hari.
                </p>
                <a href="{{ url('/berita') }}">BACA SELENGKAPNYA →</a>
            </div>
        </article>

        <article class="news-card">
            <img
                src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800&q=80"
                alt="Makanan sehat">
            <div class="news-card-content">
                <h3>Inspirasi Menu Sehat</h3>
                <p>
                    Berbagai inspirasi menu sederhana yang tetap
                    lezat dan cocok untuk aktivitas sehari-hari.
                </p>
                <a href="{{ url('/berita') }}">BACA SELENGKAPNYA →</a>
            </div>
        </article>

    </div>
</section>

<section class="gallery-section">
    <div class="section-heading">
        <p class="section-label">GALERI</p>
        <h2>GALERI KAMI</h2>
    </div>

    <div class="gallery-grid">
        <img
            src="https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=700&q=80"
            alt="Galeri makanan 1">

        <img
            src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=700&q=80"
            alt="Galeri makanan 2">

        <img
            src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?auto=format&fit=crop&w=700&q=80"
            alt="Galeri makanan 3">

        <img
            src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=700&q=80"
            alt="Galeri makanan 4">

        <img
            src="https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=700&q=80"
            alt="Galeri makanan 5">

        <img
            src="https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=700&q=80"
            alt="Galeri makanan 6">
    </div>

    <div class="gallery-button">
        <a href="{{ url('/galeri') }}" class="btn-primary">
            LIHAT GALERI
        </a>
    </div>
</section>

<section class="contact-section">
    <div class="contact-content">
        <p class="section-label">KONTAK</p>

        <h2>HUBUNGI KAMI</h2>

        <p>
            Punya pertanyaan, saran, atau ingin mengetahui lebih banyak
            tentang Tasty Food? Silakan hubungi kami melalui form berikut.
        </p>

        <a href="{{ url('/kontak') }}" class="btn-primary">
            HUBUNGI KAMI
        </a>
    </div>

    <div class="contact-info">
        <div>
            <h3>EMAIL</h3>
            <p>info@tastyfood.com</p>
        </div>

        <div>
            <h3>TELEPON</h3>
            <p>+62 812 3456 7890</p>
        </div>

        <div>
            <h3>ALAMAT</h3>
            <p>Cianjur, Jawa Barat</p>
        </div>
    </div>
</section>

@endsection