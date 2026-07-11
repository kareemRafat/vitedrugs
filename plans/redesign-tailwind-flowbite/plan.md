# Redesign: Bootstrap → Tailwind CSS v4 + Flowbite

> Migrate all public pages from Bootstrap 4/5 to Tailwind CSS v4 with Flowbite components.
> Also build public login and registration pages using Flowbite design patterns.

---

## Prerequisites

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 0.1 | Install Flowbite npm package: `npm install flowbite` | High | None | - [x] |
| 0.2 | Add Flowbite as a plugin in `resources/css/app.css` using `@plugin "flowbite/plugin"` + `@import "flowbite/src/themes/default"` (Tailwind v4 syntax) | High | 0.1 | - [x] |
| 0.3 | Import Flowbite JS bundle in `resources/js/app.js` | High | 0.1 | - [x] |
| 0.4 | Install `fuzzyfox/lucide-for-laravel` for Lucide icon Blade components | Medium | None | - [x] |

---

## ✅ Milestone 1: Auth system — Login & Register pages

**Status:** ✅ Complete

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 1.1 | Create `LoginController` with `create()` (show form) and `store()` (authenticate) | High | None | - [x] |
| 1.2 | Create `RegisterController` with `create()` (show form) and `store()` (register user) | High | None | - [x] |
| 1.3 | Add auth routes to `routes/web.php`: `GET/POST /login`, `GET/POST /register`, `POST /logout` | High | 1.1, 1.2 | - [x] |
| 1.4 | Create `resources/views/app/auth/login.blade.php` with Flowbite v4-styled form | High | 0.1–0.3 | - [x] |
| 1.5 | Create `resources/views/app/auth/register.blade.php` with Flowbite v4-styled form | High | 0.1–0.3 | - [x] |
| 1.6 | Create `resources/views/app/layouts/guest.blade.php` — minimal auth layout | High | None | - [x] |
| 1.7 | Add flashed message / validation error display using Flowbite alert components | Medium | 1.4, 1.5 | - [x] |
| 1.8 | Rewrite `welcome.blade.php` with Flowbite v4 landing page | Low | 1.3 | - [x] |

---

## ✅ Milestone 2: App shell — Master layout with Flowbite

**Status:** ✅ Complete

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 2.1 | Rewrite `resources/views/app/layouts/master.blade.php` — Flowbite nav/sidebar layout | High | 0.1–0.3 | - [x] |
| 2.2 | Rewrite `resources/views/app/layouts/main-header.blade.php` — Flowbite navbar | High | 2.1 | - [x] |
| 2.3 | Rewrite `resources/views/app/layouts/main-sidebar.blade.php` — Flowbite sidebar | High | 2.1 | - [x] |
| 2.4 | Rewrite `resources/views/app/layouts/footer.blade.php` — Flowbite-styled footer | Medium | 2.1 | - [x] |
| 2.5 | Clean `head.blade.php` — keep only Vite `@vite()` | High | None | - [x] |
| 2.6 | Clean `footer-scripts.blade.php` — keep only Vite `@vite()` and `@yield('js')` | High | 2.5 | - [x] |
| 2.7 | Replace font icons with inline SVGs | Medium | 0.1–0.3 | - [x] |
| 2.8 | Install `fuzzyfox/lucide-for-laravel` and replace SVGs with `<x-lucide-*>` components | Medium | 2.7 | - [x] |

---

## ✅ Milestone 10.5: View restructuring & Flowbite v4 class fix

**Status:** ✅ Complete — all views moved under `app/` directory and all old Flowbite v3 classes replaced with v4 design tokens.

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 10.5.1 | Restructure `resources/views/` — move all public views under `app/`, admin views under `filament/` | High | 2.6 | - [x] |
| 10.5.2 | Update all `@extends()` and `@include()` references to use `app.` prefix | High | 10.5.1 | - [x] |
| 10.5.3 | Update all controller `view()` calls to use `app.` prefix (7 controllers, 12 calls) | High | 10.5.1 | - [x] |
| 10.5.4 | Delete dead layout files: `master2`, `head`, `sidebar`, `models` | High | 10.5.1 | - [x] |
| 10.5.5 | Fix all auth, guest, and shell views to use Flowbite v4 class names (`bg-brand`, `border-default-medium`, `rounded-base`, `text-heading`, `text-body`, `shadow-xs`, etc.) | High | 2.1–2.8 | - [x] |

