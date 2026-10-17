<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Photographs of a finding.
     *
     * Modelled directly on `product_images` rather than reaching for a media
     * package. This application has exactly one image table today and it looks
     * like this one; a second pattern for the same job would be worse than
     * either one on its own.
     *
     * `finding_id` is a plain `foreignId` and not a `foreignUlid`, because
     * `findings` is the one domain model in this project keyed by a bigint
     * auto-increment rather than a ULID — `Finding` does not use `HasUlids`.
     *
     * No `domain` column either. A finding has no domain of its own: it belongs
     * to poultry, cattle or both only through the diseases that reach it. A
     * photograph of a liver is not species-specific, so splitting it per part
     * would mean uploading the same file twice.
     */
    public function up(): void
    {
        Schema::create('finding_images', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->foreignId('finding_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('image');

            /**
             * A gross-lesion photograph without a label is close to useless on
             * an atlas — "liver, pale and mottled" is the finding, and the
             * picture only illustrates it. Bilingual because the atlas and the
             * whole site render in Arabic, and an English-only caption on an
             * Arabic tile reads as an unfinished page.
             */
            $table->string('caption')->nullable();
            $table->string('caption_ar')->nullable();

            $table->integer('sort_order')
                ->default(0);

            $table->timestamps();

            /**
             * The atlas eager-loads `images` across a whole page of 48 findings
             * and reads them in order, so the composite index serves both the
             * WHERE and the ORDER BY from one place.
             */
            $table->index(['finding_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finding_images');
    }
};
