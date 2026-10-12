# Poultry Section Plan — Vetpedia

> **Status:** v5 — two-source import settled, transformer scope decided
> **Date:** 2026-10-13
> **Supersedes:** v1–v4

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
| `app/Models/Poultry/` | **empty** |
| `app/Models/LargeAnimals/` | **empty** |
| `app/Http/Controllers/Poultry/` | `PoultryLandingController` only |

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

- [ ] Move `ImportDiseaseKnowledge` out of `Pages/LargeAnimals/` to the shared `DiseasesCluster`
- [ ] Update the view namespace from `filament.pages.large-animals.*` to the shared location
- [ ] Add a `domain` select to the form, and scope the disease picker options by it
- [ ] Default the form to `poultry` when reached from the Poultry cluster and `large_animal` from Large Animals
- [ ] Keep the existing file-upload and pasted-text paths exactly as they are
- [ ] Leave the bulk 154-file load to the Artisan command (§5) — the page is for **single-disease corrections**

> **Why one page, not two:** two importers will drift apart. One page scoped by a `domain` selector
> serves both parts and cannot disagree with itself.

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

**An Artisan command, not a seeder.**

| | Seeder | Artisan command |
|---|---|---|
| Purpose | fake demo data | real client data |
| Re-runnable | no | **yes** |
| Progress + error report | no | **yes** |
| Transactional | no | **yes** |

The client **will** send corrected data, so the import must be repeatable.

```
php artisan poultry:import-data        → Source A: SQL dump → relational tables
php artisan poultry:import-knowledge   → Source B: 154 JSONs  → diseases.knowledge_payload
```

Both must be idempotent, transactional, and log a per-entity summary.

---

## 6. Merge Rule (Source A ↔ Source B)

Both sources describe the same diseases and the same lesions, in different words.

**Proposed default: JSON wins, SQL fills gaps.**
- Source B entries are richer — they carry `anatomical_location`, `weight`, `specificity` and `notes`.
- Source A fills only what Source B lacks.
- Dedupe on `canonical_name`.
- Log every conflict so it can be reviewed in Filament rather than silently overwritten.

> ⚠️ **This is the one decision not yet formally confirmed.** Flagged here for review before Phase 1 is marked done.

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
| 10 | Knowledge import | **Artisan command, not a seeder** |
| 11 | Knowledge target | **`diseases.knowledge_payload`** — one column, zero schema change |
| 12 | Transformer scope | **All 13 fields**, not just the 5 rendered today |
| 13 | Green theme | **Already working** — no change |
| 14 | `newVitPedia/` | **Keep in repo as-is** (option C) |
| 15 | Filament import page | **Option A — relocate `ImportDiseaseKnowledge` to the shared `DiseasesCluster`, add a `domain` filter.** No second page. |
| — | Merge rule A↔B | ⚠️ proposed "JSON wins, SQL fills gaps" — **needs confirmation** |

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

- [ ] Create migration adding a `domain` column to `diseases`
- [ ] Create migration adding a `domain` column to `species`
- [ ] Define the column as an enum or string with exactly two values: `large_animal`, `poultry`
- [ ] Backfill all existing rows to `large_animal`
- [ ] Add indexes on `diseases.domain` and `species.domain`
- [ ] Verify the migration is reversible
- [ ] Confirm Large Animals pages render exactly as before

### 1.2 Source A mapping decisions

- [ ] Map `findings.type = 'clinical_sign'` → `ontology_type` + `category = 'clinical'`
- [ ] Map `findings.type = 'gross_lesion'` → `ontology_type = 'lesion'` + `category = 'postmortem'`
- [ ] Decide how `findings.body_system` (free-text) maps to `body_system_id`, and what to do with unmatchable strings
- [ ] Map `disease_finding.specificity` → `is_specific`
- [ ] Map `disease_finding.required_for_diagnosis` → `is_required`
- [ ] Decide whether `diagnostic_tests`, `transmission_routes`, `risk_factors`, `disease_age_groups` import now or defer
- [ ] Document every dump column deliberately dropped

### 1.3 Source B mapping decisions

- [ ] Extend `DiseaseKnowledgePayloadTransformer` with aliases: `gross_lesions` → `postmortem_findings`, `prevention_and_control` → `prevention_control`, `diagnostic_tests` → `diagnosis`
- [ ] Add output keys for the 8 currently-unmapped fields: `microscopic_lesions`, `pathognomonic_findings`, `field_diagnosis_clues`, `epidemiological_clues`, `important_organs_affected`, `morbidity`, `mortality`, `differential_diagnoses`
- [ ] Match each JSON file to a disease by `slug`; log every file that matches nothing
- [ ] **Confirm the Source A ↔ Source B merge rule** (see §6)
- [ ] Implement the merge: JSON wins, SQL fills gaps, dedupe on `canonical_name`, log conflicts
- [ ] Write regression tests for the transformer covering all 13 keys

