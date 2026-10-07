@extends('layouts.app')

@section('title', 'Checkout - Kairo Ramen')

@section('content')
<section class="page-hero">
    <div>
        <h1>CHECKOUT</h1>
        <p>Isi data pengiriman, kami siapkan pesananmu.</p>
    </div>
</section>

<section class="section-pad order-sec">
    <div class="order-wrap order-grid-2">
        <div class="form-card">
            <h2>Data Penerima</h2>
            @if($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($blocked)
                <p class="order-warn">Keranjang kosong atau ada menu tidak tersedia. Kembali ke keranjang dulu ya.</p>
                <a href="{{ route('cart.index') }}" class="btn-primary">KEMBALI KE KERANJANG</a>
            @else
                <form action="{{ route('orders.store') }}" method="POST" id="checkoutForm">
                    @csrf
                    <div class="form-group">
                        <label for="customer_name">Nama</label>
                        <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required maxlength="255">
                    </div>
                    <div class="form-group">
                        <label for="customer_phone">Nomor HP</label>
                        <input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required maxlength="30" placeholder="08xx...">
                    </div>

                    {{-- ===== ALAMAT PENGANTARAN + MAP PICKER ===== --}}
                    <div class="form-group">
                        <label>Alamat Pengantaran</label>
                        <button type="button" id="btnOpenMap" class="btn-map">📍 Pilih Lokasi di Peta</button>

                        <div class="location-preview" id="locationPreview">
                            <p class="location-preview-label">Lokasi terpilih:</p>
                            <p class="location-preview-address" id="selectedAddressText">
                                {{ old('address', old('customer_address')) ?: 'Belum ada lokasi dipilih. Klik tombol di atas atau tulis alamat manual di bawah.' }}
                            </p>
                            <p class="location-preview-coords" id="selectedCoordsText">
                                @if(old('latitude') && old('longitude'))
                                    {{ old('latitude') }}, {{ old('longitude') }}
                                @endif
                            </p>
                            <p class="location-preview-warn" id="geocodeWarn" hidden></p>
                        </div>

                        {{-- Alamat manual: tetap required agar checkout tanpa map tetap jalan seperti dulu.
                             Saat map dipakai, JS mengisi otomatis dari hasil reverse geocoding. --}}
                        <textarea id="customer_address" name="customer_address" required rows="3" placeholder="Contoh: Jl. Asia Afrika No. 8, Bandung">{{ old('customer_address') }}</textarea>

                        {{-- Koordinat + alamat hasil geocoding: hidden, jangan diketik manual. --}}
                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                        <input type="hidden" id="address" name="address" value="{{ old('address') }}">
                    </div>

                    <div class="form-group">
                        <label for="address_note">Detail Alamat</label>
                        <textarea id="address_note" name="address_note" rows="2" placeholder="Contoh: Rumah warna putih, dekat minimarket...">{{ old('address_note') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="notes">Catatan Pesanan (opsional)</label>
                        <textarea id="notes" name="notes" rows="2" placeholder="Contoh: level pedas, tanpa daun bawang...">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn-primary full-btn-order">BUAT PESANAN</button>
                </form>

                {{-- ===== MODAL MAP (Leaflet + OpenStreetMap, tanpa API key) ===== --}}
                <div class="map-modal" id="mapModal" hidden>
                    <div class="map-modal-card" role="dialog" aria-modal="true" aria-label="Pilih lokasi pengantaran">
                        <div class="map-modal-head">
                            <h3>Pilih Lokasi Pengantaran</h3>
                            <button type="button" class="map-modal-close" id="btnCloseMap" aria-label="Tutup peta">✕</button>
                        </div>
                        <p class="order-muted map-hint">Geser peta, klik / ketuk untuk menaruh pin, atau pakai tombol lokasi saya.</p>
                        <div class="map-toolbar">
                            <button type="button" class="btn-ghost btn-small" id="btnMyLocation">◎ Lokasi Saya</button>
                            <span class="order-muted map-status" id="mapStatus">Memuat peta…</span>
                        </div>
                        <div id="deliveryMap"></div>
                        <div class="map-modal-actions">
                            <button type="button" class="btn-primary" id="btnUseLocation">Gunakan Lokasi Ini</button>
                            <button type="button" class="btn-ghost" id="btnCancelMap">Batal</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="order-summary">
            <h2>Ringkasan</h2>
            @foreach($cart['items'] as $item)
                <div class="summary-row">
                    <span>{{ $item['qty'] }} &times; {{ $item['menu']?->name ?? 'Menu dihapus' }}</span>
                    <strong>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</strong>
                </div>
            @endforeach
            <div class="summary-row total">
                <span>Total</span>
                <strong>Rp {{ number_format($cart['total'], 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    var modal = document.getElementById('mapModal');
    if (!modal) return;

    var btnOpen = document.getElementById('btnOpenMap');
    var btnClose = document.getElementById('btnCloseMap');
    var btnCancel = document.getElementById('btnCancelMap');
    var btnUse = document.getElementById('btnUseLocation');
    var btnMine = document.getElementById('btnMyLocation');
    var mapStatus = document.getElementById('mapStatus');
    var addrText = document.getElementById('selectedAddressText');
    var coordsText = document.getElementById('selectedCoordsText');
    var geocodeWarn = document.getElementById('geocodeWarn');

    var fLat = document.getElementById('latitude');
    var fLng = document.getElementById('longitude');
    var fAddr = document.getElementById('address');
    var fCustAddr = document.getElementById('customer_address');

    // Default: Bandung (lihat footer kontak). Fallback bila customer belum memilih titik.
    var DEFAULT_LAT = -6.9175;
    var DEFAULT_LNG = 107.6191;

    var map = null;
    var marker = null;
    var picked = null; // {lat, lng}

    function num(v) {
        var n = parseFloat(v);
        return isFinite(n) ? n : null;
    }

    function validLatLng(lat, lng) {
        return lat !== null && lng !== null
            && lat >= -90 && lat <= 90
            && lng >= -180 && lng <= 180;
    }

    function initMap() {
        if (map) {
            setTimeout(function () { map.invalidateSize(); }, 80);
            return;
        }
        var startLat = num(fLat.value);
        var startLng = num(fLng.value);
        var center = validLatLng(startLat, startLng) ? [startLat, startLng] : [DEFAULT_LAT, DEFAULT_LNG];

        map = L.map('deliveryMap').setView(center, 16);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        marker = L.marker(center, { draggable: true }).addTo(map);
        picked = { lat: center[0], lng: center[1] };
        setStatus('Pin siap. Klik peta untuk memindahkan pin.');

        map.on('click', function (e) {
            picked = { lat: e.latlng.lat, lng: e.latlng.lng };
            marker.setLatLng(e.latlng);
            setStatus('Titik: ' + picked.lat.toFixed(6) + ', ' + picked.lng.toFixed(6));
        });
        marker.on('dragend', function () {
            var p = marker.getLatLng();
            picked = { lat: p.lat, lng: p.lng };
            setStatus('Titik: ' + picked.lat.toFixed(6) + ', ' + picked.lng.toFixed(6));
        });

        setTimeout(function () { map.invalidateSize(); }, 80);
    }

    function setStatus(msg) {
        if (mapStatus) mapStatus.textContent = msg;
    }

    function openModal() {
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        initMap();
    }

    function closeModal() {
        modal.hidden = true;
        document.body.style.overflow = '';
    }

    // Reverse geocoding via Nominatim (gratis, tanpa API key).
    // Gagal = tidak crash: lat/lng tetap disimpan, customer isi alamat manual.
    function reverseGeocode(lat, lng, done) {
        setStatus('Mencari alamat…');
        var url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat='
            + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng);
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                done(data && data.display_name ? data.display_name : null);
            })
            .catch(function () {
                done(null);
            });
    }

    btnOpen.addEventListener('click', openModal);
    btnClose.addEventListener('click', closeModal);
    btnCancel.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.hidden) closeModal();
    });

    if (btnMine) {
        btnMine.addEventListener('click', function () {
            if (!navigator.geolocation) {
                setStatus('Browser tidak mendukung lokasi. Geser pin manual ya.');
                return;
            }
            setStatus('Mencari lokasimu…');
            navigator.geolocation.getCurrentPosition(function (pos) {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                if (!validLatLng(lat, lng)) {
                    setStatus('Lokasi tidak valid. Geser pin manual ya.');
                    return;
                }
                picked = { lat: lat, lng: lng };
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 17);
                setStatus('Titik: ' + lat.toFixed(6) + ', ' + lng.toFixed(6));
            }, function () {
                setStatus('Lokasi tidak bisa diakses. Geser pin manual ya.');
            }, { timeout: 10000 });
        });
    }

    btnUse.addEventListener('click', function () {
        if (!picked || !validLatLng(picked.lat, picked.lng)) {
            setStatus('Pin belum valid. Klik peta dulu ya.');
            return;
        }
        var lat = Math.round(picked.lat * 1e7) / 1e7;
        var lng = Math.round(picked.lng * 1e7) / 1e7;
        btnUse.disabled = true;
        btnUse.textContent = 'Mencari Alamat…';

        reverseGeocode(lat, lng, function (displayName) {
            btnUse.disabled = false;
            btnUse.textContent = 'Gunakan Lokasi Ini';

            fLat.value = lat;
            fLng.value = lng;

            if (displayName) {
                fAddr.value = displayName;
                addrText.textContent = displayName;
                coordsText.textContent = lat + ', ' + lng;
                geocodeWarn.hidden = true;
                // Isi textarea manual juga supaya checkout tanpa langkah ekstra tetap valid.
                if (!fCustAddr.value.trim()) fCustAddr.value = displayName;
            } else {
                // Geocoding gagal: simpan koordinat, biarkan customer tulis alamat manual.
                fAddr.value = '';
                addrText.textContent = 'Titik tersimpan (' + lat + ', ' + lng + '). Alamat otomatis tidak ditemukan — tulis alamat manual di bawah ya.';
                coordsText.textContent = lat + ', ' + lng;
                geocodeWarn.textContent = 'Alamat otomatis tidak berhasil ditemukan, tapi titik lokasimu sudah tersimpan. Lengkapi alamat manual di bawah.';
                geocodeWarn.hidden = false;
                fCustAddr.focus();
            }
            closeModal();
        });
    });

    // Validasi ringan sebelum submit: cegah koordinat tidak valid terkirim.
    var form = document.getElementById('checkoutForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            var hasLat = fLat.value !== '' && fLat.value !== null;
            var hasLng = fLng.value !== '' && fLng.value !== null;
            if (hasLat || hasLng) {
                var la = num(fLat.value);
                var ln = num(fLng.value);
                if (!validLatLng(la, ln)) {
                    e.preventDefault();
                    alert('Koordinat lokasi tidak valid. Pilih ulang lewat peta atau kosongkan.');
                }
            }
        });
    }
})();
</script>
@endpush
