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
    </div>
</section>

@endsection
