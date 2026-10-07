@extends('layouts.admin')

@section('title', 'Metode Pembayaran - Kairo Ramen')

@section('content')
<div class="admin-head admin-head-row">
    <div>
        <p class="section-label">PEMBAYARAN</p>
        <h1>Kelola Metode Pembayaran</h1>
        <p class="admin-muted">Customer hanya melihat metode yang aktif.</p>
    </div>
    <a href="{{ route('admin.payment-methods.create') }}" class="btn-primary">+ TAMBAH METODE</a>
</div>

<div class="admin-table-card">
    @if($methods->count())
        <div class="table-scroll">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tipe</th>
                        <th>Metode</th>
                        <th>Provider / Bank</th>
                        <th>Pemilik</th>
                        <th>Nomor</th>
                        <th>QRIS</th>
                        <th>Status</th>
                        <th>Terpakai</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($methods as $method)
                        <tr>
                            <td><span class="status-badge status-{{ $method->type === 'qris' ? 'cooking' : 'approved' }}">{{ $method->typeLabel() }}</span></td>
                            <td><strong>{{ $method->name }}</strong></td>
                            <td>{{ $method->detailLabel() ?? '—' }}</td>
                            <td>{{ $method->account_name ?? '—' }}</td>
                            <td>{{ $method->account_number ?? '—' }}</td>
                            <td>
                                @if($method->qris_image)
                                    <a href="{{ asset('storage/' . $method->qris_image) }}" target="_blank" rel="noopener">
                                        <img src="{{ asset('storage/' . $method->qris_image) }}" alt="QRIS {{ $method->name }}" class="qris-thumb">
                                    </a>
                                @else
                                    <span class="admin-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($method->is_active)
                                    <span class="status-badge status-delivered">Aktif</span>
                                @else
                                    <span class="status-badge status-off">Nonaktif</span>
                                @endif
                            </td>
                            <td>{{ $method->payments_count }} &times;</td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.payment-methods.edit', $method) }}" class="row-link">Edit</a>
                                    <form action="{{ route('admin.payment-methods.destroy', $method) }}" method="POST" onsubmit="return confirm('Hapus metode ini? Histori pembayaran lama tetap aman (snapshot).')">
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
        <p class="empty-state">Belum ada metode pembayaran. <a href="{{ route('admin.payment-methods.create') }}" class="stat-link">Tambah sekarang &rarr;</a></p>
    @endif
</div>
@endsection
