<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Purely additive. `subject` is deliberately left untouched so it keeps its
     * NOT NULL constraint — the landing form stores the visitor-facing category
     * label there as a display snapshot, while `category` holds the stable enum
     * value used for filtering.
     */
    public function up(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->string('role')->nullable()->after('email');
            $table->string('category')->nullable()->index()->after('subject');
            $table->char('locale', 2)->default('en')->after('category');
            $table->boolean('is_read')->default(false)->index()->after('locale');
            $table->timestamp('read_at')->nullable()->after('is_read');
        });
    }

    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['is_read']);
            $table->dropColumn(['role', 'category', 'locale', 'is_read', 'read_at']);
        });
    }
};
