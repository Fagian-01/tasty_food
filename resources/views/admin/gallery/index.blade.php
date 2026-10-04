@extends('layouts.admin')

@section('title', 'Kelola Galeri - Kairo Ramen')

@section('content')
<div class="admin-head admin-head-row">
    <div>
        <p class="section-label">GALERI</p>
        <h1>Kelola Galeri</h1>
    </div>
    <a href="{{ route('admin.galeri.create') }}" class="btn-primary">+ TAMBAH GALERI</a>
</div>

<div class="admin-table-card">
    @if($galleries->count())
        <div class="table-scroll">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($galleries as $gallery)
                        <tr>
                            <td><img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}" class="admin-thumb"></td>
                            <td>
                                <strong>{{ $gallery->title }}</strong>
                                @if($gallery->description)
                                    <br><small class="admin-muted">{{ \Str::limit($gallery->description, 80) }}</small>
                                @endif
                            </td>
                            <td>{{ $gallery->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ url('/galeri/' . $gallery->id) }}" target="_blank" rel="noopener" class="row-link">Lihat</a>
                                    <a href="{{ route('admin.galeri.edit', $gallery) }}" class="row-link">Edit</a>
                                    <form action="{{ route('admin.galeri.destroy', $gallery) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus galeri ini?')">
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
        <p class="empty-state">Belum ada galeri. <a href="{{ route('admin.galeri.create') }}" class="stat-link">Tambah sekarang &rarr;</a></p>
    @endif
</div>
@endsection
