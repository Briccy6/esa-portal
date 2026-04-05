# Teacher Marks Recording System - Quick Test Guide

## System Overview
The teacher marks recording system allows teachers to:
1. Select their assigned Branch
2. Select a Course within the branch
3. Select a Lesson in that course
4. Select or enter assessment details (test/exam)
5. Enter student marks
6. Save and view recorded marks

---

## Data Requirements

Before testing, verify the following data exists:

### 1. Teacher Account
- ✅ Teacher must be logged in
- ✅ Teacher must have at least one lesson assignment

### 2. Lesson-Branch-Course Assignment
The `lesson_branch_course` table MUST contain:
- `teacher_id` = Logged-in teacher's ID
- `branch_id` = A valid branch ID
- `course_id` = A valid course with students
- `lesson_id` = A valid lesson ID

Query to check:
```sql
SELECT * FROM lesson_branch_course 
WHERE teacher_id = {TEACHER_ID};
```

### 3. Student Enrollment
Students must be enrolled in the course:
```sql
SELECT * FROM students 
WHERE course_id = {COURSE_ID} 
AND branch_id = {BRANCH_ID};
```

### 4. Terms
At least one term must exist:
```sql
SELECT * FROM terms LIMIT 5;
```

---

## Step-by-Step Test

### Test Case 1: Load Branches
**Expected Result:** Dropdown shows all branches where teacher has lesson assignments

```
1. Go to /teacher/marks
2. Check console (F12 → Console)
3. Should see: "Branches loaded: X"
4. Branch dropdown should be populated
```

**If it fails:**
- Check server logs for "Error loading branches"
- Verify lesson_branch_course records exist
- Check teacher authentication

---

### Test Case 2: Load Courses
**Expected Result:** Selecting a branch populates the Course dropdown

```
1. Select a branch from dropdown
2. Check console for: "Courses loaded: X"
3. Course dropdown should populate (not disabled)
```

**If it fails:**
- Check console error message
- Verify courses are assigned to the branch
- Check lesson_branch_course table

---

### Test Case 3: Load Lessons
**Expected Result:** Selecting a course populates the Lesson dropdown

```
1. Select a course from dropdown
2. Check console for: "Lessons loaded: X"
3. Lesson dropdown should enable
4. Info text shows "X lesson(s) available"
```

**If it fails:**
- Check console error
- Verify lessons exist for this course
- Check lesson_branch_course table

---

### Test Case 4: Complete Form
**Expected Result:** "Show Students" button enables when all fields are filled

```
1. Select Branch ✓
2. Select Course ✓
3. Select Lesson ✓
4. Select Assessment Type (Test or Exam) ✓
5. Select Term ✓
6. For TEST: Enter marks_out_of and test_date ✓
7. For EXAM: Enable exam selection and date ✓
8. "Show Students" button should be ENABLED
```

**If button stays disabled:**
- Check that ALL fields are filled
- Verify values aren't empty strings
- Check browser console for validation logic

---

### Test Case 5: Load Students
**Expected Result:** Modal shows list of enrolled students

```
1. Click "Show Students" button
2. Modal should open
3. See list of students with input fields for marks
4. Check console: "Students populated: X"
```

**If modal is empty or shows error:**
- Check console for "Students API Response Status"
- Verify student enrollment: 
  ```sql
  SELECT COUNT(*) FROM students 
  WHERE course_id = X AND branch_id = Y;
  ```
- Check student registration records

---

### Test Case 6: Enter Marks
**Expected Result:** Marks validate and show percentage

```
1. Enter a mark in the input field
2. Percentage should calculate automatically
3. Color should change based on percentage:
   - Green (75%+)
   - Yellow (50-74%)
   - Red (0-49%)
4. Mark cannot exceed max value
```

**If validation fails:**
- Check browser console
- Try entering different values
- Check form validation logic

---

### Test Case 7: Save Marks
**Expected Result:** Marks saved successfully with confirmation

```
1. Fill all student marks
2. Click "Save Marks"
3. Should see loading state: "Saving..."
4. Should see success message with count
5. Modal should close after 2 seconds
```

**If save fails:**
- Check browser console for error message
- Check server logs (storage/logs/laravel.log)
- Look for entries like:
  - "Validation failed"
  - "Term not found"
  - "Mark creation failed"
  - "Error creating mark"

---

## Server Log Inspection

