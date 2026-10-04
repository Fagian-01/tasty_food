@extends('layouts.admin')

@section('title', 'Tambah Galeri - Kairo Ramen')

@section('content')
<div class="admin-head">
    <p class="section-label">GALERI</p>
    <h1>Tambah Galeri</h1>
    <p class="admin-muted">Unggah dokumentasi menu dan suasana resto.</p>
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

    <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="title">Judul Galeri</label>
            <input type="text" id="title" name="title" placeholder="Masukkan judul galeri" value="{{ old('title') }}" required>
        </div>
        <div class="form-group">
            <label for="image">Gambar</label>
            <input type="file" id="image" name="image" accept="image/*" required>
        </div>
        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="6" placeholder="Masukkan deskripsi gambar...">{{ old('description') }}</textarea>
        </div>
        <div class="admin-form-actions">
            <a href="{{ route('admin.galeri.index') }}" class="btn-ghost">Batal</a>
            <button type="submit" class="btn-primary">SIMPAN GALERI</button>
        </div>
    </form>
</div>
@endsection
