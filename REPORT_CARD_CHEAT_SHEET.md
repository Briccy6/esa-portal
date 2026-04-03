# Report Card Access Control - Developer Cheat Sheet

## Quick Reference

### Core Method Calls

```php
// Check if student can access their report card
$canAccess = ReportCardService::canAccessReportCard($student);

// Get comprehensive access status
$status = ReportCardService::getAccessStatus($student);
// Returns: can_access, reason, payment_status, setting, countdown_data, available_date

// Get payment status
$paymentStatus = FeePaymentService::getPaymentStatus($student);
// Returns: is_fully_paid, outstanding, required_fees, total_paid, last_payment_date

// Check if student paid all fees
$paid = FeePaymentService::hasPaidAllFees($student);

// Get active report card setting
$setting = ReportCardSetting::getActive();

// Check if setting is available now
$isAvailable = $setting->isAvailable();

// Get countdown data
$countdown = $setting->getCountdownData();
// Returns: months, weeks, days, hours, minutes, seconds, total_seconds, target_date

// Clear student fees after payment
$student->clearOutstandingFees($amount);
```

---

## Routes

### Student Report Cards
```php
GET    /student/report-cards                    // List all report cards
GET    /student/report-cards/{reportCard}       // Show single report card (MAIN)
GET    /student/report-cards/{reportCard}/download

POST   /flutterwave/payment/initialize-reportcard-fees  // Initialize payment
GET    /flutterwave/payment/callback              // Payment callback
POST   /flutterwave/webhook                       // Webhook handler
```

### Route Usage in Views
```blade
<!-- Payment form route -->
<form action="{{ route('flutterwave.payment.initialize-reportcard-fees') }}" method="POST">

<!-- View route -->
<a href="{{ route('student.report-cards.show', $reportCard) }}">View Report Card</a>

<!-- Download route -->
<a href="{{ route('student.report-cards.download', $reportCard) }}">Download</a>
```

---

## Control Flow Decision Tree

```
START: Student visits /student/report-cards/{id}
  ↓
1. Check Authorization
   if (student_id != auth()->id()) abort(403)
  ↓
2. Get Access Status
   $status = ReportCardService::getAccessStatus($student)
  ↓
3. Decision Point 1: Outstanding Fees?
   ├─ YES → return view('students.report-cards.payment')
   └─ NO ↓
  ↓
4. Decision Point 2: Release Date Reached?
   ├─ NO → return view('students.report-cards.countdown')
   └─ YES ↓
  ↓
5. Final Check: Can Access?
   ├─ NO → redirect with error message
   └─ YES → return view('students.report-cards.show')
  ↓
END: Appropriate view displayed
```

---

## Three Views & Their Conditions

### 1. Payment View
**Condition:** `!$accessStatus['payment_status']['is_fully_paid']`
```blade
@extends('students.app')
@section('content')
  <!-- Shows outstanding_amount -->
  RWF {{ number_format($outstanding, 2) }}
  
  <!-- Form to initialize payment -->
  <form action="{{ route('flutterwave.payment.initialize-reportcard-fees') }}" method="POST">
    @csrf
    <input type="hidden" name="amount" value="{{ $outstanding }}">
    <button type="submit">Pay Now with Flutterwave</button>
  </form>
@endsection
```

### 2. Countdown View
**Condition:** `$accessStatus['countdown_data'] != null`
```blade
@extends('students.app')
@section('content')
  <!-- Shows countdown_data breakdown -->
  Days: {{ $countdownData['days'] }}
  Hours: {{ $countdownData['hours'] }}
  Minutes: {{ $countdownData['minutes'] }}
  Seconds: {{ $countdownData['seconds'] }}
  
  <!-- Target date for JS -->
  <script>
    const targetIso = '{{ $availableDate->toIso8601String() }}';
  </script>
@endsection
```

### 3. Full Report View
**Condition:** `$accessStatus['can_access'] == true`
```blade
@extends('students.app')
@section('content')
  <!-- Shows full report card with grades -->
  @foreach($reportCard->subjects as $subject)
    {{ $subject->lesson->name }}: {{ $subject->grade }}
  @endforeach
@endsection
```

