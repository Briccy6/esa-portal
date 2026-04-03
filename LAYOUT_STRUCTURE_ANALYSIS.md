# ESA Codebase: Complete Layout & View Structure Analysis

**Date:** April 3, 2026  
**Analyst:** GitHub Copilot  
**Scope:** Complete directory structure of views and layouts

---

## 1. LAYOUT FILES DIRECTORY STRUCTURE

### 📁 resources/views/layouts/ (Main Shared Layouts)

| File | Usage | Size | Purpose |
|------|-------|------|---------|
| `app.blade.php` | ✅ ACTIVE | 60 lines | Generic app layout - used by public and some internal views |
| `client.blade.php` | ✅ ACTIVE | 80+ lines | Dark-themed client portal with sidebar (260px) |
| `public.blade.php` | ❌ UNUSED | 300+ lines | Modern public design system (NO @extends found) |
| `guest.blade.php` | ⚠️ MINIMAL | 30 lines | Guest/auth layout (only secretary login uses it) |
| `admin.blade.php` | ❌ UNUSED | - | Deprecated (uses admin/layout.blade.php instead) |
| `secretary.blade.php` | ❌ UNUSED | - | Deprecated (uses secretary/layout.blade.php instead) |
| `navigation.blade.php` | - | - | Navigation component (included by admin.layout) |
| `secretary-navigation.blade.php` | - | - | Navigation component (included by secretary.layout) |

### 📁 resources/views/admin/ (Admin Portal)

**Primary Layout:** `admin/layout.blade.php`
- **Features:** Sidebar (280px) + top navbar + main content
- **Includes:** 
  - `components.sidebar` (admin navigation)
  - `layouts.navigation` (top nav)
- **Used by:** 95+ admin views covering:
  - Dashboard, accounting, courses, staff, teachers, fees, payments, etc.
  - All sub-directory views (accounting/, assignments/, live-classes/, etc.)

### 📁 resources/views/staff/ (Staff & Support Roles)

