# Static Pages — Implementation Plan

> Routes: `GET /about`, `GET /contact`, `GET /privacy-policy`, `GET /terms-of-service`
> All use `Route::view()` — no controllers, no dynamic data

---

## Milestone 1: Contact page — Add contact form

**Status:** Done

| # | Task | Priority | Dependencies |
|---|---|---|---|---|
| 1.1 | - [x] Create a `ContactController` with `index` (show form) and `store` (handle submission) methods | Medium | None |
| 1.2 | - [x] Add `routes/web.php` entry for form POST | Medium | 1.1 |
| 1.3 | - [x] Build a contact form (name, email, subject, message) in the Blade view | Medium | 1.1 |
| 1.4 | - [x] Add validation and mail-to-log (or database store) for submissions | Medium | 1.2 |

## Milestone 2: Redesign static pages (Tailwind v4 style)

**Status:** Done

| # | Task | Priority | Dependencies |
|---|---|---|---|---|
| 2.1 | - [x] Redesign About page with dark gradient hero, white cards, Tailwind v4 | Medium | None |
| 2.2 | - [x] Redesign Contact page matching About page design + AJAX form + toast | Medium | None |
| 2.3 | - [x] Redesign Privacy Policy page matching About page design | Medium | None |
| 2.4 | - [x] Redesign Terms of Service page matching About page design | Medium | None |

## Milestone 3: Verify

| # | Task | Priority | Dependencies |
|---|---|---|---|---|
| 3.1 | - [ ] Run `composer test` | Medium | M1, M2 |
| 3.2 | - [ ] Run `./vendor/bin/pint` | Low | M1, M2 |