---

## Database Queries

### Check Student Access Status
```sql
-- Get student's outstanding fees
SELECT id, full_name, outstanding_amount 
FROM students WHERE id = [STUDENT_ID];

-- Get active report card settings
SELECT * FROM report_card_settings 
WHERE is_active = 1 
ORDER BY created_at DESC LIMIT 1;

-- Get student's report cards
SELECT * FROM report_cards 
WHERE student_id = [STUDENT_ID] 
ORDER BY created_at DESC;
```

### Check Payment Status
```sql
-- Get latest payment attempt
SELECT * FROM student_transactions 
WHERE student_id = [STUDENT_ID] 
AND type = 'report_card_fees'
ORDER BY created_at DESC LIMIT 1;

-- Get all report card fee payments
SELECT student_id, COUNT(*) as attempts, 
       SUM(IF(status='successful', amount, 0)) as total_paid
FROM student_transactions 
WHERE type = 'report_card_fees'
GROUP BY student_id;
```

### Setup Test Data
```sql
-- Create active report card setting
INSERT INTO report_card_settings 
(academic_year, term, report_card_available_at, is_active, created_at, updated_at)
VALUES 
('2026', 'Term 1', DATE_ADD(NOW(), INTERVAL 2 DAY), 1, NOW(), NOW());

-- Set student outstanding fees
UPDATE students SET outstanding_amount = 50000 WHERE id = 1;

-- Clear student fees
UPDATE students SET outstanding_amount = 0 WHERE id = 1;

-- Change report card availability
UPDATE report_card_settings 
SET report_card_available_at = DATE_SUB(NOW(), INTERVAL 1 DAY)
WHERE id = 1;
```

---

## Configuration (.env)
```bash
# Academic settings
ACADEMIC_YEAR=2026
CURRENT_TERM=Term 1

# Flutterwave
FLUTTERWAVE_PUBLIC_KEY=pk_test_xxxxx
FLUTTERWAVE_SECRET_KEY=sk_test_xxxxx
FLUTTERWAVE_WEBHOOK_HASH=xxxxx
```

---

## Configuration (config/app.php)
```php
'academic_year' => env('ACADEMIC_YEAR', '2026'),
'current_term' => env('CURRENT_TERM', 'Term 1'),
```

---

## Key File Locations

| File | Purpose |
|------|---------|
| `app/Services/ReportCardService.php` | Access control logic |
| `app/Services/FeePaymentService.php` | Fee calculation |
| `app/Http/Controllers/ReportCardController.php` | 3-state routing |
| `app/Http/Controllers/FlutterwavePaymentController.php` | Payment handling |
| `app/Models/ReportCardSetting.php` | Admin settings |
| `app/Models/Student.php` | Fee clearing |
| `resources/views/students/report-cards/payment.blade.php` | Payment view |
| `resources/views/students/report-cards/countdown.blade.php` | Countdown view |
| `resources/views/students/report-cards/show.blade.php` | Full report view |
| `routes/web.php` | All routes |
| `config/app.php` | Academic config |

---

## Troubleshooting Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# Check routes are registered
php artisan route:list | grep report-card

# Tail logs for errors
tail -100 storage/logs/laravel.log

# Check Flutterwave connection (in tinker)
php artisan tinker
> $response = Http::withHeaders(['Authorization' => 'Bearer sk_test_xxx'])
>     ->post('https://api.flutterwave.com/v3/payments', [...])
```

---

## JavaScript References

### Countdown Timer Logic
```javascript
const targetIso = '{{ $availableDate->toIso8601String() }}';
const target = new Date(targetIso).getTime();

