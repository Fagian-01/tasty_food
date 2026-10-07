@extends('layouts.admin')

@section('title', 'Edit Metode Pembayaran - Kairo Ramen')

@section('content')
<div class="admin-head">
    <p class="section-label">PEMBAYARAN</p>
    <h1>Edit Metode</h1>
    <p class="admin-muted">{{ $method->name }} — order lama tetap memakai snapshot saat transaksi.</p>
</div>

<div class="form-card admin-form">
    @if($errors->any())
        <div class="error-box">
            <ul>
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.payment-methods.update', $method) }}" method="POST" enctype="multipart/form-data" id="methodForm">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Tipe Pembayaran</label>
            <div class="type-cards">
                @foreach($types as $value => $label)
                    <label class="type-card {{ old('type', $method->type) === $value ? 'active' : '' }}">
                        <input type="radio" name="type" value="{{ $value }}" {{ old('type', $method->type) === $value ? 'checked' : '' }} required>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="form-group" id="fieldProvider" hidden>
            <label for="provider">Provider E-Wallet</label>
            <input type="text" id="provider" name="provider" placeholder="GoPay / OVO / ShopeePay / LinkAja" value="{{ old('provider', $method->provider) }}">
        </div>

        <div class="form-group" id="fieldBank" hidden>
            <label for="bank_name">Nama Bank</label>
            <input type="text" id="bank_name" name="bank_name" placeholder="BCA / BRI / BNI / Mandiri" value="{{ old('bank_name', $method->bank_name) }}">
        </div>

        <div class="form-group">
            <label for="name">Nama Metode / Label</label>
            <input type="text" id="name" name="name" value="{{ old('name', $method->name) }}" required>
        </div>

        <div class="form-row" id="fieldAccount">
            <div class="form-group">
                <label for="account_name" id="accountNameLabel">Nama Pemilik</label>
                <input type="text" id="account_name" name="account_name" value="{{ old('account_name', $method->account_name) }}" required>
            </div>
            <div class="form-group" id="fieldNumber">
                <label for="account_number" id="accountNumberLabel">Nomor</label>
                <input type="text" id="account_number" name="account_number" value="{{ old('account_number', $method->account_number) }}">
            </div>
        </div>

        <div class="form-group" id="fieldQris">
            <label for="qris_image">Upload QRIS @if(! $method->qris_image)<span id="qrisRequired">(wajib untuk tipe QRIS)</span>@endif</label>
            @if($method->qris_image)
                <img src="{{ asset('storage/' . $method->qris_image) }}" alt="QRIS {{ $method->name }}" class="qris-preview">
                <p class="hint">Gambar saat ini. Upload baru untuk mengganti (gambar lama dihapus otomatis).</p>
            @else
                <p class="hint">JPG / JPEG / PNG / WEBP, maksimal 2MB.</p>
            @endif
            <input type="file" id="qris_image" name="qris_image" accept="image/jpeg,image/jpg,image/png,image/webp" style="margin-top:8px;">
        </div>

        <div class="form-group">
            <label for="instructions">Instruksi Pembayaran (opsional)</label>
            <textarea id="instructions" name="instructions" rows="4">{{ old('instructions', $method->instructions) }}</textarea>
        </div>
        <div class="form-check">
            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $method->is_active) ? 'checked' : '' }}>
            <label for="is_active">Aktif (tampil ke customer)</label>
        </div>
        <div class="admin-form-actions">
            <a href="{{ route('admin.payment-methods.index') }}" class="btn-ghost">Batal</a>
            <button type="submit" class="btn-primary">SIMPAN PERUBAHAN</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('methodForm');
    if (!form) return;
    function currentType() {
        var c = form.querySelector('input[name="type"]:checked');
        return c ? c.value : 'dana';
    }
    function render() {
        var t = currentType();
        form.querySelectorAll('.type-card').forEach(function (card) {
            card.classList.toggle('active', card.querySelector('input').checked);
        });
        document.getElementById('fieldProvider').hidden = (t !== 'ewallet');
        document.getElementById('fieldBank').hidden = (t !== 'bank');
        var isQris = (t === 'qris');
        document.getElementById('fieldAccount').hidden = isQris;
        document.getElementById('accountNumberLabel').textContent =
            t === 'bank' ? 'Nomor Rekening' : (t === 'ewallet' ? 'Nomor E-Wallet' : 'Nomor DANA');
        document.getElementById('accountNameLabel').textContent =
            isQris ? 'Nama Merchant' : (t === 'bank' ? 'Nama Pemilik Rekening' : 'Nama Pemilik');
    }
    form.addEventListener('change', render);
    render();
})();
</script>
@endpush
