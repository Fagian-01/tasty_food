@extends('layouts.app')

@section('title', 'Edit Galeri - Tasty Food')

@section('content')

<section class="page-hero" style="min-height:300px;">
    <div>
        <h1>EDIT GALERI</h1>
        <p>Perbarui dokumentasi Tasty Food</p>
    </div>
</section>

<section class="form-sec">
    <div class="form-card">
        @if($errors->any())
            <div class="error-box">
                <ul>
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/galeri/' . $gallery->id) }}" method="POST" enctype="multipart/form-data">
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
            <button type="submit" class="btn-primary" style="width:100%;">UPDATE GALERI</button>
        </form>
    </div>
</section>

@endsection
