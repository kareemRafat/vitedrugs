# Companies Page — Implementation Plan

> Routes: `GET /companies` (index), `GET /companies/{company:slug}` (show)
> Controller: `CompanyController@index`, `CompanyController@show`
> Views: `companies/index.blade.php`, `companies/show.blade.php`

---

## Milestone 1: Index — polish

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 1.1 | Display `name_ar` in the table when available (currently searched but not shown) | Low | None |

## Milestone 2: Show — clean up unused eager loads

**Status:** 3 relations loaded but never rendered

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.1 | Display `products.dosageForm` in the products table (add a "Dosage Form" column) — data is already loaded | Medium | None |
| 2.2 | Display `parentCompany` name as a link (if it exists) — data is already loaded | Medium | None |
| 2.3 | Display `subsidiaries` list (if any) — data is already loaded | Medium | None |
| 2.4 | Add company `description` to the show page | Low | None |

## Milestone 3: Verify

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 3.1 | Run `composer test` | Medium | M2 |
| 3.2 | Run `./vendor/bin/pint` | Low | M2 |
