# Product Comparison — Implementation Plan

> **State:** Session-based (product ULIDs stored in session)
> **Max compare:** 3 products
> **Livewire SFCs:** 3 components in `resources/views/app/components/products/`
> **New files:** 5 | **Modified files:** 4

---

## Milestone 1: Backend service & session management

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 1.1 | [ ] Create `app/Actions/CompareAction.php` — add/remove/all/clear/count methods backed by session | High | None |
| 1.2 | [ ] Add `GET /products/compare` route in `routes/web.php` — named `products.compare` | High | None |
| 1.3 | [ ] Bind the compare action in `AppServiceProvider` or use `app()` resolution | Low | 1.1 |

---

## Milestone 2: Main comparison page — Livewire SFC & view

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.1 | [ ] Create `resources/views/app/products/compare.blade.php` — page wrapper with `@livewire('products.product-comparison')` | High | 1.2 |
| 2.2 | [ ] Create `resources/views/app/components/products/⚡product-comparison.blade.php` — loads 3 products with all relations via `#[Computed]`, renders side-by-side table | High | 1.1 |
| 2.3 | [ ] Implement comparison table layout — rows: image, name, type, dosage form, manufacturer, active ingredients, diseases, package size, storage, dosages, withdrawal periods, indications, contraindications, precautions, side effects | High | 2.2 |
| 2.4 | [ ] Add mobile responsive fallback (stacked cards on small screens) | Medium | 2.3 |
| 2.5 | [ ] Add empty state with illustration + "Browse products" CTA when no products selected | Medium | 2.2 |
| 2.6 | [ ] Add `remove()` and `clear()` methods with `#[On('compare-updated')]` refresh | High | 1.1, 2.2 |

---

## Milestone 3: Compare toggle button — add/remove from anywhere

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 3.1 | [ ] Create `resources/views/app/components/products/⚡product-compare-toggle.blade.php` — `$productId` prop, `toggle()` method, `#[Computed] isInCompare` | High | 1.1 |
| 3.2 | [ ] Embed toggle on product cards in `products/index.blade.php` — `<x-lucide-scale />` icon when not in compare, `<x-lucide-check-circle />` (brand color) when in compare | High | 3.1 |
| 3.3 | [ ] Embed toggle in `products/show.blade.php` — same icon toggle, placed near the title/actions area | High | 3.1 |
| 3.4 | [ ] Disable toggle when compare list is full (3/3) — show `<x-lucide-scale class="opacity-40" />` with disabled state and tooltip | Medium | 3.1 |

---

## Milestone 4: Floating compare bar — persistent bottom nav

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 4.1 | [ ] Create `resources/views/app/components/products/⚡product-compare-bar.blade.php` — sticky bottom bar with `#[On('compare-updated')]` to refresh | High | 1.1 |
| 4.2 | [ ] Show product thumbnails + names with `<x-lucide-x />` remove button per product | High | 4.1 |
| 4.3 | [ ] Show main CTA button: `<x-lucide-git-compare /> Compare (N/3)` linking to `route('products.compare')` | High | 4.1 |
| 4.4 | [ ] Show "Clear all" text link calling `clear()` | Medium | 4.1 |
| 4.5 | [ ] Hide bar entirely when compare list is empty (0 products) | High | 4.1 |
| 4.6 | [ ] Add bar to `resources/views/app/layouts/master.blade.php` before closing `</body>` | High | 4.1 |

---

## Milestone 5: Polish & verification

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 5.1 | [ ] Use Flowbite v4 semantic classes throughout (no v3 `bg-gray-*`, `rounded-lg`, etc.) | High | All M2–M4 |
| 5.2 | [ ] Ensure all views support dark mode (`dark:` variants) | High | All M2–M4 |
| 5.3 | [ ] Run `./vendor/bin/pint` for code style | Medium | All M1–M4 |
| 5.4 | [ ] Run `composer test` to ensure no regressions | High | All M1–M4 |
| 5.5 | [ ] Manual smoke test: add/remove products, max limit, clear all, view compare page, verify session | High | All M1–M4 |
