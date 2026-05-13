-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2026 at 06:02 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attendance_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`log_id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'User Login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-10 18:32:02'),
(3, 1, 'User Login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-10 18:42:55'),
(4, 1, 'Mark Attendance', 'Marked attendance for date: 2025-06-10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-10 18:45:00'),
(5, 1, 'Mark Attendance', 'Marked attendance for date: 2025-06-10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-10 18:47:07'),
(6, 1, 'Mark Attendance', 'Marked attendance for date: 2025-06-10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-10 18:52:16'),
(8, 1, 'User Login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-10 18:59:19'),
(10, 1, 'User Login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-10 19:03:32'),
(11, 1, 'Mark Attendance', 'Marked attendance for date: 2025-06-10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-10 19:05:43'),
(13, 1, 'User Login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-11 08:28:20'),
(14, 1, 'Mark Attendance', 'Marked attendance for date: 2025-06-11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-11 08:29:07'),
(17, 1, 'User Login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-11 12:47:06'),
(19, 1, 'User Login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-11 13:08:48'),
(21, 1, 'User Login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-11 13:12:05'),
(22, 7, 'User Login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-11 13:17:30'),
(23, 1, 'User Login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-11 13:23:58'),
(24, 8, 'User Login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-11 13:27:04'),
(25, 1, 'User Login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-11 13:28:00'),
(26, 1, 'Mark Attendance', 'Marked attendance for date: 2025-06-11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-11 13:28:35'),
(27, 1, 'Mark Attendance', 'Marked attendance for date: 2025-06-12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-12 09:20:29');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late') NOT NULL,
  `marked_by` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `student_id`, `date`, `status`, `marked_by`, `remarks`, `created_at`, `updated_at`) VALUES
(6, 6, '2025-06-11', 'present', 1, NULL, '2025-06-11 13:28:35', '2025-06-11 13:28:35'),
(7, 8, '2025-06-11', 'late', 1, NULL, '2025-06-11 13:28:35', '2025-06-11 13:28:35'),
(8, 7, '2025-06-11', 'absent', 1, NULL, '2025-06-11 13:28:35', '2025-06-11 13:28:35'),
(9, 9, '2025-06-12', 'present', 1, NULL, '2025-06-12 09:20:29', '2025-06-12 09:20:29'),
(10, 8, '2025-06-12', 'present', 1, NULL, '2025-06-12 09:20:29', '2025-06-12 09:20:29'),
(11, 6, '2025-06-12', 'present', 1, NULL, '2025-06-12 09:20:29', '2025-06-12 09:20:29'),
(12, 7, '2025-06-12', 'present', 1, NULL, '2025-06-12 09:20:29', '2025-06-12 09:20:29');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `setting_key`, `setting_value`, `setting_description`, `created_at`, `updated_at`) VALUES
(1, 'school_name', 'Attendance Management System', 'Name of the school', '2025-06-10 18:24:31', '2025-06-10 18:24:31'),
(2, 'school_address', '', 'Address of the school', '2025-06-10 18:24:31', '2025-06-10 18:24:31'),
(3, 'school_phone', '', 'Contact number of the school', '2025-06-10 18:24:31', '2025-06-10 18:24:31'),
(4, 'school_email', '', 'Email address of the school', '2025-06-10 18:24:31', '2025-06-10 18:24:31'),
(5, 'attendance_start_time', '09:00:00', 'Default start time for attendance', '2025-06-10 18:24:31', '2025-06-10 18:24:31'),
(6, 'attendance_end_time', '15:00:00', 'Default end time for attendance', '2025-06-10 18:24:31', '2025-06-10 18:24:31'),
(7, 'late_threshold', '09:15:00', 'Time after which a student is marked late', '2025-06-10 18:24:31', '2025-06-10 18:24:31');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `roll_number` varchar(20) NOT NULL,
  `class` varchar(50) NOT NULL,
  `section` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `full_name`, `email`, `roll_number`, `class`, `section`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(6, 7, 'santu', 'asds@gamil.com', 'STU00007', 'LKG', NULL, NULL, NULL, '2025-06-11 13:17:14', '2025-06-11 13:17:14'),
(7, 8, 'akansha', 'mail@gmail.com', 'STU00008', 'UKG', NULL, NULL, NULL, '2025-06-11 13:26:48', '2025-06-11 13:26:48'),
(8, 9, 'riha', 'rahul@gmail.com', 'STU00009', 'Nursery', NULL, NULL, NULL, '2025-06-11 13:27:48', '2025-06-11 13:27:48'),
(9, 10, 'rahul', 'kiarn@gmail.com', '3', 'Nursery', 'a', '123', 'zinganoor', '2025-06-11 13:49:52', '2025-06-11 13:49:52'),
(10, 11, 'shrinivas', 'srinivaskumri2009@gmail.com', 'STU00011', 'Nursery', NULL, NULL, NULL, '2026-05-13 19:49:36', '2026-05-13 19:49:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `email`, `full_name`, `created_at`, `updated_at`) VALUES
(1, 'sanjay', '$2y$10$2c.3dAtk8BIAWoViGJWwKOJD5ryVQVeaCb1hHxw8aIZCB4rIdUK2W', 'teacher', 'jivo21@gmail.com', 'sanjay', '2025-06-10 18:30:04', '2025-06-10 18:30:04'),
(5, 'arush', '$2y$10$/nDAgb.arPvIXx4jhlvmKeinP0TYrh3EpmKRUsUrtWR/TlV/IlEJK', 'student', 'ganesh@gmail.com', 'arush', '2025-06-11 08:25:25', '2025-06-11 08:25:25'),
(7, 'santu', '$2y$10$VxyQ6eCvxn8wL23HFRB5lOBZTGGDiVnbbP5pexVh1dCfMtFsFwzyK', 'student', 'asds@gamil.com', 'santu', '2025-06-11 13:17:14', '2025-06-11 13:17:14'),
(8, 'akansha', '$2y$10$EYc58HAmS3AVx3k.Li6.a.38sZRrWD4ktk/Fdci.YBljynVd.q1AC', 'student', 'mail@gmail.com', 'akansha', '2025-06-11 13:26:48', '2025-06-11 13:26:48'),
(9, 'riha', '$2y$10$jOHs3jIDdsnAeDmj7/qA5eEL/c/NKpbPwefdgzga2ubLL5rHyZLny', 'student', 'rahul@gmail.com', 'riha', '2025-06-11 13:27:48', '2025-06-11 13:27:48'),
(10, 'rahul', '$2y$10$yDGbGT97tgxi20tLPEZSSu2isgWU.FwKDd6MBocaw7uOyHRvZW7gW', 'student', 'kiarn@gmail.com', 'rahul', '2025-06-11 13:49:52', '2025-06-11 13:49:52'),
(11, 'shri', '$2y$10$bjBcHFRJhOGqm4wBXcVW0ejWqQQSiSU.m/VGUW66Sk7SDcCxMsAkG', 'student', 'srinivaskumri2009@gmail.com', 'shrinivas', '2026-05-13 19:49:36', '2026-05-13 19:49:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_date` (`user_id`,`created_at`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`date`),
  ADD KEY `marked_by` (`marked_by`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_user_read` (`user_id`,`is_read`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `roll_number` (`roll_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_class` (`class`),
  ADD KEY `idx_roll_number` (`roll_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`marked_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
