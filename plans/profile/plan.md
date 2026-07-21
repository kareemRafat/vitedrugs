# User Profile — Implementation Plan

> Routes: `GET /profile`, `GET /profile/edit`, `PUT /profile`, `GET /profile/security`, `PUT /profile/security`, `GET /profile/submissions`
> Controller: `ProfileController`
> Views: `app/profile/{show,edit,security,submissions}.blade.php`

---

## Milestone 1: Backend — Routes & Controller

- [ ] Add profile routes inside the existing `auth` middleware group in `routes/web.php`:
  - `GET /profile` → `profile.show`
  - `GET /profile/edit` → `profile.edit`
  - `PUT /profile` → `profile.update`
  - `GET /profile/security` → `profile.security`
  - `PUT /profile/security` → `profile.security.update`
  - `GET /profile/submissions` → `profile.submissions`
- [ ] Create `app/Http/Controllers/ProfileController.php` with 6 methods:
  - `show()` — renders profile overview
  - `edit()` — renders edit form
  - `update(Request)` — validates & updates name/email (unique check ignores current user)
  - `security()` — renders security form
  - `updateSecurity(Request)` — validates current password via `Hash::check`, updates with `Hash::make`
  - `submissions()` — queries `Product::withoutGlobalScope('approved')->where('created_by', auth()->id())->latest()->paginate(15)`
- [ ] Run `./vendor/bin/pint --format agent`

## Milestone 2: Views — Profile pages

- [ ] Create `resources/views/app/profile/partials/sidebar.blade.php` — sidebar nav with links to all 4 profile pages, active state highlighting, uses Flowbite v4 classes
- [ ] Create `resources/views/app/profile/show.blade.php` — user info card (name, email, member since, email verification badge), quick action buttons to edit/security/submissions
- [ ] Create `resources/views/app/profile/edit.blade.php` — form with name + email inputs, validation errors display, success flash message, cancel link back to profile
- [ ] Create `resources/views/app/profile/security.blade.php` — form with current_password, password, password_confirmation, validation errors, success flash
- [ ] Create `resources/views/app/profile/submissions.blade.php` — table of products (trade_name link to public page, status badge with color, submitted_at, reviewed_at, admin_notes), empty state message

## Milestone 3: Navigation — Header updates

- [ ] Add "Profile" link (`route('profile.show')`) to the desktop user dropdown in `main-header.blade.php` (above sign-out)
- [ ] Add same link to the mobile sidebar authenticated section in `main-header.blade.php`

## Milestone 4: Localization

- [ ] Add `'profile' => [...]` section to `lang/en/messages.php` with all labels, headings, flash messages, status labels
- [ ] Add Arabic translations to `lang/ar/messages.php`

## Milestone 5: Verify

- [ ] Run `composer test` to ensure no regressions
- [ ] Run `./vendor/bin/pint --format agent`
- [ ] Manual check: visit each profile page, submit forms, verify validation, check submissions list
