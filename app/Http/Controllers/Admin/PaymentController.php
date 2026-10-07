<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * APPROVE PEMBAYARAN & PESANAN — satu DB transaction, idempotent.
     * payment = paid, order (jika masih pending) = approved.
     */
    public function approve(Order $order): RedirectResponse
    {
        $order->load('payment');
        $payment = $order->payment;

        if (! $payment) {
            return back()->withErrors(['payment' => 'Order ini belum memiliki data pembayaran.']);
        }

        // Idempotent: klik dua kali / sudah lunas = sukses tanpa perubahan.
        if ($payment->status === 'paid') {
            return back()->with('success', $order->order_code.' sudah disetujui sebelumnya.');
        }

        if ($payment->status !== 'waiting_verification') {
            return back()->withErrors(['payment' => 'Hanya pembayaran yang menunggu verifikasi yang bisa disetujui.']);
        }

        DB::transaction(function () use ($order, $payment) {
            /** @var Payment $locked */
            $locked = Payment::whereKey($payment->id)->lockForUpdate()->first();
            /** @var Order $lockedOrder */
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->first();

            // Cek ulang di dalam lock: pengaman double-submit.
            if ($locked->status !== 'waiting_verification') {
                return;
            }

            $locked->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            if ($lockedOrder->status === 'pending') {
                $lockedOrder->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                ]);
            }
        });

        return back()->with('success', $order->order_code.' lunas & disetujui. Pesanan bisa lanjut dimasak.');
    }

    /**
     * TOLAK PEMBAYARAN — payment = rejected, order TIDAK berubah.
     * Customer bisa upload bukti baru (kembali ke waiting_verification).
     */
    public function reject(Order $order): RedirectResponse
    {
        $order->load('payment');
        $payment = $order->payment;

        if (! $payment) {
            return back()->withErrors(['payment' => 'Order ini belum memiliki data pembayaran.']);
        }

        if ($payment->status === 'rejected') {
            return back()->with('success', 'Pembayaran sudah ditolak sebelumnya.');
        }

        if ($payment->status !== 'waiting_verification') {
            return back()->withErrors(['payment' => 'Hanya pembayaran yang menunggu verifikasi yang bisa ditolak.']);
        }

        $payment->update(['status' => 'rejected']);

        return back()->with('success', 'Pembayaran ditolak. Customer bisa upload bukti baru.');
    }
}
