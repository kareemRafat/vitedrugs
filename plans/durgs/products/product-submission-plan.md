# Product Submission — Implementation Plan

> Users submit product data via public form → creates `Product` record directly with `status=pending` → admin reviews & flips `status=approved` → product appears on frontend.

---

## Core concept

Instead of separate `product_submissions` table + JSON blob + import service on approval, the submission form creates a real `Product` (and all its relations) immediately with `status=pending`. The admin simply toggles to `approved`. This gives:

- **Real-time validation** — check trade name duplicates, species existence, etc. at submission time
- **No import step** — data lives in proper columns and relationships from the start
- **No JSON parsing** — the infolist reads real columns, not `submitted_data`
- **Simpler admin UI** — approve = flip `status`, reject = flip `status` + add notes

---

## Milestone 1: Product schema changes

| # | Task | Priority |
|---|---|---|
| 1.1 | - [x] **Migration**: add columns to `products` table — `string('status', 20)->default('pending')`, `nullable foreignId('created_by')->constrained('users')->nullOnDelete()`, `nullable text('admin_notes')`, `nullable timestamp('reviewed_at')`. Keep existing `is_active` boolean (still used by admin panel toggle; status gates public visibility) | High |
| 1.2 | - [x] Create `App\Enums\ProductStatus` — `enum ProductStatus: string` with cases `Pending = 'pending'`, `Approved = 'approved'`, `Rejected = 'rejected'`, plus `color(): string` method matching existing `SubmissionStatus` | High |
| 1.3 | - [x] **Product model** changes: add `status` to `$fillable` + `$casts = ['status' => ProductStatus::class]`; add local scopes `scopePending($q)`, `scopeApproved($q)`, `scopeRejected($q)`, `scopePendingOrApproved($q)` (for admin listing all non-rejected); add `isPending(): bool`, `isApproved(): bool`, `isRejected(): bool` helper methods | High |
| 1.4 | - [x] **Global scope** — `booted()` on Product model: `static::addGlobalScope('approved', fn (Builder $q) => $q->where('status', ProductStatus::Approved));`. **Bypass in admin**: `ProductResource::getEloquentQuery()` calls `->withoutGlobalScope('approved')` + SoftDeletes scope (already done in `getRecordRouteBindingEloquentQuery`). Also bypass in `SitemapController` (include approved only) and `ImportDrugVetProducts` command | High |
| 1.5 | - [x] Run `php artisan migrate` | High |
| 1.6 | - [x] **Factory**: update `ProductFactory` to `'status' => ProductStatus::Approved` and `'created_by' => User::factory()` | Medium |

### Public queries affected by the global scope

