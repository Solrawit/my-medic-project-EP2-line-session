-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2024 at 09:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `line_user_id` varchar(255) DEFAULT NULL,
  `display_name` varchar(100) NOT NULL,
  `smoothness` int(1) NOT NULL,
  `stability_website` int(1) NOT NULL,
  `stability_system` int(1) NOT NULL,
  `ease_of_use` int(1) NOT NULL,
  `complexity` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `evaluated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `line_user_id`, `display_name`, `smoothness`, `stability_website`, `stability_system`, `ease_of_use`, `complexity`, `created_at`, `evaluated_date`) VALUES
(57, 19, 'U92e8a6ba279132dfccb4a176a794823a', 'nextgen.f_m', 1, 1, 1, 1, 1, '2024-07-11 13:23:47', NULL),
(66, 392, 'U067e5e3743b91e3edd12953d8ab2bb9b', 'n', 1, 1, 1, 1, 1, '2024-07-11 18:56:22', '2024-07-12'),
(67, 391, 'Udebdfc26aa20eecac641fb61be7ad145', 'Bxby_', 1, 1, 1, 1, 1, '2024-07-11 19:02:00', '2024-07-12'),
(69, 19, 'U56585d9c6e46d4d1d34e7cbc5f7c5ac0', 'mon(เลิฟเอง)', 1, 1, 1, 1, 1, '2024-07-11 19:13:47', '2024-07-12');

-- --------------------------------------------------------

--
-- Table structure for table `mdpj_user`
--

CREATE TABLE `mdpj_user` (
  `user_id` int(10) NOT NULL COMMENT 'ไอดีผู้ใช้',
  `user_username` varchar(30) NOT NULL COMMENT 'ชื่อผู้ใช้',
  `user_password` varchar(50) NOT NULL COMMENT 'รหัสผ่าน',
  `user_name` varchar(60) NOT NULL COMMENT 'ชื่อ',
  `user_surname` varchar(60) NOT NULL COMMENT 'นามสกุล',
  `user_sex` enum('ชาย','หญิง') NOT NULL COMMENT 'เพศ',
  `user_email` varchar(100) DEFAULT NULL COMMENT 'อีเมล์',
  `user_level` enum('member','admin') NOT NULL DEFAULT 'member' COMMENT 'ระดับผู้ใช้',
  `alert_time` time DEFAULT NULL COMMENT 'เวลาแจ้งเตือน'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `mdpj_user`
--

INSERT INTO `mdpj_user` (`user_id`, `user_username`, `user_password`, `user_name`, `user_surname`, `user_sex`, `user_email`, `user_level`, `alert_time`) VALUES
(1, 'admin', 'cdc1a44d6977019f2183492370fc49f7', 'สรวิชญ์', 'ช่วงชู', 'ชาย', '164405241019-st@rmutsb.ac.th', 'admin', '18:21:00'),
(2, 'admin123', 'cdc1a44d6977019f2183492370fc49f7', 'Solrawit', 'Chungchoo', 'ชาย', 'fewfew@gmail.com', 'member', NULL),
(3, 'solrawit', 'cdc1a44d6977019f2183492370fc49f7', 'test', 'test', 'หญิง', 'fewfew@gmail.com', 'member', NULL),
(4, 'member', 'cdc1a44d6977019f2183492370fc49f7', 'fewfew', 'gregre', 'ชาย', 'fewfew@gmail.com', 'member', NULL),
(20, 'testtest', '05a671c66aefea124cc08b76ea6d30bb', 'test', 'test', 'ชาย', 'testtest@gmail.com', 'member', NULL),
(21, 'member123', 'cdc1a44d6977019f2183492370fc49f7', 'member', 'member', 'ชาย', 'fewfewfew@gmail.com', 'member', NULL),
(22, '164405241019', 'c0f29a68fb4b729d1f52a89f46f25381', 'Flyaway', 'Omg', 'ชาย', 'fdsaxsc@gmail.com', 'member', NULL),
(23, 'itest', '80795e1b17f053032770c0eea9839d91', 'itest', 'zaza', 'หญิง', '8198fewf@gmail.com', 'member', NULL),
(24, 'ttt', 'cdc1a44d6977019f2183492370fc49f7', 'ดำไดำไ', 'ดำไดำไ', 'ชาย', 'ttttt@gmail.com', 'member', NULL),
(25, '123', '250cf8b51c773f3f8dc8b4be867a9a02', '123', '456', 'ชาย', '123456@gmail.com', 'member', NULL),
(26, 'yyy', 'f0a4058fd33489695d53df156b77c724', 'tot55810', 'hhhh', 'ชาย', 'feoiwfj@gmail.com', 'member', NULL),
(27, 'root', 'cdc1a44d6977019f2183492370fc49f7', 'sdq', 'dqwdw', 'ชาย', 'dqwdwq@gmail.com', 'member', NULL),
(28, 'lol55810', 'e10adc3949ba59abbe56e057f20f883e', 'Eiei', 'lnwza', 'ชาย', 'dwqdwq@gmail.com', 'member', NULL),
(29, 'test123', '202cb962ac59075b964b07152d234b70', '123', '123', 'ชาย', '123@gmail.com', 'member', NULL),
(30, 'test1234', '202cb962ac59075b964b07152d234b70', '123', '123', 'ชาย', '123@gmail.com', 'member', NULL),
(31, 'test12345', '202cb962ac59075b964b07152d234b70', '123', '123', 'ชาย', '123@gmail.com', 'member', NULL),
(33, 'test1234567', '202cb962ac59075b964b07152d234b70', '123', '123', 'ชาย', '123@gmail.com', 'member', NULL),
(34, 'test12346789789', '202cb962ac59075b964b07152d234b70', '123', '123', 'ชาย', '123@gmail.com', 'member', NULL),
(35, 'test12346789789123', '202cb962ac59075b964b07152d234b70', '123', '123', 'ชาย', '123@gmail.com', 'member', NULL),
(36, 'admin789456', 'cdc1a44d6977019f2183492370fc49f7', 'ดำไดำไ', 'ดำไดำไ', 'ชาย', '123456@gmail.com', 'member', NULL),
(37, 'admin78945656', '202cb962ac59075b964b07152d234b70', 'ดำไดำไ', 'ดำไดำไ', 'ชาย', '123456@gmail.com', 'member', NULL),
(38, 'admin78945656few', '202cb962ac59075b964b07152d234b70', 'ดำไดำไ', 'ดำไดำไ', 'ชาย', '123456@gmail.com', 'member', NULL),
(39, 'adminfewfewfew', 'cdc1a44d6977019f2183492370fc49f7', '123', '123', 'ชาย', 'feoiwfj@gmail.com', 'member', NULL),
(40, 'gregervfvx', 'cdc1a44d6977019f2183492370fc49f7', 'gergervcx', 'gergerfc', 'ชาย', 'dwqdwq@gmail.com', 'member', NULL),
(41, 'adminfewfewxc', 'cdc1a44d6977019f2183492370fc49f7', 'fewdd', 'sddd', 'ชาย', 'feoiwfj@gmail.com', 'member', NULL),
(42, '156156156', '035581cada24fa97d92e5311dd90d386', 'gfer', 'greger', 'ชาย', 'pasitza115@gmail.com', 'member', NULL),
(43, 'adminwewwww', '9b74c98f4fdde86dcf1ba9c4b5957d52', 'fewfwefew', 'fewfewfwefew', 'ชาย', 'fewfwefew@gmail.com', 'member', NULL),
(45, 'root123456', '25f9e794323b453885f5181f1b624d0b', 'Hisammex', 'GGmode', 'ชาย', '123@gmail.com', 'member', NULL),
(46, 'rock123', 'efe6398127928f1b2e9ef3207fb82663', 'rock', 'zaza', 'ชาย', 'rock123@gmail.com', 'member', NULL),
(47, 'uuuu55810', '7a84305be40b7a43ee5f45ca13a39b50', 'uuuu55810', 'uuuu55810', 'ชาย', 'uuuu55810@gmail.com', 'member', NULL),
(48, 'Zazaza123', '2200f19d2bba2567e3f242151f3c788e', 'องค์ชาย', 'ต้มยำกุ้ง', 'ชาย', 'Zazaza123@gmail.com', 'member', '18:23:00');

-- --------------------------------------------------------

--
-- Table structure for table `medicine`
--

CREATE TABLE `medicine` (
  `id` int(11) NOT NULL,
  `text_column` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine`
