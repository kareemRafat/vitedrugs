# Poultry Section Plan — Vetpedia

> **Status:** v18 — **Phases 1, 2, 4, 5 and 7 built; Phase 6 partially; Phase 3 partially.**
> Phase 1: 50/57 · Phase 2: 10/11 · Phase 3: 5/15 · Phase 4: 12/17 · Phase 5: 11/12 · Phase 6: 7/12 · Phase 7: 12/12
> **Date:** 2026-10-13
> **Supersedes:** v1–v17

---

## How to read this document

Tasks use markdown checkboxes. Tick a box when the task is genuinely done.

- **Phase 1** is the foundation — migrations and data. Nothing else can start until it is complete.
- **Phases 2–10** are one phase per page, in build order.
- Each phase has a **Milestone** (the "done" test) and **What this uses** (the plain-English explanation).

---

## 0. The Key Correction

> **v1 was wrong on a fundamental point.** It claimed there was no existing Large Animals
> implementation and that the Poultry section had to be ported/merged.
>
> **Reality:** `vitedrugs` **already has Large Animals fully built**, and Poultry is
> simply **the missing third part**.

### 0.1 Already built ✅

| Path | Contents |
|---|---|
| `app/Filament/Resources/LargeAnimals/` | **16 complete Filament resources** |
| `app/Filament/Clusters/` | `Diagnosis` · `Diseases` · `Filter` · `Search` · `Ingredients` |
| `app/Services/LargeAnimals/` | `DiagnosisService` (85 LOC) · `FilterService` · `DiagnosisShareService` · `FilterShareService` |
| `app/Http/Controllers/LargeAnimals/` | **11 controllers** |
| `app/Services/` | `Knowledge/DiseaseKnowledgePayloadTransformer` · `MedicalKnowledge/DiseaseDataLoader` · `Narrative/DiseaseArticleBuilder` |
| `app/Filament/Pages/LargeAnimals/` | `ImportDiseaseKnowledge` (accepts JSON file or pasted text) — **needs relocating to the shared cluster, see §3.4** |
| Database tables | `findings` · `body_systems` · `anatomical_structures` · `species` · `diseases` · `disease_classifications` · `differential_syndromes` · `clinical_signs` |

### 0.2 Empty / to build 🚧

| Path | Contents |
|---|---|
| `app/Filament/Resources/Poultry/` | **`.gitkeep` only** |
| `app/Models/Poultry/` | **empty** — no Poultry-specific models; the shared tables are scoped by `domain` instead (§3.2) |
| `app/Models/LargeAnimals/` | **24 models** — `HostSpecies` · `ClinicalSign` · `Finding` · `BodySystem` · `AnatomicalStructure` · `DiseaseClassification` · `Microorganism` and others |
| `app/Http/Controllers/Poultry/` | `PoultryLandingController` · `DiagnosticController` · `FilterController` · `DiseaseController` |

### 0.3 Infrastructure already scaffolded ✅

- **`SetPartTheme::PARTS = ['drugs', 'large-animals', 'poultry']`** — all three parts already registered. The part is auto-resolved from the **first URL segment** and shared to views. Any `/poultry/*` route works out of the box.
- **`routes/poultry.php` exists** with the locale prefix + `localeViewPath` middleware group.
- All three route files registered in `bootstrap/app.php` under `withRouting(web:)`.
- **Green theme verified working** — `/poultry` renders `data-part="poultry"` and resolves to emerald `#059669`. No work needed.

### 0.4 Conclusion

**No merge, no port.** There is an existing pattern to copy. Poultry = the same resources and services, scoped by `domain = 'poultry'`.

---

## 1. Verdict

### 1.1 The client wants one feature, described four times

From the transcript itself, stated twice:

> *"Clinical Finding is the same idea as the filter in Large Animals"*
> *"This is a duplication of the same idea we already have"*

All pages reduce to **one primitive**: pick species → pick symptoms/organ → get ranked diseases.
**The difference is presentation, not logic** → one engine + one results component + several thin views.

### 1.2 Do not port the old application

| | `newVitPedia/poultry` | `vitedrugs` |
|---|---|---|
| Framework | Laravel 10 | Laravel 13 |
| Admin | Filament 3 | Filament 5 |
| Frontend | jQuery + Madmin template | Livewire 4 + Tailwind 4 |

Port **the schema + the data only**.

### 1.3 The old app's "smart" services are dead code

The old app has 25 services / ~2,972 LOC (Bayesian, Geographic Prior, Mortality Prior, Necropsy Prior, FlockOutbreak, Prognosis) that ran against **empty tables**. The data now exists (§2.4) but those services are still not worth porting — the existing 85-LOC `DiagnosisService` already matches the pivot structure.

### 1.4 Two confirmed breakages in the old app

| Problem | Evidence |
|---|---|
| `/anatomy/{slug}` always 500s | `AnatomicalNavigatorController::show()` returns `view('anatomy.show')`; **that file does not exist** |
| Empty diagnostic file | `diagnostic/interview.blade.php` = **0 bytes**, no route |

---

## 2. Real Data Inventory

There are **two independent sources**, not one.

### 2.1 Source A — `vetpedia_poultry (1).sql` (7 MB, structured relational)

| Table | Rows | Note |
|---|---|---|
| `disease_finding` | **1,900** | all valid — **zero dangling** |
| `findings` | **1,118** | 458 `clinical_sign` + **660 `gross_lesion`** |
| `diagnostic_tests` | 451 | |
| `disease_species` | 302 | |
| `disease_age_groups` | 179 | |
| `diseases` | **151** | 149 linked to findings |
| `anatomical_structures` | 43 | |
| `transmission_routes` | 15 | |
| `risk_factors` / `disease_risk_factor` | 14 / 13 | |
| `body_systems` | 11 | |
| `species` | 9 | |
| `diagnostic_sessions` | 45 | ⭐ verification set (§3.3) |

Link breakdown: **1,044 → clinical signs**, **856 → gross lesions**, covering **149 / 151** diseases.

### 2.2 Source B — `poultry/storage/app/diseases/*.json` (154 files, 1.15 MB)

**One file per disease**, containing a structured knowledge document. Each file has ~13 populated fields.

| JSON key | Populated in | Notes |
|---|---|---|
| `gross_lesions` | **146 / 153** | ⭐ **928 lesions total**, richest field |
| `description`, `name`, `etiology` | 153–154 | |
| `host_species` | 152 | |
| `body_systems_affected` | 152 | |
| `important_organs_affected` | **152** | ⭐ feeds the Body Map (Phase 8) |
| `morbidity` / `mortality` | 150 | fills `disease_mortality_profiles` |
| `vaccination` | 147 | |
| `field_diagnosis_clues` | 146 | |
| `gross_lesions` | 146 | |
| `prevention_and_control` | 145 | |
| `clinical_manifestations` | 143 | |
| `epidemiological_clues` | 141 | fills `disease_epidemiology_profiles` |
| `bird_types` | 139 | |
| `susceptible_age_groups` | 136 | |
| `risk_factors` | 135 | |
| `diagnostic_tests` | 134 | |
| `synonyms` | 131 | |
| `production_effects` | 107 | |
| `transmission_routes` | 95 | |
| `pathognomonic_findings` | 94 | |
| `differential_diagnoses` | 72 | |
| `microscopic_lesions` | 42 | |
| `incubation_period` | 31 | |
| `treatment` | 17 | ⚠️ thin |

Sample lesion entry:

```json
{ "canonical_name": "hepatomegaly",
  "display_name": "Enlarged liver",
  "anatomical_location": "liver",
  "weight": 10, "specificity": "very_high",
  "is_pathognomonic": false,
  "notes": "Associated with lipid accumulation." }
```

### 2.3 What this revises

- **Lesions: 660 (SQL) + 928 (JSON)** — the JSON entries are richer (organ, weight, notes). See §6 for the merge rule.
- **Most of the "19 empty tables" now have a source.** `disease_lesion`, `disease_epidemiology_profiles`, `disease_mortality_profiles`, `disease_differential_diagnosis`, `disease_body_system`, `disease_diagnostic_test`, `disease_transmission_route`, `disease_age_groups` are all recoverable from the JSONs.
- **The Body Map is no longer guesswork** — `important_organs_affected` is populated for 152 diseases.
- **`treatment` is thin (17/154)** — the disease page's treatment section will be sparse.

### 2.4 Source C — `diagnostic_sessions` (45 rows)

The **verification set**. The old app had a "pick symptoms → get ranked diseases" tool, used 45 times. Each row is one recorded run:

```
species: Turkey · age: 70 days
symptoms picked: depression, sulfur-coloured diarrhoea
→ the system's ranked output
```

**Why it matters:** run the new engine on the same 45 inputs and check it ranks the same diseases in the same order. Without it we would be **using our own logic to verify our own logic**. Test data only — never shipped to production.

---

## 3. Where the Knowledge Data Lands

### 3.1 One table. One column.

```
Table:  diseases
Column: knowledge_payload   (JSON — already exists)
Match:  by slug
```

**No new table. No new rows.** Each of the 154 files fills in the `knowledge_payload` of one **existing** poultry disease row. Schema changes for this part: **zero**.

### 3.2 Two different "part" systems — do not mix them up

| System | Where | Values |
|---|---|---|
| **UI theme** | `SetPartTheme::PARTS` | `drugs`, `large-animals`, `poultry` *(hyphens)* |
| **Data domain** | new `domain` column | `large_animal`, `poultry` *(underscore)* |

`drugs` is a **theme only** — products never get a `domain` column.

Resulting row:

```
diseases
┌──────────┬───────────────────┬────────────┬──────────────────────────┐
│ id       │ name              │ domain     │ knowledge_payload        │
├──────────┼───────────────────┼────────────┼──────────────────────────┤
│ 01kvy... │ Newcastle Disease │ poultry    │ { clinical_signs,        │
│          │                   │            │   gross_lesions, ... }   │
└──────────┴───────────────────┴────────────┴──────────────────────────┘
```

### 3.3 The transformer exists but only 1 of 5 keys matches

`DiseaseKnowledgePayloadTransformer` already accepts *"a rich disease document wrapped in a top-level `disease` key"*. But the key names do not line up:

| Transformer expects | JSON actually has | Status |
|---|---|---|
| `clinical_manifestations` | `clinical_manifestations` | ✅ |
| `postmortem_findings` | **`gross_lesions`** | ❌ alias needed |
| `prevention_control` | **`prevention_and_control`** | ❌ alias needed |
| `diagnosis` / `diagnostic_methods` | **`diagnostic_tests`** | ❌ alias needed |
| `references` | *(absent)* | ⚠️ leave empty |
| — | `microscopic_lesions` | ❌ no target |
| — | `pathognomonic_findings` | ❌ no target |
| — | `field_diagnosis_clues` | ❌ no target |
| — | `epidemiological_clues` | ❌ no target |
| — | `important_organs_affected` | ❌ no target |
| — | `morbidity` / `mortality` | ❌ no target |
| — | `differential_diagnoses` | ❌ no target |

**Decision: extend the transformer to map all 13 fields** (not just the 5 the pages render today), then extend `DiseaseArticleBuilder` to render the new sections. Nothing is discarded.

### 3.4 The Filament import page — shared, not duplicated

There is exactly one custom Filament page for this today:

```
app/Filament/Pages/LargeAnimals/ImportDiseaseKnowledge.php
   → cluster: DiseasesCluster   (pinned to the Large Animals namespace)
   → disease picker: all diseases, no domain filter
   → one disease per upload
```

That shape is wrong for Poultry:

| Problem | Consequence |
|---|---|
| Lives under `Pages/LargeAnimals/` | Poultry editors cannot find it, and it reads as part-scoped when it is not |
| Disease picker lists **every** disease | Easy to attach a poultry payload to a large-animal disease |
| Manual — one disease per upload | Useless for 154 files |

**Decision (option A): relocate and generalise — do not build a second page.**

- [x] Move `ImportDiseaseKnowledge` out of `Pages/LargeAnimals/` to the shared `DiseasesCluster`
- [x] Update the view namespace from `filament.pages.large-animals.*` to the shared location
- [x] Add a `domain` select to the form, and scope the disease picker options by it
- [x] Default the form to `poultry` when reached from the Poultry cluster and `large_animal` from Large Animals — defaults set per-action instead; see §1.7
- [x] **Add a multi-file upload mode** (`FileUpload::make()->multiple()`) so all 154 JSONs load in one operation
- [x] Auto-match each uploaded file to a disease by filename slug (`newcastle-disease.json` → `newcastle-disease`)
- [x] Show a preview report before saving: *N matched · N unmatched*, listing the unmatched filenames
- [x] Wrap the batch save in a database transaction
- [x] Keep the existing single-file mode for later individual corrections

