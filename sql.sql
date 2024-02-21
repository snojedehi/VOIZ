-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 21, 2024 at 12:51 PM
-- Server version: 5.5.68-MariaDB
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `asteriskcdrdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `novoip_callrequests`
--

CREATE TABLE IF NOT EXISTS `novoip_callrequests` (
  `id` int(11) NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prefix` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `repeat` int(11) NOT NULL DEFAULT '2',
  `soundRepeat` int(11) NOT NULL DEFAULT '1',
  `insertDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `trunk` int(11) NOT NULL,
  `hook` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `callerID` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reqNum` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `novoip_callrequests_phones`
--

CREATE TABLE IF NOT EXISTS `novoip_callrequests_phones` (
  `id` int(11) NOT NULL,
  `number` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exData` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `repeat` int(2) NOT NULL,
  `status` enum('wating','down','pending','') COLLATE utf8mb4_unicode_ci NOT NULL,
  `callDate` datetime NOT NULL,
  `uniqueID` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CID` int(11) NOT NULL,
  `result` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `novoip_callrequests`
--
ALTER TABLE `novoip_callrequests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trunk` (`trunk`);

--
-- Indexes for table `novoip_callrequests_phones`
--
ALTER TABLE `novoip_callrequests_phones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `novoip_callrequests`
--
ALTER TABLE `novoip_callrequests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `novoip_callrequests_phones`
--
ALTER TABLE `novoip_callrequests_phones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
