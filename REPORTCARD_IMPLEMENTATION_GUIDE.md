# Report Card Access Control with Flutterwave Payment Integration

## Overview
This implementation adds a complete report card access control system with three states:
1. **Blocked** - When student has outstanding fees
2. **Countdown** - When fees are paid but release date not reached
3. **Accessible** - When all requirements are met

## Components Implemented

### 1. **Database Models**

#### ReportCardSetting Model
- Stores admin-configured report card release date/time
- Located in: `app/Models/ReportCardSetting.php`
- Fields:
  - `release_at` (timestamp) - When report cards become visible

**Migration**: `database/migrations/2026_04_02_004325_create_report_card_settings_table.php`

### 2. **Backend Controllers**

#### FlutterwavePaymentController Updates
**File**: `app/Http/Controllers/FlutterwavePaymentController.php`

**New Methods**:
- `initializeReportCardFeePayment()` - Initialize payment for outstanding fees
- `processReportCardPayment()` - Process successful payment and clear fees
- Updated `handlePaymentCallback()` - Handle report card fee payment type
- Updated `handleWebhook()` - Handle report card fee webhook events

**Transaction Types**:
- Type: `'report_card_fees'`
- Reference: `'RFC-{student_id}-{timestamp}'`
- Status tracking: pending → successful

#### StudentReportCardController (NEW)
**File**: `app/Http/Controllers/StudentReportCardController.php`

**Methods**:
- `index()` - List all student report cards with status
- `show($id)` - Show single report card with access control logic
- `accessStatus()` - Detailed status view for all report cards
- `getOutstandingFees($student)` - Calculate outstanding fees (supports multiple methods)

**Business Logic**:
```
1. Check if student has outstanding fees → Yes → Show blocked view
2. Check if release date reached → No → Show countdown view
3. All checks pass → Show report card
```

### 3. **Routes**

**File**: `routes/web.php`

**Student Routes** (Protected with `auth:student`):
```php
// Student prefix: /student/
Route::get('/report-cards')                    → index()
Route::get('/report-cards/{id}')               → show()
Route::get('/report-cards-status')             → accessStatus()
```

**Flutterwave Payment Route** (Protected with `auth:student`):
```php
// Flutterwave prefix: /flutterwave/
Route::post('/payment/initialize-reportcard-fees') → initializeReportCardFeePayment()
```

### 4. **Views (Blade Templates)**

Location: `resources/views/reportcard/`

#### blocked.blade.php
Shows when student has outstanding fees:
- Alert box with outstanding amount
- Displays amount in large text: `RWF XX,XXX.XX`
- **Flutterwave payment button** that:
  - Calls `/flutterwave/payment/initialize-reportcard-fees`
  - Redirects to Flutterwave hosted payment page
  - Multiple payment options: card, mobile money, bank transfer, USSD
- Payment feature list
- Post-payment information

#### countdown.blade.php
Shows when fees are paid but release date not reached:
- Release date & time display
- **Live countdown timer** that updates every second:
  - Days, Hours, Minutes, Seconds
  - Uses Flutterwave's JavaScript API
  - Auto-refreshes when countdown reaches zero
- Status indicators
- Time zone information

#### show.blade.php
Shows when all requirements met:
- Student information
- Academic information
- Grades table
- Print button
- Summary statistics

#### index.blade.php
Lists all student report cards:
- Card grid layout
- Status badges (Blocked/Pending/Available)
- Quick action buttons
- Academic year & term info
- Outstanding amount display (if blocked)

#### access-status.blade.php
Detailed access status overview:
- List view with status indicators
- Color-coded by status
- Legend explaining each status
- Direct links to view/pay

### 5. **Styling & Features**

