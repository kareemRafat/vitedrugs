# VetPedia Drugs — Knowledge Base Feature Set

> Port the `new Controllers` reference implementation into the Drugs part, as **web UI only**, using the consolidated M1 knowledge migrations. No API layer. Everything lives under `App\Models\Drugs`, `App\Http\Controllers\Drugs`, `resources/views/drugs`, routes under `routes/drugs.php`.

## Assumptions / decisions

- [ ] Web UI only — the `Api/*` controllers, `App\Http\Resources\*`, and their routes are NOT ported.
- [ ] `Php artisan migrate` is run to create the knowledge tables, and seeders populate demo/interlinked data.
- [ ] Old-schema references are remapped: `Drug` → `App\Models\Product`, `Article` → `App\Models\Blog`, `TherapeuticUse` is dropped. `OntologyLinkService` is dropped.
- [ ] `Disease` is shared: extend the existing `App\Models\Disease` (new relationships + `knowledge_payload` cast) instead of duplicating it.
- [ ] Feature 7 (veterinary specializations) and 8 (veterinary projects) ARE in scope: built fresh (not present in reference controllers) — specializations reuse `HostSpecies.taxonomy_group`; projects add a new `veterinary_projects` table.
- [ ] Filament admin CRUD IS in scope: add admin resources for the new KB tables and group all of them (plus the drug-landing page) under a single **`Drugs`** sidebar group.
- [ ] Porting source: `new Controllers/*` (web only) is the reference implementation, ported into `app/Http/Controllers/Drugs`. `Api/` is excluded; `DrugController.php` is excluded (depends on dropped `Drug`/`OntologyLinkService`); `Controller.php` is skipped (app already has `App\Http\Controllers\Controller`). Every `Disease::name_en` reference is remapped to the shared model's `name`. The `new Controllers/` directory is deleted after the port (M9).

---

## Milestone 1: Infra & models

- [ ] Run `php artisan migrate` to create the knowledge tables (already dry-run clean via `--pretend`).
- [ ] **New migration** `create_disease_microorganism_table` — pivot between `diseases` (ULID FK) and `microorganisms`, with `role` (`cause`/`associated`) + timestamps.
- [ ] **New migration** `create_veterinary_projects_table` — ULID id, `title`, `slug` (unique), `summary`, `content` (longText), `project_type` (enum `feasibility_study`/`investment_guide`/`guideline`), `sector`, `featured` (bool), `is_published` (bool), timestamps.
- [ ] Extend `app/Models/Disease.php` with `knowledge_payload` cast and relationships: `belongsToMany(ClinicalSign)` (pivot `weight/is_specific/is_required/is_pathognomonic`), `belongsToMany(HostSpecies)` (pivot `role/susceptibility/is_primary_host/is_reservoir/is_vector/is_carrier/is_incidental_host/notes`), `hasOne(DiseaseClassification)`, `belongsToMany(Microorganism)` (pivot `role`).
- [ ] Create `app/Models/Drugs/BodySystem` — hasMany `anatomicalStructures`; fillable `canonical_name`, `display_name`.
- [ ] Create `app/Models/Drugs/AnatomicalStructure` — belongsTo `bodySystem`, belongsTo `parent`; fillable `body_system_id`, `parent_id`, `canonical_name`, `display_name`, `type`.
- [ ] Create `app/Models/Drugs/Finding` — fillable `canonical_name`, `display_name`, `category`, `ontology_type`, `parent_id`, `is_noisy`, `is_general_sign`, `slug`.
- [ ] Create `app/Models/Drugs/Modifier` — fillable `canonical_name`, `display_name`, `type`, `modifier_group`, `is_noisy`.
- [ ] Create `app/Models/Drugs/ClinicalSign` — belongsTo `finding`, `anatomicalStructure`, `modifier`; belongsToMany `diseases`; fillable `anatomical_structure_id`, `finding_id`, `modifier_id`, `canonical_name`, `display_name`, `stage`, `severity_level_id`, `semantic_slug`.
- [ ] Create `app/Models/Drugs/HostSpecies` — belongsToMany `diseases`; fillable `canonical_name`, `display_name`, `taxonomy_group`, `is_domestic`, `is_wildlife`, `is_human`.
- [ ] Create `app/Models/Drugs/DiseaseClassification` — belongsTo `disease`; fillable `disease_id`, `infectiousness`, `transmissibility`, `etiology_type`, `occurrence_patterns`, `disease_courses`, `notifiable`, `zoonotic`, `oie_category`.
- [ ] Create `app/Models/Drugs/Microorganism` — fillable taxonomy columns, `microorganism_type`, `is_pathogenic`, `json_data`, `tags`, `slug`; `belongsToMany(Disease)` via `disease_microorganism`.
- [ ] Create `app/Models/Drugs/VeterinaryProject` — HasUlids, SoftDeletes; fillable `title`, `slug`, `summary`, `content`, `project_type`, `sector`, `featured`, `is_published`.
- [ ] Create `app/Models/Drugs/MedicalArticle` — belongsTo `disease`; fillable `title`, `slug`, `content`, `disease_id`, `species`, `article_type`, `is_published`, `summary`.
- [ ] Create `app/Models/Drugs/Synonym` — morphTo `synonymable`; fillable `term`, `normalized_term`.
- [ ] Create `app/Models/Drugs/Abbreviation` — fillable `abbreviation`, `full_term`, `description`, `category`.
- [ ] Create `app/Models/Drugs/DifferentialSyndrome` — belongsToMany `clinicalSigns`, `diseases`.
- [ ] Confirm all `App\Models\Drugs\*` autoload (PSR-4 already covers `app/Models/Drugs`).

