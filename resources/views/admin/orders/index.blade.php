@extends('layouts.admin')

@section('title', 'Pesanan - Kairo Ramen')

@section('content')
<div class="admin-head admin-head-row">
    <div>
        <p class="section-label">PESANAN</p>
        <h1>Daftar Pesanan</h1>
        <p class="admin-muted">{{ $pendingCount }} menunggu persetujuan.</p>
    </div>
</div>

<div class="order-filter">
    <a href="{{ route('admin.orders.index') }}" class="{{ ! $status ? 'active' : '' }}">Semua</a>
    @foreach($statuses as $s)
        <a href="{{ route('admin.orders.index', ['status' => $s]) }}" class="{{ $status === $s ? 'active' : '' }}">{{ ucfirst($s) }}</a>
    @endforeach
</div>

<div class="admin-table-card">
    @if($orders->count())
        <div class="table-scroll">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order Code</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_code }}</strong><br><small class="admin-muted">{{ $order->items_count }} item</small></td>
                            <td>{{ $order->customer_name }}<br><small class="admin-muted">{{ $order->customer_phone }}</small></td>
                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td><span class="status-badge status-{{ $order->status }}">{{ $order->statusLabel() }}</span></td>
                            <td><small class="admin-muted">{{ $order->created_at->format('d M Y H:i') }}</small></td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="row-link">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:18px;">{{ $orders->links() }}</div>
    @else
        <p class="empty-state">Belum ada pesanan{{ $status ? ' dengan status '.$status : '' }}.</p>
    @endif
</div>
@endsection
