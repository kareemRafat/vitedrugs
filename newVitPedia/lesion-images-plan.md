# Lesion Photographs + `OntologyType` Plan — Vetpedia

> **Status:** v3 — **Phases 0.1–0.3, 0.5, 0.6 and 1–6 shipped.** Two items open: **0.4** (needs the §0.3 decision) and **7.1** (resize — needs GD confirmed). **Phase 7** (bulk import) designed and deferred by decision.
> **Date:** 2026-10-17
> **Parent document:** [`poultry-plan.md`](./poultry-plan.md) — this closes out its **Phase 9** open task
> *"Add a documented upload path for images via Filament — blocked on the media table"* (§9.2, line 1417).

---

## How to read this document

Tasks use markdown checkboxes. Tick a box only when the task is genuinely done.

- **Phase 0** is a bug fix, not a prerequisite for the images — but it is cheap, it lands on the same files, and it removes a silent-failure field before we start writing 640 records into it.
- **Phase 0.4 blocks** the non-nullable migration. It needs a human decision per row.
- **Phases 1–3** build the upload path. **Phase 4** makes it visible on the atlas. **Phase 5** is tests.
- Each phase has a **Milestone** — the "done" test.

---

## 0. Why this document exists

### 0.1 The atlas is 640 empty frames

The lesion atlas is a photo-first tile grid. Every tile currently renders `<x-lucide-image-off>` because **there is no lesion media anywhere in the application** — no image table, no upload path, no files. `poultry-plan.md` §9.2 already recorded this honestly rather than marking the task done against a table that does not exist.

640 poultry lesions, 0 photographs. The grid is waiting for the one thing that would justify it.

### 0.2 The pattern to copy — do not invent a new one

Products already do this end to end:

| Piece | Path |
|---|---|
| Table | `database/migrations/2026_06_14_154817_create_product_images_table.php` |
| Model | `app/Models/ProductImage.php` |
| Admin UI | `app/Filament/Resources/Products/RelationManagers/ImagesRelationManager.php` |

`poultry-plan.md` §9.2 names `product_images` as "the only image table" in this application. Copy its shape exactly — `ProductImage::url()` and the `DeleteAction::before()` storage cleanup are reusable as-is.

### 0.3 ⚠️ The 17 unlabelled rows are read three different ways

`findings.ontology_type` is a **nullable string**. 17 rows are `NULL`, and the codebase disagrees on what that means:

| Reader | Line | A `NULL` finding is treated as |
|---|---|---|
| `Disease::signItems()` | `Disease.php:129` (`!== ONTOLOGY_LESION`) | a **sign** |
| `OrganAttachmentService` | `:157` (`=== 'sign'`) → `:162` (`!== 'sign'`) | a **lesion** |
| `LesionAtlasService` | `:379` (`= 'lesion'`) | **neither** |
| `PoultryLandingController` | `:32` (`= 'lesion'`) | **neither** |

**Live symptom:** the body map currently renders *Pyrexia*, *Inappetence*, *Cough*, *Bloat* and *Mastitis* under its **lesions** heading, while the atlas omits them entirely. 20 `clinical_signs` rows hang off these findings.

The comment at `OrganAttachmentService.php:159` claims "null on 22 rows" — stale, it is 17.

**This must not be fixed with a blanket `UPDATE`.** The rows split:

*Clearly clinical signs* — Pyrexia, Inappetence, Cough, Bloat, Mastitis, Mammary gland heat, Profuse diarrhea, Hemorrhagic diarrhea, Reduced milk production, Temporary infertility, Recumbency

*Plausibly lesions* — Vesicle, Erosion, Ulcer, Lymphadenitis, Submandibular lymphadenitis, Hoof sloughing

> **Decision needed.** Fill the third column. Leave a row blank to keep it `NULL` and skip Phase 0.4.

| id | display_name | decision |
|---|---|---|
| 1 | Pyrexia | |
| 3 | Inappetence | |
| 8 | Vesicle | |
| 9 | Erosion | |
| 10 | Ulcer | |
| 11 | Recumbency | |
| 13 | Reduced milk production | |
| 16 | Cough | |
| 20 | Bloat | |
| 25 | Lymphadenitis | |
| 26 | Submandibular lymphadenitis | |
| 28 | Temporary infertility | |
| 29 | Hoof sloughing | |
| 30 | Mastitis | |
| 31 | Mammary gland heat | |
| 32 | Hemorrhagic diarrhea | |
| 33 | Profuse diarrhea | |

