@extends('layouts.app')

@section('title', $gallery->title . ' - Kairo Ramen')

@section('content')

<section class="page-hero" style="min-height:300px;">
    <div>
        <h1 style="font-size:clamp(28px,3.5vw,44px);">{{ strtoupper(Str::limit($gallery->title, 50)) }}</h1>
        <p>{{ $gallery->created_at->format('d M Y') }}</p>
    </div>
</section>

<section class="detail-wrap">
    <p class="section-label">GALERI</p>
    <h1>{{ $gallery->title }}</h1>
    <p class="detail-meta">{{ $gallery->created_at->format('d M Y') }}</p>

    <img class="detail-img detail-img--gallery" src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}">

    @if($gallery->description)
        <div class="detail-body">
            <p>{{ $gallery->description }}</p>
        </div>
    @endif

    <div class="back-row">
        <a href="{{ url('/galeri') }}" class="btn-primary">&larr; KEMBALI KE GALERI</a>
        <a href="{{ url('/galeri/' . $gallery->id . '/edit') }}" class="btn-primary" style="background:#fff;color:#111;border:1px solid #ddd;">EDIT</a>
    </div>
</section>

@endsection
