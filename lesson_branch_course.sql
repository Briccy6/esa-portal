-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 05, 2026 at 12:51 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u244769603_esa`
--

-- --------------------------------------------------------

--
-- Table structure for table `lesson_branch_course`
--

CREATE TABLE `lesson_branch_course` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lesson_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lesson_branch_course`
--

INSERT INTO `lesson_branch_course` (`id`, `lesson_id`, `branch_id`, `course_id`, `teacher_id`, `created_at`, `updated_at`) VALUES
(15, 44, 4, 16, NULL, '2026-04-02 15:31:37', '2026-04-02 15:31:37'),
(16, 44, 6, 16, NULL, '2026-04-02 15:31:37', '2026-04-02 15:31:37'),
(17, 44, 5, 37, NULL, '2026-04-02 15:31:37', '2026-04-02 15:31:37'),
(18, 44, 5, 39, NULL, '2026-04-02 15:31:37', '2026-04-02 15:31:37'),
(19, 45, 4, 16, 18, '2026-04-02 15:36:31', '2026-04-02 15:36:31'),
(20, 45, 6, 16, 18, '2026-04-02 15:36:31', '2026-04-02 15:36:31'),
(21, 45, 5, 37, 18, '2026-04-02 15:36:31', '2026-04-02 15:36:31'),
(22, 46, 4, 16, NULL, '2026-04-02 15:45:19', '2026-04-02 15:45:19'),
(23, 46, 6, 16, NULL, '2026-04-02 15:45:19', '2026-04-02 15:45:19'),
(24, 46, 5, 37, NULL, '2026-04-02 15:45:19', '2026-04-02 15:45:19'),
(25, 47, 4, 16, NULL, '2026-04-02 15:50:31', '2026-04-02 15:50:31'),
(26, 47, 6, 16, NULL, '2026-04-02 15:50:31', '2026-04-02 15:50:31'),
(27, 47, 5, 37, NULL, '2026-04-02 15:50:31', '2026-04-02 15:50:31'),
(28, 28, 5, 33, 18, '2026-04-02 16:02:00', '2026-04-02 16:02:00'),
(29, 22, 5, 33, 18, '2026-04-02 16:08:06', '2026-04-02 16:08:06'),
(30, 48, 4, 14, 9, '2026-04-02 16:21:52', '2026-04-02 16:21:52'),
(31, 48, 6, 14, 9, '2026-04-02 16:21:52', '2026-04-02 16:21:52'),
(32, 48, 4, 22, 9, '2026-04-02 16:21:52', '2026-04-02 16:21:52'),
(33, 48, 6, 22, 10, '2026-04-02 16:21:52', '2026-04-02 16:21:52'),
(34, 48, 4, 23, 9, '2026-04-02 16:21:52', '2026-04-02 16:21:52'),
(35, 48, 4, 24, 10, '2026-04-02 16:21:52', '2026-04-02 16:21:52'),
(36, 48, 6, 24, 9, '2026-04-02 16:21:52', '2026-04-02 16:21:52'),
(37, 48, 4, 25, 9, '2026-04-02 16:21:52', '2026-04-02 16:21:52'),
(38, 48, 6, 25, 10, '2026-04-02 16:21:52', '2026-04-02 16:21:52'),
(39, 48, 5, 31, NULL, '2026-04-02 16:21:52', '2026-04-02 16:21:52'),
(40, 48, 5, 32, NULL, '2026-04-02 16:21:52', '2026-04-02 16:21:52'),
(41, 48, 5, 34, NULL, '2026-04-02 16:21:52', '2026-04-02 16:21:52'),
(42, 49, 4, 14, NULL, '2026-04-02 17:27:46', '2026-04-02 17:27:46'),
(43, 49, 6, 14, NULL, '2026-04-02 17:27:46', '2026-04-02 17:27:46'),
(44, 49, 4, 21, NULL, '2026-04-02 17:27:46', '2026-04-02 17:27:46'),
(45, 49, 6, 21, NULL, '2026-04-02 17:27:46', '2026-04-02 17:27:46'),
(46, 49, 4, 22, NULL, '2026-04-02 17:27:46', '2026-04-02 17:27:46'),
(47, 49, 6, 22, NULL, '2026-04-02 17:27:46', '2026-04-02 17:27:46'),
(48, 49, 5, 36, NULL, '2026-04-02 17:27:46', '2026-04-02 17:27:46'),
(49, 10, 4, 20, 10, '2026-04-05 00:42:58', '2026-04-05 00:42:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lesson_branch_course`
--
ALTER TABLE `lesson_branch_course`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lesson_branch_course_unique` (`lesson_id`,`branch_id`,`course_id`),
  ADD KEY `lesson_branch_course_lesson_id_foreign` (`lesson_id`),
  ADD KEY `lesson_branch_course_branch_id_foreign` (`branch_id`),
  ADD KEY `lesson_branch_course_course_id_foreign` (`course_id`),
  ADD KEY `lesson_branch_course_teacher_id_foreign` (`teacher_id`),
  ADD KEY `idx_lesson_branch_course_lesson_branch` (`lesson_id`,`branch_id`),
  ADD KEY `idx_lesson_branch_course_course_branch` (`course_id`,`branch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lesson_branch_course`
--
ALTER TABLE `lesson_branch_course`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lesson_branch_course`
--
ALTER TABLE `lesson_branch_course`
  ADD CONSTRAINT `lesson_branch_course_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_branch_course_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_branch_course_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_branch_course_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
