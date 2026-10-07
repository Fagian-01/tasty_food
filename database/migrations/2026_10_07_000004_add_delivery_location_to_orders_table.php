<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hasil reverse geocoding dari titik map (nullable: order lama + checkout manual tetap aman).
            $table->text('address')->nullable()->after('customer_address');
            // Koordinat titik yang dipilih customer di map (nullable: tidak wajib jika checkout manual).
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 11, 7)->nullable()->after('latitude');
            // Detail tambahan yang ditulis customer secara manual (boleh kosong).
            $table->text('address_note')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['address', 'latitude', 'longitude', 'address_note']);
        });
    }
};
