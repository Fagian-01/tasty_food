@extends('layouts.app')

@section('title', 'Tambah Galeri - Tasty Food')

@section('content')

<section class="page-hero" style="min-height:300px;">
    <div>
        <h1>TAMBAH GALERI</h1>
        <p>Tambah dokumentasi Tasty Food</p>
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

        <form action="{{ url('/galeri') }}" method="POST" enctype="multipart/form-data">
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
            <button type="submit" class="btn-primary" style="width:100%;">SIMPAN GALERI</button>
        </form>
    </div>
</section>

@endsection
