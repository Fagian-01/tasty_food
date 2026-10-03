@extends('layouts.app')

@section('title', 'Tambah Galeri - Tasty Food')

@section('content')

<section class="form-section">

    <div class="section-heading">
        <p class="section-label">GALERI</p>
        <h1>TAMBAH GALERI</h1>
    </div>

    <form
        action="{{ url('/galeri') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        <div class="form-group">
            <label for="title">Judul Galeri</label>

            <input
                type="text"
                id="title"
                name="title"
                placeholder="Masukkan judul galeri"
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
                required
            >
        </div>

        <div class="form-group">
            <label for="description">Deskripsi</label>

            <textarea
                id="description"
                name="description"
                rows="6"
                placeholder="Masukkan deskripsi gambar..."
            ></textarea>
        </div>

        <button type="submit" class="btn-primary">
            SIMPAN GALERI
        </button>

    </form>

</section>

@endsection