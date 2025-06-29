-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2025 at 10:16 AM
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
(19, 16, 28, '2025-06-29', 'approved'),
(20, 17, 27, '2025-06-29', 'rejected'),
(22, 16, 40, '2025-06-29', 'pending'),
(23, 18, 46, '2025-06-29', 'approved'),
(27, 19, 27, '2025-06-29', 'approved'),
(28, 20, 25, '2025-06-29', 'approved'),
(29, 18, 31, '2025-06-29', 'approved'),
(31, 18, 51, '2025-06-29', 'rejected'),
(32, 18, 54, '2025-06-29', 'approved'),
(33, 18, 29, '2025-06-29', 'pending');

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
(5, 46, 18, 'Your interview has been confirmed. See you soon!', '2025-06-29 04:50:47'),
(6, 55, 18, 'Your interview has been scheduled!', '2025-06-29 06:34:03');

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
(8, 23, 18, 15, 46, '2025-07-02 15:00:00', 'confirmed', '2025-06-29 04:44:33'),
(9, 19, 16, 13, 28, '2025-07-04 16:00:00', 'confirmed', '2025-06-29 05:13:55'),
(10, 27, 19, 13, 27, '2025-06-30 16:50:00', 'rejected', '2025-06-29 05:38:22'),
(11, 28, 20, 13, 25, '2025-07-09 17:45:00', 'pending', '2025-06-29 05:41:07'),
(12, 29, 18, 13, 31, '2025-07-03 13:00:00', 'rejected', '2025-06-29 05:48:33'),
(13, 32, 18, 15, 54, '2025-07-10 16:00:00', 'pending', '2025-06-29 05:50:33');

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
(2, 1, 'adopter', 'Your interview for pet \'Cacaw\' was confirmed.', 1, '2025-05-28 18:27:23'),
(4, 1, 'adopter', 'Your interview for pet \'Goldie\' was confirmed.', 1, '2025-05-28 18:41:19'),
(7, 1, 'adopter', 'Your interview for pet \'Siam\' was confirmed.', 1, '2025-05-29 02:16:50'),
(9, 1, 'adopter', 'Test', 1, '2025-06-22 14:26:51'),
(11, 1, 'adopter', 'Your interview for pet \"Beardie\" was confirmed.', 1, '2025-06-23 03:26:00'),
(13, 1, 'adopter', 'Your interview for pet \"Shortie\" was confirmed.', 1, '2025-06-23 03:26:57'),
(14, 1, 'adopter', 'Test', 1, '2025-06-23 15:41:59'),
(17, 1, 'adopter', 'Your interview for pet \"Sam\" was confirmed.', 1, '2025-06-28 14:08:16'),
(18, 1, 'adopter', 'Your interview for pet \"Skie\" was confirmed.', 0, '2025-06-28 14:11:14'),
(19, 15, 'shelter', 'New interview scheduled for pet: Dash', 1, '2025-06-29 04:44:33'),
(20, 18, 'adopter', 'You have received a follow-up message from the shelter regarding Dash', 1, '2025-06-29 04:49:58'),
(21, 18, 'adopter', 'You have received a follow-up message from the shelter regarding Dash', 1, '2025-06-29 04:50:47'),
(22, 13, 'shelter', 'New interview scheduled for pet: Tiger', 1, '2025-06-29 05:13:55'),
(23, 18, 'adopter', 'Your interview for pet \"Dash\" was confirmed.', 1, '2025-06-29 05:34:05'),
(24, 16, 'adopter', 'Your interview for pet \"Tiger\" was confirmed.', 0, '2025-06-29 05:35:58'),
(25, 13, 'shelter', 'New interview scheduled for pet: Bella', 1, '2025-06-29 05:38:22'),
(26, 19, 'adopter', 'Your interview for pet \"Bella\" was rejected.', 0, '2025-06-29 05:38:32'),
(27, 13, 'shelter', 'New interview scheduled for pet: Daisy', 1, '2025-06-29 05:41:07'),
(28, 13, 'shelter', 'Interview rescheduled for pet: Bella', 1, '2025-06-29 05:46:16'),
(29, 19, 'adopter', 'Your interview for pet \"Bella\" was rejected.', 0, '2025-06-29 05:46:25'),
(30, 13, 'shelter', 'New interview scheduled for pet: Slinky', 1, '2025-06-29 05:48:33'),
(31, 18, 'adopter', 'Your interview for pet \"Slinky\" was rejected.', 1, '2025-06-29 05:48:43'),
(32, 15, 'shelter', 'New interview scheduled for pet: Chilla', 0, '2025-06-29 05:50:33'),
(33, 13, 'shelter', 'New interview scheduled for pet: Chopper', 1, '2025-06-29 06:32:09'),
(34, 18, 'adopter', 'Your interview for pet \"Chopper\" was rejected.', 1, '2025-06-29 06:32:34'),
(35, 13, 'shelter', 'Interview rescheduled for pet: Chopper', 1, '2025-06-29 06:33:08'),
(36, 18, 'adopter', 'Your interview for pet \"Chopper\" was confirmed.', 1, '2025-06-29 06:33:26'),
(37, 18, 'adopter', 'You have received a follow-up message from the shelter regarding Chopper', 1, '2025-06-29 06:34:03'),
(38, 1, 'adopter', 'There will be a system maintenance break at 12.00 Midnight tonight.', 0, '2025-06-29 06:37:46'),
(39, 7, 'adopter', 'There will be a system maintenance break at 12.00 Midnight tonight.', 0, '2025-06-29 06:37:46'),
(40, 12, 'adopter', 'There will be a system maintenance break at 12.00 Midnight tonight.', 0, '2025-06-29 06:37:46'),
(41, 13, 'adopter', 'There will be a system maintenance break at 12.00 Midnight tonight.', 0, '2025-06-29 06:37:46'),
(42, 14, 'adopter', 'There will be a system maintenance break at 12.00 Midnight tonight.', 0, '2025-06-29 06:37:46'),
(43, 15, 'adopter', 'There will be a system maintenance break at 12.00 Midnight tonight.', 0, '2025-06-29 06:37:46'),
(44, 16, 'adopter', 'There will be a system maintenance break at 12.00 Midnight tonight.', 0, '2025-06-29 06:37:46'),
(45, 17, 'adopter', 'There will be a system maintenance break at 12.00 Midnight tonight.', 0, '2025-06-29 06:37:46'),
(46, 18, 'adopter', 'There will be a system maintenance break at 12.00 Midnight tonight.', 1, '2025-06-29 06:37:46'),
(47, 19, 'adopter', 'There will be a system maintenance break at 12.00 Midnight tonight.', 0, '2025-06-29 06:37:46'),
(48, 20, 'adopter', 'There will be a system maintenance break at 12.00 Midnight tonight.', 0, '2025-06-29 06:37:46'),
(49, 13, 'adopter', 'There will be a system maintenance tonight at 12.00 Midnight.', 0, '2025-06-29 06:38:27'),
(50, 1, 'adopter', 'Test', 0, '2025-06-29 06:45:56'),
(51, 7, '', 'Test', 0, '2025-06-29 06:45:56'),
(52, 12, 'shelter', 'Test', 0, '2025-06-29 06:45:56'),
(53, 13, 'shelter', 'Test', 1, '2025-06-29 06:45:56'),
(54, 14, 'shelter', 'Test', 0, '2025-06-29 06:45:56'),
(55, 15, 'shelter', 'Test', 0, '2025-06-29 06:45:56'),
(56, 16, 'adopter', 'Test', 0, '2025-06-29 06:45:56'),
(57, 17, 'adopter', 'Test', 0, '2025-06-29 06:45:56'),
(58, 18, 'adopter', 'Test', 1, '2025-06-29 06:45:56'),
(59, 19, 'adopter', 'Test', 0, '2025-06-29 06:45:56'),
(60, 20, 'adopter', 'Test', 0, '2025-06-29 06:45:56'),
(61, 1, 'adopter', 'There will be a maintenance on 30/6/2025, 12.00am.', 0, '2025-06-29 06:58:57'),
(62, 7, '', 'There will be a maintenance on 30/6/2025, 12.00am.', 0, '2025-06-29 06:58:57'),
(63, 12, 'shelter', 'There will be a maintenance on 30/6/2025, 12.00am.', 0, '2025-06-29 06:58:57'),
(64, 13, 'shelter', 'There will be a maintenance on 30/6/2025, 12.00am.', 0, '2025-06-29 06:58:57'),
(65, 14, 'shelter', 'There will be a maintenance on 30/6/2025, 12.00am.', 0, '2025-06-29 06:58:57'),
(66, 15, 'shelter', 'There will be a maintenance on 30/6/2025, 12.00am.', 0, '2025-06-29 06:58:57'),
(67, 16, 'adopter', 'There will be a maintenance on 30/6/2025, 12.00am.', 0, '2025-06-29 06:58:57'),
(68, 17, 'adopter', 'There will be a maintenance on 30/6/2025, 12.00am.', 0, '2025-06-29 06:58:57'),
(69, 18, 'adopter', 'There will be a maintenance on 30/6/2025, 12.00am.', 1, '2025-06-29 06:58:57'),
(70, 19, 'adopter', 'There will be a maintenance on 30/6/2025, 12.00am.', 0, '2025-06-29 06:58:57'),
(71, 20, 'adopter', 'There will be a maintenance on 30/6/2025, 12.00am.', 0, '2025-06-29 06:58:57');

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
(15, 'Coco', 'Dog', 'Cavalier King Charles Spaniel', 4, 'Male', 'A loyal and playful Cavalier King Charles Spaniel that loves people.', '1751123521_Cavalier King Charles Spaniel.jpg', 'available', 12, NULL),
(16, 'Bella', 'Dog', 'Siberian Husky', 4, 'Female', 'A loyal and playful Siberian Husky that loves people.', '1751123559_Siberian Husky.jpg', 'available', 12, NULL),
(17, 'Max', 'Dog', 'Boxer', 3, 'Male', 'A loyal and playful Boxer that loves people.', '1751123587_Boxer Dog.jpg', 'available', 12, NULL),
(18, 'Mittens', 'Cat', 'Turkish Van', 1, 'Male', 'A curious and independent Turkish Van with a gentle temperament.', '1751123612_Turkish Van.jpg', 'available', 12, NULL),
(19, 'Missy', 'Cat', 'Burmese', 2, 'Female', 'A curious and independent Burmese with a gentle temperament.', '1751123644_Burmese Cat.JPG', 'available', 12, NULL),
(20, 'Whiskers', 'Cat', 'Ragdoll', 1, 'Female', 'A curious and independent Ragdoll with a gentle temperament.', '1751123709_Ragdoll.jpg', 'available', 12, NULL),
(21, 'Feathers', 'Bird', 'Canary', 3, 'Male', 'A vibrant and intelligent Canary that loves to chirp and interact.', '1751123744_Canary Bird.jpg', 'available', 12, NULL),
(22, 'Rio', 'Bird', 'Macaw', 2, 'Male', 'A vibrant and intelligent Macaw that loves to chirp and interact.', '1751123768_Macaw Parrot.jpg', 'available', 12, NULL),
(23, 'Kiki', 'Exotic Pet', 'Serval', 3, 'Male', 'An unusual but lovable Serval that\'s perfect for exotic pet lovers.', '1751123792_Serval Exotic.jpg', 'available', 12, NULL),
(24, 'Nibbles', 'Small Mammal', 'Dwarf Hamster', 1, 'Female', 'A small and friendly Dwarf Hamster that enjoys gentle handling.', '1751123819_Dwarf Hamster.jpeg', 'available', 12, NULL),
(25, 'Daisy', 'Dog', 'Beagle', 2, 'Female', 'She\'s a loyal beagle.', '1751124728_Beagle.JPG', 'adopted', 13, '2025-06-29'),
(26, 'Luna', 'Dog', 'Golden Retriever', 4, 'Female', 'One of the prettiest Golden Retrievers up for adoption.', '1751124776_Golden Retriever.jpg', 'available', 13, NULL),
(27, 'Bella', 'Dog', 'Poodle', 2, 'Female', 'Bella\'s very friendly', '1751124803_Poodle.jpg', 'adopted', 13, '2025-06-29'),
(28, 'Tiger', 'Cat', 'Persian', 3, 'Male', 'He meows like a tiger. Roar.', '1751124842_Persian.jpg', 'adopted', 13, '2025-06-29'),
(29, 'Loki', 'Cat', 'Ragdoll', 1, 'Male', 'A curious and independent Ragdoll with a gentle temperament.', '1751124874_Ragdoll2.jpg', 'available', 13, NULL),
(30, 'Stinky', 'Cat', 'Siamese', 3, 'Male', 'He stinks. Hahaha just kidding.', '1751124910_Siamese.jpg', 'available', 13, NULL),
(31, 'Slinky', 'Reptile', 'Painted Turtle', 1, 'Male', 'A fascinating Painted Turtle known for its unique behavior and appearance.', '1751124938_Painted Turtle.jpg', 'adopted', 13, '2025-06-29'),
(32, 'Kiwi', 'Bird', 'Quaker Parrot', 4, 'Male', 'A vibrant and intelligent Quaker Parrot that loves to chirp and interact.', '1751124965_Quaker Parrot.jpg', 'available', 13, NULL),
(33, 'Snowball', 'Small Mammal', 'Hedgehog', 1, 'Male', 'A small and friendly Hedgehog that enjoys gentle handling.', '1751124988_Hedgehog2.jpg', 'available', 13, NULL),
(34, 'Tiny', 'Small Mammal', 'Hedgehog', 1, 'Female', 'A small and friendly Hedgehog that enjoys gentle handling.', '1751125010_Hedgehog3.jpg', 'available', 13, NULL),
(35, 'Rocky', 'Dog', 'Australian Shepherd', 3, 'Male', 'A loyal and playful Australian Shepherd that loves people.', '1751140056_Australian Shepherd.jpg', 'available', 14, NULL),
(36, 'Bailey', 'Dog', 'German Shepherd', 6, 'Male', 'A loyal and playful German Shepherd that loves people.', '1751140129_GermanShepherd1.jpg', 'available', 14, NULL),
(37, 'Milo', 'Dog', 'Yorkshire Terrier', 4, 'Male', 'A loyal and playful Yorkshire Terrier that loves people.', '1751140155_Yorkshire Terrier.jpeg', 'available', 14, NULL),
(38, 'Nala', 'Cat', 'Exotic Shorthair', 3, 'Female', 'A curious and independent Exotic Shorthair with a gentle temperament.', '1751140180_ExoticShorthair.jpg', 'available', 14, NULL),
(39, 'Oreo', 'Cat', 'Persian', 6, 'Male', 'A curious and independent Persian with a gentle temperament.', '1751140204_Persian1.jpg', 'available', 14, NULL),
(40, 'Pumpkin', 'Cat', 'Birman', 3, 'Female', 'A curious and independent Birman with a gentle temperament.', '1751140233_Birman.jpg', 'available', 14, NULL),
(41, 'Toffee', 'Exotic Pet', 'Capybara', 2, 'Male', 'An unusual but lovable Capybara that\'s perfect for exotic pet lovers.', '1751140282_Capybara.jpg', 'available', 14, NULL),
(42, 'Mojo', 'Exotic Pet', 'Sugar Glider', 1, 'Male', 'An unusual but lovable Sugar Glider that\'s perfect for exotic pet lovers.', '1751140320_SugarGlider1.jpg', 'available', 14, NULL),
(43, 'Beardie', 'Reptile', 'Bearded Dragon', 2, 'Male', 'A playful bearded dragon.', '1751140364_BeardedDragon1.jpg', 'available', 14, NULL),
(44, 'Cookie', 'Bird', 'Cockatoo', 4, 'Male', 'A cockatoo that would repeat whatever you say!', '1751140400_Cockatoo.jpg', 'available', 14, NULL),
(45, 'Spike', 'Reptile', 'Red-Eared Slider Turtle', 2, 'Male', 'A fascinating Red-Eared Slider Turtle known for its unique behavior and appearance.', '1751141496_Red-Eared Slider Turtle.jpg', 'available', 15, NULL),
(46, 'Dash', 'Reptile', 'Leopard Gecko', 3, 'Male', 'A fascinating Leopard Gecko known for its unique behavior and appearance.', '1751141522_Leopard Gecko.jpg', 'adopted', 15, '2025-06-29'),
(47, 'Zilla', 'Reptile', 'Leopard Gecko', 1, 'Female', 'A fascinating Leopard Gecko known for its unique behavior and appearance.', '1751141551_Leopard Gecko2.jpg', 'available', 15, NULL),
(48, 'Rex', 'Reptile', 'Uromastyx', 7, 'Male', 'A fascinating Uromastyx known for its unique behavior and appearance.', '1751141575_Uromastyx.jpg', 'available', 15, NULL),
(49, 'Scales', 'Reptile', 'Green Iguana', 4, 'Male', 'A fascinating Green Iguana known for its unique behavior and appearance.', '1751141611_Green Iguana.jpg', 'available', 15, NULL),
(50, 'Bubbles', 'Small Mammal', 'Gerbil', 1, 'Female', 'A small and friendly Gerbil that enjoys gentle handling.', '1751141695_Gerbil.jpg', 'available', 15, NULL),
(51, 'Gizmo', 'Small Mammal', 'Hedgehog', 2, 'Male', 'A small and friendly Hedgehog that enjoys gentle handling.', '1751141717_Hedgehog4.jpg', 'available', 15, NULL),
(52, 'Fluffy', 'Small Mammal', 'Rabbit', 2, 'Female', 'A small and friendly Rabbit that enjoys gentle handling.', '1751141744_Rabbit.jpg', 'available', 15, NULL),
(53, 'Loppy', 'Small Mammal', 'Holland Lop', 5, 'Female', 'A small and friendly Holland Lop that enjoys gentle handling.', '1751141774_Holland Lop.jpeg', 'available', 15, NULL),
(54, 'Chilla', 'Small Mammal', 'Chinchilla', 2, 'Male', 'A Chinchilla that loves to chill.', '1751141799_chinchilla.jpg', 'adopted', 15, '2025-06-29'),
(55, 'Chopper', 'Cat', 'British Shorthair', 3, 'Male', 'He\'s very cute and playful.', '1751178603_BSH1.png', 'adopted', 13, '2025-06-29');

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
(7, 'admin01', 'admin01@test.com', '$2y$10$9NYCOftrumDvzLUAL3D.4.RUPfWsdx1LSC8KfpDrimlvNJ7DvDhT6', 'admin', '01', '01123452534', 'admin', '2025-06-04 18:07:34', '', 'active'),
(12, 'Paws Haven Cyberjaya', 'pawshaven@gmail.com', '$2y$10$UgWIEi/yTbNRnIdeZwHka.4Wf5R2ylRxgQauVQq/rO5MOdQIrXaC2', 'Paws', 'Haven', '0177974077', 'shelter', '2025-06-28 14:42:20', '', 'active'),
(13, 'Fluffy Friends Rescue', 'fluffyfriendsrescue@gmail.com', '$2y$10$Qbk.FteTZFHx7vxz0znKRu/wy5JfSzN3udgpySSgcT9he/ionKBt.', 'Fluffy', 'Friends', '0175678003', 'shelter', '2025-06-28 14:43:21', '', 'active'),
(14, 'Loving Paws', 'lovingpaws@gmail.com', '$2y$10$eJbQB4I3DKpAAAfb2EOCu.urUhw/i1MYg4yPg4wYagMg.nJlpsIAe', 'Loving', 'Paws', '0174588593', 'shelter', '2025-06-28 14:44:08', '', 'active'),
(15, 'Scales Sanctuary', 'scalessanctuary@gmail.com', '$2y$10$hszc.UtiC7bO1Ip/u6a70.QqPknafSJcph2uGCHN0JoaB7pl6IfFW', 'Scales', 'Sanctuary', '0125643576', 'shelter', '2025-06-28 14:45:10', '', 'active'),
(16, 'Lisa Ong', 'lisaong@gmail.com', '$2y$10$MUl5BhfCuOEpKNEKefGN7.Ad7rbDeyXX6E6PHtZqekXf0Ii7J9hOq', 'Lisa', 'Ong', '0146372984', 'adopter', '2025-06-28 14:45:56', '', 'active'),
(17, 'John Tan', 'johntan@gmail.com', '$2y$10$kcN69s5juG30iZULr8KsHOLwEWxgptgxOBHgMQ3giv05LSWD3bHWi', 'John', 'Tan', '01946325463', 'adopter', '2025-06-28 14:48:47', '', 'active'),
(18, 'Azmi Sahi', 'azmisahi@gmail.com', '$2y$10$WiIy0NQch2DtKFhjl22S0.sg/G7IOFjP6eenR.HAqyUhWmcD0rlfy', 'Azmi', 'Sahi', '0177564733', 'adopter', '2025-06-28 14:53:28', NULL, 'active'),
(19, 'Ali Abu', 'aliabu@gmail.com', '$2y$10$Gg//tU.NA0eeY8JZ5sNzsOVsizZY9LAiHTe2kOcujyiShDRAuI/s6', 'Ali', 'Abu', '0178876564', 'adopter', '2025-06-29 05:37:32', NULL, 'active'),
(20, 'Alvinlim', 'alvinlim@gmail.com', '$2y$10$JtgAMNzPb1FZgqswOmML9u/69Er7H6LHNP/6vfxMKS7U2/ttGWfqu', 'Alvin', 'Lim', '0127685447', 'adopter', '2025-06-29 05:40:25', NULL, 'active');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `follow_ups`
--
ALTER TABLE `follow_ups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
