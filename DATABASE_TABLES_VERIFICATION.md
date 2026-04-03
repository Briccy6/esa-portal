# Database Tables & Queries Verification

## Correct Tables Used

### 1. COURSE FEES TABLE: `course_term_fees`

**Purpose:** Store fees required for each course in each term

**Columns Used:**
- `course_id` - Which course
- `term_id` - Which term
- `registration_fee` - Registration fee amount
- `tuition_fee` - Tuition fee amount
- `practical_fee` - Practical/lab fee amount
- `trip_fee` - Trip fee amount
- `student_card_fee` - Student card fee amount
- `is_active` - Whether this fee is active

**Example Data:**
```sql
SELECT * FROM course_term_fees;

id  | course_id | term_id | registration_fee | tuition_fee | practical_fee | trip_fee | student_card_fee | is_active
----|-----------|---------|------------------|-------------|---------------|----------|------------------|---------
1   | 5         | 1       | 10,000           | 60,000      | 5,000         | 0        | 5,000            | true
2   | 5         | 2       | 10,000           | 60,000      | 5,000         | 0        | 5,000            | true
3   | 5         | 3       | 10,000           | 50,000      | 0             | 0        | 5,000            | true
```

**Total Fee Calculation:**
- Term 1: 10,000 + 60,000 + 5,000 + 0 + 5,000 = **80,000 RWF**
- Term 2: 10,000 + 60,000 + 5,000 + 0 + 5,000 = **80,000 RWF**
- Term 3: 10,000 + 50,000 + 0 + 0 + 5,000 = **65,000 RWF**

---

### 2. STUDENT PAYMENTS TABLE: `student_transactions`

**Purpose:** Track all payments made by students

**Columns Used:**
- `student_id` - Which student made the payment
- `type` - Type of payment (e.g., 'school_fee', 'subscription', etc.)
- `amount` - Amount paid (RWF)
- `status` - Payment status ('successful', 'pending', 'failed')
- `term_id` - Which term the payment applies to
- `tx_ref` - Transaction reference from payment gateway
- `created_at` - When payment was made

**Example Data:**
```sql
SELECT * FROM student_transactions 
WHERE type = 'school_fee' 
ORDER BY student_id, created_at;

id   | student_id | type       | amount  | status     | term_id | tx_ref                    | created_at
-----|------------|----------|---------|------------|---------|---------------------------|-------------------
100  | 1          | school_fee | 50,000  | successful | 1       | FLWV3S923B11585157359449 | 2026-03-15 10:30
101  | 1          | school_fee | 30,000  | pending    | 1       | FLWV3S923B11585157359450 | 2026-03-20 14:00
102  | 2          | school_fee | 80,000  | successful | 1       | FLWV3S923B11585157359451 | 2026-03-10 09:15
103  | 2          | school_fee | 80,000  | successful | 2       | FLWV3S923B11585157359452 | 2026-04-05 11:20
104  | 3          | school_fee | 40,000  | successful | 1       | FLWV3S923B11585157359453 | 2026-03-12 08:45
```

**Filtering Rules:**
- Only count transactions where `type = 'school_fee'`
- Only count transactions where `status = 'successful'` (NOT pending or failed)
- Only count transactions where `term_id` matches selected terms

---

## Fee Calculation Queries

### Query 1: Get Required Fees for a Student (Selected Terms Only)

```php
// FROM course_term_fees TABLE
$feeQuery = CourseTermFee::where('course_id', $student->course_id)
                         ->where('is_active', true)
                         ->whereIn('term_id', $termIds);  // Only selected terms
                         
$courseTermFees = $feeQuery->get();

// Calculate: Sum of (registration_fee + tuition_fee + practical_fee + trip_fee + student_card_fee)
$requiredFees = $courseTermFees->sum(function($fee) {
    return ($fee->registration_fee ?? 0) 
         + ($fee->tuition_fee ?? 0) 
         + ($fee->practical_fee ?? 0) 
         + ($fee->trip_fee ?? 0) 
         + ($fee->student_card_fee ?? 0);
});
```

**Equivalent SQL:**
```sql
SELECT COALESCE(SUM(registration_fee), 0) 
     + COALESCE(SUM(tuition_fee), 0)
     + COALESCE(SUM(practical_fee), 0)
     + COALESCE(SUM(trip_fee), 0)
     + COALESCE(SUM(student_card_fee), 0) as total_required
FROM course_term_fees
WHERE course_id = 5  -- Student's course
  AND term_id IN (1) -- Selected terms only
  AND is_active = 1;
```

