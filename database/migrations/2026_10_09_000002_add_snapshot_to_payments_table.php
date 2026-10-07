<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Snapshot tipe saat transaksi — histori tidak berubah walau master diedit.
            // QRIS snapshot berupa COPY file (payments/qris-snapshots/), bukan
            // referensi ke master, agar histori menampilkan QRIS saat transaksi.
            $table->string('payment_method_type', 20)->nullable()->after('payment_method_name');
            $table->string('payment_provider', 100)->nullable()->after('payment_method_type');
            $table->string('payment_bank_name', 100)->nullable()->after('payment_provider');
            $table->string('payment_qris_image')->nullable()->after('payment_account_number');
        });

        // Transaksi lama (sebelum fitur tipe) dianggap dana — konsisten dengan backfill master.
        DB::table('payments')->whereNull('payment_method_type')->update(['payment_method_type' => 'dana']);
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method_type',
                'payment_provider',
                'payment_bank_name',
                'payment_qris_image',
            ]);
        });
    }
};
