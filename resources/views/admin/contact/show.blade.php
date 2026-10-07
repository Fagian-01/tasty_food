@extends('layouts.admin')

@section('title', 'Detail Pesan - Kairo Ramen')

@section('content')
<div class="admin-head">
    <div>
        <p class="section-label">KONTAK</p>
        <h1>{{ $contact->subject }}</h1>
        <p class="admin-muted">{{ $contact->name }} &middot; {{ $contact->email }} &middot; {{ $contact->created_at->format('d M Y H:i') }}</p>
        <p style="margin-top:10px;"><span class="status-badge status-{{ $contact->status ?? 'unread' }}">{{ $contact->statusLabel() }}</span></p>
    </div>
</div>

<div class="admin-table-card admin-detail">
    <p class="admin-detail-label">Isi Pesan</p>
    <p class="admin-detail-body">{{ $contact->message }}</p>
    <div class="admin-form-actions" style="margin-top:28px;">
        <a href="{{ route('admin.kontak.index') }}" class="btn-ghost">&larr; Kembali</a>
        <span style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="mailto:{{ $contact->email }}?subject=Re: {{ urlencode($contact->subject) }}" class="btn-ghost">Balas via Email &#9993;</a>
            <a href="{{ route('admin.kontak.edit', $contact) }}" class="btn-ghost">Edit</a>
            <form action="{{ route('admin.kontak.destroy', $contact) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="row-danger" style="padding:12px 22px;">Hapus</button>
            </form>
        </span>
    </div>
</div>

<div class="form-card admin-form" style="margin-top:24px;">
    <h2 style="font-size:18px;font-weight:800;margin-bottom:6px;">Balasan Admin</h2>
    <p class="admin-muted" style="margin-bottom:20px;">
        @if($contact->replied_at)
            Dibalas pada {{ $contact->replied_at->format('d M Y H:i') }}.
        @else
            Tulis balasan untuk {{ $contact->name }}. Status pesan akan menjadi Sudah Dibalas.
        @endif
    </p>

    @if($contact->admin_reply)
        <div class="reply-box">
            <p class="admin-detail-label">Balasan Terkirim</p>
            <p class="admin-detail-body">{{ $contact->admin_reply }}</p>
        </div>
    @endif

    <form action="{{ route('admin.kontak.reply', $contact) }}" method="POST" style="margin-top:20px;">
        @csrf
        <div class="form-group">
            <label for="admin_reply">{{ $contact->admin_reply ? 'Perbarui Balasan' : 'Tulis Balasan' }}</label>
            <textarea id="admin_reply" name="admin_reply" rows="6" placeholder="Halo {{ $contact->name }}, terima kasih atas pesan Anda..." required>{{ old('admin_reply', $contact->admin_reply) }}</textarea>
        </div>
        <div class="admin-form-actions">
            <button type="submit" class="btn-primary">BALAS PESAN</button>
        </div>
    </form>
</div>
@endsection
