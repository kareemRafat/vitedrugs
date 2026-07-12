# Filament Admin Panel — Complete Plan

## Status Legend
- `[x]` — Already implemented
- `[ ]` — Needs to be done

---

## Dashboard & Navigation

- `[x]` Organize resources into **navigation groups**: `Catalog`, `Content`, `System`
- `[x]` Assign proper **Heroicons** to all resources
- `[x]` Fix `Statistics` page navigation icon type (now uses `Heroicon::OutlinedChartBar`)
- `[x]` Fix `DataQuality` page navigation group type (now uses `string|UnitEnum|null`)

---

## Resources Already Built (12)

| Resource | Group | Icon | Notes |
|---|---|---|---|
| `Products/ProductResource` | Catalog | `OutlinedCube` | Full CRUD + 11 relation managers |
| `Companies/CompanyResource` | Catalog | `OutlinedBuildingOffice2` | Full CRUD + Products relation manager |
| `ActiveIngredients/ActiveIngredientResource` | Catalog | `OutlinedBeaker` | Full CRUD + DrugInteractions + DrugClasses |
| `DrugClasses/DrugClassResource` | Catalog | `OutlinedTag` | Full CRUD + ActiveIngredients |
| `Species/SpeciesResource` | Catalog | `OutlinedSwatch` | Full CRUD, soft deletes |
| `Diseases/DiseaseResource` | Catalog | `OutlinedBugAnt` | Full CRUD + Products |
| `DosageForms/DosageFormResource` | Catalog | `OutlinedTableCells` | Full CRUD, soft deletes |
| `ImportJobs/ImportJobResource` | System | `OutlinedArrowUpOnSquare` | PDF/JSON import pipeline |
| `Indications/IndicationResource` | (hidden) | `OutlinedCheckBadge` | Managed via Product |
| `Contraindications/ContraindicationResource` | (hidden) | `OutlinedXCircle` | Managed via Product |
| `Precautions/PrecautionResource` | (hidden) | `OutlinedShieldExclamation` | Managed via Product |
| `SideEffects/SideEffectResource` | (hidden) | `OutlinedExclamationTriangle` | Managed via Product |

## Resources Newly Added (5)

| Resource | Group | Icon | Notes |
|---|---|---|---|
| `BlogCategories/BlogCategoryResource` | Content | `OutlinedBookmarkSquare` | Full CRUD |
| `Blogs/BlogResource` | Content | `OutlinedNewspaper` | Full CRUD, RichEditor body, cover image upload |
| `ContactSubmissions/ContactSubmissionResource` | System | `OutlinedEnvelope` | View + Delete (no edit/create for manual use) |
| `Users/UserResource` | System | `OutlinedUsers` | Full CRUD |
| `DrugInteractions/DrugInteractionResource` | Catalog | `OutlinedArrowsRightLeft` | Full CRUD |

---

## Custom Pages (Existing)

- `[x]` **Statistics** — counts + top 10 companies/diseases/ingredients/dosage forms
- `[x]` **DataQuality** — orphan records (products without diseases/ingredients, companies without products, etc.)
- `[x]` **Navigation icon fixed** — both pages use proper `Heroicon` BackedEnum
- `[x]` **Navigation group fixed** — both pages use correct type

---

## Navigation Groups (Final)

```
Catalog
├── Products
├── Companies
├── Active Ingredients
├── Diseases
├── Dosage Forms
├── Drug Classes
├── Species
├── Drug Interactions

Content
├── Blog Posts
├── Blog Categories

System
├── Import Jobs
├── Contact Submissions
├── Users
├── Statistics (page)
├── Data Quality (page)
```

---

## Implementation Order

1. `[x]` Fix existing navigation icons & groups
2. `[x]` BlogCategoryResource
3. `[x]` BlogResource
4. `[x]` ContactSubmissionResource
5. `[x]` UserResource
6. `[x]` DrugInteractionResource

---

## Remaining

- `[ ]` Write Filament resource tests for new resources
- `[ ]]` Blog model — verify `author_id` foreign key index in migration
- `[ ]` BlogCategory model — verify `slug` unique constraint in migration
