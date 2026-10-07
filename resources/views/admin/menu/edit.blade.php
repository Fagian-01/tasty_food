@extends('layouts.admin')

@section('title', 'Edit Menu - Kairo Ramen')

@section('content')
<div class="admin-head">
    <p class="section-label">MENU</p>
    <h1>Edit Menu</h1>
    <p class="admin-muted">Perbarui menu {{ $menu->name }}.</p>
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

    <form action="{{ route('admin.menu.update', $menu) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nama Menu</label>
            <input type="text" id="name" name="name" value="{{ old('name', $menu->name) }}" required>
        </div>
        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug', $menu->slug) }}" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="price">Harga (Rp)</label>
                <input type="number" id="price" name="price" min="0" max="1000000000" step="500" value="{{ old('price', $menu->price) }}" required>
            </div>
            <div class="form-group">
                <label for="category">Kategori</label>
                <input type="text" id="category" name="category" value="{{ old('category', $menu->category) }}">
            </div>
        </div>
        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="5">{{ old('description', $menu->description) }}</textarea>
        </div>
        <div class="form-group">
            <label for="image">Ganti Gambar</label>
            @if($menu->image)
                <img class="form-preview" src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}">
            @endif
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <div class="form-check">
            <input type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', $menu->is_available) ? 'checked' : '' }}>
            <label for="is_available">Menu tersedia (tampil di Home)</label>
        </div>
        <div class="form-check">
            <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $menu->is_featured) ? 'checked' : '' }}>
            <label for="is_featured">Tampilkan di Signature Menu (Home)</label>
        </div>
        <div class="admin-form-actions">
            <a href="{{ route('admin.menu.index') }}" class="btn-ghost">Batal</a>
            <button type="submit" class="btn-primary">UPDATE MENU</button>
        </div>
    </form>
</div>
@endsection