**Color Scheme**:
- Red (#DC2626) - Blocked/Outstanding Fees
- Yellow (#EAB308) - Pending/Countdown
- Green (#16A34A) - Available

**Interactive Elements**:
- Animated countdown timer (JavaScript)
- Gradient buttons
- Hover effects
- Loading states
- Responsive grid layouts

**Payment Integration**:
- Flutterwave hosted checkout
- Secure payment processing
- Transaction reference tracking
- Webhook verification
- Error handling and user feedback

### 6. **Payment Flow Diagram**

```
Student Views Report Card
    ↓
Check Outstanding Fees
    ├→ YES: Show Blocked View
    │   ↓
    │   Click "Pay Now with Flutterwave"
    │   ↓
    │   POST /flutterwave/payment/initialize-reportcard-fees
    │   ↓
    │   Flutterwave Hosted Payment Page
    │   ↓
    │   Complete Payment
    │   ↓
    │   GET /flutterwave/payment/callback
    │   ↓
    │   Verify Transaction
    │   ↓
    │   Clear Outstanding Fees
    │   ↓
    │   Redirect to Dashboard with Success Message
    │
    └→ NO: Check Release Date
        ├→ NOT REACHED: Show Countdown View
        │   ├ Display live countdown timer
        │   └ Auto-refresh when ready
        │
        └→ REACHED: Show Report Card
            └ Display grades and details
```

### 7. **Configuration**

**Environment Variables** (should already be set):
```
FLW_SECRET_KEY=your_flutterwave_secret_key
FLW_PUBLIC_KEY=your_flutterwave_public_key
FLW_ENCRYPTION_KEY=your_encryption_key
FLW_WEBHOOK_HASH=your_webhook_hash
```

**Currency**: RWF (Rwandan Francs)

**Payment Gateway**: Flutterwave
- Supported Methods: Card, Bank Transfer, USSD, Mobile Money
- Verification: Automatic via Flutterwave API

### 8. **Key Files Modified**

1. `app/Http/Controllers/FlutterwavePaymentController.php` - Added report card payment methods
2. `routes/web.php` - Added student report card routes and payment route
3. NEW: `app/Http/Controllers/StudentReportCardController.php` - Report card access control
4. NEW: `resources/views/reportcard/` - All blade templates

### 9. **Usage Instructions for Admins**

**Setting Report Card Release Date**:
1. Use the existing `ReportCardSettingController`
2. Navigate to: `/admin/report-card-settings`
3. Set the `release_at` date and time
4. All students will see countdown until that time

**Monitoring Payments**:
- Check `StudentTransaction` table for type `'report_card_fees'`
- Status progression: pending → successful
- Each transaction stores Flutterwave transaction ID

### 10. **Outstanding Fees Calculation**

The system supports multiple fee tracking methods (adjust in `StudentReportCardController::getOutstandingFees()`):

**Method 1**: Direct field on Student model
```php
$outstanding = $student->outstanding_amount;
```

**Method 2**: From school_fees table
```php
$outstanding = $student->schoolFees()
    ->where('status', '!=', 'paid')
    ->sum('amount');
```

**Method 3**: Calculate from transactions
```php
$paidAmount = $student->transactions()
    ->where('type', 'school_fee')
    ->where('status', 'successful')
    ->sum('amount');
$outstanding = $totalFees - $paidAmount;
```

Choose the method that matches your database structure.

### 11. **Security Features**

✅ CSRF token validation  
✅ Student authentication required  
✅ Flutterwave webhook signature verification  
✅ Transaction reference tracking  
✅ SSL/TLS encryption for payments  
✅ Rate limiting on payment initialization  

### 12. **Testing Checklist**

- [ ] Admin can set report card release date
- [ ] Student with outstanding fees sees blocked view
- [ ] Pay button initializes Flutterwave payment
- [ ] Payment successful redirects and clears fees
- [ ] Student can access report card after payment
- [ ] Countdown timer displays and updates correctly
- [ ] Report card accessible after release date
- [ ] Transaction recorded in StudentTransaction table
- [ ] Email notification sent after payment
- [ ] Webhook processes payment asynchronously

### 13. **Future Enhancements**

Potential additions:
- Email notification when report card becomes available
- SMS notification for payment confirmation
- Partial fee payments
- Payment plan/installment option
- Export report card as PDF
- Email report card directly to student
- Multiple report card formats
- Custom branding on payment page

---

**Implementation Date**: April 3, 2026  
**Last Updated**: April 3, 2026