--

INSERT INTO `medicine` (`id`, `text_column`) VALUES
(1, 'Aspirin'),
(2, 'Asobrom'),
(3, 'METHOPINE'),
(4, 'MECOBALAMIN'),
(5, 'DIPHENHYDRAMINE'),
(6, 'SIMVASTATIN');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) NOT NULL,
  `site_nav` text NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `announce` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `site_nav`, `contact_email`, `announce`) VALUES
(1, 'Medicine Test', 'MEDICINE ALERT', 'medicinedev@gmail.com', 'HELLO WOLD PROJECT THIS IS ANNOUNCEMENT');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `line_user_id` varchar(100) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `picture_url` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `medicine_alert_time` time DEFAULT NULL,
  `medicine_alert_message` varchar(255) DEFAULT NULL,
  `ocr_scans_text` text DEFAULT NULL,
  `ocr_image_data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `line_user_id`, `display_name`, `picture_url`, `email`, `login_time`, `role`, `medicine_alert_time`, `medicine_alert_message`, `ocr_scans_text`, `ocr_image_data`) VALUES
(19, 'U92e8a6ba279132dfccb4a176a794823a', 'nextgen.f_m', 'https://profile.line-scdn.net/0hBVjxsz-ZHXlHAQ11kCFjBjdRHhNkcERraGFWHHYJREopYgorbDAFTSEISk56Mg4oO2AGGyAJQEFLEmofWVfhTUAxQEh7NFouaWdSlg', '', '2024-07-11 20:13:18', 'admin', NULL, NULL, 'MECOBALAMIN\n\nช่วงเวลา: เช้า\nรับประทาน: ก่อนอาหาร\nครั้งละ: 1', 'uploads/431425293_434091139072579_5442343440107955759_n.jpg'),
(235, 'Uebb754dfe410ae650fee5ea5808362d4', 'nextgen.f_m', 'https://profile.line-scdn.net/0hBVjx8CH3HXlHAQ11kCFjBjdRHhNkcERraGFWHHYJREopYgorbDAFTSEISk56Mg4oO2AGGyAJQEFLEmofWVfhTUAxQEh7NVwoaW5XnA', '', '2024-07-09 19:31:11', 'admin', NULL, NULL, 'DIPHENHYDRAMINE\n\nช่วงเวลา: เช้า\nรับประทาน: ก่อนอาหาร\nครั้งละ: 1', 'uploads/dip.jpg'),
(258, 'U25c8f1894a3ddd464d62202d4c2d93ac', 'กรกนก', 'https://profile.line-scdn.net/0hXR_71YMYB31yThQHXqF5AgIeBBdRP15vVy5NHEAdXxgYd0cvXSoaHBdKC0xOeEEvWHxLHEdGX09-XXAbbBj7SXV-WkxOekYsXCFNmA', '', '2024-07-09 10:29:26', 'user', NULL, NULL, 'MECOBALAMIN\n\nช่วงเวลา: เช้า\nรับประทาน: ก่อนอาหาร\nครั้งละ: 1', 'uploads/431425293_434091139072579_5442343440107955759_n.jpg'),
(286, 'Uec3fea76b7c5fd167f98856ef5c3369e', 'ɪ .', 'https://profile.line-scdn.net/0hxfAGsY-KJ0lsPjcCgvFZNhxuJCNPT35bRlhvKg03fn5QCjBIQV5oLlFpfC0DXjMXE1hhJgo6LnBgLVAvcmjbfWsOenhQCmYYQlFtrA', '', '2024-07-09 11:23:37', 'user', NULL, NULL, 'Aspirin\n\nช่วงเวลา: เย็น\nรับประทาน: หลังอาหาร\nครั้งละ: 3', 'uploads/432553736_1615834502546590_872099908763560096_n.jpg'),
(290, 'U25e18b05603a6f70fbad010e73c03605', 'ซาซาเกโย', 'https://profile.line-scdn.net/0h-eEQVEKrckpoKGeRPAcMNRh4cSBLWStYQ09ue1t4f3gFGDVMFEZtKgoheHwGTTYYRE8-KF4pfilkOwUsdn6Ofm8YL3tUHDMbRkc4rw', '', '2024-07-09 11:29:54', 'user', NULL, NULL, '\nช่วงเวลา: เช้า\nรับประทาน: ก่อนอาหาร\nครั้งละ: 1', 'uploads/IMG_20240516_160259.jpg'),
(362, 'U6edf06df8b95e70d8ca4206e665ba91d', 'กรกนก', 'https://profile.line-scdn.net/0hXR_76UjLB31yThQHXqF5AgIeBBdRP15vVy5NHEAdXxgYd0cvXSoaHBdKC0xOeEEvWHxLHEdGX09-XXAbbBj7SXV-WkxOe0AqXChIkg', '', '2024-07-10 14:09:31', 'user', NULL, NULL, NULL, NULL),
(390, 'U265280279c9f0be2c875596e94e6691a', 'P', 'https://profile.line-scdn.net/0hI7JMbf_5FhhnNAXoCbVoZxdkFXJERU8KGAcNK1VhSS0OAlMZQwFZLVIyQHpZUAFIGAFQdgZhGHxrJ2F-eWLqLGAESylbAVFPSVJZ9w', '', '2024-07-11 19:10:19', 'user', NULL, NULL, NULL, NULL),
(391, 'Udebdfc26aa20eecac641fb61be7ad145', 'Bxby_', 'https://profile.line-scdn.net/0h2QD0OwdkbX5pSX7HnLcTARkZbhRKODRsTCcrSl5NM01QKn8vQiwrHFUdMkldfC59EigiSl4ZMkdlWhoYdx-RSm55ME9VfCopRy8ikQ', '', '2024-07-11 20:58:04', 'user', NULL, NULL, NULL, NULL),
(392, 'U067e5e3743b91e3edd12953d8ab2bb9b', 'n', 'https://profile.line-scdn.net/0h286Uvrl5bRcbLH0M748TaGt8bn04XTQFZ01ydiwkOyQgGXgTPx1yeS0oMyJxHipIMkoleSooMHcXPxpxBXqRIxwcMCYnGSpANUoi-A', '', '2024-07-11 20:25:07', 'user', NULL, NULL, NULL, NULL),
(394, 'U207f8a49bcae269b82c6346eadaa5729', 'ᴍɪʟᴅᴛʜɪ♡ツ', 'https://profile.line-scdn.net/0hP4j2JpPaDxtHSx3mlrBxZDcbDHFkOlYJbn9Fe3BJAyt9KEkaOyxEf3ZCVH5ycxoYbCkTLSVDBSNLWHh9WR3zL0B7Uip7fkhMaS1A9A', '', '2024-07-11 21:18:10', 'user', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `mdpj_user`
--
ALTER TABLE `mdpj_user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `medicine`
--
ALTER TABLE `medicine`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `line_user_id` (`line_user_id`),
  ADD UNIQUE KEY `line_user_id_unique` (`line_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `mdpj_user`
--
ALTER TABLE `mdpj_user`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ไอดีผู้ใช้', AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `medicine`
--
ALTER TABLE `medicine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=395;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
