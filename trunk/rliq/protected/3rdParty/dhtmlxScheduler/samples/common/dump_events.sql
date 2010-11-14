-- MySQL dump 10.13  Distrib 5.1.30, for Win32 (ia32)
--
-- Host: localhost    Database: sampledb
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
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(127) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (2,'French Open','2009-05-24 00:00:00','2009-06-08 00:00:00','Philippe-Chatrier Court \nParis, FRA'),(3,'Aegon Championship','2009-06-10 00:00:00','2009-06-13 00:00:00','The Queens Club \nLondon, ENG'),(4,'Wimbledon','2009-06-21 00:00:00','2009-07-05 00:00:00','Wimbledon\nJune 21, 2009 - July 5, 2009'),(5,'Indianapolis Tennis Championships','2009-07-18 00:00:00','2009-07-27 00:00:00','Indianapolis Tennis Center \nIndianapolis, IN'),(8,'Countrywide Classic Tennis','2009-07-27 00:00:00','2009-08-02 00:00:00','Los Angeles Tennis Center. Los Angeles, CA  '),(7,'ATP Master Tennis','2009-05-11 00:00:00','2009-05-18 00:00:00','La Caja Magica.\nMadrid, Spain'),(9,'Legg Mason Tennis Classic','2009-08-01 00:00:00','2009-08-11 00:00:00','Fitzgerald Tennis Center\nWashington D.C.'),(10,'Western & Southern Financial Group Women\\\'s Open','2009-08-07 00:00:00','2009-08-17 00:00:00','Lindner Family Tennis Center\nMason, OH'),(11,'Rogers Cup Women\\\'s Tennis','2009-08-15 00:00:00','2009-08-24 00:00:00','Rexall Centre\nToronto, ON'),(12,'US Open Tennis Championship','2009-08-29 00:00:00','2009-09-14 00:00:00','Arthur Ashe Stadium\nFlushing, NY'),(13,'Barclays ATP World Tour Finals','2009-11-22 00:00:00','2009-11-28 00:00:00','O2 Dome\nLondon, ENG\n'),(14,'Western & Southern Financial Group Masters Tennis','2009-08-17 00:00:00','2009-08-24 00:00:00','Lindner Family Tennis Center\nMason, OH'),(15,' Parc Izvor ','2009-05-16 15:00:00','2009-05-16 18:00:00',' Bucharest, Romania '),(16,' Arena Zagreb ','2009-05-21 14:00:00','2009-05-21 17:00:00',' Zagreb, Croatia '),(17,' Gwardia Stadium ','2009-05-23 11:00:00','2009-05-23 14:00:00',' Warsaw, Poland '),(18,' Skonto Stadium - Riga ','2009-05-25 19:00:00','2009-05-25 22:00:00',' Riga, Latvia '),(19,' Zalgirio Stadionas ','2009-05-27 15:00:00','2009-05-27 18:00:00',' Vilnius, Lithuania '),(20,' O2 Dome ','2009-05-30 17:00:00','2009-05-30 20:00:00',' London, ENG '),(21,' Evenemententerrein Megaland ','2009-05-31 16:00:00','2009-05-31 19:00:00',' Landgraaf, NL '),(22,' HSH Nordbank Arena (formerly AOL Arena) ','2009-06-02 10:00:00','2009-06-02 13:00:00',' Hamburg, GER '),(23,' LTU Arena ','2009-06-04 11:00:00','2009-06-04 14:00:00',' Dusseldorf, GER '),(24,' LTU Arena ','2009-06-05 12:00:00','2009-06-05 15:00:00',' Dusseldorf, GER '),(25,' Zentralstadion - Leipzig ','2009-06-07 20:00:00','2009-06-07 23:00:00',' Leipzig, GER '),(26,' Zentralstadion - Leipzig ','2009-06-08 17:00:00','2009-06-08 20:00:00',' Leipzig, GER '),(27,' Olympiastadion - Berlin ','2009-06-10 14:00:00','2009-06-10 17:00:00',' Berlin, GER '),(28,' Commerz Bank Arena ','2009-06-12 14:00:00','2009-06-12 17:00:00',' Frankfurt, GER '),(29,' Olympic Stadium - Munich ','2009-06-13 11:00:00','2009-06-13 14:00:00',' Munich, GER '),(30,' Stadio Olimpico ','2009-06-16 19:00:00','2009-06-16 22:00:00',' Rome, Italy '),(31,' Comunale Giuseppe Meazza - San Siro ','2009-06-18 20:00:00','2009-06-18 23:00:00',' Milan, Italy '),(32,' Inter Stadion Slovakia ','2009-06-22 19:00:00','2009-06-22 22:00:00',' Bratislava, Slovakia '),(33,' Puskas Ferenc Stadium ','2009-06-23 14:00:00','2009-06-23 17:00:00',' Budapest, Hungary '),(34,' Slavia Stadion ','2009-06-25 10:00:00','2009-06-25 13:00:00',' Prague, Czech Republic '),(35,' Stade de France - Paris ','2009-06-27 19:00:00','2009-06-27 22:00:00',' Paris, FRA '),(36,' Parken Stadium ','2009-06-30 18:00:00','2009-06-30 21:00:00',' Copenhagen, DK '),(37,' Koengen ','2009-07-02 18:00:00','2009-07-02 21:00:00',' Bergen, Norway '),(38,' Folkets Park ','2009-07-03 11:00:00','2009-07-03 14:00:00',' Malmo, SE '),(39,' Estadio Jose Zorila ','2009-07-08 18:00:00','2009-07-08 21:00:00',' Valladolid, Spain '),(40,' Bessa Stadium ','2009-07-11 10:00:00','2009-07-11 13:00:00',' Porto, Portugal '),(41,' Estadio Olimpico - Seville ','2009-07-12 14:00:00','2009-07-12 17:00:00',' Seville, Spain '),(42,' Molson Amphitheatre ','2009-07-24 16:00:00','2009-07-24 19:00:00',' Toronto, ON '),(43,' Bell Centre ','2009-07-25 18:00:00','2009-07-25 21:00:00',' Montreal, QC '),(44,' Nissan Pavilion ','2009-07-28 17:00:00','2009-07-28 20:00:00',' Bristow, VA '),(45,' Comcast Center - MA (formerly Tweeter Center) ','2009-07-31 12:00:00','2009-07-31 15:00:00',' Mansfield, MA '),(46,' Borgata Hotel Casino & Spa ','2009-08-01 15:00:00','2009-08-01 18:00:00',' Atlantic City, NJ '),(47,' Madison Square Garden ','2009-08-03 14:00:00','2009-08-03 17:00:00',' New York, NY '),(48,' Madison Square Garden ','2009-08-04 15:00:00','2009-08-04 18:00:00',' New York, NY '),(49,' Key Arena ','2009-08-10 16:00:00','2009-08-10 19:00:00',' Seattle, WA '),(50,' Shoreline Amphitheatre ','2009-08-12 11:00:00','2009-08-12 14:00:00',' Mountain View, CA '),(51,' Cricket Wireless Amphitheatre ','2009-08-14 19:00:00','2009-08-14 22:00:00',' Chula Vista, CA '),(52,' Hollywood Bowl ','2009-08-16 17:00:00','2009-08-16 20:00:00',' Los Angeles, CA '),(53,' Hollywood Bowl ','2009-08-17 13:00:00','2009-08-17 16:00:00',' Los Angeles, CA '),(54,' Honda Center ','2009-08-19 17:00:00','2009-08-19 20:00:00',' Anaheim, CA '),(55,' Santa Barbara Bowl ','2009-08-20 16:00:00','2009-08-20 19:00:00',' Santa Barbara, CA '),(56,' Palms Casino-the Pearl ','2009-08-22 10:00:00','2009-08-22 13:00:00',' Las Vegas, NV '),(57,' US Airways Center ','2009-08-23 18:00:00','2009-08-23 21:00:00',' Phoenix, AZ '),(58,' E Center ','2009-08-25 15:00:00','2009-08-25 18:00:00',' West Valley City, UT '),(59,' Red Rocks Amphitheatre ','2009-08-27 18:00:00','2009-08-27 21:00:00',' Morrison, CO '),(60,' Superpages.com Center ','2009-08-29 17:00:00','2009-08-29 20:00:00',' Dallas, TX '),(61,' Cynthia Woods Mitchell Pavilion ','2009-08-30 18:00:00','2009-08-30 21:00:00',' Houston, TX '),(62,' Lakewood Amphitheatre ','2009-09-01 15:00:00','2009-09-01 18:00:00',' Atlanta, GA '),(63,' Ford Amphitheatre at the Florida State Fairgrounds ','2009-09-04 10:00:00','2009-09-04 13:00:00',' Tampa Bay, FL '),(64,' BankAtlantic Center ','2009-09-05 13:00:00','2009-09-05 16:00:00',' Sunrise, FL '),(65,' Konig Pilsener Arena ','2009-10-31 17:00:00','2009-10-31 20:00:00',' Oberhausen, GER '),(66,' Awd Dome ','2009-11-01 13:00:00','2009-11-01 16:00:00',' Bremen, GER '),(67,' TUI Arena (formerly Preussag Arena) ','2009-11-03 14:00:00','2009-11-03 17:00:00',' Hanover, GER '),(68,' SAP Arena ','2009-11-07 13:00:00','2009-11-07 16:00:00',' Mannheim, GER '),(69,' Schleyerhalle ','2009-11-08 12:00:00','2009-11-08 15:00:00',' Stuttgart, GER '),(70,' Stade De Geneve ','2009-11-10 17:00:00','2009-11-10 20:00:00',' Geneva, CH '),(71,' Recinto Ferial - Valencia ','2009-11-12 15:00:00','2009-11-12 18:00:00',' Valencia, Spain '),(72,' Palau Sant Jordi ','2009-11-20 12:00:00','2009-11-20 15:00:00',' Barcelona, Spain '),(73,' Halle Tony Garnier ','2009-11-23 20:00:00','2009-11-23 23:00:00',' Lyon, FRA '),(74,' Arena Nurnberg ','2009-12-01 13:00:00','2009-12-01 16:00:00',' Nuremberg, GER '),(75,' Stadthalle ','2009-12-03 14:00:00','2009-12-03 17:00:00',' Vienna, Austria '),(76,' Stadthalle Graz ','2009-12-04 13:00:00','2009-12-04 16:00:00',' Graz, AT '),(77,' Hallenstadion ','2009-12-06 16:00:00','2009-12-06 19:00:00',' Zurich, CH '),(78,' Hallenstadion ','2009-12-07 10:00:00','2009-12-07 13:00:00',' Zurich, CH '),(79,' The O2 - Dublin ','2009-12-10 17:00:00','2009-12-10 20:00:00',' Dublin, IE '),(80,' Scottish Exhibition & Conference Center ','2009-12-12 14:00:00','2009-12-12 17:00:00',' Glasgow, Scotland '),(81,' LG Arena ','2009-12-13 15:00:00','2009-12-13 18:00:00',' Birmingham, ENG '),(82,' O2 Dome ','2009-12-15 13:00:00','2009-12-15 16:00:00',' London, ENG '),(83,' O2 Dome ','2009-12-16 15:00:00','2009-12-16 18:00:00',' London, ENG '),(84,' MEN Arena Manchester ','2009-12-18 16:00:00','2009-12-18 19:00:00',' Manchester, ENG ');
	
	
CREATE TABLE IF NOT EXISTS `types` (
  `typeid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`typeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;


INSERT INTO `types` (`typeid`, `name`) VALUES
(1, 'Simple'),
(2, 'Complex'),
(3, 'Unknown');


CREATE TABLE IF NOT EXISTS `tevents` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(127) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;


INSERT INTO `tevents` (`event_id`, `event_name`, `start_date`, `end_date`, `type`) VALUES
(1, 'dblclick me!', '2010-03-02 00:00:00', '2010-03-04 00:00:00', 1),
(2, 'and me!', '2010-03-09 00:00:00', '2010-03-11 00:00:00', 2),
(3, 'and me too!', '2010-03-16 00:00:00', '2010-03-18 00:00:00', 3),
(4, 'Type 2 event', '2010-03-02 08:00:00', '2010-03-02 14:10:00', 2);
	
	
	
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-05-15 10:48:28


