# Public Fees Page - Debugging Guide

When you encounter errors on the public fees page, here's how to diagnose and fix them without needing direct server logs.

## How to Access Error Details

### In Browser Console (F12)
1. **Press F12** to open Developer Tools
2. Go to **Console** tab
3. Try selecting a branch on the fees page
4. Watch the console for error messages with this format:

```
❌ API Error Details: {
  status: 500,
  statusText: "Internal Server Error",
  data: {
    success: false,
    message: "...",
    debug: {
      totalBranches: X,
      totalCourses: Y,
      ...
    }
  }
}
```

### In Network Tab
1. Press **F12** → **Network** tab
2. Select a branch on fees page
3. Look for request to `/fees/branches/X/courses` (red = error)
4. Click it and view **Response** tab - shows JSON with error details

---

## Common Errors & Solutions

### Error 1: "No courses available for this branch"

**Cause**: `branch_course` pivot table is empty or courses aren't linked to the branch.

**What to check**:
- Console shows: `totalPivotRecords: 0`
- Means no courses are linked to any branch

**How to fix**:
1. Admin logs into system
2. Go to **Courses** section
3. Edit each course and assign it to a branch
4. The `branch_course` table gets populated automatically

**Database check** (via phpmyadmin):
```sql
SELECT * FROM branch_course WHERE branch_id = 4;
```
Should return at least 1 row. If empty = no courses assigned to branch 4.

---

### Error 2: "Branch with ID X not found"

**Cause**: The branch ID doesn't exist in database.

**What to check**:
- Console shows: `totalBranches: 0` or branch ID not in list
- URL shows: `/fees/branches/4/courses` but branch 4 doesn't exist

**How to fix**:
1. Visit `/fees/debug` page (added debug endpoint)
2. Shows all branches with their IDs
3. Use existing branch ID numbers from the dropdown

**Database check**:
```sql
SELECT id, name FROM branches LIMIT 10;
```

---

### Error 3: "Server Error 500" with error trace

**Cause**: PHP exception occurred - check details in console.

**Console will show**:
```
🔴 Server Error 500
Error Type: [exception class name]
Error Message: [specific error]
Error Location: file.php:lineNumber
Stack Trace: [full trace]
```

**How to fix** - depends on error type:

**If "ModelNotFoundException"**:
- Course or Term model not found
- Check if IDs exist in database

**If "SQLSTATE error"**:
- Database connection issue
- Check database credentials in `.env`

**If "Call to undefined method"**:
- Missing method on model (relationship not defined)
- Check model has the relationship defined

---

### Error 4: HTTP 404 "Not Found"

**Cause**: The API endpoint doesn't exist.

**What to check**:
- URL bar shows: `/fees/branches/4/courses` returns 404
- Routes may not be loaded

**How to fix**:
```bash
# Clear route cache
php artisan route:clear

# Verify routes exist
php artisan route:list | grep fees
```

---

### Error 5: Empty courses list (0 courses)

**Cause**: Either:
- No courses exist in database (`totalCourses: 0`)
- Courses not linked to this branch (`totalPivotRecords: 0`)
- Courses exist but filter is wrong

**Console will show**:
```
📊 Database Stats: {
  totalBranches: 5,
  totalCourses: 0,        // ← Problem: No courses at all
  linkedPairs: 0
}
```

**How to fix**:
1. Admin creates some test courses
2. Assigns them to branches
3. Then try fees page again

**Database queries to verify**:
```sql
-- Check total courses
SELECT COUNT(*) FROM courses;

-- Check courses for branch 4
SELECT c.* FROM courses c
JOIN branch_course bc ON c.id = bc.course_id
WHERE bc.branch_id = 4;

-- Check when courses were created
SELECT id, course_name, created_at FROM courses ORDER BY created_at DESC LIMIT 5;
```

---

## Debug Endpoint: `/fees/debug`

Visit this URL to see:
- All branches and their IDs
- All courses and their IDs  
- Branch-course link count
- Query test results for each method

Shows which query method works:
- ✅ Method 1 (whereHas) = 5 courses
- ✅ Method 2 (direct join) = 5 courses
- ⚠️ Method 3 (direct FK) = 0 courses

Green = working method, we use it
Red = doesn't work for your schema

---

## How to Read Debug Output in Console

```javascript
// Good response - fees loaded
✅ Loaded 5 courses for branch "Kigali (Down Town, Ndama House 2nd Floor)"

// Bad response - no fees for course
❌ Server Error 500
Error Type: ModelNotFoundException
Error Message: No query results found for model [App\Models\Term]
Error Location: app/Models/CourseTermFee.php:45
Debug Info: {
  totalFeesRecords: 2,      // Total records
  activeFeesRecords: 0      // Active = 0 (Problem!)
}
```

**Key indicators**:
- ✅ `✅ Loaded` = Success
- ❌ `❌ API Error` = Failure, check `debug` object
- 📊 `Database Stats` = Current state of database

---

## Step-by-Step: Fixing "No Courses" Issue

1. **Visit browser console** (F12)
2. **Look at debug stats**:
   - `totalBranches` = 0? → Create branches in admin
   - `totalCourses` = 0? → Create courses in admin
   - `totalPivotRecords` = 0? → Assign courses to branches
3. **Verify relationship**:
   - Edit a course → Select branch → Save
   - Check `/fees/debug` page again
   - `totalPivotRecords` should increase
4. **Test again** - select branch on fees page
5. **Check console** - should see ✅ success message

---

## Still Can't Find Issue?

1. **Gather information**:
   - Screenshot of console error
   - Output from `/fees/debug` page
   - Total branches/courses/pivot records

2. **Check database directly**:
   ```sql
   SELECT 
     (SELECT COUNT(*) FROM branches) as total_branches,
     (SELECT COUNT(*) FROM courses) as total_courses,
     (SELECT COUNT(*) FROM branch_course) as linked_pairs,
     (SELECT COUNT(*) FROM course_term_fees) as fee_records;
   ```

3. **Verify relationships**:
   ```sql
   -- Are courses assigned to branches?
   SELECT DISTINCT branch_id FROM branch_course;
   
   -- Do fees exist for courses?
   SELECT DISTINCT course_id FROM course_term_fees;
   ```

4. **Clear all caches**:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

---

## Summary

| Error | Check First | Most Likely Cause |
|-------|------------|-------------------|
| 500 Server Error | Console → debug section | Exception in code, check error type |
| 404 Not Found | `/fees/debug` loads? | Route not found, clear cache |
| No courses list | Database Stats | No pivot records or no courses exist |
| Empty branch list | Check branches table | No branches in database |
| Fees won't load | Check course ID exists | Course has no fee records or is inactive |

**Always check browser console first** - it now has detailed debug info that explains exactly what's wrong! 🎯
