# Products Page — Implementation Plan

> Routes: `GET /products` (index), `GET /products/{product:slug}` (show)
> Controllers: `ProductController@index`, `ProductController@show`
> Views: `products/index.blade.php`, `products/show.blade.php`

---

## Milestone 1: Index page — Optimize & polish

**Status:** Core features done, minor issues remain

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 1.1 | Lazy-load `$companies` and `$dosageForms` only when their respective filters are active (avoid loading all records on every page load) | Medium | None |
| 1.2 | Add `name_ar` column to the table display for bilingual support | Low | None |
| 1.3 | Ensure search also hits `name_ar` field for Arabic product names | Low | None |

---

## Milestone 2: Show page — Add missing sections

**Status:** 7 relationships loaded in controller but never rendered in view

### 2.1 — Add Indications section

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.1.1 | Add a "Indications" card in the main column (`col-xl-8`) after the hero section | High | None |
| 2.1.2 | Display `$indication->description` (and `->description_ar` if present) as a numbered or bulleted list ordered by `sort_order` | High | None |

### 2.2 — Add Contraindications section

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.2.1 | Add a "Contraindications" card below Indications | High | None |
| 2.2.2 | Display `$contraindication->description` in a list ordered by `sort_order` | High | None |

### 2.3 — Add Precautions section

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.3.1 | Add a "Precautions" card below Contraindications | High | None |
| 2.3.2 | Display `$precaution->description` in a list ordered by `sort_order` | High | None |

### 2.4 — Add Side Effects section

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.4.1 | Add a "Side Effects" card below Precautions | High | None |
| 2.4.2 | Display `$sideEffect->description` in a list ordered by `sort_order` | High | None |

### 2.5 — Add Alternatives section

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.5.1 | Eager-load `alternatives.alternativeProduct` in `ProductController@show` | High | None |
| 2.5.2 | Add a "Alternatives" card in the sidebar or main column | High | None |
| 2.5.3 | Display alternative product `trade_name` as a link to its show page, plus `type` and `notes` | High | None |

### 2.6 — Add Images gallery section

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.6.1 | Add an "Images" card/section (grid of thumbnails) | Medium | None |
| 2.6.2 | Display `$image->image` (path) with lightbox or enlarged view on click | Medium | None |

### 2.7 — Add Documents section

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.7.1 | Add a "Documents" card with a list of downloadable files | Medium | None |
| 2.7.2 | Display `$document->title` as a link to `$document->file_path`, with `type` badge | Medium | None |

---

## Milestone 3: Show page — Clean up controller

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 3.1 | Remove the singular `company` relation from `$product->load()` — it is never used (only `companies` plural is used) | Low | None |

---

## Milestone 4: Verify

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 4.1 | Run `composer test` to ensure no regressions | High | All M2 tasks |
| 4.2 | Run `./vendor/bin/pint` for code style | Medium | All M2 tasks |
| 4.3 | Manual check: navigate each new section on a product detail page | High | All M2 tasks |