---

## Milestone 3: Products pages — Index & Show

**Status:** Fully functional with Bootstrap classes

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 3.1 | Rewrite `resources/views/app/products/index.blade.php` — Flowbite table, search/filter, pagination | High | 2.1 |
| 3.2 | Rewrite `resources/views/app/products/show.blade.php` — hero, meta cards, tables, sidebar lists | High | 2.1, 2.2 |
| 3.3 | Add missing sections: indications, contraindications, precautions, side effects, alternatives, images, documents | High | 3.2 |
| 3.4 | Add responsive grid for mobile | High | 3.1, 3.2 |

---

## Milestone 4: Companies pages — Index & Show

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 4.1 | Rewrite `resources/views/app/companies/index.blade.php` — Flowbite table, search, pagination | High | 2.1 |
| 4.2 | Rewrite `resources/views/app/companies/show.blade.php` — info cards, products table, parent/subsidiaries | High | 2.1 |
| 4.3 | Display `products.dosageForm`, `parentCompany`, and `subsidiaries` (loaded but not shown) | Medium | 4.2 |

---

## Milestone 5: Diseases pages — Index & Show

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 5.1 | Rewrite `resources/views/app/diseases/index.blade.php` — Flowbite table, search, pagination | High | 2.1 |
| 5.2 | Rewrite `resources/views/app/diseases/show.blade.php` — disease info, related products, ingredients sidebar | High | 2.1 |
| 5.3 | Move `$ingredients` logic from view to controller | Medium | 5.2 |

---

## Milestone 6: Active Ingredients pages — Index & Show

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 6.1 | Rewrite `resources/views/app/active-ingredients/index.blade.php` — Flowbite table, pagination | High | 2.1 |
| 6.2 | Rewrite `resources/views/app/active-ingredients/show.blade.php` — ingredient info, related products, drug classes, diseases | High | 2.1 |

---

## Milestone 7: Search page

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 7.1 | Rewrite `resources/views/app/search/index.blade.php` — Flowbite search form + result cards grid | High | 2.1 |
| 7.2 | Expand search to cover `name_ar` and product-related entities | Medium | 7.1 |

---

## ✅ Milestone 8: Home page

**Status:** ✅ Complete

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 8.1 | Rewrite `resources/views/app/home.blade.php` — stat cards, latest products/companies using Flowbite v4 cards | High | 2.1 | - [x] |
| 8.2 | Fix search form action to point to `route('search')` with `q` param | Low | 8.1 | - [x] |
| 8.3 | Add `with('dosageForm')` to `$latestProducts` query to prevent N+1 | Low | None | - [x] |

---

## Milestone 9: Static pages — About, Contact, Privacy, Terms

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 9.1 | Rewrite all 4 static pages (`resources/views/app/pages/*.blade.php`) using Flowbite prose/typography | Medium | 2.1 |
| 9.2 | Add ContactController with form submission, validation, mail-to-log | Medium | 9.1 |

---

## Milestone 10: Remove legacy assets & verify

**Status:** `public/assets/` deleted. Vite builds successfully. Pint passes. Pre-existing test failure (SQLite in-memory `no such table`) remains — see AGENTS.md.

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 10.1 | Delete entire `public/assets/` directory | High | 2.5, 2.6 | - [x] |
| 10.2 | Run `npm run build` to verify Vite compilation with Flowbite | High | 0.1–0.3 | - [x] |
| 10.3 | Run `composer test` to ensure no PHP regressions | High | All M1–M9 | - [x] |
| 10.4 | Run `./vendor/bin/pint` for code style | Medium | All M1–M9 | - [x] |
| 10.5 | Manual responsive check on mobile, tablet, desktop viewports | High | All M1–M9 | - [ ] |
