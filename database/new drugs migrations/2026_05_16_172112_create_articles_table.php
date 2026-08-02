<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {

            $table->id();

            // Core Content
            $table->string('title');
            $table->string('slug')->unique();

            $table->text('summary')->nullable();
            $table->longText('content')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Media
            $table->string('featured_image')->nullable();

            // Publishing
            $table->enum('status', ['draft', 'published'])
                ->default('draft');

            $table->timestamp('published_at')->nullable();

            // Polymorphic Relation
            $table->morphs('articleable');

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
