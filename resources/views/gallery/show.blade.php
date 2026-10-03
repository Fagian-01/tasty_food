@extends('layouts.app')

@section('title', $gallery->title . ' - Tasty Food')

@section('content')

<section class="news-detail">

    <div class="news-detail-header">

        <p class="section-label">GALERI</p>

        <h1>{{ $gallery->title }}</h1>

    </div>

    <div class="news-detail-image">

        <img
            src="{{ asset('storage/' . $gallery->image) }}"
            alt="{{ $gallery->title }}"
        >

    </div>

    @if($gallery->description)

        <div class="news-detail-content">

            <p>{{ $gallery->description }}</p>

        </div>

    @endif

    <a href="{{ url('/galeri') }}" class="btn-primary">
        ← KEMBALI KE GALERI
    </a>

</section>

@endsection