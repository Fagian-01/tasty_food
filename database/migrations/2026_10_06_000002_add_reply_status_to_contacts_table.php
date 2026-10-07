<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (! Schema::hasColumn('contacts', 'admin_reply')) {
                $table->text('admin_reply')->nullable()->after('message');
            }
            if (! Schema::hasColumn('contacts', 'replied_at')) {
                $table->timestamp('replied_at')->nullable()->after('admin_reply');
            }
            if (! Schema::hasColumn('contacts', 'status')) {
                $table->string('status', 20)->default('unread')->after('replied_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['admin_reply', 'replied_at', 'status']);
        });
    }
};
