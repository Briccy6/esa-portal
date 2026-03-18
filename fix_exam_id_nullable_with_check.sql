-- SQL Script to make exam_id nullable in questions table
-- This will allow questions to exist in question banks without being tied to an exam

-- Step 1: Check current foreign key constraint name
SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'questions' 
AND COLUMN_NAME = 'exam_id' 
AND REFERENCED_TABLE_NAME = 'exams';

-- Step 2: Drop the foreign key constraint (replace with actual constraint name from above)
-- Common names are: questions_exam_id_foreign, exam_id_foreign, etc.
-- ALTER TABLE questions DROP FOREIGN KEY [ACTUAL_CONSTRAINT_NAME];

-- Step 3: Make the exam_id column nullable
ALTER TABLE questions MODIFY COLUMN exam_id INT NULL;

-- Step 4: Re-add the foreign key constraint with ON DELETE SET NULL
ALTER TABLE questions ADD CONSTRAINT questions_exam_id_foreign 
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE SET NULL;

-- Step 5: Verify the changes
DESCRIBE questions;
