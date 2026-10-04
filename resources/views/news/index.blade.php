@extends('layouts.app')

@section('title', 'Berita Kami - Kairo Ramen')

@section('content')

{{-- HERO --}}
<section class="page-hero">
    <div>
        <h1>BERITA KAMI</h1>
        <p>Kabar terbaru dari dapur Kairo Ramen</p>
    </div>
</section>

{{-- FEATURED + LIST --}}
<section class="news-page" style="background:#f7f7f7;padding-bottom:40px;">
    @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    @if($news->count())
        @php $featured = $news->first(); $others = $news->skip(1); @endphp

        <div class="news-feature">
            <div>
                <img
                    src="{{ $featured->image ? asset('storage/' . $featured->image) : 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=900&q=80' }}"
                    alt="{{ $featured->title }}"
                >
            </div>
            <div>
                <p class="news-date">{{ $featured->created_at->format('d M Y') }}</p>
                <h2>{{ strtoupper($featured->title) }}</h2>
                <p>{{ Str::limit($featured->content, 280) }}</p>
                <a href="{{ url('/berita/' . $featured->id) }}" class="btn-primary">BACA SELENGKAPNYA</a>
            </div>
        </div>
    @else
        <p class="empty-state">Belum ada berita.</p>
    @endif
</section>

{{-- BERITA LAINNYA --}}
@if(isset($others) && $others->count())
<section class="news-page" style="background:#fff;padding-top:70px;">
    <div class="section-heading" style="text-align:left;max-width:1250px;margin:0 auto 35px;">
        <h2 style="font-size:24px;">BERITA LAINNYA</h2>
    </div>
    <div class="news-grid cols-4">
        @foreach($others as $item)
            <article class="news-card">
                <img
                    src="{{ $item->image ? asset('storage/' . $item->image) : 'https://images.unsplash.com/photo-1553621042-f6e147245754?auto=format&fit=crop&w=800&q=80' }}"
                    alt="{{ $item->title }}"
                >
                <div class="news-card-content">
                    <p class="news-date">{{ $item->created_at->format('d M Y') }}</p>
                    <h3>{{ strtoupper(Str::limit($item->title, 40)) }}</h3>
                    <p>{{ Str::limit($item->content, 90) }}</p>
                    <div class="card-foot">
                        <a class="read-more" href="{{ url('/berita/' . $item->id) }}">Baca selengkapnya</a>
                        <span class="dots">...</span>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endif

@endsection
