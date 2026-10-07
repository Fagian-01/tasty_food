@extends('layouts.app')

@section('title', 'Pesanan ' . $order->order_code . ' - Kairo Ramen')

@section('content')
<section class="section-pad order-sec">
    <div class="order-wrap">
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

        <p class="section-label">PESANAN {{ $order->order_code }}</p>
        <h1>Status: {{ $order->statusLabel() }}</h1>
        <p class="order-muted">Atas nama {{ $order->customer_name }} &middot; dibuat {{ $order->created_at->format('d M Y H:i') }}</p>

        @if($order->status === 'delivered')
            <div class="delivered-box">
                <h2>Pesanan kamu sudah sampai!</h2>
                <p>Klik tombol di bawah kalau makanan sudah kamu terima.</p>
                <form action="{{ route('orders.confirm', $order->order_code) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary btn-received">PESANAN DITERIMA</button>
                </form>
            </div>
        @endif

        @if($order->status === 'completed')
            <div class="success-message">Pesanan selesai. Terima kasih sudah memesan di Kairo Ramen!</div>
        @endif

        @if($order->status === 'rejected')
            <div class="error-box">Maaf, pesanan ini ditolak admin. Hubungi kami untuk info lebih lanjut.</div>
        @endif

        @php
            $steps = [
                'pending' => 'Pesanan dibuat',
                'approved' => 'Pesanan disetujui',
                'cooking' => 'Sedang dimasak',
                'ready' => 'Siap diantar',
                'delivering' => 'Sedang diantar',
                'delivered' => 'Sudah sampai',
                'completed' => 'Selesai',
            ];
            $flow = array_keys($steps);
            $currentIndex = array_search($order->status, $flow, true);
        @endphp

        @if($order->status !== 'rejected')
            <div class="timeline">
                @foreach($steps as $key => $label)
                    @php $idx = array_search($key, $flow, true); @endphp
                    <div class="tl-row {{ $idx < $currentIndex ? 'done' : ($idx === $currentIndex ? 'current' : 'todo') }}">
                        <span class="tl-dot">{{ $idx < $currentIndex ? '✓' : ($idx === $currentIndex ? '●' : '○') }}</span>
                        <span>{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="order-summary">
            <h2>Detail Pesanan</h2>
            <div class="summary-row"><span>Nomor Pesanan</span><strong>{{ $order->order_code }}</strong></div>
            <div class="summary-row"><span>Nama</span><strong>{{ $order->customer_name }}</strong></div>
            <div class="summary-row"><span>Alamat Pengantaran</span><strong>{{ $order->address ?: $order->customer_address }}</strong></div>
            @if($order->address_note)
                <div class="summary-row"><span>Detail Alamat</span><strong>{{ $order->address_note }}</strong></div>
            @endif
            @if($order->latitude !== null && $order->longitude !== null)
                <div class="summary-row"><span>Titik Antar</span><strong>Lokasi tersimpan di peta ✓</strong></div>
            @endif
            @foreach($order->items as $item)
                <div class="summary-row">
                    <span>{{ $item->quantity }} &times; {{ $item->menu_name }}</span>
                    <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                </div>
            @endforeach
            <div class="summary-row total">
                <span>Total</span>
                <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="order-actions-center">
            <a href="{{ route('orders.track') }}" class="btn-ghost">Lacak Pesanan Lain</a>
            <a href="{{ route('menu.index') }}" class="btn-ghost">Kembali ke Menu</a>
        </div>
    </div>
</section>
@endsection
