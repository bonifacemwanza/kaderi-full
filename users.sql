INSERT INTO `langs` (`id`, `lang_key`, `english`, `arabic`, `dutch`, `french`, `german`, `russian`, `spanish`, `turkish`) VALUES (NULL, 'pending', 'Pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL);-- phpMyAdmin SQL Dump
-- version 4.9.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: May 31, 2021 at 06:29 PM
-- Server version: 5.7.26
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `kaderi`
--

-- --------------------------------------------------------

--
-- Table structure for table `quiz_data`
--

CREATE TABLE `quiz_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_number` int(11) NOT NULL,
  `lesson_number` int(11) NOT NULL,
  `status` varchar(11) DEFAULT NULL,
  `last_question_number` int(11) DEFAULT NULL,
  `score` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `quiz_data`
--

INSERT INTO `quiz_data` (`id`, `user_id`, `book_number`, `lesson_number`, `status`, `last_question_number`, `score`) VALUES
(1, 8, 5, 1, '15.38%', 2, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `quiz_data`
--
ALTER TABLE `quiz_data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quiz_data`
--
ALTER TABLE `quiz_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
