# VetPedia Drugs — Translation: `/drugs/diagnosis` + Disease Show

> Translate the `Drugs` diagnosis flow (index → refinement → results) and the linked disease show page from English-only DB/controller content to full **en/ar** support. Static UI strings are already translated via `lang/{en,ar}/drugs.php`; this milestone localizes the remaining **database content** and **controller-generated strings**, and aligns the seed data so the diagnosis engine actually returns real matches in both locales.

## Status

- **T1 (Schema)** — not started.
- **T2 (Models)** — not started.
- **T3 (Controllers)** — not started.
- **T4 (Views)** — not started.
- **T5 (Lang files)** — not started.
- **T6 (Seeders: ontology Arabic)** — not started.
- **T7 (Seeders: diagnosis data alignment)** — not started.
- **T8 (Seeders: disease content + payload)** — not started.
- **T9 (Verify)** — not started.

## Scope

- Diagnosis flow: `resources/views/drugs/diagnosis/{index,refinement,results}.blade.php` + `app/Http/Controllers/Drugs/DiagnosticController.php`.
- Disease show page: `resources/views/drugs/diseases/show.blade.php` + `app/Http/Controllers/Drugs/DiseaseController.php`.
- Shared: `App\Models\Drugs\*` ontology models, `lang/{en,ar}/drugs.php`, ontology + disease seeders.
- **In scope:** real Arabic content for ontology seeders, curated disease `name`/`name_ar`/`description`/`description_ar`, knowledge-payload Arabic fields, and seed-data alignment so diagnosis returns results.
- **Out of scope:** `App\Models\Disease` schema (already has `name_ar`/`description_ar`); Filament admin resources (Milestone 8 in `plan.md`); disease-article builder narrative strings.

## Assumptions / decisions

- [x] Add real Arabic to seeders (no admin UI exists for the ontology tables, so seeders are the only source).
- [x] Implement name selection via a `getLocalizedDisplayNameAttribute()` model accessor (reused in controller + views), falling back to English when Arabic is empty.
- [x] Scope includes the linked disease show page (hero, clinical signs, groups, host species, payload sections).
- [x] Fix seed data so `/drugs/diagnosis` actually produces matches (currently sign/disease name mismatches cause pivot links to silently skip).
- [x] Replace factory `fake()` disease content with curated real English + Arabic names and descriptions.
- [x] Translate the `knowledge_payload` sections too (postmortem/diagnosis/treatment/prevention Arabic fields + render).
- [x] Keep `DiseaseFactory` intact (still used by tests) — the curated dataset lives in the seeders.
- [x] Internal scoring/sorting keeps English enum values (`match_type`, `confidence`); the controller also emits translation keys for display.
- [x] Arabic review by a human before production release (translations written by agent).

---

## Milestone T1 — Database schema

- [ ] Create migration `add_arabic_display_names_to_drugs_ontology_tables` adding nullable string columns:
- [ ] `body_systems.display_name_ar`
- [ ] `anatomical_structures.display_name_ar`
- [ ] `findings.display_name_ar`
- [ ] `clinical_signs.display_name_ar`
- [ ] `host_species.display_name_ar`
- [ ] `differential_syndromes.name_ar`
- [ ] Run `php artisan migrate` and confirm the new columns exist (no changes to `diseases`).

## Milestone T2 — Models

- [ ] Add new columns to `$fillable` on `BodySystem` (`display_name_ar`).
- [ ] Add new columns to `$fillable` on `AnatomicalStructure` (`display_name_ar`).
- [ ] Add new columns to `$fillable` on `Finding` (`display_name_ar`).
- [ ] Add new columns to `$fillable` on `ClinicalSign` (`display_name_ar`).
- [ ] Add new columns to `$fillable` on `HostSpecies` (`display_name_ar`).
- [ ] Add new columns to `$fillable` on `DifferentialSyndrome` (`name_ar`).
- [ ] Add `getLocalizedDisplayNameAttribute(): string` accessor on each of the above (returns `display_name_ar`/`name_ar` when locale is `ar` and value present, else English).

## Milestone T3 — Controllers

- [ ] `DiagnosticController::index()` — add `localized_display_name` and localized `body_system` to the `$allSigns` map.
- [ ] `DiagnosticController::index()` — replace `'General Systemic Signs'` and `'General'` fallbacks with `__('drugs.diagnosis.general_systemic')` / `__('drugs.diagnosis.general_category')`.
- [ ] `DiagnosticController::diagnose()` — build `matched_signs` and `missing_key_findings` from `localized_display_name`.
- [ ] `DiagnosticController::diagnose()` — keep English `match_type`/`confidence` enums internally for scoring/sorting, but emit `match_type_key` + `confidence_key` translation keys for the view.
- [ ] `DiagnosticController::diagnose()` — localize all hardcoded `reasons[]` strings via `__('drugs.diagnosis.reasons.*')` with interpolated sign names.
- [ ] `DiagnosticController::diagnose()` — flash warning uses `__('drugs.diagnosis.warning')`.
- [ ] `DiagnosticController::refinedResults()` — build `matched_signs`/`missing_key_findings` from localized names; emit `match_type_key` + `confidence_key`; localize `reasons[]`.
- [ ] `Drugs\DiseaseController::show()` — group clinical signs by localized body system (`getLocalizedDisplayNameAttribute()`).

## Milestone T4 — Views

