-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 05, 2024 at 07:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mdpj_user`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_feedback`
--

CREATE TABLE `user_feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `line_user_id` varchar(255) DEFAULT NULL,
  `display_name` varchar(100) NOT NULL,
  `picture_url` varchar(100) DEFAULT NULL,
  `design_appeal` int(1) NOT NULL,
  `ease_of_use` int(1) NOT NULL,
  `user_feedback_experience` text NOT NULL,
  `notification_accuracy` int(1) NOT NULL,
  `feature_functionality` int(1) NOT NULL,
  `system_reliability` int(1) NOT NULL,
  `user_manual_completeness` int(1) NOT NULL,
  `page_load_speed` int(1) NOT NULL,
  `server_responsiveness` int(1) NOT NULL,
  `server_memory_management` int(1) NOT NULL,
  `ocr_processing_speed` int(1) NOT NULL,
  `navigation_ease` int(1) NOT NULL,
  `user_friendly_interface` int(1) NOT NULL,
  `responsive_design` int(1) NOT NULL,
  `accessibility` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `evaluated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_feedback`
--

INSERT INTO `user_feedback` (`id`, `user_id`, `line_user_id`, `display_name`, `picture_url`, `design_appeal`, `ease_of_use`, `user_feedback_experience`, `notification_accuracy`, `feature_functionality`, `system_reliability`, `user_manual_completeness`, `page_load_speed`, `server_responsiveness`, `server_memory_management`, `ocr_processing_speed`, `navigation_ease`, `user_friendly_interface`, `responsive_design`, `accessibility`, `created_at`, `evaluated_date`) VALUES
(1, 19, 'U92e8a6ba279132dfccb4a176a794823a', 'nextgen.f_m', NULL, 2, 2, 'Test ทดสอบ 1 2 3 4 5 6', 5, 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-08-05 04:28:29', '2024-08-05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user_feedback`
--
ALTER TABLE `user_feedback`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_feedback`
--
ALTER TABLE `user_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
