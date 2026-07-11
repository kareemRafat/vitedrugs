# Diseases Page — Implementation Plan

> Routes: `GET /diseases` (index), `GET /diseases/{disease:slug}` (show)
> Controller: `DiseaseController@index`, `DiseaseController@show`
> Views: `diseases/index.blade.php`, `diseases/show.blade.php`

---

## Milestone 1: Index — polish

| # | Task | Priority | Dependencies |
|---|---|---|---|---|
| 1.1 | - [ ] Display `name_ar` in the table when available (currently searched but not shown) | Low | None |

## Milestone 2: Show — clean up template logic

**Status:** Working, but business logic lives in the view

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.1 | - [ ] Move the `$ingredients` collection-building logic (nested foreach in the Blade template) into the controller | Medium | None |

## Milestone 3: Verify

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 3.1 | - [ ] Run `composer test` | Medium | M2 |
| 3.2 | - [ ] Run `./vendor/bin/pint` | Low | M2 |
