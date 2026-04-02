-- ========================================
-- TO WHOM LETTER SYSTEM SQL COMMANDS
-- ========================================

-- 1. Create the to_whom_letters table
CREATE TABLE `to_whom_letters` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `student_id` bigint(20) unsigned NOT NULL,
    `generated_by` bigint(20) unsigned DEFAULT NULL,
    `letter_type` varchar(50) NOT NULL,
    `recipient_title` varchar(100) NOT NULL,
    `purpose` varchar(255) NOT NULL,
    `content` text DEFAULT NULL,
    `letter_date` date NOT NULL,
    `reference_number` varchar(50) NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `to_whom_letters_reference_number_unique` (`reference_number`),
    KEY `to_whom_letters_student_id_letter_type_index` (`student_id`, `letter_type`),
    KEY `to_whom_letters_reference_number_index` (`reference_number`),
    CONSTRAINT `to_whom_letters_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `to_whom_letters_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Ensure users table has role column (if not exists)
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `role` varchar(50) DEFAULT 'user' AFTER `email`;

-- 3. Ensure students table has required columns (if not exists)
ALTER TABLE `students` 
ADD COLUMN IF NOT EXISTS `id_number` varchar(50) DEFAULT NULL AFTER `reg_number`,
ADD COLUMN IF NOT EXISTS `identification_type` varchar(50) DEFAULT NULL AFTER `id_number`;

-- 4. Ensure school_informations table exists (if not exists)
CREATE TABLE IF NOT EXISTS `school_informations` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `school_name` varchar(255) NOT NULL,
    `address` text NOT NULL,
    `phone` varchar(50) NOT NULL,
    `email` varchar(255) NOT NULL,
    `website` varchar(255) DEFAULT NULL,
    `logo_path` varchar(255) DEFAULT NULL,
    `logo` longblob DEFAULT NULL,
    `motto` text DEFAULT NULL,
    `vision` text DEFAULT NULL,
    `mission` text DEFAULT NULL,
    `principal_name` varchar(255) DEFAULT NULL,
    `principal_signature` longblob DEFAULT NULL,
    `principal_signature_data` longblob DEFAULT NULL,
    `stamp_signature` longblob DEFAULT NULL,
    `stamp_signature_data` longblob DEFAULT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Insert default school information (if table is empty)
INSERT IGNORE INTO `school_informations` 
(`school_name`, `address`, `phone`, `email`, `website`, `motto`, `vision`, `mission`, `principal_name`, `is_active`) 
VALUES 
('ESA Learning Portal', '123 Education Street, Learning City, Knowledge State', '+250-788-123-456', 'info@esa-learning.rw', 'https://esa-learning.rw', 'Excellence in Education Through Technology', 'To be a leading educational institution that empowers students with quality education and modern skills for the future.', 'To provide comprehensive, technology-driven education that prepares students for academic excellence and professional success.', 'Dr. John Smith', 1);

-- 6. Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_to_whom_letters_active` ON `to_whom_letters` (`is_active`);
CREATE INDEX IF NOT EXISTS `idx_to_whom_letters_date` ON `to_whom_letters` (`letter_date`);
CREATE INDEX IF NOT EXISTS `idx_to_whom_letters_type` ON `to_whom_letters` (`letter_type`);

-- 7. Verify table structure
DESCRIBE `to_whom_letters`;
DESCRIBE `school_informations`;

-- 8. Check if foreign key constraints exist
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM 
    information_schema.KEY_COLUMN_USAGE 
WHERE 
    TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'to_whom_letters' 
    AND REFERENCED_TABLE_NAME IS NOT NULL;

-- ========================================
-- SAMPLE DATA (Optional - for testing)
-- ========================================

-- Insert sample student data if needed for testing
-- (This assumes you already have students in your system)

-- Sample letter generation (for testing)
-- INSERT INTO `to_whom_letters` 
-- (`student_id`, `generated_by`, `letter_type`, `recipient_title`, `purpose`, `content`, `letter_date`, `reference_number`, `is_active`) 
-- VALUES 
-- (1, 1, 'enrollment', 'To Whom It May Concern', 'Employment verification', 'Sample letter content...', CURDATE(), 'ESA-LET-2026-0001', 1);

-- ========================================
-- VERIFICATION QUERIES
-- ========================================

-- Check if tables exist
SHOW TABLES LIKE 'to_whom_letters';
SHOW TABLES LIKE 'school_informations';

-- Count records
SELECT COUNT(*) as total_letters FROM `to_whom_letters`;
SELECT COUNT(*) as total_school_info FROM `school_informations`;

-- Sample query to test letter generation
SELECT 
    tl.*,
    s.full_name,
    s.reg_number,
    c.name as course_name,
    b.name as branch_name
FROM `to_whom_letters` tl
LEFT JOIN `students` s ON tl.student_id = s.id
LEFT JOIN `courses` c ON s.course_id = c.id
LEFT JOIN `branches` b ON s.branch_id = b.id
WHERE tl.is_active = 1
ORDER BY tl.created_at DESC;
