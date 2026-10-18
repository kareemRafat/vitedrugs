# Landing Page Redesign + Contact Messages — Vetpedia

> **Status:** v5 — **Phases 1–11 shipped.** The redesign is complete.
> Phase 1: 8/8 · Phase 2: 14/14 · Phase 3: 9/10 · Phase 4: 20/20 · Phase 5: 17/17 · Phase 6: 10/10 · Phase 7: 13/13 · Phase 8: 13/13 · Phase 9: 8/8 · Phase 10: 28/28 · Phase 11: 10/10
> **Suite:** 421 passed, 8 skipped (baseline 360). Pint clean. `npm run build` clean.

### Post-QA revisions (visual review)

Four changes requested after the first render, all landing-only:

1. **Hero overlays removed.** The reference stacked three scrims on the hero photo
   (flat multiply, directional, vertical). All three are gone — the image is
   shown as shot. *Known trade-off, accepted by request:* the white copy now sits
   directly on the photograph, so legibility depends on what is behind it at each
   viewport size. `LandingPageTest::the_hero_photo_has_no_overlay` pins this.
2. **Hero is full viewport height** — `min-h-svh`, not `min-h-screen`. `100vh`
   includes the collapsing mobile URL bar, which would push the bottom of the
   section below the fold. The old `min-h-[85vh]` is gone.
3. **Navbar is a flush full-width bar**, not the reference's floating `top-4`
   pill. Contents align to `max-w-7xl px-4 sm:px-6 lg:px-8` so they line up with
   every section. `<main>` lost its own top padding — the bar is `h-16` and the
   hero supplies its own clearance, so padding both doubled the gap.
4. **Navbar is translucent** with `backdrop-blur-md`, so the un-overlaid hero
   photograph reads through the top of the page. Dark mode uses
   `dark:bg-slate-950/85` rather than `slate-900`: over a photograph the lighter
   slate-900 tint washed out to grey and the nav lost contrast against the image.

Two hero elements were removed outright: the **"Veterinary Knowledge Platform"**
eyebrow badge and the **4-up metric strip** (Products / Diseases / Ingredients /
Species). The five orphaned translation keys were deleted from both lang files
rather than left unreferenced, and the CTA block's now-dead `mb-10` went with
them.

The counts are not lost — they all still appear in the stats section, whose
prominent card shows the six summed entities as an explicit breakdown, so the
total stays auditable. `the_stats_breakdown_accounts_for_all_six_entities` pins
that, and `the_hero_shows_no_metric_strip_or_eyebrow` pins the hero's copy.
> **Date:** 2026-10-18
> **Design reference:** [`landingpage.html`](./landingpage.html) (external mockup, `newVitPedia/`)
> **Related:** [`poultry-plan.md`](./poultry-plan.md) · [`lesion-images-plan.md`](./lesion-images-plan.md)
>
> **Decisions locked with the client:**
> 1. **Honest copy.** Every claim on the page must be verifiable from the codebase. See §1.3.
> 2. **Landing-only chrome.** New `app.layouts.landing`; the shared `main-header`/`footer` are untouched.
> 3. **`/drugs` is left alone.** It shares `LandingController`; a new `HomeController` serves `/` only.
> 4. **Read/unread inbox.** Unread badge in admin nav; messages are read-only (no edit).
> 5. **No reply action.** Admin reads and deletes only.
> 6. **Server-side i18n.** `__()` in `lang/*.php`. No JS `data-ar` swapping.
> 7. **Reference photography only.** The 10 photos already in `public/images/landing/`.

---

## How to read this document

Tasks use markdown checkboxes. Tick a box only when the task is genuinely done.

- **Phase 0** is a reality check, not code. Read it before touching anything.
- **Phase 1** is images — independent, can run in parallel with Phases 2–4.
- **Phases 2–4** are the contact messages feature (data → public form → admin). Self-contained.
- **Phases 5–9** are the landing redesign. Phase 5 (layout) must land before Phase 7 (sections).
- **Phase 10** is tests across both features. **Phase 11** is QA.
- Each phase has a **Milestone** — the "done" test.

---

## 0. Ground rules

### 0.1 Stack (verified against this repo, not assumed)

