<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Satu order = satu payment. Cascade: order dihapus -> payment ikut hapus.
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();
            // Nullable + nullOnDelete: histori payment (snapshot) tidak rusak
            // jika master metode pembayaran dihapus admin.
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            // Snapshot metode saat transaksi (anti-berubah jika master diubah).
            $table->string('payment_method_name')->nullable();
            $table->string('payment_account_name')->nullable();
            $table->string('payment_account_number')->nullable();
            // Nominal dari total order (server-side), bukan input customer.
            $table->unsignedBigInteger('amount')->default(0);
            // unpaid | waiting_verification | paid | rejected
            $table->string('status')->default('unpaid');
            $table->string('proof_image')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
