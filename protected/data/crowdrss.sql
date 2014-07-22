-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 22, 2014 at 02:27 PM
-- Server version: 5.5.37-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `crowdrss`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=25 ;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Animals'),
(2, 'Art'),
(3, 'Comics'),
(4, 'Community'),
(5, 'Crafts'),
(6, 'Dance'),
(7, 'Design'),
(8, 'Education'),
(9, 'Environment'),
(10, 'Fashion'),
(11, 'Film & Video'),
(12, 'Food'),
(13, 'Games'),
(14, 'Health'),
(15, 'Music'),
(16, 'Photography'),
(17, 'Politics'),
(18, 'Religion'),
(19, 'Small Business'),
(20, 'Sports'),
(21, 'Technology'),
(22, 'Theater'),
(23, 'Transmedia'),
(24, 'Writing');

-- --------------------------------------------------------

--
-- Table structure for table `mail_click_log`
--

CREATE TABLE IF NOT EXISTS `mail_click_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_tracking_code` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `time_clicked` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `button_name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mail_id` (`mail_tracking_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mail_log`
--

CREATE TABLE IF NOT EXISTS `mail_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tracking_code` int(11) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `time_send` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_open` timestamp NULL DEFAULT NULL,
  `extra_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tracking_code` (`tracking_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `orig_category`
--

CREATE TABLE IF NOT EXISTS `orig_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=181 ;

--
-- Dumping data for table `orig_category`
--

INSERT INTO `orig_category` (`id`, `name`, `category_id`) VALUES
(1, 'Ceramics', 2),
(2, 'Conceptual Art', 2),
(3, 'Digital Art', 2),
(4, 'Illustration', 2),
(5, 'Installations', 2),
(6, 'Mixed Media', 2),
(7, 'Painting', 2),
(8, 'Performance Art', 2),
(9, 'Public Art', 2),
(10, 'Sculpture', 2),
(11, 'Textiles', 2),
(12, 'Video Art', 2),
(13, 'Anthologies', 3),
(14, 'Comic Books', 3),
(15, 'Events', 3),
(16, 'Graphic Novels', 3),
(17, 'Webcomics', 3),
(18, 'Candles', 5),
(19, 'Crochet', 5),
(20, 'DIY', 5),
(21, 'Embroidery', 5),
(22, 'Glass', 5),
(23, 'Knitting', 5),
(24, 'Letterpress', 5),
(25, 'Pottery', 5),
(26, 'Printing', 5),
(27, 'Quilts', 5),
(28, 'Stationery', 5),
(29, 'Taxidermy', 5),
(30, 'Weaving', 5),
(31, 'Woodworking', 5),
(32, 'Performances', 6),
(33, 'Residencies', 6),
(34, 'Spaces', 6),
(35, 'Workshops', 6),
(36, 'Architecture', 7),
(37, 'Civic Design', 7),
(38, 'Graphic Design', 7),
(39, 'Interactive Design', 7),
(40, 'Product Design', 7),
(41, 'Typography', 7),
(42, 'Accessories', 10),
(43, 'Apparel', 10),
(44, 'Childrenswear', 10),
(45, 'Couture', 10),
(46, 'Footwear', 10),
(47, 'Jewelry', 10),
(48, 'Pet Fashion', 10),
(49, 'Ready-to-wear', 10),
(50, 'Action', 11),
(51, 'Animation', 11),
(52, 'Comedy', 11),
(53, 'Documentary', 11),
(54, 'Drama', 11),
(55, 'Experimental', 11),
(56, 'Family', 11),
(57, 'Fantasy', 11),
(58, 'Festivals', 11),
(59, 'Horror', 11),
(60, 'Movie Theaters', 11),
(61, 'Music Videos', 11),
(62, 'Narrative Film', 11),
(63, 'Romance', 11),
(64, 'Science Fiction', 11),
(65, 'Shorts', 11),
(66, 'Television', 11),
(67, 'Thrillers', 11),
(68, 'Webseries', 11),
(69, 'Bacon', 12),
(70, 'Community Gardens', 12),
(71, 'Cookbooks', 12),
(72, 'Drinks', 12),
(73, 'Events', 12),
(74, 'Farmer''s Markets', 12),
(75, 'Farms', 12),
(76, 'Food Trucks', 12),
(77, 'Restaurants', 12),
(78, 'Small Batch', 12),
(79, 'Spaces', 12),
(80, 'Vegan', 12),
(81, 'Gaming Hardware', 13),
(82, 'Live Games', 13),
(83, 'Mobile Games', 13),
(84, 'Playing Cards', 13),
(85, 'Puzzles', 13),
(86, 'Tabletop Games', 13),
(87, 'Video Games', 13),
(88, 'Audio', 24),
(89, 'Photo', 24),
(90, 'Print', 24),
(91, 'Video', 24),
(92, 'Web', 24),
(93, 'Blues', 15),
(94, 'Chiptune', 15),
(95, 'Classical Music', 15),
(96, 'Country & Folk', 15),
(97, 'Electronic Music', 15),
(98, 'Faith', 15),
(99, 'Hip-Hop', 15),
(100, 'Indie Rock', 15),
(101, 'Jazz', 15),
(102, 'Kids', 15),
(103, 'Latin', 15),
(104, 'Metal', 15),
(105, 'Pop', 15),
(106, 'Punk', 15),
(107, 'R&B', 15),
(108, 'Rock', 15),
(109, 'World Music', 15),
(110, 'Animals', 16),
(111, 'Fine Art ', 16),
(112, 'Nature', 16),
(113, 'People', 16),
(114, 'Photobooks', 16),
(115, 'Places', 16),
(116, 'Academic', 24),
(117, 'Anthologies', 24),
(118, 'Art Book', 24),
(119, 'Calendars', 24),
(120, 'Children''s Book', 24),
(122, 'Literary Journals', 24),
(123, 'Nonfiction', 24),
(124, 'Periodical', 24),
(125, 'Poetry', 24),
(126, 'Radio & Podcast', 24),
(127, 'Translations', 24),
(128, 'Young Adult', 24),
(129, 'Zines', 24),
(130, '3D Printing', 21),
(131, 'Apps', 21),
(132, 'Camera Equipment', 21),
(133, 'DIY Electronics', 21),
(134, 'Fabrication Tools', 21),
(135, 'Flight', 21),
(136, 'Gadgets', 21),
(137, 'Hardware', 21),
(138, 'Makerspaces', 21),
(139, 'Robots', 21),
(140, 'Software', 21),
(141, 'Sound', 21),
(142, 'Space Exploration', 21),
(143, 'Wearables', 21),
(144, 'Web', 21),
(145, 'Experimental', 22),
(146, 'Festivals', 22),
(147, 'Immersive', 22),
(148, 'Musical', 22),
(149, 'Plays', 22),
(150, 'Spaces', 22),
(151, 'Animals', 1),
(152, 'Art', 2),
(153, 'Comic', 3),
(154, 'Community', 4),
(155, 'Dance', 6),
(156, 'Design', 7),
(157, 'Education', 8),
(158, 'Environment', 9),
(159, 'Fashion', 10),
(160, 'Film', 11),
(161, 'Food', 12),
(162, 'Gaming', 13),
(163, 'Health', 14),
(164, 'Music', 15),
(165, 'Photography', 16),
(166, 'Politics', 17),
(167, 'Religion', 18),
(168, 'Small Business', 19),
(169, 'Sports', 20),
(170, 'Technology', 21),
(171, 'Theater', 22),
(172, 'Transmedia', 23),
(173, 'Video / Web', 11),
(174, 'Writing', 24),
(175, 'Crafts', 5),
(176, 'Film & Video', 11),
(177, 'Games', 13),
(178, 'Journalism', 24),
(179, 'Publishing', 24),
(180, 'Fiction', 24);

-- --------------------------------------------------------

--
-- Table structure for table `platform`
--

CREATE TABLE IF NOT EXISTS `platform` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `platform`
--

INSERT INTO `platform` (`id`, `name`) VALUES
(1, 'Kickstarter'),
(2, 'Indiegogo');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE IF NOT EXISTS `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform_id` int(11) NOT NULL,
  `orig_category_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creator` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creator_created` int(11) DEFAULT NULL,
  `creator_backed` int(11) DEFAULT NULL,
  `goal` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type_of_funding` int(11) DEFAULT NULL,
  `time_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `platform_id` (`platform_id`),
  KEY `orig_category_id` (`orig_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE IF NOT EXISTS `subscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `platform` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rss` tinyint(1) DEFAULT '0',
  `time_created` datetime NOT NULL,
  `time_updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mail_click_log`
--
ALTER TABLE `mail_click_log`
  ADD CONSTRAINT `mail_click_log_ibfk_1` FOREIGN KEY (`mail_tracking_code`) REFERENCES `mail_log` (`tracking_code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orig_category`
--
ALTER TABLE `orig_category`
  ADD CONSTRAINT `orig_category_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`platform_id`) REFERENCES `platform` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_ibfk_2` FOREIGN KEY (`orig_category_id`) REFERENCES `orig_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
