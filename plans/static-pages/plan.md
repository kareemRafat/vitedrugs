# Static Pages — Implementation Plan

> Routes: `GET /about`, `GET /contact`, `GET /privacy-policy`, `GET /terms-of-service`
> All use `Route::view()` — no controllers, no dynamic data

---

## Milestone 1: Contact page — Add contact form

**Status:** Hardcoded contact info only, no form

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 1.1 | Create a `ContactController` with `index` (show form) and `store` (handle submission) methods | Medium | None |
| 1.2 | Add `routes/web.php` entry for form POST | Medium | 1.1 |
| 1.3 | Build a contact form (name, email, subject, message) in the Blade view | Medium | 1.1 |
| 1.4 | Add validation and mail-to-log (or database store) for submissions | Medium | 1.2 |

## Milestone 2: Verify

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.1 | Run `composer test` | Medium | M1 |
| 2.2 | Run `./vendor/bin/pint` | Low | M1 |
