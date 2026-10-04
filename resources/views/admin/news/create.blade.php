@extends('layouts.admin')

@section('title', 'Tambah Berita - Kairo Ramen')

@section('content')
<div class="admin-head">
    <p class="section-label">BERITA</p>
    <h1>Tambah Berita</h1>
    <p class="admin-muted">Publikasikan kabar terbaru Kairo Ramen.</p>
</div>

<div class="form-card admin-form">
    @if($errors->any())
        <div class="error-box">
            <ul>
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="title">Judul Berita</label>
            <input type="text" id="title" name="title" placeholder="Masukkan judul berita" value="{{ old('title') }}" required>
        </div>
        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" placeholder="contoh-berita-makanan" value="{{ old('slug') }}" required>
        </div>
        <div class="form-group">
            <label for="image">Gambar</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <div class="form-group">
            <label for="content">Isi Berita</label>
            <textarea id="content" name="content" rows="8" placeholder="Tulis isi berita..." required>{{ old('content') }}</textarea>
        </div>
        <div class="admin-form-actions">
            <a href="{{ route('admin.berita.index') }}" class="btn-ghost">Batal</a>
            <button type="submit" class="btn-primary">SIMPAN BERITA</button>
        </div>
    </form>
</div>
@endsection
