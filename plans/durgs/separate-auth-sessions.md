# Separate Auth Sessions for App & Admin

## Milestone 1: Auth Config

- [x] Add `admin` guard in `config/auth.php` (same provider, session driver)
- [x] Add `authGuard('admin')` on Filament panel — tells Filament to use `admin` guard

## Milestone 2: Public Controllers (explicit `web` guard)

- [x] `LoginController` — `Auth::guard('web')` on attempt/user/logout
- [x] `RegisterController` — `Auth::guard('web')->login()`
- [x] `ProfileController` — `Auth::guard('web')` on all `Auth::user()` calls

## Milestone 3: Admin Middleware

- [x] `EnsureUserIsAdmin` — use `Auth::guard('admin')` instead of default

## Milestone 4: Public Routes

- [x] `routes/web.php` — change `middleware('auth')` to `middleware('auth:web')`

## Milestone 5: Session isolation fix

- [x] `invalidate()` → `regenerate()` on all logout paths — preserves other guard's auth state

## Milestone 6: Verify

- [x] Run `php artisan config:clear`
- [x] Run `php artisan route:clear`
- [x] Run `composer test` to confirm nothing broke
