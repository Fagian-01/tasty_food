<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(): View
    {
        return view('admin.payment-methods.index', [
            'methods' => PaymentMethod::withCount('payments')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.payment-methods.create', [
            'types' => PaymentMethod::TYPE_LABELS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMethod($request);

        if ($request->hasFile('qris_image')) {
            $validated['qris_image'] = $request->file('qris_image')->store('payment-methods/qris', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        PaymentMethod::create($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan!');
    }

    public function edit(PaymentMethod $payment_method): View
    {
        return view('admin.payment-methods.edit', [
            'method' => $payment_method,
            'types' => PaymentMethod::TYPE_LABELS,
        ]);
    }

    public function update(Request $request, PaymentMethod $payment_method): RedirectResponse
    {
        $validated = $this->validateMethod($request, $payment_method);

        if ($request->hasFile('qris_image')) {
            $this->deleteQrisIfUnused($payment_method->qris_image, $payment_method->id);
            $validated['qris_image'] = $request->file('qris_image')->store('payment-methods/qris', 'public');
        } else {
            // Tanpa upload baru: pertahankan gambar lama (wajib hanya saat create
            // atau saat metode QRIS belum punya gambar — sudah ditangani validasi).
            unset($validated['qris_image']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        $payment_method->update($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui!');
    }

    public function destroy(PaymentMethod $payment_method): RedirectResponse
    {
        // Aman: payments.payment_method_id nullOnDelete, snapshot histori tetap utuh.
        $this->deleteQrisIfUnused($payment_method->qris_image, $payment_method->id);
        $payment_method->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran dihapus. Histori pembayaran lama tetap aman.');
    }

    /**
     * Validasi conditional per tipe (server-side, JS hanya pemanis).
     *
     * DANA:    account_name + account_number wajib.
     * E-WALLET: provider + account_name + account_number wajib.
     * BANK:    bank_name + account_name + account_number wajib.
     * QRIS:    account_name (merchant) + qris_image wajib saat create;
     *          saat edit boleh memakai gambar lama.
     */
    private function validateMethod(Request $request, ?PaymentMethod $existing = null): array
    {
        $qrisRule = $existing && $existing->qris_image
            ? 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048'
            : 'required|image|mimes:jpeg,jpg,png,webp|max:2048';

        return $request->validate([
            'type' => ['required', Rule::in(PaymentMethod::TYPES)],
            'name' => 'required|string|max:255',
            'provider' => 'nullable|string|max:100|required_if:type,ewallet',
            'bank_name' => 'nullable|string|max:100|required_if:type,bank',
            'account_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:100|required_unless:type,qris',
            'qris_image' => $request->input('type') === 'qris'
                ? $qrisRule
                : 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'instructions' => 'nullable|string|max:2000',
        ], [
            'type.required' => 'Pilih tipe pembayaran dulu ya.',
            'type.in' => 'Tipe pembayaran tidak valid.',
            'provider.required_if' => 'Provider e-wallet wajib diisi (mis. GoPay, OVO).',
            'bank_name.required_if' => 'Nama bank wajib diisi (mis. BCA, BRI).',
            'account_number.required_unless' => 'Nomor rekening / nomor akun wajib diisi.',
            'qris_image.required' => 'Gambar QRIS wajib diupload untuk tipe QRIS.',
            'qris_image.image' => 'QRIS harus berupa gambar.',
            'qris_image.mimes' => 'QRIS harus JPG, JPEG, PNG, atau WEBP.',
            'qris_image.max' => 'Ukuran gambar QRIS maksimal 2MB.',
        ]);
    }

    /**
     * Hapus file QRIS hanya jika tidak dipakai metode lain.
     * Mencegah penghapusan file yang masih dirujuk payment method lain.
     * $exceptId: abaikan metode yang sedang diupdate/dihapus (record-nya
     * masih ada di DB saat pengecekan, tapi akan diganti/dihapus).
     */
    private function deleteQrisIfUnused(?string $path, ?int $exceptId = null): void
    {
        if (! $path) {
            return;
        }

        $query = PaymentMethod::where('qris_image', $path);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        if (! $query->exists() && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
