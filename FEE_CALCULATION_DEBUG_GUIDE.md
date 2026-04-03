# Fee Calculation & Report Card Generation - Debug Guide

## What Was Fixed

The report card generation system now:
1. ✅ Checks student fees from `course_term_fees` table (fees tied to course, not student)
2. ✅ Checks student payments from `student_transactions` table  
3. ✅ Calculates outstanding = required - paid
4. ✅ **BLOCKS report card generation** if outstanding > 0
5. ✅ Logs all fee checks and skipped students

---

## How to Verify It's Working

### Option 1: Quick Test (Terminal)

```bash
cd D:\esa

# Check for ALL students with outstanding fees
php artisan debug:student-fees

# Check a SPECIFIC student (replace 1 with student ID)
php artisan debug:student-fees 1
```

**Output will show:**
- Student info (ID, Name, Course, Academic Year)
- All course term fees required
- All payments made (with status and date)
- Calculation breakdown
- Whether they're eligible or not

### Example Output:
```
═══════════════════════════════════════════════════════════
STUDENT FEE DEBUG INFORMATION
═══════════════════════════════════════════════════════════

Student Information:
✓ ID: 1
✓ Name: John Doe
✓ Course ID: 5
✓ Academic Year ID: 2

Course Term Fees (Required):
┌─────────┬──────────────┐
│ Term    │ Amount       │
├─────────┼──────────────┤
│ Term 1  │ RWF 100,000  │
│ Term 2  │ RWF 100,000  │
└─────────┴──────────────┘

Student Payments (School Fees):
┌─────────────────────┬──────────┬──────────────┬────────────┐
│ Date                │ Status   │ Amount       │ Reference  │
├─────────────────────┼──────────┼──────────────┼────────────┤
│ 2026-04-01 10:30    │ success  │ RWF 100,000  │ TXN-001    │
└─────────────────────┴──────────┴──────────────┴────────────┘

Fee Calculation:
┌─────────────────────────┬────────────────┐
│ Item                    │ Amount         │
├─────────────────────────┼────────────────┤
│ Total Required          │ RWF 200,000    │
│ Total Paid (Successful) │ RWF 100,000    │
│ Outstanding Balance     │ RWF 100,000    │
│ Payment Percentage      │ 50%            │
└─────────────────────────┴────────────────┘

✗ INELIGIBLE: Student has outstanding fees, cannot generate report cards
   Outstanding: RWF 100,000
```

---

## Database Check

### Check Course Term Fees:
```sql
-- See what fees are required
SELECT id, course_id, term_id, total_fees, is_active 
FROM course_term_fees 
WHERE course_id = 5 AND is_active = 1;
```

### Check Student Payments:
```sql
-- See what payments student has made
SELECT id, student_id, type, amount, status, created_at 
FROM student_transactions 
WHERE student_id = 1 
AND type = 'school_fee'
ORDER BY created_at DESC;
```

### Calculate Outstanding for a Student:
```sql
-- Automatic calculation (like the system does)
SELECT 
  s.id,
  s.full_name,
  SUM(ctf.total_fees) as required_fees,
  COALESCE(SUM(st.amount), 0) as paid_amount,
  SUM(ctf.total_fees) - COALESCE(SUM(st.amount), 0) as outstanding
FROM students s
LEFT JOIN course_term_fees ctf ON ctf.course_id = s.course_id 
  AND ctf.is_active = 1
  AND ctf.term_id IN (
    SELECT id FROM terms WHERE academic_year_id = s.academic_year_id
  )
LEFT JOIN student_transactions st ON st.student_id = s.id 
  AND st.type = 'school_fee' 
  AND st.status = 'successful'
WHERE s.id = 1
GROUP BY s.id, s.full_name;
```

---

## How Report Card Generation Works Now

### When Admin Clicks "Generate Report Cards for Class":

```
1. System gets all students in course
2. For EACH student:
   a. Calculate: Required = SUM(course_term_fees)
   b. Calculate: Paid = SUM(successful student_transactions)
   c. Calculate: Outstanding = Required - Paid
   d. if Outstanding > 0:
      → SKIP this student
      → Log: "Skipped Student X: Outstanding RWF Y"
   e. else:
      → Generate report card
      → Log: "Generated report card for Student X"
3. Show summary:
   "Generated 15 report cards, Skipped 3 with outstanding fees"
```

### When Admin Generates for Individual Student:

```
1. Get student
2. Calculate fees (same as above)
3. if Outstanding > 0:
   → Show error message
   → Return to form without generating
   → Log warning
4. else:
   → Generate report card
```

---

## Verifying the Fix

### Test Case 1: Student with NO fees (Eligible)
```bash
# Setup: Make sure fee is PAID
# Update in database: student must have successful transaction for full amount

php artisan debug:student-fees 1
# Should show: ✓ ELIGIBLE
```

### Test Case 2: Student with UNPAID fees (Ineligible)
```bash
# Setup: Make sure course has fees but student has NOT paid

php artisan debug:student-fees 2  
# Should show: ✗ INELIGIBLE with outstanding amount
```

