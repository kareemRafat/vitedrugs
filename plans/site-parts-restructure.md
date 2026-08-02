# VetPedia Site Restructure — 3 Parts (Large Animals / Drugs / Poultry)

> One database, one `users` table for login/register. Shared root pages (blog, about, contact, privacy, terms, auth, profile) stay at the root.

| Part | URL | Color | Content |
|---|---|---|---|
| 1. Large Animals | `/large-animals` | Blue (current design) | **Current controllers only** (products, companies, diseases, active ingredients, search, blog) — unchanged, flat |
| 2. Drugs | `/drugs` | Orange (shades) | **Everything from `database/new drugs migrations/`** (knowledge base: clinical signs, findings, body systems, microorganisms, disease KB, synonyms, differential syndromes, pivots, ...) |
| 3. Poultry | `/poultry` | Green | Landing now, features later |

Source migrations: `database/new drugs migrations/` → cleaned copy into `database/migrations/` (37 kept / 15 excluded).

---

## Milestone 1: Migration cleanup

**Status:** Done

### 1.1 Excluded files — never copied

- [x] Exclude framework/duplicates: `create_users_table`, `create_password_reset_tokens_table`, `create_failed_jobs_table`, `create_personal_access_tokens_table`
- [x] Exclude keep-current tables: `create_diseases_table`, `create_species_table`
- [x] Exclude old drugs schema: `drugs`, `drug_categories`, `add_drug_category_id_to_drugs`, `add_mechanism_of_action_to_drugs`, `therapeutic_uses`, `drug_therapeutic_use`
- [x] Exclude articles (use `blogs` instead): `create_articles`, `add_unique_constraint_to_articles`, `add_content_format_to_articles`

### 1.2 Kept files — copy into `database/migrations/`

- [x] Copy anatomy/clinical base: `body_systems`, `anatomical_structures` (+`add_type`), `findings` (+`add_slug`, `add_is_general_sign`, `add_parent_id`, `add_is_noisy`, `add_ontology_type`), `modifiers` (+`add_modifier_group`, `add_is_noisy`)
- [x] Copy clinical signs: `clinical_signs` (+`add_severity_level_id`, `add_semantic_unique_index`, `add_semantic_slug`, `add_index_to_semantic_slug`), `disease_clinical_sign` (+`add_is_pathognomonic`)
- [x] Copy disease knowledge: `disease_classifications`, `transmission_ontology_tables`, `transmission_type_ontology_tables`, `reservoir_and_vector_ontology_tables`, `clinical_severity_ontology_tables`, `host_species`, `disease_host_species`, `disease_species`, `add_knowledge_payload_to_diseases`
- [x] Copy names: `synonyms` (+`add_source_type`), `abbreviations`
- [x] Copy differential syndromes: `differential_syndromes`, `differential_syndrome_clinical_sign`, `differential_syndrome_disease`
- [x] Copy microorganisms: `microorganisms` (+`add_slug`)
- [x] Copy `medical_articles` (kept — only the `articles` table is replaced by blog; confirm)

### 1.3 Adaptations

- [x] Change `disease_id` `foreignId` → `foreignUlid` in 10 files (current `diseases.id` is ULID): `disease_species`, `disease_clinical_sign`, `disease_classifications`, `transmission_ontology_tables`, `disease_host_species`, `transmission_type_ontology_tables`, `reservoir_and_vector_ontology_tables`, `clinical_severity_ontology_tables`, `differential_syndrome_disease`, `medical_articles`
- [x] Fix ordering: `disease_species.species_id` FK → `host_species` (created later) — drop that FK constraint, keep plain indexed column
- [x] Delete the `database/new drugs migrations/` source folder after copy
- [x] Run `php artisan migrate --pretend` to dry-run the full set
- [ ] (User runs the real `php artisan migrate` + builds models/factories/seeders)

> **Adaptation:** All kept files were **re-timestamped from `2026_05`/`2026_06` to `2026_08_01_000000` … `000036`** (order preserved). This is required because the kept KB migrations reference the shared `diseases`/`species` tables that are created much later in the existing `database/migrations/` set (`2026_06_16_create_diseases_table`, `2026_06_14_create_species_table`). Keeping the original earlier timestamps would make those FK constraints target tables that don't exist yet. The target file `create_disease_clinical_sign_table` was also renamed correctly (source had a truncated `.php`).

---

## Milestone 2: Folder structure

**Status:** Done

> Milestone 2 scope (confirmed with user): **folder structure only** — the `Drugs`/`Poultry` directories are created; the model/controller/resource classes themselves remain **out of scope** for the user to build.

