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

{{-- PHOTO GRID (foto saja, klik untuk detail) --}}
<section class="gallery-section" style="padding-top:70px;">
    @if($galleries->count())
        <div class="photo-grid">
            @foreach($galleries as $gallery)
                <a href="{{ url('/galeri/' . $gallery->id) }}" class="photo-tile" title="{{ $gallery->title }}">
                    <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}" loading="lazy">
                </a>
            @endforeach
        </div>
    @else
        <p class="empty-state">Belum ada galeri.</p>
    @endif
</section>

@endsection