---

## Phase 0 — Make the discriminator a closed set

> **What this uses:** `findings.ontology_type` decides whether a row appears in the atlas at all. The admin form offers a **free-text box** for it. The atlas filter is an exact string match (`where('ontology_type', 'lesion')`), so an admin who types `Lesion` — capital L — gets a save that succeeds and a lesion that has silently vanished from the page. 20 rows in this codebase already depend on the value.

- [x] **0.1** `app/Enums/OntologyType.php` — backed string enum, `Lesion = 'lesion'`, `Sign = 'sign'`, with a `color()` for badges.
- [x] **0.2** `Finding` — cast `ontology_type`; `ONTOLOGY_LESION` const deleted and its **5** call sites repointed (`LesionAtlasService`, `Disease` ×2, `DiseaseComparisonTest`, `PoultryLesionAtlasTest`).
- [x] **0.3** `FindingForm.php` — `TextInput` → `Select::make('ontology_type')->options(OntologyType::class)->required()`.
- [ ] **0.4** Migration — backfill the 17 rows from §0.3, then make `ontology_type` **non-nullable**. **Blocked on the §0.3 decision.**
- [x] **0.5** `FindingsTable.php` — `SelectFilter::make('ontology_type')`, plus `->badge()` on the column.
- [x] **0.6** `FindingInfolist.php` — `TextEntry` reads the enum label and colour.

> **Three silent breaks the cast would have caused, found and fixed before shipping:**
> - `Disease::signItemsOfType(string $ontologyType)` compared with `===` against a now-enum attribute — **always false**, which would have emptied every lesion list in the application. Signature changed to `OntologyType`.
> - `OrganAttachmentService` used `Collection::where('ontology_type', 'sign')`. `OntologyType::Sign == 'sign'` is **false** in PHP, so that filter returned **zero rows**. Now an explicit `filter()`.
> - `FindingFactory` generated `['sign', 'symptom', 'finding', 'lesion']` — **two of those four are not values anything in the app recognises**, so fixtures were building rows no production page would ever show. `PoultryDataSeeder`'s unmapped-type fallback was `'finding'` for the same reason.

**Milestone:** `/admin/diagnosis/large-animals/findings` filters to **Lesion = 640 rows**, and creating a finding cannot produce a value outside the enum.

---

## Phase 1 — Storage

- [x] **1.1** `php artisan make:migration create_finding_images_table` — `2026_10_16_235155`, migrated.

> **Note:** `finding_id` is a plain `foreignId`, **not** `foreignUlid`. `findings` uses `bigint unsigned AUTO_INCREMENT` — the one domain model in this project that is not ULID, and `Finding` does not use `HasUlids`.

```
id          ulid primary
finding_id  foreignId → findings, cascadeOnDelete
image       string
caption     string nullable
caption_ar  string nullable
sort_order  integer default 0
timestamps
```

- [x] **1.2** `caption` / `caption_ar` are load-bearing, not padding — a gross-lesion photo without a label ("liver, pale and mottled") is close to useless on an atlas. Both shipped.

**Milestone:** `php artisan migrate` succeeds and `finding_images` exists with a cascade FK.

---

## Phase 2 — Model + relation

- [x] **2.1** `app/Models/LargeAnimals/FindingImage.php` — `HasFactory` + `HasUlids`, matching `ProductImage`.
- [x] **2.2** `url()` copied from `ProductImage::url()` — keeps the "already an absolute URL" branch, which matters if any image is ever sourced externally.
- [x] **2.3** `Finding::images(): HasMany`, ordered by `sort_order` — the ordering is what decides which photograph the tile shows.
- [x] **2.4** `database/factories/FindingImageFactory.php` with `uncaptioned()` and `bilingual()` states.
- [x] **2.5** `localized_caption` accessor — Arabic when the locale is Arabic *and* the row carries one, English otherwise. An Arabic page showing an English caption beats showing nothing.

