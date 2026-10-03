@extends('layouts.app')

@section('title', 'Tambah Kontak - Tasty Food')

@section('content')

<section class="form-section">

    <div class="section-heading">
        <p class="section-label">KONTAK</p>
        <h1>KIRIM PESAN</h1>
    </div>

    <form
        action="{{ url('/kontak') }}"
        method="POST"
    >
        @csrf

        <div class="form-group">
            <label for="name">Nama</label>

            <input
                type="text"
                id="name"
                name="name"
                placeholder="Masukkan nama"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="contoh@email.com"
                required
            >
        </div>

        <div class="form-group">
            <label for="subject">Subjek</label>

            <input
                type="text"
                id="subject"
                name="subject"
                placeholder="Masukkan subjek"
                required
            >
        </div>

        <div class="form-group">
            <label for="message">Pesan</label>

            <textarea
                id="message"
                name="message"
                rows="8"
                placeholder="Tulis pesan..."
                required
            ></textarea>
        </div>

        <button type="submit" class="btn-primary">
            KIRIM PESAN
        </button>

    </form>

</section>

@endsection