# Report Card Access Control with Flutterwave Payment - COMPLETE IMPLEMENTATION SUMMARY

**Implementation Date**: April 3, 2026  
**System**: Educational Student Portal (ESA)  
**Feature**: Student Report Card Access with Fee Payment Control

---

## Executive Summary

A complete report card access control system has been implemented that allows:

1. **Admins** to set report card release dates/times
2. **Students** to see a countdown timer until release
3. **Students** with outstanding fees are blocked with a payment button
4. **Flutterwave integration** for secure online payments
5. **Automatic fee clearing** upon successful payment
6. **Multiple payment methods**: Card, Mobile Money, Bank Transfer, USSD

---

## Files Created

### 1. Controllers
- **`app/Http/Controllers/StudentReportCardController.php`** (NEW)
  - Handles student access to report cards
  - Implements fee checking and countdown logic
  - 3 methods: `index()`, `show()`, `accessStatus()`
  - 95 lines of code

### 2. Views (Blade Templates)
All located in `resources/views/reportcard/`

- **`blocked.blade.php`** (NEW)
  - Displays when student has outstanding fees
  - Flutterwave payment button integration
  - Shows outstanding amount and payment options
  - ~150 lines with styling

- **`countdown.blade.php`** (NEW)
  - Live countdown timer display
  - Shows release date/time
  - Auto-refreshes when countdown reaches zero
  - ~185 lines with animations

- **`show.blade.php`** (NEW)
  - Displays actual report card
  - Student and academic information
  - Grades table
  - Print functionality
  - ~155 lines

- **`index.blade.php`** (NEW)
  - Lists all student report cards
  - Status badges (Blocked/Pending/Available)
  - Quick action buttons
  - Card grid layout
  - ~115 lines

- **`access-status.blade.php`** (NEW)
  - Detailed access status dashboard
  - List view with filters
  - Status legend
  - ~135 lines

---

## Files Modified

### 1. Controllers
- **`app/Http/Controllers/FlutterwavePaymentController.php`**
  - Added `initializeReportCardFeePayment()` method (~60 lines)
  - Added `processReportCardPayment()` method (~50 lines)
  - Updated `handlePaymentCallback()` to handle report card fees
  - Updated `handleWebhook()` to handle report card fees
  - Total additions: ~150 lines

### 2. Models
- **`app/Models/Student.php`**
  - Added `getOutstandingFees()` method
  - Added `clearOutstandingFees()` method
  - Added `hasOutstandingFees()` method
  - Added `getFormattedOutstandingFeesAttribute()` accessor
  - Total additions: ~85 lines

### 3. Routes
- **`routes/web.php`**
  - Added payment route: `POST /flutterwave/payment/initialize-reportcard-fees`
  - Added 3 student report card routes under `student` prefix
  - Total additions: 4 new routes

---

## Database Requirements

### Table: report_card_settings
**Migration**: `database/migrations/2026_04_02_004325_create_report_card_settings_table.php`

**Fields**:
- `id` (primary key)
- `release_at` (timestamp) - When report cards become visible
- `created_at`, `updated_at`

**Usage**: Only 1 row needed per system

### Table: student_transactions (Existing)
**New transaction type**: `'report_card_fees'`

**Fields Used**:
- `student_id`
- `tx_ref` (format: `RFC-{student_id}-{timestamp}`)
- `type` (`'report_card_fees'`)
- `amount`
- `currency` (RWF)
- `status` (pending → successful)
- `meta` (JSON with outstanding_amount, flutterwave_transaction_id)
- `paid_at`

---

