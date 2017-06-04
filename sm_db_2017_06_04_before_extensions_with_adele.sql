-- MySQL dump 10.13  Distrib 5.5.54, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: subsmanager
-- ------------------------------------------------------
-- Server version	5.5.54-0ubuntu0.14.04.1

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
-- Table structure for table `attendance_history`
--

DROP TABLE IF EXISTS `attendance_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_event_id` int(11) DEFAULT NULL,
  `attendee_id` int(11) DEFAULT NULL,
  `subscription_in_use_id` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_207D9E49FA5B88E3` (`session_event_id`),
  KEY `IDX_207D9E49BCFD782A` (`attendee_id`),
  KEY `IDX_207D9E4944EFD744` (`subscription_in_use_id`),
  CONSTRAINT `FK_207D9E4944EFD744` FOREIGN KEY (`subscription_in_use_id`) REFERENCES `subscription` (`id`),
  CONSTRAINT `FK_207D9E49BCFD782A` FOREIGN KEY (`attendee_id`) REFERENCES `user_account` (`id`),
  CONSTRAINT `FK_207D9E49FA5B88E3` FOREIGN KEY (`session_event_id`) REFERENCES `session_event` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_history`
--

LOCK TABLES `attendance_history` WRITE;
/*!40000 ALTER TABLE `attendance_history` DISABLE KEYS */;
INSERT INTO `attendance_history` VALUES (1,1,26,1,'2017-05-30 14:43:17','2017-05-30 14:43:17'),(2,1,27,3,'2017-05-30 14:46:57','2017-05-30 14:46:57'),(3,1,29,5,'2017-05-30 14:46:57','2017-05-30 14:46:57'),(4,1,30,6,'2017-05-30 14:46:57','2017-05-30 14:46:57'),(5,1,37,17,'2017-05-30 14:46:57','2017-05-30 14:46:57'),(6,1,38,17,'2017-05-30 14:46:57','2017-05-30 14:46:57'),(7,1,32,8,'2017-05-30 14:48:37','2017-05-30 14:48:37'),(8,1,34,10,'2017-05-30 14:48:37','2017-05-30 14:48:37'),(9,1,31,7,'2017-05-30 14:50:14','2017-05-30 14:50:14'),(10,2,47,11,'2017-05-30 14:52:48','2017-05-30 14:52:48'),(11,2,33,9,'2017-05-30 14:52:48','2017-05-30 14:52:48'),(12,2,34,10,'2017-05-30 14:52:48','2017-05-30 14:52:48'),(13,4,46,13,'2017-05-30 14:54:44','2017-05-30 14:54:44'),(14,4,35,15,'2017-05-30 14:54:44','2017-05-30 14:54:44'),(15,4,27,3,'2017-05-30 14:54:44','2017-05-30 14:54:44'),(16,4,26,1,'2017-05-30 14:54:44','2017-05-30 14:54:44'),(17,5,34,10,'2017-05-30 14:56:22','2017-05-30 14:56:22'),(18,5,31,7,'2017-05-30 14:56:22','2017-05-30 14:56:22'),(19,5,35,15,'2017-05-30 14:56:22','2017-05-30 14:56:22'),(20,5,26,1,'2017-05-30 14:56:22','2017-05-30 14:56:22'),(21,5,47,11,'2017-05-30 14:56:22','2017-05-30 14:56:22'),(22,5,46,13,'2017-05-30 14:56:22','2017-05-30 14:56:22'),(23,5,36,16,'2017-05-30 14:56:22','2017-05-30 14:56:22'),(24,6,27,3,'2017-05-30 14:58:32','2017-05-30 14:58:32'),(25,6,39,18,'2017-05-30 14:58:32','2017-05-30 14:58:32'),(26,6,36,16,'2017-05-30 14:58:32','2017-05-30 14:58:32'),(27,6,37,17,'2017-05-30 14:58:32','2017-05-30 14:58:32'),(28,6,38,17,'2017-05-30 14:58:32','2017-05-30 14:58:32'),(29,6,32,8,'2017-05-30 14:58:32','2017-05-30 14:58:32'),(30,7,40,19,'2017-05-30 14:59:33','2017-05-30 14:59:33'),(31,7,46,13,'2017-05-30 14:59:33','2017-05-30 14:59:33'),(32,7,31,7,'2017-05-30 14:59:33','2017-05-30 14:59:33'),(33,7,33,9,'2017-05-30 14:59:33','2017-05-30 14:59:33'),(34,8,29,5,'2017-05-30 15:00:44','2017-05-30 15:00:44'),(35,8,47,11,'2017-05-30 15:00:44','2017-05-30 15:00:44'),(36,8,46,13,'2017-05-30 15:00:44','2017-05-30 15:00:44'),(37,8,27,3,'2017-05-30 15:00:44','2017-05-30 15:00:44'),(38,8,35,15,'2017-05-30 15:00:44','2017-05-30 15:00:44'),(39,9,31,7,'2017-05-30 15:02:15','2017-05-30 15:02:15'),(40,9,47,11,'2017-05-30 15:02:15','2017-05-30 15:02:15'),(41,9,46,13,'2017-05-30 15:02:15','2017-05-30 15:02:15'),(42,9,34,10,'2017-05-30 15:02:15','2017-05-30 15:02:15'),(43,10,47,11,'2017-05-30 15:02:48','2017-05-30 15:02:48'),(44,6,28,4,'2017-05-30 15:05:07','2017-05-30 15:05:07'),(45,11,28,4,'2017-05-30 15:07:27','2017-05-30 15:07:27'),(46,11,39,18,'2017-05-30 15:07:27','2017-05-30 15:07:27'),(47,11,36,16,'2017-05-30 15:07:27','2017-05-30 15:07:27'),(48,11,46,13,'2017-05-30 15:07:27','2017-05-30 15:07:27'),(49,11,26,1,'2017-05-30 15:07:27','2017-05-30 15:07:27'),(50,11,41,20,'2017-05-30 15:07:27','2017-05-30 15:07:27'),(51,11,42,20,'2017-05-30 15:07:27','2017-05-30 15:07:27'),(52,11,30,6,'2017-05-30 15:08:20','2017-05-30 15:08:20'),(53,11,29,5,'2017-05-30 15:08:20','2017-05-30 15:08:20'),(54,11,43,21,'2017-05-30 15:08:20','2017-05-30 15:08:20'),(55,12,44,22,'2017-05-30 15:09:34','2017-05-30 15:09:34'),(56,12,46,13,'2017-05-30 15:09:34','2017-05-30 15:09:34'),(57,12,47,11,'2017-05-30 15:09:34','2017-05-30 15:09:34'),(58,12,33,9,'2017-05-30 15:09:34','2017-05-30 15:09:34'),(59,12,34,10,'2017-05-30 15:09:34','2017-05-30 15:09:34'),(60,12,40,19,'2017-05-30 15:09:34','2017-05-30 15:09:34'),(61,14,26,2,'2017-05-30 15:11:20','2017-05-30 15:11:20'),(62,14,37,17,'2017-05-30 15:11:20','2017-05-30 15:11:20'),(63,14,38,17,'2017-05-30 15:11:20','2017-05-30 15:11:20'),(64,14,39,18,'2017-05-30 15:11:20','2017-05-30 15:11:20'),(65,14,35,15,'2017-05-30 15:11:20','2017-05-30 15:11:20'),(66,15,47,11,'2017-05-30 15:12:40','2017-05-30 15:12:40'),(67,15,36,16,'2017-05-30 15:12:40','2017-05-30 15:12:40'),(68,15,46,13,'2017-05-30 15:12:40','2017-05-30 15:12:40'),(69,15,26,2,'2017-05-30 15:12:40','2017-05-30 15:12:40'),(70,15,35,15,'2017-05-30 15:12:40','2017-05-30 15:12:40'),(71,15,31,7,'2017-05-30 15:12:40','2017-05-30 15:12:40'),(72,15,40,19,'2017-05-30 15:12:40','2017-05-30 15:12:40'),(73,15,44,22,'2017-05-30 15:12:40','2017-05-30 15:12:40'),(74,16,47,11,'2017-05-30 15:13:38','2017-05-30 15:13:38'),(75,16,26,2,'2017-05-30 15:13:38','2017-05-30 15:13:38'),(76,16,35,15,'2017-05-30 15:13:38','2017-05-30 15:13:38'),(77,17,40,19,'2017-05-30 15:14:40','2017-05-30 15:14:40'),(78,17,46,13,'2017-05-30 15:14:40','2017-05-30 15:14:40'),(79,17,47,11,'2017-05-30 15:14:40','2017-05-30 15:14:40'),(80,17,34,10,'2017-05-30 15:14:40','2017-05-30 15:14:40'),(81,18,35,15,'2017-05-30 15:16:06','2017-05-30 15:16:06'),(82,18,32,8,'2017-05-30 15:16:06','2017-05-30 15:16:06'),(83,18,45,23,'2017-05-30 15:16:06','2017-05-30 15:16:06'),(84,18,29,5,'2017-05-30 15:16:06','2017-05-30 15:16:06'),(85,18,31,7,'2017-05-30 15:16:06','2017-05-30 15:16:06'),(86,18,28,4,'2017-05-30 15:16:06','2017-05-30 15:16:06'),(87,18,36,16,'2017-05-30 15:16:06','2017-05-30 15:16:06'),(88,19,26,2,'2017-05-30 15:16:55','2017-05-30 15:16:55'),(89,19,46,13,'2017-05-30 15:16:55','2017-05-30 15:16:55'),(90,19,39,18,'2017-05-30 15:16:55','2017-05-30 15:16:55'),(91,20,36,16,'2017-05-30 15:18:38','2017-05-30 15:18:38'),(92,20,46,14,'2017-05-30 15:18:38','2017-05-30 15:18:38'),(93,20,26,2,'2017-05-30 15:18:38','2017-05-30 15:18:38'),(94,20,47,11,'2017-05-30 15:18:38','2017-05-30 15:18:38'),(95,20,30,6,'2017-05-30 15:18:38','2017-05-30 15:18:38'),(96,20,31,7,'2017-05-30 15:18:38','2017-05-30 15:18:38'),(97,20,40,19,'2017-05-30 15:18:38','2017-05-30 15:18:38'),(98,20,43,21,'2017-05-30 15:18:38','2017-05-30 15:18:38'),(99,20,34,10,'2017-05-30 15:18:38','2017-05-30 15:18:38'),(100,21,47,12,'2017-05-30 15:19:39','2017-05-30 15:19:39'),(101,21,30,6,'2017-05-30 15:19:39','2017-05-30 15:19:39'),(102,21,43,21,'2017-05-30 15:19:39','2017-05-30 15:19:39'),(115,24,48,31,'2017-06-04 16:11:07','2017-06-04 16:11:07'),(116,24,35,15,'2017-06-04 16:11:27','2017-06-04 16:11:27'),(118,24,39,30,'2017-06-04 16:12:28','2017-06-04 16:12:28'),(119,24,29,29,'2017-06-04 16:12:40','2017-06-04 16:12:40'),(120,24,41,20,'2017-06-04 16:12:56','2017-06-04 16:12:56'),(122,24,42,20,'2017-06-04 16:13:32','2017-06-04 16:13:32'),(123,25,47,12,'2017-06-04 16:18:15','2017-06-04 16:18:15'),(124,25,33,9,'2017-06-04 16:18:27','2017-06-04 16:18:27'),(125,25,34,10,'2017-06-04 16:18:53','2017-06-04 16:18:53'),(126,25,40,19,'2017-06-04 16:19:03','2017-06-04 16:19:03'),(127,26,35,15,'2017-06-04 16:22:37','2017-06-04 16:22:37'),(128,26,49,15,'2017-06-04 16:22:56','2017-06-04 16:22:56'),(129,26,48,31,'2017-06-04 16:23:07','2017-06-04 16:23:07');
/*!40000 ALTER TABLE `attendance_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `break_event`
--

DROP TABLE IF EXISTS `break_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `break_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `break_event_day` date NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `break_event`
--

LOCK TABLES `break_event` WRITE;
/*!40000 ALTER TABLE `break_event` DISABLE KEYS */;
/*!40000 ALTER TABLE `break_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule_item`
--

DROP TABLE IF EXISTS `schedule_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schedule_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `scheduled_day` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `scheduled_start_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `scheduled_due_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(140) COLLATE utf8_unicode_ci NOT NULL,
  `session_name` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule_item`
--

LOCK TABLES `schedule_item` WRITE;
/*!40000 ALTER TABLE `schedule_item` DISABLE KEYS */;
INSERT INTO `schedule_item` VALUES (1,'Hétfő reggel kondi','1','08:15','09:15','Érd Aréna','Kondi Torna','2017-05-30 14:02:17','2017-05-30 13:55:16',NULL),(2,'Hétfő reggel pilates','1','09:30','10:30','Érd Aréna','Pilates','2017-05-30 14:02:49','2017-05-30 13:57:01',NULL),(3,'Szerda este kondi','3','17:15','18:15','Érd Aréna','Kondi Torna','2017-05-30 13:58:38','2017-05-30 13:58:38',NULL),(4,'Csütörtök reggel step','4','08:15','09:15','Érd Aréna','Step','2017-05-30 14:00:06','2017-05-30 14:00:06',NULL),(5,'Péntek este pilates','5','19:30','20:30','Érd Aréna','Pilates','2017-05-30 14:03:28','2017-05-30 14:01:05',NULL),(6,'Szombat reggel kondi','6','09:15','10:15','Érd Aréna','Kondi Torna','2017-05-30 14:01:39','2017-05-30 14:01:39',NULL);
/*!40000 ALTER TABLE `schedule_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_event`
--

DROP TABLE IF EXISTS `session_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_item_id` int(11) NOT NULL,
  `session_event_date` datetime NOT NULL,
  `session_fee_numbers_sold` int(11) DEFAULT NULL,
  `session_fee_revenue_sold` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E0D3B2719F057EEF` (`schedule_item_id`),
  CONSTRAINT `FK_E0D3B2719F057EEF` FOREIGN KEY (`schedule_item_id`) REFERENCES `schedule_item` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_event`
--

LOCK TABLES `session_event` WRITE;
/*!40000 ALTER TABLE `session_event` DISABLE KEYS */;
INSERT INTO `session_event` VALUES (1,3,'2017-05-03 14:41:00',12,9500,'2017-05-30 14:50:14','2017-05-30 14:43:17'),(2,4,'2017-05-04 14:51:00',2,1875,'2017-05-30 14:52:48','2017-05-30 14:52:48'),(3,5,'2017-05-05 14:53:00',0,0,'2017-05-30 14:53:27','2017-05-30 14:53:27'),(4,6,'2017-05-06 14:53:00',1,0,'2017-05-30 14:54:44','2017-05-30 14:54:44'),(5,1,'2017-05-08 14:54:00',3,875,'2017-05-30 14:56:22','2017-05-30 14:56:22'),(6,3,'2017-05-10 14:56:00',8,11000,'2017-05-30 15:05:07','2017-05-30 14:58:32'),(7,4,'2017-05-11 14:58:00',0,0,'2017-05-30 14:59:33','2017-05-30 14:59:33'),(8,6,'2017-05-13 14:59:00',3,3300,'2017-05-30 15:00:48','2017-05-30 15:00:44'),(9,1,'2017-05-15 15:01:00',2,1320,'2017-05-30 15:02:15','2017-05-30 15:02:15'),(10,2,'2017-05-15 15:02:00',1,0,'2017-05-30 15:02:48','2017-05-30 15:02:48'),(11,3,'2017-05-17 15:05:00',6,7300,'2017-05-30 15:08:20','2017-05-30 15:07:27'),(12,4,'2017-05-18 15:08:00',1,1320,'2017-05-30 15:09:34','2017-05-30 15:09:34'),(13,5,'2017-05-19 15:09:00',1,875,'2017-05-30 15:10:10','2017-05-30 15:10:10'),(14,6,'2017-05-20 15:10:00',2,1500,'2017-05-30 15:11:20','2017-05-30 15:11:20'),(15,1,'2017-05-22 15:11:00',1,0,'2017-05-30 15:12:40','2017-05-30 15:12:40'),(16,2,'2017-05-22 15:13:00',2,1500,'2017-05-30 15:13:42','2017-05-30 15:13:38'),(17,4,'2017-05-25 15:13:00',0,0,'2017-05-30 15:14:40','2017-05-30 15:14:40'),(18,5,'2017-05-26 15:14:00',3,3000,'2017-05-30 15:16:06','2017-05-30 15:16:06'),(19,6,'2017-05-27 15:16:00',4,3300,'2017-05-30 15:16:55','2017-05-30 15:16:55'),(20,1,'2017-05-29 15:17:00',2,1500,'2017-05-30 15:18:38','2017-05-30 15:18:38'),(21,2,'2017-05-29 15:18:00',3,3000,'2017-05-31 12:22:51','2017-05-30 15:19:39'),(24,3,'2017-05-31 16:07:00',4,3350,'2017-06-04 16:14:33','2017-06-04 16:09:20'),(25,4,'2017-06-01 16:15:00',0,0,'2017-06-04 16:19:03','2017-06-04 16:15:42'),(26,5,'2017-06-02 16:21:00',3,3250,'2017-06-04 16:23:22','2017-06-04 16:22:07');
/*!40000 ALTER TABLE `session_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription`
--

DROP TABLE IF EXISTS `subscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `date_start_date` datetime NOT NULL,
  `date_due_date` datetime NOT NULL,
  `extensions_count` int(11) NOT NULL,
  `attendance_count` int(11) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A3C664D37E3C61F9` (`owner_id`),
  CONSTRAINT `FK_A3C664D37E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user_account` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription`
--

LOCK TABLES `subscription` WRITE;
/*!40000 ALTER TABLE `subscription` DISABLE KEYS */;
INSERT INTO `subscription` VALUES (1,26,'2017-05-03 00:00:00','2017-06-03 23:59:00',0,4,5000,'2017-05-30 15:22:21','2017-05-30 14:21:50'),(2,26,'2017-05-20 00:00:00','2017-07-20 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:23:32'),(3,27,'2017-05-03 00:00:00','2017-06-03 23:59:00',0,4,5000,'2017-05-30 15:22:21','2017-05-30 14:25:06'),(4,28,'2017-05-10 00:00:00','2017-06-10 23:59:00',0,4,5000,'2017-06-03 20:54:38','2017-05-30 14:26:03'),(5,29,'2017-05-03 00:00:00','2017-06-03 23:59:00',0,4,5000,'2017-05-30 15:22:21','2017-05-30 14:26:48'),(6,30,'2017-05-03 00:00:00','2017-07-03 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:27:42'),(7,31,'2017-05-03 00:00:00','2017-07-03 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:28:15'),(8,32,'2017-05-03 00:00:00','2017-07-03 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:28:46'),(9,33,'2017-05-04 00:00:00','2017-07-04 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:29:25'),(10,34,'2017-05-03 00:00:00','2017-07-03 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:30:00'),(11,47,'2017-05-04 00:00:00','2017-07-04 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:30:27'),(12,47,'2017-05-29 00:00:00','2017-07-29 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:31:02'),(13,46,'2017-05-06 00:00:00','2017-07-06 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:31:43'),(14,46,'2017-05-29 00:00:00','2017-07-29 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:32:21'),(15,35,'2017-05-06 00:00:00','2017-07-06 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:32:55'),(16,36,'2017-05-08 00:00:00','2017-07-08 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:33:23'),(17,37,'2017-05-10 00:00:00','2017-07-10 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:34:13'),(18,39,'2017-05-10 00:00:00','2017-06-10 23:59:00',0,4,5000,'2017-06-03 20:54:38','2017-05-30 14:34:47'),(19,40,'2017-05-11 00:00:00','2017-07-10 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:35:24'),(20,42,'2017-05-17 00:00:00','2017-07-17 23:59:00',0,10,10000,'2017-06-04 15:49:48','2017-05-30 14:36:00'),(21,43,'2017-05-17 00:00:00','2017-07-17 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:36:25'),(22,44,'2017-05-18 00:00:00','2017-06-18 23:59:00',0,4,5000,'2017-06-04 15:24:54','2017-05-30 14:37:03'),(23,45,'2017-05-25 00:00:00','2017-07-25 23:59:00',0,10,10000,'2017-06-04 15:24:54','2017-05-30 14:37:45'),(29,29,'2017-05-31 00:00:00','2017-06-30 23:59:00',0,4,5000,'2017-06-04 16:04:43','2017-06-04 16:04:43'),(30,39,'2017-05-31 00:00:00','2017-06-30 23:59:00',0,4,5000,'2017-06-04 16:05:22','2017-06-04 16:05:22'),(31,48,'2017-05-31 00:00:00','2017-06-30 23:59:00',0,4,5000,'2017-06-04 16:06:32','2017-06-04 16:06:32');
/*!40000 ALTER TABLE `subscription` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_account`
--

DROP TABLE IF EXISTS `user_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(140) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(140) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `date_updated` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_253B48AE92FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_253B48AEC05FB297` (`confirmation_token`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_account`
--

LOCK TABLES `user_account` WRITE;
/*!40000 ALTER TABLE `user_account` DISABLE KEYS */;
INSERT INTO `user_account` VALUES (12,'Admin','Admin','adminuser','adminuser','ad@ad.com','ad@ad.com',1,NULL,'$2y$13$p3jFBraF/CxWm2bmEQACyufiy04kgz0Whfb/1t6hq2r5o5ogav3hm','2017-06-04 16:21:30',NULL,NULL,'a:1:{i:0;s:10:\"ROLE_ADMIN\";}','2017-06-04 16:21:30','2017-02-25 19:44:55',NULL,1),(26,'Marika','Élő','Élő Marika','élő marika','adele@ad.com','adele@ad.com',1,NULL,'$2y$13$fJUrQNQm5tS1IYAOxs.ug.MMQ4HEaqxZJR373sJAXZPg1usgUKctC','2017-06-04 15:16:21',NULL,NULL,'a:0:{}','2017-06-04 15:16:21','2017-05-30 14:05:17',NULL,0),(27,'Edit','Mészáros','Mészáros Edit','mészáros edit',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:05:42','2017-05-30 14:05:42',NULL,0),(28,'Vanda','Balczer','Szabó Balczer Vanda','szabó balczer vanda',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-06-03 15:45:36','2017-05-30 14:06:32',NULL,0),(29,'Zsófia','Varga','Varga Zsófi','varga zsófi',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:08:42','2017-05-30 14:08:42',NULL,0),(30,'Edit','Simonkovich','Simonkovich Edit','simonkovich edit',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:09:03','2017-05-30 14:09:03',NULL,0),(31,'Zsóka','Farkas','Farkas Zsóka','farkas zsóka',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:09:26','2017-05-30 14:09:26',NULL,0),(32,'Tünde','Tóth','Tóth Tündi','tóth tündi',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:09:46','2017-05-30 14:09:46',NULL,0),(33,'Mónika','Verner','Verner Móni','verner móni',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:10:06','2017-05-30 14:10:06',NULL,0),(34,'Ildikó','Budiás','Budiás Ildikó','budiás ildikó',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:10:30','2017-05-30 14:10:30',NULL,0),(35,'Ildikó','Kóczián','Kóczián Ildi','kóczián ildi',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:10:57','2017-05-30 14:10:57',NULL,0),(36,'Marika','Nyxerják','Nyerják Marika','nyerják marika',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:11:18','2017-05-30 14:11:18',NULL,0),(37,'Orsolya','Kőhalmi','Kőhalmi Orsi','kőhalmi orsi',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:12:29','2017-05-30 14:12:29',NULL,0),(38,'Eszter','Kőhalmi','Kőhalmi Eszti, Orsi lánya','kőhalmi eszti, orsi lánya',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:13:24','2017-05-30 14:13:01',NULL,0),(39,'Ilona','Kormanik','Kormanik Ili','kormanik ili',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:13:49','2017-05-30 14:13:49',NULL,0),(40,'Judit','Mészáros','Mészáros Judit','mészáros judit',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:14:11','2017-05-30 14:14:11',NULL,0),(41,'Andrea','Zsidákovich','Zsidákovich Andi','zsidákovich andi',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:14:33','2017-05-30 14:14:33',NULL,0),(42,'Zsuzsa','Szőllősy','Szőllősy Zsuzsi','szőllősy zsuzsi',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:14:58','2017-05-30 14:14:58',NULL,0),(43,'Irén','Miklós','Miklós Irén','miklós irén',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:15:17','2017-05-30 14:15:17',NULL,0),(44,'Ildikó','Pót','Pót Ildi','pót ildi',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:15:41','2017-05-30 14:15:41',NULL,0),(45,'Zsuzsa','Steinczinger','Steinczinger Zsuzsi','steinczinger zsuzsi',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:16:23','2017-05-30 14:16:23',NULL,0),(46,'Ildikó','Veres','Nyerják Veres Ildi','nyerják veres ildi',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:16:45','2017-05-30 14:16:45',NULL,0),(47,'Andrea','Fülöp','Fülöp Andika','fülöp andika',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-05-30 14:17:00','2017-05-30 14:17:00',NULL,0),(48,'Nóra','Dr. Nemes','Nemes Nóri','nemes nóri',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-06-04 16:05:56','2017-06-04 16:05:56',NULL,0),(49,'Heni','Kóczián','Kóczián Heni','kóczián heni',NULL,NULL,0,NULL,'not_set',NULL,NULL,NULL,'a:0:{}','2017-06-04 16:21:11','2017-06-04 16:21:11',NULL,0);
/*!40000 ALTER TABLE `user_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_contact`
--

DROP TABLE IF EXISTS `user_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_first_name` varchar(140) COLLATE utf8_unicode_ci NOT NULL,
  `contact_last_name` varchar(140) COLLATE utf8_unicode_ci NOT NULL,
  `contact_email` varchar(140) COLLATE utf8_unicode_ci NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_contact`
--

LOCK TABLES `user_contact` WRITE;
/*!40000 ALTER TABLE `user_contact` DISABLE KEYS */;
INSERT INTO `user_contact` VALUES (1,'Mária','Élőné','adele@ad.com','2017-05-30 16:17:01','2017-05-30 16:17:01','$2y$13$fJUrQNQm5tS1IYAOxs.ug.MMQ4HEaqxZJR373sJAXZPg1usgUKctC',NULL);
/*!40000 ALTER TABLE `user_contact` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-06-04 16:47:14
