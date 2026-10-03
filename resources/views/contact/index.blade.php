@extends('layouts.app')

@section('title', 'Kontak - Tasty Food')

@section('content')

<section class="contact-page">

    <div class="section-heading">
        <p class="section-label">KONTAK</p>
        <h1>PESAN MASUK</h1>
    </div>

    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <div class="contact-admin-button">
        <a href="{{ url('/kontak/create') }}" class="btn-primary">
            + TAMBAH PESAN
        </a>
    </div>

    @if($contacts->count())

        <div class="contact-list">

            @foreach($contacts as $contact)

                <article class="contact-card">

                    <div class="contact-card-content">

                        <h3>{{ $contact->subject }}</h3>

                        <p>
                            <strong>Nama:</strong>
                            {{ $contact->name }}
                        </p>

                        <p>
                            <strong>Email:</strong>
                            {{ $contact->email }}
                        </p>

                        <p>
                            {{ Str::limit($contact->message, 150) }}
                        </p>

                        <div class="contact-actions">

                            <a href="{{ url('/kontak/' . $contact->id) }}">
                                LIHAT
                            </a>

                            <a href="{{ url('/kontak/' . $contact->id . '/edit') }}">
                                EDIT
                            </a>

                            <form
                                action="{{ url('/kontak/' . $contact->id) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus pesan ini?')"
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

        <p class="empty-contact">
            Belum ada pesan.
        </p>

    @endif

</section>

@endsection