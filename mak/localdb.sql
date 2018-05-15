-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2018 at 06:35 AM
-- Server version: 10.1.26-MariaDB
-- PHP Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `localdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `cid` int(255) UNSIGNED NOT NULL,
  `uid` int(255) UNSIGNED NOT NULL,
  `tid` int(255) UNSIGNED NOT NULL,
  `status` varchar(8) CHARACTER SET latin1 NOT NULL,
  `title` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(10000) COLLATE utf8_unicode_ci NOT NULL,
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `permalink` varchar(500) CHARACTER SET latin1 NOT NULL,
  `points` int(255) UNSIGNED NOT NULL,
  `haschild` smallint(255) UNSIGNED NOT NULL,
  `comment_no` smallint(255) UNSIGNED NOT NULL,
  `liked` smallint(5) UNSIGNED NOT NULL,
  `laugh` smallint(5) UNSIGNED NOT NULL,
  `love` smallint(5) UNSIGNED NOT NULL,
  `impress` smallint(5) UNSIGNED NOT NULL,
  `scary` smallint(5) UNSIGNED NOT NULL,
  `wow` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reply`
--

CREATE TABLE `reply` (
  `rid` int(255) UNSIGNED NOT NULL,
  `uid` int(255) UNSIGNED NOT NULL,
  `cid` int(255) UNSIGNED NOT NULL,
  `status` varchar(8) CHARACTER SET latin1 NOT NULL,
  `description` varchar(10000) COLLATE utf8_unicode_ci NOT NULL,
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `permalink` varchar(500) CHARACTER SET latin1 NOT NULL,
  `points` int(255) UNSIGNED NOT NULL,
  `reply_no` smallint(255) UNSIGNED NOT NULL,
  `liked` smallint(5) UNSIGNED NOT NULL,
  `laugh` smallint(5) UNSIGNED NOT NULL,
  `love` smallint(5) UNSIGNED NOT NULL,
  `impress` smallint(5) UNSIGNED NOT NULL,
  `scary` smallint(5) UNSIGNED NOT NULL,
  `wow` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `topic`
--

CREATE TABLE `topic` (
  `tid` int(255) UNSIGNED NOT NULL,
  `uid` int(255) UNSIGNED NOT NULL,
  `type` tinyint(6) NOT NULL,
  `room` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unknown',
  `status` varchar(8) CHARACTER SET latin1 NOT NULL,
  `title` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tag` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `club` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `permalink` varchar(500) CHARACTER SET latin1 NOT NULL,
  `points` int(255) NOT NULL,
  `admin_message` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin_message_close` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `liked` smallint(5) UNSIGNED NOT NULL,
  `laugh` smallint(5) UNSIGNED NOT NULL,
  `love` smallint(5) UNSIGNED NOT NULL,
  `impress` smallint(5) UNSIGNED NOT NULL,
  `scary` smallint(5) UNSIGNED NOT NULL,
  `wow` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `uid` int(255) UNSIGNED NOT NULL,
  `nickname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(200) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `tid` (`tid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `reply`
--
ALTER TABLE `reply`
  ADD PRIMARY KEY (`rid`),
  ADD KEY `uid` (`uid`),
  ADD KEY `cid` (`cid`);

--
-- Indexes for table `topic`
--
ALTER TABLE `topic`
  ADD PRIMARY KEY (`tid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`uid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
