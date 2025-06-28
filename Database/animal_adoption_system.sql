-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2025 at 12:20 PM
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
-- Database: `animal_adoption_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `adoption_applications`
--

CREATE TABLE `adoption_applications` (
  `id` int(11) NOT NULL,
  `adopter_id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `application_date` date DEFAULT curdate(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adoption_applications`
--

INSERT INTO `adoption_applications` (`id`, `adopter_id`, `pet_id`, `application_date`, `status`) VALUES
(4, 1, 7, '2025-05-24', 'approved'),
(5, 1, 2, '2025-05-24', 'rejected'),
(6, 1, 6, '2025-05-29', 'approved'),
(7, 1, 3, '2025-05-29', 'approved'),
(10, 1, 4, '2025-05-29', 'approved'),
(12, 1, 1, '2025-06-21', 'approved'),
(14, 1, 10, '2025-06-23', 'approved'),
(16, 1, 9, '2025-06-24', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `follow_ups`
--

CREATE TABLE `follow_ups` (
  `id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `adopter_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `follow_ups`
--

INSERT INTO `follow_ups` (`id`, `pet_id`, `adopter_id`, `message`, `sent_at`) VALUES
(1, 7, 1, 'Hi, how\'s it going with your new pet?\r\n', '2025-05-28 17:40:55');

-- --------------------------------------------------------

--
-- Table structure for table `interviews`
--

CREATE TABLE `interviews` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `adopter_id` int(11) NOT NULL,
  `shelter_id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `interview_datetime` datetime NOT NULL,
  `status` enum('pending','confirmed','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interviews`
--

INSERT INTO `interviews` (`id`, `application_id`, `adopter_id`, `shelter_id`, `pet_id`, `interview_datetime`, `status`, `created_at`) VALUES
(1, 6, 1, 2, 6, '2025-05-31 02:26:00', 'confirmed', '2025-05-28 18:27:00'),
(2, 7, 1, 2, 3, '2025-05-31 02:40:00', 'confirmed', '2025-05-28 18:40:45'),
(3, 4, 1, 2, 7, '2025-05-30 02:42:00', 'confirmed', '2025-05-28 18:42:51'),
(4, 10, 1, 2, 4, '2025-05-30 10:15:00', 'confirmed', '2025-05-29 02:15:13'),
(5, 12, 1, 2, 1, '2025-06-12 22:05:00', 'pending', '2025-06-22 14:05:05'),
(6, 14, 1, 2, 10, '2025-06-25 11:26:00', 'confirmed', '2025-06-23 03:26:40');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('adopter','shelter') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `role`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 'shelter', 'New interview scheduled for pet: Cacaw', 1, '2025-05-28 18:27:00'),
(2, 1, 'adopter', 'Your interview for pet \'Cacaw\' was confirmed.', 1, '2025-05-28 18:27:23'),
(3, 2, 'shelter', 'New interview scheduled for pet: Goldie', 1, '2025-05-28 18:40:45'),
(4, 1, 'adopter', 'Your interview for pet \'Goldie\' was confirmed.', 1, '2025-05-28 18:41:19'),
(5, 2, 'shelter', 'New interview scheduled for pet: Beardie', 1, '2025-05-28 18:42:51'),
(6, 2, 'shelter', 'New interview scheduled for pet: Siam', 1, '2025-05-29 02:15:13'),
(7, 1, 'adopter', 'Your interview for pet \'Siam\' was confirmed.', 1, '2025-05-29 02:16:50'),
(8, 2, 'shelter', 'New interview scheduled for pet: Skie', 1, '2025-06-22 14:05:05'),
(9, 1, 'adopter', 'Test', 1, '2025-06-22 14:26:51'),
(11, 1, 'adopter', 'Your interview for pet \"Beardie\" was confirmed.', 1, '2025-06-23 03:26:00'),
(12, 2, 'shelter', 'New interview scheduled for pet: Shortie', 1, '2025-06-23 03:26:40'),
(13, 1, 'adopter', 'Your interview for pet \"Shortie\" was confirmed.', 1, '2025-06-23 03:26:57'),
(14, 1, 'adopter', 'Test', 1, '2025-06-23 15:41:59'),
(15, 10, 'adopter', 'Test', 1, '2025-06-23 15:41:59');

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `species` varchar(100) DEFAULT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('available','adopted') DEFAULT 'available',
  `shelter_id` int(11) DEFAULT NULL,
  `adoption_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `name`, `species`, `breed`, `age`, `gender`, `description`, `image`, `status`, `shelter_id`, `adoption_date`) VALUES
(1, 'Skie', 'Dog', 'Husky', 3, 'Male', 'He\'s a very good dog.', '1748013119_Husky.png', 'adopted', 2, '2025-06-21'),
(2, 'Persi', 'Cat', 'Persian', 2, 'Female', 'She\'s a very good cat :)', '1748013348_PersianCat.jpeg', 'available', 2, NULL),
(3, 'Goldie', 'Dog', 'Golden Retriever', 3, 'Male', 'He listens to his owner.\r\n', '1748013409_GoldenRetriever.jpg', 'adopted', 2, '2025-05-29'),
(4, 'Siam', 'Cat', 'Siames', 4, 'Male', 'Siamese cat up for adoption.', '1748013451_SiameseCat.jpeg', 'adopted', 2, '2025-05-29'),
(5, 'Sheppie', 'Dog', 'German Shepherd', 6, 'Female', 'German Shepherd up for adoption. Press apply now!', '1748013486_GermanShepherd.jpeg', 'adopted', 2, '2025-06-22'),
(6, 'Cacaw', 'Bird', 'Parrot', 1, 'Male', 'He\'s noisy sometimes.', '1748013554_PArrot.jpeg', 'adopted', 2, '2025-05-29'),
(7, 'Beardie', 'Reptile', 'Bearded Dragon', 1, 'Male', 'Adopt now!', '1748013592_BeardedDragon.jpeg', 'adopted', 2, '2025-05-29'),
(8, 'Hedgie', 'Small Mammal', 'Hedgehog', 1, 'Female', 'Adopt now!', '1748013617_Hedgehog.jpeg', 'available', 2, NULL),
(9, 'Sam', 'Exotic Pet', 'Sugar Glider', 1, 'Male', 'Adopt Now!', '1748013651_SugarGlider.jpeg', 'adopted', 2, '2025-06-24'),
(10, 'Shortie', 'Cat', 'British Short Hair', 2, 'Male', 'Cute cat up for adoption!', '1748454479_BSH.jpg', 'adopted', 2, '2025-06-23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone_number` varchar(12) DEFAULT NULL,
  `role` enum('adopter','shelter','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `phone_number`, `role`, `created_at`, `phone`, `status`) VALUES
(1, 'adopter01', 'adopter01@test.com', '$2y$10$OkXxdEQhsWCrq086GO0xT.pnTgCcDs15cEOwaIZqkTuf2/s3AgRJ.', 'adopter', '01', '0123456789', 'adopter', '2025-05-23 11:18:51', NULL, 'active'),
(2, 'shelter01', 'shelter01@test.com', '$2y$10$cUv.pG6Q24toB10.65Sn6.RLgmYKlNoLJUNTR2o1PGfAwkU18Y4sq', 'shelter', '01', '0123456780', 'shelter', '2025-05-23 11:20:29', NULL, 'active'),
(7, 'admin01', 'admin01@test.com', '$2y$10$9NYCOftrumDvzLUAL3D.4.RUPfWsdx1LSC8KfpDrimlvNJ7DvDhT6', 'admin', '01', NULL, 'admin', '2025-06-04 18:07:34', '01123452534', 'active'),
(9, 'Shelter02', 'shelter02@test.com', '$2y$10$Yr/GTN9TpaqVTryvA/6ghuEzo5THScvcrUHTHsgJgwbCJ7Sa3uBx2', 'Shelter', '02', NULL, 'shelter', '2025-06-22 14:13:22', '0123456789', 'active'),
(10, 'adopter03', 'adopter03@test.com', '$2y$10$bD3mnwhz370s9EZ8BIjOtOSoK8EtXbWdzlK.Hha2EETxoWHLw6.Ae', 'adopter', '03', NULL, 'adopter', '2025-06-23 07:41:29', '011234567890123', 'active'),
(11, 'adopter04', 'adopter04@test.com', '$2y$10$WR6R953yoKdpoKuq4NYAqudQxlo1x8BQeBE3wBVde4.i.KwDEaSvC', 'adopter', '04', NULL, 'adopter', '2025-06-23 07:44:03', '011234567890', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adoption_applications`
--
ALTER TABLE `adoption_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `adopter_id` (`adopter_id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- Indexes for table `follow_ups`
--
ALTER TABLE `follow_ups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `adopter_id` (`adopter_id`);

--
-- Indexes for table `interviews`
--
ALTER TABLE `interviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `adopter_id` (`adopter_id`),
  ADD KEY `shelter_id` (`shelter_id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shelter_id` (`shelter_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adoption_applications`
--
ALTER TABLE `adoption_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `follow_ups`
--
ALTER TABLE `follow_ups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adoption_applications`
--
ALTER TABLE `adoption_applications`
  ADD CONSTRAINT `adoption_applications_ibfk_1` FOREIGN KEY (`adopter_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `adoption_applications_ibfk_2` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`);

--
-- Constraints for table `follow_ups`
--
ALTER TABLE `follow_ups`
  ADD CONSTRAINT `follow_ups_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`),
  ADD CONSTRAINT `follow_ups_ibfk_2` FOREIGN KEY (`adopter_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `interviews`
--
ALTER TABLE `interviews`
  ADD CONSTRAINT `interviews_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `adoption_applications` (`id`),
  ADD CONSTRAINT `interviews_ibfk_2` FOREIGN KEY (`adopter_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `interviews_ibfk_3` FOREIGN KEY (`shelter_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `interviews_ibfk_4` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`);

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`shelter_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

