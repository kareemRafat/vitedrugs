# Search Page — Implementation Plan

> Route: `GET /search`
> Controller: `SearchController@index`
> View: `search/index.blade.php`

---

## Milestone 1: Expand search coverage

**Status:** Searches all 4 entities but only by their main name field — misses Arabic names and product-related entities

| # | Task | Priority | Dependencies |
|---|---|---|---|---|
| 1.1 | - [x] Search products by `trade_name_ar` and also via `orWhereHas` on active ingredients, companies, and diseases (like `ProductController@index` does) | Medium | None |
| 1.2 | - [x] Search companies by `name_ar` in addition to `name` | Low | None |
| 1.3 | - [x] Search diseases by `name_ar` in addition to `name` | Low | None |
| 1.4 | - [x] Search ingredients by `name_ar` in addition to `name` | Low | None |

## Milestone 2: Verify

| # | Task | Priority | Dependencies |
|---|---|---|---|---|
| 2.1 | - [ ] Run `composer test` | Low | M1 |
| 2.2 | - [x] Run `./vendor/bin/pint` | Low | M1 |
