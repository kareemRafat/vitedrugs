# Redesign: Bootstrap → Tailwind CSS v4 + Flowbite

> Migrate all public pages from Bootstrap 4/5 to Tailwind CSS v4 with Flowbite components.
> Also build public login and registration pages using Flowbite design patterns.

---

## Prerequisites

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 0.1 | Install Flowbite npm package: `npm install flowbite` | High | None |
| 0.2 | Add Flowbite as a plugin in `resources/css/app.css` using `@plugin "flowbite/plugin"` + `@import "flowbite/src/themes/default"` (Tailwind v4 syntax) | High | 0.1 |
| 0.3 | Import Flowbite JS bundle in `resources/js/app.js` | High | 0.1 |

---

## Milestone 1: Auth system — Login & Register pages

**Status:** No public auth exists. Filament handles admin auth only.
**Approach:** Custom controllers + Flowbite-styled Blade views.

**Why NOT Laravel Breeze:**
Breeze generates Blade views with plain Tailwind utility classes — it looks like a basic starter template, **not** Flowbite components (cards, buttons, inputs, navbars, alerts). Your auth pages would not match the Flowbite design language you want. Custom views with Flowbite classes is the only way to get the look you described.

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 1.1 | Create `LoginController` with `create()` (show form) and `store()` (authenticate) | High | None |
| 1.2 | Create `RegisterController` with `create()` (show form) and `store()` (register user) | High | None |
| 1.3 | Add auth routes to `routes/web.php`: `GET/POST /login`, `GET/POST /register`, `POST /logout` | High | 1.1, 1.2 |
| 1.4 | Create `resources/views/auth/login.blade.php` with Flowbite-styled form (email, password, remember me, submit button with Flowbite's `btn-primary` classes) | High | 0.1–0.3 |
| 1.5 | Create `resources/views/auth/register.blade.php` with Flowbite-styled form (name, email, password, confirm password) | High | 0.1–0.3 |
| 1.6 | Create `resources/views/layouts/guest.blade.php` — a minimal layout for auth pages (no sidebar, no header, centered card with Flowbite card component) | High | None |
| 1.7 | Add flashed message / validation error display using Flowbite alert components | Medium | 1.4, 1.5 |
| 1.8 | Update `welcome.blade.php` to remove dead auth links or reuse it as a landing page | Low | 1.3 |

---



## Milestone 2: App shell — Master layout with Flowbite

**Status:** Master layout loads Bootstrap CSS (7 files), jQuery, 15+ vendor JS plugins, and uses Bootstrap classes throughout

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.1 | Rewrite `resources/views/layouts/master.blade.php` — replace Bootstrap shell with Flowbite's nav/sidebar layout, remove loader image reference, use Tailwind grid | High | 0.1–0.3 |
| 2.2 | Rewrite `resources/views/layouts/main-header.blade.php` — Flowbite navbar with search bar, user dropdown, mobile hamburger | High | 2.1 |
| 2.3 | Rewrite `resources/views/layouts/main-sidebar.blade.php` — Flowbite sidebar with navigation links, active state indicators | High | 2.1 |
| 2.4 | Rewrite `resources/views/layouts/footer.blade.php` — simple Flowbite-styled footer | Medium | 2.1 |
| 2.5 | Rewrite `resources/views/layouts/head.blade.php` — remove all Bootstrap CSS links (style.css, style-dark.css, skin-modes.css, sidemenu.css, sidebar.css, icons.css, mCustomScrollbar.css). Keep only Vite `@vite()` directive | High | None |
| 2.6 | Rewrite `resources/views/layouts/footer-scripts.blade.php` — remove all vendor JS scripts (jQuery, Bootstrap bundle, ionicons, moment, rating, perfect-scrollbar, sparkline, mCustomScrollbar, sidebar, eva-icons, sticky, custom, sidemenu). Keep only Vite `@vite()` and `@yield('js')` | High | 2.5 |
| 2.7 | Replace font icons (currently `fe fe-*` and `las la-*` classes) — use inline SVGs, Heroicons, or Flowbite's icon approach | Medium | 0.1–0.3 |

---

## Milestone 3: Products pages — Index & Show

**Status:** Fully functional with Bootstrap classes

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 3.1 | Rewrite `resources/views/products/index.blade.php` — table using Flowbite table component, search/filter form, pagination with Flowbite-styled links | High | 2.1 |
| 3.2 | Rewrite `resources/views/products/show.blade.php` — hero section, meta cards, tables (dosages, withdrawal periods), sidebar lists using Flowbite card/table/list components | High | 2.1, 2.2 |
| 3.3 | Add missing sections at the same time: indications, contraindications, precautions, side effects, alternatives, images, documents (per the Products plan at `plans/products/plan.md`) | High | 3.2 |
| 3.4 | Add responsive grid for mobile (Flowbite handles this via Tailwind's grid utilities) | High | 3.1, 3.2 |

---

## Milestone 4: Companies pages — Index & Show

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 4.1 | Rewrite `resources/views/companies/index.blade.php` — Flowbite table, search form, pagination | High | 2.1 |
| 4.2 | Rewrite `resources/views/companies/show.blade.php` — company info cards, contact details, products table, parent/subsidiaries sections (using Flowbite components) | High | 2.1 |
| 4.3 | Display `products.dosageForm`, `parentCompany`, and `subsidiaries` (currently loaded but not shown) | Medium | 4.2 |

---

## Milestone 5: Diseases pages — Index & Show

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 5.1 | Rewrite `resources/views/diseases/index.blade.php` — Flowbite table, search, pagination | High | 2.1 |
| 5.2 | Rewrite `resources/views/diseases/show.blade.php` — disease info, related products table, active ingredients sidebar | High | 2.1 |
| 5.3 | Move `$ingredients` logic from view to controller (code quality fix) | Medium | 5.2 |

---

## Milestone 6: Active Ingredients pages — Index & Show

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 6.1 | Rewrite `resources/views/active-ingredients/index.blade.php` — Flowbite table, pagination | High | 2.1 |
| 6.2 | Rewrite `resources/views/active-ingredients/show.blade.php` — ingredient info, indications/contraindications/precautions/side effects, related products table, drug classes and diseases sidebars | High | 2.1 |

---

## Milestone 7: Search page

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 7.1 | Rewrite `resources/views/search/index.blade.php` — Flowbite-styled search form with result cards in a responsive grid | High | 2.1 |
| 7.2 | Expand search to cover `name_ar` and product-related entities (per the Search plan at `plans/search/plan.md`) | Medium | 7.1 |

---

## Milestone 8: Home page

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 8.1 | Rewrite `resources/views/home.blade.php` — stat cards, latest products/companies lists using Flowbite card components | High | 2.1 |
| 8.2 | Fix search form action to point to `route('search')` with `q` param | Low | 8.1 |

---

## Milestone 9: Static pages — About, Contact, Privacy, Terms

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 9.1 | Rewrite all 4 static pages (`resources/views/pages/*.blade.php`) using Flowbite prose/typography components | Medium | 2.1 |
| 9.2 | Add functionality to Contact page: create `ContactController` with form submission, validation, mail-to-log | Medium | 9.1 |

---

## Milestone 10: Remove legacy assets & verify ✓

**Status:** `public/assets/` deleted. Vite build successful with Flowbite. Pint ran (101 style issues fixed). The `public/assets/` directory was deleted along with all corresponding asset references in `head.blade.php`, `footer-scripts.blade.php`, and `master.blade.php`. Pre-existing test failure (no migrations in test env) remains — see AGENTS.md.

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 10.1 | Delete entire `public/assets/` directory after confirming nothing is referenced by Filament or remaining views | High | 2.5, 2.6 | ✓ |
| 10.2 | Run `npm run build` to verify Vite compilation with Flowbite | High | 0.1–0.3 | ✓ |
| 10.3 | Run `composer test` to ensure no PHP regressions | High | All M1–M9 | ✓ |
| 10.4 | Run `./vendor/bin/pint` for code style | Medium | All M1–M9 | ✓ |
| 10.5 | Manual responsive check: navigate every page on mobile, tablet, desktop viewports | High | All M1–M9 | 
