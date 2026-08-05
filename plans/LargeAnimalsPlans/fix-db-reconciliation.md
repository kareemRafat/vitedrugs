# VetPedia Drugs — Fix Frankensteined DB + Reconcile Migrations

> The `vetdrugs` database was imported with a partial `migrations` table (only base-app migrations recorded) while all `2026_08_01*`/`2026_08_03*` knowledge-base tables already exist. As a result `php artisan migrate` fails on "table exists", three objects are genuinely missing (`disease_microorganism`, `veterinary_projects`, `diseases.knowledge_payload`), pivot tables hold orphaned bigint `disease_id`s, and the fish page 500s with `Table 'vetdrugs.disease_microorganism' doesn't exist`. This plan repairs the DB **without touching real product/company data** (4575 products, 521 companies stay intact).

## Status

- **R1 (Backup)** — ✅ done. `storage/app/backups/vetdrugs_20260805.sql` (3,945,086 bytes, 76 CREATE TABLE blocks, contains products/companies).
- **R2 (Reconcile migrations)** — ✅ done. 37 missing records inserted as batch 20 (`2026_07_13_120540` role migration + all `2026_08_01_*` except `000023`, verified against `information_schema` columns/indexes first). `migrate:status` showed only the 5 genuine migrations pending.
- **R3 (Run missing migrations)** — ✅ done. `php artisan migrate` ran exactly: `000023_add_knowledge_payload`, `111709_create_disease_microorganism`, `111710_create_veterinary_projects`, `114057_fix_disease_relation_columns_to_ulid`, `2026_08_05_000000_add_arabic_display_names`. Plus `2026_08_05_000001_add_display_name_ar_to_modifiers` (column missing for the Modifier backfill).
- **R4 (Reseed pivots)** — ✅ done. Ontology Arabic backfilled (37 signs, 29 body systems, 15 host species), curated diseases (20 with `name_ar`/`description`/`description_ar`), pivots linked (disease_clinical_sign 116, disease_host_species 82, disease_classifications 20, disease_microorganism 42), knowledge payloads on 22 diseases. Products (4575) and companies (521) untouched.
- **R5 (Cleanup junk tables)** — ✅ done. Dropped `findings_backup_before_cleanup`, `findings_backup_before_noisy_cleanup`, `findings_delete_candidates`, `modifiers_backup_before_cleanup`, `finding_cleanup_queue` (singular). 0 backup/cleanup/candidates tables remain.
- **R6 (Verify)** — ✅ done. `migrate:status` clean (0 pending); `/drugs/diagnosis` + `/ar/drugs/diagnosis` 200; `/drugs/specializations/fish` 200 in both locales (was 500); disease show renders Arabic payload; POST diagnosis flow produces refinement results; pint clean; `php artisan test --compact` 1 passed / 7 skipped.

## Constraints / decisions

- [x] Preserve all real data: `products`, `companies`, `active_ingredients`, `diseases`, `clinical_signs`, `host_species`, `microorganisms`, `blogs`, etc. — nothing dropped except the five junk tables.
- [x] Do **not** run `migrate:fresh` and do **not** run a blanket `php artisan migrate` before reconciliation (would fail on "table already exists").
- [x] Reconcile by inserting "ran" records into `migrations` for every migration whose table already exists, then run only the genuinely missing migrations.
- [x] The ULID-fix migration `2026_08_03_114057` truncates `disease_clinical_sign`/`disease_host_species`/`disease_classifications` (they hold orphaned bigint rows) and nulls `medical_articles.disease_id` — pivot seeders re-create valid links afterwards.
- [x] Junk tables (referenced nowhere in code) get dropped: `findings_cleanup_queue`, `findings_backup_before_cleanup`, `findings_backup_before_noisy_cleanup`, `findings_delete_candidates`, `modifiers_backup_before_cleanup`.

## Milestone R1 — Backup

- [x] `mysqldump` the whole `vetdrugs` DB to a timestamped SQL file under a backup directory.
- [x] Record the dump file path + size in this plan.
- [x] Verify the dump contains `products`/`companies` tables (grep table headers).

## Milestone R2 — Reconcile migrations

- [x] Compare `database/migrations/*.php` filenames against `migrations.migration` values.
- [x] For each migration file whose target tables already exist in the DB but has no `migrations` row → insert a `migrations` row (batch = next batch number, run as of today).
- [x] Leave genuinely-missing migrations unrecorded so they run in R3: `2026_08_01_000023_add_knowledge_payload_to_diseases_table`, `2026_08_03_111709_create_disease_microorganism_table`, `2026_08_03_111710_create_veterinary_projects_table`, `2026_08_03_114057_fix_disease_relation_columns_to_ulid`, `2026_08_05_000000_add_arabic_display_names_to_drugs_ontology_tables`.
- [x] Re-run `php artisan migrate:status` → all but the 5 missing show Ran, no "exists" errors.

## Milestone R3 — Run missing migrations

- [x] `php artisan migrate --no-interaction` runs exactly the 5 pending migrations (knowledge_payload column, disease_microorganism, veterinary_projects, ULID fix, Arabic display-name columns).
- [x] Confirm `diseases.knowledge_payload` column exists.
- [x] Confirm `disease_microorganism` + `veterinary_projects` tables exist.
- [x] Confirm pivot tables converted to ULID `disease_id` with FKs, and orphaned rows cleared.

## Milestone R4 — Reseed pivots (non-destructive)

- [x] Run ontology backfill seeders: `BodySystemSeeder`, `AnatomicalStructureSeeder`, `FindingSeeder`, `HostSpeciesSeeder`, `ModifierSeeder`, `ClinicalSignSeeder` (fills Arabic, creates only what's missing).
- [x] Run curated `DiseaseSeeder` (backfills `name_ar`/`description`/`description_ar` for the 20 real diseases).
- [x] Run `DiseaseClassificationSeeder`, `DiseaseHostSpeciesSeeder`, `DiseaseClinicalSignSeeder`.
- [x] Run `MicroorganismSeeder` + `DiseaseMicroorganismSeeder`.
- [x] Run `DiseaseKnowledgePayloadSeeder` (populates Arabic payload JSON).
- [x] Confirm pivot counts: `disease_clinical_sign`, `disease_host_species`, `disease_classifications`, `disease_microorganism` all > 0 with real ULID disease ids.

## Milestone R5 — Drop junk tables

- [x] `DROP TABLE IF EXISTS findings_cleanup_queue, findings_backup_before_cleanup, findings_backup_before_noisy_cleanup, findings_delete_candidates, modifiers_backup_before_cleanup;` (also dropped `finding_cleanup_queue`).
- [x] Verify the five tables are gone; verify no legitimate table was touched.

## Milestone R6 — Verify

- [x] `php artisan migrate:status` fully clean (no pending).
- [x] `/drugs/diagnosis` renders in EN and `/ar/drugs/diagnosis` renders in AR (RTL + Arabic names).
- [x] `/drugs/specializations/fish` no longer 500s (disease_microorganism exists).
- [x] Disease show page renders payload sections in both locales.
- [x] `products` count still 4575 and `companies` still 521 (no data loss).
- [x] `vendor/bin/pint --format agent` clean.
- [x] `php artisan test --compact` passes.
