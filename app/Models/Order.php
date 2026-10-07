<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = [
        'pending',
        'approved',
        'cooking',
        'ready',
        'delivering',
        'delivered',
        'completed',
        'rejected',
    ];

    // Alur maju yang diizinkan admin (tanpa lompat status).
    public const NEXT_STATUS = [
        'approved' => 'cooking',
        'cooking' => 'ready',
        'ready' => 'delivering',
        'delivering' => 'delivered',
    ];

    public const TIMESTAMP_FOR_STATUS = [
        'approved' => 'approved_at',
        'cooking' => 'cooking_at',
        'ready' => 'ready_at',
        'delivering' => 'delivering_at',
        'delivered' => 'delivered_at',
        'completed' => 'completed_at',
        'rejected' => 'rejected_at',
    ];

    protected $fillable = [
        'order_code',
        'customer_name',
        'customer_phone',
        'customer_address',
        'address',
        'latitude',
        'longitude',
        'address_note',
        'notes',
        'total_amount',
        'status',
        'approved_at',
        'cooking_at',
        'ready_at',
        'delivering_at',
        'delivered_at',
        'completed_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'integer',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'approved_at' => 'datetime',
            'cooking_at' => 'datetime',
            'ready_at' => 'datetime',
            'delivering_at' => 'datetime',
            'delivered_at' => 'datetime',
            'completed_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function nextStatus(): ?string
    {
        return self::NEXT_STATUS[$this->status] ?? null;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'cooking' => 'Sedang Dimasak',
            'ready' => 'Siap Diantar',
            'delivering' => 'Sedang Diantar',
            'delivered' => 'Sudah Sampai',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }
}
