# Report Card Access Control - Quick Testing Guide

## Pre-Flight Checklist (5 minutes)

### 1. Database Verification
```sql
-- Check if report card settings exist
SELECT * FROM report_card_settings WHERE is_active = 1;

-- Check if test student has report card
SELECT * FROM report_cards 
WHERE student_id = [YOUR_TEST_STUDENT_ID] 
LIMIT 1;

-- Check student fees
SELECT outstanding_amount FROM students 
WHERE id = [YOUR_TEST_STUDENT_ID];

SELECT SUM(amount) FROM school_fees;
```

### 2. Configuration Verification
- [ ] `.env` has `ACADEMIC_YEAR=2026`
- [ ] `.env` has `CURRENT_TERM=Term 1`  
- [ ] `.env` has valid Flutterwave test keys
- [ ] `config/app.php` has the academic_year and current_term settings

### 3. Database Setup (if needed)
```sql
-- Create test report card setting (if not exists)
INSERT INTO report_card_settings 
(academic_year, term, report_card_available_at, is_active, created_at, updated_at)
VALUES 
('2026', 'Term 1', DATE_ADD(NOW(), INTERVAL 2 DAY), 1, NOW(), NOW());

-- Update test student's outstanding amount
UPDATE students SET outstanding_amount = 50000 WHERE id = [TEST_STUDENT_ID];
```

---

## Test Scenario 1: Payment Blocking (5 minutes)

### Setup:
1. Student has outstanding fees: `UPDATE students SET outstanding_amount = 50000 WHERE id = X;`
2. Student has a report card assigned
3. Report card setting exists with future release date

### Test Steps:
1. Log in as test student
2. Navigate to `/student/report-cards`
3. Click on a report card
4. **EXPECT:** Red alert showing "Report Card Access Blocked"
5. **EXPECT:** Outstanding balance: "RWF 50,000.00"
6. **EXPECT:** "Pay Now with Flutterwave" button visible
7. **EXPECT:** Payment method icons displayed (card, mobile money, etc.)

### Verify Code Path:
- [ ] `ReportCardService::getAccessStatus()` returns `is_fully_paid = false`
- [ ] View renders `students/report-cards/payment.blade.php`
- [ ] Payment form posts to correct route

---

## Test Scenario 2: Countdown Timer (5 minutes)

### Setup:
1. Clear student fees: `UPDATE students SET outstanding_amount = 0 WHERE id = X;`
2. Student has a report card assigned
3. Report card setting exists with future release date (2-3 days from now)

### Test Steps:
1. Log in as test student
2. Navigate to `/student/report-cards/{id}`
3. **EXPECT:** Blue/gradient card with "Report Card Coming Soon" heading
4. **EXPECT:** Release date/time displayed (e.g., "January 15, 2025 2:00 PM")
5. **EXPECT:** Countdown timer showing Days:Hours:Minutes:Seconds
6. **EXPECT:** Timer decrements every second
7. **EXPECT:** Numbers are zero-padded (00:05:23)
8. **EXPECT:** "Fees Status: All fees are paid ✓"
9. **EXPECT:** "Schedule: Waiting for release date"

### Verify Code Path:
- [ ] `ReportCardService::getAccessStatus()` returns `countdown_data != null`
- [ ] View renders `students/report-cards/countdown.blade.php`
- [ ] JavaScript initializes countdown with correct target date
- [ ] Console shows no errors (open browser devtools)

### Manual Timer Test:
```javascript
// In browser console:
const target = new Date('2025-01-15T14:00:00Z').getTime();
const now = new Date().getTime();
const diff = target - now;
console.log('Milliseconds until release:', diff);
```

---

## Test Scenario 3: Full Access (5 minutes)

### Setup:
1. Clear student fees: `UPDATE students SET outstanding_amount = 0 WHERE id = X;`
2. Update report card setting to past date:
   ```sql
   UPDATE report_card_settings 
   SET report_card_available_at = DATE_SUB(NOW(), INTERVAL 1 DAY)
   WHERE id = [SETTING_ID];
   ```
3. Student has a report card assigned

### Test Steps:
1. Log in as test student
2. Navigate to `/student/report-cards/{id}`
3. **EXPECT:** Full report card displayed
4. **EXPECT:** School logo and name visible
5. **EXPECT:** Student information shown
6. **EXPECT:** Subject list with grades displayed
7. **EXPECT:** Print button available
8. **EXPECT:** Download button available (if configured)

### Verify Code Path:
- [ ] `ReportCardService::getAccessStatus()` returns `can_access = true`
- [ ] View renders `students/report-cards/show.blade.php`
- [ ] No access-blocked or countdown elements visible

---

## Test Scenario 4: Payment Processing (10 minutes)

### Setup:
1. Student has outstanding fees: `UPDATE students SET outstanding_amount = 50000 WHERE id = X;`
2. Student has report card assigned
3. Flutterwave test keys configured

