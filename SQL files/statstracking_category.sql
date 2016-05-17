CREATE DATABASE  IF NOT EXISTS `statstracking` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `statstracking`;
-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: localhost    Database: statstracking
-- ------------------------------------------------------
-- Server version	5.6.19

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
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `require_comment` tinyint(1) DEFAULT NULL,
  `division_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Category_fk1` (`department_id`),
  CONSTRAINT `Category_fk1` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,NULL,1,'Student',1,0,NULL),(2,NULL,1,'Student Leader',3,0,NULL),(3,NULL,1,'Staff',4,0,NULL),(4,NULL,1,'Faculty',5,0,NULL),(5,NULL,1,'Employers',6,0,NULL),(6,NULL,1,'Parent/Family Member',7,0,NULL),(7,NULL,1,'Alumni',2,0,NULL),(8,NULL,1,'Other',8,0,NULL),(9,1,1,'Course related',1,0,1),(10,1,1,'Program/Degree related',2,0,1),(11,1,1,'Academic Standing',3,0,1),(12,1,1,'Petitions',4,0,1),(13,1,1,'Study Skills',5,0,1),(14,1,1,'Career Exploration',6,0,2),(15,1,1,'Further Education',7,0,2),(16,1,1,'Job Search',8,0,2),(17,1,1,'Resume critique',9,0,2),(18,1,1,'Other',15,1,NULL),(19,2,1,'Workshop or Event Collaboration',1,0,NULL),(20,2,1,'Orientation of programs & services',2,0,NULL),(21,2,1,'Other',3,1,NULL),(22,3,1,'Work Study',1,0,NULL),(23,3,1,'CLN Navigations',2,0,NULL),(24,3,1,'Collaboration',3,0,NULL),(25,3,1,'Other',6,1,NULL),(26,4,1,'Work Study',1,0,NULL),(27,4,1,'Student support',2,0,NULL),(28,4,1,'CLN Navigation',3,0,NULL),(29,4,1,'Other',6,1,NULL),(30,5,1,'Job Postings',1,0,NULL),(31,5,1,'Recruitment/Career event',2,0,NULL),(32,5,1,'Campus Engagement',3,0,NULL),(33,5,1,'Other',7,1,NULL),(34,6,1,'Academic support',1,0,NULL),(35,6,1,'Career exploration support',2,0,NULL),(36,6,1,'Job Search support',3,0,NULL),(37,6,1,'Other',4,1,NULL),(38,7,1,'Academic Advising',1,0,NULL),(39,7,1,'Career Exploration',2,0,NULL),(40,7,1,'Job Search',3,0,NULL),(41,7,1,'Further Education',4,0,NULL),(42,7,1,'Career Events',5,0,NULL),(43,7,1,'Other',8,1,NULL),(44,8,1,'Referrals - Internal UTSC',1,0,NULL),(45,8,1,'Referrals - External UTSC',2,0,NULL),(46,8,1,'Other',3,1,NULL),(47,NULL,3,'toptest',1,0,NULL),(48,47,3,'medtest1',1,0,NULL),(49,47,3,'medtest2',2,0,NULL),(50,48,3,'lowtest1',1,0,NULL),(51,48,3,'lowtest2',2,0,NULL),(52,49,3,'lowtest3',1,0,NULL),(53,49,3,'lowtest4',2,0,NULL),(54,1,1,'Course Availability/Eligibility Issue',10,0,1),(55,1,1,'Experiential Programs',11,0,2),(56,1,1,'Events/Workshops',12,0,2),(57,3,1,'Student Referral',4,0,NULL),(58,3,1,'Workshop or Event Collaboration',5,0,NULL),(59,4,1,'Student Referral',4,0,NULL),(60,4,1,'Workshop or Event Collaboration',5,0,NULL),(61,5,1,'Recruitment Activities',4,0,NULL),(62,5,1,'Experiential Programs',5,0,NULL),(63,5,1,'CLN Navigation',6,0,NULL),(64,7,1,'Experiential Programs',6,0,NULL),(65,1,1,'UTSC Referrals',13,0,NULL),(66,1,1,'Check In',14,0,NULL),(67,7,1,'Check In',7,0,NULL);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-04-28 16:10:31