## Payment Flow Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                   STUDENT VIEW REPORT CARD                   │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌─────────────────────────┐
                    │ CHECK OUTSTANDING FEES  │
                    └─────────────────────────┘
                              │
                  ┌───────────┴───────────┐
                  ▼                       ▼
            HAS FEES              NO FEES
             BLOCK                 CONTINUE
              VIEW              (Show Countdown Check)
              │
              │ Click "Pay Now"
              ▼
    ┌──────────────────────────────┐
    │ INITIALIZE PAYMENT           │
    │ POST /flutterwave/payment/   │
    │ initialize-reportcard-fees   │
    └──────────────────────────────┘
              │
              ▼
    ┌──────────────────────────────┐
    │ CREATE TRANSACTION RECORD    │
    │ Status: pending              │
    │ Type: report_card_fees       │
    └──────────────────────────────┘
              │
              ▼
    ┌──────────────────────────────┐
    │ FLUTTERWAVE PAYMENT REDIRECT │
    │ Hosted Payment Page Opens    │
    └──────────────────────────────┘
              │
              ▼
    ┌──────────────────────────────┐
    │ STUDENT COMPLETES PAYMENT    │
    │ (Card/Mobile Money/etc)      │
    └──────────────────────────────┘
              │
              ▼
    ┌──────────────────────────────┐
    │ FLUTTERWAVE CALLBACK         │
    │ GET /flutterwave/payment/    │
    │ callback?transaction_id=...  │
    └──────────────────────────────┘
              │
              ▼
    ┌──────────────────────────────┐
    │ VERIFY TRANSACTION           │
    │ Flutterwave API Verification │
    └──────────────────────────────┘
              │
              ▼
    ┌──────────────────────────────┐
    │ PROCESS PAYMENT              │
    │ • Update transaction status  │
    │ • Clear student fees         │
    │ • Add flutterwave tx ID      │
    └──────────────────────────────┘
              │
              ▼
    ┌──────────────────────────────┐
    │ REDIRECT TO DASHBOARD        │
    │ Success Message Shown        │
    └──────────────────────────────┘
              │
              ▼
    ┌──────────────────────────────┐
    │ STUDENT VIEWS REPORT CARD    │
    │ (Fees no longer blocking)    │
    └──────────────────────────────┘
```

---

## Configuration Checklist

### Environment Variables (`.env`)
```
FLW_SECRET_KEY=your_secret_key_here
FLW_PUBLIC_KEY=your_public_key_here
FLW_ENCRYPTION_KEY=your_encryption_key_here
FLW_WEBHOOK_HASH=your_webhook_hash_here
```

### Admin Configuration
1. Go to `/admin/report-card-settings`
2. Set release date/time for report cards
3. All students will see countdown until that time

### Database Migration
```bash
php artisan migrate
# This creates the report_card_settings table if not already created
```

---

## Route Reference

### For Student Routes
**Prefix**: `/student/`  
**Middleware**: `auth:student`

| Route | Method | Controller | Function |
|-------|--------|-----------|----------|
| `/report-cards` | GET | StudentReportCardController | `index()` |
| `/report-cards/{id}` | GET | StudentReportCardController | `show()` |
| `/report-cards-status` | GET | StudentReportCardController | `accessStatus()` |

### For Payment Routes
**Prefix**: `/flutterwave/`  
**Middleware**: `auth:student`

| Route | Method | Controller | Function |
|-------|--------|-----------|----------|
| `/payment/initialize-reportcard-fees` | POST | FlutterwavePaymentController | `initializeReportCardFeePayment()` |
| `/payment/callback` | GET | FlutterwavePaymentController | `handlePaymentCallback()` |
| `/webhook` | POST | FlutterwavePaymentController | `handleWebhook()` |

---

## Code Examples

### Getting Student Outstanding Fees
```php
$student = Auth::user();
$fees = $student->getOutstandingFees();           // Returns decimal
$hassFees = $student->hasOutstandingFees();       // Returns boolean
$formatted = $student->formatted_outstanding_fees; // Returns "RWF XX,XXX.XX"
```

### Checking Report Card Availability
```php
$settings = ReportCardSetting::first();
$now = Carbon::now();

