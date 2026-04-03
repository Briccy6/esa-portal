# Student Report Cards Diagnostic Report

## Current State Analysis

### Issue Summary
The student report cards page shows "Access Restricted - Report card access not configured by administration" and "No Report Cards Available" because:

1. **Missing or Inactive ReportCardSetting Record**
2. **No ReportCard Records in Database**
3. **Application Configuration Not Set**

---

## Root Cause Analysis

### Problem #1: ReportCardService Cannot Find Active Settings
**Location:** `ReportCardService::getAccessStatus()` (app/Services/ReportCardService.php, line 49-52)

```php
if (!$setting) {
    $reason = 'Report card access not configured by administration';
}
```

**Why:** The `ReportCardSetting::getActive()` method returns NULL because:

**Code in ReportCardSetting Model (line 26-31):**
```php
public static function getActive()
{
    return self::where('academic_year', config('app.academic_year', '2025-2026'))
                ->where('term', config('app.current_term', 'Term 1'))
                ->where('is_active', true)
                ->first();  // Returns NULL if no match found
}
```

**Issues Found:**
- Config keys 'app.academic_year' and 'app.current_term' are **NOT SET** in .env or config files
- Fallback values used: academic_year='2025-2026', term='Term 1'
- Query looks for a ReportCardSetting record with these exact values AND is_active=true
- If no such record exists, getActive() returns NULL

---

### Problem #2: No Report Cards Available
**Location:** `StudentController::showReportCards()` (line 2238-2240)

```php
$reportCards = \App\Models\ReportCard::where('student_id', $student->id)
    ->where('status', 'approved')
    ->with(['academicYear', 'term', 'course'])
    ->orderByDesc('created_at')
    ->get();
```

**Why:** Either:
- No ReportCard records exist in the database
- OR ReportCard records exist but don't have status='approved'
- OR ReportCard records exist for different students

---

## What Should Be Done

### Step 1: Check If ReportCardSetting Record Exists
Run this in PHP Artisan Tinker:
```php
php artisan tinker

// Check all records
$allSettings = App\Models\ReportCardSetting::all();
$allSettings->count();  // Should be > 0

// Check what we're looking for
App\Models\ReportCardSetting::where('academic_year', '2025-2026')
    ->where('term', 'Term 1')
    ->where('is_active', true)
    ->first();  // Should NOT be NULL
```

### Step 2: Verify Configuration Values
```php
// In tinker:
config('app.academic_year');  // Should return something like '2025-2026'
config('app.current_term');   // Should return something like 'Term 1'
```

### Step 3: Check ReportCard Records
```php
// In tinker:
$student = App\Models\Student::first();
App\Models\ReportCard::where('student_id', $student->id)->count();
App\Models\ReportCard::where('student_id', $student->id)->get()->pluck('status');
```

### Step 4: Check Student's Outstanding Fees
```php
// In tinker:
$student = App\Models\Student::first();
$student->getOutstandingFees();  // Should be 0 to allow access
$student->hasOutstandingFees();  // Should be false
```

---

## Database Records Needed

### 1. ReportCardSetting Record
Must exist with these minimum values:
```
- academic_year: '2025-2026'  (matches config value)
- term: 'Term 1'              (matches config value)
- report_card_available_at: DateTime (past or future)
- is_active: true             (MUST BE TRUE)
```

**To create one:**
```php
php artisan tinker

App\Models\ReportCardSetting::create([
    'academic_year' => '2025-2026',
    'term' => 'Term 1',
    'report_card_available_at' => now(),  // Or future date
    'is_active' => true,
    'description' => 'Report cards for 2025-2026 Term 1'
]);
```

### 2. ReportCard Records
For each student, must have:
```
- student_id: [valid student ID]
- academic_year_id: [valid academic year ID]
- term_id: [valid term ID]
- course_id: [valid course ID]
- status: 'approved'  (MUST BE 'approved')
- approved_at: DateTime
```

### 3. Student Outstanding Fees
Must be 0 RWF for access. Check via:
- `school_fees` table (total minus paid transactions)
- OR `outstanding_amount` field in students table

---

## Configuration Steps

### Step 1: Set Application Configuration
Edit `.env` file:
```env
APP_ACADEMIC_YEAR=2025-2026
APP_CURRENT_TERM=Term 1
```

Then in `config/app.php`, add:
```php
'academic_year' => env('APP_ACADEMIC_YEAR', '2025-2026'),
'current_term' => env('APP_CURRENT_TERM', 'Term 1'),
```

### Step 2: Clear Configuration Cache
```bash
php artisan config:cache
```

### Step 3: Create ReportCardSetting Record
Use Laravel admin panel or run:
```bash
php artisan tinker
App\Models\ReportCardSetting::create([...])
```

---

## Access Flow Diagram

```
Student Access Attempt
        ↓
┌─────────────────────────────────────────┐
│ ReportCardService::getAccessStatus()    │
└──────────────┬──────────────────────────┘
               ↓
       ┌───────────────────────────────────┐
       │ Check 1: Fees Paid?               │
       │ FeePaymentService::getPaymentStatus() │
       └───────────┬───────────────────────┘
                   ↓ (if YES)
       ┌───────────────────────────────────┐
       │ Check 2: Setting Configured?      │
       │ ReportCardSetting::getActive()    │ ← ⚠️ FAILS HERE
       └───────────┬───────────────────────┘
                   ↓ (if NULL → Error!)
       Return: "not configured..."
```

---

## Summary of What to Check

| Item | Status | What to Do |
|------|--------|-----------|
| ReportCardSetting record exists | ❓ | Check if academic_year='2025-2026', term='Term 1' |
| ReportCardSetting is_active=true | ❓ | Set is_active to TRUE |
| report_card_available_at date | ❓ | Set to past date to show countdown now, or future for countdown |
| Config values set | ✗ NO | Add APP_ACADEMIC_YEAR and APP_CURRENT_TERM to .env |
| ReportCard records exist | ❓ | Check status='approved' for test student |
| Student outstanding fees | ❓ | Should be 0 RWF |

---

## Blade Template Variables Expected

The `students/report-cards.blade.php` template expects:
```php
- $reportCards (Collection)
- $hasOutstandingFees (boolean)
- $outstanding (float)
- $reportCardReleaseDate (DateTime or null)
- $releaseDatePassed (boolean)
- $countdown (CarbonPeriod or null)
- $student (Student model)
```

These are passed from `StudentController::showReportCards()` at line 2223.

---

## Next Steps

1. **Start MySQL database** (required to access any data)
2. **Run the diagnostic checks above** using `php artisan tinker`
3. **Create the missing ReportCardSetting record** if needed
4. **Set application configuration** in .env
5. **Verify ReportCard records exist** with status='approved'
6. **Clear all caches**: `php artisan config:cache && php artisan cache:clear`
7. **Test student login** to verify countdown timer and fees display
