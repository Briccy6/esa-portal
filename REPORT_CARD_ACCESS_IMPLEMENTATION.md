# Report Card Access Control Implementation

## Overview
This document outlines the complete implementation of report card access control with three states:
1. **Blocked** - Student has outstanding fees (shows payment button with Flutterwave integration)
2. **Countdown** - Student has paid all fees but report card not yet available (shows live countdown timer)
3. **Accessible** - All requirements met (shows full report card with grades and print option)

## Architecture

### Service Layer

#### 1. **ReportCardService** (`app/Services/ReportCardService.php`)
Main orchestrator for access control logic.

**Key Methods:**
- `getAccessStatus(Student $student)` - Returns comprehensive access status with all required data
- `canAccessReportCard(Student $student)` - Simple boolean check
- `getCountdownData()` - Returns countdown data for JavaScript
- `formatCountdown(array $countdownData)` - Formats countdown for display

**Access Status Structure:**
```php
[
    'can_access' => bool,              // Can access report card fully
    'reason' => string,                // Status message
    'payment_status' => array,         // Fee payment details
    'setting' => ReportCardSetting,    // Admin configuration  
    'countdown_data' => array|null,    // Time remaining (if not available)
    'available_date' => Carbon|null    // When report card becomes available
]
```

#### 2. **FeePaymentService** (`app/Services/FeePaymentService.php`)
Handles fee calculation and payment verification.

**Key Methods:**
- `getPaymentStatus(Student $student)` - Returns fee status:
  - Checks `student.outstanding_amount` field directly
  - Falls back to calculating from `school_fees` table sum
  - Calculates: `outstanding = required_fees - total_paid`

- `hasPaidAllFees(Student $student)` - Boolean check if all fees paid

**Payment Status Structure:**
```php
[
    'is_fully_paid' => bool,
    'outstanding' => float,           // Amount still owed
    'required_fees' => float,         // Total fees required
    'total_paid' => float,            // Amount already paid
    'last_payment_date' => DateTime|null
]
```

#### 3. **ReportCardSetting Model** (`app/Models/ReportCardSetting.php`)
Stores admin-configured report card release settings.

**Database Schema:**
```
- academic_year (string)               // e.g., "2026"
- term (string)                        // e.g., "Term 1"
- report_card_available_at (datetime)  // Release date/time
- is_active (boolean)                  // Whether setting is active
- description (nullable string)        // Optional description
- created_at, updated_at
```

**Key Methods:**
- `getActive()` - Retrieves active setting with fallback logic:
  1. First tries to match configured `academic_year` and `term` from `config/app.php`
  2. Falls back to most recently created active setting
  3. Returns null if no active setting exists

- `isAvailable()` - Checks if report card is currently available
  Returns: `is_active && report_card_available_at->isPast()`

- `getCountdownData()` - Returns countdown breakdown:
```php
[
    'months', 'weeks', 'days', 'hours', 'minutes', 'seconds',
    'total_seconds', 'target_date'    // ISO8601 string
]
```

### Controller Layer

#### ReportCardController (`app/Http/Controllers/ReportCardController.php`)
**Method: `studentShow(ReportCard $reportCard)`**

Three-state access control logic:

```php
1. Check Authorization
   - Student can only access their own report card (403 abort if mismatch)

2. Get Access Status
   - $accessStatus = ReportCardService::getAccessStatus($student)

3. Route 1: BLOCKED (Outstanding Fees)
   if (!$accessStatus['payment_status']['is_fully_paid']) {
       return view('students.report-cards.payment', [
           'outstanding' => $accessStatus['payment_status']['outstanding'],
           'reportCard' => $reportCard
       ]);
   }

4. Route 2: COUNTDOWN (Not Yet Available)
   if ($accessStatus['countdown_data']) {
       return view('students.report-cards.countdown', [
           'reportCard' => $reportCard,
           'countdownData' => $accessStatus['countdown_data'],
           'availableDate' => $accessStatus['available_date']
       ]);
   }

5. Route 3: ACCESSIBLE (All Checks Passed)
   return view('students.report-cards.show', [
       'reportCard' => $reportCard,
       'schoolInfo' => SchoolInformation::getActive()
   ]);
```

### View Layer

#### 1. **Payment View** (`resources/views/students/report-cards/payment.blade.php`)

Status: Outstanding Fees
- Shows red alert with "Report Card Access Blocked"
- Displays outstanding balance in large text
- Features Flutterwave payment integration form
- Payment options: Card, Mobile Money, Bank Transfer, USSD

**Key Elements:**
```blade
<!-- Outstanding Balance Display -->
<span class="text-2xl">RWF {{ number_format($outstanding, 2) }}</span>

<!-- Flutterwave Payment Form -->
<form action="{{ route('flutterwave.payment.initialize-reportcard-fees') }}" method="POST">
    @csrf
    <input type="hidden" name="amount" value="{{ $outstanding }}">
    <button type="submit">Pay Now with Flutterwave</button>
</form>
```

#### 2. **Countdown View** (`resources/views/students/report-cards/countdown.blade.php`)

