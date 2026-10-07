@extends('layouts.app')

@section('title', 'Kirim Pesan - Kairo Ramen')

@section('content')

<section class="page-hero">
    <div>
        <h1>KONTAK KAMI</h1>
        <p>Kirim pesan kepada kami</p>
    </div>
</section>

<section class="contact-form-sec">
    <div style="max-width:1100px;margin:0 auto;">
        <div class="back-row" style="margin:0 0 24px;">
            <a href="{{ url('/') }}" class="btn-primary">&larr; KEMBALI KE BERANDA</a>
        </div>

        <h2>KIRIM PESAN</h2>

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

        @if(session('success'))
            <div class="success-message" style="margin:24px auto 0;max-width:1100px;text-align:center;">{{ session('success') }}</div>
        @endif

        <h2 style="text-align:center;margin-top:80px;">HUBUNGI KAMI</h2>
        <p style="text-align:center;color:var(--muted);margin:-24px 0 36px;font-size:14px;">Kunjungi kedai kami atau kirim pesan melalui form di atas.</p>

        <div class="contact-info-grid">
            <div class="contact-info-card">
                <span class="info-ico">&#9993;</span>
                <h3>EMAIL</h3>
                <a href="mailto:hello@kairoramen.test">hello@kairoramen.test</a>
            </div>
            <div class="contact-info-card">
                <span class="info-ico">&#9742;</span>
                <h3>PHONE</h3>
                <a href="tel:+628****7890">+62 812-3456-7890</a>
            </div>
            <div class="contact-info-card">
                <span class="info-ico">&#8982;</span>
                <h3>LOCATION</h3>
                <p>Jl. Ir. H. Juanda No. 88<br>Cianjur, Jawa Barat</p>
            </div>
        </div>

        <div class="map-wrap">
            <iframe
                title="Peta lokasi Kairo Ramen"
                src="https://www.google.com/maps?q=Alun-alun+Cianjur,+Jawa+Barat&output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen></iframe>
        </div>
    </div>
</section>

@endsection
