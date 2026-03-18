-- SQL Script to make exam_id nullable in questions table
-- This will allow questions to exist in question banks without being tied to an exam

-- Step 1: Drop the foreign key constraint
-- Note: The constraint name might be different, check with SHOW CREATE TABLE questions first
ALTER TABLE questions DROP FOREIGN KEY questions_exam_id_foreign;

-- Step 2: Make the exam_id column nullable
ALTER TABLE questions MODIFY COLUMN exam_id INT NULL;

-- Step 3: Re-add the foreign key constraint with ON DELETE SET NULL
ALTER TABLE questions ADD CONSTRAINT questions_exam_id_foreign 
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE SET NULL;

-- Step 4: Verify the changes
DESCRIBE questions;
