@extends('layouts.admin')

@section('title', 'Tambah Metode Pembayaran - Kairo Ramen')

@section('content')
<div class="admin-head">
    <p class="section-label">PEMBAYARAN</p>
    <h1>Tambah Metode</h1>
    <p class="admin-muted">Pilih tipe, isi field yang muncul, upload QRIS bila perlu.</p>
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

    <form action="{{ route('admin.payment-methods.store') }}" method="POST" enctype="multipart/form-data" id="methodForm">
        @csrf
        <div class="form-group">
            <label>Tipe Pembayaran</label>
            <div class="type-cards">
                @foreach($types as $value => $label)
                    <label class="type-card {{ old('type', 'dana') === $value ? 'active' : '' }}">
                        <input type="radio" name="type" value="{{ $value }}" {{ old('type', 'dana') === $value ? 'checked' : '' }} required>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="form-group" id="fieldProvider" hidden>
            <label for="provider">Provider E-Wallet</label>
            <input type="text" id="provider" name="provider" placeholder="GoPay / OVO / ShopeePay / LinkAja" value="{{ old('provider') }}">
        </div>

        <div class="form-group" id="fieldBank" hidden>
            <label for="bank_name">Nama Bank</label>
            <input type="text" id="bank_name" name="bank_name" placeholder="BCA / BRI / BNI / Mandiri" value="{{ old('bank_name') }}">
        </div>

        <div class="form-group">
            <label for="name">Nama Metode / Label</label>
            <input type="text" id="name" name="name" placeholder="DANA KAIRO / BCA KAIRO / QRIS KAIRO RAMEN" value="{{ old('name') }}" required>
        </div>

        <div class="form-row" id="fieldAccount">
            <div class="form-group">
                <label for="account_name" id="accountNameLabel">Nama Pemilik</label>
                <input type="text" id="account_name" name="account_name" placeholder="KAIRO RAMEN" value="{{ old('account_name') }}" required>
            </div>
            <div class="form-group" id="fieldNumber">
                <label for="account_number" id="accountNumberLabel">Nomor</label>
                <input type="text" id="account_number" name="account_number" placeholder="081234567890" value="{{ old('account_number') }}">
            </div>
        </div>

        <div class="form-group" id="fieldQris">
            <label for="qris_image">Upload QRIS <span id="qrisRequired">(wajib untuk tipe QRIS)</span></label>
            <input type="file" id="qris_image" name="qris_image" accept="image/jpeg,image/jpg,image/png,image/webp">
            <p class="hint">JPG / JPEG / PNG / WEBP, maksimal 2MB.</p>
        </div>

        <div class="form-group">
            <label for="instructions">Instruksi Pembayaran (opsional)</label>
            <textarea id="instructions" name="instructions" rows="4" placeholder="Contoh: Transfer sesuai total pesanan lalu upload bukti pembayaran.">{{ old('instructions') }}</textarea>
        </div>
        <div class="form-check">
            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
            <label for="is_active">Aktif (tampil ke customer)</label>
        </div>
        <div class="admin-form-actions">
            <a href="{{ route('admin.payment-methods.index') }}" class="btn-ghost">Batal</a>
            <button type="submit" class="btn-primary">SIMPAN METODE</button>
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
        document.getElementById('qrisRequired').style.display = isQris ? '' : 'none';
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
