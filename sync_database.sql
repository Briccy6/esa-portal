-- Missing tables
CREATE TABLE `community_spaces` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `term_id` bigint(20) UNSIGNED NOT NULL,
  `min_fee_percentage` float NOT NULL DEFAULT 0,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `community_space_course` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `community_space_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `community_space_messages` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `community_space_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `staff_id` bigint(20) UNSIGNED DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `message` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sender_type` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `community_space_message_reads` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `community_space_message_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `community_space_student` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `community_space_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `joined_at` timestamp NULL DEFAULT NULL,
  `removed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `recipient_type` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payment_types` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `applies_to` varchar(255) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `default_amount` decimal(10,2) DEFAULT NULL,
  `default_discount` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `school_fees` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `term_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `school_fee_segments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_fee_id` bigint(20) UNSIGNED NOT NULL,
  `segment_name` varchar(255) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tariffs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `has_discount` tinyint(1) NOT NULL DEFAULT 0,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `discount_type` varchar(50) DEFAULT NULL,
  `deadline_type` varchar(50) DEFAULT NULL,
  `deadline_date` datetime DEFAULT NULL,
  `deadline_hours` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `transcripts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `fee_type` varchar(255) NOT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `total_fee` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(255) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `term` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Missing columns
ALTER TABLE `attendances` ADD COLUMN `timetable_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `lesson_id`;

ALTER TABLE `blog_documents` ADD COLUMN `document_size` bigint(20) DEFAULT NULL AFTER `document_path`;
ALTER TABLE `blog_documents` ADD COLUMN `document_type` varchar(255) DEFAULT NULL AFTER `document_size`;

ALTER TABLE `blog_images` ADD COLUMN `image_alt` varchar(255) DEFAULT NULL AFTER `image_path`;
ALTER TABLE `blog_images` ADD COLUMN `image_caption` text DEFAULT NULL AFTER `image_alt`;

ALTER TABLE `blogs` ADD COLUMN `excerpt` text DEFAULT NULL AFTER `content`;
ALTER TABLE `blogs` ADD COLUMN `featured_image` varchar(255) DEFAULT NULL AFTER `excerpt`;
ALTER TABLE `blogs` ADD COLUMN `author_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `featured_image`;
ALTER TABLE `blogs` ADD COLUMN `tags` varchar(255) DEFAULT NULL AFTER `author_id`;
ALTER TABLE `blogs` ADD COLUMN `category` varchar(255) DEFAULT NULL AFTER `tags`;
ALTER TABLE `blogs` ADD COLUMN `views` bigint(20) DEFAULT 0 AFTER `category`;
ALTER TABLE `blogs` ADD COLUMN `is_featured` tinyint(1) DEFAULT 0 AFTER `views`;

ALTER TABLE `client_transactions` ADD COLUMN `payment_reference` varchar(255) DEFAULT NULL AFTER `transaction_id`;

ALTER TABLE `courses` ADD COLUMN `total_cost` decimal(10,2) DEFAULT NULL AFTER `course_name`;

ALTER TABLE `exams` ADD COLUMN `course_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `subject`;
ALTER TABLE `exams` ADD COLUMN `lesson_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `course_id`;

ALTER TABLE `grades` ADD COLUMN `min_mark` tinyint(3) UNSIGNED DEFAULT NULL AFTER `max_score`;
ALTER TABLE `grades` ADD COLUMN `max_mark` tinyint(3) UNSIGNED DEFAULT NULL AFTER `min_mark`;
ALTER TABLE `grades` ADD COLUMN `remark` varchar(255) DEFAULT NULL AFTER `max_mark`;

ALTER TABLE `lesson_exam` ADD COLUMN `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;

ALTER TABLE `resources` ADD COLUMN `video_path` varchar(255) DEFAULT NULL AFTER `document_path`;

ALTER TABLE `staff_branch` ADD COLUMN `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;

ALTER TABLE `teacher_dailies` ADD COLUMN `approved` tinyint(1) DEFAULT 0 AFTER `content`;

ALTER TABLE `terms` ADD COLUMN `term` varchar(255) DEFAULT NULL AFTER `id`;
ALTER TABLE `terms` ADD COLUMN `starting_date` date DEFAULT NULL AFTER `term`;
ALTER TABLE `terms` ADD COLUMN `ending_date` date DEFAULT NULL AFTER `starting_date`;
ALTER TABLE `terms` ADD COLUMN `status` tinyint(4) DEFAULT 1 AFTER `ending_date`;
