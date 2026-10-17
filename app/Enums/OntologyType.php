<?php

namespace App\Enums;

use App\Models\LargeAnimals\Finding;

/**
 * What a finding is: something you observe on a living bird, or something you
 * find when you open the carcass.
 *
 * This is a discriminator on a shared table, not a table of its own. A lesion and
 * a clinical sign are the same noun in a different context — a finding plus an
 * anatomical structure plus a modifier — and they reach diseases through the same
 * `findings → clinical_signs → disease_clinical_sign → diseases` chain. Splitting
 * them into two tables would force every diagnostic query to union over both.
 *
 * It was a nullable string, and the nullability was the bug: `Disease::signItems()`
 * read a null as a sign, `OrganAttachmentService` read it as a *lesion*, and
 * `LesionAtlasService` read it as neither — so the same finding was simultaneously
 * on the body map under "lesions" and missing from the atlas entirely.
 *
 * @see Finding::$casts
 */
enum OntologyType: string
{
    case Lesion = 'lesion';
    case Sign = 'sign';

    /**
     * Badges on the Filament table and view page, so the two are distinguishable
     * at a glance in a list of 1,089 rows where both sit under a heading called
     * "Findings".
     */
    public function color(): string
    {
        return match ($this) {
            self::Lesion => 'danger',
            self::Sign => 'info',
        };
    }
}
