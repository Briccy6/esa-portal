<?php

echo "-- DATABASE SYNC SQL COMMANDS\n";
echo "-- These commands will sync your local database with the online database structure\n";
echo "-- Run these commands in your local MySQL database\n\n";

// Missing tables
echo "-- MISSING TABLES (need to be created):\n\n";

echo "-- 1. community_spaces table\n";
echo "CREATE TABLE `community_spaces` (\n";
echo "  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n";
echo "  `name` varchar(255) NOT NULL,\n";
echo "  `description` text DEFAULT NULL,\n";
echo "  `term_id` bigint(20) UNSIGNED NOT NULL,\n";
echo "  `min_fee_percentage` float NOT NULL DEFAULT 0,\n";
echo "  `start_date` date NOT NULL,\n";
echo "  `end_date` date NOT NULL,\n";
echo "  `created_at` timestamp NULL DEFAULT NULL,\n";
echo "  `updated_at` timestamp NULL DEFAULT NULL\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

echo "-- 2. community_space_course table\n";
echo "CREATE TABLE `community_space_course` (\n";
echo "  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n";
echo "  `community_space_id` bigint(20) UNSIGNED NOT NULL,\n";
echo "  `course_id` bigint(20) UNSIGNED NOT NULL,\n";
echo "  `created_at` timestamp NULL DEFAULT NULL,\n";
echo "  `updated_at` timestamp NULL DEFAULT NULL\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

echo "-- 3. community_space_messages table\n";
echo "CREATE TABLE `community_space_messages` (\n";
echo "  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n";
echo "  `community_space_id` bigint(20) UNSIGNED NOT NULL,\n";
echo "  `student_id` bigint(20) UNSIGNED DEFAULT NULL,\n";
echo "  `staff_id` bigint(20) UNSIGNED DEFAULT NULL,\n";
echo "  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,\n";
echo "  `message` text NOT NULL,\n";
echo "  `file_path` varchar(255) DEFAULT NULL,\n";
echo "  `file_type` varchar(100) DEFAULT NULL,\n";
echo "  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,\n";
echo "  `sender_type` varchar(100) DEFAULT NULL,\n";
echo "  `created_at` timestamp NULL DEFAULT NULL,\n";
echo "  `updated_at` timestamp NULL DEFAULT NULL\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

echo "-- 4. community_space_message_reads table\n";
echo "CREATE TABLE `community_space_message_reads` (\n";
echo "  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n";
echo "  `community_space_message_id` bigint(20) UNSIGNED NOT NULL,\n";
echo "  `user_id` bigint(20) UNSIGNED NOT NULL,\n";
echo "  `user_type` varchar(255) NOT NULL,\n";
echo "  `read_at` timestamp NOT NULL DEFAULT current_timestamp(),\n";
echo "  `created_at` timestamp NULL DEFAULT NULL,\n";
echo "  `updated_at` timestamp NULL DEFAULT NULL\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

echo "-- 5. community_space_student table\n";
echo "CREATE TABLE `community_space_student` (\n";
echo "  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n";
echo "  `community_space_id` bigint(20) UNSIGNED NOT NULL,\n";
echo "  `student_id` bigint(20) UNSIGNED NOT NULL,\n";
echo "  `status` varchar(255) DEFAULT NULL,\n";
echo "  `joined_at` timestamp NULL DEFAULT NULL,\n";
echo "  `removed_at` timestamp NULL DEFAULT NULL,\n";
echo "  `created_at` timestamp NULL DEFAULT NULL,\n";
echo "  `updated_at` timestamp NULL DEFAULT NULL\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

echo "-- 6. messages table\n";
echo "CREATE TABLE `messages` (\n";
echo "  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n";
echo "  `title` varchar(255) NOT NULL,\n";
echo "  `content` text NOT NULL,\n";
echo "  `recipient_type` varchar(255) DEFAULT NULL,\n";
echo "  `file_path` varchar(255) DEFAULT NULL,\n";
echo "  `created_at` timestamp NULL DEFAULT NULL,\n";
echo "  `updated_at` timestamp NULL DEFAULT NULL\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