**Milestone:** `$finding->images` resolves; the factory builds one with a faker path.

---

## Phase 3 — The upload form

> **What this uses:** `FindingResource::getRelations()` returned `[]` — this is the first relation manager on this resource.

- [x] **3.1** `ImagesRelationManager.php`, with a **per-owner directory**: `->directory(fn () => 'findings/'.$this->getOwnerRecord()->getKey())`. (`BlogForm` uses a flat `blog/covers`; 640 findings in one folder is not browsable.)
- [x] **3.2** Reorderable by `sort_order`; `ImageColumn` with `->disk('public')->visibility('public')`; `DeleteAction` with `before()` deleting off disk.
- [x] **3.3** Registered in `FindingResource::getRelations()`
- [x] **3.4** ~~`ImageEntry` in `FindingInfolist`~~ — **not needed.** Relation managers render on the *View* page as well as Edit, so the Images tab already covers the "see them without entering edit" case. Adding an entry too would have shown the same photographs twice on one screen.
- [x] **3.5** `php artisan storage:link` run — **it had never been run in this project**, so product and blog images were 404ing too. `composer setup` never ran it either; that step has been added.

> **Two deliberate deviations from the Products relation manager**, both noted in the class docblock:
> - **One file per submit, not `->multiple()`.** A gross lesion is one specimen — there is no second angle worth having — and `multiple()` returns a bare array of paths with nowhere to put a caption. `sort_order` remains for the cases that genuinely need more.
> - **`->image()->maxSize(8192)`, which the product version lacks.** That upload accepts whatever it is given, and a post-mortem photograph is the largest file anyone will put in this application. The security rule on file uploads is validation, not decoration.

**Lands on:** `/admin/diagnosis/large-animals/findings/{id}/edit` → **Images** tab

**Milestone:** upload a photo on any finding, reload, it survives; delete it, the file leaves disk.

---

## Phase 4 — Show it on the atlas

> **What this uses:** `LesionAtlasService` already eager-loads diseases and organs for the page's 48 findings. Images cost **one more query** — the test budget is ≤26 and there is room.

- [x] **4.1** Eager-load `images` on the page's findings; expose a `photo` key in the row payload.
- [x] **4.2** `@return` array shape in the `lesionsPaginator()` docblock updated to `photo: FindingImage|null`.
- [x] **4.3** `⚡lesion-atlas.blade.php` — `<img loading="lazy">` with `object-cover` inside the existing 4:3 frame when `photo` is set; placeholder otherwise.
- [x] **4.4** `sr-only` `image_soon` kept for the empty case, so the placeholder test still holds.
- [x] **4.5** `alt` is the lesion name and `title` is the caption. Same string in both would do neither job — `alt` names the image for a screen reader, `title` describes it on hover. `title` is omitted entirely when the caption is blank rather than emitted empty.

**Milestone:** a lesion with a photograph renders an image on the atlas; one without still renders the placeholder and still contains no `<img>`. Verified in a browser at 640×480 source rendered into a 329×246 frame with `object-fit: cover`.

---

## Phase 5 — Tests

> PHPUnit classes, not Pest. Follow `tests/Feature/Poultry/PoultryLesionAtlasTest.php`.

- [x] **5.1** `FindingImageFactory` states `uncaptioned()` and `bilingual()`.
- [x] **5.2** Atlas renders `<img>` for a lesion with a photo — *and not the placeholder*, asserted both ways.
- [x] **5.3** Atlas renders the placeholder and **no** `<img>` for a lesion without.
- [x] **5.4** Query budget still ≤26 at 48 lesions — the eager-load cost exactly one query.
- [x] **5.5** `tests/Feature/FindingOntologyAdminTest.php` — 11 tests: the form offers exactly the two enum values, the stored value reads back as an enum, a miscategorised finding is correctable, a null type cannot be created, and the list filters to lesions-only and signs-only.
- [x] **5.6** Upload covered on the same file: the Images tab renders on the edit page, the manager mounts, a photo + both captions store, a photo with no caption stores, and deleting removes the file from disk.
- [x] **5.7** Tile semantics: `alt` is the lesion name, `title` is the caption, no empty `title`, lowest `sort_order` wins, and only the first photograph of several reaches the page.