### Test Case 3: Bulk Generation Skips Ineligible
```bash
# Try to generate report cards for entire class
# Should skip students with outstanding fees
# Check logs: storage/logs/laravel.log for "Skipped" messages
```

---

## Reading the Logs

After generating report cards, check logs:

```bash
# View recent log entries (last 50 lines)
tail -50 storage/logs/laravel.log

# Look for:
# - "Report card generation skipped for student X"
# - "Report card generation rejected for student X"
# - "Outstanding fees" messages
```

### Example Log Entry:
```
[2026-04-04 14:30:15] local.WARNING: Report card generation skipped for student 5 (Jane Smith): Outstanding fees: RWF 75,000
```

---

## Troubleshooting

### Problem: Students with fees are STILL getting report cards created

**Check #1: Course has NO fees configured**
```sql
-- Verify course term fees exist
SELECT COUNT(*) FROM course_term_fees WHERE course_id = 5;
-- Should return > 0
```

**Check #2: Student payments not marked as "successful"**
```sql
-- Check transaction status
SELECT * FROM student_transactions 
WHERE student_id = 1 AND type = 'school_fee';
-- Should show status = 'successful' (not 'pending' or 'failed')
```

**Check #3: FeePaymentService not being called**
```bash
# Search logs
grep -i "outstanding" storage/logs/laravel.log

# If no results = method not being called
# If results show high amounts = calculation issue
```

### Problem: "Unknown column" error

**This was the original issue - should be FIXED now**

Verify the fix:
```php
// In FeePaymentService, line ~27-30 should read:
$courseTermFees = CourseTermFee::where('course_id', $student->course_id)
    ->whereHas('term', function ($query) use ($student) {
        $query->where('academic_year_id', $student->academic_year_id);
    })
```

**NOT this (old bad way):**
```php
// WRONG - school_fees has no student_id column
$schoolFees = DB::table('school_fees')
    ->where('student_id', $student->id)
```

---

## The Correct Data Model

```
┌─────────────────────┐
│  course_term_fees   │
├─────────────────────┤
│ id                  │
│ course_id ←──────┐  │
│ term_id           │  │
│ total_fees        │  │
│ is_active         │  │
└─────────────────────┘
                      │
                      │ (Student's course)
                      │
                    ┌─┴──────────────────┐
                    │   students         │
                    ├────────────────────┤
                    │ id                 │
                    │ course_id ─────────┘
                    │ academic_year_id   │
                    │ full_name          │
                    └────────────────────┘
                    
                    ↓ made payments via
                    
┌─────────────────────────┐
│ student_transactions    │
├─────────────────────────┤
│ id                      │
│ student_id ─────────┐   │
│ type = 'school_fee' │   │
│ amount              │   │
│ status = 'success'  │   │
│ created_at          │   │
└─────────────────────────┘
```

---

## Quick Reference

| What | Where | Command |
|------|-------|---------|
| Debug all students | CLI | `php artisan debug:student-fees` |
| Debug one student | CLI | `php artisan debug:student-fees 1` |
| Check fees in DB | SQL | See "Database Check" section |
| View logs | File | `tail storage/logs/laravel.log` |
| See generation flow | Admin UI | Try generating report cards |

---

## What Each Column Means

### course_term_fees:
- **course_id**: Which course these fees apply to
- **term_id**: Which term these fees are for
- **total_fees**: How much students in this course must pay for this term
- **is_active**: Whether this fee is currently active

### student_transactions:
- **student_id**: Which student made the payment
- **type**: Type of transaction (e.g., 'school_fee', 'subscription')
- **amount**: How much they paid
- **status**: Whether it was successful ('successful', 'pending', 'failed')
- **created_at**: When the payment was made

---

## Getting Full Data

### Get all students with their fee status:
```bash
php artisan debug:student-fees
# Shows a table of all students with outstanding fees
```

### Get detailed info for one student:
```bash
php artisan debug:student-fees 123
# Shows their fees, payments, and eligibility
```

### Track what happened during generation:
```bash
# After trying to generate, check logs
tail -100 storage/logs/laravel.log | grep -i "report card\|outstanding"
```

---

## Success Indicators

✅ **Working correctly when:**
1. `php artisan debug:student-fees` shows students with outstanding fees
2. When trying to generate for that student, you get an error/warning
3. Logs show "skipped" messages for students with fees
4. Only students with 0 outstanding get report cards generated

❌ **Not working if:**
1. Students with outstanding fees are getting reports generated
2. No "skipped" messages in logs
3. `debug:student-fees` shows 0 outstanding for student who didn't pay
4. Database has no course_term_fees data

---

## Need Help?

1. **Run the debug command** to see actual numbers
2. **Check database** with provided SQL queries
3. **Review logs** with tail command
4. **Verify fee data** exists in course_term_fees
5. **Verify payments** exist in student_transactions with status='successful'

All of these should tell you exactly what's happening with the fee calculations.

