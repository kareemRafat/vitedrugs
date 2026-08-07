# VetPedia Drugs — Large Animals Knowledge Base Feature Set

> Port the `new Controllers` reference implementation into the Large Animals part, as **web UI only**, using the consolidated M1 knowledge migrations. No API layer. Everything lives under `App\Models\LargeAnimals`, `App\Http\Controllers\LargeAnimals`, `resources/views/large-animals`, routes under `routes/large-animals.php`.

## Status

- **M1 (Infra & models)** — DONE (2026-08-03)
- **M2 (Factories)** — DONE (2026-08-03)
- **M3 (Seeders)** — DONE (2026-08-03)
- **M4 (Services)** — DONE (2026-08-03)
- **M5 (Controllers)** — DONE (2026-08-04)
- **M6 (Routes)** — DONE (2026-08-04)
- **M7 (Views)** — DONE (2026-08-04)
- **M7.5 (Localization)** — DONE (2026-08-04)
- M8 (Filament admin) — DONE (2026-08-07). Milestone 9 (verify) not started.

> **Schema note:** the pre-existing KB pivot tables (`disease_clinical_sign`, `disease_host_species`, `disease_classifications`, `medical_articles`) had `bigint` `disease_id` columns with orphaned integer IDs while `diseases.id` is ULID. Fixed via migration `2026_08_03_114057_fix_disease_relation_columns_to_ulid` (convert to `ulid` + real FKs, clear orphans). M3 seeders rebuilt the disease links.
>
> **Schema note (orphan ontology tables):** `clinical_severity_levels`, `clinical_course_types`, `transmission_types`, `transmission_routes`, `reservoirs`, `vectors` (and their `disease_*` pivots) exist in the DB schema created by migrations `2026_08_01_000010`–`000015`. They have **no models and no resource backing**, are ORPHANED from the app, and are **EXCLUDED from M8 scope**.

## Assumptions / decisions

- [x] Web UI only — the `Api/*` controllers, `App\Http\Resources\*`, and their routes are NOT ported.
- [x] `Php artisan migrate` is run to create the knowledge tables, and seeders populate demo/interlinked data.
- [ ] Old-schema references are remapped: `Drug` → `App\Models\Product`, `Article` → `App\Models\Blog`, `TherapeuticUse` is dropped. `OntologyLinkService` is dropped.
- [x] `Disease` is shared: extend the existing `App\Models\Disease` (new relationships + `knowledge_payload` array cast) instead of duplicating it.
- [x] Feature 7 (veterinary specializations) and 8 (veterinary projects) ARE in scope: built fresh (not present in reference controllers) — specializations reuse `HostSpecies.taxonomy_group`; projects add a new `veterinary_projects` table.
- [x] Filament admin CRUD IS in scope: add admin resources for the new KB tables and group all of them (plus the Large Animals landing page) under a single **`Large Animals`** sidebar group.
- [x] Porting source: `new Controllers/*` (web only) is the reference implementation, ported into `app/Http/Controllers/LargeAnimals`. `Api/` is excluded; `DrugController.php` is excluded (depends on dropped `Drug`/`OntologyLinkService`); `Controller.php` is skipped (app already has `App\Http\Controllers\Controller`). Every `Disease::name_en` reference is remapped to the shared model's `name`. The `new Controllers/` directory is deleted after the port (M9).

---

## Milestone 1: Infra & models

