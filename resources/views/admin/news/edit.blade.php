@extends('layouts.admin')

@section('title', 'Edit Berita - Kairo Ramen')

@section('content')
<div class="admin-head">
    <p class="section-label">BERITA</p>
    <h1>Edit Berita</h1>
    <p class="admin-muted">Perbarui kabar Kairo Ramen.</p>
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

    <form action="{{ route('admin.berita.update', $news) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="title">Judul Berita</label>
            <input type="text" id="title" name="title" value="{{ old('title', $news->title) }}" required>
        </div>
        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug', $news->slug) }}" required>
        </div>
        <div class="form-group">
            <label for="image">Ganti Gambar</label>
            @if($news->image)
                <img class="form-preview" src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}">
            @endif
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <div class="form-group">
            <label for="content">Isi Berita</label>
            <textarea id="content" name="content" rows="8" required>{{ old('content', $news->content) }}</textarea>
        </div>
        <div class="admin-form-actions">
            <a href="{{ route('admin.berita.index') }}" class="btn-ghost">Batal</a>
            <button type="submit" class="btn-primary">UPDATE BERITA</button>
        </div>
    </form>
</div>
@endsection
