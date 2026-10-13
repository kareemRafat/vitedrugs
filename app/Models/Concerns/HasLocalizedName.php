<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Gives a model a `localized_name` that prefers the Arabic column.
 *
 * Most catalogued models carry a `name` / `name_ar` pair but no accessor, so
 * every view that renders one re-implements the same three-line ternary — and
 * gets it subtly wrong in one place or another. `Disease` had it inline; this is
 * that accessor, moved somewhere it can be shared.
 *
 * Falls back to the English name when the Arabic one is empty rather than
 * rendering an empty label, which on an Arabic page is worse than showing the
 * English term: a vet looking for Coccidiosis will recognise the English name and
 * will not recognise nothing at all.
 *
 * @mixin Model
 */
trait HasLocalizedName
{
    public function getLocalizedNameAttribute(): string
    {
        $arabic = $this->getAttribute('name_ar');

        return app()->getLocale() === 'ar' && filled($arabic)
            ? (string) $arabic
            : (string) $this->getAttribute('name');
    }
}
