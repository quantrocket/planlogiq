-- MySQL dump 10.13  Distrib 5.1.30, for Win32 (ia32)
--
-- Host: localhost    Database: sampleDB
-- ------------------------------------------------------
-- Server version	5.1.30-community

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `events_rec`
--

DROP TABLE IF EXISTS `events_rec`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `events_rec` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `text` varchar(255) NOT NULL,
  `rec_type` varchar(64) NOT NULL,
  `event_pid` int(11) NOT NULL,
  `event_length` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `events_rec`
--

LOCK TABLES `events_rec` WRITE;
/*!40000 ALTER TABLE `events_rec` DISABLE KEYS */;
INSERT INTO `events_rec` VALUES (1,'2009-07-01 00:00:00','9999-02-01 00:00:00','Second Friday','month_1_5_2_#no',0,86400),(2,'2009-06-29 10:00:00','9999-02-01 00:00:00','Test build','week_1___1,3,5#no',0,3600),(3,'2009-07-22 10:00:00','2009-07-22 11:00:00','','none',2,1248246000),(4,'2009-07-21 00:00:00','2009-08-30 00:00:00','Each 8 days, 5 times','day_8___#5',0,172800),(5,'2009-07-16 10:00:00','2009-07-16 11:00:00','Test build','',2,1247814000),(6,'2009-06-29 10:00:00','2009-06-29 11:00:00','','none',2,1246258800),(15,'2009-07-06 00:00:00','2009-07-19 23:59:00','2 Wed','week_1___0#2',0,300),(17,'2009-07-01 00:00:00','2009-08-04 23:59:00','New event','month_1_2_2_#2',0,300),(19,'2009-07-01 00:00:00','9999-02-01 00:00:00','2nd monday','month_1_2_1_#no',0,300),(20,'2009-01-01 00:00:00','9999-02-01 00:00:00','New event','year_1_1_2_#no',0,300),(21,'2010-01-31 00:00:00','9999-02-01 00:00:00','New event','month_1___#no',0,86400);
/*!40000 ALTER TABLE `events_rec` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-07-22 10:53:28
