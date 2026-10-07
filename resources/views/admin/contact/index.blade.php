@extends('layouts.admin')

@section('title', 'Pesan Masuk - Kairo Ramen')

@section('content')
<div class="admin-head">
    <p class="section-label">KONTAK</p>
    <h1>Pesan Masuk ({{ $contacts->count() }})</h1>
    <p class="admin-muted">Pesan yang dikirim pengunjung melalui halaman kontak.</p>
</div>

<div class="admin-table-card">
    @if($contacts->count())
        <div class="table-scroll">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Pengirim</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contacts as $contact)
                        <tr>
                            <td>
                                <strong>{{ $contact->subject }}</strong><br>
                                <small class="admin-muted">{{ \Str::limit($contact->message, 70) }}</small>
                            </td>
                            <td>{{ $contact->name }}<br><small class="admin-muted">{{ $contact->email }}</small></td>
                            <td><span class="status-badge status-{{ $contact->status ?? 'unread' }}">{{ $contact->statusLabel() }}</span></td>
                            <td>{{ $contact->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.kontak.show', $contact) }}" class="row-link">Lihat</a>
                                    <a href="{{ route('admin.kontak.edit', $contact) }}" class="row-link">Edit</a>
                                    <form action="{{ route('admin.kontak.destroy', $contact) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="row-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="empty-state">Belum ada pesan masuk.</p>
    @endif
</div>
@endsection
