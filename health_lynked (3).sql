-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 06, 2018 at 12:45 PM
-- Server version: 5.7.22-0ubuntu0.17.10.1
-- PHP Version: 5.6.36-1+ubuntu17.10.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `health_lynked`
--

-- --------------------------------------------------------

--
-- Table structure for table `medical_groups`
--

CREATE TABLE `medical_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('PRIVATE','PUBLIC') NOT NULL DEFAULT 'PUBLIC',
  `description` text NOT NULL,
  `owner` bigint(20) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `updated_on` timestamp NULL DEFAULT NULL,
  `secret` varchar(8) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `medical_groups`
--

INSERT INTO `medical_groups` (`id`, `name`, `type`, `description`, `owner`, `active`, `created_by`, `created_on`, `updated_by`, `updated_on`, `secret`, `image`, `icon`) VALUES
(1, 'Brain Cancer Symptoms', 'PRIVATE', 'no Description required', 65734, 1, 65734, '2018-08-01 11:41:43', 65734, NULL, '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `medical_group_invitations`
--

CREATE TABLE `medical_group_invitations` (
  `id` bigint(20) NOT NULL,
  `medical_group_id` int(11) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('PENDING','ACCEPTED','REJECTED') NOT NULL DEFAULT 'PENDING',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(11) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `type` enum('ADMIN','USER') NOT NULL DEFAULT 'USER',
  `secret` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `medical_group_invitations`
--

INSERT INTO `medical_group_invitations` (`id`, `medical_group_id`, `user_id`, `email`, `status`, `active`, `created_by`, `created_on`, `updated_by`, `updated_on`, `note`, `type`, `secret`) VALUES
(53, 1, NULL, 'khageswar@tailwebs.com', 'PENDING', 1, 65734, '2018-08-01 11:26:06', 65734, NULL, NULL, 'USER', 'cb3f40f795daaf62010b140b4e3e94b1'),
(54, 1, NULL, ' sathees@tailwebs.com', 'PENDING', 1, 65734, '2018-08-01 11:26:06', 65734, NULL, NULL, 'USER', '9031df88bf24702db6ae3aba0d88fe18'),
(55, 1, NULL, ' sanjay@tailwebs.com', 'PENDING', 1, 65734, '2018-08-01 11:26:06', 65734, NULL, NULL, 'USER', '3641edf577dd4f0b7e744232ff8cc01c'),
(56, 1, NULL, ' santoshi@tailwebs.com', 'PENDING', 1, 65734, '2018-08-01 11:26:06', 65734, NULL, NULL, 'USER', 'f8b3bb8bdd5341da362b8aa053820590'),
(57, 1, 65734, 'rsharma@mailinator.com', 'PENDING', 1, 65734, '2018-08-01 12:53:41', 65734, NULL, NULL, 'USER', '20473eed6da66e4e06da0c46f0642c44');

-- --------------------------------------------------------

--
-- Table structure for table `medical_group_users`
--

CREATE TABLE `medical_group_users` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `medical_group_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(11) DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `ratings` tinyint(4) DEFAULT NULL,
  `invitation_id` bigint(20) DEFAULT NULL COMMENT 'Invitation ID for Log reference',
  `status` enum('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'APPROVED',
  `admin_approval` enum('PENDING','ACCEPTED','REJECTED') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `medical_group_users`
--

INSERT INTO `medical_group_users` (`id`, `user_id`, `medical_group_id`, `active`, `admin`, `created_by`, `created_on`, `updated_by`, `updated_on`, `note`, `ratings`, `invitation_id`, `status`, `admin_approval`) VALUES
(12, 65734, 1, 1, 0, 65734, '2018-08-02 07:55:05', 65734, NULL, 'Fake user rejected.', NULL, 57, 'APPROVED', 'ACCEPTED');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `medical_groups`
--
ALTER TABLE `medical_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medical_group_invitations`
--
ALTER TABLE `medical_group_invitations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medical_group_users`
--
ALTER TABLE `medical_group_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `medical_groups`
--
ALTER TABLE `medical_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `medical_group_invitations`
--
ALTER TABLE `medical_group_invitations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
--
-- AUTO_INCREMENT for table `medical_group_users`
--
ALTER TABLE `medical_group_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
