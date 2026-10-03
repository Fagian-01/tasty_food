@extends('layouts.app')

@section('title', 'Berita - Tasty Food')

@section('content')

<section class="news-page">
    <div class="section-heading">
        <p class="section-label">BERITA</p>
        <h1>BERITA TERBARU</h1>
    </div>

    @if($news->count())
        <div class="news-grid">
            @foreach($news as $item)
                <article class="news-card">
                    @if($item->image)
                        <img
                            src="{{ asset('storage/' . $item->image) }}"
                            alt="{{ $item->title }}"
                        >
                    @endif

                    <div class="news-card-content">
                        <h3>{{ $item->title }}</h3>

                        <p>
                            {{ Str::limit($item->content, 120) }}
                        </p>

                        <a href="{{ url('/berita/' . $item->id) }}">
                            BACA SELENGKAPNYA →
                        </a>
                        <div class="news-actions">
                            <a href="{{ url('/berita/' . $item->id . '/edit') }}">
                                EDIT
                            </a>

                            <form
                                action="{{ url('/berita/' . $item->id) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus berita ini?')"
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
            Belum ada berita.
        </p>
    @endif
</section>

@endsection