<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Halaman pembayaran customer (diakses via order_code, sama seperti tracking).
     */
    public function show(string $order_code): View
    {
        $order = Order::with(['items', 'payment'])
            ->where('order_code', $order_code)
            ->firstOrFail();

        $methods = PaymentMethod::active()->orderBy('name')->get();

        // Dipakai JS untuk render detail + tombol salin (tanpa arrow-fn di Blade).
        $methodsJson = $methods->map(function ($m) {
            return [
                'id' => $m->id,
                'type' => $m->type,
                'type_label' => $m->typeLabel(),
                'name' => $m->name,
                'provider' => $m->provider,
                'bank' => $m->bank_name,
                'detail' => $m->detailLabel(),
                'owner' => $m->account_name,
                'number' => $m->account_number,
                'qris' => $m->qris_image ? asset('storage/'.$m->qris_image) : null,
                'instructions' => $m->instructions,
            ];
        })->keyBy('id')->toJson();

        return view('payments.show', compact('order', 'methods', 'methodsJson'));
    }

    /**
     * Customer memilih metode + upload bukti transfer.
     * Status & nominal SELALU ditentukan server, bukan dari input.
     */
    public function store(Request $request, string $order_code): RedirectResponse
    {
        $order = Order::with('payment')->where('order_code', $order_code)->firstOrFail();

        if (in_array($order->status, ['rejected', 'completed'], true)) {
            return back()->withErrors(['payment' => 'Pesanan ini sudah tidak bisa dibayar.']);
        }

        $payment = $order->payment;

        // Bukti tidak boleh diganti saat menunggu verifikasi / sudah lunas.
        if ($payment && in_array($payment->status, ['waiting_verification', 'paid'], true)) {
            return back()->withErrors(['payment' => 'Bukti pembayaranmu sudah terkirim dan tidak bisa diganti.']);
        }

        $validated = $request->validate([
            'payment_method' => [
                'required',
                'integer',
                Rule::exists('payment_methods', 'id')->where('is_active', true),
            ],
            'proof_image' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'payment_method.required' => 'Pilih metode pembayaran dulu ya.',
            'payment_method.exists' => 'Metode pembayaran tidak valid / tidak aktif.',
            'proof_image.required' => 'Bukti transfer wajib diupload.',
            'proof_image.image' => 'Bukti transfer harus berupa gambar.',
            'proof_image.mimes' => 'Bukti transfer harus JPG, JPEG, PNG, atau WEBP.',
            'proof_image.max' => 'Ukuran bukti transfer maksimal 2MB.',
        ]);

        // Ambil ulang dari DB (abaikan apa pun selain id): snapshot anti-berubah.
        $method = PaymentMethod::where('id', $validated['payment_method'])
            ->where('is_active', true)
            ->firstOrFail();

        DB::transaction(function () use ($order, $payment, $method, $request) {
            $path = $request->file('proof_image')->store('payments', 'public');

            // Hapus bukti lama bila customer upload ulang setelah ditolak.
            if ($payment?->proof_image && Storage::disk('public')->exists($payment->proof_image)) {
                Storage::disk('public')->delete($payment->proof_image);
            }

            // QRIS di-snapshot sebagai COPY file per transaksi, sehingga histori
            // tetap menampilkan QRIS saat transaksi walau master diganti.
            // Bukti lama dihapus saat upload ulang (metode sama maupun ganti).
            if ($payment?->payment_qris_image && Storage::disk('public')->exists($payment->payment_qris_image)) {
                Storage::disk('public')->delete($payment->payment_qris_image);
            }
            $qrisSnapshot = $method->qris_image && Storage::disk('public')->exists($method->qris_image)
                ? Storage::disk('public')->putFile('payments/qris-snapshots', new \Illuminate\Http\File(Storage::disk('public')->path($method->qris_image)))
                : null;

            $data = [
                'payment_method_id' => $method->id,
                'payment_method_name' => $method->name,
                'payment_method_type' => $method->type,
                'payment_provider' => $method->provider,
                'payment_bank_name' => $method->bank_name,
                'payment_account_name' => $method->account_name,
                'payment_account_number' => $method->account_number,
                'payment_qris_image' => $qrisSnapshot,
                // Nominal dari total order (server-side), bukan input customer.
                'amount' => (int) $order->total_amount,
                'status' => 'waiting_verification',
                'proof_image' => $path,
                'paid_at' => null,
            ];

            if ($payment) {
                $payment->update($data);
            } else {
                $order->payment()->create($data);
            }
        });

        return redirect()
            ->route('payments.show', $order->order_code)
            ->with('success', 'Bukti pembayaran terkirim. Menunggu verifikasi admin.');
    }
}
