# Database Relationships - Fee Calculation System

## Visual Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                        COURSE TERM FEES                         │
│  (What each course must pay per term)                           │
├─────────────────────────────────────────────────────────────────┤
│ id  │ course_id │ term_id │ total_fees    │ is_active          │
├─────┼───────────┼─────────┼───────────────┼────────────────────┤
│ 1   │ 5         │ 1       │ 100,000 RWF   │ true               │
│ 2   │ 5         │ 2       │ 100,000 RWF   │ true               │
│ 3   │ 5         │ 3       │ 50,000 RWF    │ true               │
│ 4   │ 6         │ 1       │ 150,000 RWF   │ true               │
└─────┴───────────┴─────────┴───────────────┴────────────────────┘
                    │                │
                    │                └──── TERMS
                    │                      ├─ Term 1
                    │                      ├─ Term 2
                    │                      └─ Term 3
                    │
                    └──── COURSES
                           ├─ Form 1 (ID: 5)
                           ├─ Form 2 (ID: 6)
                           └─ Form 3


                    ┌──────────────┐
                    │   STUDENTS   │
                    ├──────────────┤
                    │ id = 1       │
                    │ course_id=5  │◄─────── Linked to Form 1
                    │ name="John"  │
                    └──────────────┘
                           │
                           │ Made payments
                           ↓
        ┌─────────────────────────────────────────────┐
        │         STUDENT TRANSACTIONS                │
        │  (What student paid for school fees)       │
        ├─────────────────────────────────────────────┤
        │ id │ student_id │ type        │ amount │   │
        ├────┼────────────┼─────────────┼────────┤   │
        │ 1  │ 1          │ school_fee  │ 100k   │✓  │  (successful)
        │ 2  │ 1          │ school_fee  │ 50k    │✓  │  (successful)
        │ 3  │ 1          │ school_fee  │ 50k    │?  │  (pending)
        └─────────────────────────────────────────────┘


FEE CALCULATION FOR STUDENT ID 1:

  Required Fees:
    Term 1: 100,000 (from course_term_fees row 1)
  + Term 2: 100,000 (from course_term_fees row 2)
  + Term 3:  50,000 (from course_term_fees row 3)
  ──────────────────
  Total Required: 250,000 RWF

  Paid Amounts (successful only):
    Payment 1: 100,000 RWF ✓
  + Payment 2:  50,000 RWF ✓
  ──────────────────
  Total Paid: 150,000 RWF

  Outstanding = 250,000 - 150,000 = 100,000 RWF
  
  Can Generate Report? NO (outstanding > 0)
```

---

## How FeePaymentService Queries Work

### Query 1: Get All Course Term Fees
```php
$courseTermFees = CourseTermFee::where('course_id', $student->course_id)
    ->whereHas('term', function ($query) use ($student) {
        $query->where('academic_year_id', $student->academic_year_id);
    })
    ->where('is_active', true)
    ->get();
```

**Translation:**
- Find all course_term_fees where:
  - course_id = student's course
  - term's academic_year_id = student's academic year
  - is_active = true (being enforced)

**Result:** All fees this student owes for all terms

### Query 2: Sum Required Fees
```php
$requiredFees = $courseTermFees->sum('total_fees');
```
**Translation:** Add up all the total_fees amounts
**Result:** Total RWF required

### Query 3: Get All Successful Payments
```php
$totalPaid = StudentTransaction::where('student_id', $student->id)
    ->where('type', 'school_fee')
    ->where('status', 'successful')
    ->sum('amount');
```

**Translation:**
- Find all student_transactions where:
  - student_id = this student
  - type = 'school_fee' (not other transaction types)
  - status = 'successful' (not pending or failed)
- Sum the amounts

**Result:** Total RWF successfully paid by student

### Query 4: Calculate Outstanding
```php
$outstanding = max(0, $requiredFees - $totalPaid);
```

**Translation:** Subtract paid from required (but never go below 0)
**Result:** How much they still owe

---

## Complete Table Relationships

```
                            ┌─────────────┐
                            │ ACADEMIC    │
                            │ YEARS       │
                            ├─────────────┤
                            │ id          │
                            │ year        │
                            └─────────────┘
                                   ▲
                                   │ has many
                                   │
                    ┌──────────────┴──────────────┐
                    │        TERMS               │
                    ├────────────────────────────┤
                    │ id                         │
                    │ academic_year_id ► ▲       │
                    │ name                       │
                    └────────────────────────────┘
                           ▲
                           │ 1 term
    ┌──────────────────────┴──────────────────────┐
    │      COURSE TERM FEES                       │
    ├───────────────────────────────────────────┐ │
    │ For THIS course, in THIS term, fees are:  │ │
    │ course_id ──────┐                          │ │
    │ term_id ────────┼──────┐                   │ │
    │ total_fees      │      │                   │ │
    └───────────────────────────────────────────┘ │
         ▲            │      │                    │
         │            │      └────────────────────┘
         └────────────┼────────────┐
              belongs │            │ in
              to      │            │
         ┌────────────┴─┐      ┌───┴─────────┐
         │   COURSES    │      │    TERMS    │
         ├──────────────┤      └─────────────┘
         │ id           │
         │ name         │
         └──────────────┘
              ▲
              │ attends
              │
         ┌────┴──────────────┐
         │     STUDENTS      │
         ├───────────────────┤
         │ id                │
         │ course_id ────────┘
         │ academic_year_id ──┐
         └───────────────────┐ │
                             │ │
         ┌───────────────────┘ │
         │  STUDENT            │
         │  TRANSACTIONS       │
         │ ───────────────────│
    ┌────┤ student_id ◄──────┘
    │    │ type  ────────┐
    │    │ amount        │ Must be
    │    │ status ◄──────┘ 'school_fee'
    │    │              & 'successful'
    │    └────────────────
    │
    │ (Sum all successful school_fee payments)
    │
    ↓