- [x] Run `php artisan migrate` to create the knowledge tables (already dry-run clean via `--pretend`).
- [x] **New migration** `create_disease_microorganism_table` — pivot between `diseases` (ULID FK) and `microorganisms`, with `role` (`cause`/`associated`) + timestamps.
- [x] **New migration** `create_veterinary_projects_table` — ULID id, `title`, `slug` (unique), `summary`, `content` (longText), `project_type` (enum `feasibility_study`/`investment_guide`/`guideline`), `sector`, `featured` (bool), `is_published` (bool), timestamps.
- [x] Extend `app/Models/Disease.php` with `knowledge_payload` array cast and relationships: `belongsToMany(ClinicalSign)` (pivot `weight`/`is_specific`/`is_required`/`is_pathognomonic`), `belongsToMany(HostSpecies)` (pivot `role`/`susceptibility`/`is_primary_host`/`is_reservoir`/`is_vector`/`is_carrier`/`is_incidental_host`/`notes`), `hasOne(DiseaseClassification)`, `belongsToMany(Microorganism)` (pivot `role`).
- [x] Create `app/Models/LargeAnimals/BodySystem` — hasMany `anatomicalStructures`; fillable `canonical_name`, `display_name`, `display_name_ar`.
- [x] Create `app/Models/LargeAnimals/AnatomicalStructure` — belongsTo `bodySystem`, belongsTo `parent`; fillable `body_system_id`, `parent_id`, `canonical_name`, `display_name`, `display_name_ar`, `type`.
- [x] Create `app/Models/LargeAnimals/Finding` — fillable `canonical_name`, `display_name`, `display_name_ar`, `category`, `ontology_type`, `parent_id`, `is_noisy`, `is_general_sign`, `slug`.
- [x] Create `app/Models/LargeAnimals/Modifier` — fillable `canonical_name`, `display_name`, `display_name_ar`, `type`, `modifier_group`, `is_noisy`.
- [x] Create `app/Models/LargeAnimals/ClinicalSign` — belongsTo `finding`, `anatomicalStructure`, `modifier`; belongsToMany `diseases`; fillable `anatomical_structure_id`, `finding_id`, `modifier_id`, `canonical_name`, `display_name`, `display_name_ar`, `stage`, `severity_level_id`, `semantic_slug`.
- [x] Create `app/Models/LargeAnimals/HostSpecies` — belongsToMany `diseases`; fillable `canonical_name`, `display_name`, `display_name_ar`, `taxonomy_group`, `is_domestic`, `is_wildlife`, `is_human`.
- [x] Create `app/Models/LargeAnimals/DiseaseClassification` — belongsTo `disease`; fillable `disease_id`, `infectiousness`, `transmissibility`, `etiology_type`, `occurrence_patterns`, `disease_courses`, `notifiable`, `zoonotic`, `oie_category`.
- [x] Create `app/Models/LargeAnimals/Microorganism` — fillable taxonomy columns (`normalized_name`, `kingdom`, `phylum`, `class`, `order`, `family`, `genus`, `species`), `microorganism_type`, `is_pathogenic`, `searchable_text`, `json_data`, `source_json_file`, `tags`, `slug`, `is_topic`; `belongsToMany(Disease)` via `disease_microorganism`, `belongsToMany(ActiveIngredient)` via `microorganism_active_ingredient` (pivot `sensitivity`/`notes`).
- [x] Create `app/Models/LargeAnimals/VeterinaryProject` — HasUlids, SoftDeletes; fillable `title`, `slug`, `summary`, `content`, `project_type`, `sector`, `featured`, `is_published`.
- [x] Create `app/Models/LargeAnimals/MedicalArticle` — belongsTo `disease`; fillable `title`, `title_ar`, `slug`, `content`, `content_ar`, `disease_id`, `species`, `species_ar`, `article_type`, `is_published`, `summary`, `summary_ar`.
- [x] Create `app/Models/LargeAnimals/Synonym` — morphTo `synonymable`; fillable `term`, `normalized_term`, `source_type`, `synonymable_type`, `synonymable_id`.
- [x] Create `app/Models/LargeAnimals/Abbreviation` — fillable `abbreviation`, `full_term`, `description`, `category`.
- [x] Create `app/Models/LargeAnimals/DifferentialSyndrome` — fillable `name`, `name_ar`, `description`; belongsToMany `clinicalSigns`, `diseases` (pivot `boost_score`).
- [x] Confirm all `App\Models\LargeAnimals\*` autoload (PSR-4 already covers `app/Models/LargeAnimals`).

## Milestone 2: Factories

