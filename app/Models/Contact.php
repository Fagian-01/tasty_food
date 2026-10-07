<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'admin_reply',
        'replied_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'replied_at' => 'datetime',
        ];
    }

    public function isUnread(): bool
    {
        return $this->status === 'unread';
    }

    public function isReplied(): bool
    {
        return $this->status === 'replied';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'read' => 'Sudah Dibaca',
            'replied' => 'Sudah Dibalas',
            default => 'Belum Dibaca',
        };
    }
}