---

### Query 2: Get Paid Amount for a Student (Selected Terms Only)

```php
// FROM student_transactions TABLE
$transactionQuery = StudentTransaction::where('student_id', $student->id)
                                      ->where('type', 'school_fee')  // School fees only
                                      ->where('status', 'successful')  // Successful only
                                      ->whereIn('term_id', $termIds);  // Selected terms only
                                      
$totalPaid = $transactionQuery->sum('amount');
```

**Equivalent SQL:**
```sql
SELECT COALESCE(SUM(amount), 0) as total_paid
FROM student_transactions
WHERE student_id = 1  -- Specific student
  AND type = 'school_fee'  -- School fee payments only
  AND status = 'successful'  -- Confirmed payments only
  AND term_id IN (1);  -- Selected terms only
```

---

### Query 3: Calculate Outstanding

```php
$outstanding = max(0, $requiredFees - $totalPaid);
```

**Logic:**
```
Outstanding = Total Required - Total Paid
If result is negative (overpaid), show 0
```

---

## Real-World Example: Student A, Term 1 Fees Check

### Step 1: Get Required Fees from course_term_fees
```sql
SELECT * FROM course_term_fees
WHERE course_id = 5 (Form 1)
  AND term_id = 1
  AND is_active = 1;

-- Results:
id | course_id | term_id | registration_fee | tuition_fee | practical_fee | trip_fee | student_card_fee
1  | 5         | 1       | 10,000          | 60,000      | 5,000         | 0        | 5,000
```

**Calculation:**
```
Required Fee = 10,000 + 60,000 + 5,000 + 0 + 5,000 = 80,000 RWF
```

### Step 2: Get Payments from student_transactions
```sql
SELECT * FROM student_transactions
WHERE student_id = 1
  AND type = 'school_fee'
  AND status = 'successful'
  AND term_id = 1;

-- Results:
id  | student_id | type       | amount | status     | term_id
100 | 1          | school_fee | 50,000 | successful | 1
```

**Calculation:**
```
Total Paid = 50,000 RWF (only successful payments count)
```

### Step 3: Calculate Outstanding
```
Outstanding = 80,000 - 50,000 = 30,000 RWF

Status: UNPAID ⚠️ (cannot generate report)
```

---

## Verification Queries (For IT/Admin)

### Verify Course Fees Exist

```sql
-- Check if course fees exist for specific course/term
SELECT id, course_id, term_id, 
       registration_fee, tuition_fee, practical_fee, trip_fee, student_card_fee,
       (registration_fee + tuition_fee + practical_fee + trip_fee + student_card_fee) as total
FROM course_term_fees
WHERE course_id = 5 AND term_id = 1 AND is_active = 1;

-- Expected: At least 1 row with non-zero total
```

### Verify Student Transactions with Term IDs

```sql
-- Check if student payments have term_id
SELECT id, student_id, type, amount, status, term_id, created_at
FROM student_transactions
WHERE type = 'school_fee' AND term_id IS NOT NULL
ORDER BY student_id, term_id, created_at
LIMIT 10;

-- Expected: Transactions have term_id populated (NOT NULL)
```

### Verify Calculation for Specific Student

```sql
-- Get all info for one student's fees
SELECT 
  'FEES' as component,
  COUNT(*) as record_count,
  SUM(registration_fee + tuition_fee + practical_fee + trip_fee + student_card_fee) as total_amount,
  GROUP_CONCAT(DISTINCT term_id) as terms
FROM course_term_fees
WHERE course_id = (SELECT course_id FROM students WHERE id = 1)
  AND term_id IN (1)
  AND is_active = 1

UNION

SELECT 
  'PAYMENTS' as component,
  COUNT(*) as record_count,
  SUM(CASE WHEN status = 'successful' THEN amount ELSE 0 END) as total_amount,
  GROUP_CONCAT(DISTINCT term_id) as terms
FROM student_transactions
WHERE student_id = 1 AND type = 'school_fee';

-- Expected: Shows fees and successful payments for comparison
```

### Find Students with Outstanding Fees (Specific Term)

