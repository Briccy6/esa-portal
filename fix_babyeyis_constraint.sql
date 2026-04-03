-- Fix the foreign key constraint issue on babyeyis table
-- This script removes the foreign key constraints that reference the non-existent users table

-- First, identify and drop the foreign key constraint on created_by
ALTER TABLE `babyeyis` DROP FOREIGN KEY `fk_babyeyis_created_by`;

-- Drop the foreign key on edited_by if it exists
ALTER TABLE `babyeyis` DROP FOREIGN KEY `fk_babyeyis_edited_by`;

-- Optional: If you want to restore constraints pointing to the correct table (staff or admins),
-- you would add them back after removing the columns or restructuring the table.
-- For now, leaving them without constraints allows both staff and admin IDs to be stored.

-- Verify the table structure after removing constraints
SHOW CREATE TABLE `babyeyis`;
