# Product Submission — Implementation Plan

> Users submit product data via public form → stored as `ProductSubmission` (JSON) → admin reviews & approves (creates real `Product` with all relations) or rejects.

---

## Milestone 1: Database schema & model

| # | Task | Priority |
|---|---|---|
| 1.1 | - [ ] Create migration `create_product_submissions_table` with: `ulid id`, `foreignId('user_id')->nullable()->constrained()`, `longText submitted_data`, `string submitted_by_name`, `string submitted_by_email`, `nullable string submitted_by_phone`, `string status` (default: `pending`), `text admin_notes` nullable, timestamps | High |
| 1.2 | - [ ] Create `App\Enums\SubmissionStatus` enum with cases `Pending`, `Approved`, `Rejected` | High |
| 1.3 | - [ ] Create `App\Models\ProductSubmission` with `HasUlids`, `$fillable`, `$casts = ['submitted_data' => 'array', 'status' => SubmissionStatus::class]`, `belongsTo User`, `scopePending()`, `scopeApproved()`, `scopeRejected()` | High |
| 1.4 | - [ ] Run `php artisan migrate` | High |

---

## Milestone 2: Public form (controller + route + view)

| # | Task | Priority |
|---|---|---|
| 2.1 | - [ ] Create `App\Http\Requests\StoreProductSubmissionRequest` with validation rules for all form fields | High |
| 2.2 | - [ ] Create `App\Http\Controllers\ProductSubmissionController` with `create()` and `store()` methods (follows `ContactController` pattern, uses the Form Request) | High |
| 2.3 | - [ ] Add routes in `routes/web.php`: `GET /products/create-submission` (`products.submission.create`) and `POST /products/create-submission` (`products.submission.store`) — placed **before** the `products.show` route to avoid slug conflicts | High |
| 2.4 | - [ ] Apply `throttle:3,60` middleware on the `POST` route for spam protection | Medium |
| 2.5 | - [ ] Create `resources/views/app/products/create-submission.blade.php` with Flowbite v4 classes containing sections: Basic Info, Classification, Active Ingredients (Alpine.js repeater), Diseases, Medical Details (Indications/Contraindications/Precautions/Side Effects repeater), Dosages (Alpine.js repeater), Withdrawal Periods (Alpine.js repeater), Media (image/document URLs) | High |
| 2.6 | - [ ] Add `pages.products.submission.*` entries in `lang/en/messages.php` (labels, placeholders, validation, success) | Medium |
| 2.7 | - [ ] Add Arabic translations in `lang/ar/messages.php` | Medium |

---

## Milestone 3: Admin Filament resource

| # | Task | Priority |
|---|---|---|
| 3.1 | - [ ] Create `ProductSubmissionResource` under `System` nav group with `heroicon-o-inbox-arrow-down` icon | High |
| 3.2 | - [ ] Create `Tables/ProductSubmissionsTable` with columns: submitter name, email, status (badge), created_at (date), searchable + filter by status | High |
| 3.3 | - [ ] Create `Schemas/ProductSubmissionInfolist` with sections showing all submitted data | High |
| 3.4 | - [ ] Create `Pages/ListProductSubmissions` (no CreateAction in header) | High |
| 3.5 | - [ ] Create `Pages/ViewProductSubmission` with Approve & Reject header actions | High |
| 3.6 | - [ ] Implement Approve action: modal confirmation → launches `ApproveProductSubmissionAction` → marks approved | High |
| 3.7 | - [ ] Implement Reject action: modal with `admin_notes` textarea → marks rejected | High |

---

## Milestone 4: Product creation action

| # | Task | Priority |
|---|---|---|
| 4.1 | - [ ] Create `App\Actions\ApproveProductSubmissionAction` that reads `submitted_data`, passes it to `ProductImportService::import()`, and creates Product + all relations inside a DB transaction | High |
| 4.2 | - [ ] On success: mark submission `Approved`, store created `product_id` on submission if needed | High |
| 4.3 | - [ ] On failure: catch exception, store error message on `admin_notes`, keep status `pending` | Medium |

---

## Milestone 5: Verify

| # | Task | Priority |
|---|---|---|
| 5.1 | - [ ] Run `php artisan migrate` to create the table | High |
| 5.2 | - [ ] Visit `/products/create-submission` and submit a complete form | High |
| 5.3 | - [ ] Check admin panel at `/admin/product-submissions` — verify record appears | High |
| 5.4 | - [ ] Approve the submission — verify Product + relations created in DB | High |
| 5.5 | - [ ] Reject a submission — verify status updates with notes | High |
| 5.6 | - [ ] Run `./vendor/bin/pint --format agent` for code style | Medium |
| 5.7 | - [ ] Run `npm run build` to verify frontend compiles | Medium |
