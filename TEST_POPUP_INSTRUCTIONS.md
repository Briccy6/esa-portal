# How to Test the Outstanding Fees Popup

## What's New
A new "Check Outstanding Fees" button has been added to the Report Card generation form. When clicked, it shows a popup/modal listing all students with unpaid fees before generating report cards.

---

## Step-by-Step Testing

### 1. Navigate to the Form
```
Admin Dashboard 
  → Report Cards (menu on left)
  → "Generate New Report Card" button
```

### 2. Fill in Required Fields
Select:
- **Course:** Any course with students (e.g., "Form 1")
- **Academic Year:** Any year (e.g., "2025-2026")
- **Term(s):** Hold Ctrl and select one or more terms

### 3. Click "Check Outstanding Fees" Button
Look for the yellow button with text:
```
Check Outstanding Fees
```

### 4. Wait for Modal to Appear
You should see a popup with:
- Red header saying "Students with Outstanding Fees"
- Loading spinner briefly, then:
  - Either a table showing students with fees
  - Or a green message saying "No outstanding fees"

### 5. Review the Modal Content

#### If Students Have Outstanding Fees:
You'll see a table with columns:
- **Student Name** - Full name of student
- **Student ID** - Unique student ID
- **Outstanding** - Amount still owed (in RED badge)
- **Required** - Total fees required
- **Paid** - Amount they've already paid

Footer shows:
- How many students have outstanding fees
- Warning message
- "Proceed Anyway" button (if you want to generate anyway)

#### If No Outstanding Fees:
You'll see:
- Green success message saying "No students have outstanding fees"
- All students are eligible for report card generation
- "Proceed Anyway" button will be hidden

### 6. Close the Modal
Click the X button or "Close" button to dismiss the modal

---

## What Each Element Means

### Outstanding Amount (Red)
The amount the student still owes
- Shows in RWF currency format
- Only shown if > 0

### Example Modal Display:
```
⚠️ WARNING: 3 student(s) have outstanding fees in this course 
for the selected terms

┌─────────────────────────────────────────────────────┐
│ Student Name      │ ID   │ Outstanding │ Req │ Paid │
├─────────────────────────────────────────────────────┤
│ John Doe          │ S001 │ 50,000      │     │      │
│ Jane Smith        │ S002 │ 75,000      │     │      │
│ Bob Johnson       │ S003 │ 30,000      │     │      │
└─────────────────────────────────────────────────────┘

Note: These students cannot have report cards generated 
until they pay their outstanding fees.
```

---

## Common Scenarios

### Scenario 1: All Students Have Paid ✅
```
Action: Click "Check Outstanding Fees"
Result: Green modal appears
         "No students have outstanding fees in this course..."
Status: Ready to generate report cards
```

### Scenario 2: Some Students Owe Money ⚠️
```
Action: Click "Check Outstanding Fees"
Result: Red modal with table of 5 students
        Shows amounts owed
Status: You can:
        - Click close and send them payment reminders first
        - Click "Proceed Anyway" to continue for others
```

### Scenario 3: Error in Modal
```
Action: Click "Check Outstanding Fees"
Result: Error message appears
        "An error occurred while checking fees"
Status: Check:
        - All fields are filled in
        - Browser console (F12) for JavaScript errors
        - Server logs for PHP errors
```

---

## Buttons in the Modal

### Close Button (Top Right X)
- Closes the modal without taking action
- You can adjust selections and try again

### Close Button (Footer)
- Same as above - closes the modal
- Returns to the form

### Proceed Anyway Button (Footer)
- Only shows if there ARE students with outstanding fees
- Click to generate report cards despite unpaid students
- Student records will be rejected in the next step

---

## If the Button Doesn't Work

### Check #1: Fields Selected?
```
✓ Course must be selected
✓ Academic Year must be selected  
✓ At least one Term must be selected
```
If any are empty, you'll see an alert: "Please select Course and Academic Year first"

### Check #2: Browser Console
Press **F12** to open browser developer tools:
- Click **Console** tab
- Look for red error messages
- Report any errors to admin

### Check #3: Server Logs
```bash
tail -50 storage/logs/laravel.log
```
Look for errors containing "Outstanding Fees" or "check-fees"

### Check #4: Clear Cache
```bash
php artisan cache:clear
php artisan config:cache
```
Then refresh the page and try again

---

## Expected Behavior Flow

```
Admin opens Create Report Card form
         ↓
Admin selects Course, Year, Term(s)
         ↓
Admin clicks "Check Outstanding Fees" button
         ↓
Modal appears with loading spinner
         ↓
[2-3 seconds pass]
         ↓
Modal updates with results:
  → Shows table if students owe fees
  → Shows green message if all paid
         ↓
Admin can:
  - Close modal to send reminders first
  - Click "Proceed Anyway" to generate anyway
```

---

## Success Indicators

✅ **Everything working correctly when:**
1. Mo dal appears after clicking button (no page reload)
2. Table displays with student names and payment info
3. Currency values are formatted (e.g., "50,000" not "50000")
4. Modal can be closed with X button
5. "Proceed Anyway" button works to continue
6. No errors in browser console (F12)

---

## After Testing

Once you confirm the popup works:

### For Admin Users:
1. **Outstanding Fees Management Page**
   - Go to: Report Cards → "Outstanding Fees Management" link
   - Shows ALL students in system with outstanding fees
   - Can filter by course, branch, amount
   - Can send bulk email reminders

2. **Send Payment Reminders**
   - From the modal or Outstanding Fees page
   - Click email icon next to student name
   - Student receives payment reminder with link to pay

3. **Student Payment Experience**
   - Student navigates to their report card
   - If they have fees: Red "Pay Now with Flutterwave" button
   - After payment: Automatic fee clearing + report card access

---

## Quick Reference

| Item | Location |
|------|----------|
| Check Fees Button | Report Card Create Form |
| Outstanding Fees Management | Report Cards → Outstanding Fees |
| Student Payment View | Student Portal → Report Cards |
| Fee Payment Endpoint | POST /flutterwave/payment/initialize-reportcard-fees |
| Admin Guide | ADMIN_OUTSTANDING_FEES_GUIDE.md |

---

## Support

If the popup isn't appearing or not working:

**Check browser console (F12 → Console):**
```javascript
// Test form selector
document.getElementById('checkFeesBtn')  // Should show the button element

// Test route
'{{ route("admin.report-cards.check-fees-for-generation") }}'  // Should show URL
```

**Check form submission:**
- Open Network tab (F12 → Network)
- Click "Check Outstanding Fees" 
- Look for POST request to `/report-cards/check-fees-for-generation`
- Response should show status 200 + student list

---

**Date:** April 4, 2026  
**Status:** Ready for Testing  
**Feature:** Admin Outstanding Fees Popup  
