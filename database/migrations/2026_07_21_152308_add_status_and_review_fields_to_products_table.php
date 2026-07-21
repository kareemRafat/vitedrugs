<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('is_active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('status');
            $table->text('admin_notes')->nullable()->after('created_by');
            $table->timestamp('reviewed_at')->nullable()->after('admin_notes');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['status', 'created_by', 'admin_notes', 'reviewed_at']);
        });
    }
};
