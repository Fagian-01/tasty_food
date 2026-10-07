@extends('layouts.admin')

@section('title', 'Order ' . $order->order_code . ' - Kairo Ramen')

@section('content')
<div class="admin-head">
    <p class="section-label">ORDER #{{ $order->order_code }}</p>
    <h1>{{ $order->statusLabel() }}</h1>
    <p class="admin-muted">Dibuat {{ $order->created_at->format('d M Y H:i') }}</p>
</div>

@if(session('success'))
    <div class="success-message">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="error-box">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="admin-table-card" style="margin-bottom:20px;">
    <div class="admin-table-head"><h2>Customer</h2><span class="status-badge status-{{ $order->status }}">{{ $order->statusLabel() }}</span></div>
    <p><strong>Nama:</strong> {{ $order->customer_name }}</p>
    <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
    <p><strong>Alamat:</strong> {{ $order->customer_address }}</p>
    @if($order->notes)
        <p><strong>Catatan:</strong> {{ $order->notes }}</p>
    @endif
</div>

<div class="admin-table-card" style="margin-bottom:20px;">
    <div class="admin-table-head"><h2>Alamat Pengantaran</h2></div>
    <p>{{ $order->address ?: $order->customer_address }}</p>
    @if($order->address_note)
        <p style="margin-top:8px;"><strong>Detail:</strong> {{ $order->address_note }}</p>
    @endif
    @if($order->latitude !== null && $order->longitude !== null)
        <p class="admin-muted" style="margin-top:8px;">Koordinat: {{ $order->latitude }}, {{ $order->longitude }}</p>
        <div class="admin-form-actions" style="margin-top:12px;">
            <a class="btn-primary"
               href="https://www.openstreetmap.org/?mlat={{ $order->latitude }}&mlon={{ $order->longitude }}#map=17/{{ $order->latitude }}/{{ $order->longitude }}"
               target="_blank" rel="noopener">LIHAT LOKASI DI MAP</a>
        </div>
    @else
        <p class="admin-muted" style="margin-top:8px;">Customer checkout tanpa pin peta (alamat manual).</p>
    @endif
</div>

<div class="admin-table-card" style="margin-bottom:20px;">
    <div class="admin-table-head"><h2>Item</h2></div>
    <div class="table-scroll">
        <table class="admin-table">
            <thead><tr><th>Menu</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->quantity }} &times; {{ $item->menu_name }}</td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="admin-table-head" style="margin-top:16px;margin-bottom:0;">
        <h2>Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h2>
    </div>
</div>

<div class="admin-table-card" style="margin-bottom:20px;">
    <div class="admin-table-head"><h2>Pembayaran</h2>
        @if($order->payment)
            <span class="status-badge status-{{ $order->payment->status === 'paid' ? 'delivered' : ($order->payment->status === 'rejected' ? 'rejected' : ($order->payment->status === 'waiting_verification' ? 'cooking' : 'pending')) }}">{{ $order->payment->statusLabel() }}</span>
        @endif
    </div>
    @if($order->payment && $order->payment->payment_method_name)
        <p><strong>Metode:</strong> {{ $order->payment->payment_method_name }} ({{ $order->payment->typeLabel() }})</p>
        @if($order->payment->payment_provider)
            <p><strong>Provider:</strong> {{ $order->payment->payment_provider }}</p>
        @endif
        @if($order->payment->payment_bank_name)
            <p><strong>Bank:</strong> {{ $order->payment->payment_bank_name }}</p>
        @endif
        <p><strong>{{ $order->payment->payment_method_type === 'qris' ? 'Merchant' : 'Atas nama' }}:</strong> {{ $order->payment->payment_account_name }}</p>
        @if($order->payment->payment_account_number)
            <p><strong>Nomor:</strong> {{ $order->payment->payment_account_number }}</p>
        @endif
        @if($order->payment->payment_qris_image)
            <p style="margin-top:12px;"><strong>QRIS saat transaksi (snapshot):</strong></p>
            <a href="{{ asset('storage/' . $order->payment->payment_qris_image) }}" target="_blank" rel="noopener">
                <img src="{{ asset('storage/' . $order->payment->payment_qris_image) }}" alt="QRIS histori {{ $order->order_code }}" class="pay-proof-admin">
            </a>
        @endif
        <p><strong>Total:</strong> Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</p>
        @if($order->payment->paid_at)
            <p><strong>Lunas pada:</strong> {{ $order->payment->paid_at->format('d M Y H:i') }}</p>
        @endif
        @if($order->payment->proof_image)
            <p style="margin-top:12px;"><strong>Bukti Transfer:</strong></p>
            <a href="{{ asset('storage/' . $order->payment->proof_image) }}" target="_blank" rel="noopener">
                <img src="{{ asset('storage/' . $order->payment->proof_image) }}" alt="Bukti transfer {{ $order->order_code }}" class="pay-proof-admin">
            </a>
        @else
            <p class="admin-muted" style="margin-top:8px;">Belum ada bukti transfer.</p>
        @endif
        @if($order->payment->status === 'waiting_verification')
            <div class="admin-form-actions">
                <form action="{{ route('admin.payments.approve', $order) }}" method="POST" style="flex:1;display:flex;">
                    @csrf
                    <button type="submit" class="btn-primary" style="flex:1;background:var(--accent);" onclick="return confirm('Setujui pesanan & pembayaran ini? Order masuk APPROVED, payment LUNAS.')">✓ APPROVE PESANAN &amp; PEMBAYARAN</button>
                </form>
                <form action="{{ route('admin.payments.reject', $order) }}" method="POST" style="flex:1;display:flex;">
                    @csrf
                    <button type="submit" class="row-danger" style="flex:1;padding:15px;" onclick="return confirm('Tolak bukti pembayaran ini?')">✕ TOLAK PEMBAYARAN</button>
                </form>
            </div>
        @endif
    @else
        <p class="admin-muted">Customer belum memilih metode pembayaran (unpaid).</p>
    @endif