- [x] `BodySystemFactory`, `AnatomicalStructureFactory`, `FindingFactory`, `ModifierFactory`, `ClinicalSignFactory` (with correct FK wiring), `HostSpeciesFactory`, `DiseaseClassificationFactory`, `MicroorganismFactory`, `MedicalArticleFactory`, `SynonymFactory`, `AbbreviationFactory`, `DifferentialSyndromeFactory`, `VeterinaryProjectFactory`.
- [x] Each factory uses `fake()`/`$this->faker`, ULID-safe relations, and realistic defaults (e.g., `microorganism_type` from enum, `canonical_name`/`display_name`).
- [x] Factories reference real existing `Disease::factory()` for disease-linked models.
- [x] Run one factory-sanity check per model (tinker / test) to ensure no validation/FK issues.

## Milestone 3: Seeders ("all data")

- [x] `BodySystemSeeder` (Respiratory, Digestive, Nervous, Reproductive, Musculoskeletal, ...) + `AnatomicalStructureSeeder`.
- [x] `FindingSeeder` + `ModifierSeeder`.
- [x] `ClinicalSignSeeder` — build signs combining finding + modifier + anatomy (canonical_name, display_name).
- [x] `HostSpeciesSeeder` — Cattle, Buffalo, Sheep, Goat, Camel, Equine, Dogs, Cats, Poultry, Turkey, Calf (preferred order from `FilterController`); populate `taxonomy_group` (`ruminant` for Cattle/Buffalo/Sheep/Goat/Camel, `poultry` for Poultry/Turkey, plus a `fish` species) to power Specializations (F7).
- [x] `DiseaseClassificationSeeder` — attach an existing Disease to each classification; set `etiology_type`, `zoonotic`, `notifiable`, etc.
- [x] `DiseaseHostSpeciesSeeder` — pivot records with role/susceptibility/ontology flags.
- [x] `DiseaseClinicalSignSeeder` — attach diseases to clinical signs with `weight`/`is_specific`/`is_required`/`is_pathognomonic`.
- [x] `MicroorganismSeeder` — bacteria/virus/fungi/parasite records.
- [x] `DiseaseMicroorganismSeeder` — link microorganisms to diseases via the `disease_microorganism` pivot (`role` cause/associated) so microorganism pages + specializations render.
- [x] `VeterinaryProjectSeeder` — a few `feasibility_study` + `investment_guide` + `guideline` records (F8).
- [x] `MedicalArticleSeeder` — a few published articles linked to a disease.
- [x] `SynonymSeeder` + `AbbreviationSeeder`.
- [x] `DiseaseKnowledgePayloadSeeder` — populate `knowledge_payload` JSON (`clinical_signs`, `postmortem_findings`, `diagnosis`, `treatment`, `prevention_control`, `references`) on existing diseases so encyclopedia/comparison/diagnosis render data (`references` = list of `{title, url/source}` for the encyclopedia page, F5).
- [x] Wire everything into `DatabaseSeeder` (or a single `KnowledgeBaseSeeder` called from it).
- [x] Run `php artisan db:seed` and verify counts + no FK errors.

## Milestone 4: Services

- [x] `app/Services/MedicalKnowledge/DiseaseDataLoader` (load a disease by slug from DB / article source).
- [x] `app/Services/Narrative/DiseaseArticleBuilder` (build a narrative article structure from a Disease + its knowledge payload, incl. a `references` section) used by `DiseaseArticleController`/`TestArticleController`.
- [x] `app/Services/LargeAnimals/DiagnosisService` + `DiagnosisShareService` (diagnosis scoring/splits + shareable results).
- [x] `app/Services/LargeAnimals/FilterService` + `FilterShareService` (species/etiology/body-system/zoonotic filtering + shareable filter results).

## Milestone 5: Controllers (adapted into `app/Http/Controllers/LargeAnimals`)