Status: Report Card Not Yet Available
- Displays scheduled release date and time
- Shows course, academic year, and term information
- Live countdown timer with four display boxes:
  - Days (blue gradient)
  - Hours (purple gradient)
  - Minutes (pink gradient)
  - Seconds (red gradient)

**JavaScript Countdown Logic:**
```javascript
- Calculates diff = target_time - current_time
- Updates display every 1000ms
- Auto-refreshes page when countdown reaches 0
- Pads numbers with leading zeros (00:05:23)
```

#### 3. **Show View** (`resources/views/students/report-cards/show.blade.php`)

Status: Report Card Accessible
- Displays full report card with all subjects and grades
- Shows print button
- Displays school name, academic year, term
- Professional layout with school logo and information

---

## Payment Integration

### Flutterwave Payment Flow

#### 1. **Initialize Payment** 
Route: `POST /flutterwave/payment/initialize-reportcard-fees`
Handler: `FlutterwavePaymentController::initializeReportCardFeePayment()`

**Steps:**
1. Retrieve student and outstanding amount
2. Validate amount > 0
3. Generate transaction reference: `RFC-{student_id}-{timestamp}`
4. Prepare payment payload with:
   - Amount in RWF
   - Multiple payment options
   - Redirect URL for callback
   - Customer details
   - Meta data (student_id, type: 'report_card_fees')
   - Custom title/description

5. Call Flutterwave API: POST `https://api.flutterwave.com/v3/payments`
6. Create pending transaction record
7. Return JSON with redirect URL to Flutterwave checkout

**Request Example:**
```
POST /flutterwave/payment/initialize-reportcard-fees
{
    "amount": 50000
}
```

**Response Example:**
```json
{
    "success": true,
    "redirect_url": "https://checkout.flutterwave.com/v3/hosted/...",
    "tx_ref": "RFC-123-1704067200"
}
```

#### 2. **Process Payment**
When student completes payment on Flutterwave:
1. Redirected to `route('flutterwave.payment.callback')`
2. Flutterwave sends webhook to `POST /flutterwave/webhook`
3. Webhook verifies signature and event type
4. If `charge.completed` and `type = 'report_card_fees'`:
   - Call `processReportCardPayment($txData, $meta)`
   - Update transaction status to 'successful'
   - Call `Student::clearOutstandingFees($amount)`
   - Log successful processing

#### 3. **Clear Outstanding Fees**
Method: `Student::clearOutstandingFees($amount)`

**Logic:**
1. Reduce `outstanding_amount` by payment amount
2. Ensure it never goes below 0
3. Persist to database

---

## Configuration

### Environment Variables
Add to `.env`:
```
ACADEMIC_YEAR=2026
CURRENT_TERM=Term 1
FLUTTERWAVE_SECRET_KEY=sk_live_xxxxx
FLUTTERWAVE_WEBHOOK_HASH=xxxxx
```

### App Configuration
Set in `config/app.php`:
```php
'academic_year' => env('ACADEMIC_YEAR', '2026'),
'current_term' => env('CURRENT_TERM', 'Term 1'),
```

---

## Routes

### Student Report Card Routes
```php
Route::prefix('student/report-cards')->name('student.report-cards.')->group(function () {
    Route::get('/', [ReportCardController::class, 'studentIndex'])
        ->name('index')
        ->middleware('auth:student');
        
    Route::get('/{reportCard}', [ReportCardController::class, 'studentShow'])
        ->name('show')
        ->middleware('auth:student');
        
    Route::get('/{reportCard}/download', [ReportCardController::class, 'studentDownload'])
        ->name('download')
        ->middleware('auth:student');
});
```

### Flutterwave Payment Routes
```php
Route::middleware('auth:student')->prefix('flutterwave')->name('flutterwave.')->group(function () {
    Route::post('/payment/initialize-reportcard-fees', 
        [FlutterwavePaymentController::class, 'initializeReportCardFeePayment'])
        ->name('payment.initialize-reportcard-fees');
        
    Route::get('/payment/callback', 
        [FlutterwavePaymentController::class, 'handlePaymentCallback'])
        ->name('payment.callback');
        
    Route::post('/webhook', 
        [FlutterwavePaymentController::class, 'handleWebhook'])
        ->name('webhook');
});
```

---

## User Experience Flow

### Scenario 1: Student with Outstanding Fees
1. Student navigates to `/student/report-cards/{id}`
2. System checks fee status → **Outstanding fees detected**
3. **Payment view displayed:**
   - Shows red alert: "Report Card Access Blocked"
   - Outstanding balance: "RWF 50,000.00"
   - Payment button: "Pay Now with Flutterwave"
4. Student clicks "Pay Now"
5. JavaScript form submits to `/flutterwave/payment/initialize-reportcard-fees`
6. Flutterwave checkout page opens
7. Student selects payment method and completes payment
8. After successful payment:
   - Webhook processes payment
   - `OutstandingFees` reset to 0
   - Student can now access report card
   - Automatic redirect/refresh recommended

