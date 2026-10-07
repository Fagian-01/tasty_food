@extends('layouts.app')

@section('title', 'Pesanan Berhasil - Kairo Ramen')

@section('content')
<section class="section-pad order-sec">
    <div class="order-wrap order-success">
        <p class="section-label">PESANAN DITERIMA DAPUR</p>
        <h1>Pesanan kamu berhasil dibuat.</h1>
        <p class="order-muted">Simpan nomor pesanan ini untuk melacak status.</p>
        <div class="order-code-big">{{ $order->order_code }}</div>
        <p>Status: <strong>{{ $order->statusLabel() }}</strong></p>
        <p class="order-muted">Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
        <p class="order-muted">Lakukan pembayaran agar pesananmu segera diverifikasi admin.</p>
        <div class="order-actions-center">
            <a href="{{ route('payments.show', $order->order_code) }}" class="btn-primary">BAYAR SEKARANG</a>
            <a href="{{ route('orders.show', $order->order_code) }}" class="btn-ghost">Lacak Pesanan</a>
            <a href="{{ route('menu.index') }}" class="btn-ghost">Kembali ke Menu</a>
        </div>
    </div>
</section>
@endsection