## Milestone 2: Factories

- [ ] `BodySystemFactory`, `AnatomicalStructureFactory`, `FindingFactory`, `ModifierFactory`, `ClinicalSignFactory` (with correct FK wiring), `HostSpeciesFactory`, `DiseaseClassificationFactory`, `MicroorganismFactory`, `MedicalArticleFactory`, `SynonymFactory`, `AbbreviationFactory`, `DifferentialSyndromeFactory`, `VeterinaryProjectFactory`.
- [ ] Each factory uses `fake()`/`$this->faker`, ULID-safe relations, and realistic defaults (e.g., `microorganism_type` from enum, `canonical_name`/`display_name`).
- [ ] Factories reference real existing `Disease::factory()` for disease-linked models.
- [ ] Run one factory-sanity check per model (tinker / test) to ensure no validation/FK issues.

## Milestone 3: Seeders ("all data")

- [ ] `BodySystemSeeder` (Respiratory, Digestive, Nervous, Reproductive, Musculoskeletal, ...) + `AnatomicalStructureSeeder`.
- [ ] `FindingSeeder` + `ModifierSeeder`.
- [ ] `ClinicalSignSeeder` — build signs combining finding + modifier + anatomy (canonical_name, display_name).
- [ ] `HostSpeciesSeeder` — Cattle, Buffalo, Sheep, Goat, Camel, Equine, Dogs, Cats, Poultry, Turkey, Calf (preferred order from `FilterController`); populate `taxonomy_group` (`ruminant` for Cattle/Buffalo/Sheep/Goat/Camel, `poultry` for Poultry/Turkey, plus a `fish` species) to power Specializations (F7).
- [ ] `DiseaseClassificationSeeder` — attach an existing Disease to each classification; set `etiology_type`, `zoonotic`, `notifiable`, etc.
- [ ] `DiseaseHostSpeciesSeeder` — pivot records with role/susceptibility/ontology flags.
- [ ] `DiseaseClinicalSignSeeder` — attach diseases to clinical signs with `weight`/`is_specific`/`is_required`/`is_pathognomonic`.
- [ ] `MicroorganismSeeder` — bacteria/virus/fungi/parasite records.
- [ ] `DiseaseMicroorganismSeeder` — link microorganisms to diseases via the `disease_microorganism` pivot (`role` cause/associated) so microorganism pages + specializations render.
- [ ] `VeterinaryProjectSeeder` — a few `feasibility_study` + `investment_guide` + `guideline` records (F8).
- [ ] `MedicalArticleSeeder` — a few published articles linked to a disease.
- [ ] `SynonymSeeder` + `AbbreviationSeeder`.
- [ ] `DiseaseKnowledgePayloadSeeder` — populate `knowledge_payload` JSON (`clinical_signs`, `postmortem_findings`, `diagnosis`, `treatment`, `prevention_control`, `references`) on existing diseases so encyclopedia/comparison/diagnosis render data (`references` = list of `{title, url/source}` for the encyclopedia page, F5).
- [ ] Wire everything into `DatabaseSeeder` (or a single `KnowledgeBaseSeeder` called from it).
- [ ] Run `php artisan db:seed` and verify counts + no FK errors.

