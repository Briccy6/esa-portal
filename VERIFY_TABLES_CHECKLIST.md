# ✅ Tables Verification Checklist

## Quick Verification Steps

### Step 1: Verify Database Tables Exist
```bash
# In MySQL/your database:
SHOW TABLES LIKE 'course_term_fees';
SHOW TABLES LIKE 'student_transactions';
```
- [ ] `course_term_fees` table exists
- [ ] `student_transactions` table exists

### Step 2: Verify course_term_fees Has Correct Columns
```bash
DESCRIBE course_term_fees;
```
- [ ] registration_fee column exists
- [ ] tuition_fee column exists
- [ ] practical_fee column exists
- [ ] trip_fee column exists
- [ ] student_card_fee column exists
- [ ] course_id column exists
- [ ] term_id column exists
- [ ] is_active column exists

### Step 3: Verify student_transactions Has Correct Columns
```bash
DESCRIBE student_transactions;
```
- [ ] student_id column exists
- [ ] type column exists (should contain 'school_fee')
- [ ] amount column exists
- [ ] status column exists (should contain 'successful', 'pending', 'failed')
- [ ] term_id column exists
- [ ] tx_ref column exists

### Step 4: Sample Data Check
```bash
# Check if course_term_fees has data
SELECT COUNT(*) FROM course_term_fees WHERE is_active = 1;

# Check if student_transactions has school fee data
SELECT COUNT(*) FROM student_transactions 
WHERE type = 'school_fee' AND status = 'successful';

# Check if there are students with outstanding fees
SELECT COUNT(*) FROM student_transactions 
WHERE type = 'school_fee' AND status IN ('pending', 'failed');
```
- [ ] course_term_fees has at least 1 active record
- [ ] student_transactions has at least 1 successful payment
- [ ] (Optional) Check for pending/failed payments too

### Step 5: Test Code Verification
```bash
# Run this command:
php -l app/Services/FeePaymentService.php
```
- [ ] No syntax errors in FeePaymentService.php
- [ ] Output shows: "No syntax errors detected"

### Step 6: Run Automated Verification
```bash
# In Laravel
php artisan tinker

# Then paste:
include 'verify_fee_database.php'
```
- [ ] ✓ course_term_fees record displayed
- [ ] ✓ student_transactions record displayed
- [ ] ✓ term_id populated in transactions
- [ ] ✓ Calculation shows actual values
- [ ] ✓ FeePaymentService returns results

### Step 7: Test Two-Step Workflow
In your browser:
1. Go to "Report Cards" → "Generate Reports"
2. Select Course and Year
3. Select Term(s)
4. Leave Student blank
5. Click "Step 1: Check Class Fees Before Generating"

Expected result:
- [ ] Page loads successfully
- [ ] Shows all students in that course/year
- [ ] Shows "Required", "Paid", "Outstanding" for each
- [ ] Students with unpaid fees marked as "Unpaid ⚠️"
- [ ] Can see payment history when expanded
- [ ] Amounts match database values

### Step 8: Verify Fee Calculation
On the fees page, pick one student and verify manually:

```bash
# Query the database
SELECT SUM(registration_fee + tuition_fee + practical_fee + trip_fee + student_card_fee) as total
FROM course_term_fees 
WHERE course_id = [student's course]
  AND term_id IN ([selected terms])
  AND is_active = 1;

# Get paid amount
SELECT SUM(amount) FROM student_transactions
WHERE student_id = [student's id]
  AND type = 'school_fee'
  AND status = 'successful'
  AND term_id IN ([selected terms]);
```
- [ ] Required fees match database calculation
- [ ] Paid amount matches database calculation
- [ ] Outstanding = Required - Paid
- [ ] Matches what's shown on fees page

### Step 9: Generate Reports
1. After reviewing fees, click "Proceed to Generate Reports"
2. Reports should only generate for students with 0 outstanding fees
3. Students with outstanding fees should be skipped

Expected result:
- [ ] System shows which students generated successfully
- [ ] System shows which students were skipped (with amounts)
- [ ] Only eligible students received reports
- [ ] Message confirms "course_term_fees" and "student_transactions" tables used

---

## Database Query Verification

### To verify fees are being calculated correctly:

```sql
-- Query 1: Check total fees per course per term
SELECT 
    ct.id,
    ct.course_id,
    ct.term_id,
    (ct.registration_fee + ct.tuition_fee + ct.practical_fee + ct.trip_fee + ct.student_card_fee) as total_required,
    ct.registration_fee,
    ct.tuition_fee,
    ct.practical_fee,
    ct.trip_fee,
    ct.student_card_fee
FROM course_term_fees ct
WHERE ct.is_active = 1
LIMIT 5;
```

### To verify payments are being summed correctly:

```sql
-- Query 2: Check student payments per term
SELECT 
    student_id,
    term_id,
    SUM(amount) as total_paid,
    COUNT(*) as payment_count
FROM student_transactions
WHERE type = 'school_fee' 
  AND status = 'successful'
GROUP BY student_id, term_id
LIMIT 5;
```

### To calculate outstanding fees:

```sql
-- Query 3: Outstanding = Required - Paid
SELECT 
    s.id as student_id,
    s.name,
    ct.total_required,
    COALESCE(st.total_paid, 0) as total_paid,
    (ct.total_required - COALESCE(st.total_paid, 0)) as outstanding
FROM students s
JOIN course_term_fees ct ON s.course_id = ct.course_id
LEFT JOIN (
    SELECT student_id, term_id, SUM(amount) as total_paid
    FROM student_transactions
    WHERE type = 'school_fee' AND status = 'successful'
    GROUP BY student_id, term_id
) st ON s.id = st.student_id AND ct.term_id = st.term_id
LIMIT 10;
```

---

## ✅ All Steps Completed?

If all checkboxes are checked, then:
- ✅ Database tables exist and have correct columns
- ✅ Your data is properly stored in course_term_fees and student_transactions
- ✅ PHP code has no syntax errors
- ✅ Code logic correctly fetches from the right tables
- ✅ Fee calculations are accurate
- ✅ Two-step workflow displays all students correctly
- ✅ Report generation respects term-specific fees
- ✅ System is ready for production use

## Need Help?

If any step fails:
1. Check that you're using the exact tables: `course_term_fees` and `student_transactions`
2. Verify that all students in the course have fees set in `course_term_fees`
3. Check that student payments are recorded in `student_transactions` with `type='school_fee'`
4. Run the verification script to see actual values
5. Check logs: `storage/logs/laravel.log`
