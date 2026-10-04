@extends('layouts.app')

@section('title', $contact->subject . ' - Kairo Ramen')

@section('content')

<section class="page-hero" style="min-height:300px;">
    <div>
        <h1>DETAIL PESAN</h1>
        <p>{{ $contact->created_at->format('d M Y') }}</p>
    </div>
</section>

<section class="detail-wrap">
    <p class="section-label">KONTAK</p>
    <h1>{{ $contact->subject }}</h1>
    <p class="detail-meta">{{ $contact->name }} &middot; {{ $contact->email }} &middot; {{ $contact->created_at->format('d M Y') }}</p>

    <div class="detail-body">
        <p>{{ $contact->message }}</p>
    </div>

    <div class="back-row">
        <a href="{{ url('/kontak') }}" class="btn-primary">&larr; KEMBALI KE KONTAK</a>
        <a href="{{ url('/kontak/' . $contact->id . '/edit') }}" class="btn-primary" style="background:#fff;color:#111;border:1px solid #ddd;">EDIT</a>
    </div>
</section>

@endsection
