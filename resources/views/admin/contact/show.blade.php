@extends('layouts.admin')

@section('title', 'Detail Pesan - Kairo Ramen')

@section('content')
<div class="admin-head">
    <p class="section-label">KONTAK</p>
    <h1>{{ $contact->subject }}</h1>
    <p class="admin-muted">{{ $contact->name }} &middot; {{ $contact->email }} &middot; {{ $contact->created_at->format('d M Y H:i') }}</p>
</div>

<div class="admin-table-card admin-detail">
    <p class="admin-detail-label">Isi Pesan</p>
    <p class="admin-detail-body">{{ $contact->message }}</p>
    <div class="admin-form-actions" style="margin-top:28px;">
        <a href="{{ route('admin.kontak.index') }}" class="btn-ghost">&larr; Kembali</a>
        <span style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('admin.kontak.edit', $contact) }}" class="btn-ghost">Edit</a>
            <form action="{{ route('admin.kontak.destroy', $contact) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="row-danger" style="padding:12px 22px;">Hapus</button>
            </form>
        </span>
    </div>
</div>
@endsection
