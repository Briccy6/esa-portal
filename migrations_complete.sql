CREATE TABLE `course_term_fees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned NOT NULL,
  `term_id` bigint(20) unsigned NOT NULL,
  `registration_fee` decimal(10,2) DEFAULT NULL,
  `tuition_fee` decimal(10,2) DEFAULT NULL,
  `practical_fee` decimal(10,2) DEFAULT NULL,
  `trip_fee` decimal(10,2) DEFAULT NULL,
  `student_card_fee` decimal(10,2) DEFAULT NULL,
  `total_fee` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_term_fees_course_id_term_id_unique` (`course_id`,`term_id`),
  KEY `course_term_fees_course_id_foreign` (`course_id`),
  KEY `course_term_fees_term_id_foreign` (`term_id`),
  CONSTRAINT `course_term_fees_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_term_fees_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `student_transactions` ADD COLUMN `term_id` bigint(20) unsigned NULL AFTER `amount`;

ALTER TABLE `student_transactions` ADD CONSTRAINT `student_transactions_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id`) ON DELETE SET NULL;

ALTER TABLE `student_transactions` ADD INDEX `student_transactions_term_id_index` (`term_id`);

ALTER TABLE `expenses` ADD COLUMN `term_id` bigint(20) unsigned NULL AFTER `amount`;

ALTER TABLE `expenses` ADD CONSTRAINT `expenses_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id`) ON DELETE SET NULL;

ALTER TABLE `expenses` ADD INDEX `expenses_term_id_index` (`term_id`);

ALTER TABLE `marks` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
