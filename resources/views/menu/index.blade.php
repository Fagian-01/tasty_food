@extends('layouts.app')

@section('title', 'Menu - Kairo Ramen')

@section('content')
<section class="page-hero">
    <div>
        <h1>MENU KAIRO</h1>
        <p>Pilih ramen favoritmu, masukkan ke keranjang, checkout.</p>
    </div>
</section>

<section class="sig-sec">
    <div class="section-heading">
        <p class="section-label">PESAN MAKANAN</p>
        <h2>SEMUA MENU TERSEDIA</h2>
    </div>

    <div class="order-actions">
        <a href="{{ route('cart.index') }}" class="btn-ghost">Lihat Keranjang ({{ session('cart') ? array_sum(session('cart')) : 0 }})</a>
        <a href="{{ route('orders.track') }}" class="btn-ghost">Lacak Pesanan</a>
    </div>

    @if($menus->count())
        <div class="sig-grid order-grid">
            @foreach($menus as $menu)
                <article class="sig-card">
                    <img
                        src="{{ $menu->image ? asset('storage/' . $menu->image) : asset('images/hero-ramen-cutout.png') }}"
                        alt="{{ $menu->name }}"
                        loading="lazy">
                    <div class="sig-body">
                        @if($menu->category)
                            <p class="menu-cat">{{ strtoupper($menu->category) }}</p>
                        @endif
                        <h3>{{ strtoupper($menu->name) }}</h3>
                        @if($menu->description)
                            <p class="menu-desc">{{ $menu->description }}</p>
                        @endif
                        <p class="price">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        <form action="{{ route('cart.add', $menu) }}" method="POST" class="order-add-form">
                            @csrf
                            <button type="submit" class="btn-primary btn-add-cart">TAMBAH KE KERANJANG</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="empty-state order-empty">
            <p class="empty-title">Belum ada menu tersedia.</p>
            <p>Coba lagi nanti, dapur kami sedang menyiapkan yang terbaik.</p>
        </div>
    @endif
</section>
@endsection