function updateCountdown() {
    const now = new Date().getTime();
    const diff = target - now;
    
    if (diff <= 0) {
        location.reload();  // Auto-refresh when done
        return;
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    document.querySelector('.countdown-days').textContent = String(days).padStart(2, '0');
    document.querySelector('.countdown-hours').textContent = String(hours).padStart(2, '0');
    document.querySelector('.countdown-minutes').textContent = String(minutes).padStart(2, '0');
    document.querySelector('.countdown-seconds').textContent = String(seconds).padStart(2, '0');
}

updateCountdown();
setInterval(updateCountdown, 1000);  // Update every second
```

---

## Payment Flow Overview

```
1. Student clicks "Pay Now" button
   ↓
2. Form POSTs to /flutterwave/payment/initialize-reportcard-fees
   ↓
3. Controller creates payment payload
   ↓
4. API call to Flutterwave v3/payments
   ↓
5. Response with checkout URL
   ↓
6. Browser redirects to Flutterwave
   ↓
7. Student selects payment method
   ↓
8. Flutterwave sends webhook to /flutterwave/webhook
   ↓
9. Controller verifies signature
   ↓
10. Transaction status updated to 'successful'
    ↓
11. Student fees cleared: outstanding_amount = 0
    ↓
12. Student redirected/refreshes
    ↓
13. Access status rechecked → Now shows countdown or report
```

---

## Common Status Values

### Payment Status
```php
'is_fully_paid' => true|false           // Student paid all fees
'outstanding' => 50000                  // Amount still owed
'required_fees' => 100000               // Total fees required
'total_paid' => 50000                   // Amount already paid
```

### Access Status
```php
'can_access' => true|false              // Can access report card
'reason' => 'string'                    // Why can or can't access
'payment_status' => [...as above...]
'countdown_data' => [...or null]        // If not yet available
'available_date' => DateTime|null
```

### Transaction Status
```php
'pending'       // Payment initialized, awaiting Flutterwave completion
'successful'    // Payment verified by webhook
'failed'        // Webhook indicated failure
'cancelled'     // Student cancelled payment
```

---

## Testing Scenarios Table

| Scenario | Outstanding | Released | Expected View |
|----------|-------------|----------|---|
| Blocked | > 0 | No | Payment |
| Countdown | 0 | Future | Countdown |
| Accessible | 0 | Past | Report Card |
| Config Error | N/A | N/A | Error message |

---

## Key Dates & Times

```php
// Available date (from settings)
$availableDate = $setting->report_card_available_at;

// Countdown starts
$countdown = $setting->getCountdownData();
// Returns time difference until $availableDate

// When countdown reaches 0
// Page refreshes, discovers report card is now available
// Displays full report card instead

// Settings format in database
academic_year = '2026'          // String, not ID
term = 'Term 1'                 // String, not ID
report_card_available_at = DateTime  // ISO8601 format
```

---

## Important Notes

⚠️ **Case Sensitivity:**
- Route names ARE case-sensitive in config
- Table names ARE NOT case-sensitive in most SQL
- View names ARE case-sensitive in Laravel

⚠️ **Format Requirements:**
- `academic_year` in settings must match `config('app.academic_year')`
- `term` in settings must match `config('app.current_term')`
- `report_card_available_at` must be DateTime, not string
- `tx_ref` format: `RFC-{student_id}-{timestamp}`

⚠️ **Authorization:**
- Student can ONLY access their own report card
- Accessing another student's card returns 403
- Webhook requires valid secret key for verification

---

## Quick Debug Checklist

```
[ ] Database online and accessible
[ ] report_card_settings has active record
[ ] Student has outstanding_amount set or school_fees calculated
[ ] Student has report_card assigned
[ ] .env has ACADEMIC_YEAR and CURRENT_TERM
[ ] .env has valid Flutterwave keys
[ ] config/app.php has academic settings
[ ] Routes cleared: php artisan cache:clear
[ ] PHP errors in storage/logs/laravel.log? (check)
[ ] JavaScript errors in browser console? (check F12)
[ ] Network tab shows successful POST to payment endpoint? (check)
```

---

## One-Liners

```bash
# Test route exists
php artisan route:list | grep "student/report-cards"

# Check if config loaded
php artisan tinker
> config('app.academic_year')

# View latest logs
tail -f storage/logs/laravel.log | grep -i "report\|flutterwave"

# Check database connection
php artisan migrate:status

# Clear everything
php artisan optimize:clear
```

---

**Last Updated:** January 2025
**Status:** Ready for Testing
**Version:** 1.0 Complete
