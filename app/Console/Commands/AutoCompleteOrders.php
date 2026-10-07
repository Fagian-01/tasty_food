<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class AutoCompleteOrders extends Command
{
    protected $signature = 'orders:auto-complete';

    protected $description = 'Ubah order delivered > 1 jam menjadi completed (idempotent).';

    public function handle(): int
    {
        // Idempotent: hanya sentuh status delivered yang delivered_at-nya sudah lewat 1 jam.
        // Order completed/rejected tidak pernah diubah.
        $count = Order::where('status', 'delivered')
            ->where('delivered_at', '<=', now()->subHour())
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
                'updated_at' => now(),
            ]);

        $this->info("Auto-completed {$count} order(s).");

        return self::SUCCESS;
    }
}
