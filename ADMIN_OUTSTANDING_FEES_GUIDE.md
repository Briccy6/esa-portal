# Admin Guide: Managing Outstanding Fees Before Report Card Generation

## Overview
This guide explains how to check for and manage students with outstanding fees before generating report cards in the system.

---

## Quick Actions

### 1. Check Outstanding Fees Before Generating Report Cards

**When:** Before generating bulk report cards for a class
**Where:** Report Cards → Generate New Report Card → "Check Outstanding Fees" button

**Steps:**
1. Go to **Admin Dashboard** → **Report Cards** → **Generate New Report Card**
2. Select:
   - Course
   - Academic Year
   - Term(s)
3. Click **"Check Outstanding Fees"** button
4. A modal will appear showing:
   - Count of students with outstanding fees
   - Table with their details:
     - Student Name
     - Student ID
     - Outstanding Amount (in red)
     - Required Fees
     - Amount Already Paid
5. You have options:
   - **Close**: Don't generate yet, collect fees first
   - **Proceed Anyway**: Generate for other students, they'll handle exceptions

---

### 2. View All Students with Outstanding Fees

**Where:** Report Cards → Outstanding Fees Management

**Features:**
- See all students across ALL courses with outstanding fees
- Filter by:
  - Course
  - Branch
  - Outstanding amount range
- Statistics dashboard showing:
  - Total students with outstanding fees
  - Total outstanding amount
  - Average outstanding per student
  - Total already paid
- Bulk email reminder to students

---

### 3. Send Payment Reminders to Students

**Where:** Report Cards → Outstanding Fees Management

**For Single Student:**
1. Find student in the list
2. Click action button
3. Select "Send Email"
4. Email sent with payment details

**For Multiple Students:**
1. Check students you want to remind
2. Click "Send Bulk Email"
3. Confirmation shows count sent
4. Students receive reminder with payment link

---

## Workflow Examples

### Example 1: Generate Report Cards for Form 6
**Scenario:** You want to generate report cards for all Form 6 students

**Process:**
1. Go to Generate New Report Card
2. Select:
   - Form 6 (course)
   - 2026 (academic year)
   - Term 1 (term)
3. Click "Check Outstanding Fees"
4. System shows 5 students owe fees:
   - Student A: RWF 50,000
   - Student B: RWF 75,000
   - etc.

**Decision Point:**
- **Option 1 (Recommended):** Close modal, send them payment reminders, wait
- **Option 2:** Click "Proceed Anyway" to generate for others who paid

---

### Example 2: Daily Fee Collection Review

**Process:**
1. Go to Report Cards → Outstanding Fees Management
2. System automatically loaded with all students with outstanding fees
3. Review statistics:
   - Total students needing payment: 23
   - Total outstanding: RWF 1,200,000
   - Average per student: RWF 52,173
4. Export list or send bulk reminder emails

---

### Example 3: Before End-of-Term Report Card Release

**Process:**
1. Two weeks before report card release date:
   - Go to Outstanding Fees Management
   - Check for students with fees
   - Send bulk email reminders
2. One week before:
   - Check again
   - Release for paid students only
   - Mark unpaid students for follow-up
3. On release date:
   - Students ready to pay extra fees see payment button
   - Others see countdown timer or payment prompt

---

## Understanding the Modal

### When Modal Shows "No Outstanding Fees"
✅ **Good:** All students in this course/year/term have paid
- You can safely generate report cards
- All students are eligible

### When Modal Shows Students with Fees
⚠️ **Attention:** Some students need to pay
- The table shows WHO and HOW MUCH
- You can:
  - Send them reminders (button in Outstanding Fees view)
  - Wait for payment
  - Proceed without them (use "Proceed Anyway")

---

## Important Features

### Fee Payment Status Indicators

| Indicator | Meaning |
|-----------|---------|
| 🔴 RWF 50,000 (Red) | Outstanding amount due |
| 🟢 RWF 100,000 (Green) | Amount already paid |
| 🟦 RWF 150,000 | Total required fees |

### Payment Options for Students

When you send a reminder email, students can:
- Pay via Flutterwave (card, mobile money, bank transfer, USSD)
- Automatic fee update after successful payment
- Immediate report card access once paid

---

## Troubleshooting

### "Check Outstanding Fees" Button Not Working

**Check:**
1. ✓ Course is selected
2. ✓ Academic Year is selected
3. ✓ At least one Term is selected
4. ✓ Browser JavaScript is enabled (F12 → Console check for errors)

**Fix:**
- Clear browser cache: Ctrl+Shift+Delete
- Refresh page: Ctrl+R
- Try different browser

### Modal Appears Empty with Loading Spinner

**Cause:** Server is processing
**Fix:** Wait a few seconds, spinner should update

If it hangs:
- Check browser console (F12) for errors
- Check server logs: `tail -f storage/logs/laravel.log`
- Contact system admin

### Student Count Doesn't Match

**Possible Reasons:**
1. Filter applied (check Course, Branch, Amount filters)
2. Some students already paid (refresh page to see updated)
3. Student not in selected course/year/term

**Fix:**
1. Clear all filters
2. Refresh page (F5)
3. Check Academic Year is correct

---

## Best Practices

✅ **Do:**
- Check outstanding fees BEFORE generating bulk report cards
- Send reminders 2-3 days before deadlines
- Review statistics regularly
- Document payment receipts

❌ **Don't:**
- Generate report cards for students with outstanding fees
- Wait until last minute to collect fees
- Ignore the modal warning
- Delete payment records

---

## Quick Keys

| Action | Shortcut |
|--------|----------|
| Generate Report Card | Admin Menu → Report Cards → Generate New |
| View All Outstanding | Admin Menu → Report Cards → Outstanding Fees |
| Send Reminder | Outstanding Fees page → Student row → Send Email |
| Check Fees | Report Card form → "Check Outstanding Fees" button |

---

## Integration with Student Portal

Students with outstanding fees will see a **Red Payment Button** at:
- Report Card Access page
- When they try to download their report card
- Shows exact amount due

Once they pay via Flutterwave:
- Automatic fee clearing
- Immediate access to report card
- Email confirmation sent

---

## FAQ

**Q: What if a student disputes the fee amount?**
A: Review the `school_fees` table for that student and academic year. Adjust if calculation error found, then notify student.

**Q: Can fees be partially paid?**
A: Yes, system calculates `outstanding = required - paid`. Students can make multiple payments.

**Q: How long does payment processing take?**
A: Immediate via Flutterwave. Within 1 hour if webhook is enabled.

**Q: Can I export the outstanding fees list?**
A: Yes, use browser's print function (Ctrl+P) on Outstanding Fees page, save as PDF.

**Q: Student paid but still shows outstanding?**
A: Database sync issue. Click refresh icon or contact admin to force sync.

---

## Support

For issues or questions:
1. Check browser console (F12) for error messages
2. Review `storage/logs/laravel.log` on server
3. Verify `.env` configuration for Flutterwave keys
4. Contact system administrator

---

**Last Updated:** April 2026
**Status:** Live in Production
**Version:** 1.0
