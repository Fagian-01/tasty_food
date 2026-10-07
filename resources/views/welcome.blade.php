@extends('layouts.app')

@section('title', 'Home - Kairo Ramen')

@section('content')

{{-- HERO --}}
<section class="hero">
    <div class="hero-content">
        <p class="hero-kicker">JAPANESE COMFORT FOOD &middot; ラーメン</p>
        <div class="hero-rule"></div>
        <h1><span class="thin">JAPANESE</span>KAIRO RAMEN</h1>
        <p>
            Japanese comfort food for every moment.
            Semangkuk ramen hangat dengan kaldu autentik,
            disajikan segar untuk menemani setiap momenmu.
        </p>
        <a href="{{ url('/tentang') }}" class="btn-primary">TENTANG KAMI</a>
    </div>
    <div class="hero-visual">
        <img
            src="{{ asset('images/hero-ramen-cutout.png') }}"
            alt="Mangkuk ramen Kairo Ramen"
            fetchpriority="high">
    </div>
</section>

{{-- TENTANG KAMI --}}
<section class="section-pad">
    <div class="center-text">
        <p class="section-label">TENTANG KAMI</p>
        <p>
            Kairo Ramen lahir dari kecintaan pada semangkuk ramen hangat.
            Kami merebus kaldu perlahan, memakai mi segar setiap hari,
            dan menyajikan Japanese comfort food yang sederhana,
            hangat, dan bikin kembali lagi.
        </p>
        <div class="underline"></div>
    </div>
</section>

{{-- KEUNGGULAN --}}
<section class="features-band">
    <div class="features-grid">
        <div class="feature-card">
            <img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=300&q=80" alt="Kaldu 12 jam">
            <h3>KALDU 12 JAM</h3>
            <p>Kaldu ayam dan tulang direbus perlahan hingga gurih dan hangat.</p>
        </div>
        <div class="feature-card">
            <img src="https://images.unsplash.com/photo-1496116218417-1a781b1c416c?auto=format&fit=crop&w=300&q=80" alt="Gyoza segar">
            <h3>BAHAN SEGAR</h3>
            <p>Mi, sayur, dan topping disiapkan segar setiap hari di dapur kami.</p>
        </div>
        <div class="feature-card">
            <img src="https://images.unsplash.com/photo-1579871494447-9811cf80d66c?auto=format&fit=crop&w=300&q=80" alt="Resep autentik">
            <h3>RESEP AUTENTIK</h3>
            <p>Racikan shoyu, miso, dan tare khas Jepang dengan cita rasa seimbang.</p>
        </div>
        <div class="feature-card">
            <img src="https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=300&q=80" alt="Sajian hangat">
            <h3>SAJIAN HANGAT</h3>
            <p>Disajikan panas mengepul, porsi pas, nyaman untuk makan sendiri maupun bersama.</p>
        </div>
    </div>
</section>

{{-- SIGNATURE --}}
<section class="sig-sec">
    <div class="section-heading">
        <p class="section-label">SIGNATURE</p>
        <h2>FAVORIT DI KAIRO</h2>
    </div>
    <div class="sig-grid">
        @if(isset($featuredMenus) && $featuredMenus->count())
            @foreach($featuredMenus as $menu)
                <article class="sig-card">
                    <img
                        src="{{ $menu->image ? asset('storage/' . $menu->image) : asset('images/hero-ramen-cutout.png') }}"
                        alt="{{ $menu->name }}"
                        loading="lazy">
                    <div class="sig-body">
                        @if($menu->category)
                            <p class="menu-cat">{{ strtoupper($menu->category) }}</p>
                        @endif
                        <h3>{{ strtoupper($menu->name) }}</h3>
                        @if($menu->description)
                            <p class="menu-desc">{{ $menu->description }}</p>
                        @endif
                        <p class="price">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                    </div>
                </article>
            @endforeach
        @else
        <div class="sig-empty">
            <p class="empty-title">Signature menu segera hadir.</p>
            <p>Dapur kami sedang menyiapkan pilihan terbaik. Sementara itu, lihat semua menu yang tersedia.</p>
            <a href="{{ route('menu.index') }}" class="btn-primary">LIHAT SEMUA MENU</a>
        </div>
        @endif
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
                        src="{{ $item->image ? asset('storage/' . $item->image) : 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=800&q=80' }}"
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
                <img src="https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=800&q=80" alt="Menu ramen baru">
                <div class="news-card-content">
                    <h3>MENU RAMEN BARU</h3>
                    <p>Kenalan dengan miso butter ramen, kuah creamy favorit musim hujan.</p>
                    <div class="card-foot">
                        <a class="read-more" href="{{ url('/berita') }}">Baca selengkapnya</a>
                        <span class="dots">...</span>
                    </div>
                </div>
            </article>
            <article class="news-card">
                <img src="https://images.unsplash.com/photo-1496116218417-1a781b1c416c?auto=format&fit=crop&w=800&q=80" alt="Gyoza homemade">
                <div class="news-card-content">
                    <h3>GYOZA HOMEMADE</h3>
                    <p>Gyoza dilipat satu per satu setiap pagi, renyah di luar juicy di dalam.</p>
                    <div class="card-foot">
                        <a class="read-more" href="{{ url('/berita') }}">Baca selengkapnya</a>
                        <span class="dots">...</span>
                    </div>
                </div>
            </article>
            <article class="news-card">
                <img src="https://images.unsplash.com/photo-1553621042-f6e147245754?auto=format&fit=crop&w=800&q=80" alt="Sushi segar">
                <div class="news-card-content">
                    <h3>SUSHI SEGAR SETIAP HARI</h3>
                    <p>Ikan segar pilihan dan nasi pulen, disiapkan setiap pagi.</p>
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
                    <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}">
                </a>
            @endforeach
        @else
            <img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=700&q=80" alt="Shoyu ramen">
            <img src="https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=700&q=80" alt="Miso ramen">
            <img src="https://images.unsplash.com/photo-1553621042-f6e147245754?auto=format&fit=crop&w=700&q=80" alt="Sushi set">
            <img src="https://images.unsplash.com/photo-1496116218417-1a781b1c416c?auto=format&fit=crop&w=700&q=80" alt="Gyoza">
            <img src="https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=700&q=80" alt="Donburi">
            <img src="https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=700&q=80" alt="Karaage">
        @endif
    </div>

    <div class="gallery-button">
        <a href="{{ url('/galeri') }}" class="btn-primary">LIHAT LEBIH BANYAK</a>
    </div>
</section>

{{-- VISIT --}}
<section class="visit-band">
    <h2>LAPAR? <span>MAMPIR KE KAIRO.</span></h2>
    <p>Kedai kami buka setiap hari 10.00 &ndash; 22.00. Hangat, cepat, dan ramah di kantong.</p>
    <a href="{{ url('/kontak') }}" class="btn-primary">HUBUNGI KAMI</a>
</section>

@endsection
