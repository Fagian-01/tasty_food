@extends('layouts.admin')

@section('title', 'Edit Pesan - Kairo Ramen')

@section('content')
<div class="admin-head">
    <p class="section-label">KONTAK</p>
    <h1>Edit Pesan</h1>
    <p class="admin-muted">Perbarui pesan masuk dari pengunjung.</p>
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

    <form action="{{ route('admin.kontak.update', $contact) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" value="{{ old('subject', $contact->subject) }}" required>
        </div>
        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" id="name" name="name" value="{{ old('name', $contact->name) }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $contact->email) }}" required>
        </div>
        <div class="form-group">
            <label for="message">Pesan</label>
            <textarea id="message" name="message" rows="6" required>{{ old('message', $contact->message) }}</textarea>
        </div>
        <div class="admin-form-actions">
            <a href="{{ route('admin.kontak.index') }}" class="btn-ghost">Batal</a>
            <button type="submit" class="btn-primary">UPDATE PESAN</button>
        </div>
    </form>
</div>
@endsection
