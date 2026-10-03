@extends('layouts.app')

@section('title', 'Tambah Berita - Tasty Food')

@section('content')

<section class="form-section">

    <div class="section-heading">
        <p class="section-label">BERITA</p>
        <h1>TAMBAH BERITA</h1>
    </div>

    <form action="{{ url('/berita') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="title">Judul Berita</label>
            <input
                type="text"
                id="title"
                name="title"
                placeholder="Masukkan judul berita"
                required
            >
        </div>

        <div class="form-group">
            <label for="slug">Slug</label>
            <input
                type="text"
                id="slug"
                name="slug"
                placeholder="contoh-berita-makanan"
                required
            >
        </div>

        <div class="form-group">
            <label for="image">Gambar</label>
            <input
                type="file"
                id="image"
                name="image"
                accept="image/*"
            >
        </div>

        <div class="form-group">
            <label for="content">Isi Berita</label>
            <textarea
                id="content"
                name="content"
                rows="8"
                placeholder="Tulis isi berita..."
                required
            ></textarea>
        </div>

        <button type="submit" class="btn-primary">
            SIMPAN BERITA
        </button>
    </form>

</section>

@endsection