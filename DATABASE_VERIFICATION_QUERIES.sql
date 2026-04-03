# Database Verification Queries

Use these SQL queries to verify your data is set up correctly for babyeyis to display to students.

## 1. Check Published Babyeyis

```sql
-- Count all babyeyis by status
SELECT status, COUNT(*) as count FROM babyeyis GROUP BY status;

-- See all published babyeyis with details
SELECT 
    id, 
    course_id, 
    term_id, 
    reference_number, 
    status, 
    created_at,
    (SELECT course_name FROM courses WHERE id = babyeyis.course_id) as course_name,
    (SELECT name FROM terms WHERE id = babyeyis.term_id) as term_name
FROM babyeyis 
WHERE status = 'published'
ORDER BY created_at DESC;

-- Count published babyeyis per course
SELECT 
    course_id,
    (SELECT course_name FROM courses WHERE id = babyeyis.course_id) as course_name,
    COUNT(*) as published_count
FROM babyeyis 
WHERE status = 'published'
GROUP BY course_id;
```

## 2. Check Students with Course Assignments

```sql
-- Count students with courses
SELECT COUNT(*) as students_with_courses FROM students WHERE course_id IS NOT NULL;

-- List students with their courses and branches
SELECT 
    s.id,
    s.full_name,
    s.email,
    s.course_id,
    c.course_name,
    c.course_type,
    s.branch_id,
    b.name as branch_name
FROM students s
LEFT JOIN courses c ON s.course_id = c.id
LEFT JOIN branches b ON s.branch_id = b.id
WHERE s.course_id IS NOT NULL
LIMIT 20;

-- Find students in a specific course