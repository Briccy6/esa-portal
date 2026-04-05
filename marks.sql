-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 05, 2026 at 01:20 PM
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
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `id` int(11) NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `lesson_id` bigint(20) UNSIGNED DEFAULT NULL,
  `term_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assessment_type` varchar(255) DEFAULT NULL,
  `exam_id` bigint(20) UNSIGNED DEFAULT NULL,
  `value` int(11) NOT NULL,
  `marks_out_of` int(11) DEFAULT NULL,
  `test_date` date DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`id`, `student_id`, `course_id`, `branch_id`, `lesson_id`, `term_id`, `assessment_type`, `exam_id`, `value`, `marks_out_of`, `test_date`, `exam_date`, `grade_id`, `status`, `created_at`, `updated_at`) VALUES
(133, 18, 20, 4, 10, 3, 'test', NULL, 10, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(134, 19, 20, 4, 10, 3, 'test', NULL, 5, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(135, 28, 20, 4, 10, 3, 'test', NULL, 0, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(136, 137, 20, 4, 10, 3, 'test', NULL, 0, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(137, 138, 20, 4, 10, 3, 'test', NULL, 0, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(138, 139, 20, 4, 10, 3, 'test', NULL, 0, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(139, 140, 20, 4, 10, 3, 'test', NULL, 0, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(140, 160, 20, 4, 10, 3, 'test', NULL, 0, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(141, 165, 20, 4, 10, 3, 'test', NULL, 0, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(142, 172, 20, 4, 10, 3, 'test', NULL, 0, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(143, 181, 20, 4, 10, 3, 'test', NULL, 0, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(144, 185, 20, 4, 10, 3, 'test', NULL, 0, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(145, 188, 20, 4, 10, 3, 'test', NULL, 0, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(146, 240, 20, 4, 10, 3, 'test', NULL, 0, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(147, 243, 20, 4, 10, 3, 'test', NULL, 0, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(148, 269, 20, 4, 10, 3, 'test', NULL, 6, 10, '2026-03-10', NULL, NULL, 'recorded', '2026-04-05 13:12:24', '2026-04-05 13:12:24'),
(149, 18, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(150, 19, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(151, 28, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(152, 137, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(153, 138, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(154, 139, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(155, 140, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(156, 160, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(157, 165, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(158, 172, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(159, 181, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(160, 185, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(161, 188, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(162, 240, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(163, 243, 20, 4, 10, 3, 'exam', 5, 0, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(164, 269, 20, 4, 10, 3, 'exam', 5, 89, NULL, NULL, '2026-01-05', NULL, 'recorded', '2026-04-05 13:17:52', '2026-04-05 13:17:52'),
(165, 18, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(166, 19, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(167, 28, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(168, 137, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(169, 138, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(170, 139, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(171, 140, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(172, 160, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(173, 165, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(174, 172, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(175, 181, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(176, 185, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(177, 188, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(178, 240, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(179, 243, 20, 4, 10, 4, 'exam', 6, 0, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09'),
(180, 269, 20, 4, 10, 4, 'exam', 6, 19, NULL, NULL, '2026-03-30', NULL, 'recorded', '2026-04-05 13:18:09', '2026-04-05 13:18:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marks_student_id_foreign` (`student_id`),
  ADD KEY `marks_course_id_foreign` (`course_id`),
  ADD KEY `marks_branch_id_foreign` (`branch_id`),
  ADD KEY `marks_exam_id_foreign` (`exam_id`),
  ADD KEY `marks_grade_id_foreign` (`grade_id`),
  ADD KEY `marks_lesson_id_foreign` (`lesson_id`),
  ADD KEY `marks_term_id_foreign` (`term_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `marks`
--
ALTER TABLE `marks`
  ADD CONSTRAINT `marks_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marks_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marks_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marks_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `marks_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marks_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marks_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
