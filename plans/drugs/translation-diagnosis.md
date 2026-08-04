# VetPedia Drugs — Translation: `/drugs/diagnosis` + Disease Show

> Translate the `Drugs` diagnosis flow (index → refinement → results) and the linked disease show page from English-only DB/controller content to full **en/ar** support. Static UI strings are already translated via `lang/{en,ar}/drugs.php`; this milestone localizes the remaining **database content** and **controller-generated strings**, and aligns the seed data so the diagnosis engine actually returns real matches in both locales.

## Status

- **T1 (Schema)** — ✅ done. Migration `2026_08_05_000000_add_arabic_display_names_to_drugs_ontology_tables.php` created; columns applied via `php artisan migrate` (see `plans/drugs/fix-db-reconciliation.md` M3).
- **T2 (Models)** — ✅ done. `display_name_ar`/`name_ar` added to `$fillable` + `getLocalizedDisplayNameAttribute()` on all 6 ontology models.
- **T3 (Controllers)** — ✅ done. `DiagnosticController` localizes species/signs/body systems/categories, emits `match_type_key`/`confidence_key`, localizes all `reasons[]`, localized `matched_signs`/`missing_key_findings`, localized warning. `DiseaseController::show()` groups by localized body system.
- **T4 (Views)** — ✅ done. `index` (localized species, groups, signs, categories, JS filter matches Arabic), `refinement` (localized signs + translated match-type badge), `results` (localized names/descriptions/badges), `diseases/show` (localized hero, signs, groups, host species, payload `_ar` fields with fallback).
- **T5 (Lang files)** — ✅ done. `match_types`, `confidences`, `reasons`, `categories`, `general_systemic`, `general_category` added to `lang/{en,ar}/drugs.php`.
- **T6 (Seeders: ontology Arabic)** — ✅ done. `BodySystem` (19 live systems + legacy aliases), `AnatomicalStructure`, `Finding`, `HostSpecies`, `Modifier` seeders now carry Arabic and are idempotent (`firstOrCreate` + Arabic-only update, non-destructive to existing rows).
- **T7 (Seeders: diagnosis data alignment)** — ✅ done. `ClinicalSignSeeder` expanded to all 37 canonical signs referenced by `DiseaseClinicalSignSeeder`, with Arabic + anatomy mapping, idempotent.
- **T8 (Seeders: disease content + payload)** — ✅ done. `DiseaseSeeder` curated (20 real diseases with `name_ar`/`description`/`description_ar`, idempotent by name); `DiseaseKnowledgePayloadSeeder` generates `_ar` fields for every payload section.
- **T9 (Verify)** — 🔄 in progress. Runs after DB reconciliation (M1–M6 in `fix-db-reconciliation.md`): migrate → reseed → smoke test both locales → tests.

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

- [x] Create migration `add_arabic_display_names_to_drugs_ontology_tables` adding nullable string columns:
- [x] `body_systems.display_name_ar`
- [x] `anatomical_structures.display_name_ar`
- [x] `findings.display_name_ar`
- [x] `clinical_signs.display_name_ar`
- [x] `host_species.display_name_ar`
- [x] `differential_syndromes.name_ar`
- [x] Run `php artisan migrate` and confirm the new columns exist (no changes to `diseases`).

## Milestone T2 — Models

- [x] Add new columns to `$fillable` on `BodySystem` (`display_name_ar`).
- [x] Add new columns to `$fillable` on `AnatomicalStructure` (`display_name_ar`).
- [x] Add new columns to `$fillable` on `Finding` (`display_name_ar`).
- [x] Add new columns to `$fillable` on `ClinicalSign` (`display_name_ar`).
- [x] Add new columns to `$fillable` on `HostSpecies` (`display_name_ar`).
- [x] Add new columns to `$fillable` on `DifferentialSyndrome` (`name_ar`).
- [x] Add `getLocalizedDisplayNameAttribute(): string` accessor on each of the above (returns `display_name_ar`/`name_ar` when locale is `ar` and value present, else English).

## Milestone T3 — Controllers

- [x] `DiagnosticController::index()` — add `localized_display_name` and localized `body_system` to the `$allSigns` map.
- [x] `DiagnosticController::index()` — replace `'General Systemic Signs'` and `'General'` fallbacks with `__('drugs.diagnosis.general_systemic')` / `__('drugs.diagnosis.general_category')`.
- [x] `DiagnosticController::diagnose()` — build `matched_signs` and `missing_key_findings` from `localized_display_name`.
- [x] `DiagnosticController::diagnose()` — keep English `match_type`/`confidence` enums internally for scoring/sorting, but emit `match_type_key` + `confidence_key` translation keys for the view.
- [x] `DiagnosticController::diagnose()` — localize all hardcoded `reasons[]` strings via `__('drugs.diagnosis.reasons.*')` with interpolated sign names.
- [x] `DiagnosticController::diagnose()` — flash warning uses `__('drugs.diagnosis.warning')`.
- [x] `DiagnosticController::refinedResults()` — build `matched_signs`/`missing_key_findings` from localized names; emit `match_type_key` + `confidence_key`; localize `reasons[]`.
- [x] `Drugs\DiseaseController::show()` — group clinical signs by localized body system (`getLocalizedDisplayNameAttribute()`).

## Milestone T4 — Views

