# Blog Feature — Implementation Plan

> No soft deletes. Filament admin is deferred.

---

## Milestone 1: Database Migrations

- [x] Create migration `create_blog_categories_table` (ULID id, name, name_ar, slug unique, description, description_ar, is_active bool default true, timestamps)
- [x] Create migration `create_blogs_table` (ULID id, blog_category_id FK cascade, title, title_ar, slug unique, excerpt, excerpt_ar, body longText, body_ar longText, cover_image nullable, author_id FK users nullable, published_at nullable, is_active bool default false, meta_title nullable, meta_description nullable, timestamps)
- [x] Run `php artisan migrate`

## Milestone 2: Models

- [x] Create `App\Models\BlogCategory` (HasUlids, HasFactory, fillable, `blogs()` hasMany Blog)
- [x] Create `App\Models\Blog` (HasUlids, HasFactory, fillable, casts for published_at/is_active, `category()` belongsTo BlogCategory, `author()` belongsTo User, `scopePublished()`)
- [x] Update `App\Models\User` with `blogs()` hasMany Blog (optional, deferred)

## Milestone 3: Factories & Seeders

- [x] Create `Database\Factories\BlogCategoryFactory`
- [x] Create `Database\Factories\BlogFactory`
- [x] Create `Database\Seeders\BlogSeeder` (3 categories + 10 sample published blogs)

## Milestone 4: Public Controller & Routes

- [x] Create `App\Http\Controllers\BlogController` with `index()` (paginated 20, search, category filter) and `show(Blog $blog)`
- [x] Add routes in `routes/web.php`: `GET /blog` (blog.index), `GET /blog/{blog:slug}` (blog.show)

## Milestone 5: Public Views

- [x] Create `resources/views/app/blog/index.blade.php` (extends master layout, card grid of blog posts, category filter, search, pagination)
- [x] Create `resources/views/app/blog/show.blade.php` (extends master layout, full article with cover image, breadcrumb, title, meta, body, category link)

## Milestone 6: Homepage

- [x] Update `HomeController@index`: query latest 3 published blogs with category, pass to view
- [x] Update `resources/views/app/home.blade.php`: add "Latest from Blog" card section below existing stats/products/companies

## Milestone 7: Navigation

- [x] Add "Blog" link in `main-header.blade.php` desktop nav bar (after Companies)
- [x] Add "Blog" link in `main-header.blade.php` mobile sidebar (after Companies, with `<x-lucide-newspaper />`)
- [x] Add "Blog" link in `footer.blade.php` (after Sitemap)
- [x] Add nav translation keys in `lang/en/messages.php` and `lang/ar/messages.php`

## Milestone 8: Sitemap

- [x] Update `SitemapController@index`: add `Blog::all()` to data
- [x] Update `resources/views/sitemap.blade.php`: add blog post URLs

## Milestone 9: Storage

- [x] Run `php artisan storage:link` (creates public symlink for all file uploads including blog cover images)

## Milestone 10: Testing

- [x] Run `php artisan test --compact` to confirm existing tests still pass
- [x] Manual verification: visit `/blog`, click a post, check homepage section, check nav links
