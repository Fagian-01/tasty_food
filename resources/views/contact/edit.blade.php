@extends('layouts.app')

@section('title', 'Edit Pesan - Tasty Food')

@section('content')

<section class="page-hero" style="min-height:300px;">
    <div>
        <h1>EDIT PESAN</h1>
        <p>Perbarui pesan masuk</p>
    </div>
</section>

<section class="contact-form-sec">
    <div style="max-width:1100px;margin:0 auto;">
        <h2>EDIT PESAN</h2>

        @if($errors->any())
            <div class="error-box">
                <ul>
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/kontak/' . $contact->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="contact-form-grid">
                <div class="stack">
                    <input type="text" name="subject" placeholder="Subject" value="{{ old('subject', $contact->subject) }}" required>
                    <input type="text" name="name" placeholder="Name" value="{{ old('name', $contact->name) }}" required>
                    <input type="email" name="email" placeholder="Email" value="{{ old('email', $contact->email) }}" required>
                </div>
                <textarea name="message" placeholder="Message" required>{{ old('message', $contact->message) }}</textarea>
            </div>
            <div class="full-btn">
                <button type="submit" class="btn-primary">UPDATE PESAN</button>
            </div>
        </form>
    </div>
</section>

@endsection