</div>

@php $payStatus = $order->payment?->status ?? 'unpaid'; @endphp
<div class="admin-table-card" style="margin-bottom:20px;">
    <div class="admin-table-head"><h2>Aksi Status</h2></div>
    @if($order->status === 'pending' && $payStatus === 'unpaid')
        <div class="order-warn">Menunggu pembayaran customer. Tidak ada aksi sampai bukti pembayaran diupload.</div>
        <div class="admin-form-actions">
            <form action="{{ route('admin.orders.reject', $order) }}" method="POST" style="flex:1;display:flex;">
                @csrf
                <button type="submit" class="row-danger" style="flex:1;padding:15px;" onclick="return confirm('Tolak pesanan ini?')">Tolak Pesanan</button>
            </form>
        </div>
    @elseif($order->status === 'pending' && $payStatus === 'rejected')
        <div class="order-warn">Pembayaran ditolak. Menunggu customer upload bukti baru. Order tidak bisa diproses.</div>
        <div class="admin-form-actions">
            <form action="{{ route('admin.orders.reject', $order) }}" method="POST" style="flex:1;display:flex;">
                @csrf
                <button type="submit" class="row-danger" style="flex:1;padding:15px;" onclick="return confirm('Tolak pesanan ini?')">Tolak Pesanan</button>
            </form>
        </div>
    @elseif($order->status === 'pending' && $payStatus === 'paid')
        <div class="order-warn">Pembayaran sudah lunas — gunakan tombol APPROVE PESANAN &amp; PEMBAYARAN di section Pembayaran.</div>
    @elseif($order->status === 'pending')
        <div class="admin-form-actions">
            <form action="{{ route('admin.orders.reject', $order) }}" method="POST" style="flex:1;display:flex;">
                @csrf
                <button type="submit" class="row-danger" style="flex:1;padding:15px;" onclick="return confirm('Tolak pesanan ini?')">Tolak Pesanan</button>
            </form>
        </div>
    @elseif($order->nextStatus())
        <form action="{{ route('admin.orders.advance', $order) }}" method="POST">
            @csrf
            <button type="submit" class="btn-primary">
                @if($order->status === 'approved') Mulai Masak (→ Sedang Dimasak)
                @elseif($order->status === 'cooking') Tandai Siap Diantar
                @elseif($order->status === 'ready') Mulai Antar (→ Sedang Diantar)
                @elseif($order->status === 'delivering') Tandai Sudah Sampai
                @endif
            </button>
        </form>
    @else
        <p class="admin-muted">Tidak ada aksi lagi untuk status ini.</p>
    @endif
</div>

<div class="admin-table-card">
    <div class="admin-table-head"><h2>Timeline</h2></div>
    @php
        $rows = [
            ['Dibuat', $order->created_at],
            ['Disetujui', $order->approved_at],
            ['Dimasak', $order->cooking_at],
            ['Siap diantar', $order->ready_at],
            ['Diantar', $order->delivering_at],
            ['Sampai', $order->delivered_at],
            ['Selesai', $order->completed_at],
            ['Ditolak', $order->rejected_at],
        ];
    @endphp
    @foreach($rows as [$label, $time])
        @if($time)
            <p>{{ $label }}: <strong>{{ $time->format('d M Y H:i') }}</strong></p>
        @endif
    @endforeach
    <div class="admin-form-actions">
        <a href="{{ route('admin.orders.index') }}" class="btn-ghost">Kembali ke Daftar</a>
    </div>
</div>
@endsection
