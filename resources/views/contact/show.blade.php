@extends('layouts.app')

@section('title', $contact->subject . ' - Tasty Food')

@section('content')

<section class="news-detail">

    <div class="news-detail-header">

        <p class="section-label">KONTAK</p>

        <h1>{{ $contact->subject }}</h1>

    </div>

    <div class="news-detail-content">

        <p>
            <strong>Nama:</strong>
            {{ $contact->name }}
        </p>

        <p>
            <strong>Email:</strong>
            {{ $contact->email }}
        </p>

        <p>
            <strong>Pesan:</strong>
        </p>

        <p>{{ $contact->message }}</p>

    </div>

    <a href="{{ url('/kontak') }}" class="btn-primary">
        ← KEMBALI KE KONTAK
    </a>

</section>

@endsection