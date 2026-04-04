-- Fix exam subject field to be nullable
-- Applied: April 4, 2026

ALTER TABLE `exams` MODIFY COLUMN `subject` VARCHAR(255) NULL DEFAULT '';

-- Verify the change
SHOW COLUMNS FROM `exams` WHERE `Field` = 'subject';