**Result:** `319 passed / 8 skipped / 0 failed` (1025 assertions). Pint clean. `npm run build` run.

> **Two unrelated pre-existing flakes** surfaced across three full runs — `PartProductCatalogueTest` and a `dosage_forms.slug` unique collision. Both are Faker-seed dependent, both pass in isolation and on re-run, and neither touches findings, ontology or images.

---

## Phase 6 — Photograph on the create form

- [x] **6.1** `FindingForm` — existing fields wrapped in `Section::make('Details')`; `Section::make('Photograph')` holds the upload plus both captions.
- [x] **6.2** `->disk('public')->visibility('public')->image()->maxSize(8192)` — same validation as `ImagesRelationManager`, so the two entry points cannot drift.
- [x] **6.3** `->directory('findings/tmp')` — staged, not filed.
- [x] **6.4** `CreateFinding::handleRecordCreation()` — create the finding, `Storage::move()` the upload to `findings/{id}/`, then write the `finding_images` row.
- [x] **6.5** `image`, `caption`, `caption_ar` stripped from `$data` before `Finding::create()`.
- [x] **6.6** Any failure in the attach step is caught, logged, and surfaced as a warning notification. The lesion is still created.
- [x] **6.7** Field hidden on `EditFinding`, keyed off `$schema->getLivewire() instanceof CreateRecord`.

### The one wrinkle this had to solve

**Filament uploads the file when the form submits — before the finding exists.** `findings.id` is a `bigint AUTO_INCREMENT`, so `findings/{id}/…` cannot be named at upload time.

| | How | Cost |
|---|---|---|
| **A — chosen** | Stage in `findings/tmp`, then `Storage::move()` into `findings/{id}/` | one extra move, invisible to the user |
| B | Directory from the `canonical_name` slug | two conventions on disk for one thing |
| C | Leave it in a UUID folder | loses the per-finding grouping |

**A** was chosen so both entry points file into the same folder and there is one convention to look for on disk.

### Tests — 9 added to `FindingOntologyAdminTest`

One `findings` row and one `finding_images` row · the path starts `findings/{id}/` · **`findings/tmp/` is left empty** · no photo still creates no image row · a blank caption stores `null` not `''` · the photo fields never reach the `findings` table · a PDF renamed `.jpg` is rejected · **the lesion survives a photograph that cannot be filed** · the edit form does not offer the field

**Result:** `328 passed / 8 skipped / 0 failed` (1060 assertions).

> **Two findings from writing the tests, both fixed:**
> - Filament rewrites upload filenames to **UUIDs**. An assertion on the original filename would have been asserting a detail Filament owns; only the extension is stable.
> - `->acceptedFileTypes()` **overrides** `->image()` rather than adding to it. An explicit list of jpeg/png/webp/avif was silently dropping **heic and heif** — exactly what a vet photographing on a phone will have. `->image()` alone resolves to `image/*`.

---

## Phase 7 — Bulk photograph import *(not built — deferred by decision)*

> **Deferred.** The four decisions it turns on were put to the client and the work was explicitly parked.

The open questions, for whenever it is picked up:

- **Where it runs.** A CLI command (`php artisan findings:import-images <folder>`) or a box in the admin panel. The panel feels friendlier but sends the whole folder in one HTTP request — 640 photos at 2 MB is ~1.3 GB, which times out partway and leaves you guessing which landed. The CLI has no such ceiling.
- **How files match lesions.** By `canonical_name` in the filename is the obvious answer, and needs **no new column**: `findings.canonical_name` is already uniquely indexed, so it is one indexed lookup. Storing the filename on the row was rejected — the filename belongs to the file on disk, not the lesion, and the two go stale silently.
- **Captions.** Left blank, filled later in the Images tab.
- **Safety.** Dry run first (report matches, misses and skips; change nothing), then a real run that never overwrites without an explicit flag.

---

## 6. Files touched

