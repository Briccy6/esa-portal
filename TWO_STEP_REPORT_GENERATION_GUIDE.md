# Two-Step Report Generation with Fee Checking - Workflow Guide

## Overview

The system now requires a **mandatory two-step process** for generating bulk class reports:

1. **Step 1: Check Class Fees** - View all students' fee status before generating
2. **Step 2: Generate Reports** - Only eligible students (fully paid) proceed

This ensures admins know exactly which students don't have reports due to unpaid fees, and allows sending payment reminders before generating.

---

## The New Workflow

### For Single Student Report Generation

**Path:**  
Report Cards → Generate New → Select Student → Select Course → Select Terms → **Generate Report Card**

✅ **Behavior:**
- Direct generation (no fee check required)
- System still validates fees before creating report
- If student has outstanding fees, blocked with error message

### For Class/Bulk Report Generation

**Path:**  
Report Cards → Generate New → Select Course → Select Terms → **Check Class Fees** → **Proceed to Generate Reports**

✅ **Behavior:**
- Must check fees FIRST (mandatory)
- Shows all students in class with their fee status
- Shows payment history for each student
- Option to send payment reminders
- Then can proceed to bulk generate reports

---

## Step-by-Step Guide

### STEP 1: Go to Report Card Generation Form

1. Navigate to **Admin Dashboard → Report Cards → Generate New Report Card**
2. You'll see a form with fields for:
   - Student (optional - leave blank for class generation)
   - Course (required)
   - Academic Year (required)
   - Term(s) (required - select specific terms)

### STEP 2A: For Single Student (Optional)

If you want to generate a report for **ONE student**:

1. Select the **Student** from dropdown
2. Select **Course**
3. Select **Academic Year**
4. Select **Term(s)**
5. Click **"Generate Report Card for Student"** button
6. System validates and generates immediately

### STEP 2B: For Class Generation (Recommended)

If you want to generate reports for an **ENTIRE CLASS**:

1. **LEAVE the Student field BLANK**
2. Select **Course** (e.g., "Form 3")
3. Select **Academic Year** (e.g., "2025-2026")
4. Select **Term(s)** (e.g., "Term 1")
   - You can select multiple terms
   - Each term will have separate report cards
5. Click **"Step 1: Check Class Fees Before Generating"** button

### STEP 3: Check Class Fees Page

You'll be taken to a comprehensive fees page showing:

#### Overview Summary
- **Course:** Which class is being checked
- **Year:** Academic year
- **Total Students:** How many in the class
- **Unpaid Students:** How many haven't paid fully for selected terms
- **Financial Summary:** Total required vs paid vs outstanding

#### Detailed Student Table
For EACH student, you see:

| Column | Shows |
|--------|-------|
| Student ID | Unique identifier |
| Name | Student name + email |
| Required | Total fees for selected term(s) |
| Paid | Amount student has paid for selected term(s) |
| Outstanding | Amount still owed |
| Progress | Payment progress bar (%) |
| Status | "Ready" (eligible) or "Unpaid" (blocked) |
| Action | View payment history + Send email (if unpaid) |

#### Payment History
Click **"History"** button on any student to expand and see:
- All payment dates and amounts
- Payment status (successful/pending/failed)
- Transaction reference numbers

### STEP 4: Optional - Send Payment Reminders

For students with outstanding fees:

1. Click **"Email"** button on their row to send individual reminder
2. Or click **"Send Reminders to All Unpaid Students"** to bulk send
3. Customize the payment reminder message if desired
4. System sends email with:
   - Student name
   - Amount owed
   - Selected terms
   - Payment deadline

### STEP 5: Proceed to Generate Reports

After reviewing fees and (optionally) sending reminders:

1. Click **"Proceed to Generate Reports"** button
2. System generates reports for:
   - ✅ ALL students with **ZERO outstanding** for selected term(s)
   - ❌ SKIPS students with **outstanding fees**

#### Generation Results

You'll see a message like:
```
Generated 85 report card(s) across 1 term(s).
Skipped 15 duplicate(s).
Skipped 6 student(s) with outstanding fees.
```

This means:
- 85 students had reports generated
- 15 were already generated (not duplicated)
- 6 were blocked due to unpaid fees

---

## Key Features of the New System

### 1. Payment History Visibility