TOTAL PAID RWF
```

---

## Step-by-Step Example

### Student: John Doe (ID: 1)
- Course: Form 2 (ID: 5)
- Academic Year: 2025-2026 (ID: 2)

### Step 1: Find Course Term Fees
```sql
SELECT total_fees FROM course_term_fees 
WHERE course_id = 5 
  AND term_id IN (1, 2, 3)  -- All terms in academic year 2
  AND is_active = 1;
```
**Result:**
- Term 1: 100,000
- Term 2: 100,000
- Term 3: 50,000
**Total Required: 250,000**

### Step 2: Find Student Payments
```sql
SELECT SUM(amount) FROM student_transactions
WHERE student_id = 1
  AND type = 'school_fee'
  AND status = 'successful';
```
**Result:**
- Payment 1: 100,000 RWF
- Payment 2: 50,000 RWF
- Payment 3 (pending): 50,000 RWF (NOT counted - status != 'successful')
**Total Paid: 150,000**

### Step 3: Calculate Outstanding
```
Outstanding = 250,000 - 150,000 = 100,000 RWF
```

### Step 4: Decision
```
100,000 > 0?  YES → Block report generation
              Message: "Cannot generate. Outstanding: RWF 100,000"
```

---

## Key Points About the Data Model

| Concept | Table | Linked By | Meaning |
|---------|-------|-----------|---------|
| Course Fees | course_term_fees | course_id + term_id | What a course costs per term |
| Student | students | id | Individual student |
| Payment | student_transactions | student_id | Money student paid |
| Status | transaction.status | 'successful' | Payment actually completed |

**Critical:** Fees are **NOT** in a "school_fees" table by student
- Fees are **course-based** (all Form 1 students pay same)
- Stored in **course_term_fees**
- Payments tracked in **student_transactions**

---

## Database Schema (Simplified)

```sql
-- Fees (tied to courses, not students)
CREATE TABLE course_term_fees (
  id INT PRIMARY KEY,
  course_id INT,          -- Which course
  term_id INT,            -- Which term
  total_fees DECIMAL,     -- How much to pay
  is_active BOOLEAN       -- Currently active
);

-- Payments (tied to students)
CREATE TABLE student_transactions (
  id INT PRIMARY KEY,
  student_id INT,         -- Which student paid
  type VARCHAR,           -- 'school_fee', 'subscription', etc.
  amount DECIMAL,         -- How much paid
  status VARCHAR,         -- 'successful', 'pending', 'failed'
  created_at TIMESTAMP    -- When paid
);

-- Students (tied to courses and years)
CREATE TABLE students (
  id INT PRIMARY KEY,
  full_name VARCHAR,
  course_id INT,          -- Which course attending
  academic_year_id INT    -- Which academic year
);
```

---

## Validation Query

To verify fees are being calculated correctly:

```sql
SELECT 
  s.id,
  s.full_name,
  s.course_id,
  s.academic_year_id,
  SUM(ctf.total_fees) as total_required,
  COALESCE(SUM(st.amount), 0) as total_paid,
  SUM(ctf.total_fees) - COALESCE(SUM(st.amount), 0) as outstanding,
  CASE 
    WHEN SUM(ctf.total_fees) - COALESCE(SUM(st.amount), 0) > 0 
    THEN 'INELIGIBLE'
    ELSE 'ELIGIBLE'
  END as status
FROM students s
LEFT JOIN course_term_fees ctf 
  ON ctf.course_id = s.course_id 
  AND ctf.is_active = 1
  AND ctf.term_id IN (
    SELECT id FROM terms 
    WHERE academic_year_id = s.academic_year_id
  )
LEFT JOIN student_transactions st 
  ON st.student_id = s.id 
  AND st.type = 'school_fee'
  AND st.status = 'successful'
GROUP BY s.id, s.full_name, s.course_id, s.academic_year_id
HAVING outstanding > 0
ORDER BY outstanding DESC;
```

**This shows:**
- All students with outstanding fees
- Exact amounts required and paid
- Their eligibility status

---

## Summary

✅ **Correct Data Model:**
- Fees in `course_term_fees` (course-based)
- Payments in `student_transactions` (student-based)
- Linked through course + term + student

✅ **FeePaymentService does:**
1. Get all course_term_fees for student's course and academic year
2. Sum them to get total required
3. Get all successful student_transactions for that student
4. Sum them to get total paid
5. Calculate outstanding = required - paid

✅ **Result:**
- Accurate fee calculation
- No students missed
- All terms checked
- Full audit trail

