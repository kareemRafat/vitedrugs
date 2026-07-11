# Localization — mcamara/laravel-localization (en/ar)

> Install and configure the mcamara package, add language switcher, translate all Blade UI strings, and add localized model accessors.
> Locale prefix strategy: `/en/products`, `/ar/products` (both prefixed).

---

## Prerequisites

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 0.1 | Run `composer require mcamara/laravel-localization` | High | None | - [ ] |
| 0.2 | Publish config: `php artisan vendor:publish --provider="Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider"` | High | 0.1 | - [ ] |
| 0.3 | Edit `config/laravellocalization.php` — set `supportedLocales` to `en` + `ar`, `hideDefaultLocaleInURL` to `false`, `useAcceptLanguageHeader` to `false` | High | 0.2 | - [ ] |
| 0.4 | Set `APP_LOCALE=en` and `APP_FALLBACK_LOCALE=en` in `.env` | High | None | - [ ] |
| 0.5 | Create `lang/en/` and `lang/ar/` directories with `messages.php` files | High | None | - [ ] |
| 0.6 | Register middleware aliases in `bootstrap/app.php` (localize, localizationRedirect, localeSessionRedirect, localeViewPath) | High | 0.1 | - [ ] |
| 0.7 | Add `LoadsTranslatedCachedRoutes` trait to `AppServiceProvider` for route cache support | Medium | 0.1 | - [ ] |

---

## Step 1: Restructure routes

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 1.1 | Wrap all public GET routes in `Route::group(['prefix' => LaravelLocalization::setLocale(), ...])` | High | 0.6 | - [ ] |
| 1.2 | Keep `admin/*`, `/sitemap.xml`, `/robots.txt` outside the locale group | High | 1.1 | - [ ] |
| 1.3 | Localize POST routes for login, register, logout inside the group | High | 1.1 | - [ ] |
| 1.4 | Update route references in controllers if needed | Medium | 1.1 | - [ ] |

---

## Step 2: Language files — UI strings

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 2.1 | Create `lang/en/messages.php` — all English UI strings (nav, auth forms, buttons, headings, placeholders, footer, empty states, search, pages) | High | 0.5 | - [ ] |
| 2.2 | Create `lang/ar/messages.php` — Arabic translations for all keys | High | 2.1 | - [ ] |

---

## Step 3: Translate Blade views

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 3.1 | Translate `app/layouts/main-header.blade.php` — nav links, dropdown, sign-in button | High | 2.1 | - [ ] |
| 3.2 | Translate `app/layouts/main-sidebar.blade.php` — section labels, all nav items | High | 2.1 | - [ ] |
| 3.3 | Translate `app/layouts/footer.blade.php` — links, copyright line | Medium | 2.1 | - [ ] |
| 3.4 | Translate `app/layouts/guest.blade.php` — app name + footer text | Medium | 2.1 | - [ ] |
| 3.5 | Translate `app/auth/login.blade.php` — heading, email, password, remember, button, link | High | 2.1 | - [ ] |
| 3.6 | Translate `app/auth/register.blade.php` — heading, name, email, password, confirm, button, link | High | 2.1 | - [ ] |
| 3.7 | Translate `app/welcome.blade.php` — welcome text, buttons | Medium | 2.1 | - [ ] |
| 3.8 | Translate `app/home.blade.php` — hero, stat card titles, section headings, empty states | Medium | 2.1 | - [ ] |
| 3.9 | Translate `app/products/index.blade.php` — page title, search, table headers, filter labels, pagination, empty state | High | 2.1 | - [ ] |
| 3.10 | Translate `app/products/show.blade.php` — tabs, table headers, sidebar sections, action labels | High | 2.1 | - [ ] |
| 3.11 | Translate `app/companies/index.blade.php` — page title, search, table, empty state | High | 2.1 | - [ ] |
| 3.12 | Translate `app/companies/show.blade.php` — info labels, table, sidebar sections | High | 2.1 | - [ ] |
| 3.13 | Translate `app/diseases/index.blade.php` — page title, search, table, empty state | High | 2.1 | - [ ] |
| 3.14 | Translate `app/diseases/show.blade.php` — info labels, related products, sidebar | High | 2.1 | - [ ] |
| 3.15 | Translate `app/active-ingredients/index.blade.php` — page title, table, empty state | High | 2.1 | - [ ] |
| 3.16 | Translate `app/active-ingredients/show.blade.php` — info labels, tables, sidebar sections | High | 2.1 | - [ ] |
| 3.17 | Translate `app/search/index.blade.php` — search placeholder, result labels, empty state | Medium | 2.1 | - [ ] |
| 3.18 | Translate `app/pages/*.blade.php` — static page content (about, contact, privacy, terms) | Medium | 2.1 | - [ ] |
| 3.19 | Translate `app/errors/404.blade.php` — error message, home link | Low | 2.1 | - [ ] |
| 3.20 | Translate `app/empty.blade.php` — placeholder content | Low | 2.1 | - [ ] |

---

## Step 4: Model localized accessors

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 4.1 | Add `getNameAttribute()` accessor to **ActiveIngredient** — returns `name_ar ?? name` when locale is `ar` | High | 0.4 | - [ ] |
| 4.2 | Add `getNameAttribute()` + `getDescriptionAttribute()` to **Company** + `getAddressAttribute()` | High | 0.4 | - [ ] |
| 4.3 | Add `getDescriptionAttribute()` to **Contraindication**, **Indication**, **Precaution**, **SideEffect** | High | 0.4 | - [ ] |
| 4.4 | Add `getNameAttribute()` + `getDescriptionAttribute()` to **Disease**, **DosageForm**, **DrugClass**, **Species** | High | 0.4 | - [ ] |
| 4.5 | Add `getTradeNameAttribute()` + `getDescriptionAttribute()` to **Product** | High | 0.4 | - [ ] |
| 4.6 | Replace all `->name`, `->name_ar`, `->description`, `->description_ar`, `->trade_name`, `->trade_name_ar`, `->address_ar` references in Blade views with `->name`, `->description`, etc. (accessors handle locale automatically) | High | 4.1–4.5 | - [ ] |

---

## Step 5: Language switcher component

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 5.1 | Create `resources/views/app/components/language-switcher.blade.php` — dropdown using `LaravelLocalization::getSupportedLocales()` and `getLocalizedURL()` with native names + flag | High | 0.3 | - [ ] |
| 5.2 | Include switcher in `app/layouts/main-header.blade.php` — next to user menu / nav links | High | 5.1 | - [ ] |
| 5.3 | Include switcher in `app/layouts/footer.blade.php` — alongside footer links | Medium | 5.1 | - [ ] |
| 5.4 | Add `dir="rtl"` class/attribute support for Arabic layout (optional but nice) | Low | 5.1 | - [ ] |

---

## Step 6: Verify

| # | Task | Priority | Dependencies | Done |
|---|---|---|---|---|
| 6.1 | Run `php artisan route:trans:list en` — verify all English routes resolve | High | 1.1 | - [ ] |
| 6.2 | Run `php artisan route:trans:list ar` — verify all Arabic routes resolve | High | 1.1 | - [ ] |
| 6.3 | Visit `/en/products` and `/ar/products` in browser — both load, language switcher works | High | 5.2 | - [ ] |
| 6.4 | Run `npm run build` | High | 3.1–3.20 | - [ ] |
| 6.5 | Run `./vendor/bin/pint` | High | 4.1–4.6 | - [ ] |
| 6.6 | Run `composer test` | Medium | All | - [ ] |
