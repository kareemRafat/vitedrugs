# Home Page — Implementation Plan

> Route: `GET /`
> Controller: `HomeController@index`
> View: `home.blade.php`

---

## Milestone 1: Fix minor issues

| # | Task | Priority | Dependencies |
|---|---|---|---|---|
| 1.1 | - [x] Eager-load `dosageForm` on `$latestProducts` query to avoid N+1 | Low | None |
| 1.2 | - [x] Fix the search form `action="#"` to point to `route('search')` with query param `q` | Low | None |

## Milestone 2: Verify

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.1 | - [ ] Run `composer test` | Low | M1 |
| 2.2 | - [x] Run `./vendor/bin/pint` | Low | M1 |