### Location
```bash
# Linux/Mac
tail -f storage/logs/laravel.log

# Windows
Get-Content storage/logs/laravel.log -Tail 50 -Wait
```

### Key Log Entries to Check

**Success Scenario:**
```
[2024-...] INFO: MarksController::store - Incoming Request Data...
[2024-...] INFO: Starting marks creation loop...
[2024-...] INFO: Mark created successfully...
[2024-...] INFO: Marks creation completed
```

**Error Scenario:**
```
[2024-...] ERROR: Validation failed
[2024-...] ERROR: Term not found
[2024-...] ERROR: Error creating mark
[2024-...] ERROR: Error fetching students
```

---

## Common Issues & Solutions

### Issue 1: "No branches available for you"
**Cause:** Teacher has no lesson assignments
**Fix:**
```sql
-- Add lesson assignment to teacher
INSERT INTO lesson_branch_course (lesson_id, branch_id, course_id, teacher_id)
VALUES (1, 1, 1, {TEACHER_ID});
```

### Issue 2: Course dropdown doesn't populate
**Cause:** No lesson_branch_course records for this branch
**Check:**
```sql
SELECT * FROM lesson_branch_course 
WHERE teacher_id = {TEACHER_ID} 
AND branch_id = {SELECTED_BRANCH_ID};
```

### Issue 3: Students not loading
**Cause:** No students in the course/branch combination
**Check:**
```sql
SELECT * FROM students 
WHERE course_id = {COURSE_ID} 
AND branch_id = {BRANCH_ID};
```

### Issue 4: "Validation failed" or "Term not found"
**Cause:** Invalid form data being submitted
**Check:**
- All dropdowns have values selected
- Term ID is valid in database
- Student marks are numeric values

### Issue 5: Mark saves but doesn't appear in "View Marks"
**Cause:** Marks not associated with correct term
**Check:**
- Verify term_id in marks table
- Check query in viewMarks() method

---

## Database Verification Queries

Run these to verify your data setup:

```sql
-- Check teacher lesson assignments
SELECT * FROM lesson_branch_course 
WHERE teacher_id = {YOUR_TEACHER_ID};

-- Check course enrollment
SELECT * FROM students 
WHERE course_id = {COURSE_ID};

-- Check terms
SELECT * FROM terms LIMIT 10;

-- Check marks (after saving)
SELECT * FROM marks 
WHERE lesson_id = {LESSON_ID} 
AND test_date = '2024-...'
ORDER BY student_id;

-- Count marks per lesson
SELECT lesson_id, COUNT(*) as mark_count 
FROM marks 
GROUP BY lesson_id
ORDER BY mark_count DESC;
```

---

## Browser Console Commands

Use these in browser console (F12) to test APIs directly:

```javascript
// Test branches API
fetch('/api/teacher/branches')
  .then(r => r.json())
  .then(d => console.log('Branches:', d));

// Test courses API
fetch('/api/teacher/branch/1/courses')
  .then(r => r.json())
  .then(d => console.log('Courses:', d));

// Test lessons API
fetch('/api/teacher/branch/1/course/1/lessons')
  .then(r => r.json())
  .then(d => console.log('Lessons:', d));

// Test students API
fetch('/api/teacher/branch/1/course/1/students')
  .then(r => r.json())
  .then(d => console.log('Students:', d));
```

---

## Performance Expectations

| Action | Expected Time |
|--------|---|
| Load branches | < 500ms |
| Load courses | < 500ms |
| Load lessons | < 500ms |
| Load students | < 1000ms |
| Save marks | < 2000ms |
| View recorded marks | < 1500ms |

If any action takes longer, check:
- Database indexes
- Server load
- Network latency

---

## Success Criteria

✅ All tests pass if:
- Dropdowns populate correctly
- Students load in modal
- Marks validate and calculate percentage
- Form submission shows success message
- Marks appear in "View Recorded Marks" page
- Server logs show "Mark created successfully" entry

❌ Tests fail if:
- Dropdowns show errors
- Students list is empty
- Form shows validation errors
- Server logs show exceptions
- Browser console shows fetch errors

---

## Debugging Checklist

- [ ] Browser console shows no JavaScript errors
- [ ] Network tab shows all API calls returning 200 status
- [ ] Server logs show expected info level log entries
- [ ] Teacher has lesson assignments (lesson_branch_course)
- [ ] Students are enrolled in the course
- [ ] Terms exist in the database
- [ ] Marks table has correct structure
- [ ] Teacher is properly authenticated
