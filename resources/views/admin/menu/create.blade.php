@extends('layouts.admin')

@section('title', 'Tambah Menu - Kairo Ramen')

@section('content')
<div class="admin-head">
    <p class="section-label">MENU</p>
    <h1>Tambah Menu</h1>
    <p class="admin-muted">Tambahkan menu baru Kairo Ramen.</p>
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

    <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="name">Nama Menu</label>
            <input type="text" id="name" name="name" placeholder="Shoyu Ramen" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" placeholder="shoyu-ramen" value="{{ old('slug') }}" required>
            <p class="hint">Huruf kecil, tanpa spasi. Contoh: spicy-miso-ramen</p>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="price">Harga (Rp)</label>
                <input type="number" id="price" name="price" min="0" max="1000000000" step="500" placeholder="38000" value="{{ old('price') }}" required>
            </div>
            <div class="form-group">
                <label for="category">Kategori</label>
                <input type="text" id="category" name="category" placeholder="Ramen / Donburi / Sushi / Side" value="{{ old('category') }}">
            </div>
        </div>
        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="5" placeholder="Deskripsi singkat menu...">{{ old('description') }}</textarea>
        </div>
        <div class="form-group">
            <label for="image">Gambar</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <div class="form-check">
            <input type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}>
            <label for="is_available">Menu tersedia (tampil di Home)</label>
        </div>
        <div class="form-check">
            <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
            <label for="is_featured">Tampilkan di Signature Menu (Home)</label>
        </div>
        <div class="admin-form-actions">
            <a href="{{ route('admin.menu.index') }}" class="btn-ghost">Batal</a>
            <button type="submit" class="btn-primary">SIMPAN MENU</button>
        </div>
    </form>
</div>
@endsection
