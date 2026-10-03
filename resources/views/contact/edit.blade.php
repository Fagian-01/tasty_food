@extends('layouts.app')

@section('title', 'Edit Kontak - Tasty Food')

@section('content')

<section class="form-section">

    <div class="section-heading">
        <p class="section-label">KONTAK</p>
        <h1>EDIT PESAN</h1>
    </div>

    <form
        action="{{ url('/kontak/' . $contact->id) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nama</label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ $contact->name }}"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                value="{{ $contact->email }}"
                required
            >
        </div>

        <div class="form-group">
            <label for="subject">Subjek</label>

            <input
                type="text"
                id="subject"
                name="subject"
                value="{{ $contact->subject }}"
                required
            >
        </div>

        <div class="form-group">
            <label for="message">Pesan</label>

            <textarea
                id="message"
                name="message"
                rows="8"
                required
            >{{ $contact->message }}</textarea>
        </div>

        <button type="submit" class="btn-primary">
            UPDATE PESAN
        </button>

    </form>

</section>

@endsection