@extends('layouts.app')

@section('title', 'Galeri Kami - Kairo Ramen')

@section('content')

{{-- HERO --}}
<section class="page-hero">
    <div>
        <h1>GALERI KAMI</h1>
        <p>Ramen, gyoza, sushi, dan momen hangat di Kairo Ramen</p>
    </div>
</section>

{{-- SLIDER --}}
<section style="background:#f5f5f5;padding:70px 8%;">
    @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <div class="admin-bar">
        <a href="{{ url('/galeri/create') }}" class="btn-primary">+ TAMBAH GALERI</a>
    </div>

    @if($galleries->count())
        <div class="gallery-slider" id="gallerySlider">
            @foreach($galleries->take(5) as $i => $g)
                <div class="slide {{ $i === 0 ? 'active' : '' }}">
                    <a href="{{ url('/galeri/' . $g->id) }}">
                        <img src="{{ asset('storage/' . $g->image) }}" alt="{{ $g->title }}">
                    </a>
                    <p class="slider-cap">{{ $g->title }}</p>
                </div>
            @endforeach
            @if($galleries->count() > 1)
                <button class="slider-btn prev" type="button" onclick="moveSlide(-1)">&#8249;</button>
                <button class="slider-btn next" type="button" onclick="moveSlide(1)">&#8250;</button>
            @endif
        </div>
    @endif
</section>

{{-- GRID --}}
<section class="gallery-section" style="padding-top:70px;">
    @if($galleries->count())
        <div class="gallery-grid">
            @foreach($galleries as $gallery)
                <article class="g-card">
                    <a href="{{ url('/galeri/' . $gallery->id) }}">
                        <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}" loading="lazy">
                    </a>
                    <div class="g-body">
                        <h3>{{ strtoupper(Str::limit($gallery->title, 40)) }}</h3>
                        @if($gallery->description)
                            <p>{{ Str::limit($gallery->description, 80) }}</p>
                        @endif
                        <div class="card-foot">
                            <a class="read-more" href="{{ url('/galeri/' . $gallery->id) }}">Lihat detail</a>
                            <span class="dots">...</span>
                        </div>
                        <div class="crud-actions">
                            <a href="{{ url('/galeri/' . $gallery->id . '/edit') }}">EDIT</a>
                            <form action="{{ url('/galeri/' . $gallery->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus galeri ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="danger">HAPUS</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <p class="empty-state">Belum ada galeri.</p>
    @endif
</section>

@endsection

@push('scripts')
<script>
    (function () {
        var idx = 0;
        window.moveSlide = function (n) {
            var slides = document.querySelectorAll('#gallerySlider .slide');
            if (!slides.length) return;
            slides[idx].classList.remove('active');
            idx = (idx + n + slides.length) % slides.length;
            slides[idx].classList.add('active');
        };
    })();
</script>
@endpush
