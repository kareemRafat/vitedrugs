# VetPedia Drugs — Large Animals: Client Modifications Round (2026-08)

> Implement the client's feedback on the large-animals part (production `arvetpedia.com`): landing content showcase, footer redesign, diagnosis grid + prominent errors, optional species filter, "Antibiotics" → "Drugs" rename, and full Specializations restructure (Ruminants/Poultry/Aquaculture → Infectious Diseases / Internal Medicine / Zoonotic Diseases). English-only copy for new content until translation is fixed (`fallback_locale=en` covers `/ar`). All changes local-first; deploy later.

## Status

- **In progress** (2026-08-24). Milestones 1–4 complete. Milestone 4 verified: filter submits without species (share token + results page null-safe), invalid species still rejected; EN/AR pages 200 with "(optional)" hint. Pint clean; FilterToolTest 11 passed. Pre-existing unrelated failure kept: `AdminPanelSmokeTest` expects MySQL db `vetdrugs` which does not exist locally (fails on clean tree too).

## Decisions

- [x] Footer quick-links are **part-aware** (large-animals pages show large-animals links; drugs pages show drugs links) — fixes cross-project confusion.
- [x] Shared platform pages (blog/about/contact/privacy/terms) **keep the last visited project's theme** via session (SetPartTheme).
- [x] Zoonotic detection via a new `is_zoonotic` boolean **directly on `diseases`** (backfilled from `disease_classifications.zoonotic`) plus new `is_internal` boolean — matches client wording.
- [x] Specialization show pages become **static article + diseases list only** (products/articles/microorganisms sections removed there).
- [x] Microorganism section label: **"Drugs"**.
- [x] Landing gets a **full redesign** (services grid + live stats).
- [x] New copy is **English-only for now**; Arabic lang keys intentionally omitted (Laravel falls back to `en`).

---

## Milestone 1 — Database: disease category flags

- [x] **New** migration `add_is_zoonotic_and_is_internal_to_diseases_table`:
  - `$table->boolean('is_zoonotic')->default(false)` and `$table->boolean('is_internal')->default(false)` after `is_active`.
  - Backfill in `up()`: set `is_zoonotic = true` where the disease has `disease_classifications.zoonotic = 1` (query-builder statement, MySQL + SQLite compatible); reverse backfill in `down()` before dropping columns is not required (columns just drop).
- [x] **Edit** `app/Models/Disease.php`: add `is_zoonotic`, `is_internal` to `$fillable` and `$casts`.
- [x] **Edit** `database/factories/DiseaseFactory.php`: defaults `false` for both flags.
- [x] **Edit** `app/Filament/Resources/Diseases/Schemas/DiseaseForm.php`: two `Toggle`s (`is_zoonotic`, `is_internal`).
- [x] **Edit** `app/Filament/Resources/Diseases/Tables/DiseasesTable.php`: boolean icon columns + `TernaryFilter` (or `SelectFilter`) for each flag.

## Milestone 2 — Specializations restructure

- [x] **Rewrite** `app/Http/Controllers/LargeAnimals/SpecializationController.php`:
  - `GROUPS = ['infectious-diseases', 'internal-medicine', 'zoonotic-diseases']`.
  - `index()`: per-group disease counts only.
  - `show(string $group)`: 404 on unknown keys (legacy `ruminant|poultry|fish` 404 too); diseases query per group: infectious = **all** active diseases ordered by name; internal = `is_internal`; zoonotic = `is_zoonotic`. No species/products/articles/microorganisms logic.
- [x] **Edit** `resources/views/large-animals/specializations/index.blade.php`: three category cards with icons (`bug` — no `virus` icon in installed Lucide set, `stethoscope`, `globe`), description snippet, disease count.
- [x] **Edit** `resources/views/large-animals/specializations/show.blade.php`: breadcrumb + hero + static article block + diseases grid (existing card pattern). Remove products/articles/microorganisms sections.
- [x] Static article copy implemented as **lang keys** (`specializations.articles.{group}.{title,paragraphs}`) instead of blade partials — renders bilingually from the show view; no separate partials needed.
- [x] **Edit** `lang/en/large-animals.php` + `lang/ar/large-animals.php`: new group names (Infectious Diseases / Internal Medicine / Zoonotic Diseases — الأمراض المعدية / الأمراض الباطنة / الأمراض المشتركة), descriptions, full EN+AR article content; unused species/products/articles strings removed.
- [x] **Edit** `app/Models/Disease.php`: added `localized_name` accessor (`name_ar` on AR locale) so category pages show Arabic disease names.

## Milestone 3 — Diagnosis page UX

- [x] **Edit** `resources/views/large-animals/diagnosis/index.blade.php`:
  - Wrap symptom inputs in `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3`; initial state = one row of 3 inputs.
  - Alpine `addRow()` appends **a row of 3** inputs; per-input remove button shown when total count > 3 (absolute-positioned X inside the cell).
- [x] Prominent validation alerts (Flowbite v4 danger tokens `bg-danger-soft border-danger-subtle text-fg-danger-strong` + `lucide-alert-triangle`, `role="alert"`) for `host_species_id`, `clinical_signs`, `clinical_signs.*` errors replacing the small `<p>` markup.
- [x] Client-side guard on submit (`onSubmit → validate()`): any input with free text but no selected suggestion id gets a red `border-danger-subtle` state, focus jumps to first invalid input, prominent alert shows exact message `__('validation.diagnosis.sign_invalid')`, submission aborted. Guard clears when a suggestion is selected.
- [x] Species + signs headings marked required: red `*` suffix, `aria-required="true"`; server rule already `required`.
- [x] **New** `tests/Feature/DiagnosisFormTest.php`: species+min:3 enforcement, non-existent sign ids rejected, valid 3-sign submission renders results (3 passed).