| File | Change |
|---|---|
| `app/Enums/OntologyType.php` | **new** |
| `app/Models/LargeAnimals/Finding.php` | enum cast, `images()`, const deleted |
| `app/Models/LargeAnimals/FindingImage.php` | **new** |
| `app/Models/Disease.php` | `signItemsOfType()` signature, enum comparisons |
| `app/Services/Knowledge/LesionAtlasService.php` | eager-load `images`, `photo` key |
| `app/Services/Knowledge/OrganAttachmentService.php` | Collection filter vs enum, stale comment |
| `app/Http/Controllers/Poultry/PoultryLandingController.php` | raw string → enum |
| `app/Filament/.../Findings/FindingResource.php` | register relation |
| `.../Findings/Schemas/FindingForm.php` | `Select` for ontology; two sections; create-only photo field |
| `.../Findings/Pages/CreateFinding.php` | `handleRecordCreation()` — create, move, attach |
| `.../Findings/Schemas/FindingInfolist.php` | enum badge |
| `.../Findings/Tables/FindingsTable.php` | ontology filter + badge |
| `.../Findings/RelationManagers/ImagesRelationManager.php` | **new** |
| `resources/views/.../⚡lesion-atlas.blade.php` | `<img>` branch |
| `database/migrations/…_create_finding_images_table.php` | **new** |
| `database/factories/FindingImageFactory.php` | **new** |
| `database/factories/FindingFactory.php` | invalid enum values removed |
| `database/seeders/PoultryDataSeeder.php` | invalid fallback removed |
| `tests/Feature/FindingOntologyAdminTest.php` | **new**, 20 tests |
| `tests/Feature/Poultry/PoultryLesionAtlasTest.php` | 5 photo tests |
| `tests/Feature/DiseaseComparisonTest.php` | helper typed to the enum |
| `tests/Feature/Poultry/PoultryDataSeederTest.php` | asserts the enum |
| `composer.json` | `storage:link` added to `setup` |

**Not done:** the `ontology_type` non-nullable migration — waiting on §0.3.

---

## 7. Open decisions — **need an answer before Phase 1**

### 7.1 Resize on upload?

**Not done — deferred deliberately.** `->image()` validates the MIME type and `->maxSize(8192)` rejects the 50 MB phone photo, but nothing downsizes. Post-mortem photographs are routinely 5 MB+ and the atlas tile is ~300 px wide.

Adding `->resize()` or `Rule::dimensions()` would need GD or Imagick confirmed on the deployment target, and neither is verified here. Flagging rather than shipping something that might fail at upload time in production.

### 7.2 Public or private disk?

**Decided: `public`** — matches `product_images` and `blog/covers`, and keeps `<img src="{{ $url }}">` trivial. Reversible in one place (`FindingImage::url()`) if it needs to change.

> ⚠️ **Not a coding concern but a licensing one.** Public URLs are hotlinkable and indexable. If these become licensed textbook images rather than your own photographs, they need `private` + signed URLs, which changes the tile markup and costs a query per tile.

> ⚠️ **`APP_URL` must match the served origin.** `Storage::url()` builds from `APP_URL`, so on any environment where the app is served from a different host or port than `APP_URL` states, every image 404s. This bit during verification — `.env` here has `APP_URL=http://localhost` while the dev server runs on `:8123`.

### 7.3 Body map thumbnails too?

The body map already lists lesions per organ with a cap and "show more" (`poultry-plan.md` Phase 8). A thumbnail there costs roughly 10 lines on the same eager-load, and it is what lets the two pages coexist rather than compete.

The reasoning that gets us here: the atlas earns its own URL back once photographs exist — 640 of them is a gallery, not an accordion. The body map stays the anatomical lookup. Before photographs existed, the two pages were answering the same question twice.

### 7.4 Backfill the 17 rows?

See §0.3. Blocks Phase 0.4.

---

## 8. Out of scope

- **Arabic lesion names.** `poultry-plan.md` line 1419 records **1 of 640** lesions has an Arabic name. That is a content problem, not a code problem, and `Finding` already carries `display_name_ar`.
- **Microscopic lesions.** The dump has 42 of them; nothing in this plan stores them.
- **Bulk import.** Photographs are added one finding at a time through Filament. A bulk path is only worth building once there is a source directory to import from.
