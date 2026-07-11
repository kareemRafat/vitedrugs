# Active Ingredients Page — Implementation Plan

> Routes: `GET /active-ingredients` (index), `GET /active-ingredients/{activeIngredient:slug}` (show)
> Controller: `ActiveIngredientController@index`, `ActiveIngredientController@show`
> Views: `active-ingredients/index.blade.php`, `active-ingredients/show.blade.php`

---

## Milestone 1: Show — Fix N+1 query

**Status:** `products.diseases` is accessed in a loop but not eager-loaded

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 1.1 | Add `products.diseases` to the `load()` call in `ActiveIngredientController@show` | High | None |

## Milestone 2: Show — Remove unused eager load

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.1 | Remove `products.dosageForm` from `load()` — it is never rendered on this page | Low | None |

## Milestone 3: Verify

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 3.1 | Run `composer test` | High | M1 |
| 3.2 | Run `./vendor/bin/pint` | Low | M1 |
