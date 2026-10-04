@extends('layouts.admin')

@section('title', 'Edit Galeri - Kairo Ramen')

@section('content')
<div class="admin-head">
    <p class="section-label">GALERI</p>
    <h1>Edit Galeri</h1>
    <p class="admin-muted">Perbarui dokumentasi Kairo Ramen.</p>
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

    <form action="{{ route('admin.galeri.update', $gallery) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="title">Judul Galeri</label>
            <input type="text" id="title" name="title" value="{{ old('title', $gallery->title) }}" required>
        </div>
        <div class="form-group">
            <label>Gambar Saat Ini</label>
            <img class="form-preview" src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}">
            <label for="image" style="margin-top:14px;">Ganti Gambar</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="6">{{ old('description', $gallery->description) }}</textarea>
        </div>
        <div class="admin-form-actions">
            <a href="{{ route('admin.galeri.index') }}" class="btn-ghost">Batal</a>
            <button type="submit" class="btn-primary">UPDATE GALERI</button>
        </div>
    </form>
</div>
@endsection