> **Why one page, not two:** two importers will drift apart. One page scoped by a `domain` selector
> serves both parts and cannot disagree with itself.
>
> **Why batch upload, not one-at-a-time:** 154 separate uploads is hours of manual clicking. Files are
> already named by disease, so slug matching makes the bulk case trivial — while still keeping a human
> in the loop, reviewing the report before anything is written.

---

## 4. Resolved Data Questions

### 4.1 Lesions — two sources, no scraping

v1 incorrectly reported the Lesion Atlas as empty; it was waiting on a separate `lesions` table. Lesions live **inside `findings`** as `type = 'gross_lesion'` in Source A, and in `gross_lesions` in Source B.

SQL → app mapping for `findings` is **1:1**:

| Dump (poultry) | `vitedrugs.findings` |
|---|---|
| `type = 'clinical_sign'` | `ontology_type = 'sign'\|'symptom'` + `category = 'clinical'` |
| `type = 'gross_lesion'` | `ontology_type = 'lesion'` + `category = 'postmortem'` |
| `body_system` (varchar) | `body_system_id` (FK → `body_systems`) |

`FindingFactory` already contains `'lesion'` and `'postmortem'` → the design is already compatible.

### 4.2 Clinical Cases — removed from scope

- `clinical_cases` is **empty** in the dump; no equivalent exists in `vitedrugs`.
- **The client never mentioned it** in the transcript. It was inferred from the old schema.

---

## 5. Import Mechanism

**Seeders and a Filament page. No Artisan commands. No new service classes.**

The project already ships **36 seeders**, including the exact entities we need:

```
SpeciesSeeder · BodySystemSeeder · AnatomicalStructureSeeder
FindingSeeder · DiseaseSeeder · ClinicalSignSeeder
DiseaseClinicalSignSeeder · DiseaseKnowledgePayloadSeeder · KnowledgeBaseSeeder
```

`KnowledgeBaseSeeder` already orchestrates the whole ontology. We extend that pattern rather than inventing new infrastructure.

| Source | Mechanism | Lands in |
|---|---|---|
| **A** — SQL dump | **Extend the existing seeders** with poultry rows | relational tables |
| **B** — 154 JSONs | **Filament upload page** (multi-file) | `diseases.knowledge_payload` |

```
php artisan db:seed --class=PoultryDataSeeder    → Source A
/admin → Import Disease Knowledge               → Source B
```

### 5.1 Seeder requirements

- Every seeder must use `updateOrCreate` (never `create`), so re-running after a corrected dump duplicates nothing
- The dump-parsing logic (~150 lines) lives **inside** the seeder class — no separate service
- Each seeder prints a per-entity summary: created, updated, skipped, failed
- Register the new poultry seeders in `KnowledgeBaseSeeder` (or `DatabaseSeeder`) in dependency order

### 5.2 Dump access

- **Scratch database (chosen):** import the 7 MB dump once into a throwaway MySQL schema, read it with normal queries, transform, drop it. Far more reliable than parsing INSERT statements as text.
- The dump path is **not hardcoded** — add `config/vetpedia.php` → `poultry_source_path` so it can be repointed without touching code.

---

## 6. Merge Rule — CLOSED, not needed ✅

**The two sources never merge, because they write to different places.**

```
Source A (SQL)  →  relational tables            findings · disease_finding · species · body_systems
Source B (JSON) →  diseases.knowledge_payload   (one JSON column)
```

The 660 SQL lesions power the **diagnostic engine** (Phase 4). The 928 JSON lesions power the **disease article page** (Phase 3). They sit side by side and never contend.

**Consequences:**
- No `MergeResolver` service
- No merge-rule decision
- No dedupe logic, no conflict logging

> ✅ **Closed. All Phase 1 decisions are now settled.**

---

## 7. The Dump's Pivot Matches the Existing Engine

`DiagnosisService::rank()` scores on `weight` · `is_pathognomonic` · `is_required` · `is_specific`.

| `disease_finding` (dump) | Maps to |
|---|---|
| `weight` | `weight` ✅ identical |
| `is_pathognomonic` | `is_pathognomonic` ✅ identical |
| `required_for_diagnosis` | `is_required` ✅ |
| `specificity` (`low`/`medium`/`high`/`very_high`) | `is_specific` ✅ |
| `negative_finding` | new — optional |
| `likelihood_ratio_positive` / `_negative` | schema present (currently NULL) |

**The existing 85-LOC engine can consume Poultry data with minimal change.**

---

## 8. Page Overview

| # | Page | Route | Component |
|---|---|---|---|
| 1 | Hub | `GET /poultry` | — |
| 2 | Diseases | `GET /poultry/diseases/{slug}` | `⚡disease-card` |
| 3 | Diagnosis | `GET/POST /poultry/diagnosis` | `⚡diagnosis-engine` |
| 4 | Filter | `GET/POST /poultry/filter` | `⚡finding-finder` |
| 5 | Search | `GET /poultry/search` | — |
| 6 | Comparison | `GET /poultry/compare` | `⚡disease-compare-builder` |
| 7 | Body Map | `GET /poultry/body-map` | `⚡body-map` |
| 8 | Lesion Atlas | `GET /poultry/atlas` | `⚡lesion-atlas` |
| 9 | Drugs | `GET /poultry/products` | reuse `products.*` |

### 8.1 Livewire v4 SFC conventions

Component file — note the `⚡` prefix (U+26A1) in `resources/views/app/components/poultry/`.
Invoked as `@livewire('poultry.{name}')`.

> `#[Url]` combined with the existing `DiagnosisShareService` gives **shareable diagnosis result links** with no extra work.

### 8.2 Tailwind v4 / Flowbite tokens (mandatory)

Use semantic tokens only — the old v3 utilities will **not render**:

`bg-brand` · `hover:bg-brand-strong` · `bg-neutral-primary-soft` · `bg-neutral-secondary-medium`
· `text-heading` · `text-body` · `text-fg-brand` · `border-default-medium` · `rounded-base`
· `shadow-xs` · `focus:ring-brand` · `focus:border-brand`

Icons via Blade components: `<x-lucide-search />` (`fuzzyfox/lucide-for-laravel`).

---

## 9. Locked Decisions ✅

| # | Decision | Value |
|---|---|---|
| 1 | URL base | **`/poultry`** |
| 2 | Page naming | **`/poultry/diagnosis`** and **`/poultry/filter`** (match `large-animals`) |
| 3 | `domain` column scope | **`diseases` + `species` only** |
| 4 | `domain` values | **`large_animal`** and **`poultry`** (underscores) |
| 5 | Drugs | **Filter the existing `Product` model** — no duplicate catalog |
| 6 | `diagnostic_sessions` | **Verification set** — tests only, not production |
| 7 | Stack | **Laravel 13 · Livewire 4 · Tailwind 4** |
| 8 | Clinical Cases | **Removed from scope** |
| 9 | Lesions | **Two sources** — 660 (SQL) + 928 (JSON) |
| 10 | Knowledge import | **Filament page — no Artisan command, no new services** |
| 11 | Knowledge target | **`diseases.knowledge_payload`** — one column, zero schema change |
| 12 | Transformer scope | **All 13 fields**, not just the 5 rendered today |
| 13 | Green theme | **Already working** — no change |
| 14 | `newVitPedia/` | **Keep in repo as-is** (option C) |
| 15 | Filament import page | **Option A — relocate `ImportDiseaseKnowledge` to the shared `DiseasesCluster`, add a `domain` filter.** No second page. |
| 16 | Bulk JSON load | **Multi-file upload with slug auto-matching** on that same page — one operation, not 154 |
| 17 | SQL dump load | **Extend the 36 existing seeders** — no new commands, no new services |
| 18 | Merge rule A↔B | ✅ **CLOSED — not needed.** Different destinations, never contends |
| 19 | Page views | **Shared, not copied.** `resources/views/{diagnosis,filter}/` serve both parts, parameterised by `$part`. Copying ~500 lines ×2 per tool would guarantee drift |
| 20 | Engine services | **Parameterised, not subclassed.** `DiagnosisService::rank()` and `FilterService::filter()` take a **required** `$domain`. There is no `Poultry\DiagnosisService` — a second copy of the scoring maths is the one thing that must not diverge |
| 21 | Species scoping | `host_species` has **no `domain` column** — it uses `taxonomy_group`. Part membership is mapped explicitly in `config/vetpedia.php` → `parts[].taxonomy_groups`, listing every group rather than excluding poultry with `!=` |
| 22 | Sign scoping | `clinical_signs` has no domain either. A sign belongs to a part when it is **reachable through at least one disease in that part**, via `ClinicalSign → diseases.domain` |
| 23 | Translation files | **Layered, not duplicated.** `lang/*/poultry.php` starts from `large-animals.php` via `array_replace_recursive` and overrides only bird-specific wording (~20 keys). A key added upstream arrives for free |
| 24 | Share links | **Versioned and domain-tagged.** Token `VERSION` = 2 with a `domain` field, rejected on mismatch. Both services are base64url-encoded |
| 25 | Large-animal tools | Now **scoped to `large_animal`** — 169 diseases → 18. A deliberate, visible behaviour change; see §2.1 |
| 26 | Search architecture | **One shared controller, service and view**, keyed on `$part`, with bucket membership declared per part in `config/search.php`. Forking per part would duplicate config-reading code and is how the unscoped-leak defect was able to survive unnoticed |
| 27 | Search buckets | Large-animals shows seven buckets, poultry four. A bucket is either `domain`, `part`, `through_disease` or `none` — the four vocabularies the data actually uses. Part membership by `taxonomy_group` alone is **not** sufficient: 14 of 384 `disease_host_species` rows cross the domain line |
| 28 | Fuzzy matching | **LIKE first, trigram Dice as a fallback only when LIKE comes up short.** Not Levenshtein: the doubled-letter typo it was meant to catch (`cocido` → `coccidiosis`) is 5 edits away, beyond any usable threshold |

---

## 10. Cleanup Completed ✅

`newVitPedia/` went from **195.4 MB → 8.67 MB**. Nothing outside that directory was touched.

**Removed (191 MB):** `vendor/` · `public/assets/` · `.git/` · `storage/framework/` · `storage/logs/`
**Removed (2.5 MB):** `composer.lock` · `public/` · `bootstrap/` · `tests/` · `.env` *(contained live AWS / Pusher / DB / mail credentials)* · `.env.example` · `artisan` · `composer.json` · `package.json` · `vite.config.js` · `phpunit.xml` · `.editorconfig` · `.gitignore` · `.gitattributes`

**Preserved:** the SQL dump · 154 disease JSONs · `app/` · `database/` (38 migrations) · `resources/` · `routes/` · `config/` · `README.md` · plan + requirements docs.

---

# 11. Execution Phases

> **Phase order matters.** Phase 1 gates everything.

---

## Phase 1 — Migrations & Data Foundation

**Milestone:** The database holds the Poultry dataset from both sources, scoped by `domain`,
verified against known counts, with knowledge payloads attached.
**Blocked:** every other phase.

**What this uses:** This phase has no page. It is the plumbing. It takes two inputs — a SQL dump
(151 diseases, 1,118 findings, 1,900 links, 660 lesions) and 154 JSON disease files (928 lesions
plus 12 more knowledge fields) — and files them into the existing tables with a `domain` label.

### 1.1 Schema

- [x] Create migration adding a `domain` column to `diseases`
- [x] Create migration adding a `domain` column to `species`
- [x] Define the column as an enum or string with exactly two values: `large_animal`, `poultry`
- [x] Backfill all existing rows to `large_animal`
- [x] Add indexes on `diseases.domain` and `species.domain` — implemented as composite `domain, is_active`
- [x] Verify the migration is reversible — `migrate:rollback --step=2` then `migrate`, both clean
- [ ] Confirm Large Animals pages render exactly as before — **the 8 admin smoke tests are still `markTestSkipped`, so this is unverified**

### 1.2 Source A mapping decisions

