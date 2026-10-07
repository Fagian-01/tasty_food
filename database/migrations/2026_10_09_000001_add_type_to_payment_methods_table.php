<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            // dana | ewallet | bank | qris. Default dana agar baris lama tetap valid.
            $table->string('type', 20)->default('dana')->after('name');
            $table->string('provider', 100)->nullable()->after('type');
            $table->string('bank_name', 100)->nullable()->after('provider');
            $table->string('qris_image')->nullable()->after('account_number');

            $table->index('type');
        });

        // Kolom akun jadi nullable untuk tipe QRIS (tanpa doctrine/dbal: raw SQL MySQL).
        // QRIS tidak butuh nomor rekening/e-wallet.
        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE `payment_methods` MODIFY `account_name` VARCHAR(255) NULL, MODIFY `account_number` VARCHAR(255) NULL');
        }

        // Backfill data lama — JANGAN hapus/duplikat: DANA → dana, bank dikenal → bank.
        $banks = ['BCA', 'BRI', 'BNI', 'MANDIRI', 'BTN', 'CIMB', 'DANAMON', 'PERMATA', 'BSI'];
        DB::table('payment_methods')->orderBy('id')->chunkById(100, function ($rows) use ($banks) {
            foreach ($rows as $row) {
                $upper = strtoupper(trim((string) $row->name));
                if (in_array($upper, $banks, true) || str_starts_with($upper, 'BANK ')) {
                    DB::table('payment_methods')->where('id', $row->id)->update([
                        'type' => 'bank',
                        'bank_name' => $row->name,
                    ]);
                } else {
                    DB::table('payment_methods')->where('id', $row->id)->update(['type' => 'dana']);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn(['type', 'provider', 'bank_name', 'qris_image']);
        });
    }
};