| # | Query location | File | Change needed |
|---|---|---|---|
| 1.4a | ProductController@index | `app/Http/Controllers/ProductController.php` | Already filtered by global scope — no change needed |
| 1.4b | ProductController@show | `app/Http/Controllers/ProductController.php` | Already filtered — no change |
| 1.4c | SearchController@index | `app/Http/Controllers/SearchController.php` | Already filtered — no change |
| 1.4d | HomeController@index | `app/Http/Controllers/HomeController.php` | Already filtered — verify counts only include approved |
| 1.4e | LandingController | `app/Http/Controllers/LandingController.php` | Already filtered — verify counts only include approved |
| 1.4f | CompanyController@show | `app/Http/Controllers/CompanyController.php` | Relationship `products` — global scope applies automatically if Product model is used (yes, `$company->products` uses Product model) |
| 1.4g | DiseaseController@show | `app/Http/Controllers/DiseaseController.php` | Same — relationship auto-filtered |
| 1.4h | ActiveIngredientController@show | `app/Http/Controllers/ActiveIngredientController.php` | Same — relationship auto-filtered |
| 1.4i | SitemapController | `app/Http/Controllers/SitemapController.php` | Global scope applies = only approved in sitemap ✅ |
| 1.4j | product-browser Livewire | `resources/views/app/components/products/⚡product-browser.blade.php` | `Product::query()` in `filteredQuery()` — auto-filtered ✅ |
| 1.4k | product-compare-builder | `resources/views/app/components/products/⚡product-compare-builder.blade.php` | `searchQuery()` and `loadProduct()` use Product — need `->withoutGlobalScope('approved')` because admin may want to search/compare pending products |
| 1.4l | product-compare-bar | `resources/views/app/components/products/⚡product-compare-bar.blade.php` | `Product::query()->whereIn('id', $ids)` — auto-filtered, but if a product is pending and added to compare from index page (where it won't appear)... this is fine since pending won't appear in search |

---

## Milestone 2: Public form (controller + route + view) — mostly done, just need to rewire

The submission form view (`create-submission.blade.php`) and JS already exist and work. The controller needs to create Product + relations directly instead of storing JSON.

| # | Task | Priority |
|---|---|---|
| 2.1 | - [x] **Rewrite `ProductSubmissionController::store()`** — instead of `ProductSubmission::create(['submitted_data' => $validated])`, it creates: `Product::create([...])` with `status=pending`, then creates all relations (active ingredients via `syncWithoutDetaching`, indications/contraindications/precautions/side effects via `create()`, dosages/withdrawal periods via `create()`, diseases via `syncWithoutDetaching`, companies via `syncWithoutDetaching` with role, images/documents via `firstOrCreate`). **Reuse the relation-creation logic from `ProductImportService::import()`** — extract it into a trait or helper so both the submission controller and the old import command can share it | High |
| 2.2 | - [x] **Trade name duplicate check** — in `StoreProductSubmissionRequest`, add `Rule::unique('products', 'trade_name')->where('status', ProductStatus::Pending)`. If a pending product with that trade name exists, show a warning message (not a hard error): "A product with this name has already been submitted and is pending review" | High |
| 2.3 | - [x] **Routes** — already exist (`GET/POST /products/create-submission` mapped to `ProductSubmissionController`) | None |
| 2.4 | - [x] The `StoreProductSubmissionRequest` already has validation rules — update to validate `trade_name` uniqueness (pending products only), ensure species references exist | High |
| 2.5 | - [x] `throttle:3,60` on POST route — already applied | None |
| 2.6 | - [x] **Remove** `ProductSubmission` model file entirely | High |
| 2.7 | - [x] **Remove** `ApproveProductSubmissionAction` file entirely | High |
| 2.8 | - [x] **Remove** `ProductImportService` file — inline any remaining reusable logic into the controller or a new helper | High |

### Files to modify (controller/view side)

| File | Action |
|---|---|
| `app/Http/Controllers/ProductSubmissionController.php` | Rewrite `store()` to create Product + relations directly |
| `app/Http/Requests/StoreProductSubmissionRequest.php` | Add trade_name unique rule (pending only) |
| `app/Models/Product.php` | Add `created_by` to `$fillable`, add `belongsTo(User::class, 'created_by')` relation |
| `lang/en/messages.php` | Update/add translation for duplicate trade_name warning |
| `lang/ar/messages.php` | Same for Arabic |
| `routes/web.php` | Remove `use App\Models\ProductSubmission;` if present — no route change needed |

---

## Milestone 3: Admin Filament — pending products tab on existing ProductResource

**Decision: Add a "Pending" tab to the existing `ProductResource`** instead of creating a separate resource. This avoids duplicating all the relation managers, schemas, and tables. The existing `ProductResource` already handles CRUD for products — we just add a tab filter and review actions.

| # | Task | Priority |
|---|---|---|
| 3.1 | - [x] **`ProductResource::getEloquentQuery()`** — override to `->withoutGlobalScope('approved')->withoutGlobalScope(SoftDeletingScope::class)` so admin sees all products (pending, approved, rejected, trashed) | High |
| 3.2 | - [x] **`ProductsTable`** — add `status` column as badge (using `ProductStatus::color()`), add `status` select filter (pending/approved/rejected/all), add `created_by` (user name) and `submitted_at` columns, add **bulk approve/reject actions** | High |
| 3.3 | - [x] **`ListProducts` page** — add tab (`ProductStatus::Pending->value`) with label "Pending" + count badge. Tabs: All \| Pending \| Approved \| Rejected | High |
| 3.4 | - [x] **`ProductInfolist`** — add `status` badge entry with color, add `created_by` (user name relation), add `admin_notes` text entry (visible only when filled), add `reviewed_at` datetime entry | High |
| 3.5 | - [x] **`ViewProduct` page** — add "Approve" and "Reject" header actions (same pattern as old `ViewProductSubmission.php` but operating on Product directly). Approve: `$record->update(['status' => ProductStatus::Approved, 'reviewed_at' => now()])`. Reject: modal with `admin_notes` textarea → `$record->update(['status' => ProductStatus::Rejected, 'admin_notes' => ..., 'reviewed_at' => now()])` | High |
| 3.6 | - [x] **`ProductForm`** — keep `is_active` toggle, but make `status` and submitter fields read-only (they are set by the submission flow, not by the form) | Medium |
| 3.7 | - [x] Add a **"Submitted" section** to `ProductInfolist` and `ProductForm` showing: `created_by` (user name), status, admin_notes, reviewed_at — placed in a separate section, not mixed with product data | High |

### Filament files to modify (not create new)

| File | Action |
|---|---|
| `app/Filament/Resources/Products/ProductResource.php` | Override `getEloquentQuery()`, add navigation badge for pending count |
| `app/Filament/Resources/Products/Tables/ProductsTable.php` | Add status/submitter columns + status filter + bulk actions |
| `app/Filament/Resources/Products/Pages/ListProducts.php` | Add tabs (All/Pending/Approved/Rejected) |
| `app/Filament/Resources/Products/Pages/ViewProduct.php` | Add Approve/Reject header actions |
| `app/Filament/Resources/Products/Schemas/ProductInfolist.php` | Add status + submitter info + admin_notes entries |
| `app/Filament/Resources/Products/Schemas/ProductForm.php` | Add read-only status + submitter fields |

---

## Milestone 4: Remove all old product_submission files

Delete the following files — no migration needed for the table itself (just delete its migration file so `php artisan migrate` never creates it on a fresh DB):

| # | File to delete | Reason |
|---|---|---|
| 4.1 | `database/migrations/2026_07_21_000001_create_product_submissions_table.php` | No `down()` migration needed — just remove the file so fresh installs don't create the table |
| 4.2 | `app/Models/ProductSubmission.php` | Replaced by `status` on Product |
| 4.3 | `app/Enums/SubmissionStatus.php` | Replaced by `ProductStatus` |
| 4.4 | `app/Actions/Products/ApproveProductSubmissionAction.php` | Logic inlined into ViewProduct page actions |
| 4.5 | `app/Services/Imports/ProductImportService.php` | Relation-creation logic moved into controller or helper |
| 4.6 | `app/Filament/Resources/ProductSubmissions/` (entire directory) | No separate resource needed |
| 4.7 | `app/Http/Requests/StoreProductSubmissionRequest.php` | If it exists — this already exists and is kept (just updated) |

---

## Milestone 5: Verify

| # | Task | Priority |
|---|---|---|
| 5.1 | - [x] Run `php artisan migrate` (should succeed, no `product_submissions` table) | High |
| 5.2 | - [x] Visit `/products/create-submission`, submit a complete form — verify Product created with `status=pending` | High |
| 5.3 | - [x] Verify the product is NOT visible on `/products`, `/products/{slug}`, or `/search` | High |
| 5.4 | - [x] Check admin panel at `/admin/products` with "Pending" tab — verify product appears | High |
| 5.5 | - [x] Approve the product — verify it now appears on `/products` and show page | High |
| 5.6 | - [x] Reject another — verify status updates with notes, product stays hidden | High |
| 5.7 | - [x] Run `./vendor/bin/pint --format agent` | Medium |
| 5.8 | - [x] Run `npm run build` | Medium |
