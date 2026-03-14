-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 14, 2026 at 02:51 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `LibraryDatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `Books`
--

CREATE TABLE `Books` (
  `ISBN` text NOT NULL,
  `BookTitle` text NOT NULL,
  `Author` text NOT NULL,
  `Edition` int(11) NOT NULL,
  `Year` year(4) NOT NULL,
  `Category` int(11) NOT NULL,
  `Reserved` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Categories`
--

CREATE TABLE `Categories` (
  `CategoryID` text NOT NULL,
  `CategoryDescription` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Reservations`
--

CREATE TABLE `Reservations` (
  `ISBN` int(11) NOT NULL,
  `Username` int(11) NOT NULL,
  `ReservedDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `Username` text NOT NULL,
  `Password` text NOT NULL,
  `FirstName` text NOT NULL,
  `Surname` text NOT NULL,
  `AddressLine1` text NOT NULL,
  `AddressLine2` text NOT NULL,
  `City` text NOT NULL,
  `Telephone` text NOT NULL,
  `Mobile` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Books`
--
ALTER TABLE `Books`
  ADD UNIQUE KEY `ISBN` (`ISBN`) USING HASH;

--
-- Indexes for table `Categories`
--
ALTER TABLE `Categories`
  ADD UNIQUE KEY `CategoryID` (`CategoryID`) USING HASH;

--
-- Indexes for table `Reservations`
--
ALTER TABLE `Reservations`
  ADD UNIQUE KEY `ISBN` (`ISBN`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD UNIQUE KEY `Username` (`Username`) USING HASH;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