- [x] Map `findings.type = 'clinical_sign'` → `ontology_type` + `category = 'clinical'`
- [x] Map `findings.type = 'gross_lesion'` → `ontology_type = 'lesion'` + `category = 'postmortem'`
- [x] Decide how `findings.body_system` (free-text) maps to `body_system_id`, and what to do with unmatchable strings — **no `body_system_id` column exists on `findings`; value dropped, deferred to Phase 8**
- [x] Map `disease_finding.specificity` → `is_specific` — `high` / `very_high` only
- [x] Map `disease_finding.required_for_diagnosis` → `is_required`
- [x] Decide whether `diagnostic_tests`, `transmission_routes`, `risk_factors`, `disease_age_groups` import now or defer — **all four deferred to a later phase**
- [x] Document every dump column deliberately dropped — see the `PoultryDataSeeder` class docblock

### 1.3 Source B mapping decisions

- [x] Extend `DiseaseKnowledgePayloadTransformer` with aliases: `gross_lesions` → `postmortem_findings`, `prevention_and_control` → `prevention_control`, `diagnostic_tests` → `diagnosis`
- [x] Add output keys for the 8 currently-unmapped fields: `microscopic_lesions`, `pathognomonic_findings`, `field_diagnosis_clues`, `epidemiological_clues`, `important_organs_affected`, `morbidity`, `mortality`, `differential_diagnoses`
- [x] Confirm the transformer matches the JSON's `slug` field to `Disease.slug` — **done in the Filament page by filename, not in the transformer; the transformer never sees the disease**
- [x] Write regression tests for the transformer covering all 13 keys — **14 output keys, 12 tests**
- [x] ✅ No merge work required — see §6

### 1.4 Seeders — Source A (SQL dump)

Extend the existing seeders. No new commands, no new services.

- [x] Add `config/vetpedia.php` with `poultry_source_path`, defaulting to `newVitPedia/vetpedia_poultry (1).sql` — key is `vetpedia.poultry.sql_dump`, overridable by `VETPEDIA_POULTRY_SQL_DUMP`
- [ ] Create `PoultryDataSeeder` that loads the dump into a scratch MySQL schema, reads it, and drops the schema — **deliberately not done; the dump is parsed in-process instead. No scratch schema, no `mysql` CLI dependency, and it runs under SQLite in tests**
- [x] Seed `species` (9) → `domain = poultry`
- [x] Seed `body_systems` (11) and `anatomical_structures` (43)
- [x] Seed `findings` (1,118) — 458 signs + 660 lesions → **1,118 dump rows collapse to 1,072 distinct findings**; see §1.6
- [x] Seed `diseases` (151) → `domain = poultry`
- [x] Seed the `disease_finding` pivot (1,900) with weight, specificity, pathognomonic, required → **1,732 after merges**; see §1.6
- [ ] Seed `disease_species` (302) and `disease_age_groups` (179) — **`disease_species` is dead (its `disease_id` is `bigint` while `diseases.id` is a ULID, and nothing reads it). Seeded `disease_host_species` (302 → 311) instead, which is what `DiagnosisService` actually reads. `disease_age_groups` has no table and was dropped**
- [x] Use `updateOrCreate` everywhere so a re-run never duplicates rows
- [x] Print a per-entity summary: created, updated, skipped, failed
- [x] Register `PoultryDataSeeder` in `KnowledgeBaseSeeder` after its dependencies

### 1.5 Filament upload — Source B (154 JSONs)

Handled by the admin panel, not a command. See §3.4.

- [x] Relocate `ImportDiseaseKnowledge` to the shared `DiseasesCluster`
- [x] Support **multi-file upload** (`->multiple()`) so all 154 load in one operation — **requires `->preserveFilenames()`; Filament otherwise rewrites the name to a UUID and slug matching silently fails**
- [x] Auto-match each uploaded file to a disease by filename slug (`newcastle-disease.json` → `newcastle-disease`) — plus a 5-entry alias map in `config/vetpedia.php`
- [x] Show a preview report before saving: *N matched · N unmatched*, with the unmatched files listed — implemented as a *Preview only* toggle rather than a two-step modal
- [x] Run each payload through the extended transformer, write to `diseases.knowledge_payload`
- [x] Keep a single-file mode on the same page for later individual corrections
- [x] Wrap the batch save in a transaction
- [x] Log a summary: files processed, matched, unmatched, failed

### 1.6 Verification

