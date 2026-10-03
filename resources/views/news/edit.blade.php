@extends('layouts.app')

@section('title', 'Edit Berita - Tasty Food')

@section('content')

<section class="form-section">

    <div class="section-heading">
        <p class="section-label">BERITA</p>
        <h1>EDIT BERITA</h1>
    </div>

    <form
        action="{{ url('/berita/' . $news->id) }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Judul Berita</label>

            <input
                type="text"
                id="title"
                name="title"
                value="{{ $news->title }}"
                required
            >
        </div>

        <div class="form-group">
            <label for="slug">Slug</label>

            <input
                type="text"
                id="slug"
                name="slug"
                value="{{ $news->slug }}"
                required
            >
        </div>

        <div class="form-group">
            <label for="image">Ganti Gambar</label>

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
                required
            >{{ $news->content }}</textarea>
        </div>

        <button type="submit" class="btn-primary">
            UPDATE BERITA
        </button>

    </form>

</section>

@endsection