- [x] `drugs/diagnosis/index.blade.php` — localized host-species `<option>` labels.
- [x] `drugs/diagnosis/index.blade.php` — localized body-system group headings.
- [x] `drugs/diagnosis/index.blade.php` — localized sign names (`localized_display_name`) and localized category chip.
- [x] `drugs/diagnosis/index.blade.php` — extend `groupVisible`/`signVisible` JS `names` arrays to include Arabic terms so filtering works in `ar`.
- [x] `drugs/diagnosis/refinement.blade.php` — localized sign names (`localized_display_name`).
- [x] `drugs/diagnosis/results.blade.php` — disease title uses `name_ar ?: name`; description uses `description_ar ?: description`.
- [x] `drugs/diagnosis/results.blade.php` — translate `match_type` + `confidence` badges from the emitted keys.
- [x] `drugs/diseases/show.blade.php` — localized hero heading + subtitle (`name_ar`/`description_ar`).
- [x] `drugs/diseases/show.blade.php` — localized clinical-sign names + body-system group headings.
- [x] `drugs/diseases/show.blade.php` — localized host-species names.
- [x] `drugs/diseases/show.blade.php` — render knowledge-payload Arabic fields (`display_name_ar`, `method_ar`, `intervention_ar`, `measure_ar`, `description_ar`) with English fallback.

## Milestone T5 — Lang files

- [x] Add `diagnosis.match_types.*` keys to `lang/en/drugs.php` — Weak General / Strong / Moderate / Pathognomonic / Refined / Pathognomonic Reinforced.
- [x] Add `diagnosis.match_types.*` keys to `lang/ar/drugs.php` — Arabic equivalents.
- [x] Add `diagnosis.reasons.*` keys to `lang/en/drugs.php` — ~15 evidence strings.
- [x] Add `diagnosis.reasons.*` keys to `lang/ar/drugs.php` — Arabic equivalents.
- [x] Reuse existing `diagnosis.low/moderate/high` keys for confidence badges in both views.
- [x] Add `diagnosis.general_systemic` + `diagnosis.general_category` fallback keys to `lang/{en,ar}/drugs.php`.
- [x] Verify no view passes a missing/misspelled key (run `php artisan view:cache` + smoke test both locales).

## Milestone T6 — Seeders: ontology Arabic

- [x] `BodySystemSeeder` — add real Arabic `display_name_ar`; make idempotent (`updateOrCreate`).
- [x] `AnatomicalStructureSeeder` — add real Arabic `display_name_ar`; idempotent.
- [x] `FindingSeeder` — add real Arabic `display_name_ar`; idempotent.
- [x] `HostSpeciesSeeder` — add real Arabic `display_name_ar`; idempotent.
- [x] `ModifierSeeder` — add real Arabic `display_name_ar` (used indirectly via signs); idempotent.

## Milestone T7 — Seeders: diagnosis data alignment

- [x] Expand `ClinicalSignSeeder` from 7 signs to cover every canonical name referenced by `DiseaseClinicalSignSeeder` (~25: oral vesicle, tongue vesicle, tachypnea, lymphadenitis, hoof sloughing, etc.), each with `display_name` + `display_name_ar`.
- [x] Ensure anatomical structures + body systems exist for every new sign (add to `BodySystemSeeder`/`AnatomicalStructureSeeder` if missing).
- [x] Confirm pivot links now attach: `count(disease_clinical_sign)` matches expected disease×sign rows after reseeding.

## Milestone T8 — Seeders: disease content + payload

- [x] `DiseaseSeeder` — replace `Disease::factory()->count(30)` with a curated dataset of the diseases actually referenced by `DiseaseClinicalSignSeeder`/`DiseaseClassificationSeeder` (FMD, Mastitis, Coccidiosis, Salmonellosis, Pneumonia/CRD family, Strangles, Septicaemia, Foot rot, Colibacillosis, etc.), each with real `name`, `name_ar`, `description`, `description_ar`, `slug`, `is_active`.
- [x] Make `DiseaseSeeder` idempotent (`updateOrCreate` by `name`) so it re-runs safely.
- [x] `DiseaseKnowledgePayloadSeeder` — add Arabic variants to payload items (`display_name_ar`, `method_ar`, `intervention_ar`, `measure_ar`, `description_ar`) and store them on the payload.
- [x] Confirm `DiseaseClassificationSeeder` and `DiseaseHostSpeciesSeeder` still attach after the disease list change.
- [x] Keep `DiseaseFactory` untouched (still consumed by tests).

## Milestone T9 — Verify

- [x] `php artisan migrate` runs clean.
- [x] `php artisan db:seed` (fresh + re-run) is idempotent with no FK errors.
- [x] Spot-check pivot counts via `php artisan tinker --execute` / `database-query` (sign↔disease links actually attached).
- [x] Walk the full flow in **EN**: pick signs → refinement → results → disease show, confirm content renders.
- [x] Walk the full flow in **AR** (`/ar/drugs/diagnosis`): confirm RTL, Arabic font, translated badges/reasons, localized names; English fallback when Arabic null.
- [x] Verify JS sign filter matches Arabic names in `ar` locale.
- [x] `vendor/bin/pint --format agent` on all changed PHP.
- [x] `php artisan test --compact` passes.
- [ ] `npm run build` if any Blade/JS changes need compiling. (No Vite asset changes were made — Blade inline scripts only.)