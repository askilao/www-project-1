-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Feb 28, 2019 at 11:25 PM
-- Server version: 10.3.12-MariaDB-1:10.3.12+maria~bionic
-- PHP Version: 7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `myDb`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_token`
--

CREATE TABLE `auth_token` (
  `id` int(11) NOT NULL,
  `selector` char(12) NOT NULL,
  `token` char(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('0','1','2','') NOT NULL,
  `isAdmin` tinyint(1) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `mail` varchar(20) NOT NULL,
  `time` datetime NOT NULL,
  `comment` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `code` text NOT NULL,
  `title` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`code`, `title`) VALUES
('IMT1337', 'Hack or die');

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

CREATE TABLE `playlist` (
  `playlist_id` int(11) NOT NULL,
  `playlist_title` text NOT NULL,
  `description` text NOT NULL,
  `created_by_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `playlist_subscriptions`
--

CREATE TABLE `playlist_subscriptions` (
  `id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `playlist_video`
--

CREATE TABLE `playlist_video` (
  `id` int(11) NOT NULL,
  `fk_video_id` int(11) NOT NULL,
  `fk_playlist_id` int(11) NOT NULL,
  `video_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rating_log`
--

CREATE TABLE `rating_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_request`
--

CREATE TABLE `teacher_request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `firstname` text NOT NULL,
  `mail` char(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('0','1','2') DEFAULT '0',
  `isAdmin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `firstname`, `mail`, `password`, `role`, `isAdmin`) VALUES
(15, 'Teacher', 'teach@teach.no', '$2y$10$9koLnb/iKwTR12a0USur6u836X0Z75Ytf0eMEDP5kzOU/3HNX6vpm', '1', 1),
(16, 'Admin', 'admin@unitube.no', '$2y$10$/RsRFyjQsOngt8zL7JuBeuMaZ6QE6cxRgEwcXxHCrZG9O3rAWNlQO', '2', 1),
(33, 'student', 'student@stud.ntnu.no', '$2y$10$0EAwZUIQ2HPtBaQGrsVkqOl26.nfbdPA7cXqPI6UR0VVTkKqoaztG', '0', 0);

-- --------------------------------------------------------

--
-- Table structure for table `video`
--

CREATE TABLE `video` (
  `video_id` int(11) NOT NULL,
  `fk_owner_id` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `extension` varchar(5) NOT NULL,
  `mime` varchar(100) NOT NULL,
  `size` int(11) NOT NULL,
  `description` text NOT NULL,
  `emne` varchar(8) NOT NULL,
  `thumbnail` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `video_viewcount`
--

CREATE TABLE `video_viewcount` (
  `rating_id` int(11) NOT NULL,
  `fk_video_id` int(11) NOT NULL,
  `votes` int(11) DEFAULT NULL,
  `sum_of_vote` int(11) DEFAULT NULL,
  `rating` int(11) GENERATED ALWAYS AS (`sum_of_vote` / `votes`) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_token`
--
ALTER TABLE `auth_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_id_pk_1` (`video_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`code`(10));

--
-- Indexes for table `playlist`
--
ALTER TABLE `playlist`
  ADD PRIMARY KEY (`playlist_id`),
  ADD KEY `fk_user_id` (`created_by_user`);

--
-- Indexes for table `playlist_subscriptions`
--
ALTER TABLE `playlist_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `playlist_fk_1` (`playlist_id`);

--
-- Indexes for table `playlist_video`
--
ALTER TABLE `playlist_video`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_video_playlist_id` (`fk_video_id`,`fk_playlist_id`),
  ADD KEY `fk_playlist_id` (`fk_playlist_id`);

--
-- Indexes for table `rating_log`
--
ALTER TABLE `rating_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_video_id_2` (`video_id`);

--
-- Indexes for table `teacher_request`
--
ALTER TABLE `teacher_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`video_id`),
  ADD KEY `fk_user_id` (`fk_owner_id`);

--
-- Indexes for table `video_viewcount`
--
ALTER TABLE `video_viewcount`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `fk_video_id` (`fk_video_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth_token`
--
ALTER TABLE `auth_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `playlist`
--
ALTER TABLE `playlist`
  MODIFY `playlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `playlist_subscriptions`
--
ALTER TABLE `playlist_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `playlist_video`
--
ALTER TABLE `playlist_video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `rating_log`
--
ALTER TABLE `rating_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `teacher_request`
--
ALTER TABLE `teacher_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `video`
--
ALTER TABLE `video`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `video_viewcount`
--
ALTER TABLE `video_viewcount`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `video_id_pk_1` FOREIGN KEY (`video_id`) REFERENCES `video` (`video_id`) ON DELETE CASCADE;

--
-- Constraints for table `playlist`
--
ALTER TABLE `playlist`
  ADD CONSTRAINT `playlist_ibfk_1` FOREIGN KEY (`created_by_user`) REFERENCES `user` (`id`) ON DELETE NO ACTION;

--
-- Constraints for table `playlist_subscriptions`
--
ALTER TABLE `playlist_subscriptions`
  ADD CONSTRAINT `playlist_fk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlist` (`playlist_id`) ON DELETE CASCADE;

--
-- Constraints for table `playlist_video`
--
ALTER TABLE `playlist_video`
  ADD CONSTRAINT `fk_playlist_id` FOREIGN KEY (`fk_playlist_id`) REFERENCES `playlist` (`playlist_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_video_id_1` FOREIGN KEY (`fk_video_id`) REFERENCES `video` (`video_id`);

--
-- Constraints for table `rating_log`
--
ALTER TABLE `rating_log`
  ADD CONSTRAINT `fk_video_id_2` FOREIGN KEY (`video_id`) REFERENCES `video` (`video_id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_request`
--
ALTER TABLE `teacher_request`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `video`
--
ALTER TABLE `video`
  ADD CONSTRAINT `video_ibfk_1` FOREIGN KEY (`fk_owner_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION;

--
-- Constraints for table `video_viewcount`
--
ALTER TABLE `video_viewcount`
  ADD CONSTRAINT `video_viewcount_ibfk_1` FOREIGN KEY (`fk_video_id`) REFERENCES `video` (`video_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
