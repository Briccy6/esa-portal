# Babyeyi Automation System - User Guide

## Overview

This system automatically generates and publishes **Babyeyi** (term registration documents) for all courses. Each document includes:
- Complete fee schedule table (specific to course and term)
- Student registration requirements
- Payment instructions and deadlines
- Class entry conditions
- Professional school branding

---

## Quick Start for Admins

### Method 1: Web Interface (Recommended)

1. **Log in** as Admin or Staff
2. Navigate to **Babyeyi Documents** section
3. Click **"Bulk Generate"** button (purple)
4. **Select:**
   - Academic Year
   - Term (automatically populated)
   - ☑️ Check "Auto-Publish" to make live immediately
5. Click **"Generate Babyeyis"**

✅ Documents are created and published!

### Method 2: Command Line

```bash
# Generate only (kept in draft)
php artisan babyeyi:generate --term-id=1

# Generate AND publish immediately
php artisan babyeyi:generate --term-id=1 --publish
```

**Options:**
- `--term-id=` : Required. The term to generate for
- `--academic-year-id=` : Optional. Defaults to term's academic year
- `--publish` : Auto-publish generated documents
- `--create-by=` : Staff ID who created them (defaults to 1)
- `--force` : Force regeneration even if exists

---

## What Gets Created

### One Babyeyi Per Course
- Each course with **active term fees** gets one document
- Automatically skips courses without fees configured
- Includes course name, type, and academic year

### Content Includes
1. **Fee Schedule Table**
   - Registration Fee
   - Tuition Fee
   - Practical Fee
   - Trip/Excursion Fee
   - Student Card Fee
   - **Total Amount Due** (highlighted)

2. **Important Sections**
   - Term dates and duration
   - Conditions to enter class
   - Payment information and deadlines
   - Late enrollment penalties

3. **Professional Elements**
   - School logo and header
   - Reference number
   - Signature blocks for acknowledgment

---

## Student View

### Where Students Access Documents

1. **Student Portal** → **My Babyeyi Documents**
2. See all published documents for their course
3. **View** document in browser
4. **Download** as PDF

### What Students See

- Their specific course's fee table
- Not generic - shows THEIR fees only
- Based on their branch and course assignment
- Professional, printable format

---

## Managing Documents After Creation

### Publish Draft Documents
Only if you unchecked "Auto-Publish" during generation:

```bash
# Publish all drafts for a term
POST /babyeyi/bulk/publish
  term_id: 1
```

Or click "Publish" button on each document individually.

### Update Fees (After term started)

**If you change CourseTermFees after documents are created:**

```bash
# Regenerate all documents for a term with new fees
POST /babyeyi/bulk/regenerate
  term_id: 1
```

This rebuilds all documents with updated fees.
Students will see new amounts immediately.

### Delete Documents

- Only **draft** documents can be deleted
- Published docs cannot be deleted (archive instead)

---

## Important Workflows

### Workflow 1: New Term Setup
1. Create academic year
2. Create term with dates
3. Configure CourseTermFees for courses
4. **Bulk Generate** babyeyis
5. ✅ Students see registration requirements

### Workflow 2: Emergency Fee Update
1. Update CourseTermFees
2. **Regenerate** babyeyis for that term
3. ✅ Updates published to students immediately

### Workflow 3: Late Course Addition
1. New course added for term
2. Configure its CourseTermFees
3. **Quickly Generate** just that course
4. ✅ Available immediately

---

## Troubleshooting

### "No babyeyis created"
- **Cause**: No courses have CourseTermFees for this term
- **Fix**: Set up fees in **Course Term Fees** first

### "Documents not showing to students"
- **Cause**: Status is "draft", not "published"
- **Fix**: Use Bulk Publish or check Auto-Publish checkbox

### "Students see old fees"
- **Cause**: Babyeyis not regenerated after fee change
- **Fix**: Use **Bulk Regenerate** from Babyeyi Operations

### "Same babyeyi created twice"
- **Cause**: Course-term combination already exists
- **Fix**: Delete the draft and regenerate, or use --force flag

---

## Best Practices

✅ **DO:**
- Set up all **CourseTermFees** BEFORE bulk generating
- Use **Auto-Publish** for immediate student access
- **Regenerate** if fees change mid-term
- Review documents before publishing if needed
- Keep consistent pricing across branches

❌ **DON'T:**
- Generate before fees are configured
- Delete published documents (archive instead)
- Manually edit HTML in "Edit" after bulk generation
- Create multiple babyeyis for same course-term combo

---

## Command Reference

### Artisan Commands

```bash
# Show all available options
php artisan babyeyi:generate --help

# Minimal usage (creates draft)
php artisan babyeyi:generate --term-id=1

# Full production use
php artisan babyeyi:generate \
  --term-id=1 \
  --academic-year-id=2 \
  --publish \
  --create-by=3

# With force regeneration
php artisan babyeyi:generate --term-id=1 --publish --force
```

### API Routes

| Method | Route | Purpose |
|--------|-------|---------|
| GET | `/babyeyi/` | List all babyeyis |
| GET | `/babyeyi/bulk/create` | Show bulk generation form |
| POST | `/babyeyi/bulk/store` | Process bulk generation |
| POST | `/babyeyi/bulk/publish` | Publish drafts |
| POST | `/babyeyi/bulk/regenerate` | Update with new content |
| POST | `/babyeyi/quick-generate/{termId}` | One-click generate & publish |

---

## Files Created/Modified

### New Files
- `app/Services/BabyeyiService.php` - Core generation logic
- `app/Console/Commands/GenerateBabyeyis.php` - Artisan command
- `resources/views/admin/babyeyi/bulk-create.blade.php` - Admin UI
- `resources/views/staff/babyeyi/bulk-create.blade.php` - Staff UI

### Modified Files
- `app/Http/Controllers/BabyeyiController.php` - Added bulk methods
- `routes/web.php` - Added bulk routes
- `resources/views/admin/babyeyi/index.blade.php` - Added button
- `resources/views/staff/babyeyi/index.blade.php` - Added button

---

## Support

For issues or questions about the babyeyi system:
1. Check **Troubleshooting** section above
2. Review **Workflow** for your scenario
3. Check **CourseTermFees** configuration
4. Verify student course assignments

---

## Version History

- **v1.0** (Current) - Automated bulk generation with fee tables
  - Generates for all courses with active fees
  - Includes complete fee schedule
  - Auto-publish option
  - Regeneration support
  - Artisan command support

