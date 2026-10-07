@extends('layouts.app')

@section('title', 'Keranjang - Kairo Ramen')

@section('content')
<section class="page-hero">
    <div>
        <h1>KERANJANG</h1>
        <p>Atur jumlah pesananmu sebelum checkout.</p>
    </div>
</section>

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

        @if($cart['isEmpty'])
            <div class="order-empty">
                <p class="empty-title">Keranjangmu masih kosong.</p>
                <p>Yuk pilih ramen hangat favoritmu dulu.</p>
                <a href="{{ route('menu.index') }}" class="btn-primary">LIHAT MENU</a>
            </div>
        @else
            <div class="cart-list">
                @foreach($cart['items'] as $item)
                    <div class="cart-row">
                        <div class="cart-info">
                            <strong>{{ $item['menu']?->name ?? 'Menu dihapus' }}</strong>
                            @if(! $item['available'])
                                <span class="status-badge status-off">TIDAK TERSEDIA</span>
                            @endif
                            <p class="cart-price">Rp {{ number_format($item['price'], 0, ',', '.') }} / porsi</p>
                        </div>
                        <div class="cart-qty">
                            <form action="{{ route('cart.update', $item['menu_id']) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="qty" value="{{ $item['qty'] }}" min="1" max="99">
                                <button type="submit" class="btn-ghost">Update</button>
                            </form>
                            <form action="{{ route('cart.remove', $item['menu_id']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="cart-remove">Hapus</button>
                            </form>
                        </div>
                        <p class="cart-sub">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>

            <div class="order-summary">
                <div class="summary-row total">
                    <span>Total</span>
                    <strong>Rp {{ number_format($cart['total'], 0, ',', '.') }}</strong>
                </div>
                @if($cart['hasUnavailable'])
                    <p class="order-warn">Ada menu yang tidak tersedia. Hapus dari keranjang untuk lanjut.</p>
                @else
                    <a href="{{ route('orders.checkout') }}" class="btn-primary full-btn-order">LANJUT KE CHECKOUT</a>
                @endif
                <a href="{{ route('menu.index') }}" class="back-link">+ Tambah menu lain</a>
            </div>
        @endif
    </div>
</section>
@endsection
