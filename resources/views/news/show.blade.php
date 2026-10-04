@extends('layouts.app')

@section('title', $news->title . ' - Kairo Ramen')

@section('content')

<section class="page-hero" style="min-height:300px;">
    <div>
        <h1 style="font-size:clamp(28px,3.5vw,44px);">{{ strtoupper(Str::limit($news->title, 50)) }}</h1>
        <p>{{ $news->created_at->format('d M Y') }}</p>
    </div>
</section>

<section class="detail-wrap">
    <p class="section-label">BERITA</p>
    <h1>{{ $news->title }}</h1>
    <p class="detail-meta">{{ $news->created_at->format('d M Y') }}</p>

    @if($news->image)
        <img class="detail-img" src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}">
    @endif

    <div class="detail-body">
        @foreach(preg_split('/\n\s*\n/', $news->content) as $para)
            <p>{{ trim($para) }}</p>
        @endforeach
    </div>

    <div class="back-row">
        <a href="{{ url('/berita') }}" class="btn-primary">&larr; KEMBALI KE BERITA</a>
        <a href="{{ url('/berita/' . $news->id . '/edit') }}" class="btn-primary" style="background:#fff;color:#111;border:1px solid #ddd;">EDIT</a>
    </div>
</section>

@endsection
