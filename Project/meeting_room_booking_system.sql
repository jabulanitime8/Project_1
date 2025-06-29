-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2025 at 05:52 PM
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
-- Database: `meeting_room_booking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `officer_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` enum('pending','approved','rejected','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `room_id`, `user_id`, `officer_id`, `title`, `description`, `start_time`, `end_time`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 1, 'บรรยายธรรม', '', '2025-07-01 13:00:00', '2025-07-01 13:30:00', 'pending', '2025-06-29 15:27:53', '2025-06-29 15:27:53'),
(2, 1, 1, 1, 'สหกรญ์ออมทรัพย์', '', '2025-07-01 08:00:00', '2025-07-01 12:30:00', 'pending', '2025-06-29 15:46:01', '2025-06-29 15:46:01');

-- --------------------------------------------------------

--
-- Table structure for table `officers`
--

CREATE TABLE `officers` (
  `id` int(11) NOT NULL,
  `officer_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officers`
--

INSERT INTO `officers` (`id`, `officer_name`, `email`, `phone`, `department`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'จ.อ.พีรวิชญ์ บับภีร์', 'pirawit07@gmail.com', '0650206787', 'สื่อสารและอิเล็กทรอนิกส์', 1, '2025-06-29 11:51:51', '2025-06-29 11:52:39');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 10,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_name`, `capacity`, `description`, `location`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ห้องประชุมปิยามุมัง', 50, 'จอทีวี, ระบบเสียงครบวงจร, รองรับ video conference', 'ชั้น 4 บก.นทพ.', 1, '2025-06-29 11:51:35', '2025-06-29 11:51:35'),
(2, 'ห้องประชุมห้วยทราย', 30, 'โปรเจคเตอร์, ระบบเสียงครบวงจร, รองรับ video conference', 'ชั้น 2 บก.นทพ.', 1, '2025-06-29 11:51:35', '2025-06-29 11:51:35'),
(3, 'ห้องประชุมส่วนสั่งการ', 20, 'จอทีวี, ระบบเสียงครบวงจร, รองรับ video conference', 'ชั้น 5 บก.นทพ.', 1, '2025-06-29 11:51:35', '2025-06-29 11:51:35'),
(4, 'ห้องประชุมห้วยฮ่องไคร้', 20, 'โปรเจคเตอร์, ระบบเสียงครบวงจร', 'ชั้น 4 บก.นทพ.', 1, '2025-06-29 11:51:35', '2025-06-29 11:51:35'),
(5, 'หอประชุมมหาศรานนท์', 200, 'จอLED, ระบบเสียงครบวงจร, รองรับ video conference', 'หอประชุมมหาศรานนท์', 1, '2025-06-29 11:51:35', '2025-06-29 11:51:35'),
(6, 'อาคารเอนกประสงค์', 300, 'จอLED, ระบบเสียงครบวงจร, รองรับ video conference', 'อาคารเอนกประสงค์', 1, '2025-06-29 11:51:35', '2025-06-29 11:51:35'),
(7, 'แหล่งชุมนุมเจริญศิริ', 30, 'โปรเจคเตอร์, ระบบเสียงครบวงจร', 'แหล่งชุมนุมเจริญศิริ', 1, '2025-06-29 11:51:35', '2025-06-29 11:51:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `created_at`) VALUES
(1, 'pirawit.b', '$2y$10$cfH/M973zjXuiAxHBmQMluY6LGnpm9q9Vg3AMBya9dM5dh.pG6w8u', 'pirawit07@gmail.com', '2025-06-29 11:55:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `officer_id` (`officer_id`);

--
-- Indexes for table `officers`
--
ALTER TABLE `officers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_name` (`room_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `officers`
--
ALTER TABLE `officers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`officer_id`) REFERENCES `officers` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
