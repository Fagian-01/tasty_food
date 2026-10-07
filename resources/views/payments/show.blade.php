@extends('layouts.app')

@section('title', 'Pembayaran ' . $order->order_code . ' - Kairo Ramen')

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

        <p class="section-label">PEMBAYARAN MANUAL</p>
        <h1>Bayar Pesanan {{ $order->order_code }}</h1>
        <p class="order-muted">Transfer manual, upload bukti, tunggu verifikasi admin.</p>

        @php $payment = $order->payment; @endphp

        <div class="pay-grid">
            <div class="order-summary">
                <h2>Total Pesanan</h2>
                <div class="summary-row"><span>Nomor Pesanan</span><strong>{{ $order->order_code }}</strong></div>
                @foreach($order->items as $item)
                    <div class="summary-row">
                        <span>{{ $item->quantity }} &times; {{ $item->menu_name }}</span>
                        <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                    </div>
                @endforeach
                <div class="summary-row total">
                    <span>Total yang harus dibayar</span>
                    <strong class="pay-total">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                </div>
            </div>

            <div class="order-summary">
                <h2>Status Pembayaran</h2>
                @if(! $payment || $payment->status === 'unpaid')
                    <p><span class="status-badge status-pending">Belum Bayar</span></p>
                    <p class="order-muted" style="margin-top:10px;">Pilih metode di bawah, transfer, lalu upload bukti.</p>
                @elseif($payment->status === 'waiting_verification')
                    <p><span class="status-badge status-cooking">Menunggu Verifikasi Pembayaran</span></p>
                    <p class="order-muted" style="margin-top:10px;">
                        Bukti terkirim ke <strong>{{ $payment->payment_method_name }}</strong>.
                        Pesanan belum masuk dapur sebelum admin menyetujui.
                    </p>
                @elseif($payment->status === 'paid')
                    <p><span class="status-badge status-delivered">Pembayaran berhasil diverifikasi</span></p>
                    <p class="order-muted" style="margin-top:10px;">Pesananmu lanjut ke dapur. Terima kasih!</p>
                @elseif($payment->status === 'rejected')
                    <p><span class="status-badge status-rejected">Pembayaran ditolak</span></p>
                    <p class="order-muted" style="margin-top:10px;">Bukti tidak valid. Silakan upload bukti pembayaran kembali di bawah.</p>
                @endif

                @if($payment && $payment->payment_method_name)
                    <div class="summary-row"><span>Metode</span><strong>{{ $payment->payment_method_name }} ({{ $payment->typeLabel() }})</strong></div>
                    @if($payment->payment_provider)
                        <div class="summary-row"><span>Provider</span><strong>{{ $payment->payment_provider }}</strong></div>
                    @endif
                    @if($payment->payment_bank_name)
                        <div class="summary-row"><span>Bank</span><strong>{{ $payment->payment_bank_name }}</strong></div>
                    @endif
                    <div class="summary-row"><span>{{ $payment->payment_method_type === 'qris' ? 'Merchant' : 'Atas nama' }}</span><strong>{{ $payment->payment_account_name }}</strong></div>
                    @if($payment->payment_account_number)
                        <div class="summary-row"><span>Nomor tujuan</span><strong>{{ $payment->payment_account_number }}</strong></div>
                    @endif
                    @if($payment->payment_qris_image)
                        <p style="margin-top:10px;"><strong>QRIS saat transaksi:</strong></p>
                        <img src="{{ asset('storage/' . $payment->payment_qris_image) }}" alt="QRIS histori {{ $order->order_code }}" class="pay-proof">
                    @endif
                @endif
            </div>
        </div>

        @if(! $payment || ! in_array($payment->status, ['waiting_verification', 'paid'], true))
            <div class="order-summary" style="margin-top:26px;">
                <h2>Metode Pembayaran</h2>
                @if($methods->count())
                    <form action="{{ route('payments.store', $order->order_code) }}" method="POST" enctype="multipart/form-data" id="payForm">
                        @csrf
                        <div class="pay-methods">
                            @foreach($methods as $m)
                                <label class="pay-method-card">
                                    <input type="radio" name="payment_method" value="{{ $m->id }}"
                                        {{ old('payment_method', $payment?->payment_method_id) == $m->id ? 'checked' : '' }} required>
                                    <span class="pay-method-body">
                                        <small class="pay-type">{{ $m->typeLabel() }}{{ $m->detailLabel() ? ' · ' . $m->detailLabel() : '' }}</small>
                                        <strong>{{ $m->name }}</strong>
                                        @if($m->type === 'qris')
                                            <small>{{ $m->account_name }}</small>
                                            @if($m->qris_image)
                                                <img src="{{ asset('storage/' . $m->qris_image) }}" alt="QRIS {{ $m->name }}" class="pay-qris-thumb">
                                            @endif
                                        @else
                                            <small>{{ $m->account_name }} &middot; {{ $m->account_number }}</small>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <div id="payDetail" class="pay-detail" hidden>
                            <div class="summary-row"><span>Tipe</span><strong id="payDetailType">—</strong></div>
                            <div class="summary-row"><span>Transfer ke</span><strong id="payDetailName">—</strong></div>
                            <div class="summary-row" id="payDetailProviderRow" hidden><span id="payDetailProviderLabel">Provider</span><strong id="payDetailProvider">—</strong></div>
                            <div class="summary-row"><span id="payDetailOwnerLabel">Atas nama</span><strong id="payDetailOwner">—</strong></div>
                            <div class="summary-row" id="payDetailNumberRow">
                                <span id="payDetailNumberLabel">Nomor tujuan</span>
                                <strong><span id="payDetailNumber">—</span>
                                    <button type="button" class="btn-ghost btn-small" id="payCopyBtn" style="margin-left:10px;">SALIN NOMOR</button>
                                </strong>
                            </div>
                            <div id="payDetailQrisRow" hidden style="margin-top:12px;">
                                <p><strong>Scan QRIS di bawah:</strong></p>
                                <img id="payDetailQris" src="" alt="QRIS pembayaran" class="pay-qris-big">
                            </div>
                            <div class="summary-row total">
                                <span>Nominal transfer</span>
                                <strong class="pay-total">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                            </div>
                            <p class="order-muted" id="payDetailInstruksi" style="margin-top:10px;"></p>
                        </div>

                        <div class="form-group" style="margin-top:18px;">
                            <label for="proof_image">Bukti Transfer (wajib)</label>
                            <input type="file" id="proof_image" name="proof_image" accept="image/jpeg,image/jpg,image/png,image/webp" required>
                            <p class="hint">JPG / JPEG / PNG / WEBP, maksimal 2MB.</p>
                        </div>

                        <button type="submit" class="btn-primary full-btn-order">KIRIM BUKTI PEMBAYARAN</button>
                    </form>
                @else
                    <p class="empty-state">Metode pembayaran belum tersedia. Hubungi admin Kairo Ramen.</p>
                @endif
            </div>
        @elseif($payment && $payment->status === 'waiting_verification')
            <div class="order-summary" style="margin-top:26px;">
                <h2>Bukti Terkirim</h2>
                <p class="order-muted">Menunggu verifikasi admin. Bukti tidak bisa diganti selama menunggu.</p>
                @if($payment->proof_image)
                    <img src="{{ asset('storage/' . $payment->proof_image) }}" alt="Bukti transfer" class="pay-proof">
                @endif
            </div>
        @elseif($payment && $payment->status === 'paid')
            <div class="order-summary" style="margin-top:26px;">
                <h2>Sudah Lunas</h2>
                <p class="order-muted">Bukti tidak bisa diganti setelah pembayaran diverifikasi.</p>
                @if($payment->proof_image)
                    <img src="{{ asset('storage/' . $payment->proof_image) }}" alt="Bukti transfer" class="pay-proof">
                @endif
            </div>
        @endif

        <div class="order-actions-center">
            @if($order->status !== 'pending' || ($payment?->status ?? 'unpaid') === 'paid')
                <a href="{{ route('orders.show', $order->order_code) }}" class="btn-primary">LACAK PESANAN</a>
            @else
                <a href="{{ route('payments.show', $order->order_code) }}" class="btn-ghost">Refresh Status</a>
            @endif
            <a href="{{ route('menu.index') }}" class="btn-ghost">Kembali ke Menu</a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    var methods = {!! $methodsJson !!};
    var form = document.getElementById('payForm');
    if (!form) return;
    var detail = document.getElementById('payDetail');
    function el(id) { return document.getElementById(id); }
    function render() {
        var checked = form.querySelector('input[name="payment_method"]:checked');
        if (!checked) { detail.hidden = true; return; }
        var m = methods[checked.value];
        if (!m) { detail.hidden = true; return; }
        var isQris = (m.type === 'qris');
        detail.hidden = false;
        el('payDetailType').textContent = m.detail ? (m.type_label + ' · ' + m.detail) : m.type_label;
        el('payDetailName').textContent = m.name;
        el('payDetailOwnerLabel').textContent = isQris ? 'Merchant' : 'Atas nama';
        el('payDetailOwner').textContent = m.owner || '—';
        el('payDetailNumberRow').hidden = isQris;
        if (!isQris) {
            el('payDetailNumberLabel').textContent = m.type === 'bank' ? 'Nomor rekening' : 'Nomor tujuan';
            el('payDetailNumber').textContent = m.number || '—';
        }
        var provRow = el('payDetailProviderRow');
        if (m.detail) {
            provRow.hidden = false;
            el('payDetailProviderLabel').textContent = m.type === 'bank' ? 'Bank' : 'Provider';
            el('payDetailProvider').textContent = m.detail;
        } else {
            provRow.hidden = true;
        }
        var qrisRow = el('payDetailQrisRow');
        if (isQris && m.qris) {
            qrisRow.hidden = false;
            el('payDetailQris').src = m.qris;
        } else {
            qrisRow.hidden = true;
        }
        el('payDetailInstruksi').textContent = m.instructions || '';
    }
    form.addEventListener('change', render);
    render();
    var copyBtn = document.getElementById('payCopyBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            var num = document.getElementById('payDetailNumber').textContent.trim();
            function done() { copyBtn.textContent = 'TERSALIN ✓'; setTimeout(function(){ copyBtn.textContent = 'SALIN NOMOR'; }, 1500); }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(num).then(done, done);
            } else {
                var t = document.createElement('textarea');
                t.value = num; document.body.appendChild(t); t.select();
                try { document.execCommand('copy'); } catch (e) {}
                document.body.removeChild(t); done();
            }
        });
    }
})();
</script>
@endpush