echo "-- 7. payment_types table\n";
echo "CREATE TABLE `payment_types` (\n";
echo "  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n";
echo "  `name` varchar(255) NOT NULL,\n";
echo "  `applies_to` varchar(255) DEFAULT NULL,\n";
echo "  `is_required` tinyint(1) NOT NULL DEFAULT 0,\n";
echo "  `default_amount` decimal(10,2) DEFAULT NULL,\n";
echo "  `default_discount` decimal(5,2) DEFAULT NULL,\n";
echo "  `created_at` timestamp NULL DEFAULT NULL,\n";
echo "  `updated_at` timestamp NULL DEFAULT NULL\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

echo "-- 8. school_fees table\n";
echo "CREATE TABLE `school_fees` (\n";
echo "  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n";
echo "  `academic_year_id` bigint(20) UNSIGNED NOT NULL,\n";
echo "  `term_id` bigint(20) UNSIGNED NOT NULL,\n";
echo "  `amount` decimal(10,2) NOT NULL,\n";
echo "  `created_at` timestamp NULL DEFAULT NULL,\n";
echo "  `updated_at` timestamp NULL DEFAULT NULL\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

echo "-- 9. school_fee_segments table\n";
echo "CREATE TABLE `school_fee_segments` (\n";
echo "  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n";
echo "  `school_fee_id` bigint(20) UNSIGNED NOT NULL,\n";
echo "  `segment_name` varchar(255) NOT NULL,\n";
echo "  `percentage` decimal(5,2) NOT NULL,\n";
echo "  `created_at` timestamp NULL DEFAULT NULL,\n";
echo "  `updated_at` timestamp NULL DEFAULT NULL\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

echo "-- 10. tariffs table\n";
echo "CREATE TABLE `tariffs` (\n";
echo "  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n";
echo "  `activity` varchar(255) NOT NULL,\n";
echo "  `amount` decimal(10,2) NOT NULL,\n";
echo "  `has_discount` tinyint(1) NOT NULL DEFAULT 0,\n";
echo "  `discount_amount` decimal(10,2) DEFAULT NULL,\n";
echo "  `discount_type` varchar(50) DEFAULT NULL,\n";
echo "  `deadline_type` varchar(50) DEFAULT NULL,\n";
echo "  `deadline_date` datetime DEFAULT NULL,\n";
echo "  `deadline_hours` int(11) DEFAULT NULL,\n";
echo "  `created_at` timestamp NULL DEFAULT NULL,\n";
echo "  `updated_at` timestamp NULL DEFAULT NULL\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

echo "-- 11. transcripts table\n";
echo "CREATE TABLE `transcripts` (\n";
echo "  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n";
echo "  `student_id` bigint(20) UNSIGNED NOT NULL,\n";
echo "  `course_id` bigint(20) UNSIGNED NOT NULL,\n";
echo "  `fee_type` varchar(255) NOT NULL,\n";
echo "  `amount_paid` decimal(10,2) DEFAULT NULL,\n";
echo "  `total_fee` decimal(10,2) DEFAULT NULL,\n";
echo "  `payment_method` varchar(255) NOT NULL,\n";
echo "  `payment_date` date DEFAULT NULL,\n";
echo "  `term` varchar(255) DEFAULT NULL,\n";
echo "  `created_at` timestamp NULL DEFAULT NULL,\n";
echo "  `updated_at` timestamp NULL DEFAULT NULL\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

echo "-- MISSING COLUMNS (need to be added to existing tables):\n\n";

echo "-- 1. attendances table - add timetable_id column\n";
echo "ALTER TABLE `attendances` ADD COLUMN `timetable_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `lesson_id`;\n\n";

echo "-- 2. blog_documents table - add missing columns\n";
echo "ALTER TABLE `blog_documents` ADD COLUMN `document_size` bigint(20) DEFAULT NULL AFTER `document_path`;\n";
echo "ALTER TABLE `blog_documents` ADD COLUMN `document_type` varchar(255) DEFAULT NULL AFTER `document_size`;\n\n";

