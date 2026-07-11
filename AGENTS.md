# Vetpedia Drugs — Agent Guide

**Language:** All communication (questions, answers, code comments) must be in **English only**.



A **Laravel 12 + Filament 5** veterinary drug database with public frontend and admin panel.

## Stack

| Layer | Choice |
|---|---|
| Backend | Laravel 12 (PHP 8.2+) |
| Admin | Filament 5 at `/admin` |
| DB dev | MySQL (`DB_CONNECTION=mysql`) |
| DB test | SQLite `:memory:` (see `phpunit.xml`) |
| Frontend | Blade + TailwindCSS v4 + Vite |
| Pagination | Bootstrap 5 (`Paginator::useBootstrap()` in `AppServiceProvider`) |
| IDs | **ULIDs** on all domain models (`HasUlids` trait) |
| Code style | Laravel Pint (`./vendor/bin/pint`) |

## Dev commands

```bash
composer setup          # full first-time setup (install, .env, key, migrate, npm build)
composer dev            # runs server + queue + logs + Vite concurrently
composer test           # config:clear then php artisan test
npm run dev             # Vite dev server
npm run build           # Vite production build
```

Run a single test:
```bash
php artisan test tests/Feature/ExampleTest.php
# or
./vendor/bin/phpunit tests/Feature/ExampleTest.php
```

## Architecture

### App entrypoints
- **Public routes** → `routes/web.php` — `/products`, `/companies`, `/diseases`, `/active-ingredients`, `/search`, sitemap
- **Admin panel** → `app/Providers/Filament/AdminPanelProvider.php` — path `/admin`, auto-discovers `app/Filament/Resources/`
- **Console commands** → `routes/console.php`

### Major directory ownership
```
app/
├── Actions/              # Single-action classes (ImportProductsAction)
├── Filament/
│   ├── Resources/        # 12 Filament admin resources
│   ├── Pages/            # Custom admin pages (DataQuality, Statistics)
├── Http/Controllers/     # Public frontend controllers (8 files)
├── Jobs/                 # Queue jobs (ProcessImportJob)
├── Models/               # 19 Eloquent models, all using HasUlids
├── Services/Imports/     # PDF → AI → normalize → validate → import
```

### Key models & relationships
- **Product** has: company, dosageForm, activeIngredients (w/ pivot: strength, unit), diseases, companies (w/ pivot: role), dosages, withdrawalPeriods, indications, contraindications, precautions, sideEffects, images, documents
- **Company** has type (`manufacturer`/`agent`/`distributor`) and self-referential `parent_company_id` for subsidiaries
- **Product–Company** pivot `product_company` has a `role` column (manufacturer/agent/distributor)
- **Product has helper scopes**: `manufacturer()`, `agent()`, `distributor()`
- Most models use `SoftDeletes` and `HasFactory`

### Import pipeline (notable)
1. PDF document uploaded via Filament → extracted to JSON (via AI) → stored on `ImportJob.extracted_json`
2. `ProcessImportJob` queued → `ImportProductsAction::execute()` → normalizer → validator → service writes to DB
3. ImportJob tracks: `total_products`, `imported_products`, `failed_products`, `error_message`

### Localization
Arabic fields (`name_ar`, `description_ar`, `trade_name_ar`, etc.) exist on most entities. Default locale is `en`.

## Testing quirks
- `phpunit.xml` sets `DB_CONNECTION=sqlite` + `DB_DATABASE=:memory:` — no external DB needed
- No `RefreshDatabase` trait used in tests yet (only ExampleTest exists)
- Session, cache, queue all use `array`/`sync` drivers in test env

## Dev environment
- `.env` uses MySQL (`vetpedia_drugs` database, root/no password)
- Session, cache, queue all default to `database` driver
- Mail defaults to `log` driver
- Debugbar available but disabled by default in `.env`
- No CI workflows exist

## Style conventions
- PSR-4: `App\` → `app/`, `Database\Factories\` → `database/factories/`, `Tests\` → `tests/`
- Filament Resources organized with `Schemas/`, `Tables/`, `Pages/` subdirectories per resource
- Use `php artisan filament:upgrade` after composer updates (runs automatically via `post-autoload-dump`)
- Don't commit `.env`, `vendor/`, `node_modules/`, `public/build/`
