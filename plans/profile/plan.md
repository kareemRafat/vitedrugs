# User Profile — Implementation Plan

> Routes: `GET /profile`, `GET /profile/edit`, `PUT /profile`, `GET /profile/security`, `PUT /profile/security`, `GET /profile/submissions`
> Controller: `ProfileController`
> Views: `app/profile/{show,edit,security,submissions}.blade.php`

---

## Milestone 1: Backend — Routes & Controller

- [x] Add profile routes inside the existing `auth` middleware group in `routes/web.php`:
  - `GET /profile` → `profile.show`
  - `GET /profile/edit` → `profile.edit`
  - `PUT /profile` → `profile.update`
  - `GET /profile/security` → `profile.security`
  - `PUT /profile/security` → `profile.security.update`
  - `GET /profile/submissions` → `profile.submissions`
- [x] Create `app/Http/Controllers/ProfileController.php` with 6 methods:
  - `show()` — renders profile overview
  - `edit()` — renders edit form
  - `update(Request)` — validates & updates name/email (unique check ignores current user)
  - `security()` — renders security form
  - `updateSecurity(Request)` — validates current password via `Hash::check`, updates with `Hash::make`
  - `submissions()` — queries `Product::withoutGlobalScope('approved')->where('created_by', auth()->id())->latest()->paginate(15)`
- [x] Add `products()` HasMany relationship to `User` model
- [x] Run `./vendor/bin/pint --format agent`

## Milestone 2: Views — Profile pages

- [x] Create `resources/views/app/profile/partials/sidebar.blade.php` — sidebar nav with 4 links, active state (brand left border + brand-tinted bg), Lucide icons, dark mode
- [x] Create `resources/views/app/profile/show.blade.php` — gradient header, avatar circle, user info card, email verification badge, quick action buttons
- [x] Create `resources/views/app/profile/edit.blade.php` — form with name + email inputs, Lucide icon prefixes, validation errors, success flash, cancel button
- [x] Create `resources/views/app/profile/security.blade.php` — form with current_password, new password, confirm password, validation errors, success flash
- [x] Create `resources/views/app/profile/submissions.blade.php` — product table (trade name, status badge with icon, submitted/reviewed dates, view link), admin notes row, empty state with CTA, pagination

## Milestone 3: Navigation — Header updates

- [x] Add "My Profile" link (`route('profile.show')`) to the desktop user dropdown in `main-header.blade.php` (above sign-out)
- [x] Add same link to the mobile sidebar authenticated section in `main-header.blade.php`

## Milestone 4: Localization

- [x] Add `'profile' => [...]` section to `lang/en/messages.php` with all labels, headings, flash messages, status labels, nav strings
- [x] Add Arabic translations to `lang/ar/messages.php`

## Milestone 5: Verify

- [x] Run `composer test` — 1 passed, 7 skipped (no regressions)
- [x] Run `./vendor/bin/pint --format agent` — passed
- [x] Route list verified — 6 profile routes registered correctly
- [x] Manual check: User model, controller, views, header, localization all verified