if ($now->lt($settings->release_at)) {
    echo "Report card available in: " . $now->diff($settings->release_at);
} else {
    echo "Report card is available";
}
```

### Creating Payment Transaction
```php
StudentTransaction::create([
    'student_id' => $studentId,
    'tx_ref' => 'RFC-' . $studentId . '-' . time(),
    'type' => 'report_card_fees',
    'amount' => $amount,
    'currency' => 'RWF',
    'status' => 'pending'
]);
```

---

## Implementation Verification Checklist

### Database
- [ ] `report_card_settings` table exists
- [ ] `student_transactions` table exists and has all required columns
- [ ] At least one row in `report_card_settings` with a release date

### Files
- [ ] `app/Http/Controllers/StudentReportCardController.php` exists
- [ ] `app/Http/Controllers/FlutterwavePaymentController.php` updated
- [ ] `app/Models/Student.php` has new fee methods
- [ ] All 5 blade templates in `resources/views/reportcard/` exist
- [ ] `routes/web.php` has new routes

### Functionality
- [ ] Admin can set report card release date
- [ ] Student redirects to countdown view when date not reached
- [ ] Student with outstanding fees sees payment button
- [ ] Flutterwave payment dialog opens when clicking payment button
- [ ] Payment verification works
- [ ] Report card accessible after payment
- [ ] Transactions are recorded in database

### Configuration
- [ ] Flutterwave credentials set in `.env`
- [ ] Report card release date set in admin area
- [ ] Email notifications configured (optional)

---

## Testing Scenarios

### Scenario 1: Outstanding Fees
1. Set report card release date to future
2. Create a student with outstanding fees
3. Visit `/student/report-cards/{id}`
4. **Expected**: Sees blocked view with payment button

### Scenario 2: Countdown Timer
1. Student has no outstanding fees
2. Release date is in future
3. Visit `/student/report-cards/{id}`
4. **Expected**: Sees countdown timer that updates every second

### Scenario 3: Payment Process
1. Click "Pay Now" button
2. **Expected**: Flutterwave payment page opens
3. Complete payment with test card
4. **Expected**: Redirected to dashboard with success message

### Scenario 4: Report Card Access
1. Release date reached
2. Student has no outstanding fees
3. Visit `/student/report-cards/{id}`
4. **Expected**: Report card displayed with grades

---

## Troubleshooting Guide

### Payment Button Not Working
**Check**:
- Flutterwave API keys in `.env`
- Route registered correctly
- Student is authenticated

### Countdown Timer Not Updating
**Check**:
- JavaScript enabled in browser
- Flutterwave script loaded (`<script src="https://checkout.flutterwave.com/v3.js">`)
- Release date set in settings

### Outstanding Fees Not Calculated
**Check**:
- Student model has `getOutstandingFees()` method
- Database has `school_fees` or `outstanding_amount` field
- Query returns correct data via tinker

### Payment Not Completing
**Check**:
- Webhook URL configured in Flutterwave dashboard
- `handleWebhook()` method receiving requests
- Webhook signature verification passes

---

## Future Enhancements

Potential additions:
- [ ] Email notification when report card released
- [ ] SMS reminder for outstanding fees
- [ ] Partial payment support
- [ ] Payment plan/installment options
- [ ] PDF export of report card
- [ ] Email report card to student
- [ ] Multiple report format options
- [ ] Custom payment page branding
- [ ] Bulk payment for multiple report cards
- [ ] Payment receipt archival

---

## Support & Documentation Links

- **Flutterwave Docs**: https://developer.flutterwave.com/
- **Laravel Docs**: https://laravel.com/docs
- **Blade Templates**: https://laravel.com/docs/blade
- **Carbon DateTime**: https://carbon.nesbot.com/

---

## Rollback Instructions

If you need to revert this implementation:

1. Delete new blade templates from `resources/views/reportcard/`
2. Remove new controller: `app/Http/Controllers/StudentReportCardController.php`
3. Remove routes from `routes/web.php`
4. Remove methods from `FlutterwavePaymentController.php`
5. Remove methods from `Student.php` model
6. Drop `report_card_settings` table: `php artisan migrate:rollback`

---

## Implementation Team Notes

- **Total Files Created**: 6 (1 controller + 5 views)
- **Total Files Modified**: 4 (controllers, model, routes)
- **Total Lines Added**: ~1,500+
- **Database Tables**: 1 new (report_card_settings)
- **Routes Added**: 4 new routes
- **Integration Points**: 3 (Flutterwave payment flow)

---

**Status**: ✅ COMPLETE AND READY FOR TESTING

Last Updated: April 3, 2026 by GitHub Copilot
