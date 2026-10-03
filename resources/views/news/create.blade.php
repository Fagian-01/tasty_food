@extends('layouts.app')

@section('title', 'Tambah Berita - Tasty Food')

@section('content')

<section class="page-hero" style="min-height:300px;">
    <div>
        <h1>TAMBAH BERITA</h1>
        <p>Tambah kabar terbaru Tasty Food</p>
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

        <form action="{{ url('/berita') }}" method="POST" enctype="multipart/form-data">
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
            <button type="submit" class="btn-primary" style="width:100%;">SIMPAN BERITA</button>
        </form>
    </div>
</section>

@endsection
