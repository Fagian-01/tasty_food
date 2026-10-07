<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    // Tipe konsisten di backend + frontend + database.
    public const TYPES = ['dana', 'ewallet', 'bank', 'qris'];

    public const TYPE_LABELS = [
        'dana' => 'DANA',
        'ewallet' => 'E-Wallet',
        'bank' => 'Bank',
        'qris' => 'QRIS',
    ];

    protected $fillable = [
        'type',
        'name',
        'provider',
        'bank_name',
        'account_name',
        'account_number',
        'qris_image',
        'instructions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** Customer hanya boleh melihat metode aktif. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->type] ?? strtoupper((string) $this->type);
    }

    /** Label pelengkap: provider (e-wallet) / nama bank (bank). */
    public function detailLabel(): ?string
    {
        return match ($this->type) {
            'ewallet' => $this->provider,
            'bank' => $this->bank_name,
            default => null,
        };
    }
}
