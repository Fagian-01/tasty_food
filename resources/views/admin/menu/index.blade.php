@extends('layouts.admin')

@section('title', 'Kelola Menu - Kairo Ramen')

@section('content')
<div class="admin-head admin-head-row">
    <div>
        <p class="section-label">MENU</p>
        <h1>Kelola Menu</h1>
        <p class="admin-muted">Menu yang tampil di Home diambil dari data ini.</p>
    </div>
    <a href="{{ route('admin.menu.create') }}" class="btn-primary">+ TAMBAH MENU</a>
</div>

<div class="admin-table-card">
    @if($menus->count())
        <div class="table-scroll">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Signature</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menus as $menu)
                        <tr>
                            <td>
                                <strong>{{ $menu->name }}</strong><br>
                                <small class="admin-muted">{{ $menu->slug }}</small>
                            </td>
                            <td>{{ $menu->category ?? '—' }}</td>
                            <td><strong>Rp {{ number_format($menu->price, 0, ',', '.') }}</strong></td>
                            <td>
                                @if($menu->is_available)
                                    <span class="status-badge status-ok">Tersedia</span>
                                @else
                                    <span class="status-badge status-off">Habis</span>
                                @endif
                            </td>
                            <td>
                                @if($menu->is_featured)
                                    <span class="status-badge status-approved">Signature</span>
                                @else
                                    <span class="admin-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    <form action="{{ route('admin.menu.featured', $menu) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="row-link">{{ $menu->is_featured ? 'Unfeature' : 'Feature' }}</button>
                                    </form>
                                    <a href="{{ route('admin.menu.edit', $menu) }}" class="row-link">Edit</a>
                                    <form action="{{ route('admin.menu.destroy', $menu) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
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
        <p class="empty-state">Belum ada menu. <a href="{{ route('admin.menu.create') }}" class="stat-link">Tambah sekarang &rarr;</a></p>
    @endif
</div>
@endsection
