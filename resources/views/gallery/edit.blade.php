@extends('layouts.app')

@section('title', 'Edit Galeri - Tasty Food')

@section('content')

<section class="form-section">

    <div class="section-heading">
        <p class="section-label">GALERI</p>
        <h1>EDIT GALERI</h1>
    </div>

    <form
        action="{{ url('/galeri/' . $gallery->id) }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Judul Galeri</label>

            <input
                type="text"
                id="title"
                name="title"
                value="{{ $gallery->title }}"
                required
            >
        </div>

        <div class="form-group">

            <label>Gambar Saat Ini</label>

            <img
                src="{{ asset('storage/' . $gallery->image) }}"
                alt="{{ $gallery->title }}"
                style="width: 250px; display: block; margin-bottom: 15px;"
            >

            <label for="image">Ganti Gambar</label>

            <input
                type="file"
                id="image"
                name="image"
                accept="image/*"
            >

        </div>

        <div class="form-group">
            <label for="description">Deskripsi</label>

            <textarea
                id="description"
                name="description"
                rows="6"
            >{{ $gallery->description }}</textarea>
        </div>

        <button type="submit" class="btn-primary">
            UPDATE GALERI
        </button>

    </form>

</section>

@endsection