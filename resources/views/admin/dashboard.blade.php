@extends('layouts.admin')

@section('title', 'Dashboard - Kairo Ramen')

@section('content')
<div class="admin-head">
    <div>
        <p class="section-label">DASHBOARD</p>
        <h1>Selamat datang, {{ auth()->user()->name ?? 'Admin' }}</h1>
        <p class="admin-muted">Kelola berita, galeri, dan pesan masuk Kairo Ramen dari satu tempat.</p>
    </div>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <p class="stat-num">{{ $newsCount }}</p>
        <p class="stat-label">Total Berita</p>
        <a href="{{ route('admin.berita.index') }}" class="stat-link">Kelola Berita &rarr;</a>
    </div>
    <div class="stat-card">
        <p class="stat-num">{{ $galleryCount }}</p>
        <p class="stat-label">Total Galeri</p>
        <a href="{{ route('admin.galeri.index') }}" class="stat-link">Kelola Galeri &rarr;</a>
    </div>
    <div class="stat-card">
        <p class="stat-num">{{ $contactCount }}</p>
        <p class="stat-label">Pesan Masuk</p>
        <a href="{{ route('admin.kontak.index') }}" class="stat-link">Lihat Pesan &rarr;</a>
    </div>
</div>

<div class="quick-grid">
    <a href="{{ route('admin.berita.create') }}" class="quick-card">
        <span class="quick-ico">+</span>
        <strong>Tambah Berita</strong>
        <small>Publikasikan kabar terbaru resto</small>
    </a>
    <a href="{{ route('admin.galeri.create') }}" class="quick-card">
        <span class="quick-ico">+</span>
        <strong>Tambah Galeri</strong>
        <small>Unggah dokumentasi menu &amp; resto</small>
    </a>
    <a href="{{ route('admin.kontak.index') }}" class="quick-card">
        <span class="quick-ico">&#9993;</span>
        <strong>Lihat Pesan</strong>
        <small>Balas pesan dari pengunjung</small>
    </a>
</div>

<div class="admin-table-card">
    <div class="admin-table-head">
        <h2>Pesan Terbaru</h2>
        <a href="{{ route('admin.kontak.index') }}" class="stat-link">Semua pesan &rarr;</a>
    </div>
    @if($latestMessages->count())
        <div class="table-scroll">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestMessages as $msg)
                        <tr>
                            <td><strong>{{ $msg->subject }}</strong><br><small class="admin-muted">{{ \Str::limit($msg->message, 60) }}</small></td>
                            <td>{{ $msg->name }}<br><small class="admin-muted">{{ $msg->email }}</small></td>
                            <td>{{ $msg->created_at->format('d M Y') }}</td>
                            <td><a href="{{ route('admin.kontak.show', $msg) }}" class="row-link">Lihat</a></td>
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