echo "-- 3. blog_images table - add missing columns\n";
echo "ALTER TABLE `blog_images` ADD COLUMN `image_alt` varchar(255) DEFAULT NULL AFTER `image_path`;\n";
echo "ALTER TABLE `blog_images` ADD COLUMN `image_caption` text DEFAULT NULL AFTER `image_alt`;\n\n";

echo "-- 4. blogs table - add missing columns\n";
echo "ALTER TABLE `blogs` ADD COLUMN `excerpt` text DEFAULT NULL AFTER `content`;\n";
echo "ALTER TABLE `blogs` ADD COLUMN `featured_image` varchar(255) DEFAULT NULL AFTER `excerpt`;\n";
echo "ALTER TABLE `blogs` ADD COLUMN `author_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `featured_image`;\n";
echo "ALTER TABLE `blogs` ADD COLUMN `tags` varchar(255) DEFAULT NULL AFTER `author_id`;\n";
echo "ALTER TABLE `blogs` ADD COLUMN `category` varchar(255) DEFAULT NULL AFTER `tags`;\n";
echo "ALTER TABLE `blogs` ADD COLUMN `views` bigint(20) DEFAULT 0 AFTER `category`;\n";
echo "ALTER TABLE `blogs` ADD COLUMN `is_featured` tinyint(1) DEFAULT 0 AFTER `views`;\n\n";

echo "-- 5. client_transactions table - add payment_reference column\n";
echo "ALTER TABLE `client_transactions` ADD COLUMN `payment_reference` varchar(255) DEFAULT NULL AFTER `transaction_id`;\n\n";

echo "-- 6. courses table - add total_cost column\n";
echo "ALTER TABLE `courses` ADD COLUMN `total_cost` decimal(10,2) DEFAULT NULL AFTER `course_name`;\n\n";

echo "-- 7. exams table - add missing columns\n";
echo "ALTER TABLE `exams` ADD COLUMN `course_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `subject`;\n";
echo "ALTER TABLE `exams` ADD COLUMN `lesson_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `course_id`;\n\n";

echo "-- 8. grades table - add missing columns\n";
echo "ALTER TABLE `grades` ADD COLUMN `min_mark` tinyint(3) UNSIGNED DEFAULT NULL AFTER `max_score`;\n";
echo "ALTER TABLE `grades` ADD COLUMN `max_mark` tinyint(3) UNSIGNED DEFAULT NULL AFTER `min_mark`;\n";
echo "ALTER TABLE `grades` ADD COLUMN `remark` varchar(255) DEFAULT NULL AFTER `max_mark`;\n\n";

echo "-- 9. lesson_exam table - add id column\n";
echo "ALTER TABLE `lesson_exam` ADD COLUMN `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;\n\n";

echo "-- 10. resources table - add video_path column\n";
echo "ALTER TABLE `resources` ADD COLUMN `video_path` varchar(255) DEFAULT NULL AFTER `document_path`;\n\n";

echo "-- 11. staff_branch table - add id column\n";
echo "ALTER TABLE `staff_branch` ADD COLUMN `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;\n\n";

echo "-- 12. teacher_dailies table - add approved column\n";
echo "ALTER TABLE `teacher_dailies` ADD COLUMN `approved` tinyint(1) DEFAULT 0 AFTER `content`;\n\n";

echo "-- 13. terms table - add missing columns\n";
echo "ALTER TABLE `terms` ADD COLUMN `term` varchar(255) DEFAULT NULL AFTER `id`;\n";
echo "ALTER TABLE `terms` ADD COLUMN `starting_date` date DEFAULT NULL AFTER `term`;\n";
echo "ALTER TABLE `terms` ADD COLUMN `ending_date` date DEFAULT NULL AFTER `starting_date`;\n";
echo "ALTER TABLE `terms` ADD COLUMN `status` tinyint(4) DEFAULT 1 AFTER `ending_date`;\n\n";

echo "\n-- SUMMARY:\n";
echo "-- - 11 missing tables need to be created\n";
echo "-- - 13 tables need column additions\n";
echo "-- - 1 table exists in local but not online (dismissed_students)\n";
echo "-- Run these SQL commands in order to sync your local database\n";