```sql
SELECT 
  s.id,
  s.full_name,
  s.student_id,
  SUM(
    COALESCE(ctf.registration_fee, 0) +
    COALESCE(ctf.tuition_fee, 0) +
    COALESCE(ctf.practical_fee, 0) +
    COALESCE(ctf.trip_fee, 0) +
    COALESCE(ctf.student_card_fee, 0)
  ) as required_fees,
  COALESCE(SUM(CASE WHEN st.status = 'successful' THEN st.amount ELSE 0 END), 0) as paid_fees,
  (
    SUM(
      COALESCE(ctf.registration_fee, 0) +
      COALESCE(ctf.tuition_fee, 0) +
      COALESCE(ctf.practical_fee, 0) +
      COALESCE(ctf.trip_fee, 0) +
      COALESCE(ctf.student_card_fee, 0)
    ) - COALESCE(SUM(CASE WHEN st.status = 'successful' THEN st.amount ELSE 0 END), 0)
  ) as outstanding
FROM students s
LEFT JOIN course_term_fees ctf 
  ON ctf.course_id = s.course_id 
  AND ctf.term_id = 1  -- SELECT TERM
  AND ctf.is_active = 1
LEFT JOIN student_transactions st 
  ON st.student_id = s.id 
  AND st.term_id = 1  -- SELECT TERM
  AND st.type = 'school_fee'
WHERE s.course_id = 5  -- SELECT COURSE
  AND s.academic_year_id = 4  -- SELECT YEAR
GROUP BY s.id
HAVING outstanding > 0
ORDER BY outstanding DESC;

-- Expected: Shows students with unpaid balances for selected term
```

---

## Table Structure Confirmation

### course_term_fees Table Structure
```
Column Name        | Type    | Nullable | Key
-------------------|---------|----------|-----
id                 | BIGINT  | NO       | PRI
course_id          | BIGINT  | NO       | FK (courses)
term_id            | BIGINT  | NO       | FK (terms)
registration_fee   | DECIMAL | YES      | 
tuition_fee        | DECIMAL | YES      |
practical_fee      | DECIMAL | YES      |
trip_fee           | DECIMAL | YES      |
student_card_fee   | DECIMAL | YES      |
discount_fee       | DECIMAL | YES      |
is_active          | BOOLEAN | NO       | DEFAULT TRUE
notes              | TEXT    | YES      |
created_at         | TIMESTAMP | NO     |
updated_at         | TIMESTAMP | NO     |
```

### student_transactions Table Structure
```
Column Name      | Type      | Nullable | Key
-----------------|-----------|----------|-----
id               | BIGINT    | NO       | PRI
student_id       | BIGINT    | NO       | FK (students)
tx_ref           | VARCHAR   | YES      |
transaction_id   | VARCHAR   | YES      |
type             | VARCHAR   | YES      | (school_fee, subscription, etc.)
amount           | DECIMAL   | NO       |
currency         | VARCHAR   | NO       | DEFAULT 'RWF'
status           | VARCHAR   | NO       | (successful, pending, failed)
meta             | JSON      | YES      |
paid_at          | TIMESTAMP | YES      |
term_id          | BIGINT    | YES      | FK (terms) ← IMPORTANT
created_at       | TIMESTAMP | NO       |
updated_at       | TIMESTAMP | NO       |
```

---

## Code Implementation Confirmation

### What the Code Does:

1. ✅ **Fetches from `course_term_fees` table**
   - Where `course_id` = student's course
   - Where `term_id` IN (selected terms)
   - Where `is_active` = true

2. ✅ **Calculates fees by summing columns**
   ```
   Total = registration_fee + tuition_fee + practical_fee + trip_fee + student_card_fee
   ```

3. ✅ **Fetches from `student_transactions` table**
   - Where `student_id` = student's ID
   - Where `type` = 'school_fee'
   - Where `status` = 'successful' (ONLY confirmed payments)
   - Where `term_id` IN (selected terms)

4. ✅ **Sums payments**
   ```
   Total Paid = SUM(amount) from successful transactions
   ```

5. ✅ **Calculates outstanding**
   ```
   Outstanding = max(0, Total Fees - Total Paid)
   ```

---

## Summary

✅ **Using Correct Tables:**
- Fees: `course_term_fees` (NOT a separate school_fees table)
- Payments: `student_transactions` (NOT school_fees table)

✅ **Correct Columns:**
- Fees calculated from: registration_fee + tuition_fee + practical_fee + trip_fee + student_card_fee
- Payments fetched from: amount column

✅ **Correct Filters:**
- Type = 'school_fee' for student transactions
- Status = 'successful' for valid payments
- Term_id matching selected terms

✅ **Correct Calculations:**
- Required = Sum of all fee components
- Paid = Sum of successful payments for selected terms
- Outstanding = Required - Paid

✅ **System is now fetching from the CORRECT tables and columns**
