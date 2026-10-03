@extends('layouts.app')

@section('title', 'Galeri - Tasty Food')

@section('content')

<section class="gallery-section">

    <div class="section-heading">
        <p class="section-label">GALERI</p>
        <h1>GALERI KAMI</h1>
    </div>

    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <div class="gallery-admin-button">
        <a href="{{ url('/galeri/create') }}" class="btn-primary">
            + TAMBAH GALERI
        </a>
    </div>

    @if($galleries->count())

        <div class="gallery-grid">

            @foreach($galleries as $gallery)

                <article class="gallery-card">

                    <img
                        src="{{ asset('storage/' . $gallery->image) }}"
                        alt="{{ $gallery->title }}"
                    >

                    <div class="gallery-card-content">

                        <h3>{{ $gallery->title }}</h3>

                        @if($gallery->description)
                            <p>
                                {{ $gallery->description }}
                            </p>
                        @endif

                        <a href="{{ url('/galeri/' . $gallery->id) }}">
                            LIHAT DETAIL →
                        </a>

                        <div class="gallery-actions">

                            <a href="{{ url('/galeri/' . $gallery->id . '/edit') }}">
                                EDIT
                            </a>

                            <form
                                action="{{ url('/galeri/' . $gallery->id) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus galeri ini?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit">
                                    HAPUS
                                </button>
                            </form>

                        </div>

                    </div>

                </article>

            @endforeach

        </div>

    @else

        <p class="empty-news">
            Belum ada galeri.
        </p>

    @endif

</section>

@endsection