- [x] Large Animals: NO new files — current controllers/models/resources stay flat and unchanged (`app/Http/Controllers`, `app/Models`, `app/Filament/Resources`)
- [x] Create `app/Http/Controllers/Drugs` — knowledge-base controllers (ClinicalSignController, FindingController, BodySystemController, ...) + DrugLandingController
- [x] Create `app/Http/Controllers/Poultry` — PoultryLandingController + future
- [x] Create `app/Models/Drugs` — knowledge-base models (ClinicalSign, Finding, BodySystem, AnatomicalStructure, Modifier, Synonym, Abbreviation, Microorganism, MedicalArticle, DifferentialSyndrome, HostSpecies, ...)
- [x] Create `app/Models/Poultry`
- [x] Create `app/Filament/Resources/Drugs` — knowledge-base admin resources
- [x] Create `app/Filament/Resources/Poultry`
- [x] Create `resources/views/drugs` — knowledge-base views + drugs landing
- [x] Create `resources/views/poultry` — poultry landing + future views
- [x] Keep the existing flat `Disease`/`Species` models in Large Animals — the `diseases`/`species` tables are SHARED (current `disease_product` links + Drugs KB pivots `disease_clinical_sign`, `disease_species`, ...)
- [x] Verify PSR-4 autoloads `App\Http\Controllers\Drugs\`, `App\Models\Drugs\`, `App\Filament\Resources\Drugs\`, `App\Http\Controllers\Poultry\`, etc. (no composer change needed)

```
app/
├── Actions/  Enums/  Jobs/  Services/       # shared (unchanged)
├── Filament/
│   ├── Resources/                           # FLAT — existing Large Animals resources only
│   │   ├── Products/  Companies/  ...       # current (unchanged)
│   │   ├── Drugs/                           # NEW — knowledge-base admin resources (from new migrations)
│   │   └── Poultry/                         # NEW — future
│   ├── Pages/  Widgets/
├── Http/
│   ├── Controllers/                         # FLAT — current Large Animals controllers only
│   │   ├── ProductController.php  ...       # current (unchanged)
│   │   ├── Drugs/                           # NEW — knowledge-base controllers + DrugLandingController
│   │   │   ├── ClinicalSignController.php
│   │   │   ├── FindingController.php
│   │   │   └── ...
│   │   └── Poultry/                         # NEW — PoultryLandingController + future
│   └── Middleware/                          # + SetPartTheme (M4)
├── Models/                                  # FLAT — current Large Animals models only
│   ├── Product.php  Disease.php  ...        # current (unchanged; Disease/Species shared with Drugs KB)
│   ├── Drugs/                               # NEW — knowledge-base models (ClinicalSign, Finding, ...)
│   └── Poultry/                             # NEW — future
├── Providers/                               # shared (unchanged)

resources/views/
├── app/                                     # shared layouts + current Large Animals views (unchanged)
├── drugs/                                   # NEW — drugs landing + knowledge-base views
└── poultry/                                 # NEW — poultry landing + future views

