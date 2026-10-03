@extends('layouts.app')

@section('title', 'Home - Tasty Food')

@section('content')

{{-- HERO --}}
<section class="hero">
    <div class="hero-content">
        <div class="hero-rule"></div>
        <h1><span class="thin">HEALTHY</span>TASTY FOOD</h1>
        <p>
            Makanan yang lezat, sehat, dan dibuat dengan bahan-bahan
            berkualitas untuk menemani setiap momenmu.
        </p>
        <a href="{{ url('/tentang') }}" class="btn-primary">TENTANG KAMI</a>
    </div>
    <div class="hero-image">
        <img
            src="https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=1000&q=80"
            alt="Hidangan Tasty Food">
    </div>
</section>

{{-- TENTANG KAMI --}}
<section class="section-pad">
    <div class="center-text">
        <p class="section-label">TENTANG KAMI</p>
        <p>
            Tasty Food hadir untuk memberikan pengalaman menikmati makanan
            yang lezat sekaligus berkualitas. Kami menggunakan bahan-bahan
            pilihan dan menjaga setiap proses pengolahan agar menghasilkan
            makanan yang sehat dan nikmat.
        </p>
        <div class="underline"></div>
    </div>
</section>

{{-- KEUNGGULAN --}}
<section class="features-band">
    <div class="features-grid">
        <div class="feature-card">
            <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=300&q=80" alt="Bahan segar">
            <h3>BAHAN SEGAR</h3>
            <p>Sayuran dan bahan pilihan yang segar setiap hari untuk cita rasa terbaik.</p>
        </div>
        <div class="feature-card">
            <img src="https://images.unsplash.com/photo-1467003909585-2f8a72700288?auto=format&fit=crop&w=300&q=80" alt="Resep sehat">
            <h3>RESEP SEHAT</h3>
            <p>Diolah dengan gizi seimbang tanpa mengorbankan kelezatan hidangan.</p>
        </div>
        <div class="feature-card">
            <img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=300&q=80" alt="Cita rasa nusantara">
            <h3>CITA RASA NUSANTARA</h3>
            <p>Kekayaan kuliner Indonesia yang autentik dalam setiap sajian kami.</p>
        </div>
        <div class="feature-card">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=300&q=80" alt="Sajian berkualitas">
            <h3>SAJIAN BERKUALITAS</h3>
            <p>Setiap piring disiapkan dengan teliti oleh tim dapur berpengalaman.</p>
        </div>
    </div>
</section>

{{-- BERITA KAMI --}}
<section class="news-section">
    <div class="section-heading">
        <p class="section-label">BERITA</p>
        <h2>BERITA KAMI</h2>
    </div>

    @if($latestNews->count())
        <div class="news-grid">
            @foreach($latestNews as $item)
                <article class="news-card">
                    <img
                        src="{{ $item->image ? asset('storage/' . $item->image) : 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80' }}"
                        alt="{{ $item->title }}">
                    <div class="news-card-content">
                        <p class="news-date">{{ $item->created_at->format('d M Y') }}</p>
                        <h3>{{ $item->title }}</h3>
                        <p>{{ Str::limit($item->content, 100) }}</p>
                        <div class="card-foot">
                            <a class="read-more" href="{{ url('/berita/' . $item->id) }}">Baca selengkapnya</a>
                            <span class="dots">...</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="news-grid">
            <article class="news-card">
                <img src="https://images.unsplash.com/photo-1567337710282-00832b415979?auto=format&fit=crop&w=800&q=80" alt="Makanan khas nusantara">
                <div class="news-card-content">
                    <h3>MAKANAN KHAS NUSANTARA</h3>
                    <p>Jelajahi kekayaan kuliner Indonesia dari Sabang sampai Merauke.</p>
                    <div class="card-foot">
                        <a class="read-more" href="{{ url('/berita') }}">Baca selengkapnya</a>
                        <span class="dots">...</span>
                    </div>
                </div>
            </article>
            <article class="news-card">
                <img src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?auto=format&fit=crop&w=800&q=80" alt="Tips memilih bahan">
                <div class="news-card-content">
                    <h3>TIPS MEMILIH BAHAN SEGAR</h3>
                    <p>Cara memilih sayur dan bahan makanan yang segar dan berkualitas.</p>
                    <div class="card-foot">
                        <a class="read-more" href="{{ url('/berita') }}">Baca selengkapnya</a>
                        <span class="dots">...</span>
                    </div>
                </div>
            </article>
            <article class="news-card">
                <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800&q=80" alt="Menu sehat">
                <div class="news-card-content">
                    <h3>INSPIRASI MENU SEHAT</h3>
                    <p>Ide menu sederhana yang lezat untuk aktivitas sehari-hari.</p>
                    <div class="card-foot">
                        <a class="read-more" href="{{ url('/berita') }}">Baca selengkapnya</a>
                        <span class="dots">...</span>
                    </div>
                </div>
            </article>
        </div>
    @endif
</section>

{{-- GALERI KAMI --}}
<section class="gallery-section">
    <div class="section-heading">
        <p class="section-label">GALERI</p>
        <h2>GALERI KAMI</h2>
    </div>

    <div class="gallery-grid">
        @if($latestGalleries->count())
            @foreach($latestGalleries as $gallery)
                <a href="{{ url('/galeri/' . $gallery->id) }}">
                    <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}" style="width:100%;height:300px;object-fit:cover;display:block;">
                </a>
            @endforeach
        @else
            <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=700&q=80" alt="Salad segar">
            <img src="https://images.unsplash.com/photo-1467003909585-2f8a72700288?auto=format&fit=crop&w=700&q=80" alt="Salmon panggang">
            <img src="https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=700&q=80" alt="Salad buah">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=700&q=80" alt="Hidangan utama">
            <img src="https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=700&q=80" alt="Pasta sehat">
            <img src="https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?auto=format&fit=crop&w=700&q=80" alt="Pancake buah">
        @endif
    </div>

    <div class="gallery-button">
        <a href="{{ url('/galeri') }}" class="btn-primary">LIHAT LEBIH BANYAK</a>
    </div>
</section>

@endsection
