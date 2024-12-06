-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2024 at 03:31 PM
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
-- Database: `impoundment`
--

-- --------------------------------------------------------

--
-- Table structure for table `impound_records`
--

CREATE TABLE `impound_records` (
  `record_id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `rider_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `violation_id` int(11) DEFAULT NULL,
  `officer_id` int(11) DEFAULT NULL,
  `impound_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `release_date` timestamp NULL DEFAULT NULL,
  `status` enum('impounded','released') DEFAULT 'impounded'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `impound_records`
--

INSERT INTO `impound_records` (`record_id`, `vehicle_id`, `rider_id`, `owner_id`, `violation_id`, `officer_id`, `impound_date`, `release_date`, `status`) VALUES
(1, 1, 1, 1, 1, 1, '2024-11-14 09:45:08', '2024-11-14 03:54:59', 'released'),
(2, 2, 2, 2, 2, 16, '2024-11-14 11:51:57', '2024-11-18 20:43:16', 'released'),
(3, 3, 3, 3, 3, 1, '2024-11-14 12:15:54', NULL, 'impounded'),
(4, 5, 5, 5, 1, 16, '2024-11-15 15:26:30', NULL, 'impounded'),
(5, 5, 5, 5, 2, 16, '2024-11-15 15:26:30', NULL, 'impounded'),
(6, 6, 6, 6, 5, 16, '2024-11-15 15:33:24', NULL, 'impounded'),
(7, 8, 7, 7, 6, 1, '2024-11-19 03:23:33', NULL, 'impounded'),
(8, 9, 8, 8, 7, 1, '2024-11-19 05:10:39', NULL, 'impounded'),
(9, 10, 9, 9, 8, 1, '2024-11-19 13:19:07', NULL, 'impounded'),
(10, 11, 10, 10, 9, 1, '2024-11-21 10:03:28', NULL, 'impounded'),
(11, 12, 11, 11, 10, 1, '2024-11-21 10:31:44', NULL, 'impounded'),
(12, 13, 12, 12, 11, 1, '2024-11-21 10:39:53', NULL, 'impounded'),
(13, 14, 13, 13, 12, 1, '2024-11-21 10:46:29', NULL, 'impounded'),
(14, 16, 15, 15, 13, 1, '2024-11-21 11:01:31', NULL, 'impounded'),
(15, 17, 16, 16, 14, 1, '2024-11-21 11:02:36', NULL, 'impounded'),
(16, 18, 17, 17, 15, 16, '2024-11-21 13:54:32', NULL, 'impounded'),
(17, 19, 18, 18, 16, 16, '2024-11-21 13:55:01', NULL, 'impounded'),
(18, 20, 19, 19, 17, 1, '2024-11-23 08:15:59', '2024-11-23 01:17:11', 'released'),
(19, 21, 20, 20, 18, 1, '2024-11-26 14:28:40', '2024-11-26 07:29:25', 'released');

-- --------------------------------------------------------

--
-- Table structure for table `officers`
--

CREATE TABLE `officers` (
  `officer_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','officer') DEFAULT 'officer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officers`
--

INSERT INTO `officers` (`officer_id`, `first_name`, `last_name`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Mark Allan', 'Sagana', 'admin', '$2y$10$06rC8qmPfGY6.HRznw0Z0ejmmTmS0PAbC6NVEcqUcljivRB7iZEXu', 'admin', '2024-11-14 09:30:32'),
(16, 'Stephanie', 'Valenciano', 'officer1', '$2y$10$d6Tkm/Skho5XjZpdnd6adOtJSIo35oLVHwWwlPcMaa3uJwYPCQVdq', 'officer', '2024-11-14 11:42:17'),
(18, 'John Jezter', 'Estacio', 'OfficerEstacio', '$2y$10$/U4Og82j5qoMQAk1nRs3IeXAUR7H3gTDR1PrGmzm6EsBCveghIH6e', 'officer', '2024-11-16 05:33:55');

-- --------------------------------------------------------

--
-- Table structure for table `registered_owners`
--

CREATE TABLE `registered_owners` (
  `owner_id` int(11) NOT NULL,
  `owner_first_name` varchar(50) NOT NULL,
  `owner_middle_name` varchar(50) DEFAULT NULL,
  `owner_last_name` varchar(50) NOT NULL,
  `owner_age` int(11) DEFAULT NULL,
  `owner_gender` enum('male','female') DEFAULT NULL,
  `owner_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registered_owners`
--

INSERT INTO `registered_owners` (`owner_id`, `owner_first_name`, `owner_middle_name`, `owner_last_name`, `owner_age`, `owner_gender`, `owner_address`, `created_at`) VALUES
(1, 'Mark Allan', 'Gavino', 'Sagana', 24, 'male', 'Mambangnan, San Leonardo', '2024-11-14 09:45:08'),
(2, 'Mark Allan', 'Gavino', 'Sagana', 24, 'male', 'Mambangnan, San Leonardo', '2024-11-14 11:51:57'),
(3, 'Ariel', 'Tide', 'Surf', 23, 'male', 'Nieves, San Leonardo', '2024-11-14 12:15:54'),
(4, 'Romeo', 'Malaca', 'Gavino', 64, 'male', 'Mambangnan, San Leonardo', '2024-11-15 15:22:22'),
(5, 'Romnick', 'Malaca', 'Gavino', 48, 'male', 'Mambangnan, San Leonardo', '2024-11-15 15:26:30'),
(6, 'John', 'Jezter', 'Estacio', 23, 'male', 'Bulacan', '2024-11-15 15:33:24'),
(7, 'Mark Allan', 'Gavino', 'Sagana', 24, 'male', 'Mambangnan, San Leonardo', '2024-11-19 03:23:33'),
(8, 'Bryan', 'Happy', 'Cumbe', 23, 'male', 'Mambangnan', '2024-11-19 05:10:39'),
(9, 'John Jezter', 'Torres', 'Estacio', 23, 'male', 'Bulacan', '2024-11-19 13:19:07'),
(10, 'RUSSEL', 'JOHN', 'GERONIMO', 22, 'male', 'NIEVES, SAN LEONARDO', '2024-11-21 10:03:28'),
(11, 'qweqw', 'e123qwe', '12eqweqw', 23, 'male', 'qweqwe123123', '2024-11-21 10:31:44'),
(12, 'ghj', 'ghj', 'gj', 54, 'male', 'ghj', '2024-11-21 10:39:53'),
(13, 'klkl', 'klkl', 'klkl', 23, 'male', 'klkl', '2024-11-21 10:46:29'),
(14, 'zxc', 'zxc', 'zxc', 238, 'male', 'qwe', '2024-11-21 10:50:48'),
(15, 'hkhj', 'hjkj', 'khhk', 63, 'male', 'weerw', '2024-11-21 11:01:31'),
(16, 'flhlhf', '', 'fhlhf', 45, 'male', 'hjkkj', '2024-11-21 11:02:36'),
(17, 'ariel', '', 'ariel', 25, 'male', 'dfhggfdas', '2024-11-21 13:54:32'),
(18, 'qxe', 'xq', 'qx', 213, 'male', 'asd', '2024-11-21 13:55:01'),
(19, 'John', 'Jezter', 'Estacio', 24, 'male', 'Bulacan', '2024-11-23 08:15:59'),
(20, 'Mang', '', 'Kanor', 54, 'male', 'Mallorca, San Leonardo', '2024-11-26 14:28:40');

-- --------------------------------------------------------

--
-- Table structure for table `riders`
--

CREATE TABLE `riders` (
  `rider_id` int(11) NOT NULL,
  `rider_first_name` varchar(50) NOT NULL,
  `rider_middle_name` varchar(50) DEFAULT NULL,
  `rider_last_name` varchar(50) NOT NULL,
  `rider_age` int(11) DEFAULT NULL,
  `rider_gender` enum('male','female') DEFAULT NULL,
  `rider_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `license_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riders`
--

INSERT INTO `riders` (`rider_id`, `rider_first_name`, `rider_middle_name`, `rider_last_name`, `rider_age`, `rider_gender`, `rider_address`, `created_at`, `license_number`) VALUES
(1, 'Marc Angelo', 'Gavino', 'Sagana', 22, 'male', 'Mambangnan, San Leonardo', '2024-11-14 09:45:08', ''),
(2, 'Mark Allan', 'Gavino', 'Sagana', 24, 'male', 'Mambangnan, San Leonardo', '2024-11-14 11:51:57', ''),
(3, 'Ariel', 'Tide', 'Surf', 23, 'male', 'Nieves, San Leonardo', '2024-11-14 12:15:54', ''),
(4, 'Romeo', 'Malaca', 'Gavino', 64, 'male', 'Mambangnan, San Leonardo', '2024-11-15 15:22:22', ''),
(5, 'Romnick', 'Malaca', 'Gavino', 48, 'male', 'Mambangnan, San Leonardo', '2024-11-15 15:26:30', ''),
(6, 'John', 'Jezter', 'Estacio', 23, 'male', 'Bulacan', '2024-11-15 15:33:24', ''),
(7, 'Mark Allan', 'Gavino', 'Sagana', 24, 'male', 'Mambangnan, San Leonardo', '2024-11-19 03:23:33', ''),
(8, 'Bryan', 'Happy', 'Cumbe', 23, 'male', 'Mambangnan', '2024-11-19 05:10:39', ''),
(9, 'John Jezter', 'Torres', 'Estacio', 23, 'male', 'Bulacan', '2024-11-19 13:19:07', ''),
(10, 'RUSSEL', 'JOHN', 'GERONIMO', 22, 'male', 'NIEVES, SAN LEONARDO', '2024-11-21 10:03:28', ''),
(11, 'qweqw', 'e123qwe', '12eqweqw', 23, 'male', 'qweqwe123123', '2024-11-21 10:31:44', ''),
(12, 'ghj', 'ghj', 'gj', 54, 'male', 'ghj', '2024-11-21 10:39:53', ''),
(13, 'klkl', 'klkl', 'klkl', 23, 'male', 'klkl', '2024-11-21 10:46:29', ''),
(14, 'zxc', 'zxc', 'zxc', 238, 'male', 'qwe', '2024-11-21 10:50:48', ''),
(15, 'hkhj', 'hjkj', 'khhk', 63, 'male', 'weerw', '2024-11-21 11:01:31', ''),
(16, 'flhlhf', '', 'fhlhf', 45, 'male', 'hjkkj', '2024-11-21 11:02:36', ''),
(17, 'ariel', '', 'ariel', 25, 'male', 'dfhggfdas', '2024-11-21 13:54:32', ''),
(18, 'qxe', 'xq', 'qx', 213, 'male', 'asd', '2024-11-21 13:55:01', ''),
(19, 'John', 'Jezter', 'Estacio', 24, 'male', 'Bulacan', '2024-11-23 08:15:59', '31g23g'),
(20, 'Mang', '', 'Kanor', 54, 'male', 'Mallorca, San Leonardo', '2024-11-26 14:28:40', '');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `color` varchar(30) DEFAULT NULL,
  `orcr_number` varchar(50) DEFAULT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `chassis_number` varchar(50) DEFAULT NULL,
  `engine_number` varchar(50) DEFAULT NULL,
  `plate_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `color`, `orcr_number`, `vehicle_type`, `chassis_number`, `engine_number`, `plate_number`, `created_at`) VALUES
(1, 'BLUE', 'ABC11111', 'YAMAHA/NMAX 155', 'QWE12313123123123123', '123QWE1231', 'ABC1234', '2024-11-14 09:45:08'),
(2, 'White', 'ASD123123', 'Suzuki/Raider 150 FI', 'DIR123123', '1231432213', 'PUP4352', '2024-11-14 11:51:57'),
(3, 'Black', '1123qw123123', 'Honda/PCX 160', '123asd123123123123', '123asdqwe1', 'OKL2390', '2024-11-14 12:15:54'),
(4, 'Yellow', '99023480923490234823', 'Honda/Click 125i', '23468972348792348972', '0982349823', 'JLK8872', '2024-11-15 15:22:22'),
(5, 'Green', '23462387423787832467', 'Honda', '87192378912783128793', '1829387137', 'RED1231', '2024-11-15 15:26:30'),
(6, 'Red', '871831287931183279', 'Yamaha', '1261262738127821738', '9812983127', 'GFG9292', '2024-11-15 15:33:24'),
(7, 'Pink', '12312312312312312312', 'Honda CBR 150', '12312312312312312313', '4534543234', 'PUP4567', '2024-11-19 03:21:15'),
(8, 'Pink', 'ioausdfiu7887asd67as', 'Honda CBR 150', '12312312312312312313', '4534543234', 'JKL878', '2024-11-19 03:23:33'),
(9, 'Pink', '78asd78as78dsasdsada', 'Suzuki', 'Aasdas8876asd6asd78a', '8asd78as7a', '78ad67as', '2024-11-19 05:10:39'),
(10, 'Green', 'asda', 'QJmotor Fortres 160', 'gasdkaksjdj12hj312hj', 'kasdkhas78', 'asdasd', '2024-11-19 13:19:07'),
(11, 'YELLOW', '', 'YAMAHA MIO I 125', 'YU22223121UI2121UI', '12U31YI1I1', '', '2024-11-21 10:03:28'),
(12, 'Black', '123', 'qweqwe', 'qwe12312e', 'qe12eqweqw', '123', '2024-11-21 10:31:44'),
(13, '123tyutyuty', '', 'fghfghfgh', 'fghfgh', 'jghjghj', '', '2024-11-21 10:39:53'),
(14, 'klklk', '', 'klkl', 'klkl', 'klkl', '', '2024-11-21 10:46:29'),
(15, 'zxc', '', 'zzxc', 'zxc', 'zc', '', '2024-11-21 10:50:48'),
(16, 'hjkjhk', '', 'hjkhjk', 'hkhj', 'hjkhjk', '', '2024-11-21 11:01:31'),
(17, 'hlffll', '', 'flflhl', 'fhlhlf', 'flhflh', '', '2024-11-21 11:02:36'),
(18, 'pink', '', 'rusi', '1fc33f122', 'f12312', '', '2024-11-21 13:54:32'),
(19, 'qwqwe', '', 'xq e', 'dqwe', 'qwexd', '', '2024-11-21 13:55:01'),
(20, 'Matte Red', '', 'Suzuki Raider 150', '23423wre32432234', 'wer3242342', '', '2024-11-23 08:15:59'),
(21, 'Red', '', 'Honda', '2j312hj1k3j', '13h1h23j1k', '', '2024-11-26 14:28:40');

-- --------------------------------------------------------

--
-- Table structure for table `violations`
--

CREATE TABLE `violations` (
  `violation_id` int(11) NOT NULL,
  `violations_type` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `violations`
--

INSERT INTO `violations` (`violation_id`, `violations_type`, `created_at`) VALUES
(1, 'No Helmet', '2024-11-14 09:45:08'),
(2, 'No License', '2024-11-14 11:51:57'),
(3, 'No Carried CR/OR', '2024-11-14 12:15:54'),
(4, 'Modified Muffler', '2024-11-15 15:15:14'),
(5, 'Fake License', '2024-11-15 15:33:24'),
(6, 'Fake License', '2024-11-19 03:23:33'),
(7, 'Fake License, No Helmet', '2024-11-19 05:10:39'),
(8, 'Driving without a license plate, No CR/OR Carried', '2024-11-19 13:19:07'),
(9, 'N', '2024-11-21 10:03:28'),
(10, 'NOT CARRIED CR/OR', '2024-11-21 10:31:44'),
(11, 'okay', '2024-11-21 10:39:53'),
(12, 'N', '2024-11-21 10:46:29'),
(13, 'No Helmet, No License, Reckless Driving', '2024-11-21 11:01:31'),
(14, 'No Helmet, No License, Reckless Driving, Fake License', '2024-11-21 11:02:36'),
(15, 'No Drivers License', '2024-11-21 13:54:32'),
(16, 'No Drivers License', '2024-11-21 13:55:01'),
(17, 'Not Carried OR/CR, No Plate Number, Fake License', '2024-11-23 08:15:59'),
(18, 'No Drivers License, Failed to show ORCR, No Plate Number', '2024-11-26 14:28:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `impound_records`
--
ALTER TABLE `impound_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `rider_id` (`rider_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `violation_id` (`violation_id`),
  ADD KEY `officer_id` (`officer_id`);

--
-- Indexes for table `officers`
--
ALTER TABLE `officers`
  ADD PRIMARY KEY (`officer_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `registered_owners`
--
ALTER TABLE `registered_owners`
  ADD PRIMARY KEY (`owner_id`);

--
-- Indexes for table `riders`
--
ALTER TABLE `riders`
  ADD PRIMARY KEY (`rider_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicle_id`);

--
-- Indexes for table `violations`
--
ALTER TABLE `violations`
  ADD PRIMARY KEY (`violation_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `impound_records`
--
ALTER TABLE `impound_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `officers`
--
ALTER TABLE `officers`
  MODIFY `officer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `registered_owners`
--
ALTER TABLE `registered_owners`
  MODIFY `owner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `riders`
--
ALTER TABLE `riders`
  MODIFY `rider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `violations`
--
ALTER TABLE `violations`
  MODIFY `violation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `impound_records`
--
ALTER TABLE `impound_records`
  ADD CONSTRAINT `impound_records_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `impound_records_ibfk_2` FOREIGN KEY (`rider_id`) REFERENCES `riders` (`rider_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `impound_records_ibfk_3` FOREIGN KEY (`owner_id`) REFERENCES `registered_owners` (`owner_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `impound_records_ibfk_4` FOREIGN KEY (`violation_id`) REFERENCES `violations` (`violation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `impound_records_ibfk_5` FOREIGN KEY (`officer_id`) REFERENCES `officers` (`officer_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