routes/
├── web.php                                  # shared + large-animals routes (current)
├── drugs.php                              # NEW — drugs group (locale + drugs prefix)
└── poultry.php                            # NEW — poultry group (locale + poultry prefix)
```

---

## Milestone 3: Routing restructure

**Status:** Done

- [x] Keep shared routes at root (unchanged): `/`, blog, about, contact, privacy-policy, terms, login/register/logout, email/verify, profile (+ submissions)
- [x] Add `large-animals` prefix group: `/large-animals`, `/large-animals/products`, `/large-animals/products/compare`, `/large-animals/products/create-submission`, `/large-animals/products/{slug}`, `/large-animals/companies`, `/large-animals/companies/{slug}`, `/large-animals/diseases`, `/large-animals/diseases/{slug}`, `/large-animals/active-ingredients`, `/large-animals/active-ingredients/{slug}`, `/large-animals/search`
- [x] Keep route names unchanged (`products.index`, `companies.index`, `diseases.index`, `active-ingredients.index`, `search`) so existing `route()`/`routeIs()` calls keep working
- [x] Create `routes/drugs.php` — drugs group (locale prefix + `drugs` prefix), `GET /drugs` → `DrugLandingController`, future KB routes added here
- [x] Create `routes/poultry.php` — poultry group (locale prefix + `poultry` prefix), `GET /poultry` → `PoultryLandingController`
- [x] Register `drugs.php` + `poultry.php` in `bootstrap/app.php` `withRouting` `web` array (Laravel 13 supports `array|string`; each file gets `Route::middleware('web')`); shared + large-animals routes stay in `web.php`
- [x] Each part file repeats the locale-prefix group (`prefix => LaravelLocalization::setLocale()`, `middleware => 'localeViewPath'`) used in `web.php`
- [x] Add a `large-animals.home` route (`GET /large-animals`) so the navbar part tabs have a target for the Large Animals part
- [x] Name the part routes `large-animals.home`, `drugs.home`, `poultry.home` for the navbar part switcher
- [x] Set `hideDefaultLocaleInURL => true` in `config/laravellocalization.php` so parts are at `/large-animals`, `/drugs`, `/poultry` (Arabic stays `/ar/large-animals`)
- [x] Verify sitemap URLs update automatically (SitemapController uses `route()`)

> **Note:** `DrugLandingController`, `PoultryLandingController`, `resources/views/drugs/landing.blade.php`, and `resources/views/poultry/landing.blade.php` were created as **minimal stubs** so the `/drugs` and `/poultry` routes resolve. They will be fully built out in Milestone 5. English inline text only (no translatable keys touched, since Arabic files use special encoding).

---

## Milestone 4: Theming — same design, different color per part

**Status:** Done

- [x] Add `data-part="{{ $part ?? 'large-animals' }}"` on `<body>` in `resources/views/app/layouts/master.blade.php`
- [x] Create `SetPartTheme` middleware that sets `$part` from the route prefix (`large-animals`/`drugs`/`poultry`, default `large-animals`)
- [x] Register `SetPartTheme` on the localized route groups (registered globally via the `web` middleware group in `bootstrap/app.php`, which covers all localized route groups including the part files)
- [x] Add `[data-part='drugs']` orange token overrides in `resources/css/app.css` (brand / brand-strong / brand-medium / brand-soft / brand-subtle / brand-light / brand-softer / fg-brand*) incl. dark-mode variants
- [x] Add `[data-part='poultry']` green token overrides in `resources/css/app.css` incl. dark-mode variants
- [x] Keep Large Animals = current blue as the default (no changes to existing pages)

> Verified via `php artisan route:list` (all three parts present; names unchanged) and HTTP smoke tests — `/drugs` → `data-part="drugs"`, `/poultry` → `data-part="poultry"`, `/large-animals` → `data-part="large-animals"`; all return 200.

---

## Milestone 5: Part landings, hub & navbar

**Status:** Done

- [x] Create `DrugLandingController` (orange `/drugs` landing) using only semantic tokens
- [x] Create `PoultryLandingController` (green `/poultry` landing) using only semantic tokens
- [x] Create `resources/views/drugs/landing.blade.php` + `resources/views/poultry/landing.blade.php` extending the shared master layout
- [x] Add a parts section (3 cards linking the parts) to `resources/views/app/landing.blade.php` home hub

### 5.1 Navbar — single shared header + conditional part links

- [x] Add a Large Animals / Drugs / Poultry **part switcher** (tabs) to `resources/views/app/layouts/main-header.blade.php` — always visible on every page
- [x] Tint the active part tab with that part's color (blue/orange/green) via the `$part` value
- [x] Render the part-specific nav links with `@switch($part)`:
  - `large-animals`: Products, Add Product, Compare Products, Diseases, Active Ingredients, Companies (current links, unchanged)
  - `drugs`: minimal links (landing now; knowledge-base links — clinical signs, findings, body systems, ... — grow as you build them)
  - `poultry`: minimal links (landing for now, grows later)
- [x] Keep shared controls in all parts: search, theme toggle, language switcher, auth/user menu, "Platform" dropdown (blog, about, contact, privacy, terms)
- [x] Replace `routeIs('products.*')` active-state logic with `$part`-aware highlighting in the header
- [x] Apply the same part-switcher + conditional logic to the mobile sidebar (`main-header.blade.php` mobile section)
- [ ] Optional: extract per-part link lists into partials like `resources/views/app/layouts/parts/{part}-nav.blade.php`

---

## Milestone 6: Verify

**Status:** Done

- [x] Run `php artisan route:list` to confirm `/large-animals`, `/drugs`, `/poultry` routes
- [x] Run `php artisan migrate --pretend` sanity check
- [x] Run `vendor/bin/pint --dirty` on changed PHP files
- [x] Run `npm run build` (CSS token changes need a rebuild)
- [x] Manual check: large-animals pages blue, `/drugs` orange, `/poultry` green, shared pages intact

---

## Out of scope (user does it)

- [ ] Running the real `php artisan migrate`
- [ ] Creating models/factories/seeders for the new tables
- [ ] Building the Drugs knowledge-base public pages / Filament resources (under `app/Http/Controllers/Drugs`, `app/Models/Drugs`, `app/Filament/Resources/Drugs`, `resources/views/drugs`)
- [ ] Building full Poultry features