### Scenario 2: Student with Paid Fees but Report Card Not Yet Released
1. Student navigates to `/student/report-cards/{id}`
2. System checks fee status → **Fees fully paid ✓**
3. System checks availability → **Not yet available**
4. **Countdown view displayed:**
   - Release date/time shown: "January 15, 2025 2:00 PM"
   - Countdown timer: "2 Days 5 Hours 23 Minutes 45 Seconds"
   - Timer updates every second
   - Info boxes show: Fees Status ✓, Waiting for date, Unlocking soon...
5. Timer counts down in real-time
6. When countdown reaches 0, page auto-refreshes
7. System detects `report_card_available_at` is now in past
8. Full report card displayed

### Scenario 3: Student Eligible for Full Access
1. Student navigates to `/student/report-cards/{id}`
2. System checks fee status → **Fees fully paid ✓**
3. System checks availability → **Available ✓**
4. **Full report card displayed:**
   - School logo and name
   - Student information
   - Academic year and term
   - All subjects with grades
   - Print button
   - Download button

---

## Testing Checklist

- [ ] Database has active `report_card_settings` record
- [ ] Test student has outstanding fees in `students.outstanding_amount` OR `school_fees` table
- [ ] Test student has a report card record
- [ ] Flutterwave test keys configured in `.env`
- [ ] Test Scenario 1 - Payment blocking and view display
- [ ] Test Scenario 2 - Countdown display and updates
- [ ] Test Scenario 3 - Full report card display when eligible
- [ ] Test Flutterwave payment initialization and redirect
- [ ] Test webhook signature verification
- [ ] Test fee clearing after successful payment
- [ ] Test auto-refresh when countdown completes
- [ ] Verify transaction records created with proper status
- [ ] Check logs for no error messages

---

## Troubleshooting

### Issue: Student seeing "Access Restricted - Report card access not configured"
**Solution:** 
- Create an active `ReportCardSetting` record
- Ensure `is_active = true`
- Configure `academic_year` and `term` matching `config/app.php` values
- Or just ensure at least one active setting exists (will use most recent)

### Issue: Countdown not displaying
**Solution:**
- Verify `report_card_available_at` is in the future
- Check JavaScript console for errors
- Verify ISO8601 date format is correct
- Test in browser `new Date('2025-01-15T14:00:00Z')` parses correctly

### Issue: Payment button not working
**Solution:**
- Verify Flutterwave secret key is correct
- Check that student has `auth:student` middleware applied
- Verify route `route('flutterwave.payment.initialize-reportcard-fees')` generates correct URL
- Check browser console for form submission errors
- Verify CSRF token is included in form

### Issue: Fees not clearing after payment
**Solution:**
- Verify webhook signature hash is correct
- Check that transaction type is `'report_card_fees'`
- Verify webhook is receiving `charge.completed` event
- Check logs in `storage/logs/laravel.log`
- Ensure `Student::clearOutstandingFees()` is being called

---

## Files Modified/Created

### New Files Created:
- `resources/views/students/report-cards/payment.blade.php` - Payment view
- `resources/views/students/report-cards/countdown.blade.php` - Countdown view

### Modified Files:
- `app/Http/Controllers/ReportCardController.php` - Updated `studentShow()` method
- `app/Http/Controllers/FlutterwavePaymentController.php` - Added report card fee methods
- `app/Services/FeePaymentService.php` - Simplified fee calculation
- `app/Services/ReportCardService.php` - Added access status methods
- `app/Models/ReportCardSetting.php` - Added flexible `getActive()` method
- `app/Models/Student.php` - Uses existing `clearOutstandingFees()` method
- `routes/web.php` - Routes already configured
- `config/app.php` - Added academic_year and current_term settings

### Existing Files (No Changes):
- `app/Models/ReportCard.php` - Used by controller
- `app/Models/StudentTransaction.php` - Used for transaction logging
- `resources/views/students/report-cards/show.blade.php` - Existing full view

---

## Next Steps / Future Enhancements

1. **Email Notifications:**
   - Send email when payment is successful
   - Send email when report card becomes available
   - Send email reminder 24 hours before deadline

2. **Dashboard Integration:**
   - Show report card status on student dashboard
   - Quick access widgets showing countdown or payment status

3. **Admin Features:**
   - Admin can manually extend report card release date
   - Admin can view payment history per student
   - Admin analytics on fees collection

4. **SMS Notifications:**
   - SMS reminder when countdown nears completion
   - SMS confirmation after payment

5. **Scheduled Tasks:**
   - Queue job to notify students when report card becomes available
   - Automatic fee reconciliation

---

## Support & Debugging

For issues, check:
1. Application logs: `storage/logs/laravel.log`
2. Database: Verify student has report card record
3. Database: Verify report_card_settings table has active entry
4. Database: Check student_transactions table for payment records
5. Browser console: Check for JavaScript errors
6. Network tab: Verify API calls to Flutterwave
7. Flutterwave dashboard: Verify transaction appears there

---

**Implementation Date:** January 2025
**Status:** Complete and Ready for Testing
**Last Updated:** [Current Date]
