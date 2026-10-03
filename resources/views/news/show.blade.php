@extends('layouts.app')

@section('title', $news->title . ' - Tasty Food')

@section('content')

<section class="news-detail">

    <div class="news-detail-header">
        <p class="section-label">BERITA</p>

        <h1>{{ $news->title }}</h1>
    </div>

    @if($news->image)
        <div class="news-detail-image">
            <img
                src="{{ asset('storage/' . $news->image) }}"
                alt="{{ $news->title }}"
            >
        </div>
    @endif

    <div class="news-detail-content">
        <p>{{ $news->content }}</p>
    </div>

    <a href="{{ url('/berita') }}" class="btn-primary">
        ← KEMBALI KE BERITA
    </a>

</section>

@endsection