@extends('layouts.app')

@section('title', 'Lacak Pesanan - Kairo Ramen')

@section('content')
<section class="page-hero">
    <div>
        <h1>LACAK PESANAN</h1>
        <p>Masukkan nomor pesanan, misal KAIRO-0001.</p>
    </div>
</section>

<section class="section-pad order-sec">
    <div class="order-wrap order-narrow">
        <div class="form-card">
            @if($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('orders.track.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="order_code">Nomor Pesanan</label>
                    <input id="order_code" name="order_code" value="{{ old('order_code') }}" required maxlength="50" placeholder="KAIRO-0001" style="text-transform:uppercase">
                </div>
                <button type="submit" class="btn-primary full-btn-order">CEK STATUS</button>
            </form>
        </div>
    </div>
</section>
@endsection
