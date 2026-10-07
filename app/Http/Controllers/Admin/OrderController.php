<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = Order::withCount('items')->latest();

        if ($status && in_array($status, Order::STATUSES, true)) {
            $query->where('status', $status);
        } else {
            $status = null;
        }

        return view('admin.orders.index', [
            'orders' => $query->paginate(20)->withQueryString(),
            'status' => $status,
            'statuses' => Order::STATUSES,
            'pendingCount' => Order::where('status', 'pending')->count(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items.menu', 'payment']);

        return view('admin.orders.show', compact('order'));
    }

    public function approve(Order $order): RedirectResponse
    {
        // Jalur approve order terpisah DINONAKTIFKAN — flow baru memakai
        // SATU tombol "APPROVE PESANAN & PEMBAYARAN" (Admin\PaymentController).
        // Endpoint lama dipertahankan agar tidak 404, tapi selalu ditolak
        // dengan arahan ke tombol yang benar. Tidak ada perubahan data.
        if ($order->status === 'pending') {
            $order->loadMissing('payment');
            if (($order->payment?->status ?? 'unpaid') === 'waiting_verification') {
                return back()->withErrors(['order' => 'Gunakan tombol APPROVE PESANAN & PEMBAYARAN untuk memverifikasi pembayaran ini.']);
            }
        }

        return back()->withErrors(['order' => 'Approval order terpisah sudah tidak berlaku. Verifikasi pembayaran melalui tombol APPROVE PESANAN & PEMBAYARAN.']);
    }

    public function reject(Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return back()->withErrors(['order' => 'Hanya pesanan pending yang bisa ditolak.']);
        }

        $order->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        return back()->with('success', $order->order_code.' ditolak.');
    }

    public function advance(Order $order): RedirectResponse
    {
        $next = $order->nextStatus();

        if ($next === null) {
            return back()->withErrors(['order' => 'Status ini tidak bisa dimajukan lagi.']);
        }

        $timestampField = Order::TIMESTAMP_FOR_STATUS[$next];

        // Guard dapur (server-side, bukan sekadar UI): TIDAK ada jalan ke
        // cooking kecuali payment SUDAH paid. Berlaku untuk order baru
        // (punya baris payment) maupun order lawas (tanpa baris payment →
        // ditolak juga, karena flow baru mewajibkan bayar dulu).
        // Tahap setelah cooking tidak butuh guard tambahan: tidak mungkin
        // sampai sana tanpa lewat gerbang paid ini.
        if ($next === 'cooking') {
            $order->loadMissing('payment');
            if (($order->payment?->status ?? 'unpaid') !== 'paid') {
                return back()->withErrors(['order' => 'Tidak bisa masuk dapur sebelum pembayaran LUNAS (paid). Verifikasi pembayaran dulu via APPROVE PESANAN & PEMBAYARAN.']);
            }
            if ($order->status !== 'approved') {
                return back()->withErrors(['order' => 'Order harus berstatus APPROVED sebelum dimasak.']);
            }
        }

        $order->update([
            'status' => $next,
            $timestampField => now(),
        ]);

        return back()->with('success', $order->order_code.' → '.$order->statusLabel().'.');
    }
}
