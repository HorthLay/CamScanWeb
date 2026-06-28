# CamWeb

A Laravel 13 Camera Scan management system with biometric-style UI, custom authentication, and dynamic role-based tab access control.

---

## Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.3+ |
| Frontend | Blade, Tailwind CSS (CDN), Material Symbols |
| Database | MySQL |
| Auth | Custom (name + password, no email) |
| Fonts | Space Grotesk, JetBrains Mono |

---

## Requirements

- PHP 8.3+
- Composer
- MySQL
- Node.js (optional, only if you switch to Vite)

---

## Installation

```bash
# 1. Clone the repo
git clone <your-repo-url> camweb
cd camweb

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Configure your database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=CamWeb
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations and seed
php artisan migrate:fresh --seed

# 7. Start the server
php artisan serve
```

Visit `http://127.0.0.1:8000`

---

## Default Users

| Name | Password | Role | Active |
|---|---|---|---|
| `admin` | `password` | Admin | ✅ |
| `jane` | `password` | Staff | ✅ |
| `john` | `password` | Staff | ❌ |

---

## Database Schema

```
users
├── id
├── name
├── gender          enum: male, female, other
├── password
├── active          boolean, default: false
├── role_id         FK → roles.id (nullable)
├── remember_token
└── timestamps

roles
├── id
├── name            unique
├── slug            unique
├── description
└── timestamps

tabs
├── id
├── name
├── slug            unique
├── icon            Material Symbol name
├── route           Laravel named route
├── order
└── timestamps

role_tab            pivot
├── role_id         FK → roles.id
└── tab_id          FK → tabs.id
```

---

## Default Tabs

| Tab | Slug | Route | Order |
|---|---|---|---|
| Dashboard | `dashboard` | `dashboard` | 1 |
| Attendance | `attendance` | `attendance` | 2 |
| Reports | `reports` | `reports` | 3 |
| Users | `users` | `users` | 4 |
| Settings | `settings` | `settings` | 5 |
| Role Management | `role-management` | `roles.index` | 6 |

---

## Authentication Flow

```
POST /login
  → validate name + password fields
  → check user exists by name
  → check user.active === true       ← blocked if false
  → Auth::attempt()                  ← verify password
  → session regenerate
  → redirect → /dashboard

Every protected request
  → auth middleware: must be logged in
  → active middleware: re-checks active on every request
    (catches mid-session deactivations immediately)
```

---

## Role-Based Tab Access

Access control works at two levels:

**Middleware** — protects routes:
```php
Route::middleware('tab.access:reports')->group(function () {
    Route::get('/reports', ...)->name('reports');
});
```

**Blade** — renders only accessible nav items:
```blade
@foreach(auth()->user()->accessibleTabs() as $tab)
    <a href="{{ route($tab->route) }}">{{ $tab->name }}</a>
@endforeach
```

**User helper methods:**
```php
$user->canAccess('reports');      // bool
$user->accessibleTabs();          // Collection of Tab models sorted by order
```

---

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── LoginController.php
│   │   ├── Admin/
│   │   │   └── RoleController.php
│   │   └── DashboardController.php
│   └── Middleware/
│       ├── EnsureUserIsActive.php
│       └── CheckTabAccess.php
├── Models/
│   ├── User.php
│   ├── Role.php
│   └── Tab.php

bootstrap/
└── app.php                        ← middleware aliases registered here

database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   └── 2026_xx_xx_create_roles_table.php
├── seeders/
│   └── DatabaseSeeder.php
└── factories/
    └── UserFactory.php

resources/views/
├── layouts/
│   └── app.blade.php              ← sidebar layout
├── auth/
│   └── login.blade.php
├── dashboard.blade.php
└── coming-soon.blade.php

routes/
└── web.php
```

---

## Adding a New Tab / Page

**1. Add the route in `routes/web.php`:**
```php
Route::middleware('tab.access:my-tab')->group(function () {
    Route::get('/my-tab', [MyTabController::class, 'index'])->name('my-tab');
});
```

**2. Create the view extending the layout:**
```blade
@extends('layouts.app')
@section('title', 'My Tab')
@section('page-title', 'My Tab')

@section('content')
    {{-- page content --}}
@endsection
```

**3. Insert the tab into the database:**
```php
Tab::create([
    'name'  => 'My Tab',
    'slug'  => 'my-tab',
    'icon'  => 'star',          // any Material Symbol name
    'route' => 'my-tab',
    'order' => 7,
]);
```

**4. Assign the tab to roles via the Role Management UI or:**
```php
$role->tabs()->attach($tab->id);
```

The tab will appear in the sidebar automatically for any role it is assigned to.

---

## Middleware Reference

| Alias | Class | Purpose |
|---|---|---|
| `active` | `EnsureUserIsActive` | Blocks inactive users, logs them out |
| `tab.access:{slug}` | `CheckTabAccess` | Checks role has access to the given tab slug |

---

## Role Management (Admin)

Roles and their tab assignments are managed at runtime — no code changes needed.

| Action | Route | Method |
|---|---|---|
| List roles | `GET /admin/roles` | `RoleController@index` |
| Create role | `POST /admin/roles` | `RoleController@store` |
| Update role | `PUT /admin/roles/{role}` | `RoleController@update` |
| Delete role | `DELETE /admin/roles/{role}` | `RoleController@destroy` |
| Sync tabs | `POST /admin/roles/{role}/tabs` | `RoleController@syncTabs` |

Send `tab_ids[]` array to `syncTabs` to replace all tab assignments for that role.

---

## Laravel 13 Features Used

- PHP Attributes on models (`#[Fillable]`, `#[Hidden]`, `#[Casts]`)
- Attribute-free `bootstrap/app.php` middleware registration (no `Kernel.php`)
- Streamlined application skeleton (no `app/Http/Kernel.php`)

---

## License

Internal use only.