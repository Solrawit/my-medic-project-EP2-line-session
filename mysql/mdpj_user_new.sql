-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2024 at 01:32 AM
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
(2, 'วิธีการ'),
(3, 'HOW TO'),
(4, 'MECOBALAMIN');

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
(19, 'U92e8a6ba279132dfccb4a176a794823a', 'nextgen.f_m', 'https://profile.line-scdn.net/0hBVjxsz-ZHXlHAQ11kCFjBjdRHhNkcERraGFWHHYJREopYgorbDAFTSEISk56Mg4oO2AGGyAJQEFLEmofWVfhTUAxQEh7NFouaWdSlg', '', '2024-07-02 01:32:01', 'admin', NULL, NULL, 'MECOBALAMIN\n\nช่วงเวลา: เช้า\nรับประทาน: ก่อนอาหาร\nครั้งละ: 1', 'uploads/431425293_434091139072579_5442343440107955759_n.jpg');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `line_user_id` (`line_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mdpj_user`
--
ALTER TABLE `mdpj_user`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ไอดีผู้ใช้', AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `medicine`
--
ALTER TABLE `medicine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=235;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
