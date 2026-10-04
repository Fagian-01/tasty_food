@extends('layouts.app')

@section('title', 'Kontak Kami - Kairo Ramen')

@section('content')

{{-- HERO --}}
<section class="page-hero">
    <div>
        <h1>KONTAK KAMI</h1>
        <p>Reservasi, pertanyaan menu, atau sekadar menyapa</p>
    </div>
</section>

{{-- FORM --}}
<section class="contact-form-sec">
    <div style="max-width:1100px;margin:0 auto;">
        <h2>KONTAK KAMI</h2>

        @if(session('success'))
            <div class="success-message" style="margin:0 0 24px;">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="error-box">
                <ul>
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/kontak') }}" method="POST">
            @csrf
            <div class="contact-form-grid">
                <div class="stack">
                    <input type="text" name="subject" placeholder="Subject" value="{{ old('subject') }}" required>
                    <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" required>
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                </div>
                <textarea name="message" placeholder="Message" required>{{ old('message') }}</textarea>
            </div>
            <div class="full-btn">
                <button type="submit" class="btn-primary">KIRIM</button>
            </div>
        </form>

        <div class="info-trio">
            <div>
                <div class="ico">&#9993;</div>
                <h3>EMAIL</h3>
                <p>kairoramen@gmail.com</p>
            </div>
            <div>
                <div class="ico">&#9742;</div>
                <h3>PHONE</h3>
                <p>+62 812 3456 7890</p>
            </div>
            <div>
                <div class="ico">&#9673;</div>
                <h3>LOCATION</h3>
                <p>Kota Bandung, Jawa Barat</p>
            </div>
        </div>
    </div>
</section>

{{-- MAP --}}
<section class="map-sec">
    <iframe
        title="Lokasi Kairo Ramen"
        src="https://www.google.com/maps?q=Bandung,Jawa+Barat&output=embed"
        loading="lazy"
    ></iframe>
</section>

{{-- PESAN MASUK (admin list, CRUD tetap) --}}
<section class="msg-sec">
    <div style="max-width:1100px;margin:0 auto 30px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
        <h2 style="font-size:24px;font-weight:900;">PESAN MASUK</h2>
        <a href="{{ url('/kontak/create') }}" class="btn-primary">+ TAMBAH PESAN</a>
    </div>

    <div class="msg-list">
        @forelse($contacts as $contact)
            <article class="msg-card">
                <h3>{{ $contact->subject }}</h3>
                <p class="meta">{{ $contact->name }} &middot; {{ $contact->email }} &middot; {{ $contact->created_at->format('d M Y') }}</p>
                <p class="body">{{ Str::limit($contact->message, 150) }}</p>
                <div class="crud-actions">
                    <a href="{{ url('/kontak/' . $contact->id) }}">LIHAT</a>
                    <a href="{{ url('/kontak/' . $contact->id . '/edit') }}">EDIT</a>
                    <form action="{{ url('/kontak/' . $contact->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="danger">HAPUS</button>
                    </form>
                </div>
            </article>
        @empty
            <p class="empty-state">Belum ada pesan masuk.</p>
        @endforelse
    </div>
</section>

@endsection