- [x] Assert exactly 151 poultry diseases exist
- [x] Assert exactly 1,118 findings exist, of which 660 have `ontology_type = 'lesion'` → **actual: 1,089 findings total = 432 signs + 640 lesions + 17 curated. The 1,118 figure is dump *rows*, not findings: the dump spells the same finding three ways (`sudden death` / `sudden_death` / `sudden-death`) and 46 such duplicates merge on load, taking 660 lesions to 640**
- [x] Assert exactly 1,900 disease↔finding links exist, **zero** orphaned → **actual: 1,744 poultry links (1,848 including large-animal). 156 links collapse because their finding merged. Zero orphans confirmed**
- [x] Assert 149 of 151 diseases have at least one finding → **actual 150 of 151; only `fusarium-mycotoxicosis` has none**
- [x] Assert the Filament batch reports the expected matched/unmatched split — **record the real numbers here**
- [ ] Assert `important_organs_affected` is present for ~152 diseases in `knowledge_payload` — **blocked: the bulk import has not been run yet. 2 of 151 carry a payload**
- [ ] Spot-check 5 diseases by hand against both sources (Newcastle Disease, Coccidiosis, Avian Influenza, Aflatoxicosis, Marek's Disease)
- [x] Run the seeder twice and confirm row counts are unchanged (idempotency proof)
- [x] Confirm the Large Animals test suite still passes — **69 passed, 8 skipped; the 8 skips are pre-existing `markTestSkipped` admin smoke tests, not regressions**
- [x] Write all the counts above as automated tests — `PoultryDataSeederTest` (13), `KnowledgePayloadTransformerTest` (12), `ImportDiseaseKnowledgePageTest` (10), `ClinicalSignSeederTest` (8)

#### Real matched / unmatched split

**151 matched · 3 unmatched · 0 failed.**

Three documents are intentionally unresolved and are listed in the comment above `knowledge_file_aliases` in `config/vetpedia.php`:

| Document | Why it has no target |
|---|---|
| `duck-viral-hepatitis` | the dump splits this into type 1, type 2 and type 3 — three separate Diseases |
| `duck-virus-enteritis` | no such Disease exists in the dump |
| `mycotoxicosis-family-profile` | a cross-disease profile, not a Disease |

Five more were rescued by the alias map, after being worded differently from the Disease they describe:

| Document | Disease |
|---|---|
| `ostrich-codiostomum-infection` | `codiostomum-struthionis-infection` |
| `ostrich-feather-pecking-and-cannibalism` | `feather-pecking-and-cannibalism` |
| `ostrich-ingested-toxins` | `ostrich-ingested-toxicosis` |
| `ostrich-trauma` | `trauma` |
| `oosporein` | `oosporein-toxicosis` |

### 1.7 Admin access

- [ ] Add the `Poultry` Filament cluster — **deliberately skipped; a cluster means duplicate resources, which contradicts the one-shared-page decision. The import page is domain-scoped instead**
- [x] Add a `domain` select to the Diseases and Species admin forms — **plus an infolist badge and a table column + filter, on both resources.** Placed first in each form, because a Disease saved into the wrong domain disappears from the poultry section silently, so the field preventing it should not be below the fold
- [ ] Default new records inside the cluster to `poultry` — **superseded.** With no cluster there is no poultry entry point to anchor a default to, and a `?domain=poultry` query-param default would be an affordance nothing links to. The visible Select makes this unnecessary. Revisit if Phase 2 gives poultry an admin landing spot
- [x] Confirm an editor can fix a poultry disease without touching a large-animal one — **`domain` was previously invisible *and* unfixable in the admin: absent from the form means Filament never submits it, so a miscategorised record could only be corrected by re-running a seeder or using tinker. Now editable. One edge remains, see §1.9**
- [x] Relocate `ImportDiseaseKnowledge` from `Pages/LargeAnimals/` to the shared `DiseasesCluster` (§3.4) — namespace moved to `App\Filament\Pages\Diseases`
- [x] Move its Blade view out of `filament/pages/large-animals/` to the shared namespace
- [x] Add a `domain` select to the import form and scope the disease picker options by it
- [x] Default the form's `domain` from whichever cluster the editor arrived from — **bulk action defaults to `poultry`, single action to `large_animal`; not cluster-derived because there is no Poultry cluster**
- [x] Confirm the relocated page is reachable from both the Large Animals and Poultry clusters — reachable at `/admin/diseases/import-disease-knowledge`; it was already on `DiseasesCluster` before the move, so the URL is unchanged

### 1.8 Surface `domain` in the admin (completes §1.7)

Not in the original plan, and it should have been. Phase 1 put `domain` on the models and wired it
into one custom page, then left the shared Disease and Species resources untouched — so the column
that separates the whole site was invisible to the people who maintain it.

**What it cost before this was fixed**

1. An editor opening a disease record could not tell which section it belonged to.
2. A miscategorised record could not be corrected through the admin at all — `domain` was in
   `$fillable` but absent from the form, so Filament never submitted it.
3. A new record saved from the admin silently inherited the column default, `large_animal`.

**What was built** — six files, additive, no change to existing records:

| File | Added |
|---|---|
| `Diseases/Schemas/DiseaseForm.php` | `Select`, first field |
| `Diseases/Schemas/DiseaseInfolist.php` | `TextEntry` badge |
| `Diseases/Tables/DiseasesTable.php` | `TextColumn` badge + `SelectFilter` |
| `Species/Schemas/SpeciesForm.php` | `Select`, first field |
| `Species/Schemas/SpeciesInfolist.php` | `TextEntry` badge |
| `Species/Tables/SpeciesTable.php` | `TextColumn` badge + `SelectFilter` |

`domain` is **editable**, not locked. A mistake is visible immediately in the filtered table, and
locking it would block the legitimate recategorisation case.

Also added: `app/Models/Concerns/HasDomain.php`, a trait carrying the constants, `domainOptions()`
and the three scopes. Four copies of the same label array across the Filament files, plus the
duplication already in `Disease` and `Species`, made this worth doing.

Tests: `tests/Feature/DomainAdminAccessTest.php` — 6 tests, including that a miscategorised disease
can now be corrected.

> ⚠️ **One edge still open.** `Findings`, `HostSpecies` and `AnatomicalStructures` are shared and
> unscoped, so an editor who has filtered to poultry can still attach a large-animal host species to
> a poultry disease through the relation manager. Closing it means either a domain filter on those
> resources or a cluster — a decision not taken yet.
>
> **Update at v11 — still open, and now slightly more awkward to ignore.** `HostSpecies::scopeForPart()`
> now exists, and the public poultry pages use it, so the *reader* side of this is closed. But the
> **editor-facing hole is untouched**: nothing in the Filament relation manager calls the scope, so an
> admin can still cross-attach. Closing it is now a small, obvious change rather than an open question.
> Recorded here rather than silently fixed, because it is Phase 1 admin work and this was frontend work.

### 1.9 Unplanned repair — `ClinicalSignSeeder` (completes §1.4)

Phase 1 could not be verified until this was fixed, because `db:seed` **died** on it. It is
recorded here because it changes shared ontology that later phases depend on.

`clinical_signs` is unique on `(finding_id, modifier_id, anatomical_structure_id)`, and the seeder
described 37 signs of which only 15 were representable:

| Failure | Count | Cause |
|---|---|---|
| `1062 Duplicate entry` | **14** | two signs sharing a finding *and* an anatomy are the same row — `tongue vesicle` collided with `oral vesicle`, and so on across five groups |
| `finding_id` NOT NULL | **8** | the finding was left `null` — anemia, lacrimation, weakness, tachycardia, lymphadenitis, submandibular lymphadenitis, infertility, temporary infertility |
| silently wrong | — | `firstOrCreate(['canonical_name' => …])` matched a column with no unique index, so once `PoultryDataSeeder` wrote 1,072 modifier-less signs sharing names like `pyrexia`, it would match a poultry row and skip the curated sign |

Fixed by making each sign representable rather than dropping concepts — a veterinary reference
cannot lose the difference between *profuse* and *hemorrhagic* diarrhea:

- `FindingSeeder` — added **15 findings**: the 8 that were missing, plus 7 qualifier findings
  (`hemorrhagic diarrhea`, `profuse diarrhea`, `respiratory distress`, `tachypnea`, `mastitis`,
  `mammary gland heat`, `hoof sloughing`).
- `AnatomicalStructureSeeder` — added **3 structures** (`tongue`, `interdigital_space`,
  `coronary_band`) so the vesicle group separates on anatomy instead of colliding.
- `ClinicalSignSeeder` — rewritten to match on composition, resolve anatomy by `canonical_name`,
  look names up in two queries instead of 74, throw a named exception listing anything unresolved,
  and only write when a value actually differs.
- `tests/Feature/LargeAnimals/ClinicalSignSeederTest.php` — 8 tests covering both crash modes,
  idempotency, and the poultry-sign collision.

> ⚠️ **Needs review:** the 18 new Arabic display names were written by the agent and have not been
> checked by a native speaker. They are content, not code.

> ⚠️ **Also found, not fixed:** `migrate:fresh` shells out to the `mysql` CLI to load
> `database/schema/mysql-schema.sql`, and `mysql` is not on `PATH` on this machine
> (`C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin` must be added). Unrelated to Poultry, but it makes
> the documented reset path fail.

---

## Phase 2 — Hub (Home)

**Route:** `GET /poultry` · **Milestone:** `/poultry` loads in both English and Arabic, shows live
counts, and links to every section built so far.

**What this page is used for:** The front door of the Poultry section. Its job is to give a visitor a
clear map of what is inside and let them jump straight to the tool they need. It shows live numbers
(how many diseases, how many symptoms) so the section feels populated, then a grid of cards.

### Tasks

- [x] Upgrade `PoultryLandingController` (currently returns an empty view)
- [x] Build the landing view under the Poultry view namespace
- [x] Add hero section with title and one-line description, Arabic and English — added `lang/en/poultry.php` and `lang/ar/poultry.php`; the view previously hardcoded English
- [x] Add a stats strip: diseases, findings, lesions, species — all read live, never hardcoded — verified live: **151 · 1,075 · 640 · 10**
- [x] Add a card grid linking to each section — **2 cards, now pointing at `poultry/*` rather than `large-animals/*`.** See §2.1
- [x] Hide cards for sections not yet built — no dead links — asserted by test: the page must not contain `poultry/diseases`, `/compare`, `/lesions`, `/products` or `/search`
- [x] Use Flowbite v4 semantic tokens only
- [x] Add Lucide icons to each card
- [x] Responsive: 1 column mobile, 2 tablet, 3–4 desktop
- [ ] Verify the RTL layout for Arabic — **asserted `dir="rtl"` and that the Arabic strings render; nobody has looked at it.** Re-checked at v11 and still no human eye on it: `/ar/poultry/diagnosis` returns 200, renders `dir="rtl"`, and resolves **zero** unresolved translation keys, but layout and typography still need a person before launch
- [ ] Confirm the green part theme and correct logo/colours render — **partially verified.** `/poultry/diagnosis` renders `data-part="poultry"`, so `SetPartTheme` resolves correctly. The **logo and colour choices themselves are still unrendered and unchecked by a human**

### 2.1 The cross-part defect — CLOSED ✅

Phase 2 was built before Phases 3–10, so **no `/poultry/*` section existed to link to**. Three
options were weighed:

| | Approach | Outcome |
|---|---|---|
| **A** | Stats only, cards arrive with each phase | Honest but a near-empty page |
| **B** | Link out to the working `large-animals` tools | Page is useful today, cards are cross-part |
| **C** ✅ | Promote the routes to `/poultry/*` now | Re-scoping Phases 4 and 5 into Phase 2 |

**B shipped first** so Phase 2 was useful before Phases 4 and 5 existed. **C has since been built**
and B's defect is closed.

> ⚠️ **The defect B created.** Clicking *Differential Diagnosis* or *Clinical Filter* left the poultry
> section entirely. `SetPartTheme` resolves the theme from the **first URL segment**, so
> `/large-animals/diagnosis` re-themed the whole page as Large Animals — heading, colours and all —
> with no way back. A visible notice on the hub admitted the tools were shared; it did not fix the
> navigation. The notice is now **deleted**, because it is no longer true.

Two pre-existing bugs that the defect exposed were **live on the Large Animals page** and are now
fixed on both sides:

1. **`suggestions()` was unscoped.** It matched `display_name LIKE %q%` across all 1,109 clinical
   signs, so a poultry user was offered large-animal signs. Invisible only because the page said
   "Large Animals". Both parts now filter through `diseases.domain` — verified live: *Hoof sloughing*
   and *Interdigital vesicle* return nothing on `/poultry`, and *Ataxia* returns results on both
   because it is genuinely linked to diseases in each.
2. **Share links carried no domain.** A link made under Large Animals, opened under
   `/poultry/diagnosis/results`, would have rendered large-animal results. Tokens are now versioned
   and domain-tagged; a mismatch is rejected and the user is told the link expired.

> **A third, latent bug surfaced while fixing the second.** `DiagnosisShareService::create()`
> returned raw base64, which can contain `+`. In a query string `+` decodes as a space, so roughly
> one token in 64 silently failed to decrypt. Both share services are now base64url-encoded.

#### What C actually cost — four deviations from the estimate

v10 estimated this as *"6 routes, a new domain-scoped controller, and 3 copied views (~16 KB) ·
116 lang keys for diagnosis and 28 for filter, × 2 languages"*. All four numbers were wrong:

| v10 estimated | Actually built | Why |
|---|---|---|
| 6 routes | **10** | the disease detail route was pulled forward from Phase 3 — without it every result link dead-ended back into Large Animals, which is the defect being fixed |
| 1 controller | **3** (+1 shared trait) | `DiagnosticController`, `FilterController`, `DiseaseController`, plus `ScopesToSitePart` for the validation rules |
| 3 copied views | **0 copied** | 5 views in `resources/views/{diagnosis,filter}/` are **shared** and parameterised by `$part` (see §9.19) |
| 144 lang keys ×2, hand-written | **~20 overrides ×2** | `lang/*/poultry.php` starts from `large-animals.php` via `array_replace_recursive` and overrides only the wording that is wrong for birds (§9.23) |

Copying was rejected because the diagnosis and filter views are ~500 lines each including two inline
Alpine components; forking them would guarantee drift for no benefit.

#### One deliberate behaviour change worth stating plainly

**The large-animal tools are now scoped to `large_animal`.** Their diagnosis and filter results go
from 169 diseases to 18. This is the point of the fix — the two parts are now symmetric — but it is a
visible reduction on a previously-working page, so it is recorded here rather than left implicit in a
tick. Large-animal users can no longer browse poultry diseases from the large-animal pages; that is
what `/poultry` is for.

### 2.2 Judgement calls worth recording

- **Findings and lesions are counted through the disease links**, not off the `findings` table
  directly. A bare `Finding::count()` returns 1,089 including large-animal-only findings; the
  link-scoped query returns 1,075. Same reasoning as the admin filters in §1.8.
- **Species is counted by `taxonomy_group = 'poultry'` (10), not by disease link (13).** Counting by
  link includes cattle, sheep, goat, pig and calf, which are legitimately linked to cross-host
  diseases such as salmonellosis — correct data, wrong number for a "bird species" figure.
  > Worth a separate look: are those cross-host links intentional, or did `DiseaseHostSpeciesSeeder`
  > sweep across the now-shared `diseases` table?
- **The Arabic is agent-written and unreviewed** — 18 strings here, plus 18 in §1.9. Both need a
  native speaker before launch.

---

## Phase 3 — Diseases

**Routes:** `GET /poultry/diseases` (list) + `GET /poultry/diseases/{slug}` (detail)
**Milestone:** All 151 poultry diseases are browsable, and each detail page shows its full knowledge
payload — not just symptoms.

**What this page is used for:** The reference library. A vet comes here to look up a disease — what it
is, which birds it affects, how old they are when it hits, which symptoms point to it, and what to do
about it. Everything else on the site links into these pages.

> ⏩ **Part of Phase 3 was built at v11, out of order.** Every row in a Phase 4 or Phase 5 result links
> to a disease detail page. Without one, every link on those pages dead-ended back into
> `/large-animals/diseases/{slug}` — re-theming the page as Large Animals, which is the exact defect
> §2.1 was closing. So `Poultry\DiseaseController` and the detail route were pulled forward. The
> **browse/list half of Phase 3 remains unbuilt** and is still the bulk of the remaining work here.

### Tasks

- [x] Add `Poultry\DiseaseController` mirroring `LargeAnimals\DiseaseController` — ⏩ pulled forward at v11; resolves strictly against `domain = poultry`
- [ ] Add the list route, paginated, alphabetised, filtered to `domain = poultry`
- [ ] Add letter/group navigation for browsing 151 entries
- [ ] Build the list view using the existing Large Animals disease card as the base
- [x] Add the detail route, resolved by slug, scoped to `domain = poultry` — ⏩ pulled forward at v11; a large-animal slug is a **404** here, asserted by test
- [ ] Show: name, Arabic name, description, etiology, affected species, bird types, age groups, synonyms — **partial.** The shared view renders name, Arabic name, description, etiology, affected species, zoonotic/notifiable/OIE classification, the knowledge-payload sections and related products. **`bird_types`, `age_groups` and `synonyms` are not shown** — the first two have no table (§1.4)
- [x] Show the symptom list with each symptom's weight, pathognomonic and required flags
- [x] Visually distinguish pathognomonic and required symptoms — three badges: pathognomonic, required, specific
- [ ] Extend `DiseaseArticleBuilder` to render the 8 new knowledge sections: gross lesions, microscopic lesions, field diagnosis clues, epidemiological clues, pathognomonic findings, differential diagnoses, morbidity/mortality, important organs — **blocked on the bulk JSON import (§1.6); 2 of 151 diseases carry a payload**
- [ ] Show the **treatment** section — ⚠️ note only 17 of 154 diseases have treatment data, so render a graceful empty state
- [ ] Link each disease to the diagnosis tool, pre-loaded with its key symptoms
- [ ] Link each disease to the drugs that treat it (Phase 9)
- [x] Add `404` handling for unknown slugs — via `firstOrFail`; asserted by test
- [ ] Add Arabic names/descriptions for at least the top 20 diseases
- [ ] Feature tests: list renders, detail renders, unknown slug 404s, large-animal slug rejected — **3 of 4 covered** at v11: detail renders, unknown slug 404s, large-animal slug rejected. **The list-render assertion waits on the list route**

---

## Phase 4 — Diagnosis Engine

**Routes:** `GET/POST /poultry/diagnosis`
**Milestone:** A user picks a species and ticks symptoms, and gets a ranked disease list that matches
the expected output on all 45 verification cases.

**What this page is used for:** The flagship feature and the main reason the client wants this section.
The user has a sick bird and does not know what is wrong. They pick the bird type, tick the symptoms
they can see, and get a ranked list of the most likely diseases with a confidence level and an
explanation of which symptoms drove the ranking.

> **Built at v11 on the real `/poultry/diagnosis` routes.** The engine was not rewritten — see §9.20.
> Every note below records what the live page actually does, not what was planned.

### Tasks

- [x] Adapt `LargeAnimals\DiagnosisService` into `Poultry\DiagnosisService`, scoped to `domain = poultry` — **done, but not as a separate class.** `DiagnosisService::rank()` takes a **required** `$domain` and applies `->forDomain($domain)`. A `Poultry\DiagnosisService` subclass would have been a second copy of the scoring maths, which is exactly the thing that must not drift. Same for `FilterService` in Phase 5
- [x] Keep the existing scoring approach — weight plus pathognomonic, required and specificity bonuses — **unchanged and verified live.** Worked example: Chicken + Ataxia + Head Tremors + Neck Tremors (each `weight 10`, pathognomonic, specific) scores **210** for *Avian Encephalomyelitis* against **70** for the runner-up → 100% vs 33%
- [x] Map the imported pivot flags onto the scoring correctly (see §1.2) — **confirmed end-to-end against live data**, not just in unit tests
- [x] Add the species picker as step one — **10 poultry species, not 9** (`taxonomy_group = 'poultry'`; see §2.2 for why 9 was the wrong figure)
- [ ] Build the symptom picker grouped by body system, searchable, using the 458 clinical signs — **searchable ✅, grouped by body system ❌.** The large-animal picker was reused rather than rebuilt. Also note the sign count: **1,080 of 1,109 signs are poultry-linked**, so the "458 signs" figure in §2.2 was a dump-row count, not a reality
- [x] Build the results view: ranked diseases with a confidence percentage — three tiers: primary ≥50%, secondary 30–49%, low support <30%
- [x] Show *why* each disease ranked where it did — matched-signs column plus a match-score percentage (matched weight ÷ total disease weight)
- [x] Show "key symptoms you did not select" — **this is what makes the tool useful rather than a guessing game.** Confirmed live: the Avian Encephalomyelitis run surfaces *Partial Leg Paralysis, Complete Leg Paralysis, Incoordination, Blindness, Lens Opacity*
- [x] Sync species and symptoms to the URL so results can be bookmarked and shared
- [x] Wire the URL state into the existing share mechanism — via the existing encrypted-token mechanism, no new infrastructure
- [ ] Handle the empty case — nothing selected yet — **unreachable by design.** `InitialDiagnosisRequest` enforces `min:3` signs, so the form cannot be submitted empty. The refinement step's empty state covers the case that can actually happen
- [x] Handle the no-match case — symptoms matching no poultry disease — empty state with guidance text
- [ ] Add an age filter using the 179 age-group records — **no table exists**; `disease_age_groups` was dropped in Phase 1 (§1.4). Needs the data landing first
- [ ] **Verify against the 45 `diagnostic_sessions`** — the engine must rank the same diseases in the same order — **still the largest unticked item in this phase**
- [ ] Fix whatever the verification reveals, re-run until it passes — blocked by the line above
- [x] Automated tests for ranking order, no-match path, empty path — `PoultryDiagnosticTest`, 11 tests: species scoping, ranking, large-animal exclusion, cross-part species rejection, cross-part sign rejection, suggestion scoping, share-link rejection, share-link round trip, detail page reachability, large-animal slug 404, and the large-animal → poultry redirect
- [x] Confirm Large Animals diagnosis still works unchanged — **it works, and it is deliberately *changed*.** Large-animal results are now scoped to `large_animal`: 169 diseases → 18. See §2.1 for why that is the intent, not a regression. Verified live: signs *Pyrexia / Oral vesicle / Tongue vesicle* on Cattle rank *Foot and Mouth Disease* first

---

## Phase 5 — Clinical Findings Filter

**Routes:** `GET/POST /poultry/filter`
**Milestone:** A user selects symptoms and gets matching diseases, working as the exact reverse of
the diagnosis page.

**What this page is used for:** The mirror image of the diagnosis tool — and the point the client made
himself. The diagnosis page asks *"I have these symptoms, what is it?"*. This page asks *"I saw this
symptom, what could cause it?"*. A farmer who spots one worrying sign but has no full picture uses this.

> **Built at v11 on the real `/poultry/filter` routes**, sharing both the controller logic and the
> views with Phase 4 — see §9.19 and §9.20.

### Tasks

- [x] Adapt `LargeAnimals\FilterController` and `FilterService` into Poultry equivalents — **parameterised, not duplicated.** `FilterService::filter()` takes a required `$domain`; `Poultry\FilterController` is a thin sibling of the large-animal one
- [x] Build the symptom multi-select using the same 458 clinical signs — **1,080 poultry-linked signs** (§2.2's 458 was a dump-row count). Type-ahead search, minimum 2 characters
- [x] Support one symptom and several — `min:1`
- [x] Build the results view: every disease matching the selected symptoms
- [ ] Show overlap — how many of a disease's symptoms the user selected out of its total — **partial.** Matched signs are shown as chips and results are **sorted by match count**, but the *"n of m"* ratio is not rendered
- [x] Sort by match strength
- [x] Link each result to the disease detail page — to `/poultry/diseases/{slug}`, the Phase 3 page pulled forward for exactly this reason
- [x] Add search by symptom name
- [x] Sync selections to the URL
- [x] Add empty state and no-results state
- [x] Reuse the Phase 4 results component rather than writing a second one — **one view tree serves both tools across both parts**
- [x] Feature tests: single symptom, multiple symptoms, no results — `PoultryFilterTest`, 9 tests. Verified live: Chicken + *Ataxia* matches 13 poultry diseases; clearing the species raises the count and still returns no large-animal disease

---

## Phase 6 — Global Search

**Route:** `GET /poultry/search`
**Milestone:** One search box returns matching diseases, symptoms, species and products on submit.

**What this page is used for:** A single entry point when the user does not know which section to go
to. Instead of guessing whether the answer lives in the disease library or the drug catalogue, they
type one word and get everything relevant. The box is a plain form, deliberately identical in shape to
`/drugs/search`: you type, and results appear when you submit. A live dropdown was built and then
removed — see deviation 5.

### Tasks

- [x] Adapt `LargeAnimals\SearchController` into a Poultry search controller → **built as one shared
  controller instead of a fork.** `Knowledge\SearchController` reads `$part` from the request and both
  parts point at the same service and view. Forking would have duplicated ~80 lines that only exist to
  read configuration. The old `LargeAnimals\SearchController` and its view are deleted.
- [x] Search across diseases, findings, species, and (Phase 9) products → **findings became clinical
  signs, and products were pulled forward.** See the two deviations below.
- [x] Filter to the Poultry domain except products, which use species matching → **products are scoped
  through `disease_product` → `diseases.domain`, not through species.** Species matching would have let
  the 14 cross-domain `disease_host_species` rows leak poultry drugs into large-animal results. Nine of
  the thirty products treat a poultry disease today, so the bucket is populated.
- [ ] Add a live suggestions endpoint returning matches as the user types → **built, then removed.**
  `GET {part}/search/suggestions` returned 404 and is no longer registered. The dropdown it fed is gone;
  see deviation 5.
- [ ] Group dropdown results by type, with icons per type → **built, then removed with the dropdown.**
  Every bucket still renders as its own card with an icon and a count, so the grouping survives — only
  the dropdown did not.
- [x] Support Arabic search terms as well as English → every bucket searches its `*_ar` column
  regardless of locale, so an English query on the Arabic page still matches. On the Arabic locale the
  Arabic name becomes the heading and the English one drops to the sub-line, matching the
  `localized_name` / `localized_display_name` accessors the models already expose.
- [x] Add fuzzy matching so near-misses still find something → **trigram Dice, not Levenshtein.** See
  the third deviation below. LIKE runs first and the near-miss pass only runs when LIKE came back short.
- [ ] Build the full results page with type filters → **the page is built; the type filter chips are
  not.** Every bucket renders as its own card with a count, so results are already visually grouped,
  but there is no control to narrow to one type.
- [x] Add an empty state with helpful suggestions → no-match state links onward to the filter tool and
  the diagnosis tool
- [ ] Debounce input so we do not query on every keystroke → **built, then removed with the dropdown.**
  Nothing is queried while typing, which satisfies the intent, but there is no longer any `@input`
  handler to debounce.
- [ ] Rate-limit the suggestions endpoint → **the endpoint it limited is gone**, so the `60/min`
  limiter and `search.too_many_requests` were deleted with it. `/drugs/search` is equally unthrottled
  and un-debounced, so the two are now consistent.
- [x] Tests: English match, Arabic match, fuzzy match, no results → 54 tests in
  `tests/Feature/Knowledge/`, six of them the part-isolation cases the old page failed, nine of them
  added at v14 for paging, real totals, hidden empty buckets and product relation matching

### Deviations from the tasks above

Five of these were decided against the wording above, so they are recorded here rather than left
implicit in the diff.

**1. Search `ClinicalSign`, not `Finding`.** The tasks say "findings", but `findings` (1,089 rows) has
no `domain` column and no direct link to `Disease` — only `clinical_signs` does. They are also the same
vocabulary at two layers: `ClinicalSign` is `Finding` plus anatomical structure and modifier, and the
seeder writes one plain sign per finding. Exposing both buckets would render the same matches twice,
which is precisely the defect this work set out to fix. One "Clinical Signs" bucket instead, and it is
what the diagnosis tool already searches. `Finding` remains reachable on the admin side.

**2. Trigram similarity, not Levenshtein.** The intended design was LIKE first, then a Levenshtein
pass. Measured against the real data, Levenshtein cannot do the job it was chosen for:
`levenshtein('cocido', 'coccidiosis')` is **5**, so any threshold loose enough to catch that also
matches noise. The trigrams of `cocido` (`coc`, `cid`) overlap `coccidiosis` directly, giving
Dice ≈ 0.31 against a 0.3 floor. It is also O(n) rather than O(n·m), and it only runs when LIKE misses,
so the common case is still one query per bucket.

**3. Products pulled forward from Phase 9.** The task text deferred products to Phase 9 but then
described how to scope them, which is contradictory. Nine of the thirty products already treat a poultry
disease, so leaving the bucket empty would have shipped a search page that silently omitted part of its
own data.

**4. The near-miss threshold stayed at 0.30, and a front-anchoring gate was added instead.** This one
corrects deviation 2 above. A 0.30 Dice floor was chosen without measuring the corpus against false
positives, and it produced them: `chicken` matched **thirteen** clinical signs beginning "Thickened",
because `chicken` and `thickened` share the interior trigram run `hic/ick/cke/ken` and score **0.667**.

The obvious fix — raise the floor to 0.50 — **does not work**. 0.667 clears 0.50 comfortably. The two
words only begin matching at their *second* trigram, so what separates a typo from an accident of
spelling is not how similar they are but *where* they start matching. A near-miss must therefore be
**anchored at the front of the candidate**: same first trigram, or — to also catch transpositions —
same leading two characters. `pnuemonia` → `pneumonia` is the transposition case, where `pnu` and `pne`
differ but `pn` survives.

Anchoring removes the false positives on its own, so raising the score was unnecessary *and* harmful:
`cocido` → `coccidiosis` is anchored but scores only **0.308**, and a 0.50 floor would have thrown it
away. `min_score` therefore stays at 0.30 and anchoring is a hard requirement rather than a relaxed
alternative. Measured against the corpus, 8 of 9 known cases now behave correctly.

**5. The suggestions dropdown was built and then removed.** The tasks above asked for a live endpoint, a
grouped dropdown, debouncing and a rate limit. All four shipped, and all four were then removed, because
the page was compared against `/drugs/search` and the two did not match: drugs is a plain form, and the
poultry and large-animal pages had grown a keystroke-driven dropdown the drugs page has never had.

The search view is now a plain GET form — no `x-data`, no `@input`, no `<script>` — matching drugs
exactly. With the dropdown gone, `GET {part}/search/suggestions` returned 404 and was deleted along with
`SearchService::suggestions()`, the `search-suggestions` rate limiter, `search.limits.suggestions*` and
`search.too_many_requests` in both locales. Grouping is not lost: every bucket still renders as its own
card with an icon and a count.

The four unticked task boxes above are left open rather than deleted, so the reversal stays visible in
the plan instead of disappearing from it.

> ⚠️ **Corrected at v14.** This entry claimed the page was *"matching drugs exactly"*. It was not —
> only the **form** was. The **results** still differed in four ways, all of which are now fixed; see
> the v14 section below. The v13 claim was true of the input and overstated about the output, and the
> difference was not visible in a diff because the drugs page was never opened next to it.

### v14 — Parity with `/drugs/search`

The v13 work made the two pages *look* alike. Comparing them line by line showed the results half was
still a different design, and in three of the four cases it was worse, not merely different.

| | `/drugs/search` (before) | poultry + large-animals (before) |
|---|---|---|
| Paging | 10 per bucket, `<x-pagination>` per bucket | **none** — capped at 20, silently |
| Bucket badge | real total | `count($items)` — **reported the cap, not the total** |
| Product matching | trade name + ingredient + company + disease | trade name only |
| Empty buckets | hidden | all rendered, reading "no matching items" |

**1. The silent truncation is the one that mattered.** Searching a common fragment matched more than
twenty diseases and the page listed twenty, with a badge reading `20` and no way to reach the rest.
The badge was not merely capped — it was *counting the page it had just sliced*, so it was wrong in
exactly the case where a reader would use it to decide whether anything was missing.

Each bucket now paginates on its own query-string key, `{bucket}_page` (`diseases_page`,
`products_page`), continuing the convention the drugs page already used (`disease_page`,
`product_page`) generalised to the bucket name. One key per bucket rather than a single `page`,
because seven cards on one page must be able to sit on seven different pages without one card's
paging resetting another's.

Paging is done in memory rather than by the database's paginator, because ranking happens in PHP:
the rows arrive already ordered by relevance, so a page cannot be expressed as `LIMIT/OFFSET` over an
unsorted table. The consequence is recorded rather than hidden — a bucket collects at most
`search.limits.pool` (500) rows, so a term matching more than that *is* still truncated. It is set
well above a page so a normal search never reaches it, and because the badge is read from the same
pool, the badge and the page count can never disagree.

**2. The product bucket was narrower than the drugs one.** Typing a manufacturer or an active
ingredient returned products on `/drugs/search` and nothing on `/poultry`. That reads as a broken
search rather than as a narrower one. Buckets may now declare `relations`, folded into the LIKE pass
via `orWhereHas`; the products bucket declares its three. Scoping is unchanged — a product is still
admitted only by the domain of a disease it treats, so widening what is *searched* did not widen what
is *visible*. A test asserts a product treating only a large-animal disease stays invisible to a
poultry query even when its manufacturer name matches.

**3. Empty buckets are hidden**, as they are on the drugs page. `search.no_section_results` and its
`@forelse` branch went with them, from both locales.

**4. The near-miss pass was hydrating far too much.** It fires for every bucket whose LIKE pass came
up short of a full page, so on a narrow query *all seven* buckets scanned at once at 2,000 rows each —
up to 14,000 models on a single request, for a page that shows at most 20 rows. `scan_limit` is now
500, the pass is skipped entirely once the LIKE pass has a full page to show, and it selects only the
columns the ranking and presentation steps read rather than whole rows (a `diseases` row carries a
`knowledge_payload` this code never looks at). The column list is derived from each bucket's own
config, so a bucket cannot present a row whose column the select left out.

**Not changed, deliberately.** The page uses `<x-large-animals.page-hero>`, which reads like a
misnomer on `/poultry` but is what every shared part view uses (`diagnosis/*`, `filter/*`). Swapping
it for the generic `<x-page-hero>` would have made this one page inconsistent with its siblings, and
the generic component hardcodes *sky* tokens for dark mode rather than the part's brand — correct
for drugs, wrong for the two green/amber parts.

**Phase 6 stays 7/12.** None of the four deliberately-unticked boxes (§ deviation 5) were ticked: the
live endpoint, the grouped dropdown, debouncing and the rate limit all remain reversed and out of
scope. The unticked *type filter chips* also remain unticked — results are visually grouped by bucket,
but there is still no control to narrow to one type.

### What this fixed beyond the new page

**Search results now hand off to the tools instead of dead-ending.** Clinical-sign and host-species
results linked to `{part}.diagnosis` and `{part}.filter` with no parameters, and neither controller read
the query string — so clicking a sign opened an empty diagnosis form. `ToolPrefillService` now resolves
both parameters and validates them against the part:

| Result clicked | Lands on | Carries |
|---|---|---|
| Clinical sign | `{part}.signs/{canonical_name}` | its own page ✅ **changed at v16** |
| Host species | `{part}.filter` | `?host_species_id={id}` — species preselected |

> ⚠️ **The clinical sign row has been repointed twice.** It originally went to `{part}.diagnosis`,
> which looked reasonable and was a dead end. `InitialDiagnosisRequest` requires a species **and**
> `min:3` signs, so `/poultry/diagnosis?sign=93` opened a form that could not be submitted with what
> it had been given — one sign filled, no species, two more still required. The link promised a
> hand-off and delivered a form the user had to complete from scratch anyway.
>
> At v15 it went to `{part}.filter`, which worked but answered the question one level later than it
> needed to. **At v16 it has its own page**, `GET {part}/signs/{canonical_name}` — the sign's
> details plus every disease carrying it, each with its pivot weight and its pathognomonic /
> required / specific flags. A sign is a real entry in the knowledge base — the filter tool reads
> the same rows — so it gets a real page.
>
> The filter and diagnosis hand-offs did not disappear: they are now buttons at the bottom of the
> sign page, which is a better place for them than a search result. The filter prefill stays for
> exactly that reason.

### v16 — The clinical sign page

| | |
|---|---|
| Route | `GET /{part}/signs/{sign}` → `{part}.signs.show` |
| Views | `resources/views/signs/show.blade.php` — shared, keyed on `$part` |
| Controllers | `Poultry\SignController` · `LargeAnimals\SignController` — thin siblings |
| Shared logic | `ResolvesPartClinicalSign` trait (part scoping + weight ordering) |

**Addressed by `canonical_name`, not by slug or id.** `clinical_signs` has no slug column, and the
canonical name is the stable normalised key — it is what the seeder merges duplicates on. Adding a
slug column to a 1,089-row shared table to serve a URL would have been a schema change for a
cosmetic reason.

**Part scoping is a `whereHas`, not a column.** `clinical_signs` has no domain column, so a sign
belongs to a part when at least one disease in it carries the sign (§9.22). The consequence is that
a sign carried by both domains is legitimately on both pages — and each page lists only its own
domain's diseases. A poultry-only sign on `/large-animals/signs/…` is a **404**, the same decision
the disease pages make and for the same reason.

**Diseases are ordered by pivot weight, descending** — the weight the diagnosis engine scores on, so
the page and the tool agree about which disease is the strongest match. Ties fall back to the name.

**Two bugs worth recording, because both failed as something else entirely:**

1. A closure passed to `with(['diseases' => fn (Builder $d) => …])` receives the **relation**, not
   the query builder. Type-hinting `Builder` produced a `TypeError` that read like a broken
   relationship. The diseases are now loaded by a separate method that scopes them, which is clearer
   anyway.
2. `route($part.'search', …)` instead of `route($part.'.search', …)` — the dot was PHP's
   concatenation operator rather than part of the string, so it rendered `poultrysearch`. Reading the
   output rather than the source hid it: the source *looked* right. Hex-dumping the line settled it
   in one command, after a stale-compiled-view theory and a `view:clear` both failed to explain it.

**Tests:** `SignPageTest`, 12 — rendering, pivot flags, weight ordering, anatomy/body system, both
hand-offs, the large-animals half, a sign on both pages, cross-part 404, unknown 404, inactive
disease excluded, reachable from search, and no unresolved translation key in either locale.

> The Arabic strings here are agent-written and **unreviewed by a native speaker**, as with the rest
> of this file. `pathognomonic`, `required`, `specific`, `weight` and `na` deliberately reuse the
> existing disease-page wording rather than introducing a second set of terms for the same idea.

A sign is accepted when it is reachable from that part's domain, which is why a sign carried by both
domains works on either tool. Anything else — a deleted id, a `sign=abc`, `sign=1.5`, `sign=-4`, or an id
belonging to the other part — degrades to an ordinary empty tool at 200 rather than a 404, because these
parameters come from URLs and a stale bookmark should not be an error page.

`LargeAnimals\SearchController` ran **one unscoped query per model**. Probing the live site before
this change:

| Query on `/large-animals/search` | Returned | Actual domain |
|---|---|---|
| `Coccidiosis` | `coccidiosis`, `ostrich-coccidiosis`, `rabbit-coccidiosis` | all **poultry** |
| `Newcastle` | `ostrich-newcastle-disease` | **poultry** |
| `dyspnea` | sign 23 | large_animal |
| `dyspnea` | sign **41** | **poultry** |

`dyspnea` is the clearest case: it exists as two separate `clinical_signs` rows, one per part, and the
large-animal page rendered both. That page also ignored `diseases.is_active`, so deactivated diseases
stayed findable, and `main-header.blade.php` matched the search route with a two-way `match`, sending the
poultry search button to the **drugs** page. All three are fixed, and
`tests/Feature/Knowledge/SearchScopingTest.php` exists to keep them fixed.

### Scoping reference

Four models, four vocabularies — no single scope covers them.

| Bucket | How part membership is expressed |
|---|---|
| Diseases | `->forDomain($domain)` on its own `domain` column |
| Clinical Signs | `->whereHas('diseases', fn ($d) => $d->forDomain($domain))` — no domain of its own |
| Host Species | `->forPart($part)` via `taxonomy_group`; throws on an unknown part, and `config/vetpedia.parts` has no `drugs` key |
| Products | `->whereHas('diseases', …)` through `disease_product`, plus the `approved` global scope |

Bucket membership is per part in `config/search.php`: large-animals gets seven buckets including blog,
microorganisms and medical articles; poultry gets four, because it has no content of its own in the
other three.

### Arabic search is only as good as the Arabic names

Every bucket searches its `*_ar` column, and on the Arabic locale the Arabic name becomes the heading.
Verified live: searching `الكوكسيديا` finds Coccidiosis and renders it in Arabic under `dir="rtl"`.

But **only 2 of the 151 poultry diseases carry an Arabic name** — `coccidiosis` and `enteritis`. The
other 149 have `name_ar` empty, so for most of the catalogue an Arabic query returns nothing no matter
how the search is built. This is a Phase 1 data gap, not a Phase 6 defect, and it is now the single
biggest thing limiting the feature. Until the Arabic names exist, the honest statement is that Arabic
search *works* but covers 1% of the diseases.

---

## Phase 7 — Disease Comparison

**Route:** `GET /poultry/compare`
**Milestone:** A user can select up to four poultry diseases and see them side by side.

**What this page is used for:** When a disease looks similar to another, the practical question is
*"how do I tell these apart?"*. This lines the candidates up so the differences are obvious at a
glance — shared symptoms, unique symptoms, affected organs, age. It saves opening several pages and
comparing from memory.

> **Built at v18 on real `/poultry/compare` and `/large-animals/compare` routes.** Three of the tasks
> below were rewritten against the data and one was dropped; the reasons are recorded rather than
> left implicit in the diff.

### Tasks

- [x] Adapt the existing `⚡disease-compare-builder` for poultry rather than writing a new one — **one shared SFC, not a copy.** Moved from `app/components/large-animals/` to `app/components/knowledge/`, so it resolves as `knowledge.disease-compare-builder` and is no longer named after one part. §9.19 applies: two copies of a 500-line matrix builder is how they drift.
- [x] Restrict the disease picker to poultry diseases — **and the loader, and the setter.** See below.
- [x] Support 2 to 4 diseases — a fifth is **refused, not truncated**: silently dropping it would leave the reader comparing something other than what they asked for.
- [x] Build the side-by-side table
- [x] Show symptoms shared by all selected — these cannot tell them apart — **as a group, not as a flag.** See below.
- [x] Show unique symptoms per disease — these are the differentiators — **listed first**, because a finding only one disease has is the entire reason to open the page.
- [x] Compare affected organs (`important_organs_affected`) and age groups — **split, and both rewritten.** Age groups have no table (§1.4); organs read the payload and the tab stays hidden until the import lands. See below.
- [x] Sync selections to the URL via the URL attribute so a comparison can be shared — **`?diseases[]=<slug>`.** Four `#[Url(as: 'disease1')]` integer params became one slug array. Old `?disease1=<ulid>` bookmarks are ignored rather than redirected; a stale bookmark degrades to an empty comparison at 200, the same treatment the v15 prefill parameters get.
- [x] Add remove and clear-all controls — **removal closes the gap.** The table's columns come from the selection in order, so an empty slot in the middle would render as an empty column and read as "these diseases share nothing".
- [x] Add per-column search within the builder
- [x] Horizontal scroll on mobile — `overflow-x-auto` on the table, as before.
- [x] Show a note explaining why shared signs do not help distinguish — the legend line plus a labelled group header on every `shared` group.
- [x] Tests: 2-way, 4-way, over-limit attempts, clear — **`DiseaseComparisonTest`, 27.** Rewritten in place rather than added alongside, since the file already tested this page against an API that no longer exists.

### The comparison was reading the wrong source

Every matrix read `diseases.knowledge_payload`. On the live database:

| | |
|---|---|
| Poultry diseases | 151 |
| …carrying a `knowledge_payload` | **2** |
| …carrying `important_organs_affected` | **0** |

A payload-only matrix renders **empty for 149 of 151 diseases**. The relational pivot is populated:

| Relational source | Diseases covered |
|---|---|
| `disease_clinical_sign` → findings typed `sign` | 159 |
| → findings typed `lesion` | 148 |
| `disease_host_species` | 384 rows |
| `disease_product` | 20 |

Signs and lesions are the **same relation** — `clinical_signs` is a layer over `findings`, and the dump imported gross lesions into the same table with `findings.ontology_type = 'lesion'` (§4.1). They are split by reading that column. `Disease::signItems()` and `Disease::lesionItems()` are the two places the question is asked, and the payload is merged on top for fields that have no table at all.

### Four things found while building it

**1. The picker was unscoped — the same defect class as the search page.** `searchQuery()` ran
`Disease::where('name','like',…)` with no domain filter, so `/large-animals/compare` offered
**151 poultry diseases**. Scoping is now applied in three places, not one: the picker query, the
loader, and — the one that was nearly missed — `addDisease()`. Filtering only at the loader is not
enough, because a foreign slug would still occupy one of the four slots, and four crafted calls
would fill the comparison with nothing at all.

**2. `findings.ontology_type` is nullable, and matching `'sign'` dropped Pyrexia.** The curated
signs — **Pyrexia**, **Inappetence**, **Hemorrhagic diarrhea** — carry `NULL` on both
`ontology_type` and `category`. They are the most common signs in the clinical vocabulary and they
were falling out of *both* matrices, silently, with no error. The rule is now to **exclude** lesions
rather than match a clinical value: a post-mortem finding is explicitly typed `lesion`, anything not
typed as one is a clinical observation. Pinned by a test, because it is exactly the kind of
assumption that breaks without failing.

**3. "Differs" was not the question the plan asked.** The old matrix had one flag — *not all equal*.
The plan asks for *shared by all* and *unique to one*, which are different questions and neither is
answered by that flag. Every row is now classified into `unique` / `partial` / `shared`, with a
labelled group header, ordered `unique → partial → shared`. Group headers carry `has_diff` so a
group that is identical across every column disappears with the "differences only" toggle instead of
leaving a label over nothing.

> **`partial` is unreachable with two diseases** — a finding is then either in both or in one. It
> needs three. Worth stating because the two-way test cannot cover it, and the practical case that
> matters ("these two look alike, what tells them apart?") is a three-way question.

**4. The organs substitute did not work, and was removed.** The plan asks for
`important_organs_affected`, which is populated for zero poultry diseases today. Deriving organs from
`clinicalSigns.anatomical_structure` looked like the obvious replacement and **was built** — but
measured on the live data only **27 of 1,109** sign rows carry an anatomy, covering **2 of 151**
diseases. A tab built on 2 of 151 records is worse than no tab. Organs now read the payload like
every other payload field: **152 of the 154 source documents** have the key populated and it simply
has not been imported (§1.6). The tab is hidden today and **needs no code change when the import
runs**. It is a flat string list rather than a list of objects, which is why it reads through
`payloadList()` and not `payloadItems()`.

### What the page compares now

| Axis | Source | Status |
|---|---|---|
| Overview — etiology, host species, susceptibility, microorganisms, zoonotic, notifiable, OIE | `disease_classifications`, `disease_host_species`, `disease_microorganism` | ✅ works |
| Treating drugs | `disease_product` | ✅ new |
| Clinical signs | relational pivot + payload | ✅ works |
| Gross lesions | relational pivot + payload | ✅ works |
| Organs | `knowledge_payload.important_organs_affected` | ⏳ hidden until the bulk import |
| Diagnosis · Treatment · Prevention · References | payload only | ⏳ hidden until the bulk import |

**Dropped:** age groups (no table — §1.4) and differentials (`differential_syndrome_disease` is
**0 rows**, so a differential tab could never appear). Host **susceptibility** replaces age: it is
read off `disease_host_species.susceptibility` and reported at the highest level recorded, because
one host being highly susceptible is the finding that matters, not an average across a dozen.
It is a three-value vocabulary — `moderate` / `high` / `susceptible` — displayed headline-cased
rather than translated, matching how `etiology_type` is already rendered raw in the column card.

> ⚠️ **Susceptibility is untranslated on the Arabic page.** The three values come from the database,
> not from a lang file. Translating them needs a native speaker, as does the rest of this file's
> Arabic.

**A tab with no rows is not rendered at all** rather than shown empty. Four of the seven axes are
payload-only and the payload is 2-of-151 imported, so an empty panel would be the page's dominant
impression. An absent tab is honest; an empty one looks broken.

**Not changed:** the page still uses `<x-large-animals.page-hero>`, which reads like a misnomer on
`/poultry` but is what every shared part view uses (`diagnosis/*`, `filter/*`) — same reasoning as
v14.

---

## Phase 8 — Body Map

**Route:** `GET /poultry/body-map`
**Milestone:** A chicken diagram is clickable, and clicking any organ lists the diseases and lesions
affecting that part.

**What this page is used for:** A visual way into the same data. Instead of reading a list of body
systems, the user clicks directly on the part they are worried about — the heart, the intestines, the
air sacs — and immediately sees what goes wrong there. It is the most intuitive entry point for
someone who does not know the anatomical name of what they are looking at.

> ⭐ **`important_organs_affected` is populated for 152 diseases** — the organ-to-disease mapping
> already exists in the data. Only the illustration is missing.

### Tasks

- [ ] Commission or source an anatomically accurate chicken illustration with defined clickable regions — **this is the blocker**
- [ ] Confirm the licensing permits commercial use
- [ ] Extract the organ list from `important_organs_affected` across all 152 diseases and build a frequency table
- [ ] Map the 43 anatomical structures onto the illustration's regions
- [ ] Link each region → body system → anatomical structures
- [ ] Map the 928 JSON lesions onto the organs they occur in (via `anatomical_location`)
- [ ] Build a Livewire component for the interactive diagram
- [ ] Add hover states and a clear selected state
- [ ] Show the clicked organ's name and related diseases in a side panel
- [ ] Show the lesions associated with that organ
- [ ] Keyboard accessible, not mouse only
- [ ] Works on touch devices, including pinch zoom
- [ ] Add a fallback list view for users who cannot use the diagram
- [ ] Seed the 11 body systems so every region has content
- [ ] Tests: region selection, keyboard navigation, fallback view

---

## Phase 9 — Lesion Atlas

**Route:** `GET /poultry/atlas`
**Milestone:** The merged lesion set is browsable and searchable by body system, and the atlas
degrades gracefully while images are still missing.

**What this page is used for:** A visual reference of what a disease looks like from the inside, found
during post-mortem. This is the page a vet consults after opening a bird and wanting to identify what
they are seeing.

> ⚠️ **Honest caveat:** we have lesion *names and descriptions* (660 from SQL + 928 from JSON) but
> **no photographs** — `media_assets` is empty. On launch this is a searchable text reference; images
> get attached later.

### Tasks

- [ ] Build the atlas controller and view
- [ ] List the merged lesion set (`ontology_type = 'lesion'`)
- [ ] Group by body system, using the 11 body systems
- [ ] Add a filter by body system
- [ ] Add text search across lesion names and descriptions
- [ ] Add letter navigation
- [ ] Show, per lesion, which diseases cause it — the 856 SQL links plus the JSON associations
- [ ] Show the JSON `notes` field alongside each lesion
- [ ] Make the layout image-ready so photographs can be added without redesign
- [ ] Show a clear "image coming soon" placeholder rather than a broken box
- [ ] Add a documented upload path for images via Filament
- [ ] Link each lesion to its disease detail page
- [ ] Add English and Arabic names for lesions
- [ ] Tests: grouping, filtering, search, empty-image placeholder

---

## Phase 10 — Part Drug Catalogue

**Route:** `GET /poultry/products` and `GET /large-animals/products`
**Milestone:** A visitor can browse the drugs applicable to their part, see active ingredients,
companies and withdrawal periods, with links back to the disease pages they treat.

**What this page is used for:** The drug catalogue, restricted to what is relevant to the part. This
completes the client's "three parts" structure — disease knowledge and treatment knowledge in one
place, so a vet reading about a disease can go straight to what treats it.

> **No new product data** — this reuses the existing catalogue and shows the part-relevant slice.

### 10.1 There is no species column on products

`products` is **one shared table for the whole site** — 30 drugs — with no species column. Nothing
records "this one is for poultry". The only traces of part-applicability are indirect, and they do
not agree:

| Signal | Poultry products (of 30) |
|---|---|
| `dosages` → `species.domain = poultry` | 15 |
| `disease_product` → `diseases.domain = poultry` | 12 |
| `withdrawal_periods` → `species.domain = poultry` | 6 |
| union of all three | 24 |

| Product | Poultry dose | Poultry disease | Poultry withdrawal |
|---|---|---|---|
| Ivomec | — | ✓ Coccidiosis | — |
| Droncit | ✓ | — | — |
| Ripercol | — | — | ✓ |
| Ketofen | — | — | — |

### 10.2 Locked decisions

- **Inclusion rule: a poultry dosing instruction.** `products → dosages → species.domain = <part>`.
  The widest of the trustworthy signals and the only one actually *about the bird*. Excludes the six
  products with no poultry-specific record at all (Doxypen, Fasinex, Panacur, Sulfivet, Terramycin,
  Ketofen) for a stated reason rather than silently. Yields 15 poultry / ~15 large-animal products.
- **One shared component, both parts** — same shape as Phase 7's compare builder (§9.19).
- **Cards link out to `/drugs/products/{slug}`** — one URL per product, no duplicated detail page,
  and the Drugs part is left untouched. Scoped relations are loaded for the card, not the detail view.
- **Withdrawal periods shown per species, with an explicit not-recorded state.** Never blank, never
  zero: *"No withdrawal period recorded — confirm with the manufacturer."* Silence on a food-animal
  page reads as "no restriction", which is the one reading that can hurt someone.

### 10.3 Known data defects (recorded, not hidden)

- **The product catalogue is placeholder data.** 30 products with lorem ipsum descriptions. Cards do
  not render description text.
- **Withdrawal figures are lorem ipsum and self-contradictory.** Excenel has two Quail rows with egg
  days 3 and 5; Ripercol has three Broiler rows reading 10/7/0, 28/0/1 and 3/14/0. Both rows render,
  species-labelled. This reads as a data bug rather than a page bug — it is one.
- **`milk_days` is populated on poultry rows.** Meaningless for a bird. The poultry card renders meat
  and egg days; milk is not shown.
- **`species` row "Poultry" (slug `poultry`) is tagged `domain = large_animal`.** No dosage row
  references it, so the count is unaffected, but it will misclassify a future one.
- **The disease detail page already links to products** (`large-animals.diseases.show`, block at line
  201) — Phase 10 does not build it. It queries `diseases` lazily; the eager load is added here.

### Tasks

- [ ] `Product::scopeApplicableTo(string $domain)` — `whereHas('dosages.species')`, no migration
- [ ] `Product::scopeWithPartRelations()` — eager-loads the part's doses, withdrawal periods and diseases
- [ ] Shared SFC `resources/views/app/components/products/⚡part-catalogue.blade.php`
- [ ] Search across trade name, active ingredient, manufacturer and treated disease
- [ ] Filters: dosage form, active ingredient, manufacturer
- [ ] Card: trade name, active ingredients + strength, manufacturer, dosage form, part dose rows, withdrawal block, treated diseases
- [ ] Withdrawal block: species-labelled rows; meat + egg days on poultry; explicit not-recorded state
- [ ] Thin controllers `Poultry\ProductController` / `LargeAnimals\ProductController`, mirroring `CompareController`
- [ ] Routes inside the localization group — `poultry.products.index`, `large-animals.products.index`
- [ ] Navbar link + landing hub card on both parts
- [ ] Eager-load `diseases` on both `DiseaseController`s — the existing related-products block is an N+1
- [ ] Confirm `/drugs/products` is unaffected — same 30 products, same filters, same URLs
- [ ] `PartProductCatalogueTest` — inclusion rule, part isolation, withdrawal present vs not-recorded, each filter, empty state, disease→product link, navbar link

---

## 12. Implementation Notes

- **Copy, don't invent.** Reuse `LargeAnimals/` resources, services and controllers, then scope. Every phase is a copy-then-filter job.
- **`domain` is enough.** Filtering through `Disease.domain` answers most needs. Only add `domain` elsewhere if a shared item must behave differently per part.
- **Surgical filters, not global scopes.** Use an explicit `where('domain', 'poultry')` on every poultry-scoped query. The Drugs part must still see every product.
- **Underscores vs hyphens.** `domain` values use `large_animal` / `poultry`; theme parts use `drugs` / `large-animals` / `poultry`.
- **Share components, do not duplicate.** Reuse `products.*` and `large-animals.*` rather than copying into `poultry/`.
- **Localization is not optional.** Every route must stay inside the localization group in `routes/poultry.php` or it will not render in Arabic.
- **No v3 Tailwind classes.** See §8.2.
- **ULIDs everywhere.** All domain models use `HasUlids`.
- **Run Pint** on every changed PHP file before finishing each phase.
- **Dev server is on `127.0.0.1:8000`**, not port 80 — something else is bound to 80 locally.

### 12.1 Notes added at v11

- **`$part` is load-bearing — do not refactor it casually.** In the shared views `$part` is
  simultaneously the **route-name prefix** and the **translation namespace**, because both
  vocabularies happen to be spelled `large-animals` / `poultry`. §3.2 warns never to mix the
  hyphenated and underscored forms; here they coincide *on purpose*, so one typo changes a URL and
  its copy at the same time. Controllers pass `part` explicitly rather than reading the middleware's
  session value, so a shared link cannot inherit the wrong part.
- **Factories must be domain-deterministic.** `HostSpeciesFactory` used to randomise
  `taxonomy_group`, which meant a large-animal test had roughly a **1-in-6 chance** of generating a
  bird and failing for no visible reason. It now has explicit `->poultry()` / `->largeAnimals()`
  states. `DiseaseFactory` gained `->poultry()` / `->largeAnimals()` / `->named()` — the last
  because `diseases.name` and `diseases.slug` are both UNIQUE, so a test naming one disease
  explicitly can still collide with another that rolled the same name. **If a test names one disease,
  it must name every disease it creates:** every name in the factory's pool is a real veterinary
  disease, so it is exactly the name a test reaches for, and the resulting failure is an intermittent
  UNIQUE violation about one run in thirty rather than a clear error.
- **Scoped validation matters as much as scoped queries.** The suggestions endpoints are scoped, but
  validation runs separately and a hand-crafted POST can carry any id. `ScopesToSitePart` re-checks
  both the species and every sign against the part, so a large-animal sign POSTed to
  `/poultry/diagnosis` is rejected rather than silently scoring zero matches.
- **`localeViewPath` is skipped on POST** (`httpMethodsIgnored` includes POST/PUT/PATCH/DELETE).
  The refinement and results views render from a POST, so they never get the locale path registered.
  Harmless under FPM — the view finder is rebuilt each request — but latent under **Octane**, where
  it persists.
- **One pre-existing UX bug found and fixed here:** `showResults` flashed a `warning` on an invalid
  share link and redirected to the diagnosis index, but only the **filter** index rendered
  `session('warning')`. The diagnosis page silently swallowed it.

---

## 13. Changelog

| Version | Change |
|---|---|
| **v19** | **Phase 10 built: `/poultry/products` and `/large-animals/products`.** The plan said *"decide how a product is marked as poultry-applicable"* and treated it as a formality. It was the only real decision in the phase: **`products` is one shared table with no species column**, and the three available traces disagree — 15 products have a poultry dose, 12 treat a poultry disease, 6 carry a poultry withdrawal figure, 24 match at least one. Dosing won because it is the only one of the three that is a statement *about the animal*, which leaves 15 rather than 24 and excludes six products (Ketofen, Panacur, Fasinex, Doxypen, Sulfivet, Terramycin) for a reason that can be stated. The page shows 15 of 30 and that is the filter working, not a broken import. No migration — `Product::applicableTo()` and `withPartRelations()` are scopes, and the second is constrained rather than filtered afterwards so a poultry card never lists a cattle dose. **Withdrawal is never omitted and never blank**: 6 of 30 products have a poultry figure, and on a food-animal page an empty box reads as "no restriction", so the gap is stated in words instead. Cards link out to `/drugs/products/{slug}` — one URL per product beats a detail page scoped to 15 rows. Also: `Disease::getLocalizedNameAttribute()` moved to a shared `HasLocalizedName` trait (it was the only model with one; four others lacked it) and now covers `Species`, `Company`, `ActiveIngredient` and `DosageForm`; `milk_days` is hidden on the poultry page because it is meaningless for a bird and the source data populates it anyway; an N+1 fixed on both disease controllers, where the shared view's related-products block queried `diseases` lazily on every page. **Two implementation bugs the tests caught, both silent:** `wherePivot('role', …)` inside `whereHas` emits an unqualified `"pivot" = ?` rather than `product_company"."role"` on a relation declared `withPivot('role')`, so the manufacturer filter matched nothing and the page returned zero rows with no error anywhere; and an eager-load constraint is handed a **`Relation`** while a `whereHas` nested inside it is handed a **`Builder`**, so both are typed on the union. **236 passed, 8 skipped.** |
| **v18** | **Phase 7 built: `/poultry/compare`, and the same leak fixed on `/large-animals/compare`.** One shared SFC moved to `app/components/knowledge/`, replacing a 491-line large-animals-only component whose picker ran `Disease::where('name','like',…)` with no domain filter and offered **151 poultry diseases** on the large-animals page — the same defect class as the unscoped search page. Scoping now applies in three places, not one: the picker, the loader, and `addDisease()`, because a foreign slug filtered only at the loader would still occupy one of the four slots. **The component read `knowledge_payload`, which 2 of 151 poultry diseases carry** — every matrix would have rendered empty. Signs and lesions now come from the relational pivot (159 and 148 diseases), split by `findings.ontology_type`; the payload merges on top. Matching `'sign'` exactly dropped **Pyrexia**, **Inappetence** and **Hemorrhagic diarrhea**, whose `ontology_type` is `NULL` — the rule is now to exclude lesions rather than match a clinical value. The plan's *"shared by all"* and *"unique to one"* are real questions and neither is answered by the old single `differs` flag, so rows are grouped `unique → partial → shared` with labelled headers; **`partial` is unreachable with two diseases** and needed its own three-way test. Selection is `?diseases[]=<slug>` instead of four integer params. **The organs substitute was built and then removed** — deriving from `anatomical_structure` looked right but only 27 of 1,109 sign rows carry an anatomy, covering 2 of 151 diseases; organs now read `important_organs_affected`, which is populated in **152 of 154 source documents** and simply not imported yet, so the tab is hidden and needs no code change when it lands. Age groups dropped (no table), differentials dropped (0 rows), susceptibility added in their place. **`DiseaseComparisonTest` rewritten in place, 27 tests. 201 passed, 8 skipped.** | **A canonical sign name is not unique, and the page was resolving it to one record.** `ClinicalSign` is a finding plus an anatomy plus a modifier, and five names are recorded twice on the poultry side — anemia, dehydration, depression, diarrhea, weight loss. Consequences, all verified against the live data: `/poultry/search` listed **Depression twice pointing at one URL**, and the page behind it resolved with `first()`, which is ordered by id and so always chose the *smaller* record — depression showed **11 of ~49** diseases, anemia **1 of 19**. A name now resolves to a *set* of records, the page presents the concept and unions their diseases one row per disease (heaviest weight wins, flags OR-ed), and the search collapses with a config-driven `dedupe_by` applied after scoring and before the limit so the cap and badge count concepts. **The large-animals half was already correct — zero duplicates there — and is now covered by a test, which was the actual gap behind the v16 request.** URLs keep `%20`; recorded as cosmetic. **180 passed, 8 skipped.** |
| **v16** | **A clinical sign now has its own page.** The search row has been repointed twice — diagnosis (a dead end: that form needs a species and `min:3` signs and could not be submitted with one sign), then filter (correct but one level later than needed), and now `GET {part}/signs/{canonical_name}`. The page shows the sign's anatomy, body system, stage, modifier and finding, then every disease carrying it with its pivot weight and its pathognomonic / required / specific flags, ordered by weight so the page agrees with the diagnosis engine about the strongest match. Part scoping is a `whereHas` through `diseases` rather than a column, so a cross-domain sign appears on both pages with only its own diseases, and a single-domain sign on the other part's URL is a 404. Routed by `canonical_name` because `clinical_signs` has no slug column. The filter and diagnosis hand-offs moved to the bottom of the page, which is why the v15 filter prefill was kept. **176 passed, 8 skipped.** |
| **v15** | **A clinical sign in search now opens the filter tool, not the diagnosis tool.** Pointing it at `?sign={id}` on the diagnosis page was a dead end dressed as a hand-off: `InitialDiagnosisRequest` requires a species and `min:3` signs, so the form arrived holding one sign and could not be submitted at all. The filter tool is the sign's real home — it asks "what could cause this sign?", and its minimum is one, so results are there on arrival. `FilterController::index()` gained an `initialSignTokens` prefill in both parts, seeded into the Alpine picker's `tokens` rather than rendered beside it, so a prefilled sign is an ordinary token: removable, and posted with the rest. The diagnosis prefill still works and still accepts `?sign=`; it is just no longer what a search result points at. **164 passed, 8 skipped.** |
| **v14** | **Phase 6 parity with `/drugs/search`; deviation 5 corrected.** v13 recorded that the page "matched drugs exactly". It matched on the *form* and not on the *results*, and the difference was invisible in a diff because the drugs page was never opened beside it. Four gaps closed: buckets paginate on their own `{bucket}_page` key at 10 per page instead of silently truncating at 20; the badge counts the bucket instead of the page, so it stopped reporting the cap as if it were the total; the products bucket matches on manufacturer, active ingredient and disease name as the drugs page does, via a new `relations` key folded into the LIKE pass with part-scoping unchanged; empty buckets are hidden rather than rendered as "no matching items" cards. The near-miss pass — which fired for every short bucket, so up to 14,000 models hydrated per request — now runs at 500 rows, only while there is no full page to show, and selects just the columns it reads. **160 passed, 8 skipped.** |
| **v13** | **Phase 6 corrected: submit-only search, a working near-miss gate, and working deep links.** The live suggestions stack built in v12 was removed - the page is now a plain form identical to `/drugs/search`, and the endpoint, rate limiter, config limits and both translation keys went with it (Phase 6 11/12 → 7/12; the four unticked boxes left open rather than deleted). Separately, the 0.30 fuzzy floor chosen in v12 was corrected: it matched `chicken` against thirteen "Thickened" signs at Dice 0.667, and raising the floor to 0.50 would **not** have caught them. Near-misses must now be anchored at the front of the candidate - same first trigram, or same leading two characters for transpositions - which removes the false positives without discarding `cocido` → `coccidiosis` at 0.308. Clinical-sign and host-species results now deep-link into the diagnosis and filter tools via `ToolPrefillService`, with per-part validation and silent fallback to 200 for unusable ids. The generic `page-hero` got the same overflow fix as the large-animal one. **151 passed, 8 skipped.** |
| **v12** | **Phase 6 built: `/poultry/search`, and the same leak fixed on `/large-animals/search`.** Shared controller/service/view keyed on `$part`; bucket membership per part in `config/search.php`. Live suggestions endpoint with grouped icons, debounced, rate-limited 60/min. Arabic searchable in both directions and rendered locale-aware, but **only 2 of 151 poultry diseases carry an Arabic name**, which caps the feature until Phase 1 fills them. Near-miss matching by trigram Dice after a LIKE first pass — **not** Levenshtein, which was measured against the data and cannot catch the typo it was chosen for. Two deviations recorded: `ClinicalSign` searched instead of `Finding` (same vocabulary, and `findings` cannot be domain-scoped), and products pulled forward from Phase 9 because nine of thirty already treat a poultry disease. Fixed the two-way header match that sent the poultry search button to the drugs page. **128 passed, 8 skipped.** |
| v11 | **Phases 4 and 5 built on real `/poultry/*` routes; §2.1 cross-part defect CLOSED.** Engines parameterised by domain rather than subclassed, views shared rather than copied, lang files layered rather than duplicated. Share tokens versioned and domain-tagged, which also surfaced a latent `+`-in-query-string bug. Phase 3's detail route pulled forward so result links do not dead-end into Large Animals. Large-animal tools now domain-scoped (169 → 18 diseases). **106 passed, 8 skipped.** |
| v10 | Phase 2 hub built as option B: live scoped stats, EN/AR lang files, 2 cards. Recorded the cross-part navigation defect it creates, and two pre-existing bugs it exposed (unscoped sign suggestions, domain-less share links). |
| v9 | Surfaced `domain` in the shared Disease/Species admin resources — it was invisible *and* unfixable, which made the §1.7 editor-isolation criterion fail rather than merely unticked. Added `HasDomain` trait and 6 tests. §1.8 records it. |
| v8 | Phase 1 built and ticked. Recorded real counts, which corrected three plan figures. Documented the `ClinicalSignSeeder` repair that unblocked `db:seed`. |
| v7 | Dropped Artisan commands and new services. Source A now loads via the 36 existing seeders; Source B via multi-file Filament upload with slug auto-matching. Merge rule **closed as not needed** (different destinations). |
| v6 | Filament import page decision (option A): relocate `ImportDiseaseKnowledge` to the shared `DiseasesCluster` with a `domain` filter. Added §3.4 and Phase 1.7 tasks. |
| v5 | Discovered 154 disease JSON files (928 lesions, 13 knowledge fields). Chose Artisan command over seeder, `diseases.knowledge_payload` as the single target, all 13 transformer keys, `domain` values confirmed. Merge rule flagged as still-open. |
| v4 | Added phased execution plan: Phase 1 migrations/data, Phases 2–10 one per page, each with milestone, tasks and plain-English purpose. |
| v3 | All 5 decisions locked; URL naming fixed to match `large-animals`; file/route conventions, Tailwind v4 token rules, implementation notes. |
| v2 | Corrected: Large Animals already built; 660 lesions exist; Clinical Cases removed; pivot/engine match discovered. |
| v1 | First draft — contained incorrect claims in §0 and §3.1. |