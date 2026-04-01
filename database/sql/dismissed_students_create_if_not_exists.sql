-- dismissed_students — matches Laravel migrations:
--   2026_03_29_181452_create_dismissed_students_table
--   2026_04_02_120000_align_dismissed_students_table
--
-- Run on MySQL 5.7+ / MariaDB 10.2+ (phpMyAdmin, CLI, or host SQL console).
-- Requires existing tables: branches, courses, academic_years, admins

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `dismissed_students` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `sex` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `reg_number` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `payed_registration_fees` tinyint(1) NOT NULL DEFAULT 0,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `id_number` varchar(255) DEFAULT NULL,
  `identification_type` varchar(255) DEFAULT NULL,
  `id_photo` varchar(255) DEFAULT NULL,
  `passport_photo` varchar(255) DEFAULT NULL,
  `senior6_document` varchar(255) DEFAULT NULL,
  `senior3_document` varchar(255) DEFAULT NULL,
  `other_supporting_document` varchar(255) DEFAULT NULL,
  `nesa_registered` tinyint(1) NOT NULL DEFAULT 0,
  `nesa_sdms_code` varchar(255) DEFAULT NULL,
  `nesa_reg_number` varchar(255) DEFAULT NULL,
  `nesa_document` varchar(255) DEFAULT NULL,
  `nesa_registration_date` date DEFAULT NULL,
  `nesa_remarks` text DEFAULT NULL,
  `discount` decimal(5,2) DEFAULT NULL,
  `education_level` varchar(255) DEFAULT NULL,
  `dismissal_reason` text NOT NULL,
  `dismissal_notes` text DEFAULT NULL,
  `dismissed_by` bigint(20) UNSIGNED NOT NULL,
  `dismissal_date` date NOT NULL,
  `original_student_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dismissed_students_email_unique` (`email`),
  UNIQUE KEY `dismissed_students_reg_number_unique` (`reg_number`),
  UNIQUE KEY `dismissed_students_original_student_id_unique` (`original_student_id`),
  KEY `dismissed_students_dismissal_date_index` (`dismissal_date`),
  CONSTRAINT `dismissed_students_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `dismissed_students_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `dismissed_students_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  CONSTRAINT `dismissed_students_dismissed_by_foreign` FOREIGN KEY (`dismissed_by`) REFERENCES `admins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------------
-- B) Table exists but is missing `payed_registration_fees` (older installs).
--    Run this whole block once; it does nothing if the column already exists.
-- ---------------------------------------------------------------------------
SET @col_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dismissed_students'
    AND COLUMN_NAME = 'payed_registration_fees'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `dismissed_students` ADD COLUMN `payed_registration_fees` TINYINT(1) NOT NULL DEFAULT 0 AFTER `password`',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------------
-- C) Table exists with NOT NULL sex/phone (old schema); align with `students`.
--    Safe to run even if already nullable (may show a harmless notice).
-- ---------------------------------------------------------------------------
ALTER TABLE `dismissed_students` MODIFY `sex` VARCHAR(20) NULL;
ALTER TABLE `dismissed_students` MODIFY `phone` VARCHAR(255) NULL;