Each student's payment history shows:
- **Date:** When payment was made
- **Amount:** How much paid (RWF)
- **Status:** ✓ Successful, ⏳ Pending, ✗ Failed
- **Transaction Ref:** Bank/Payment system reference

**Example:**
```
Date: 03 Apr 2026 14:30
Amount: RWF 50,000
Status: ✓ Successful (payment completed)
Tx Ref: FLWV3S9239SB1585157359449
```

### 2. Term-Specific Checking

The fees shown are **ONLY for selected terms**, not entire year:

**Example Scenario:**
- Student paid RWF 80,000 in Term 1 (fully paid)
- Student paid RWF 30,000 in Term 2 (owes RWF 50,000)
- Generating reports for Term 1 → **ALLOWED** ✓ (fully paid for Term 1)
- Generating reports for Term 2 → **BLOCKED** ✗ (owes RWF 50,000 for Term 2)
- Generating reports for Term 1 + Term 2 → **BLOCKED** ✗ (owes for Term 2)

### 3. Financial Summary at Top

Quick overview without scrolling:

| Metric | Shows |
|--------|-------|
| Total Required | Sum of all course fees for selected term(s) |
| Total Paid | Sum of successful payments for selected term(s) |
| Total Outstanding | Total RWF not yet paid |

**Example:**
```
Total Required Fees:    RWF 1,600,000
  (100 students × RWF 80,000 per term × 2 terms)

Total Paid:             RWF 1,450,000
  (Some students paid partially)

Total Outstanding:      RWF 150,000
  (Still to be collected)
```

### 4. Payment Progress Indicator

Each student has a **progress bar** showing:
- Green fill = percentage paid
- Visual indication of payment progress
- Percentage shown below (e.g., "75%")

**Examples:**
```
100% paid ████████████ 100% → Status: "Ready" ✓
75% paid  █████████░░░░ 75%  → Status: "Unpaid" ⚠
50% paid  ██████░░░░░░░ 50% → Status: "Unpaid" ⚠
0% paid   ░░░░░░░░░░░░░ 0%   → Status: "Unpaid" ⚠
```

---

## Important Rules

### Rule 1: Term Selection Matters
- Only fees for **selected terms** are checked
- Only payments **for those terms** are counted
- Payments in other terms don't reduce the outstanding

### Rule 2: Successful Payments Only
- **Counted:** Payments with status = "successful"
- **NOT Counted:** Pending or failed payments
- This ensures only confirmed payments reduce outstanding

### Rule 3: All-or-Nothing Per Term
- If generating reports for Term 1 + Term 2:
  - Student must be fully paid for BOTH terms
  - If owing anything in either term, **BLOCKED**

### Rule 4: Annual Scope
- Fees checked from course_term_fees table
- For the selected academic year only
- Multiple terms in that year support multi-term reports

---

## Common Scenarios

### Scenario 1: Student Paid Partially for Term 1

**Data:**
- Term 1 fees: RWF 80,000
- Student paid: RWF 50,000
- Outstanding: RWF 30,000

**Admin Action:** Check fees for Term 1

**Result:**
- Student listed as "Unpaid"
- Option to send email reminder
- Report will NOT be generated

**To Fix:**
- Student pays remaining RWF 30,000
- Admin checks fees again
- Now eligible for report generation

### Scenario 2: Student Paid for Term 1, Owes for Term 2

**Data:**
- Term 1: Paid RWF 80,000 (✓ fully paid)
- Term 2: Paid RWF 20,000, Owes RWF 60,000

**Admin Action:** Check fees for **Term 1 only**

**Result:**
- Student shows as "Ready" ✓
- Report generated successfully

**Admin Action:** Check fees for **Term 1 + Term 2**

**Result:**
- Student shows as "Unpaid" ⚠
- Report NOT generated (owes Term 2)

### Scenario 3: 100 Students, 15 Unpaid

**Admin Action:** Check fees for class

**School Fees Page Shows:**
- Total students: 100
- Unpaid: 15
- Can send reminders to all 15 at once
- After sending emails, proceed to generate

**Generation Result:**
- 85 reports generated (paid students)
- 15 skipped (unpaid students)

---

## Troubleshooting

### Issue 1: "No students showing with outstanding fees"

**Possible Causes:**
1. StudentTransaction records don't have term_id filled
2. All students paid for selected terms
3. Wrong term selected