### Test Steps:
1. Log in as test student
2. Navigate to report card (should show payment view)
3. Click "Pay Now with Flutterwave" button
4. **EXPECT:** Flutterwave form submits to `/flutterwave/payment/initialize-reportcard-fees`
5. **EXPECT:** Browser redirects to Flutterwave checkout page
6. **EXPECT:** Checkout page shows payment amount (RWF 50,000)
7. Complete payment on Flutterwave (use test card if available)
8. **EXPECT:** Redirected back to your app
9. Check database:
   ```sql
   SELECT * FROM student_transactions 
   WHERE student_id = X AND type = 'report_card_fees'
   ORDER BY created_at DESC LIMIT 1;
   ```
   - `status` should be 'successful'
   - `tx_ref` should start with 'RFC-'

10. Check student record:
    ```sql
    SELECT outstanding_amount FROM students WHERE id = X;
    ```
    - `outstanding_amount` should be 0 or reduced by payment amount

### Browser Console Checks:
```javascript
// Open browser DevTools → Console
// You should see no errors
// Check Network tab for successful POST to initialize endpoint
```

### Verify Payment Transitions:
1. After payment, some payment should show countdown (if not yet available) or full report card
2. No payment blocking should occur anymore
3. If available date is now in past, full report card should show

---

## Test Scenario 5: Edge Cases (10 minutes)

### Edge Case 1: No Active Report Card Setting
- [ ] Remove all active settings (set `is_active = 0`)
- [ ] Access report card
- **EXPECT:** Sees message "Report card access not configured by administration"
- [ ] Should not crash or show error

### Edge Case 2: Access Denial
- [ ] Log in as Student A
- [ ] Try accessing Student B's report card via URL
- **EXPECT:** 403 Forbidden error
- [ ] Not a blank page or server error

### Edge Case 3: Missing Report Card Setting
- [ ] Delete the report card setting
- [ ] Access report card
- **EXPECT:** ReportCardService handles gracefully
- [ ] No database errors in logs

### Edge Case 4: Zero Outstanding Fees
- [ ] Student has `outstanding_amount = 0`
- [ ] Try to pay
- **EXPECT:** Payment form shows "No outstanding fees to pay"
- [ ] Doesn't allow partial/zero payments

---

## Database Inspection Queries

### Check All Components Are In Place:
```sql
-- 1. Active Report Card Settings
SELECT 'Report Card Settings' as check_type, COUNT(*) as count 
FROM report_card_settings WHERE is_active = 1;

-- 2. Test Student Report Cards
SELECT 'Student Report Cards' as check_type, COUNT(*) as count 
FROM report_cards WHERE student_id = [TEST_STUDENT_ID];

-- 3. Student Transaction History
SELECT 'Payment Transactions' as check_type, COUNT(*) as count 
FROM student_transactions WHERE student_id = [TEST_STUDENT_ID] 
AND type = 'report_card_fees';

-- 4. Student Outstanding Fees
SELECT id, full_name, outstanding_amount FROM students WHERE id = [TEST_STUDENT_ID];

-- 5. Latest Payment Transaction
SELECT tx_ref, amount, status, paid_at, created_at 
FROM student_transactions 
WHERE student_id = [TEST_STUDENT_ID] AND type = 'report_card_fees'
ORDER BY created_at DESC LIMIT 1;
```

---

## Log File Locations

Check these if tests fail:

```bash
# Application logs
tail -100 storage/logs/laravel.log

# Look for keywords:
# - "Flutterwave"
# - "report_card_fees"
# - "Report card fee payment"
# - "Webhook"
```

---

## Common Issues & Quick Fixes

### Issue: Payment button doesn't work
**Fix:**
1. Check Flutterwave keys in `.env`
2. Verify route exists: `php artisan route:list | grep report-card`
3. Clear cache: `php artisan cache:clear`
4. Check browser console for JavaScript errors

### Issue: Countdown shows but doesn't update
**Fix:**
1. Check browser console (F12)
2. Verify JavaScript is enabled
3. Check that target date is ISO8601 formatted
4. Verify date is in future (not past)

### Issue: Payment succeeds but fees don't clear
**Fix:**
1. Check webhook logs in `storage/logs/laravel.log`
2. Verify webhook signature hash in `.env`
3. Check that transaction was created with type 'report_card_fees'
4. Manually check `student_transactions` table for the payment

### Issue: "Access not configured" message
**Fix:**
1. Create report card setting: 
   ```sql
   INSERT INTO report_card_settings 
   (academic_year, term, report_card_available_at, is_active, created_at, updated_at)
   VALUES ('2026', 'Term 1', NOW(), 1, NOW(), NOW());
   ```
2. Verify `config/app.php` has matching academic_year/term
3. Or ensure at least one active setting exists

---

## Success Criteria

Test is successful when:
- ✅ Payment view blocks access with correct amount displayed
- ✅ Countdown timer displays and updates every second
- ✅ Full report card displays when eligible
- ✅ Flutterwave payment initializes without errors
- ✅ Student fees are cleared after successful payment
- ✅ No error messages in browser console
- ✅ No database errors in application logs
- ✅ Transaction records created with proper status
- ✅ Payment transitions to report card view correctly

---

## Support Contact

For issues:
1. Check `REPORT_CARD_ACCESS_IMPLEMENTATION.md` for detailed documentation
2. Review application logs: `storage/logs/laravel.log`
3. Run database verification queries above
4. Check browser console and network tab

**Test Coverage Time:** ~30-40 minutes for full scenario testing
**Estimated Stability:** 95% (pending database configuration)
