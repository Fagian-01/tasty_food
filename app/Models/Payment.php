<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    // Status payment TERPISAH dari status order.
    public const STATUSES = [
        'unpaid',
        'waiting_verification',
        'paid',
        'rejected',
    ];

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'payment_method_name',
        'payment_method_type',
        'payment_provider',
        'payment_bank_name',
        'payment_account_name',
        'payment_account_number',
        'payment_qris_image',
        'amount',
        'status',
        'proof_image',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Bersihkan file milik transaksi ini saat baris payment dihapus
        // (order dihapus / cascade). Snapshot QRIS adalah copy per transaksi,
        // jadi aman dihapus tanpa menyentuh master maupun transaksi lain.
        static::deleting(function (Payment $payment) {
            foreach (['proof_image', 'payment_qris_image'] as $field) {
                $path = $payment->{$field};
                if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                }
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'unpaid' => 'Belum Bayar',
            'waiting_verification' => 'Menunggu Verifikasi',
            'paid' => 'Lunas',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function typeLabel(): string
    {
        return PaymentMethod::TYPE_LABELS[$this->payment_method_type]
            ?? ($this->payment_method_type ? strtoupper($this->payment_method_type) : '—');
    }
}