## Milestone 4 — Filter page: optional species

- [x] **Edit** `app/Http/Requests/LargeAnimals/FilterRequest.php`: `host_species_id` rule `required` → `nullable` (keep `exists`); message stays.
- [x] **Edit** `app/Services/LargeAnimals/FilterShareService.php`: `create()` no longer assumes `host_species_id` exists (`?? null`); `read()` validation accepts null species — fixes 500 when submitting without species (caught by new test).
- [x] **Edit** `resources/views/large-animals/filter/index.blade.php`: species label hint "(optional)" via new lang key `filter.optional` (en/ar) — applied to filtering only when chosen.
- [x] **Edit** `lang/ar/large-animals.php`: removed stale "(اختياري)" from the **diagnosis** species label (species is required there per client).
- [x] Verified: results page renders fine without species criteria (FilterService already guards empty `host_species_id`); FilterToolTest extended to 11 tests, all passing.
- [x] Follow-up fix: submitting the form completely empty silently redirected back with no feedback — added prominent red alert (`bg-danger-soft border-danger-subtle`, warning icon, `role="alert"`) above the sign picker for `clinical_signs` / `clinical_signs.*` errors + required `*` marker on "Selected clinical signs". Added filter-specific validation messages (`validation.filter.*` en/ar) because it previously reused diagnosis copy saying "three signs" while the filter requires one ("يرجى إضافة علامة سريرية واحدة على الأقل لتصفية الأمراض."). FilterToolTest now 12 tests, all passing.
- [x] Follow-up fix: after a failed submit the redirect-back landed at page top, hiding the alert — both filter and diagnosis pages now auto smooth-scroll to the first `[role="alert"]` on load when validation errors exist (`@if ($errors->any())` guarded inline script).
- [x] Follow-up (Option B): filter form submits via `fetch` — no reload on validation errors. `FilterController::store()` returns `{ redirect }` JSON when `wantsJson()`; view uses `@submit.prevent` + Alpine `submitForm()`: empty tokens → instant localized alert (no request); 422 → inline red alert from JSON errors (token chips preserved); success → navigate to results URL; fetch failure → native form fallback. Submit button disabled while pending. FilterToolTest extended with AJAX cases (422 JSON + redirect payload) — 14 tests passing.

## Milestone 5 — Microorganism show label

- [ ] **Edit** `lang/en/large-animals.php` microorganisms keys: `antibiotics` → value `"Drugs"`, `no_antibiotics` → `"No linked drugs."`, `antibiotic_count` → `"Linked drugs"` (keys renamed to `drugs` / `no_drugs` / `drug_count`).
- [ ] **Edit** `lang/ar/large-animals.php`: matching Arabic values (`الأدوية` / `لا توجد أدوية مرتبطة.` / `الأدوية المرتبطة`).
- [ ] **Edit** `resources/views/large-animals/microorganisms/show.blade.php`: reference renamed keys.

## Milestone 6 — Landing redesign (`/large-animals`)

- [ ] **Edit** `app/Http/Controllers/LargeAnimals/DrugLandingController.php`: gather live counts (active diseases, clinical signs, microorganisms, published articles, projects, host species).
- [ ] **Rewrite** `resources/views/large-animals/landing.blade.php`:
  - Hero (badge + heading + subtitle) using existing gradient pattern.
  - Services/features grid linking Diagnosis, Filter, Comparison, Microorganisms, Articles, Specializations, Projects (Lucide icons + short English copy).
  - Stats band with live counts.
  - Compact CTA strip.
  - All copy via new `large-animals.landing.*` lang keys (English only).

## Milestone 7 — Footer redesign + part-awareness

- [ ] **Edit** `app/Http/Middleware/SetPartTheme.php`: remember last project part in `session()` whenever URL resolves to `drugs|large-animals|poultry`; fallback for non-project URLs reads session (default unchanged: first PARTS entry).
- [ ] **Edit** `resources/views/app/layouts/footer.blade.php`:
  - Compress vertical space (~40%): smaller paddings/gaps/hr margins.
  - Part-aware "Quick Links": large-animals → Diagnosis/Filter/Specializations/Microorganisms/Articles; drugs → Products/Diseases/Ingredients/Companies/Blog.
  - Keep Resources column (About/Contact/Privacy/Terms) + Language column + compact copyright row.
- [ ] Sanity-check header/footer on platform pages reached from each part (blog, about, contact).

## Milestone 8 — Tests & QA

- [ ] **New** `tests/Feature/SpecializationsTest.php`: index shows 3 categories with correct counts; each group lists only its diseases (flags respected); legacy group slug 404s; article partial rendered.
- [ ] **Edit** `tests/Feature/FilterToolTest.php`: species omitted → still filters by signs; invalid species id still rejected.
- [ ] **New** minimal diagnosis validation test: free-text sign id → session error with `sign_invalid` message; missing species → error; valid 3 signs passes.
- [ ] Run `vendor/bin/pint --dirty --format agent`.
- [ ] Run affected tests filtered, then `php artisan test --compact`.
- [ ] `npm run build` so new Tailwind v4 classes compile for local testing.

## Non-goals / Follow-ups

- [ ] 301 redirects from `/specializations/{ruminant,poultry,fish}` (only if SEO report demands it).
- [ ] Arabic translations for all new copy (deferred by client).
- [ ] Making large-animals the site-wide default part for bare first visits (out of scope unless requested).
