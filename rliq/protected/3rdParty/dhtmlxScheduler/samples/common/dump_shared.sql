-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 24, 2009 at 12:36 PM
-- Server version: 5.1.30
-- PHP Version: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `sampledb`
--

-- --------------------------------------------------------

--
-- Table structure for table `events_shared`
--

CREATE TABLE IF NOT EXISTS `events_shared` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `text` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `event_type` int(11) NOT NULL DEFAULT '0',
  `userId` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `events_shared`
--

INSERT INTO `events_shared` (`event_id`, `start_date`, `end_date`, `text`, `event_type`, `userId`) VALUES
(4, '2009-06-17 09:05:00', '2009-06-17 16:55:00', 'New event', 1, 1),
(2, '2009-06-03 00:00:00', '2009-06-06 00:00:00', 'New event', 0, 1),
(3, '2009-06-09 00:00:00', '2009-06-12 00:00:00', 'New event', 0, 1),
(5, '2009-06-03 00:00:00', '2009-06-05 00:00:00', 'USer 2 event', 1, 2),
(6, '2009-06-02 00:00:00', '2009-06-06 00:00:00', 'user 2', 1, 2),
(7, '2009-06-03 00:00:00', '2009-06-06 00:00:00', 'New event', 1, 2),
(8, '2009-06-10 00:00:00', '2009-06-12 00:00:00', '234', 0, 2),
(9, '2009-06-18 21:15:00', '2009-06-18 22:55:00', 'Some event', 1, 2),
(10, '2009-06-05 00:00:00', '2009-06-07 00:00:00', 'asd adf', 1, 1),
(11, '2009-06-09 00:00:00', '2009-06-10 16:55:00', 'Some event', 0, 1);
