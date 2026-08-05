# Pathogens (Microorganisms) — Antibiotics + Article Links — Implementation Plan

> Domain: Large Animals knowledge base
> Table: `microorganisms` (bacteria / viruses / fungi / parasites)
> Goal: Show which antibiotics affect each pathogen (+ sensitivity), and make pathogen names in disease articles clickable links to the pathogen page.

---

## Milestone 1: Data layer — antibiotic links

**Status:** Complete — pivot table, enum, and both relationships in place.

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 1.1 | - [x] Create migration `create_microorganism_active_ingredient_table` (`microorganism_id` foreignId, `active_ingredient_id` foreignUlid, `sensitivity` string, `notes` text nullable, timestamps, composite PK) | High | None |
| 1.2 | - [x] Add `App\Enums\AntibioticSensitivity` enum (`Sensitive`, `Moderate`, `Resistant`) matching `ProductStatus` style | Medium | None |
| 1.3 | - [x] Add `activeIngredients()` belongsToMany on `Microorganism` model (withPivot `sensitivity`, `notes` + withTimestamps) | High | 1.1 |
| 1.4 | - [x] Add `microorganisms()` belongsToMany on `ActiveIngredient` model | High | 1.1 |
| 1.5 | - [x] Run `php artisan migrate` to create pivot table | Medium | 1.1 |

## Milestone 2: Seed example antibiotic links

### Status: Complete — data populated via seeder.

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 2.1 | - [x] Create `MicroorganismActiveIngredientSeeder` linking known antibiotics (matched by name: Amoxicillin, Enrofloxacin, Oxytetracycline, Ceftiofur, Doxycycline, Tylosin, Florfenicol) to key pathogens with realistic sensitivity | Medium | 1.3, 1.4 |
| 2.2 | - [x] Register seeder in `KnowledgeBaseSeeder` after `DiseaseMicroorganismSeeder` | Medium | 2.1 |
| 2.3 | - [x] Run `php artisan db:seed --class=MicroorganismActiveIngredientSeeder` to verify | Low | 2.1 |

## Milestone 3: Public show page — antibiotics section

### What: render linked antibiotics with sensitivity badge on the pathogen page

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 3.1 | - [x] Add "Antibiotics" card to `large-animals/microorganisms/show.blade.php` main column; list active ingredients with sensitivity badge | High | 1.3 |
| 3.2 | - [x] Add empty-state message when no antibiotics are linked | Medium | 3.1 |
| 3.3 | - [x] Add Arabic + English lang keys: `microorganisms.antibiotics`, `microorganisms.no_antibiotics`, `microorganisms.sensitivity.{sensitive,moderate,resistant}` | Medium | 3.1 |
| 3.4 | - [x] Run `npm run build` to compile new Flowbite/Tailwind classes | Low | 3.1 |

## Milestone 4: Clickable pathogen links in disease article

### What: fix display_name bug + render microorganism links

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 4.1 | - [x] Fix `DiseaseArticleBuilder` microorganism block (replace broken `pluck('display_name')`), emit dedicated `microorganisms` section with `{name, slug, role}` | High | None |
| 4.2 | - [x] Add `microorganisms` render branch in `large-animals/medical/article.blade.php` (clickable links → `large-animals.microorganisms.show` + cause/associated badge) | High | 4.1 |
| 4.3 | - [x] Confirm `TestArticleController` (`article-test` route) renders the new section | Low | 4.2 |

## Milestone 5: Verify

| # | Task | Priority | Dependencies |
|---|---|---|---|
| 5.1 | - [x] Run `vendor/bin/pint --dirty` | Low | M1–M4 |
| 5.2 | - [x] Run `php artisan test --compact` | Low | M1–M4 |
| 5.3 | - [x] Optionally regenerate `database/schema/mysql-schema.sql` | Low | 1.1 |

---

## Completed scope (follow-ups)
- **Microorganism catalog data quality**: added `is_topic` flag + `Microorganism::catalogue()` scope; deleted `Unknown`/duplicate rows; re-classified mislabeled fungi → `fungus`. Index groups organisms by type (fixed order) with disease/antibiotic counts.
- **Translation rename approved & applied**: `الكائنات الدقيقة` → `الكائنات الممرضة` (plural) and `كائن دقيق` → `كائن ممرض` (singular) across `lang/ar/large-animals.php`.

## Out of scope (deferred)
- JSON narrative article on the microorganism show page
- Filament admin resource for `microorganisms` (admin management deferred)