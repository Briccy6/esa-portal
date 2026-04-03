# Quick Reference: Two-Step Report Generation

## The Change

**Before:** Report Card → Select Course/Terms → Generate (might miss unpaid fees)

**Now:** Report Card → Select Course/Terms → Check Fees → Send Reminders (if needed) → Generate

---

## Quick Start

### For ONE Student's Report

1. Admin → Report Cards → Generate New
2. **Select the Student** name
3. Select Course, Year, Term(s)
4. Click **"Generate Report Card for Student"**
5. Done ✓

### For ENTIRE CLASS

1. Admin → Report Cards → Generate New
2. **Leave Student blank** 
3. Select Course (e.g., Form 1)
4. Select Year (e.g., 2025-2026)
5. Select Term(s) (e.g., Term 1)
6. Click **"Step 1: Check Class Fees Before Generating"** ← NEW BUTTON
7. ✅ See all students' fee status
   - Green ✓ = Ready to generate report
   - Red ⚠ = Outstanding fees (can't generate)
8. Optional: Click **"Email"** on unpaid students to send payment reminders
9. Optional: **"Send Reminders to All Unpaid Students"** for bulk sending
10. Click **"Proceed to Generate Reports"** ← Now generate
11. Done ✓

---

## What You'll See on Fees Page

```
┌─────────────────────────────────────────────────┐
│ CHECK FEES BEFORE GENERATING REPORTS            │
├─────────────────────────────────────────────────┤
│                                                 │
│ COURSE: Form 1          YEAR: 2025-2026        │
│ TOTAL STUDENTS: 50      UNPAID: 16             │
│                                                 │
│ ─────────────────────────────────────────────  │
│ Total Required:  RWF 4,000,000                 │
│ Total Paid:      RWF 3,560,000                 │
│ Outstanding:     RWF   440,000                 │
│ ─────────────────────────────────────────────  │
│                                                 │
│ STUDENT LIST:                                  │
│ ┌───────────────────────────────────────────┐  │
│ │ John Doe         80,000  80,000 ████ 100%│  │
│ │ Jane Smith       80,000  50,000 ██░░  62% │  │
│ │ Bob Johnson      80,000  40,000 █░░░  50%│  │
│ │ ...              ...     ...    ...   ... │  │
│ └───────────────────────────────────────────┘  │
│                                                 │
│ [Send Reminders] [Proceed to Generate]        │
└─────────────────────────────────────────────────┘
```

---

## Key Numbers Explained

| Column | Means |
|--------|-------|
| Required | Total fees for selected term(s) |
| Paid | Amount student paid for selected term(s) |
| Outstanding | Still owed for selected term(s) |
| Progress | % paid |
| Status | ✓ Ready / ⚠ Unpaid |

---

## Common Actions

### Send Payment Reminder to One Student

1. On fees page, find student with outstanding fees
2. Click **"Email"** button in their row
3. Customize message (optional)
4. Click **"Send Email"**
5. Email sent to student with payment details

### Send Reminders to ALL Unpaid Students

1. On fees page, click **"Send Reminders to All Unpaid Students"**
2. Confirm: "Send to 16 students?"
3. Emails sent to all unpaid students

### View Payment History

1. On fees page, click **"History"** button on any student
2. See all their payments:
   - Date paid
   - Amount
   - Status (successful/pending/failed)
   - Transaction reference

### Proceed to Generate

1. After reviewing fees, click **"Proceed to Generate Reports"**
2. System generates for all eligible students
3. Shows result:
   - "Generated 34 reports"
   - "Skipped 16 with outstanding fees"

---

## Important Rules

✅ **Only selected term fees are checked** - not entire year  
✅ **Only successful payments count** - pending/failed don't count  
✅ **Must be 0 outstanding** - even RWF 1 blocks generation  
✅ **Email is optional** - helps collect fees but not required  

---

## What Changed

| Before | Now |
|--------|-----|
| No fee visibility | See all students' fees before generating |
| Unknowingly generated reports for unpaid | Blocked if unpaid + optional email reminder |
| No payment history shown | Can see each payment made |
| Direct generation | Two-step: Check → Generate |
| Single course generation only | Can generate multiple terms at once |

---

## When to Use Each Option

### Check Class Fees Page When:
- Generating reports for entire class
- Want to see who hasn't paid
- Need to send payment reminders
- Want transparency before bulk action

### Direct Generation When:
- Generating report for specific student
- Student payment status already known
- No need for payment history view

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Student shows unpaid but I know they paid | Check if payment is marked "successful" in history |
| No students showing on fees page | Verify you selected Course, Year, and Terms |
| Can't click "Check Class Fees" button | Make sure Student field is blank (for bulk only) |
| Report still generated for unpaid student | Database issue - contact admin |

---

## Database Check (For IT)

To verify system is working correctly:

```sql
-- Should see payments with term_id
SELECT * FROM student_transactions 
WHERE type = 'school_fee' AND status = 'successful'
LIMIT 5;

-- Should see term fees
SELECT * FROM course_term_fees 
WHERE is_active = 1 
LIMIT 5;

-- Count students with outstanding for Term 1
SELECT COUNT(DISTINCT student_id) as unpaid_count
FROM students s
WHERE s.course_id = 5 AND s.academic_year_id = 4
  AND NOT EXISTS (
    SELECT 1 FROM student_transactions st
    WHERE st.student_id = s.id
    AND st.type = 'school_fee'
    AND st.status = 'successful'
    AND st.term_id = 1
    GROUP BY st.student_id
    HAVING SUM(st.amount) >= (
      SELECT SUM(total_fees)
      FROM course_term_fees ctf
      WHERE ctf.course_id = s.course_id
      AND ctf.term_id = 1
    )
  );
```

---

## Next Steps

1. ✅ Navigate to Report Cards section
2. ✅ Try generating for ONE student (unchanged)
3. ✅ Try checking class fees (new feature)
4. ✅ Review the fees page layout
5. ✅ Send a test payment reminder email
6. ✅ Generate bulk reports with the new flow
7. ✅ Monitor production for any issues

---

## Support

**Questions?** Check TWO_STEP_REPORT_GENERATION_GUIDE.md for detailed explanations

**Issues?** Contact IT with:
- Course name
- Academic year
- Term(s) selected
- Screenshot of issue