### 1.4 Importer — Source A (`poultry:import-data`)

- [ ] Build as a reusable Artisan command, idempotent, safe to re-run
- [ ] Wrap in a database transaction so a failure leaves no half-imported state
- [ ] Import `species` (9) → `domain = poultry`
- [ ] Import `body_systems` (11) and `anatomical_structures` (43)
- [ ] Import `findings` (1,118) — 458 signs + 660 lesions
- [ ] Import `diseases` (151) → `domain = poultry`
- [ ] Import the `disease_finding` pivot (1,900) with weight, specificity, pathognomonic, required
- [ ] Import `disease_species` (302) and `disease_age_groups` (179)
- [ ] Log a summary: rows imported per table, skipped, failed

### 1.5 Importer — Source B (`poultry:import-knowledge`)

- [ ] Build as a second reusable Artisan command, idempotent
- [ ] Loop all 154 JSON files, match by `slug`
- [ ] Run each through the extended transformer
- [ ] Merge with Source A data per the confirmed rule
- [ ] Write into `diseases.knowledge_payload`
- [ ] Log a summary: files processed, matched, unmatched, merged, conflicts

### 1.6 Verification

- [ ] Assert exactly 151 poultry diseases exist
- [ ] Assert exactly 1,118 findings exist, of which 660 have `ontology_type = 'lesion'`
- [ ] Assert exactly 1,900 disease↔finding links exist, **zero** orphaned
- [ ] Assert 149 of 151 diseases have at least one finding
- [ ] Assert 154 JSON files processed and N matched to a disease — **record the real N here**
- [ ] Assert total lesion count equals 660 (SQL) + merged JSON entries
- [ ] Assert `important_organs_affected` is present for ~152 diseases
- [ ] Spot-check 5 diseases by hand against both sources (Newcastle Disease, Coccidiosis, Avian Influenza, Aflatoxicosis, Marek's Disease)
- [ ] Confirm the Large Animals test suite still passes
- [ ] Write all the counts above as automated tests

### 1.7 Admin access

- [ ] Add the `Poultry` Filament cluster
- [ ] Add a `domain` select to the Diseases and Species admin forms
- [ ] Default new records inside the cluster to `poultry`
- [ ] Confirm an editor can fix a poultry disease without touching a large-animal one
- [ ] Relocate `ImportDiseaseKnowledge` from `Pages/LargeAnimals/` to the shared `DiseasesCluster` (§3.4)
- [ ] Move its Blade view out of `filament/pages/large-animals/` to the shared namespace
- [ ] Add a `domain` select to the import form and scope the disease picker options by it
- [ ] Default the form's `domain` from whichever cluster the editor arrived from
- [ ] Confirm the relocated page is reachable from both the Large Animals and Poultry clusters

---

## Phase 2 — Hub (Home)

**Route:** `GET /poultry` · **Milestone:** `/poultry` loads in both English and Arabic, shows live
counts, and links to every section built so far.

**What this page is used for:** The front door of the Poultry section. Its job is to give a visitor a
clear map of what is inside and let them jump straight to the tool they need. It shows live numbers
(how many diseases, how many symptoms) so the section feels populated, then a grid of cards.

### Tasks

- [ ] Upgrade `PoultryLandingController` (currently returns an empty view)
- [ ] Build the landing view under the Poultry view namespace
- [ ] Add hero section with title and one-line description, Arabic and English
- [ ] Add a stats strip: diseases, findings, lesions, species — all read live, never hardcoded
- [ ] Add a card grid linking to each section
- [ ] Hide cards for sections not yet built — no dead links
- [ ] Use Flowbite v4 semantic tokens only
- [ ] Add Lucide icons to each card
- [ ] Responsive: 1 column mobile, 2 tablet, 3–4 desktop
- [ ] Verify the RTL layout for Arabic
- [ ] Confirm the green part theme and correct logo/colours render

---

## Phase 3 — Diseases

**Routes:** `GET /poultry/diseases` (list) + `GET /poultry/diseases/{slug}` (detail)
**Milestone:** All 151 poultry diseases are browsable, and each detail page shows its full knowledge
payload — not just symptoms.

**What this page is used for:** The reference library. A vet comes here to look up a disease — what it
is, which birds it affects, how old they are when it hits, which symptoms point to it, and what to do
about it. Everything else on the site links into these pages.

### Tasks

- [ ] Add `Poultry\DiseaseController` mirroring `LargeAnimals\DiseaseController`
- [ ] Add the list route, paginated, alphabetised, filtered to `domain = poultry`
- [ ] Add letter/group navigation for browsing 151 entries
- [ ] Build the list view using the existing Large Animals disease card as the base
- [ ] Add the detail route, resolved by slug, scoped to `domain = poultry`
- [ ] Show: name, Arabic name, description, etiology, affected species, bird types, age groups, synonyms
- [ ] Show the symptom list with each symptom's weight, pathognomonic and required flags
- [ ] Visually distinguish pathognomonic and required symptoms
- [ ] Extend `DiseaseArticleBuilder` to render the 8 new knowledge sections: gross lesions, microscopic lesions, field diagnosis clues, epidemiological clues, pathognomonic findings, differential diagnoses, morbidity/mortality, important organs
- [ ] Show the **treatment** section — ⚠️ note only 17 of 154 diseases have treatment data, so render a graceful empty state
- [ ] Link each disease to the diagnosis tool, pre-loaded with its key symptoms
- [ ] Link each disease to the drugs that treat it (Phase 9)
- [ ] Add `404` handling for unknown slugs
- [ ] Add Arabic names/descriptions for at least the top 20 diseases
- [ ] Feature tests: list renders, detail renders, unknown slug 404s, large-animal slug rejected

---

## Phase 4 — Diagnosis Engine

**Routes:** `GET/POST /poultry/diagnosis`
**Milestone:** A user picks a species and ticks symptoms, and gets a ranked disease list that matches
the expected output on all 45 verification cases.

**What this page is used for:** The flagship feature and the main reason the client wants this section.
The user has a sick bird and does not know what is wrong. They pick the bird type, tick the symptoms
they can see, and get a ranked list of the most likely diseases with a confidence level and an
explanation of which symptoms drove the ranking.

### Tasks

- [ ] Adapt `LargeAnimals\DiagnosisService` into `Poultry\DiagnosisService`, scoped to `domain = poultry`
- [ ] Keep the existing scoring approach — weight plus pathognomonic, required and specificity bonuses
- [ ] Map the imported pivot flags onto the scoring correctly (see §1.2)
- [ ] Add the species picker as step one — the 9 poultry species
- [ ] Build the symptom picker grouped by body system, searchable, using the 458 clinical signs
- [ ] Build the results view: ranked diseases with a confidence percentage
- [ ] Show *why* each disease ranked where it did — which symptoms matched and how strongly
- [ ] Show "key symptoms you did not select" — this is what makes the tool useful rather than a guessing game
- [ ] Sync species and symptoms to the URL so results can be bookmarked and shared
- [ ] Wire the URL state into the existing share mechanism
- [ ] Handle the empty case — nothing selected yet
- [ ] Handle the no-match case — symptoms matching no poultry disease
- [ ] Add an age filter using the 179 age-group records
- [ ] **Verify against the 45 `diagnostic_sessions`** — the engine must rank the same diseases in the same order
- [ ] Fix whatever the verification reveals, re-run until it passes
- [ ] Automated tests for ranking order, no-match path, empty path
- [ ] Confirm Large Animals diagnosis still works unchanged

---

## Phase 5 — Clinical Findings Filter

**Routes:** `GET/POST /poultry/filter`
**Milestone:** A user selects symptoms and gets matching diseases, working as the exact reverse of
the diagnosis page.

**What this page is used for:** The mirror image of the diagnosis tool — and the point the client made
himself. The diagnosis page asks *"I have these symptoms, what is it?"*. This page asks *"I saw this
symptom, what could cause it?"*. A farmer who spots one worrying sign but has no full picture uses this.

### Tasks

- [ ] Adapt `LargeAnimals\FilterController` and `FilterService` into Poultry equivalents
- [ ] Build the symptom multi-select using the same 458 clinical signs
- [ ] Support one symptom and several
- [ ] Build the results view: every disease matching the selected symptoms
- [ ] Show overlap — how many of a disease's symptoms the user selected out of its total
- [ ] Sort by match strength
- [ ] Link each result to the disease detail page
- [ ] Add search by symptom name
- [ ] Sync selections to the URL
- [ ] Add empty state and no-results state
- [ ] Reuse the Phase 4 results component rather than writing a second one
- [ ] Feature tests: single symptom, multiple symptoms, no results

---

## Phase 6 — Global Search

**Route:** `GET /poultry/search`
**Milestone:** One search box returns matching diseases, symptoms, species and products, with live
suggestions.

**What this page is used for:** A single entry point when the user does not know which section to go
to. Instead of guessing whether the answer lives in the disease library or the drug catalogue, they
type one word and get everything relevant. The suggestions dropdown matters most — most users never
press enter.

### Tasks

- [ ] Adapt `LargeAnimals\SearchController` into a Poultry search controller
- [ ] Search across diseases, findings, species, and (Phase 9) products
- [ ] Filter to the Poultry domain except products, which use species matching
- [ ] Add a live suggestions endpoint returning matches as the user types
- [ ] Group dropdown results by type, with icons per type
- [ ] Support Arabic search terms as well as English
- [ ] Add fuzzy matching so near-misses still find something
- [ ] Build the full results page with type filters
- [ ] Add an empty state with helpful suggestions
- [ ] Debounce input so we do not query on every keystroke
- [ ] Rate-limit the suggestions endpoint
- [ ] Tests: English match, Arabic match, fuzzy match, no results

---

## Phase 7 — Disease Comparison

**Route:** `GET /poultry/compare`
**Milestone:** A user can select up to four poultry diseases and see them side by side.

**What this page is used for:** When a disease looks similar to another, the practical question is
*"how do I tell these apart?"*. This lines the candidates up so the differences are obvious at a
glance — shared symptoms, unique symptoms, affected organs, age. It saves opening several pages and
comparing from memory.

### Tasks

- [ ] Adapt the existing `⚡disease-compare-builder` for poultry rather than writing a new one
- [ ] Restrict the disease picker to poultry diseases
- [ ] Support 2 to 4 diseases
- [ ] Build the side-by-side table
- [ ] Show symptoms shared by all selected — these cannot tell them apart
- [ ] Show unique symptoms per disease — these are the differentiators
- [ ] Compare affected organs (`important_organs_affected`) and age groups
- [ ] Sync selections to the URL via the URL attribute so a comparison can be shared
- [ ] Add remove and clear-all controls
- [ ] Add per-column search within the builder
- [ ] Horizontal scroll on mobile
- [ ] Show a note explaining why shared signs do not help distinguish
- [ ] Tests: 2-way, 4-way, over-limit attempts, clear

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

## Phase 10 — Poultry Drugs

**Route:** `GET /poultry/products`
**Milestone:** A visitor can browse poultry-applicable drugs, see active ingredients and companies,
with links back to the disease pages they treat.

**What this page is used for:** The drug catalogue, restricted to what is relevant to poultry. This
completes the client's "three parts" structure — disease knowledge and treatment knowledge in one
place, so a vet reading about a disease can go straight to what treats it.

> **No new product data** — this reuses the existing catalogue and shows the poultry-relevant slice.

### Tasks

- [ ] Decide how a product is marked as poultry-applicable — reuse the existing species relationship
- [ ] Add a Poultry products index showing only poultry-applicable products
- [ ] Add filters: dosage form, active ingredient, manufacturer
- [ ] Reuse the existing product card and comparison components
- [ ] Link each product to the poultry diseases it treats or prevents
- [ ] Link each product to the relevant disease detail pages
- [ ] Link from each poultry disease page to the drugs that treat it — **this cross-link ties the section together**
- [ ] Show withdrawal periods prominently — these matter for food-producing birds
- [ ] Handle the case where no products are tagged yet — clear empty state
- [ ] Confirm the Drugs part of the site is unaffected
- [ ] Tests: filter, empty state, disease-to-product link

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

---

## 13. Changelog

| Version | Change |
|---|---|
| **v6** | Filament import page decision (option A): relocate `ImportDiseaseKnowledge` to the shared `DiseasesCluster` with a `domain` filter. Added §3.4 and Phase 1.7 tasks. |
| v5 | Discovered 154 disease JSON files (928 lesions, 13 knowledge fields). Chose Artisan command over seeder, `diseases.knowledge_payload` as the single target, all 13 transformer keys, `domain` values confirmed. Merge rule flagged as still-open. |
| v4 | Added phased execution plan: Phase 1 migrations/data, Phases 2–10 one per page, each with milestone, tasks and plain-English purpose. |
| v3 | All 5 decisions locked; URL naming fixed to match `large-animals`; file/route conventions, Tailwind v4 token rules, implementation notes. |
| v2 | Corrected: Large Animals already built; 660 lesions exist; Clinical Cases removed; pivot/engine match discovered. |
| v1 | First draft — contained incorrect claims in §0 and §3.1. |