- [ ] `drugs/diagnosis/index.blade.php` — localized host-species `<option>` labels.
- [ ] `drugs/diagnosis/index.blade.php` — localized body-system group headings.
- [ ] `drugs/diagnosis/index.blade.php` — localized sign names (`localized_display_name`) and localized category chip.
- [ ] `drugs/diagnosis/index.blade.php` — extend `groupVisible`/`signVisible` JS `names` arrays to include Arabic terms so filtering works in `ar`.
- [ ] `drugs/diagnosis/refinement.blade.php` — localized sign names (`localized_display_name`).
- [ ] `drugs/diagnosis/results.blade.php` — disease title uses `name_ar ?: name`; description uses `description_ar ?: description`.
- [ ] `drugs/diagnosis/results.blade.php` — translate `match_type` + `confidence` badges from the emitted keys.
- [ ] `drugs/diseases/show.blade.php` — localized hero heading + subtitle (`name_ar`/`description_ar`).
- [ ] `drugs/diseases/show.blade.php` — localized clinical-sign names + body-system group headings.
- [ ] `drugs/diseases/show.blade.php` — localized host-species names.
- [ ] `drugs/diseases/show.blade.php` — render knowledge-payload Arabic fields (`display_name_ar`, `method_ar`, `intervention_ar`, `measure_ar`, `description_ar`) with English fallback.

## Milestone T5 — Lang files

- [ ] Add `diagnosis.match_types.*` keys to `lang/en/drugs.php` — Weak General / Strong / Moderate / Pathognomonic / Refined / Pathognomonic Reinforced.
- [ ] Add `diagnosis.match_types.*` keys to `lang/ar/drugs.php` — Arabic equivalents.
- [ ] Add `diagnosis.reasons.*` keys to `lang/en/drugs.php` — ~15 evidence strings.
- [ ] Add `diagnosis.reasons.*` keys to `lang/ar/drugs.php` — Arabic equivalents.
- [ ] Reuse existing `diagnosis.low/moderate/high` keys for confidence badges in both views.
- [ ] Add `diagnosis.general_systemic` + `diagnosis.general_category` fallback keys to `lang/{en,ar}/drugs.php`.
- [ ] Verify no view passes a missing/misspelled key (run `php artisan view:cache` + smoke test both locales).

## Milestone T6 — Seeders: ontology Arabic

- [ ] `BodySystemSeeder` — add real Arabic `display_name_ar`; make idempotent (`updateOrCreate`).
- [ ] `AnatomicalStructureSeeder` — add real Arabic `display_name_ar`; idempotent.
- [ ] `FindingSeeder` — add real Arabic `display_name_ar`; idempotent.
- [ ] `HostSpeciesSeeder` — add real Arabic `display_name_ar`; idempotent.
- [ ] `ModifierSeeder` — add real Arabic `display_name_ar` (used indirectly via signs); idempotent.

## Milestone T7 — Seeders: diagnosis data alignment

- [ ] Expand `ClinicalSignSeeder` from 7 signs to cover every canonical name referenced by `DiseaseClinicalSignSeeder` (~25: oral vesicle, tongue vesicle, tachypnea, lymphadenitis, hoof sloughing, etc.), each with `display_name` + `display_name_ar`.
- [ ] Ensure anatomical structures + body systems exist for every new sign (add to `BodySystemSeeder`/`AnatomicalStructureSeeder` if missing).
- [ ] Confirm pivot links now attach: `count(disease_clinical_sign)` matches expected disease×sign rows after reseeding.

## Milestone T8 — Seeders: disease content + payload

- [ ] `DiseaseSeeder` — replace `Disease::factory()->count(30)` with a curated dataset of the diseases actually referenced by `DiseaseClinicalSignSeeder`/`DiseaseClassificationSeeder` (FMD, Mastitis, Coccidiosis, Salmonellosis, Pneumonia/CRD family, Strangles, Septicaemia, Foot rot, Colibacillosis, etc.), each with real `name`, `name_ar`, `description`, `description_ar`, `slug`, `is_active`.
- [ ] Make `DiseaseSeeder` idempotent (`updateOrCreate` by `name`) so it re-runs safely.
- [ ] `DiseaseKnowledgePayloadSeeder` — add Arabic variants to payload items (`display_name_ar`, `method_ar`, `intervention_ar`, `measure_ar`, `description_ar`) and store them on the payload.
- [ ] Confirm `DiseaseClassificationSeeder` and `DiseaseHostSpeciesSeeder` still attach after the disease list change.
- [ ] Keep `DiseaseFactory` untouched (still consumed by tests).

## Milestone T9 — Verify

- [ ] `php artisan migrate` runs clean.
- [ ] `php artisan db:seed` (fresh + re-run) is idempotent with no FK errors.
- [ ] Spot-check pivot counts via `php artisan tinker --execute` / `database-query` (sign↔disease links actually attached).
- [ ] Walk the full flow in **EN**: pick signs → refinement → results → disease show, confirm content renders.
- [ ] Walk the full flow in **AR** (`/ar/drugs/diagnosis`): confirm RTL, Arabic font, translated badges/reasons, localized names; English fallback when Arabic null.
- [ ] Verify JS sign filter matches Arabic names in `ar` locale.
- [ ] `vendor/bin/pint --format agent` on all changed PHP.
- [ ] `php artisan test --compact` passes.
- [ ] `npm run build` if any Blade/JS changes need compiling.