- [x] `SearchController` — search `Disease`, `ClinicalSign`, `Product`, `Blog`, `Microorganism` (name/normalized_name/tags), `MedicalArticle` (title/summary) (remapped; no `Drug`/`TherapeuticUse`); view `large-animals.search.index`. Covers comprehensive search across diseases, drugs, microorganisms, and articles (F1).
- [x] `DiagnosticController` — symptom-picker + results (score by `weight`, `is_specific`, `is_required`, `is_pathognomonic`; confidence Low/Moderate/High; missing key findings; primary/secondary/low-support splits); views `large-animals.diagnosis.index` / `refinement` / `results`.
- [x] `FilterController` — filter by species, etiology, body system, zoonotic (clinical-sign tokens on index); views `large-animals.filter.index` / `results`.
- [x] `DiseaseComparisonController` — index (selector of up to 4) + compare (presence matrix across clinical signs, postmortem findings, diagnostics, treatments, preventions); via Livewire `disease-compare-builder` on `large-animals.comparison.index`.
- [x] `DiseaseController::show` — rich disease page with clinical signs grouped (rich relationship load + knowledge_payload); view `large-animals.diseases.show`.
- [x] `MedicalArticleController` — index (published, paginated) + show (with auto TOC from `<h2>`s); views `large-animals.medical-articles.*`.
- [x] `MicroorganismController` — index (browse grouped by `microorganism_type` enum) + show page listing the diseases it causes (via `disease_microorganism`); views `large-animals.microorganisms.*`.
- [x] `SpecializationController` — index (ruminant/poultry/fish cards) + show per group aggregating diseases (via `disease_host_species.taxonomy_group`), drugs/products (via `disease_product`), articles (via `medical_articles.species`), microorganisms (via `disease_microorganism`); views `large-animals.specializations.*` (F7).
- [x] `DrugLandingController` — Large Animals landing/home page; view `large-animals.landing`.
- [x] `VeterinaryProjectController` — index (published, paginated) + show; views `large-animals.projects.*` (F8).
- [x] `DiseaseArticleController` + `TestArticleController` — generated narrative article page (dev/test path with a hardcoded slug); view `large-animals.medical.article`.
- [x] Resolve collisions by keeping the new classes only in the `LargeAnimals\` namespace (existing root `DiseaseController`/`SearchController` untouched).

## Milestone 6: Routes (`routes/large-animals.php`, prefix `large-animals`, localized group)

- [x] `GET /large-animals` → `LargeAnimals\DrugLandingController` (`large-animals.home`).
- [x] `GET /large-animals/search` → `LargeAnimals\SearchController@index`.
- [x] `GET /large-animals/diagnosis` (picker) + `GET /large-animals/diagnosis/suggestions` + `POST /large-animals/diagnosis` (run) + `GET|POST /large-animals/diagnosis/results` (refined) → `LargeAnimals\DiagnosticController`.
- [x] `GET /large-animals/filter` (+ `GET /suggestions`, `GET|POST /results`) → `LargeAnimals\FilterController`.
- [x] `GET /large-animals/compare` (Livewire `disease-compare-builder`).
- [x] `GET /large-animals/diseases/{slug}` → `LargeAnimals\DiseaseController@show`; `GET /large-animals/diseases/{slug}/article` → `LargeAnimals\DiseaseArticleController@show`.
- [x] `GET /large-animals/articles` + `GET /large-animals/articles/{slug}` → `LargeAnimals\MedicalArticleController`.
- [x] `GET /large-animals/microorganisms` + `GET /large-animals/microorganisms/{slug}` → `LargeAnimals\MicroorganismController`.
- [x] `GET /large-animals/specializations` + `GET /large-animals/specializations/{group}` → `LargeAnimals\SpecializationController` (F7).
- [x] `GET /large-animals/projects` + `GET /large-animals/projects/{slug}` → `LargeAnimals\VeterinaryProjectController` (F8).
- [x] `GET /large-animals/article-test` (dev) → `LargeAnimals\TestArticleController@show` (guarded/local-only).
- [x] All routes inside the existing localized `large-animals` prefix group (locale prefix via `LaravelLocalization`, `localeViewPath` middleware).

> **Notes (implementation):** slug params are `{slug}` (not `{disease:slug}`/etc.) because the ported controllers accept a plain `string $slug`, not model binding. `article-test` is only registered in `local`/`testing` env.

## Milestone 7 — Views (Flowbite v4 semantic tokens)

- [x] `resources/views/large-animals/landing.blade.php`.
- [x] `resources/views/large-animals/search/index.blade.php`.
- [x] `resources/views/large-animals/diagnosis/index.blade.php` + `refinement.blade.php` + `results.blade.php`.
- [x] `resources/views/large-animals/filter/index.blade.php` + `results.blade.php`.
- [x] `resources/views/large-animals/comparison/index.blade.php` (Livewire `disease-compare-builder`).
- [x] `resources/views/large-animals/diseases/show.blade.php`.
- [x] `resources/views/large-animals/medical-articles/index.blade.php` + `show.blade.php`.
- [x] `resources/views/large-animals/microorganisms/index.blade.php` + `show.blade.php`.
- [x] `resources/views/large-animals/specializations/index.blade.php` + `show.blade.php` (F7).
- [x] `resources/views/large-animals/projects/index.blade.php` + `show.blade.php` (F8).
- [x] `resources/views/large-animals/medical/article.blade.php`.
- [x] All views use only v4 semantic tokens/test classes (`bg-brand`, `text-heading`, `text-bold`, `bg-neutral-*`, `border-default-medium`, `rounded-base`, dark variants) and extend the shared master layout.
- [x] Wire the new `/large-animals/*` links (incl. Specializations + Projects) into the part-aware navbar (`main-header.blade.php` `@switch($part)` large-animals case, desktop + mobile).
- [x] All views reference their UI strings via `__('large-animals.*')` (reusing `messages.common` where possible) so the new pages render in both en/ar.

## Milestone 7.5 — Localization (UI only, en/ar)

> **Scope:** UI chrome on all new `/large-animals/*` views uses `__('large-animals.*')` via dedicated lang files `lang/{en,ar}/large-animals.php` (Laravel resolves the file name as the namespace; `messages.php` stays untouched). **No DB/migration/model/seeder changes** — content fields (`canonical_name`, `display_name`) stay English. Explicitly NOT translated (raw English): disease-article section titles (from `DiseaseArticleBuilder`), confidence labels `Low`/`Moderate`/`High` (English medical terms), canonical/display names, and all medical terminology.

- [x] Create `lang/en/large-animals.php` with:
  - `nav` — Search, Diagnosis, Filter, Comparison, Microorganisms, Specializations, Projects, Articles.
  - `search.*` — heading, placeholder, section labels, empty state.
  - `diagnosis.*` — picker title, species select, submit; results sections (matched signs, missing key signs, primary/secondary/low-support splits).
  - `filter.*` — filter labels (species, etiology, body system, zoonotic), clear.
  - `comparison.*` — selector, "up to 4", presence-matrix column headers.
  - `disease.*` — page section headings (Clinical Signs, Postmortem Findings, Diagnosis, Treatment, Prevention & Control, References).
  - `medical_articles.*` — index/show, TOC, empty states.
  - `microorganisms.*` — type labels (bacteria/virus/fungus/parasite), cause/associated.
  - `specializations.*` — group labels (ruminant/poultry/fish), section titles.
  - `projects.*` — project types (`feasibility_study`/`investment_guide`/`guideline`), featured badge.
- [x] Create `lang/ar/large-animals.php` mirroring all keys in Arabic.
- [x] Verify `/en/large-animals/*` and `/ar/large-animals/*` render 200 in both locales; language switcher flips Large Animals pages correctly.

## Milestone 8 — Filament admin (Large Animals KB), grouped in sidebar

> Build admin resources for the Large Animals knowledge tables under `app/Filament/Resources/LargeAnimals/` (matching the existing per-model folder convention: `<Model>/<Model>Resource.php`, `Schemas/`, `Tables/`, `Pages/`, `RelationManagers/`). Every resource/page gets `protected static string|UnitEnum|null $navigationGroup = 'Large Animals';` so they collapse under one sidebar group. Group title resolves automatically in Filament 5; register `->navigationGroups(['Catalog', 'Content', 'Large Animals', 'System'])` order in `AdminPanelProvider`.

### 8.1 Sidebar grouping

- [x] Register the `Large Animals` navigation group and set group ordering in `app/Providers/Filament/AdminPanelProvider.php` (via `->navigationGroups(['Catalog', 'Content', 'Large Animals', 'System'])`), so all KB resources appear together below the existing `Catalog` / `Content` / `System` groups.
- [x] Set `$navigationGroup = 'Large Animals'` on every new Large Animals resource + the two new custom pages.
- [x] Keep the shared `Diseases` resource (in `Catalog`) visible; add to it the `ClinicalSignsRelationManager`, `HostSpeciesRelationManager`, and `MicroorganismsRelationManager`.
- [x] **`knowledge_payload` import editing (JSON-driven):** add a `LargeAnimals\ImportDiseaseKnowledge` custom page that validates a JSON payload (`clinical_signs`, `postmortem_findings`, `diagnosis`, `treatment`, `prevention_control`, `references`) onto `diseases.knowledge_payload` (already a nullable JSON column, array-cast — **NO migration needed**). Link it under `Large Animals`.

> **Approach (agreed):** hand-write every PHP Filament file to match the existing `Schemas/` + `Tables/` + `Pages/` convention (mirror `Diseases`/`ActiveIngredients`/`DataQuality`); do NOT use `make:filament-resource --generate` output. No migrations, no diff of models (the enums stay plain strings — see 8.2).

### 8.2 CRUD resources (`app/Filament/Resources/LargeAnimals/`)

- [x] `BodySystemResource` (list/create/edit/view) — `canonical_name`, `display_name`, `display_name_ar`.
- [x] `AnatomicalStructureResource` — `body_system_id`, `parent_id`, names (`display_name_ar`), `type`.
- [x] `FindingResource` — `canonical_name`, `display_name`, `display_name_ar`, `category`, `ontology_type`, `parent_id`, `is_noisy`, `is_general_sign`, `slug`.
- [x] `ModifierResource` — names (`display_name_ar`), `type`, `modifier_group`, `is_noisy`.
- [x] `ClinicalSignResource` — finding/anatomy/modifier selects, names (`display_name_ar`), `stage`, `severity_level_id`, `semantic_slug`; `DiseasesRelationManager` (reverse pivot).
- [x] `HostSpeciesResource` — names (`display_name_ar`), `taxonomy_group` select (`ruminant`/`poultry`/`fish`) so Specializations are admin-editable (F7), `is_domestic`, `is_wildlife`, `is_human`; `DiseasesRelationManager`.
- [x] `DiseaseClassificationResource` — inline/hasOne on Disease (infectiousness, transmissibility, `etiology_type`, `zoonotic`, `notifiable`, `oie_category`, `occurrence_patterns`, `disease_courses`).
- [x] `MicroorganismResource` — full taxonomy columns, `microorganism_type` select, `is_pathogenic`, `is_topic`, `searchable_text`, `tags`, `slug`, `json_data`; `DiseasesRelationManager` (pivot `role`) + `ActiveIngredientsRelationManager` (pivot `sensitivity` via `App\Enums\AntibioticSensitivity`, `notes`).
- [x] `MedicalArticleResource` — `title`, `slug`, `summary`, rich `content` (RichEditor), `disease_id`, `species`, `article_type`, `is_published` (+ `_ar` fields).
- [x] `SynonymResource` — `term`, `normalized_term`, `source_type`, morph `synonymable`.
- [x] `AbbreviationResource` — `abbreviation`, `full_term`, `description`, `category`.
- [x] `DifferentialSyndromeResource` — `name`/`name_ar`, `description`; `ClinicalSignsRelationManager` + `DiseasesRelationManager` (pivot `boost_score`).
- [x] `VeterinaryProjectResource` — `title`/`slug`/`summary`/`sector`, `project_type` select, `featured`, `is_published`, rich `content` (RichEditor) (F8).
- [x] Each resource uses the existing `Schemas/` + `Tables/` folder convention (Form/Infolist/Table classes) and `Heroicon::Outlined*` navigation icons.
- [x] **Select fields are plain strings + `Select->options()`** (no BackedEnum classes, no DB/cast changes): `microorganism_type` (bacteria/virus/fungus/parasite), `taxonomy_group` (ruminant/poultry/fish), `project_type` (feasibility_study/investment_guide/guideline), `article_type`, `role` pivots (cause/associated), `etiology_type`, `notifiable`, `zoonotic`, `oie_category`. Only the existing `App\Enums\AntibioticSensitivity` is used (pivot `sensitivity`).

### 8.3 Relation managers on shared & Large Animals resources

- [x] `Diseases\RelationManagers\ClinicalSignsRelationManager` — attach signs with pivot `weight`, `is_specific`, `is_required`, `is_pathognomonic`.
- [x] `Diseases\RelationManagers\MicroorganismsRelationManager` — attach microorganisms via `disease_microorganism` pivot `role` (cause/associated).
- [x] `Diseases\RelationManagers\HostSpeciesRelationManager` — pivot `role`/`susceptibility`/`is_primary_host`/`is_reservoir`/`is_vector`/`is_carrier`/`is_incidental_host`/`notes`.
- [x] `ClinicalSignResource` `DiseasesRelationManager` (reverse pivot).
- [x] `HostSpeciesResource` `DiseasesRelationManager` (reverse pivot).
- [x] `MicroorganismResource` `DiseasesRelationManager` (reverse pivot `role`) + `ActiveIngredientsRelationManager` (sensitivity/notes).
- [x] `DifferentialSyndromeResource` `ClinicalSignsRelationManager` + `DiseasesRelationManager` (pivot `boost_score`).

### 8.4 Custom admin pages

- [x] `LargeAnimals\ImportDiseaseKnowledge` page — **both input modes, with hints** to the admin: (a) a file-upload field accepting a JSON file and (b) a JSON textarea fallback. Both validate the same payload shape (`clinical_signs`, `postmortem_findings`, `diagnosis`, `treatment`, `prevention_control`, `references`) and store it on a selected Disease's `knowledge_payload`. Visible `helperText`/hints explain the expected JSON keys.
- [x] `LargeAnimals\KnowledgeOverview` page (dashboard-ish): counts for clinical signs, microorganisms, host species, articles, diseases **with `knowledge_payload` set** ("Knowledge Coverage").
- [x] Group both pages under `Large Animals`.

> **Excluded from M8:** orphaned ontology tables `clinical_severity_levels`, `clinical_course_types`, `transmission_types`, `transmission_routes`, `reservoirs`, `vectors` (M1 migrations `2026_08_01_000010`–`000015`) — no models, out of scope.

## Milestone 9 — Verify

- [ ] `php artisan route:list --path=large-animals` — confirm all `/large-animals/*` routes + names.
- [ ] `php artisan migrate --pretend` — includes the new `disease_microorganism` + `veterinary_projects` tables.
- [ ] `php artisan view:cache` — compiles clean.
- [ ] `php artisan db:seed` (idempotent, re-runnable).
- [ ] `vendor/bin/pint --dirty` on changed PHP.
- [ ] HTTP smoke test each `/large-animals/*` page → 200 + `data-part="large-animals"` (incl. `/large-animals/microorganisms`, `/large-animals/specializations`, `/large-animals/projects`).
- [ ] HTTP smoke test each `/large-animals/*` page in **both locales** (`/en/large-animals/*` and `/ar/large-animals/*`) → 200, no missing `__('large-animals.*')` keys.
- [ ] Filament: boot `/admin` with all new resources in one `Large Animals` sidebar group (no group-name/icon errors).
- [ ] DELETE `new Controllers/` (web + Api) after all Large Animals controllers are ported to `app/Http/Controllers/LargeAnimals` — nothing references it anymore.
- [ ] Optional: feature test for `DiagnosticController` scoring/confidence path.