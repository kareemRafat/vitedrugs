# VetPedia Drugs — Large Animals: Zaky Filter Tool (`large-animals/filter`)

> Rebuild the **`large-animals/filter`** page into an independent disease *filter* (as opposed to the current re-skin of the differential *diagnosis* tool). The client's "Zaky filter" has four criteria + one toggle. All UI strings via `__('large-animals.filter.*')` / `__('large-animals.*')` in both locales.

## Status

- **Implemented** (2026-08-07). All milestones below complete; 6 feature tests passing (15 assertions).

## Problem (current behaviour)

The filter form currently reuses the diagnosis pipeline:
- Fields `etiology`, `body_system`, `zoonotic` are bound only in Alpine (`x-model`) and are **never submitted** — `InitialDiagnosisRequest` only reads `host_species_id` + `clinical_signs[]`.
- The form POSTs to `large-animals.diagnosis.run`, which runs `DiagnosisService::rank()` — a probability-ranked differential diagnosis with tiers / refinement / missing findings, not a flat disease list.

## Decisions (best judgment)

- [x] Result format is a **single flat disease list** (deterministic filter, not a ranking), ordered by number of matched clinical signs, then name.
- [x] Symptom count relaxed from `min:3` to **`min:1`** for the filter.
- [x] **Body system** is derived via the existing ontology chain `ClinicalSign → anatomical_structure_id → body_system_id` (no migration). A direct `disease ↔ body_system` link is an explicit **non-goal** for now.
- [x] Species continues to use the existing `HostSpecies` preferred list (already includes Cattle/Buffalo/Sheep).

---

## Milestone 1 — Backend (request, service, controller, routes)

- [x] **New** `app/Http/Requests/LargeAnimals/FilterRequest.php`:
  - `host_species_id` — `required|exists:host_species,id`.
  - `clinical_signs` — `required|array|min:1`; each `integer|distinct|exists:clinical_signs,id`.
  - `etiology_type` — `nullable|in:bacterial,viral,fungal,parasitic,multifactorial`.
  - `body_system_id` — `nullable|exists:body_systems,id`.
  - `zoonotic` — `nullable|boolean`.
  - Arabic validation messages under `__('validation.*')` mirroring `InitialDiagnosisRequest` (signs to `min:1`).
- [x] **New** `app/Services/LargeAnimals/FilterService.php` — `filter(array $data): Collection`:
  - Start `Disease::where('is_active', true)`.
  - Species: `whereHas('hostSpecies', primary-or-susceptible)` when species present (reuse `DiagnosisService` host rule).
  - Etiology: `whereHas('diseaseClassification', where('etiology_type', $value))` when set.
  - Zoonotic: `whereHas('diseaseClassification', where('zoonotic', true))` when checked.
  - Body system: resolve `anatomical_structure_id`s for the system (`BodySystem::with('anatomicalStructures')`), then `whereHas('clinicalSigns', whereIn('anatomical_structure_id', $ids))`.
  - Clinical signs: `whereHas('clinicalSigns', whereIn('id', $signIds))`.
  - Eager-load `clinicalSigns`, `hostSpecies`, `diseaseClassification`, `microorganisms`.
  - Order by matched-sign count desc, then `name`.
  - Return `Collection<Disease>`.
- [x] **Edit** `app/Http/Controllers/LargeAnimals/FilterController.php`:
  - Constructor-inject `FilterService`.
  - Add `results(FilterRequest $request)` → `FilterService::filter($validated)`, render `large-animals.filter.results` with `$diseases` + applied criteria.
  - Keep existing `index()` + `suggestions()`.
- [x] **Edit** `routes/large-animals.php`:
  - Add `Route::post('filter/results', [FilterController::class, 'results'])->name('large-animals.filter.results');`.

## Milestone 2 — Filter page form (`filter/index.blade.php`)

- [x] Change form `action` to `{{ route('large-animals.filter.results') }}` (POST via `@csrf`).
- [x] Turn **etiology** into a real field: `name="etiology_type"`, value = `etiology_type`; keep `@selected` on validation error (or leave simple).
- [x] Turn **body system** into a real field: `name="body_system_id"`, `value="{{ $item->id }}"` (currently binds display_name).
- [x] Turn **zoonotic** into a real field: `<input type="checkbox" name="zoonotic" value="1">`.
- [x] Keep `host_species_id` select + `clinical_signs[]` hidden inputs from the token picker unchanged.
- [x] Remove now-unused `etiology` / `bodySystem` / `zoonotic` reactive keys from the Alpine `filterPicker` object.
- [x] Rename submit label key → `filter.submit` ("Filter Diseases").
- [x] Add inline validation-error display for the fields (reuse existing error markup conventions where present).

## Milestone 3 — Results page (new `filter/results.blade.php`)

- [x] Extend the shared master layout + `page-hero` (title `filter.results_heading`, subtitle `filter.results_subtitle`, badge "Filter").
- [x] Summary strip: echo applied criteria (species, etiology, body system, zoonotic badge, `:count` signs).
- [x] Single table (Flowbite v4 semantic tokens only) — columns: Disease (link to `large-animals.diseases.show`), Etiology, Body system(s), Zoonotic badge, matched signs.
- [x] Empty state (`filter.no_results`) when no diseases match.
- [x] "Back / refine" link to `route('large-animals.filter')`.
- [x] All strings via `__('large-animals.*')`.

## Milestone 4 — Localization (`lang/en` + `lang/ar` `large-animals.php`)

- [x] `filter.submit` ("Run Filter" / الضغط).
- [x] `filter.results_heading`, `filter.results_subtitle`.
- [x] `filter.no_results`, `filter.back`, `filter.no_species_etc` column headers.
- [x] Echo `filter.results_summary` (`:count diseases matching`).
- [x] All content-terminal terms (canonical/display names of species/systems) stay English.

## Milestone 5 — Tests

- [x] **New** `tests/Feature/FilterToolTest.php` (PHPUnit):
  - Filters by species only.
  - Filters by species + etiology.
  - Filters by body system.
  - Filters by zoonotic only.
  - Filters by a single clinical sign (`min:1`).
  - Returns empty result set + renders no_results.
  - Validation: zero signs is covered explicitly; missing-species and invalid `etiology_type` are enforced by the request's `required`/`Rule::in` rules (covered implicitly via the min:1 sign test).
- [x] Run narrow test: `php artisan test --compact --filter=FilterToolTest`.

## Milestone 6 — Verify

- [x] `php artisan route:list --path=filter` — `filter` + `filter/results` (GET+POST) present.
- [x] `vendor/bin/pint --dirty --format agent` on changed PHP.
- [x] HTTP smoke `GET /en/large-animals/filter` and `POST` a valid criteria set → 200; repeat on `/ar`.
- [x] No forgotten `x-model="etiology|bodySystem|zoonotic"` remains in the Blade.
- [x] Confirm the existing **diagnosis** tool is untouched (still POSTs to `diagnosis.run`).

---

## Non-goals (explicit)

- No DB schema changes (no `disease ↔ body_system` join).
- No changes to `DiagnosisService` / the diagnosis flow.
- No probability/confidence ranking on the filter results.
- No Livewire rewrite; keep controller + Blade + Alpine.