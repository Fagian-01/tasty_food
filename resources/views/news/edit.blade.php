@extends('layouts.app')

@section('title', 'Edit Berita - Tasty Food')

@section('content')

<section class="page-hero" style="min-height:300px;">
    <div>
        <h1>EDIT BERITA</h1>
        <p>Perbarui kabar Tasty Food</p>
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

        <form action="{{ url('/berita/' . $news->id) }}" method="POST" enctype="multipart/form-data">
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
            <button type="submit" class="btn-primary" style="width:100%;">UPDATE BERITA</button>
        </form>
    </div>
</section>

@endsection