## Milestone 4: Services

- [ ] `app/Services/MedicalKnowledge/DiseaseDataLoader` (load a disease by slug from DB / article source).
- [ ] `app/Services/Narrative/DiseaseArticleBuilder` (build a narrative article structure from a Disease + its knowledge payload, incl. a `references` section) used by `DiseaseArticleController`/`TestArticleController`.

## Milestone 5: Controllers (adapted into `app/Http/Controllers/Drugs`)

- [ ] `SearchController` — search `Disease`, `ClinicalSign`, `Product`, `Blog`, `Microorganism` (name/normalized_name/tags), `MedicalArticle` (title/summary) (remapped; no `Drug`/`TherapeuticUse`); view `drugs.search.index`. Covers comprehensive search across diseases, drugs, microorganisms, and articles (F1).
- [ ] `DiagnosticController` — symptom-picker + results (score by `weight`, `is_specific`, `is_required`, `is_pathognomonic`; confidence Low/Moderate/High; missing key findings; primary/secondary/low-support splits); view `drugs.diagnosis.results`.
- [ ] `FilterController` — filter by species, etiology, body system, zoonotic (clinical-sign tokens on index); view `drugs.filter.index`.
- [ ] `DiseaseComparisonController` — index (selector of up to 4) + compare (presence matrix across clinical signs, postmortem findings, diagnostics, treatments, preventions); view `drugs.comparison.*`.
- [ ] `DiseaseController::show` — rich disease page with clinical signs grouped (rich relationship load); view `drugs.diseases.show`.
- [ ] `MedicalArticleController` — index (published, paginated 12) + show (with auto TOC from `<h2>`s); views `drugs.medical-articles.*`.
- [ ] `MicroorganismController` — index (browse grouped by `microorganism_type` enum) + show page listing the diseases it causes (via `disease_microorganism`); views `drugs.microorganisms.*`.
- [ ] `SpecializationController` — index (ruminant/poultry/fish cards) + show per group aggregating diseases (via `disease_host_species.taxonomy_group`), drugs/products (via `disease_product`), articles (via `medical_articles.species`), microorganisms (via `disease_microorganism`); views `drugs.specializations.*` (F7).
- [ ] `VeterinaryProjectController` — index (published, paginated) + show; views `drugs.projects.*` (F8).
- [ ] `DiseaseArticleController` + `TestArticleController` — generated narrative article page (dev/test path with a hardcoded slug); view `drugs.medical.article`.
- [ ] Resolve collisions by keeping the new classes only in the `Drugs\` namespace (existing root `DiseaseController`/`SearchController` untouched).

## Milestone 6: Routes (routes/drugs.php, under `drugs` part)

- [ ] `GET /drugs/search` → `Drugs\SearchController@index`.
- [ ] `GET /drugs/diagnosis` → `Drugs\DiagnosticController@index` (picker) and `POST /drugs/diagnosis` → `results`.
- [ ] `GET /drugs/filter` → `Drugs\FilterController@index` (+ autocomplete endpoint for clinical signs).
- [ ] `GET /drugs/diseases/{disease:slug}` → `Drugs\DiseaseController@show`.
- [ ] `GET /drugs/compare` (selector) and `GET /drugs/compare/results` (or POST) → `Drugs\DiseaseComparisonController`.
- [ ] `GET /drugs/articles` + `GET /drugs/articles/{article:slug}` → `Drugs\MedicalArticleController`.
- [ ] `GET /drugs/microorganisms` + `GET /drugs/microorganisms/{microorganism:slug}` → `Drugs\MicroorganismController`.
- [ ] `GET /drugs/specializations` + `GET /drugs/specializations/{group}` → `Drugs\SpecializationController` (F7).
- [ ] `GET /drugs/projects` + `GET /drugs/projects/{project:slug}` → `Drugs\VeterinaryProjectController` (F8).
- [ ] `GET /drugs/article-test` (dev) → `Drugs\TestArticleController@show` (guarded/local-only).
- [ ] All routes inside the existing localized `drugs` prefix group in `routes/drugs.php`.

## Milestone 7 — Views (Flowbite v4 semantic tokens)

- [ ] `resources/views/drugs/search/index.blade.php`.
- [ ] `resources/views/drugs/diagnosis/index.blade.php` + `results.blade.php`.
- [ ] `resources/views/drugs/filter/index.blade.php`.
- [ ] `resources/views/drugs/comparison/index.blade.php` + `results.blade.php`.
- [ ] `resources/views/drugs/diseases/show.blade.php`.
- [ ] `resources/views/drugs/medical-articles/index.blade.php` + `show.blade.php`.
- [ ] `resources/views/drugs/microorganisms/index.blade.php` + `show.blade.php`.
- [ ] `resources/views/drugs/specializations/index.blade.php` + `show.blade.php` (F7).
- [ ] `resources/views/drugs/projects/index.blade.php` + `show.blade.php` (F8).
- [ ] `resources/views/drugs/medical/article.blade.php`.
- [ ] All views use only v4 semantic tokens/test classes (`bg-brand`, `text-heading`, `text-bold`, `bg-neutral-*`, `border-default-medium`, `rounded-base`, dark variants) and extend the shared master layout.
- [ ] Wire the new `/drugs/*` links (incl. Specializations + Projects) into the part-aware navbar (`main-header.blade.php` `@switch($part)` drugs case, desktop + mobile).

## Milestone 8 — Filament admin (Drugs KB), grouped in sidebar

> Build admin resources for the new knowledge tables under `app/Filament/Resources/Drugs/` (matching the existing per-model folder convention: `<Model>/<Model>Resource.php`, `Schemas/`, `Tables/`, `Pages/`, `RelationManagers/`). Every resource/page gets `protected static string|UnitEnum|null $navigationGroup = 'Drugs';` so they collapse under one sidebar group. Group title resolved automatically in Filament 5; optionally register `->navigationGroups([...])` order in `AdminPanelProvider`.

### 8.1 Sidebar grouping

- [ ] Register the `Drugs` navigation group and set group ordering in `app/Providers/Filament/AdminPanelProvider.php` (via `->navigationGroups()`), so all KB resources appear together below the existing `Catalog` / `System` groups.
- [ ] Set `$navigationGroup = 'Drugs'` on every new Drugs resource (+ the new landing/admin page).
- [ ] Confirm existing shared `Disease` resource (in `Catalog`) stays visible; add an explicit Decision on whether knowledge-payload editing lives in the shared `DiseaseResource` relation manager OR a new `Drugs\DiseaseResource`. Default: keep shared `DiseaseResource` and add a **`DiseaseClinicalSignsRelationManager`** (pivot weight/specific/required/pathognomonic) + **`DiseaseHostSpeciesRelationManager`** + **`KnowledgePayloadRelationManager`** to it.

### 8.2 CRUD resources (`app/Filament/Resources/Drugs/`)

- [ ] `BodySystemResource` (list/create/edit/view).
- [ ] `AnatomicalStructureResource` — with `BodySystemsRelationManager`/parent selection.
- [ ] `FindingResource`.
- [ ] `ModifierResource`.
- [ ] `ClinicalSignResource` — fields for finding/anatomy/modifier, `canonical_name`, `display_name`, `stage`, `semantic_slug`; `weight`/flags via pivot.
- [ ] `HostSpeciesResource`.
- [ ] `DiseaseClassificationResource` — inline/hasOne on Disease (etiology_type, zoonotic, notifiable, oie_category, occurrence_patterns, disease_courses).
- [ ] `MicroorganismResource` — taxonomy columns + `microorganism_type` enum + `slug`.
- [ ] `MedicalArticleResource` — rich `content` (RichEditor) + `disease_id`, `species`, `article_type`, `is_published`, `summary`.
- [ ] `SynonymResource` + `AbbreviationResource`.
- [ ] `DifferentialSyndromeResource` — belongsToMany clinical signs + diseases relation managers.
- [ ] `VeterinaryProjectResource` — `title`/`slug`/`summary`/`sector`, `project_type` enum, `featured`, `is_published`, rich `content` (RichEditor) (F8).
- [ ] Each resource uses the existing `Schemas/` + `Tables/` folder convention (Form/Infolist/Table classes) and `Heroicon::Outlined*` navigation icons.

### 8.3 Relation managers on shared resources

- [ ] `DiseaseResource\RelationManagers\ClinicalSignsRelationManager` — attach signs with pivot `weight`, `is_specific`, `is_required`, `is_pathognomonic`.
- [ ] `DiseaseResource\RelationManagers\HostSpeciesRelationManager` — pivot role/susceptibility/reservoir/vector/carrier/incidental/notes.
- [ ] `DiseaseResource\RelationManagers\MedicalArticlesRelationManager` — articles owned by a Disease.
- [ ] `ClinicalSignResource` relation managers: `DiseasesRelationManager` (reverse pivot), `FindingsRelationManager`.
- [ ] `HostSpeciesResource` — add `taxonomy_group` select (`ruminant`/`poultry`/`fish`) so Specializations are admin-editable (F7).
- [ ] `MicroorganismResource` `DiseasesRelationManager` — attach diseases via the `disease_microorganism` pivot (`role` cause/associated).

### 8.4 Custom admin pages (optional)

- [ ] `Drugs\KnowledgeOverview` page (dashboard-ish): counts for clinical signs, microorganisms, host species, articles, diseases with knowledge payload ("Knowledge Coverage").
- [ ] Group the new page under `Drugs` too.

## Milestone 9 — Verify

- [ ] `php artisan route:list` — confirm all `/drugs/*` routes + names.
- [ ] `php artisan migrate --pretend` — includes the new `disease_microorganism` + `veterinary_projects` tables.
- [ ] `php artisan view:cache` — compiles clean.
- [ ] `php artisan db:seed` (idempotent, re-runnable).
- [ ] `vendor/bin/pint --dirty` on changed PHP.
- [ ] HTTP smoke test each `/drugs/*` page → 200 + `data-part="drugs"` (incl. `/drugs/microorganisms`, `/drugs/specializations`, `/drugs/projects`).
- [ ] Filament: boot `/admin` with all new resources in one `Drugs` sidebar group (no group-name/icon errors).
- [ ] DELETE `new Controllers/` (web + Api) after all Drugs controllers are ported to `app/Http/Controllers/Drugs` — nothing references it anymore.
- [ ] Optional: feature test for `DiagnosticController` scoring/confidence path.