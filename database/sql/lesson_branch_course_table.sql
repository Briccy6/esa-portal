-- ESA Portal - Lesson Branch Course Table
-- Multi-Branch Lesson Support System
-- Created: April 2, 2026

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS=0;

-- Create lesson_branch_course table
CREATE TABLE `lesson_branch_course` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lesson_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lesson_branch_course_unique` (`lesson_id`, `branch_id`, `course_id`),
  KEY `lesson_branch_course_lesson_id_foreign` (`lesson_id`),
  KEY `lesson_branch_course_branch_id_foreign` (`branch_id`),
  KEY `lesson_branch_course_course_id_foreign` (`course_id`),
  KEY `lesson_branch_course_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `lesson_branch_course_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lesson_branch_course_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lesson_branch_course_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lesson_branch_course_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

-- Add indexes for better performance
CREATE INDEX `idx_lesson_branch_course_lesson_branch` ON `lesson_branch_course` (`lesson_id`, `branch_id`);
CREATE INDEX `idx_lesson_branch_course_course_branch` ON `lesson_branch_course` (`course_id`, `branch_id`);

-- Table description:
-- This table enables multi-branch lesson assignments, allowing a single lesson to be
-- assigned to multiple courses across different branches with different teachers per branch.
--
-- Columns:
-- - id: Primary key
-- - lesson_id: Foreign key to lessons table (the lesson being assigned)
-- - branch_id: Foreign key to branches table (which branch this assignment is for)
-- - course_id: Foreign key to courses table (which course this assignment is for)
-- - teacher_id: Foreign key to teachers table (teacher assigned to this specific branch-course combination)
-- - created_at/updated_at: Timestamps
--
-- Constraints:
-- - Unique constraint ensures each lesson can only be assigned once per branch-course combination
-- - Cascade delete on lesson, branch, course to maintain data integrity
-- - Set null on teacher delete to preserve assignment history
--
-- Usage Example:
-- A "Mathematics" lesson can be assigned to:
-- - Kigali Branch → Tourism Course → Teacher John Doe
-- - Musanze Branch → Tourism Course → Teacher Jane Smith
-- - Kigali Branch → Hospitality Course → Teacher Mike Wilson