#### `staff/layout.blade.php`
- **Used by:** accountant/* views (3 views)
- **Features:** Top navbar (gradient), sidebar, Bootstrap-based
- **Structure:** Full page layout with navigation

#### `staff/partials/sidebar_dos.blade.php` ⚠️ IMPORTANT
- **NOT just a sidebar** - This is a FULL PAGE LAYOUT
- **Used by:** 
  - `dos/lessons/*` (index, create)
  - `exams/dos_*` (index, create, edit)
  - `staff/dashboard_dos.blade.php`
- **Features:**
  - Fixed sidebar (280px) with purple gradient
  - Top navigation bar
  - Collapsible sidebar (#sidebar ID)
  - Main content area (#main-content)
  - Complete HTML structure

#### `staff/partials/sidebar_sec.blade.php` ⚠️ IMPORTANT
- **NOT just a sidebar** - This is a FULL PAGE LAYOUT
- **Used by:**
  - `babyeyi/index.blade.php` (list)
  - `babyeyi/show.blade.php` (view)
  - `babyeyi/edit.blade.php` (edit)
  - `babyeyi/create.blade.php` (create)
  - `babyeyi/preview.blade.php`
  - `secretary/dashboard.blade.php`
- **Features:**
  - Fixed sidebar (260px) with blue gradient  
  - Top navigation bar
  - Collapsible sidebar with toggle button
  - Main content area with left margin offset
  - Complete HTML structure

### 📁 resources/views/teacher/ 

**Layout:** `teacher/layout.blade.php`
- **Used by:** 20+ teacher views including:
  - `teacher/dashboard.blade.php`
  - `teacher/courses/*`, `teacher/marks/*`, `teacher/exams/*`
  - `teacher/questions/*`, `teacher/resources/*`, `teacher/dailies/*`
  - `teacher/question-banks/*`, `teacher/live-classes/*`
- **Features:** Similar to staff layout with teacher-specific navigation

### 📁 resources/views/dos/

**Layout:** `dos/layout.blade.php`
- **Used by:** `dos/lessons/*` views (2 views)
- **Note:** Views here also extend `dos.layout` directly
- **Alternative:** Some views use `staff/partials/sidebar_dos.blade.php`

### 📁 resources/views/secretary/

**Layout:** `secretary/layout.blade.php`
- **Used by:**
  - `secretary/dashboard.blade.php`
  - `secretary/student-registration/*` (index, show, edit)
- **Features:** Basic layout similar to staff layout

### 📁 resources/views/accountant/

**Layout:** `accountant/layout.blade.php`
- **Status:** EXISTS but NOT USED
- **Note:** Accountant views use `staff.layout` instead
- **Contains:** 3 views (course-payment-details, payment-schedule, student-payment-details)

### 📁 resources/views/students/

**Layout:** `students/app.blade.php` (NOT students/layout.blade.php)
- **Used by:** 20+ student-facing views
- **Includes:**
  - Student dashboard
  - Grades, exams, courses, report cards
  - Payment views, invoices
  - Attendance, profile, resources
- **Features:** Bootstrap-based, responsive design
- **Associated Views:**
  - `to-whom/student-*` (show, index, create)
  - `tokens/dashboard.blade.php`
  - `babyeyi/student-*`
  - `exams/disqualified.blade.php`

---

## 2. PUBLIC VIEWS LOCATION & STRUCTURE

### 📁 resources/views/public/ (Publicly Accessible Views)

All public views use `@extends('layouts.app')`

#### Subdirectories:

1. **public/exams/** (5 files)
   - `index.blade.php` - List public exams
   - `show.blade.php` - View exam details
   - `take.blade.php` - Take an exam
   - `results.blade.php` - View exam results
   - `payment-success.blade.php` - Payment confirmation

2. **public/subscriptions/** (5 files)
   - `index.blade.php` - List subscriptions
   - `show.blade.php` - Subscription details
   - `login.blade.php` - Subscriber login
   - `register.blade.php` - New subscriber registration
   - `purchase.blade.php` - Subscription purchase
   - `dashboard.blade.php` - Subscriber dashboard

3. **public/tokens/** (2 files)
   - `index.blade.php` - View token balance
   - `purchase.blade.php` - Buy tokens

4. **public/resources/** (subdirectory for resources)

### 📁 resources/views/babyeyi/ (Baby EYI Project)

Mixed layout usage:
- **Public views:** `babyeyi/public-*.blade.php` → `@extends('layouts.app')`
  - `public-index.blade.php`
  - `public-show.blade.php`
  
- **Student views:** `babyeyi/student-*.blade.php` → `@extends('students.app')`
  - `student-dashboard.blade.php`
  - `student-show.blade.php`
  
- **Admin views:** `babyeyi/*.blade.php` → `@extends('staff.partials.sidebar_sec')`
  - `index.blade.php` (list)
  - `show.blade.php` (view)
  - `edit.blade.php` (edit)
  - `create.blade.php` (create)
  - `preview.blade.php` (preview)
  - `pdf.blade.php` (PDF export)

---

## 3. LAYOUT USAGE MATRIX

### By Layout File:

```
layouts/app.blade.php (12+ views)
├── public/exams/* (5)
├── public/subscriptions/* (5)
├── public/tokens/* (2)
├── babyeyi/public-* (2)
├── auth/register-choice.blade.php
├── tokens/mobile-money.blade.php
└── admin/assignments/* (some)

layouts/client.blade.php (10 views)
├── client/dashboard.blade.php
├── client/exams/* (3 - index, show, take)
├── client/subscriptions/* (2 - index, purchase)
├── client/tokens/* (2 - index, purchase)
├── client/profile/edit.blade.php
└── client/settings/index.blade.php

admin/layout.blade.php (95+ views)
├── admin/* (all subdirectories)
├── youtube/upload.blade.php
└── transactions/history.blade.php

staff/layout.blade.php (3 views)
├── accountant/course-payment-details.blade.php
├── accountant/payment-schedule.blade.php
└── accountant/student-payment-details.blade.php

teacher/layout.blade.php (20+ views)
├── teacher/* (all subdirectories)
└── teacher/resources/*, courses/*, marks/*, etc.

staff/partials/sidebar_dos.blade.php (6 views)
├── dos/lessons/* (2)
├── exams/dos_* (3)
└── staff/dashboard_dos.blade.php

staff/partials/sidebar_sec.blade.php (8+ views)
├── babyeyi/* (5 main + variants)
├── secretary/dashboard.blade.php
└── secretary/student-registration/*

students/app.blade.php (20+ views)
├── students/* (all)
├── to-whom/student-* (3)
├── tokens/dashboard.blade.php
├── babyeyi/student-* (2)
└── exams/disqualified.blade.php

dos/layout.blade.php (2 views)
├── dos/lessons/* (2)

secretary/layout.blade.php (4 views)
├── secretary/dashboard.blade.php
├── secretary/student-registration/* (3)

layouts/guest.blade.php (1 view)
└── secretary/login.blade.php
```

---

## 4. KEY STRUCTURAL INSIGHTS

### 🔍 Secondary Sidebar Layouts Are Full Page Layouts

**Important Finding:** Files named `sidebar_*.blade.php` in `staff/partials/` are NOT just sidebars:

```html
<!-- These are FULL page layouts with HTML structure: -->
<!DOCTYPE html>
<html>
  <head>...</head>
  <body>
    <div id="sidebar">...</div>    <!-- Fixed sidebar (280px or 260px) -->
    <div id="main-content">        <!-- Main content area -->
      @yield('content')
    </div>
  </body>
</html>
```

**Better naming would be:**
- `staff/partials/sidebar_dos.blade.php` → `dos_layout.blade.php`
- `staff/partials/sidebar_sec.blade.php` → `secretary_layout.blade.php`

### 🎨 Sidebar Width Standards

| Layout | Width | Collapsed | Color/Theme |
|--------|-------|-----------|-------------|
| Admin sidebar | 280px | 70px | Blue (#2c3e50) |
| Client sidebar | 260px | 60px | Dark with gradients |
| DOS sidebar | 280px | 70px | Purple gradient |
| Secretary sidebar | 260px | 70px | Blue gradient |
| Teacher sidebar | 280px | 70px | Similar to admin |

### 📐 Layout Content Section Names

Most layouts follow this pattern:
```blade
<!-- Yield pattern -->
@yield('content')      <!-- Main content -->
@yield('title')        <!-- Page title -->
@yield('description')  <!-- Meta description -->

<!-- Stack pattern (for CSS/JS) -->
@stack('styles')       <!-- CSS additions -->
@stack('scripts')      <!-- JS additions -->
```

### 🚫 Unused/Deprecated Layouts

Files that exist but have NO views using them:
- `layouts/public.blade.php` - Exists with modern design, but no @extends
- `layouts/admin.blade.php` - Uses admin/layout.blade.php instead
- `layouts/secretary.blade.php` - Uses secretary/layout.blade.php instead
- `accountant/layout.blade.php` - Accountant views use staff.layout
- `students/layout` - Referenced in code but file doesn't exist

---

## 5. COMPONENT INCLUDES BY LAYOUT

### Admin Layout Includes:
```blade
@include('components.sidebar')       <!-- Admin navigation sidebar -->
@include('layouts.navigation')       <!-- Top navigation bar -->
@yield('content')                    <!-- Main content area -->
```

### Public Layout Includes:
```blade
@include('components.public-navigation')   <!-- Public nav -->
@include('components.live-streaming-banner') <!-- Banner -->
@yield('content')                          <!-- Main content -->
@include('components.footer')              <!-- Footer -->
```

### Client Layout Includes:
```blade
<!-- Custom sidebar structure in layout -->
<!-- Top navigation bar -->
@yield('content')
<!-- No footer component includes -->
```

---

## 6. NAVIGATION COMPONENTS

Located in `resources/views/layouts/`:

- `navigation.blade.php` - Included in admin.layout
- `secretary-navigation.blade.php` - Included in secretary.layout

Located in `resources/views/components/`:

- `public-navigation.blade.php` - For public portal
- `public-navbar.blade.php` - Alternative public nav
- `sidebar.blade.php` - Admin sidebar (white, 256px wide)
- `footer.blade.php` - Public footer
- `live-streaming-banner.blade.php` - Live stream promo

---

## 7. LAYOUT SELECTION GUIDE

### Use this layout when:

| Need | Use | Location |
|------|-----|----------|
| General app view | `layouts.app` | resources/views/layouts/ |
| Admin panel | `admin.layout` | resources/views/admin/ |
| Client/dark portal | `layouts.client` | resources/views/layouts/ |
| Student dashboard | `students.app` | resources/views/students/ |
| Teacher panel | `teacher.layout` | resources/views/teacher/ |
| DOS/Academic | `dos.layout` | resources/views/dos/ |
| Secretary panel | `secretary.layout` | resources/views/secretary/ |
| Guest/Auth | `layouts.guest` | resources/views/layouts/ |
| Public access | `layouts.public` (unused) | - |

---

## 8. VIEW TO LAYOUT MAPPING SUMMARY TABLE

```
┌─────────────────────────────────────────────────┬──────────────────────┐
│ View Directory                                  │ Layout Used          │
├─────────────────────────────────────────────────┼──────────────────────┤
│ resources/views/public/*                        │ layouts.app          │
│ resources/views/client/*                        │ layouts.client       │
│ resources/views/admin/*                         │ admin.layout         │
│ resources/views/students/*                      │ students.app         │
│ resources/views/teacher/*                       │ teacher.layout       │
│ resources/views/dos/*                           │ dos.layout           │
│ resources/views/secretary/*                     │ secretary.layout     │
│ resources/views/accountant/*                    │ staff.layout         │
│ resources/views/babyeyi/public-*                │ layouts.app          │
│ resources/views/babyeyi/student-*               │ students.app         │
│ resources/views/babyeyi/admin-*                 │ sidebar_sec          │
│ resources/views/youtube/upload.blade.php        │ admin.layout         │
│ resources/views/tokens/*                        │ layouts.app/app      │
│ resources/views/to-whom/student-*               │ students.app         │
│ resources/views/exams/dos_*                     │ dos.layout           │
│ resources/views/attendance/mark.blade.php       │ sidebar_dos          │
└─────────────────────────────────────────────────┴──────────────────────┘
```

---

## 9. RECOMMENDED IMPROVEMENTS

### 1. Rename Confusing Sidebar Files
```
staff/partials/sidebar_dos.blade.php → dos/layout.blade.php
staff/partials/sidebar_sec.blade.php → secretary_layout_alt.blade.php
```

### 2. Remove Unused Layouts
- Delete `layouts/public.blade.php` or use it for public views
- Delete `accountant/layout.blade.php` (not used)
- Clean up `layouts/admin.blade.php` (deprecated)

### 3. Fix Dead Code
- Remove `@extends('students.layout')` references (file doesn't exist)
- Consolidate DOS layout options

### 4. Document Layout Selections
Create a config file documenting which layout serves which role/view type

---

## 10. ROLE-BASED ACCESS PATTERNS

| User Role | Primary Layout | Alternative | Views Count |
|-----------|----------------|-------------|-------------|
| Admin | admin.layout | - | 95+ |
| Teacher | teacher.layout | - | 20+ |
| Student | students.app | - | 20+ |
| DOS | dos.layout, sidebar_dos | - | 6+ |
| Secretary | secretary.layout, sidebar_sec | - | 8+ |
| Accountant | staff.layout | - | 3 |
| Public User | layouts.app | layouts.client | 12+ |
| Client | layouts.client | - | 10 |
| Guest | layouts.guest | - | 1 |

---

## 11. FILES NOT LINKED TO ANY LAYOUT

These views don't extend from layouts (full page views or components):

- Error pages
- Email templates (resources/views/emails/)
- Partials (not standalone)
- Some test/debug views

---

**End of Analysis**

*This document provides a complete map of the ESA codebase layout structure for developers working on layout modifications, refactoring, or new feature implementation.*
