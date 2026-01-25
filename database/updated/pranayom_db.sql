-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 25, 2026 at 03:25 AM
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
-- Database: `pranayom_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default_avatar.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `user_id`, `full_name`, `phone`, `profile_picture`) VALUES
(1, 1, 'System', '01710000000', 'admin_1_1769267006.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `schedule_day` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') DEFAULT NULL,
  `schedule_time` time NOT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `capacity` int(11) DEFAULT 20,
  `class_type` enum('yoga','meditation','prenatal','postnatal','general') DEFAULT 'general',
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`, `trainer_id`, `description`, `schedule_day`, `schedule_time`, `duration_minutes`, `capacity`, `class_type`, `is_active`) VALUES
(1, 'Morning Yoga', 1, NULL, 'monday', '07:00:00', 60, 20, 'yoga', 1),
(2, 'Prenatal Care', 1, NULL, 'wednesday', '10:00:00', 60, 20, 'prenatal', 1),
(3, 'Strength Basics', 2, NULL, 'friday', '18:00:00', 75, 20, 'general', 1);

-- --------------------------------------------------------

--
-- Table structure for table `class_bookings`
--

CREATE TABLE `class_bookings` (
  `booking_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `status` enum('booked','attended','cancelled') DEFAULT 'booked',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_bookings`
--

INSERT INTO `class_bookings` (`booking_id`, `member_id`, `class_id`, `booking_date`, `status`, `created_at`) VALUES
(1, 1, 1, '2025-01-20', 'booked', '2026-01-23 14:56:53'),
(2, 2, 2, '2025-01-21', 'booked', '2026-01-23 14:56:53'),
(3, 3, 3, '2025-01-22', 'booked', '2026-01-23 14:56:53'),
(4, 1, 2, '2026-01-24', 'cancelled', '2026-01-24 15:11:26'),
(5, 6, 1, '2026-01-25', 'cancelled', '2026-01-24 18:29:46'),
(6, 1, 2, '2026-01-25', 'cancelled', '2026-01-25 01:05:45');

-- --------------------------------------------------------

--
-- Table structure for table `diet_plans`
--

CREATE TABLE `diet_plans` (
  `diet_plan_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `meal_name` varchar(100) NOT NULL,
  `meal_time` enum('breakfast','lunch','dinner','snack') NOT NULL,
  `food_items` text NOT NULL,
  `calories` decimal(10,2) DEFAULT NULL,
  `protein_grams` decimal(10,2) DEFAULT NULL,
  `carbs_grams` decimal(10,2) DEFAULT NULL,
  `fat_grams` decimal(10,2) DEFAULT NULL,
  `created_by` enum('trainer','member') NOT NULL,
  `plan_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_consumed` tinyint(1) DEFAULT 0,
  `product_weight` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `diet_plans`
--

INSERT INTO `diet_plans` (`diet_plan_id`, `member_id`, `trainer_id`, `meal_name`, `meal_time`, `food_items`, `calories`, `protein_grams`, `carbs_grams`, `fat_grams`, `created_by`, `plan_date`, `created_at`, `is_consumed`, `product_weight`) VALUES
(1, 1, 1, 'Healthy Breakfast', 'breakfast', 'Oats, Banana, Milk', 350.00, NULL, NULL, NULL, 'trainer', '2026-01-23', '2026-01-23 14:56:53', 0, 0.00),
(2, 2, 1, 'Light Lunch', 'lunch', 'Rice, Vegetables', 450.00, NULL, NULL, NULL, 'trainer', '2026-01-23', '2026-01-23 14:56:53', 0, 0.00),
(3, 3, 2, 'Protein Dinner', 'dinner', 'Chicken, Salad', 600.00, NULL, NULL, NULL, 'trainer', '2026-01-23', '2026-01-23 14:56:53', 0, 0.00),
(4, 4, 1, 'Afia Breakfast', 'breakfast', 'Smoothie, Fruits', 300.00, NULL, NULL, NULL, 'trainer', '2026-01-23', '2026-01-23 14:56:53', 0, 0.00),
(5, 4, 1, 'Afia Lunch', 'lunch', 'Salad, Yogurt', 400.00, NULL, NULL, NULL, 'trainer', '2026-01-23', '2026-01-23 14:56:53', 0, 0.00),
(6, 4, 1, 'Afia Dinner', 'dinner', 'Fish, Veggies', 500.00, NULL, NULL, NULL, 'trainer', '2026-01-23', '2026-01-23 14:56:53', 0, 0.00),
(7, 4, 1, 'Breakfast', 'breakfast', 'adfsad', NULL, NULL, NULL, NULL, 'trainer', '2026-01-25', '2026-01-25 01:09:19', 0, 0.00),
(8, 4, 1, 'Lunch', 'lunch', 'asdfaer', NULL, NULL, NULL, NULL, 'trainer', '2026-01-25', '2026-01-25 01:09:19', 0, 0.00),
(9, 4, 1, 'Dinner', 'dinner', 'aaaaa', NULL, NULL, NULL, NULL, 'trainer', '2026-01-25', '2026-01-25 01:09:19', 0, 0.00),
(10, 1, 1, 'Breakfast', 'breakfast', 's', 5.00, 0.00, 0.00, 0.00, 'trainer', '2026-01-19', '2026-01-25 02:02:50', 0, 0.00),
(11, 1, 1, 'Lunch', 'lunch', 's', 101.00, 0.00, 0.00, 0.00, 'trainer', '2026-01-19', '2026-01-25 02:02:50', 0, 0.00),
(12, 1, 1, 'Dinner', 'dinner', 'd', 200.00, 0.00, 0.00, 0.00, 'trainer', '2026-01-19', '2026-01-25 02:02:50', 0, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `membership_type` enum('basic','premium','vip') DEFAULT 'basic',
  `join_date` date NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'default_avatar.jpg',
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `medical_notes` text DEFAULT NULL,
  `trainer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `user_id`, `full_name`, `phone`, `address`, `date_of_birth`, `gender`, `membership_type`, `join_date`, `profile_picture`, `emergency_contact`, `emergency_phone`, `medical_notes`, `trainer_id`) VALUES
(1, 4, 'Selim Member', '01730000001', NULL, NULL, 'male', 'premium', '2026-01-23', 'default_avatar.jpg', NULL, NULL, NULL, 1),
(2, 5, 'Ayesha Member', '01730000002', NULL, NULL, 'female', 'basic', '2026-01-23', 'default_avatar.jpg', NULL, NULL, NULL, 1),
(3, 6, 'Nabila Member', '01730000003', NULL, NULL, 'female', 'vip', '2026-01-23', 'default_avatar.jpg', NULL, NULL, NULL, 2),
(4, 7, 'Afia Member', '01730000004', NULL, NULL, 'female', 'premium', '2026-01-23', 'default_avatar.jpg', NULL, NULL, NULL, 1),
(5, 8, 'member', '000000', NULL, NULL, 'male', 'basic', '2026-01-24', 'default_avatar.jpg', NULL, NULL, '', 3),
(6, 10, 'member', '23333333333', NULL, NULL, 'female', 'basic', '2026-01-25', 'default_avatar.jpg', NULL, NULL, '', NULL),
(7, 12, '12none', '1122', NULL, NULL, 'male', 'basic', '2026-01-25', 'default_avatar.jpg', NULL, NULL, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `message_text`, `sent_at`, `is_read`) VALUES
(1, 4, 2, 'hi', '2026-01-24 15:10:02', 0),
(2, 7, 2, 'hi', '2026-01-25 01:10:45', 0);

-- --------------------------------------------------------

--
-- Table structure for table `progress_tracking`
--

CREATE TABLE `progress_tracking` (
  `progress_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `tracking_date` date NOT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `heart_rate` int(11) DEFAULT NULL,
  `sleep_hours` decimal(4,2) DEFAULT NULL,
  `mood` enum('excellent','good','neutral','poor','bad') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `progress_tracking`
--

INSERT INTO `progress_tracking` (`progress_id`, `member_id`, `tracking_date`, `weight_kg`, `heart_rate`, `sleep_hours`, `mood`, `notes`, `created_at`) VALUES
(1, 1, '2026-01-23', 72.50, NULL, 7.50, 'good', NULL, '2026-01-23 14:56:53'),
(2, 2, '2026-01-23', 65.20, NULL, 8.00, 'excellent', NULL, '2026-01-23 14:56:53'),
(3, 3, '2026-01-23', 58.00, NULL, 6.80, 'neutral', NULL, '2026-01-23 14:56:53'),
(4, 4, '2026-01-23', 60.00, NULL, 7.00, 'good', NULL, '2026-01-23 14:56:53'),
(5, 1, '2026-01-24', 72.50, 120, 7.50, 'excellent', '', '2026-01-24 15:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `rating_type` enum('app','trainer') NOT NULL,
  `rating_value` int(11) NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`rating_id`, `member_id`, `trainer_id`, `rating_type`, `rating_value`, `comments`, `created_at`) VALUES
(1, 1, 1, 'trainer', 5, 'Excellent guidance', '2026-01-23 14:56:53'),
(2, 2, 1, 'trainer', 4, 'Very supportive', '2026-01-23 14:56:53'),
(3, 3, 2, 'trainer', 5, 'Great strength program', '2026-01-23 14:56:53');

-- --------------------------------------------------------

--
-- Table structure for table `routines`
--

CREATE TABLE `routines` (
  `routine_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `routine_type` enum('yoga','cardio','strength','flexibility','mixed') DEFAULT 'mixed',
  `difficulty_level` enum('beginner','intermediate','advanced') DEFAULT 'beginner',
  `duration_minutes` int(11) DEFAULT NULL,
  `exercises` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`exercises`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `scheduled_date` date DEFAULT NULL,
  `completed_exercises` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `routines`
--

INSERT INTO `routines` (`routine_id`, `member_id`, `trainer_id`, `title`, `description`, `routine_type`, `difficulty_level`, `duration_minutes`, `exercises`, `created_at`, `is_active`, `scheduled_date`, `completed_exercises`) VALUES
(1, 1, 1, 'Morning Yoga Flow', 'Gentle morning yoga', 'yoga', 'beginner', 30, '[{\"name\":\"Cat-Cow\",\"sets\":1,\"reps\":\"10 breaths\"}]', '2026-01-23 14:56:53', 1, NULL, '[0]'),
(2, 2, 1, 'Prenatal Strength', 'Safe pregnancy routine', 'strength', 'beginner', 25, '[{\"name\":\"Wall Push-ups\",\"sets\":2,\"reps\":10}]', '2026-01-23 14:56:53', 1, NULL, NULL),
(3, 3, 2, 'Full Body Strength', 'Intermediate strength workout', 'strength', 'intermediate', 45, '[{\"name\":\"Deadlift\",\"sets\":4,\"reps\":8}]', '2026-01-23 14:56:53', 1, NULL, NULL),
(4, 4, 1, 'Evening Yoga', 'Relaxing evening yoga', 'yoga', 'beginner', 20, '[{\"name\":\"Child Pose\",\"sets\":1,\"reps\":\"5 breaths\"}]', '2026-01-23 14:56:53', 1, NULL, NULL),
(5, 4, 1, 'Morning Flow', 'do nothing', 'mixed', 'beginner', NULL, '[{\"name\":\"ghuma babu \",\"description\":\"asdfdfsd\",\"duration\":\"5min\",\"reps\":\"5 round khelbo\",\"notes\":\"tmi onk cute baby\"}]', '2026-01-25 01:08:46', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `trainer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `bio` text DEFAULT NULL,
  `certification` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default_avatar.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`trainer_id`, `user_id`, `full_name`, `phone`, `specialization`, `experience_years`, `bio`, `certification`, `profile_picture`) VALUES
(1, 2, 'Rahim Yoga Trainer', '01720000001', 'Yoga & Meditation', 5, NULL, NULL, 'default_avatar.jpg'),
(2, 3, 'Karim Strength Trainer', '01720000002', 'Strength Training', 7, NULL, NULL, 'default_avatar.jpg'),
(3, 9, 'trainer', '000000', 'yoga', 10, '', 'adfadf', 'default_avatar.jpg'),
(4, 11, 'ria', '123', 'Pilates', 5, '', '', 'default_avatar.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('member','trainer','admin') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`, `role`, `is_active`, `created_at`, `last_login`) VALUES
(1, 'admin1', '$2y$10$HDaT0G7XJtsqiXafsHRpdO6z4hp/cIsbQiaH2iPIDKdQkpBYO43W2', 'admin@pranayom.com', 'admin', 1, '2026-01-23 14:56:53', '2026-01-25 01:04:40'),
(2, 'trainer1', '$2y$10$mqOP7JhTdI4AvDqVQXGIhuBOMVffN3yRf3UHHzg4bem1zWEjZAJcm', 'trainer1@pranayom.com', 'trainer', 1, '2026-01-23 14:56:53', '2026-01-25 02:10:40'),
(3, 'trainer2', '$2y$10$mqOP7JhTdI4AvDqVQXGIhuBOMVffN3yRf3UHHzg4bem1zWEjZAJcm', 'trainer2@pranayom.com', 'trainer', 1, '2026-01-23 14:56:53', NULL),
(4, 'member1', '$2y$10$btbKVdeeKiB6qVeFRLWP3uNuK7uqLQcfDRmZC/NBcZRmrlqoR7DWS', 'member1@pranayom.com', 'member', 1, '2026-01-23 14:56:53', '2026-01-25 02:19:49'),
(5, 'member2', '$2y$10$mqOP7JhTdI4AvDqVQXGIhuBOMVffN3yRf3UHHzg4bem1zWEjZAJcm', 'member2@pranayom.com', 'member', 1, '2026-01-23 14:56:53', NULL),
(6, 'member3', '$2y$10$mqOP7JhTdI4AvDqVQXGIhuBOMVffN3yRf3UHHzg4bem1zWEjZAJcm', 'member3@pranayom.com', 'member', 1, '2026-01-23 14:56:53', NULL),
(7, 'member_afia', '$2y$10$mqOP7JhTdI4AvDqVQXGIhuBOMVffN3yRf3UHHzg4bem1zWEjZAJcm', 'member_afia@pranayom.com', 'member', 1, '2026-01-23 14:56:53', '2026-01-25 01:09:47'),
(8, 'membertest', '$2y$10$MEVs1g/MW0JW/VB6axBqB.BdhxVdKbH0Q9K/QtGkzNRPk8zh.GN2K', 'member@gmail.com', 'member', 1, '2026-01-24 15:01:33', NULL),
(9, 'treainerTest', '$2y$10$R3A0Nr1GwxAppsjiOPvvsuf/NIQtw3r6Ws2jT/P1hVastOw/5lGEy', 'trainer@gmail.com', 'trainer', 1, '2026-01-24 15:02:54', '2026-01-24 18:40:09'),
(10, 'membertest2', '$2y$10$2MOzzpqJ7CP4dRlgCkvbhO8Q89l3zXvbe/sfBmMlJtwHGpcYGKe0O', 'moni@gmail.com', 'member', 1, '2026-01-24 18:27:50', '2026-01-24 18:40:28'),
(11, 'ria', '$2y$10$1/n0O5f8LjXciN/jxEv6peYJPdsWX2QVX87ECY9oFp2yM7V.L3/K2', 'ria@gmail.com', 'trainer', 1, '2026-01-25 00:58:06', NULL),
(12, 'none2', '$2y$10$3GPPDpPHn3XBaH1ZzXXcg.b3Sn.BHMknGicn9IO62h7OurY8mUVzq', 'none@gmalil.com', 'member', 1, '2026-01-25 01:02:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `workout_content`
--

CREATE TABLE `workout_content` (
  `content_id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content_type` enum('video','image','document','article') DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workout_content`
--

INSERT INTO `workout_content` (`content_id`, `trainer_id`, `title`, `content_type`, `file_path`, `tags`, `created_at`) VALUES
(1, 1, 'Morning Yoga Video', 'video', 'videos/yoga.mp4', 'yoga,morning', '2026-01-23 14:56:53'),
(2, 2, 'Strength Guide PDF', 'document', 'docs/strength.pdf', 'strength,training', '2026-01-23 14:56:53');

-- --------------------------------------------------------

--
-- Table structure for table `yoga_sessions`
--

CREATE TABLE `yoga_sessions` (
  `session_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `routine_id` int(11) DEFAULT NULL,
  `session_date` date NOT NULL,
  `duration_minutes` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `yoga_sessions`
--

INSERT INTO `yoga_sessions` (`session_id`, `member_id`, `routine_id`, `session_date`, `duration_minutes`, `notes`, `created_at`) VALUES
(1, 1, NULL, '2026-01-25', 30, '', '2026-01-25 01:39:57'),
(2, 1, NULL, '2026-01-25', 30, '', '2026-01-25 01:59:28'),
(3, 1, NULL, '2026-01-19', 30, '', '2026-01-25 01:59:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `class_bookings`
--
ALTER TABLE `class_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `member_id` (`member_id`,`class_id`,`booking_date`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `diet_plans`
--
ALTER TABLE `diet_plans`
  ADD PRIMARY KEY (`diet_plan_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `fk_member_trainer` (`trainer_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `progress_tracking`
--
ALTER TABLE `progress_tracking`
  ADD PRIMARY KEY (`progress_id`),
  ADD UNIQUE KEY `member_id` (`member_id`,`tracking_date`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `routines`
--
ALTER TABLE `routines`
  ADD PRIMARY KEY (`routine_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`trainer_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `workout_content`
--
ALTER TABLE `workout_content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `yoga_sessions`
--
ALTER TABLE `yoga_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `routine_id` (`routine_id`),
  ADD KEY `idx_yoga_sessions_member_date` (`member_id`,`session_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `class_bookings`
--
ALTER TABLE `class_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `diet_plans`
--
ALTER TABLE `diet_plans`
  MODIFY `diet_plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `progress_tracking`
--
ALTER TABLE `progress_tracking`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `routines`
--
ALTER TABLE `routines`
  MODIFY `routine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `trainer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `workout_content`
--
ALTER TABLE `workout_content`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `yoga_sessions`
--
ALTER TABLE `yoga_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE CASCADE;

--
-- Constraints for table `class_bookings`
--
ALTER TABLE `class_bookings`
  ADD CONSTRAINT `class_bookings_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_bookings_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE;

--
-- Constraints for table `diet_plans`
--
ALTER TABLE `diet_plans`
  ADD CONSTRAINT `diet_plans_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `diet_plans_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE SET NULL;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `fk_member_trainer` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `progress_tracking`
--
ALTER TABLE `progress_tracking`
  ADD CONSTRAINT `progress_tracking_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE CASCADE;

--
-- Constraints for table `routines`
--
ALTER TABLE `routines`
  ADD CONSTRAINT `routines_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `routines_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE CASCADE;

--
-- Constraints for table `trainers`
--
ALTER TABLE `trainers`
  ADD CONSTRAINT `trainers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_content`
--
ALTER TABLE `workout_content`
  ADD CONSTRAINT `workout_content_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE CASCADE;

--
-- Constraints for table `yoga_sessions`
--
ALTER TABLE `yoga_sessions`
  ADD CONSTRAINT `yoga_sessions_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `yoga_sessions_ibfk_2` FOREIGN KEY (`routine_id`) REFERENCES `routines` (`routine_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