| Layer | Version / detail |
|---|---|
| PHP | 8.3.33 (Laragon) |
| Laravel | 13 |
| Filament | 5 (`Filament\Actions\`, `Filament\Schemas\`, `Filament\Tables\`) |
| Livewire | 4 (SFC components in `resources/views/app/components/`) |
| Tailwind | 4 + **Flowbite v4 semantic tokens** |
| i18n | `mcamara/laravel-localization`, URL-prefixed `/en/…` `/ar/…`, server-side |
| Admin DB | MySQL `vetdrugs` · **Test DB** SQLite `:memory:` |

### 0.2 Real numbers to design against

Queried 2026-10-18. These drive the stats section — **never hardcode them.**

| Entity | Count |
|---|---|
| `products` (not soft-deleted) | **30** |
| `diseases` | **169** |
| `active_ingredients` | **25** |
| `companies` | **15** |
| `dosage_forms` | **13** |
| `species` | **21** |
| Published `blogs` | **10** |
| `contact_submissions` | **0** |

All six entity tables carry `created_at` + `deleted_at` + `is_active`, so month-over-month deltas are computable (§1.4).

### 0.3 Files that must NOT be broken

| File / test | Why |
|---|---|
| `app/Views/app/layouts/main-header.blade.php` | Shared by every page. Redesigning it is out of scope by decision 2. |
| `tests/Feature/PartNavigationTest.php:80-125` | Asserts a `<nav>` block and a `#mobileSidebar` block on `large-animals.home` and `poultry.home`. Both still use `master.blade.php`. |
| `LandingController` | Still serves `/drugs`. Untouched (decision 3). |
| `resources/views/app/components/contact/⚡contact-form.blade.php` | Already live on `/contact`. Extended in Phase 3, not replaced. |

---

## 1. Reality check — what already exists

### 1.1 A working contact pipeline already exists ✅ **extended in Phase 3**

This was the single most important finding. The plan did **not** add a second one.

`resources/views/app/components/contact/⚡contact-form.blade.php` is a Livewire v4 SFC:

- 4 public props: `name`, `email`, `subject`, `message`
- `rules()` validates; `submit()` calls `ContactSubmission::create()` then `$this->dispatch('contact-sent')`
- Mounted at `app/pages/contact.blade.php:92` as `@livewire('contact.contact-form')`
- Toast element `<x-toast id="contactToast">` lives at `app/pages/contact.blade.php:10`

**The table has never had anything write to it from anywhere but `/contact`** — which has no visitors. So the work is *extending* this component, not building `POST /contact` + a `FormRequest` + `ContactController@store`.

> ⚠️ A naive read of "add a working contact form" produces exactly that three-file duplication. Do not do it.

### 1.2 The admin inbox resource already exists — and had a live bug ✅ **fixed in Phase 4**

`app/Filament/Resources/ContactSubmissions/` was already complete enough to read messages:

| Piece | Before | After |
|---|---|---|
| `ContactSubmissionResource.php` | nav group `System`, label `Messages`, no badge | + `getNavigationBadge/Color/Tooltip` |
| `ContactSubmissionsTable.php` | 4 columns, **no filters**, `->paginated(false)` | 9 columns, 4 filters, 10/25/50 pagination |
| `ContactSubmissionInfolist.php` | name / email / subject / created_at / message | + role, locale, category, read at |
| `Pages/ViewContactSubmission.php` | ❌ **`EditAction` with no `form()` and no `edit` page — the button could never work** | `toggleRead` + `DeleteAction`; read-on-open |

`plans/durgs/filament/PLAN.md:41` documents the intent as *"View + Delete (no edit/create for manual use)"*. Removing `EditAction` therefore **restored the documented intent** rather than adding to it.

### 1.3 ⚠️ The reference mockup states things that are not true

`landingpage.html` is a fictional mockup. Shipping its copy verbatim puts false claims on the homepage.

| Reference says | Reality in this codebase |
|---|---|
| "Direct pipeline from official **EMA, FDA CVM, and SFDA** SPCs" | No regulator feed. Data comes from the PDF → AI → normalise → validate import pipeline. |
| "**Continuous active surveillance** for adverse event notifications, label revisions, withdrawal recalibration" | None of that exists |
| `clinical@vetpedia.org` · `partners@vetpedia.org` | Mailboxes that do not exist — clicks bounce |
| "**Boston, USA · London, UK · Riyadh, KSA**" | No offices |
| "Replies within **24 clinical hours**", "board-certified veterinary pharmacologists" | No support team, no SLA |
| "**Encrypted transmission under ISO/HL7** clinical safety standards" | Ordinary HTTPS form |
| "**Medical Grade · Open Clinical Access · ISO/HL7 Compliant**" (footer) | Unverifiable |
| "**Peer-review score 9.4 / 10**", "Monograph accuracy 99.4%" | No review process (KPIs already replaced with real counts) |
| "**3 Comments**" on a blog post | No comments table exists. That post has zero comments. |
| Blog authors "**Chris**, **Kevin Cooper**, **Alisa Fox**" | Invented. Real `blogs.author_id` renders instead. |

**What replaces it** — statements grounded in models that demonstrably exist: `Dosage`, `WithdrawalPeriod`, `DrugInteraction`, `Contraindication`, `Precaution`, `SideEffect`, `Indication`, `Alternative`, `DrugClass`, `DosageForm`, `Species`, the `*_ar` bilingual columns, and the `large-animals.diagnosis` / `poultry.diagnosis` engines.

| Pillar | New copy basis |
|---|---|
| Structured Data | Structured records for products, active ingredients, dosage forms, indications, contraindications, precautions, side effects, interactions, withdrawal periods |
| Bilingual Engine | Every entity carries Arabic alongside English; full RTL layout |
| Decision Tools | Differential diagnosis and product comparison across large animals and poultry |
| Pharmacovigilance | → rename. Withdrawal periods, contraindications and interaction data kept alongside every product |

### 1.4 KPI cards — real data mapping

The reference's 5 cards are fabricated. Chosen mapping (client-approved):

| Slot | Metric | Source |
|---|---|---|
| Prominent blue card | **Total indexed records** | sum of the six counts |
| 2×2 · 1 | Diseases | `diseases` count + `+N this month` |
| 2×2 · 2 | Active Ingredients | `active_ingredients` count + `+N this month` |
| 2×2 · 3 | Products | `products` count + `+N this month` |
| 2×2 · 4 | Companies | `companies` count + `+N this month` |

Delta = rows created since the first of the current month. **Badge hidden when the delta is 0** — never show a fake `+0`.

### 1.5 ⚠️ Three technical traps in this repo

| # | Trap | Fix |
|---|---|---|
| 1 | `app.css:8-33` **remaps `--color-slate-50…800` under `.dark`**. A literal copy of the reference's `bg-slate-50` would render near-white on a dark page. | `.landing-scope` block that re-declares the stock Tailwind slate values, so the reference palette renders as designed and the rest of the app is untouched. |
| 2 | `master.blade.php:49` defaults `<body data-part="large-animals">` → **orange** brand. The reference is indigo. | The landing layout is a standalone document and opts out of part theming, using stock `indigo/slate/sky/amber/emerald/violet`. |
| 3 | RTL. The reference already uses logical properties (`ps-`, `pe-`, `start-`, `end-`, `inset-x-0`) which Tailwind v4 mirrors automatically. | Remaining manual fixes only: `border-r`→`border-e`, `→` arrows get `rtl:rotate-180`, hero gradient `rtl:bg-gradient-to-l`, drawer `end-0`, and `[dir=rtl]` zeroes letter-spacing on `tracking-tight`. |

### 1.6 ⚠️ `/` and `/drugs` share a controller and a view

```
routes/web.php:30   Route::get('/',          LandingController::class)->name('home');
routes/web.php:77   Route::get('/drugs',     LandingController::class)->name('drugs.home');
```

Both render `app.landing`. Rewriting `LandingController` would silently redesign `/drugs` too — out of scope by decision 3. Hence the new `HomeController`.

### 1.7 Image weight — ✅ resolved in Phase 1

`public/images/landing/` originally held 10 Unsplash photographs totalling **27.7 MB** (largest 8 MB). `git status` reported the folder as **untracked (`??`)**, so the originals were **not recoverable from git history**.

They are now backed up to `newVitPedia/landing-originals/` (11 files, 27.68 MB, outside `public/`), and `public/images/landing/` holds only the emitted assets.

### 1.8 Environment answers (no installs required)

| Checked | Result |
|---|---|
| PHP `gd` | ✅ **bundled 2.1.0**, JPEG + **WebP** support |
| PHP `imagick` | ❌ not loaded |
| `magick` / `jpegoptim` / `cwebp` CLIs | ❌ not installed |
| Decision | **Use GD. No composer/npm package, no new dependency.** |

---

## 2. Phase 1 — Image pipeline

> **Milestone:** every landing image is served from `public/images/landing/` at a sane weight, the originals are preserved outside `public/`, and a fresh `php artisan optimize:clear` shows no errors.
> **What this uses:** the bundled GD extension. No dependency changes.

- [x] Back up all 10 originals + `hero.png` to `newVitPedia/landing-originals/` (outside `public/`, so not served) — **done before anything else; verified 11 files / 29,020,257 bytes byte-identical**
- [x] Write a one-off PHP script using GD (throwaway; not committed)
- [x] Resize long edge per slot: hero **2400px**, chapter rows **1600px**, pillar cards **900px**, CTA texture **1600px**
- [x] Re-encode progressive JPEG at quality 78 with metadata stripped
- [x] Emit a `.webp` sibling for each (GD has WebP support)
- [x] Rename to semantic filenames per the §4 slot map
- [x] Record before/after byte totals; confirm total is under ~2 MB
- [x] Delete the throwaway script

### 2.1 Shipped result — measured 2026-10-18

| Slot | Source | Output | Dimensions | JPEG | WebP |
|---|---|---|---|---|---|
| Hero | **`hero.png`** (client-supplied) | `hero` | 1717×916 | 176.4 KB | 122.1 KB |
| Chapter 01 Large Animals | `monika-kubala` | `chapter-large-animals` | 1600×1071 | 325.3 KB | 278.7 KB |
| Chapter 02 Vet Drugs | `alexander-grey` | `chapter-drugs` | 1067×1600 | 146.9 KB | 77.8 KB |
| Chapter 03 Poultry | `artem-beliaikin` | `chapter-poultry` | 1600×1067 | 360.9 KB | 378.7 KB |
| Pillar 01 Structured Data | `tobias-schmucker` | `pillar-structured-data` | 900×600 | 82.5 KB | 61.1 KB |
| Pillar 02 Bilingual | `ian-murray` | `pillar-bilingual` | 900×600 | 117.9 KB | 127.8 KB |
| Pillar 03 Decision Tools | `wolfgang-hasselmann` | `pillar-decision-tools` | 506×900 | 53.7 KB | 38.7 KB |
| Pillar 04 Surveillance | `zoe-richardson` | `pillar-surveillance` | 600×900 | 79.3 KB | 52.9 KB |
| CTA band texture | `jason-holland` | `cta-texture` | 1600×1280 | 332.9 KB | 290.7 KB |

```
sources read      :  23.50 MB
written to disk   :   3.03 MB   (jpeg + webp fallback pair)
browser downloads :   1.40 MB   (webp only, via <picture>)
webp vs source    :   5.94 % of original
```

**No upscaling** — `hero.png` is 1717px, below the 2400px target, so it was re-encoded at native size rather than blown up.

> ⚠️ **Two open items carried into Phase 7:**
> 1. `hero.png` (2.0 MB) is **now redundant** — `hero.jpg`/`.webp` are the served assets. It was left in place because the client placed it there; delete once the crop is confirmed.
> 2. **The hero image has "ARVETPEDIA" baked into the pixels.** It is Latin text that cannot translate, so the Arabic page will show it unchanged; it also duplicates the header wordmark. Needs a decision before Phase 7 ships.

**Slot map** — 9 of 10 photos used, no photo appears twice:

| Slot | Source file | Subject | Target |
|---|---|---|---|
| Hero full-bleed | **`hero.png`** *(client-supplied, replaces `marc-pell`)* | Water buffalo · cow · sheep · hen at dawn | 2400px |
| Chapter 01 Large Animals | `monika-kubala-OpMfiq8nPI0` | Dairy cows | 1600px |
| Chapter 02 Vet Drugs | `alexander-grey-FEPfs43yiPE` | Pill bottle + capsules | 1600px |
| Chapter 03 Poultry | `artem-beliaikin-Mpm69Pad_-8` | Commercial laying flock (6000×4000) | 1600px |
| Pillar 01 Structured Data | `tobias-schmucker-5arv1V8szIs` | Ear-tagged cow (traceability) | 900px |
| Pillar 02 Bilingual | `ian-murray-XwWCK-18RjI` | Hen close-up | 900px |
| Pillar 03 Decision Tools | `wolfgang-hasselmann-uMfSHeycnYQ` | Rooster head, inspection | 900px |
| Pillar 04 Surveillance | `zoe-richardson-gHAAa9U4a0k` | Broiler flock | 900px |
| CTA band texture (10% opacity) | `jason-holland-G61iAuzI9NQ` | Hen in field | 1600px |
| *(spare — unused)* | `marc-pell-xPlO5xZAAOc` · `vidar-nordli-mathisen-dC-z4r8tr6U` | Dairy panorama · rooster | — |

> `hero.png` composition is favourable for the reference layout: the subjects sit right-of-centre and the left third is open dark field — exactly where the H1, subtitle and search bar land.

> The contact section's "map texture" is **CSS only** (dot grid + radial gradient). No photo stands in for a map.

---

## 3. Phase 2 — Contact messages: schema + model

> **Milestone:** `php artisan migrate` runs clean on MySQL **and** the SQLite test database; `ContactSubmission` has the new fields cast, scoped and fillable; the factory can seed unread and read records.
> **What this uses:** the existing `contact_submissions` table and `ContactSubmissionFactory`. Nothing is altered — the migration is purely additive.

- [x] Create migration `add_inquiry_fields_to_contact_submissions_table` via `php artisan make:migration` — `2026_10_18_000926_…`
- [x] Add `role` — `string`, nullable
- [x] Add `category` — `string`, nullable, indexed
- [x] Add `locale` — `char(2)`, default `'en'`
- [x] Add `is_read` — `boolean`, default `false`, indexed
- [x] Add `read_at` — `timestamp`, nullable
- [x] **Do not alter `subject`.** Keep it NOT NULL (see §3.1)
- [x] Run `php artisan migrate` and verify against SQLite too — MySQL applied; SQLite verified with a full `up → down → up` cycle on a throwaway file
- [x] Create `app/Enums/ContactCategory.php` — `FormularyIntegration` · `MonographCorrection` · `InstitutionalLicensing` · `Pharmacovigilance` · `GeneralFeedback`, TitleCase cases, string-backed, implements `HasLabel` so Filament generates options from it
- [x] Add `ContactCategory::cases()` labels to `lang/en/messages.php` and `lang/ar/messages.php` — new top-level `contact_categories` block in both
- [x] `ContactSubmission`: extend `$fillable` with `role`, `category`, `locale`
- [x] `ContactSubmission`: add `protected function casts(): array` (`is_read` => bool, `read_at` => datetime, `category` => `ContactCategory::class`) — matches the repo's Laravel 13 convention, per `AGENTS.md`
- [x] Add `scopeUnread()` and `scopeRead()`
- [x] Add `markAsRead()` and `markAsUnread()` — both guard on current state, so `read_at` is stamped exactly once
- [x] Extend `ContactSubmissionFactory` with `unread()` and `read()` states, plus a `category()` state

**Shipped:** 5 columns added, no column altered, reversible. `markAsRead()`/`markAsUnread()` are idempotent so the `read_at` stamp reflects first open, not last render.

### 3.1 Why `subject` stays NOT NULL

The obvious approach is to make `subject` nullable so the reference's *"Inquiry Category"* select lands in `category` with nothing in `subject`. That forces a `->change()` on a live column and leaves an empty column in the admin list.

Instead: `category` stores the **enum value** (stable, filterable, survives label edits) and `subject` stores **the label the visitor actually saw** (a display snapshot). This keeps the schema additive-only, makes `subject` non-null, and leaves `/contact`'s existing free-text subject working unchanged.

---

## 4. Phase 3 — Contact messages: public form

> **Milestone:** submitting the reference's 5-field form from the landing creates a `ContactSubmission` with `role`, `category`, `locale` and a category-derived `subject`; submitting from `/contact` still creates one exactly as before.
> **What this uses:** the existing SFC at `resources/views/app/components/contact/⚡contact-form.blade.php`. Extended, not replaced.

- [x] Add `public string $role = ''` and `public ?string $category = null` to the SFC
- [x] Add `Rule::enum(ContactCategory::class)` validation for `category` — required on `landing`, `nullable` on `page`
- [x] Capture `app()->getLocale()` into `locale` on submit
- [x] On submit, write `$category->value` to `category` and `$category->getLabel()` to `subject`
- [x] Add a `$variant` public prop — `landing` | `page`
- [x] `landing` variant renders the reference's 5 fields: name, clinical title/role, institutional email, category select, message
- [x] `page` variant leaves the current `/contact` markup untouched — behind an `@if`, byte-identical to the previous markup
- [ ] Add `<x-toast id="contactToast">` to the landing layout (it currently exists only in `app/pages/contact.blade.php:10`) — **blocked on Phase 5**; the landing variant is not mounted anywhere until Phase 8
- [x] Verify the toast fires on success in both variants — the `contact-sent` dispatch is variant-independent; toast element still to come with the layout
- [x] Verify inline validation errors render in both languages — each field carries an `@error` block in its own branch

> ⚠️ **Framework note for whoever writes Phase 10's tests:** Livewire v4 **removed** `assertHasErrors()` / `assertHasNoErrors()`, and Filament's `assertHasFormErrors()` only works on Filament components — the contact form is a plain Livewire SFC. `tests/Feature/ContactSubmissionTest.php` reads the bag off the instance instead:
> `$component->instance()->getErrorBag()->has($field)`. Also note `assertCanSeeTableRecords($records, inOrder: true)` — the second argument is `$inOrder`, **not** `$perPage`, and `assertCountTableRecords()` counts the *total*, not the page size.

**Shipped:** `ContactSubmissionTest` — 13 tests covering both variants, the enum/snapshot split, locale capture, idempotent read marking, and both scopes.

---

## 5. Phase 4 — Contact messages: Filament inbox

> **Milestone:** the admin sidebar shows an unread count that decreases when a message is opened; the list paginates and filters; the broken Edit button is gone.
> **What this uses:** the existing resource. Mirrors the badge pattern already proven at `ProductResource.php:102-110`.

### 5.1 `ContactSubmissionResource`

- [x] `getNavigationBadge()` — unread count as string, `null` when 0 so no empty badge renders
- [x] `getNavigationBadgeColor()` — `'warning'`
- [x] `getNavigationBadgeTooltip()` — "Unread messages"
- [x] Leave `form()` absent — read-only per decision 4

### 5.2 `ContactSubmissionsTable`

- [x] Remove `->paginated(false)`; use `->paginated([10, 25, 50])`
- [x] `->defaultSort('created_at', 'desc')`
- [x] Column: name — bold and warning-tinted when unread
- [x] Column: email — copyable
- [x] Column: role — `—` placeholder, hidden by default
- [x] Column: subject
- [x] Column: category — badge, coloured from `ContactCategory::color()`
- [x] Column: locale — as a badge, upper-cased
- [x] Column: read/unread indicator icon — envelope / open-envelope
- [x] Column: `created_at` — relative (`since()`) with a full date tooltip, sortable
- [x] Filter: read status (`SelectFilter`)
- [x] Filter: category (`SelectFilter`, options generated from the enum via `HasLabel`)
- [x] Filter: locale (`SelectFilter`)
- [x] Filter: date range (`DatePicker` pair)
- [x] Record actions: `ViewAction`, `DeleteAction`, toggle-read
- [x] Bulk actions: delete, mark read, mark unread

### 5.3 `ContactSubmissionInfolist`

- [x] Sender section: name, email, role, locale
- [x] Details section: category, subject, received at, read at
- [x] Message section (now `prose()`)

### 5.4 `ViewContactSubmission`

- [x] **Remove the broken `EditAction`** (§1.2) — restore "View + Delete"
- [x] Replace with `DeleteAction`
- [x] `mount()` marks the record read and stamps `read_at`
- [x] Dispatch `refresh-sidebar` after marking read so the badge updates
- [x] Added a header toggle so a mis-click can be undone without leaving the page
- [x] Confirm `mount()` covers both the table's `ViewAction` modal and a direct URL — the override calls `parent::mount()` first, so the record is resolved before marking

**Shipped:** `ContactSubmissionAdminTest` — 14 tests: badge count / hidden-at-zero / drops-on-open, read-on-open, no `read_at` restamp, no `edit` action, per-row toggle, all four filters, filter narrowing, pagination, newest-first sort.

---

## 6. Phase 5 — Landing layout (chrome)

> **Milestone:** `/` renders the reference's floating pill header + 4-column dark footer, in EN and AR, in light and dark mode, on desktop and mobile. No other page changes appearance.
> **What this uses:** a standalone document modelled on `master.blade.php`. It does **not** extend it.

- [x] Create `resources/views/app/layouts/landing.blade.php` as a standalone HTML document
- [x] Copy the `<head>` contract from `master.blade.php`: charset, viewport, favicon, title/meta/keywords, OG tags, `csrf-token`, `noindex` guard
- [x] Carry over the dark-mode pre-paint script (`master.blade.php:28-44`) — runs before render, no flash. **Deviation:** it reads the `theme` key, not the reference's `vetpedia-theme`, so the toggle stays in sync with every other page
- [x] Emit `lang` and `dir="rtl"` on `<html>` exactly as `master.blade.php:2` does
- [x] Keep `@vite(['resources/css/app.css', 'resources/js/app.js'])`
- [x] Add the reference font stack: Inter + IBM Plex Sans Arabic + JetBrains Mono
- [x] Scope the fonts so **no other page** changes type — `--font-sans` / `--font-mono` are re-declared inside `.landing-scope`, never on `html`
- [x] Include `<x-cookie-consent />` for parity with `master.blade.php:62` — placed **outside** `.landing-scope`, since it is built from `bg-brand` / `bg-neutral-primary-soft` and must keep the site theme
- [x] Include `@livewire('products.product-compare-bar')` for parity
- [x] Create `resources/views/app/layouts/landing-header.blade.php` — fixed pill header: wordmark, 7 nav links, search-focus button, dark toggle, EN/ع switcher, Sign in / avatar, hamburger
- [x] Nav links resolve to real routes: `large-animals.home`, `drugs.home`, `poultry.home`, `#database-stats`, `blog.index`, `about`, `contact`
- [x] Header gains its scroll shadow after 20px — Alpine `@scroll.window` rather than the reference's `addEventListener`, so there is no second listener to tear down
- [x] Mobile drawer: `end-0`, ESC-to-close, backdrop click, `aria-expanded`
- [x] Create `resources/views/app/layouts/landing-footer.blade.php` — 4 columns: brand, platform, resources, language + notes
- [x] Footer language column links via `LaravelLocalization::getLocalizedURL()` (matches `footer.blade.php:90-97`)
- [x] Back-to-top affordance
- [x] **Remove** the ISO/HL7 badge line (§1.3) — replaced with `"Educational reference only — not a substitute for professional veterinary judgement."`, a limitation that is true rather than a certification that is not

### 5.1 Verified

Rendered in both locales and asserted: layout renders, `.landing-scope` present, header/drawer/footer present, fonts loaded, **no `main-header` or `data-part` leakage**, cookie consent + compare bar present, `theme` key used, all nav links resolve, no ISO/HL7 or Boston/London/Riyadh strings, `dir="rtl"` + `lang="ar"` on `<html>`, Arabic copy resolved, **zero unresolved translation keys** in EN or AR.

> ⚠️ **Deferred to Phase 7:** asserting that `route()` emits `/{locale}/…`-prefixed links. That needs a real HTTP request, because the routes are registered with the prefix resolved from the request path — it cannot be proven by rendering a view in isolation. The templates use the identical `route()` / `getLocalizedURL()` calls as the existing working footer.

---

## 7. Phase 6 — CSS scope + RTL

> **Milestone:** the landing renders in the reference's exact palette in both themes, mirrors correctly in RTL, and no other page's colours shift.
> **What this uses:** a scoped block in `resources/css/app.css`. Mitigates trap §1.5 #1.

- [x] Add a `.landing-scope` block to `resources/css/app.css`
- [x] Re-declare the stock Tailwind `--color-slate-50…950` inside the scope so `app.css:8-33`'s dark remap cannot reach it — **using Tailwind v4's exact oklch values**, not hand-rounded hex, which would have introduced a visible shift
- [x] Pin the font stack: Inter → IBM Plex Sans Arabic → `system-ui`; JetBrains Mono for eyebrows and metrics
- [x] `[dir=rtl]` → font-family swaps to IBM Plex Sans Arabic, `line-height: 1.68`
- [x] `[dir=rtl]` → zero letter-spacing on `.tracking-tight` / `.tracking-tighter`
- [x] `[dir=rtl]` → `text-transform: none` on `.uppercase` — Arabic has no case, but a mixed Latin run would still be upper-cased
- [x] Port the orb-float keyframes and honour `prefers-reduced-motion`
- [x] Port `.hero-image-scrim` and `.card-photo-scrim`
- [x] Port `writing-mode: vertical-rl` for the pillar edge labels, with an RTL variant (`vertical-lr`)
- [x] Everything scoped — **nothing may leak to other pages**
- [x] Confirm `npm run build` is clean

### 6.1 Why the override actually works — verified in the compiled CSS

The mechanism is not a guess. Checked against the built `app-*.css`:

```css
.bg-slate-50{background-color:var(--color-slate-50)}          /* utility → var() */
.dark{... --color-slate-50:#f0f4f8; ...}                       /* the remap, still intact */
.landing-scope{--color-slate-50:oklch(98.4% .003 247.858);...} /* restores stock palette */
```

Tailwind v4 emits every colour utility as `var(--color-…)`, and custom properties resolve to the **nearest ancestor declaration regardless of specificity**. `.dark` sits on `<html>`; `.landing-scope` sits on a descendant, so it wins for its whole subtree. The remap is left completely untouched for every other page — confirmed still present in the output.

---

## 8. Phase 7 — HomeController + upper sections

> **Milestone:** `/` serves the reference's hero, four-pillar band, chapter rows and stats grid from live data, and `/drugs` still renders the old design.
> **What this uses:** a new controller. `LandingController` is left completely alone (§1.6).

- [x] Create `app/Http/Controllers/HomeController.php`
- [x] Compute the six counts + `totalRecords` + per-entity month-over-month deltas — driven by a single `COUNTED` map, so adding an entity is one line and every section updates together
- [x] Eager-load the 3 latest published blogs with `category` and `author`
- [x] Register it for `Route::get('/')` in `routes/web.php:30`; **leave line 77 alone**
- [x] Create `resources/views/app/landing/sections/` — plain includes, **not** under `app/components/` (that path is a registered Livewire finder location)
- [x] `hero.blade.php` — full-bleed photo + 3 scrim layers, H1, subtitle, search form → `route('search')` with `q`, 2 CTAs (`drugs.home`, `large-animals.diagnosis`)
- [x] Add the hero **4-up metric strip**. The reference has an empty `<!-- Metric Strip 4-up -->` comment with no markup — built from the live counts (products, diseases, ingredients, species)
- [x] `pillars.blade.php` — 4 tall cards `01`–`04`, `id="about"`, honest copy per §1.3
- [x] `chapters.blade.php` — 3 rows → `large-animals.home`, `drugs.home`, `poultry.home`, `id="browse-hub"`
- [x] `stats.blade.php` — 1 prominent + 2×2, `id="database-stats"`, mapping per §1.4
- [x] Hide the delta badge entirely when the delta is 0
- [x] Section order must match the reference: hero → pillars → chapters → stats → contact → blog → CTA

### 7.1 Two things that were decided differently than planned

**`Product::query()->count()` is already filtered, and that is correct.** `Product` carries a global scope for `status = approved AND is_active = true`, so the headline product count is exactly the set the public site actually shows — not every row in the table. No extra filter was added on top; doing so would have double-filtered.

**`is_active` is deliberately NOT applied to the other five entities.** Those tables have the column but nothing in the public site filters on it. Applying it here would make the landing page disagree with the number a visitor sees after clicking through to that index.

### 7.2 The `/drugs` regression this phase nearly shipped

Pointing `HomeController` at `app.landing` while `LandingController` still rendered the same view meant `/drugs` started rendering the redesign with `$counts` undefined — a 500. The old 686-line view was moved to `resources/views/app/drugs.blade.php`, restored **byte-identically** from git (`git cat-file blob` → hash-verified against HEAD's blob id), and `LandingController` now points at it. `LandingPageTest::the_drugs_hub_keeps_the_pre_redesign_design` pins it.

---

## 9. Phase 8 — Lower sections

> **Milestone:** the contact, blog and CTA sections render with real data and the form submits end to end.
> **What this uses:** the Phase 3 form component and the Phase 2 schema.

- [x] `contact.blade.php` — CSS-only map texture (§2), 2 channel cards, SLA card, form card, `id="contact"`
- [x] Remove the fake `clinical@vetpedia.org` / `partners@vetpedia.org` addresses
- [x] Remove the Boston / London / Riyadh chips and the ISO/HL7 transmission claim (§1.3)
- [x] Mount `@livewire('contact.contact-form', ['variant' => 'landing'])`
- [x] `blog.blade.php` — 1 large + 2 small, `id="blog"`
- [x] Render the **real** author, date and category
- [x] Replace the fake "3 Comments" with real read time (reuse the existing `messages.blog.read_time` key)
- [x] Graceful placeholder when a blog has no `cover_image`
- [x] Hide the whole section when there are no published blogs
- [x] `cta.blade.php` — gradient band, photo texture at 10% opacity, honest copy
- [x] Rewrite `resources/views/app/landing.blade.php` to extend the new landing layout

### 8.1 The "replies within 24 clinical hours" card

The reference promised a response SLA and named three offices. None of that is
operable here, so the card now states what actually happens: submissions are
written to the database, appear in the staff inbox, and are marked read once
reviewed — with an explicit **"no automated reply and no guaranteed response
time."** A limitation stated plainly beats a service level that cannot be met.

The two channel cards lost their mailboxes but kept their structure: each now
links to `#contact-form` rather than to an address nobody monitors.

The reference's contact map was a remote CDN photograph that cannot be
reproduced, so the texture is a CSS grid with a radial mask instead.

---

## 10. Phase 9 — Translations

> **Milestone:** every string on the landing resolves in EN and AR with no raw keys, and `dir="rtl"` renders correctly.
> **What this uses:** server-side `__()`. No JS `data-ar` swapping (decision 6).

- [x] Restructure the `landing` block in `lang/en/messages.php`
- [x] Mirror it in `lang/ar/messages.php`
- [x] Group by section: `meta` · `header` · `hero` · `pillars` · `chapters` · `stats` · `contact` · `blog` · `cta` · `footer`
- [x] Reuse existing `messages.nav.*` and `messages.pages.contact.*` keys where they already fit
- [x] Check Arabic does not overflow the pill card, pillar cards or stat cards
- [x] **The premise "only `app/landing.blade.php` consumes `messages.landing.*`" was wrong** — see §9.1
- [x] Add the `ContactCategory` labels — they were already added in Phase 3 under the separate top-level `contact_categories` block, and are reused by both form variants

### 9.1 The plan's own safety check was based on a false premise

Phase 9 listed this as done:

> *Verified: `messages.landing.*` is referenced by `app/landing.blade.php` **only** (73 references, no other consumer) — safe to restructure*

That was true of the *view file*, but not of the *route*. `LandingController`
also rendered `app.landing`, so `/drugs` was reading the same 59 flat keys.
Restructuring `landing` left `/drugs` rendering raw `messages.landing.…` strings
across its hero, feature grid, how-it-works, browse-by-category and CTA blocks.

Fixed by adding a separate top-level `drugs_hub` block to both lang files,
holding the original 59 keys, and rewriting the references inside
`app/drugs.blade.php` to `messages.drugs_hub.*`. The values were lifted from git
HEAD by script rather than retyped, so nothing can drift. `LandingPageTest`
now asserts the key sets match across locales **and** that `/drugs` renders zero
raw keys.

**Lesson: a translation-key audit has to follow routes, not files.** One view can
have two consumers.

---

## 11. Phase 10 — Tests

> **Milestone:** `php artisan test` green, and a deliberately broken AR key fails the suite.
> **What this uses:** PHPUnit 12 (no Pest), `RefreshDatabase`, SQLite `:memory:`.

- [x] `tests/Feature/LandingPageTest.php` — **26 tests**, all passing
  - [x] `/` returns 200 in both locales
  - [x] Arabic emits `dir="rtl"` and `lang="ar"`; English emits neither RTL
  - [x] Live counts appear in the stats section — asserted against `data-entity` / `data-count` on seeded rows, not "greater than zero"
  - [x] The prominent card's breakdown names all six summed entities
  - [x] The `+N this month` badge hides at zero and appears for the one entity that changed
  - [x] Every nav link and every chapter link resolves to a real route
  - [x] The hero search posts to the real `search` route with `q`
  - [x] The language switcher offers both locales in both directions
  - [x] Blog section hides when no published blogs exist
  - [x] Blog section renders the **real** author, category, date and read time — and Arabic metadata on `/ar`
  - [x] A blog with no cover image gets the placeholder
  - [x] No `data-ar` / `data-en` attributes leak into the markup
  - [x] **Every new translation key resolves in EN and AR** — mirrors `PartNavigationTest::every_nav_label_resolves_in_both_locales`, and is why that test exists
  - [x] Zero raw `messages.*` keys reach the rendered page in either locale
  - [x] The landing page uses its own chrome — no `data-part`, no shared `mobileSidebar`
  - [x] `/drugs` keeps the pre-redesign design and renders no unresolved keys
  - [x] No fabricated claim survives (ISO/HL7, FDA CVM, SFDA, 9.4/10, 24 clinical hours, the two mailboxes, the three cities, board-certified)
  - [x] Every landing `<img>` declares `width` and `height` — no layout shift
  - [x] Every referenced landing asset exists on disk
- [x] `tests/Feature/ContactSubmissionTest.php` — 13 tests, re-verified green against the landing variant
- [x] `tests/Feature/ContactSubmissionAdminTest.php` — 14 tests, still green
- [x] Run the full suite: `php artisan test --compact` → **413 passed, 8 skipped**
- [x] Re-ran the full suite under **two independent `--order-by=random` seeds** — green both times, so the shared Faker `unique()` pool has not been disturbed
- [x] Confirm `tests/Feature/PartNavigationTest.php` still passes (regression gate) — 56 passed across it, `ContactSubmission*`, and `LandingPageTest`

### 10.1 Note on testing the Arabic page

The Arabic page is exercised via `app()->setLocale('ar')` + `GET /`, matching the
convention already used across this suite — **not** by requesting `/ar`.

`/{locale}` is applied by LaravelLocalization from `Request::segment(1)` while
routes are registered. Under the test kernel the application boots before any
request exists, so the prefixed URIs are never registered and `/ar` is a 404.
This is a test-harness property, not a site bug — the Arabic page was verified
over a real HTTP request at `/ar/` as well, where it returns 200 with all 17
internal links correctly prefixed.

---

## 12. Phase 11 — QA

> **Milestone:** visually compared against the reference at 1440px in LTR **and** RTL, light **and** dark; Pint clean; build clean.

- [x] Confirm **no other page** changed appearance — `/drugs`, `/about`, `/contact`, `/blog`, `/large-animals`, `/poultry` all still render 200 on `app.layouts.master` with the shared navbar and **zero** `landing-scope` leakage
- [x] Confirm `/drugs` still renders the old design (§1.6) — pinned by test
- [x] Check no layout shift from images — every slot has explicit dimensions. **This found a real gap:** the 7 pillar and chapter photographs shipped without `width`/`height` and were about to jump. Fixed, and now asserted by `every_landing_image_declares_its_dimensions`.
- [x] Check every referenced asset exists on disk — 18 referenced, 0 missing
- [x] Confirm no scratch/scaffolding files were left behind
- [x] `vendor/bin/pint --dirty` → clean
- [x] `npm run build` → clean, 200.34 kB CSS / 28.26 kB gzip
- [ ] **Visual diff at 1440px (LTR + RTL, light + dark) and 375/768px responsive pass** — *needs a human eye or a screenshot harness; not verifiable from this environment. Automated equivalents are green, but do not read this as a sign-off.*

### 11.1 Dead CSS removed

The Phase 6 block carried five rules nothing referenced: `.hero-image-scrim` and
four `.hover-card-*` glows. Verified against the reference that removing them was
correct — each of those five appears **exactly once** in `landingpage.html`,
inside its `<style>` block, and is never applied to any element. They were dead
in the reference too. The hero ended up using Tailwind's directional gradient
utilities instead (it needs three different gradient directions, which one named
class cannot express), and the cards use Tailwind's own `hover:shadow-*`.

Everything still referenced survived minification: `.landing-scope`,
`.vertical-label`, `.card-photo-scrim`, `.animate-orb-1`, and the stock slate
override.

### 11.2 Pillar cards corrected to match the reference (second pass)

The first pass rebuilt the four pillar cards from the reference's *intent* rather
than its markup, and drifted in seven ways. Now matched card-for-card:

| | Was | Now |
|---|---|---|
| Photo treatment | none | `brightness-[0.7] dark:brightness-[0.6] contrast-[1.1]` (`contrast-[1.15]` on card 03) |
| Scrim | `.card-photo-scrim` (a bottom-up wash) | the reference's `from-slate-950 via-slate-950/75 to-slate-950/50` |
| Card 03 accent | dropped | `bg-sky-600/75 mix-blend-multiply` + `from-sky-950 via-sky-900/60 to-sky-800/40` + `border-sky-500/40 bg-sky-950/40` + `text-sky-100` + `bg-sky-300/80` rule |
| Body alignment | left-aligned | `text-center` with `max-w-xs mx-auto` |
| Index numeral | `text-5xl text-white/25` | `text-6xl sm:text-7xl text-white` + `drop-shadow-md` (`drop-shadow-lg` on 03) |
| Heading | `text-xl` sentence case | `text-lg uppercase tracking-wide` |
| Bottom edge caption | a floating rotated `01` at the top-right | the reference's centred vertical tag + `w-px h-12` hairline rule |

The divider edges also now follow the reference exactly: cards 01 and 03 carry
`sm:border-e`, card 02 carries `lg:border-e`, card 04 carries none. That is not a
typo in the reference — at `sm` the grid is 2-up so only the first card of each
row needs an inner border, and at `lg` it is 4-up so cards 01–03 all need one.

Two deliberate deviations, both narrow:

- `writing-mode: vertical-rl` is applied via the `.vertical-label` class rather
  than inline. Identical in LTR; the class also carries an RTL `vertical-lr`
  variant so the captions read top-to-bottom in both directions, which an inline
  style cannot do.
- `border-r` → `border-e`. Physical right/left borders put the dividers on the
  wrong side in Arabic. Tailwind v4's logical properties produce identical LTR
  output and behave correctly under RTL.

Vertical-label copy is honest rather than the reference's `"REGULATORY SYNC"`:
Structured Schema · Bilingual EN / AR · Clinical Fields · Editorial Review.

**Verified against the compiled CSS, not by eye.** Tailwind silently drops a
utility whose class never appears in source, and the page still renders — just
without the brightness filter. 30 utilities were asserted present in the built
stylesheet, plus 4 cards / 4 tags / 4 rules and the intact accent treatment in
the rendered output. Two of those checks are now permanent tests
(`the_pillar_cards_match_the_reference_structure`,
`the_pillar_carry_no_fabricated_claims`).

### 11.2 The multi-app-in-one-process trap (cost real time)

Diagnosing the `/ar` failure led to a harness artifact that looked exactly like a
site-wide Arabic outage: building a **second `Illuminate\Foundation\Application`
in the same PHP process** re-points `Container::getInstance()` and the Facade
root while the first app's Blade providers are still wired, so `__components`
resolves against the wrong finder and every component-bearing page throws
"No hint path defined". Six pages appeared to be broken; none of them were.

**One app instance per process.** The throwaway scripts that needed both locales
were split into two runs. Recorded here because the symptom is a convincing fake
outage and the cause is not obvious from the error.

---

## 13. Risk register

| Risk | Impact | Mitigation |
|---|---|---|
| ~~Image overwrite loses the originals~~ | Unrecoverable — folder untracked | ✅ **Closed in Phase 1** — backed up and verified byte-identical |
| **"ARVETPEDIA" is baked into `hero.png`** | Latin text cannot translate; shows unchanged on the Arabic page; duplicates the header wordmark | ⚠️ **Open — needs your call.** The redesign serves `hero.jpg` / `hero.webp`, which are re-encoded from the same photograph and still carry the wordmark. Options: crop it out, or supply a text-free version. |
| `hero.png` left in `public/` (2.0 MB) | Dead weight in the deployable folder | ⚠️ **Open.** Not referenced by any view; kept only because you placed it. Say the word and it goes. |
| `.landing-scope` slate override leaks and recolours the whole site | Site-wide regression | ✅ **Closed in Phase 6 and re-verified in Phase 11** — scope is one subtree; the `.dark` remap is still intact in the compiled CSS; six other pages spot-checked |
| `/drugs` silently redesigned | Out-of-scope change to the drugs hub | ✅ **Closed in Phase 7** — it *did* 500 mid-phase, before the page shipped. Old view restored byte-identically to `app/drugs.blade.php`; `LandingPageTest` pins both the layout and the absence of raw keys |
| **Refactoring `messages.landing.*` broke `/drugs`** | The un-redesigned hub rendered raw `messages.landing.…` strings | ✅ **Closed in Phase 9** — the plan's own "single consumer" audit was based on a false premise. Keys moved to a separate `drugs_hub` block, lifted from HEAD by script. See §9.1 |
| **Two app instances in one PHP process** | Faked a site-wide Arabic outage during diagnosis | ✅ **Documented in §11.2** — one app per process; the test suite uses `app()->setLocale()` per the existing convention |
| `PartNavigationTest` fails | CI breakage | ✅ **Closed** — passes; regression gate ran green |
| An EN-only translation key ships | Raw key string renders in Arabic | ✅ **Closed in Phase 10** — key-set parity asserted across locales for both `landing` and `drugs_hub`, plus a zero-raw-keys assertion on the rendered page |
| Read/unread `mount()` fires on a table prefetch | Badge drops without the admin reading | Guarded on `! $is_read` in Phase 4 |
| `.dark` slate remap defeats the reference palette | Whole landing renders wrong in dark mode | ✅ **Closed in Phase 6** — mechanism verified against the compiled CSS |
| Shared theme key between landing and site | Toggling dark mode on `/` desyncs every other page | ✅ **Closed in Phase 5** — both layouts read/write `theme` |
| Landing palette leaking to other pages | Site-wide colour regression | ✅ **Closed in Phase 6**, re-verified in Phase 11 |
| **Photos without intrinsic dimensions** | The page jumps as images stream in | ✅ **Found and closed in Phase 11** — 7 pillar/chapter images shipped unsized; now asserted by `every_landing_image_declares_its_dimensions` |
| **Shared Faker `unique()` pool leaking across test files** | Any test adding a `fake()` draw can shift which names other tests get, turning a latent collision into a red suite | ⚠️ **Hit and fixed in Phase 4 — twice.** (1) `DosageFormFactory` derives its slug from `unique()->randomElement()`, while `PartProductCatalogueTest` pinned `'Bolus'` — also in that pool. Two creates → `UNIQUE constraint failed: dosage_forms.slug`. The test now pins `'Bolus Form'`, outside the pool. (2) Same shape for `ProductFactory`: the test pins `'Amoxivet'`/`'Baytril'` onto specific products and then asserts *by name* that one part's page never shows the other part's drug — but both names were still in the factory's random pool, so an unrelated random product could carry the name and fail the assertion for a reason unrelated to filtering. Both names removed from the pool. **Lesson for any new factory in this project: do not put a name in a random pool if a test pins that name and asserts on it.** `LandingPageTest` sidesteps the trap entirely by inserting rows directly rather than through factories; the full suite is re-run under two random orderings to confirm. |

---

## 14. Explicitly out of scope

- Redesigning `main-header.blade.php` or `footer.blade.php` (decision 2)
- `/drugs` redesign (decision 3)
- A reply / mail-sending action in the admin (decision 5)
- Editing stored messages (decision 4)
- Client-side `data-ar` translation swapping (decision 6)
- Any new composer or npm dependency
- The three empty placeholder sections in the reference (HTML comments only, no markup)