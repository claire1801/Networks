-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 25, 2019 at 12:13 AM
-- Server version: 5.7.25-0ubuntu0.18.04.2
-- PHP Version: 7.2.15-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scrum_poker`
--

-- --------------------------------------------------------

--
-- Table structure for table `estimation`
--

CREATE TABLE `estimation` (
  `project_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `estimation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

--
-- Dumping data for table `estimation`
--

INSERT INTO `estimation` (`project_id`, `ticket_id`, `user_id`, `estimation`) VALUES
(1, 1, 1, 2),
(1, 1, 2, 3),
(1, 2, 1, 4),
(1, 2, 2, 4),
(1, 3, 1, 4),
(1, 3, 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `project_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`project_id`, `name`, `start_time`, `end_time`) VALUES
(1, 'SolarPLM', '2019-03-24 22:49:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_to_user`
--

CREATE TABLE `project_to_user` (
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `project_to_user`
--

INSERT INTO `project_to_user` (`project_id`, `user_id`) VALUES
(1, 2),
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `ticket_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `url` text,
  `final_estimation` int(3) DEFAULT NULL,
  `estimated` tinyint(1) DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `last_update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

--
-- Dumping data for table `ticket`
--

INSERT INTO `ticket` (`ticket_id`, `name`, `description`, `url`, `final_estimation`, `estimated`, `project_id`, `creator_id`, `last_update_time`) VALUES
(1, 'issue1', 'test tet test test test', 'http://github/issue1', 2, NULL, 1, 3, '2019-03-24 22:51:42'),
(2, 'issue2', 'test2', 'testtestsetset', 4, NULL, 1, 3, '2019-03-25 00:07:28'),
(3, '555', 'test', 'test', 4, NULL, 1, 3, '2019-03-25 00:10:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `scrum_master` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `full_name`, `email`, `password`, `scrum_master`) VALUES
(1, 'louise', 'Louise Ritchie', 'lr53@hw.ac.uk', 'password', 0),
(2, 'claire', 'Claire Cullen', 'cc525@hw.ac.uk', 'password', 0),
(3, 'arthi', 'Aarthi Balavignesh', 'ab237@hw.ac.uk', 'password', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `estimation`
--
ALTER TABLE `estimation`
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `project_to_user`
--
ALTER TABLE `project_to_user`
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `creator_id` (`creator_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `estimation`
--
ALTER TABLE `estimation`
  ADD CONSTRAINT `estimation_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `estimation_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`),
  ADD CONSTRAINT `estimation_ibfk_4` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`);

--
-- Constraints for table `project_to_user`
--
ALTER TABLE `project_to_user`
  ADD CONSTRAINT `project_to_user_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`),
  ADD CONSTRAINT `project_to_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`),
  ADD CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`creator_id`) REFERENCES `users` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
