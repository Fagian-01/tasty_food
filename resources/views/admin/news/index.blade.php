@extends('layouts.admin')

@section('title', 'Kelola Berita - Kairo Ramen')

@section('content')
<div class="admin-head admin-head-row">
    <div>
        <p class="section-label">BERITA</p>
        <h1>Kelola Berita</h1>
    </div>
    <a href="{{ route('admin.berita.create') }}" class="btn-primary">+ TAMBAH BERITA</a>
</div>

<div class="admin-table-card">
    @if($news->count())
        <div class="table-scroll">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($news as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->title }}</strong><br>
                                <small class="admin-muted">{{ $item->slug }}</small>
                            </td>
                            <td>{{ $item->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ url('/berita/' . $item->id) }}" target="_blank" rel="noopener" class="row-link">Lihat</a>
                                    <a href="{{ route('admin.berita.edit', $item) }}" class="row-link">Edit</a>
                                    <form action="{{ route('admin.berita.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus berita ini?')">
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
        <p class="empty-state">Belum ada berita. <a href="{{ route('admin.berita.create') }}" class="stat-link">Tambah sekarang &rarr;</a></p>
    @endif
</div>
@endsection