**Solution:**
- Check database: `SELECT * FROM student_transactions WHERE type = 'school_fee' AND term_id IS NULL;`
- Verify payment records have term_id populated
- Select different term and try again

### Issue 2: "Payment showing but don't affect calculation"

**Possible Causes:**
1. Payment status is not "successful" (maybe pending)
2. Payment term_id doesn't match form selection
3. Payment is old from different academic year

**Solution:**
- Check payment status in table
- Verify payment term matches selected terms
- Look at payment date vs academic year

### Issue 3: "Student appears paid but report won't generate"

**Possible Causes:**
1. Multiple terms selected and student owes in one
2. Payment records don't have term_id
3. Outstanding fees from a different term

**Solution:**
- Check fee page - see which terms student owes for
- Only generate for terms they've paid
- Send email reminder for outstanding term

---

## Benefits of Two-Step Process

✅ **Admin Visibility:** See exactly which students can/can't have reports  
✅ **Transparency:** Shows payment history and amounts owed  
✅ **Communication:** Send payment reminders before bulk generation  
✅ **Efficiency:** Prevent wasted report generation time  
✅ **Accountability:** Clear tracking of who was skipped and why  
✅ **Flexibility:** Option to handle unpaid students separately  

---

## Step-by-Step Example

Let's generate Term 1 reports for Form 3 class (Accounting course):

### Step 1: Form Selection
```
Student:         [Leave blank]
Course:          [Select "Accounting"]
Academic Year:   [Select "2025-2026"]
Terms:           [Select "Term 1"]
```

### Step 2: Click Button
Click: **"Step 1: Check Class Fees Before Generating"**

### Step 3: Review Fees Page
```
COURSE:          Accounting
YEAR:            2025-2026
TERMS:           Term 1 (Jan 15 - Mar 31, 2026)
TOTAL STUDENTS:  100
UNPAID STUDENTS: 16

SUMMARY:
- Total Required for Term 1:    RWF 8,000,000 (100 × 80,000)
- Total Paid for Term 1:         RWF 7,280,000 (16 students haven't paid all)
- Total Outstanding for Term 1:  RWF 720,000

STUDENT LIST:
┌─────────────────────────────────────────────────────────────┐
│ # │ ID     │ Name            │ Required │ Paid  │ Outstanding │
├─────────────────────────────────────────────────────────────┤
│ 1 │ S001   │ John Doe        │ 80,000   │ 80,000│ -           │ ✓ Ready
│ 2 │ S002   │ Jane Smith      │ 80,000   │ 50,000│ 30,000      │ ⚠ Unpaid
│ 3 │ S003   │ Bob Johnson     │ 80,000   │ 40,000│ 40,000      │ ⚠ Unpaid
│...│        │ ...             │ ...      │ ...   │ ...         │ ...
└─────────────────────────────────────────────────────────────┘
```

### Step 4: Send Reminders (Optional)
Click: **"Send Reminders to All Unpaid Students"** (to send 16 payment reminder emails)

### Step 5: Generate Reports
Click: **"Proceed to Generate Reports"**

### Step 6: Results
```
✓ Generated 84 report card(s) across 1 term(s).
✓ Skipped 16 student(s) with outstanding fees.

(Note: 100 total - 84 generated = 16 with unpaid fees)
```

---

## Database Requirements

This feature requires:
- `StudentTransaction` table with `term_id` column
- `course_term_fees` table with course and term fees
- `Term` table with academic year reference
- All payment records properly linked to terms

**Verify with:**
```sql
-- Check StudentTransaction has term_id
SELECT * FROM student_transactions LIMIT 5;

-- Check course_term_fees exists
SELECT * FROM course_term_fees LIMIT 5;

-- Check Term relationships
SELECT t.id, t.name, ay.year FROM terms t
JOIN academic_years ay ON t.academic_year_id = ay.id;
```

---

## Summary

✅ **Old Flow:** Direct generation (risky, may miss unpaid fees)  
✅ **New Flow:** Check fees first → Send reminders → Generate reports (safe, transparent)

The system now ensures that:
1. Admins always see fees before generating reports
2. Payment history is visible for each student
3. Communication with students can happen proactively
4. Only eligible students get reports generated

All while maintaining the option for quick single-student report generation.
