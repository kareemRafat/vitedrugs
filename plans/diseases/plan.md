# Diseases Page — Implementation Plan

> Routes: `GET /diseases` (index), `GET /diseases/{disease:slug}` (show)
> Controller: `DiseaseController@index`, `DiseaseController@show`
> Views: `diseases/index.blade.php`, `diseases/show.blade.php`

---

## ✅ Milestone 1: Index — polish

**Status:** ✅ Complete

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 1.1 | - [x] Display `name_ar` in the table when available (currently searched but not shown) | Low | None |

## ✅ Milestone 2: Show — clean up template logic

**Status:** ✅ Complete — `$ingredients` logic moved to controller.

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.1 | - [x] Move the `$ingredients` collection-building logic (nested foreach in the Blade template) into the controller | Medium | None |

## ✅ Milestone 3: Verify

**Status:** ✅ Complete — Pint passes. Pre-existing test failure (locale redirect) unaffected.

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 3.1 | - [x] Run `composer test` | Medium | M2 |
| 3.2 | - [x] Run `./vendor/bin/pint` | Low | M2 |
