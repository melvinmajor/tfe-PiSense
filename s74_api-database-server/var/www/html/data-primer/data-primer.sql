-- MySQL dump 10.16  Distrib 10.1.16-MariaDB, for osx10.11 (x86_64)
--
-- Host: localhost    Database: data-primer
-- ------------------------------------------------------
-- Server version	10.1.16-MariaDB

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
-- Table structure for table `acct_contact`
--

DROP TABLE IF EXISTS `acct_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acct_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('client','supplier') DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `system_id` int(11) NOT NULL,
  `contact_info_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_contact_system1_idx` (`system_id`),
  KEY `fk_contact_contact_info1_idx` (`contact_info_id`),
  CONSTRAINT `fk_contact_contact_info1` FOREIGN KEY (`contact_info_id`) REFERENCES `acct_contact_info` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_contact_system1` FOREIGN KEY (`system_id`) REFERENCES `acct_system` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acct_contact`
--

LOCK TABLES `acct_contact` WRITE;
/*!40000 ALTER TABLE `acct_contact` DISABLE KEYS */;
INSERT INTO `acct_contact` VALUES (9,'client','Plubming Inc','suspended',14,27),(10,'client','System Restore','suspended',14,28),(11,'client','Testing test','suspended',14,29);
/*!40000 ALTER TABLE `acct_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acct_contact_info`
--

DROP TABLE IF EXISTS `acct_contact_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acct_contact_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address_1` varchar(255) DEFAULT NULL,
  `address_2` varchar(255) DEFAULT NULL,
  `address_3` varchar(255) DEFAULT NULL,
  `country_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_contact_info_city1_idx` (`country_id`),
  CONSTRAINT `fk_contact_info_city1` FOREIGN KEY (`country_id`) REFERENCES `acct_country` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acct_contact_info`
--

LOCK TABLES `acct_contact_info` WRITE;
/*!40000 ALTER TABLE `acct_contact_info` DISABLE KEYS */;
INSERT INTO `acct_contact_info` VALUES (25,'Street',NULL,NULL,2),(26,'Some Street',NULL,NULL,5),(27,NULL,NULL,NULL,1),(28,NULL,NULL,NULL,2),(29,NULL,NULL,NULL,2),(31,NULL,NULL,NULL,1),(32,'street 49',NULL,NULL,1);
/*!40000 ALTER TABLE `acct_contact_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acct_country`
--

DROP TABLE IF EXISTS `acct_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acct_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acct_country`
--

LOCK TABLES `acct_country` WRITE;
/*!40000 ALTER TABLE `acct_country` DISABLE KEYS */;
INSERT INTO `acct_country` VALUES (1,'UK'),(2,'Latvia'),(3,'Iceland'),(4,'Germany'),(5,'India');
/*!40000 ALTER TABLE `acct_country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acct_document`
--

DROP TABLE IF EXISTS `acct_document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acct_document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_no` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `type` enum('invoice','payment') DEFAULT NULL,
  `contact_id` int(11) NOT NULL,
  `is_archived` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_document_contact1_idx` (`contact_id`),
  CONSTRAINT `fk_document_contact1` FOREIGN KEY (`contact_id`) REFERENCES `acct_contact` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acct_document`
--

LOCK TABLES `acct_document` WRITE;
/*!40000 ALTER TABLE `acct_document` DISABLE KEYS */;
INSERT INTO `acct_document` VALUES (3,NULL,'2016-07-10','invoice',9,NULL),(4,NULL,'2016-07-10','invoice',9,NULL),(5,NULL,'2016-07-10','payment',9,NULL);
/*!40000 ALTER TABLE `acct_document` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acct_invoice`
--

DROP TABLE IF EXISTS `acct_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acct_invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `purchase` decimal(8,2) DEFAULT NULL,
  `shipping` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_invoice_document1_idx` (`document_id`),
  CONSTRAINT `fk_invoice_document1` FOREIGN KEY (`document_id`) REFERENCES `acct_document` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acct_invoice`
--

LOCK TABLES `acct_invoice` WRITE;
/*!40000 ALTER TABLE `acct_invoice` DISABLE KEYS */;
INSERT INTO `acct_invoice` VALUES (1,3,10.00,5.00),(2,4,30.00,0.00);
/*!40000 ALTER TABLE `acct_invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acct_payment`
--

DROP TABLE IF EXISTS `acct_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acct_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payment_document1_idx` (`document_id`),
  CONSTRAINT `fk_payment_document1` FOREIGN KEY (`document_id`) REFERENCES `acct_document` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acct_payment`
--

LOCK TABLES `acct_payment` WRITE;
/*!40000 ALTER TABLE `acct_payment` DISABLE KEYS */;
INSERT INTO `acct_payment` VALUES (1,5,30.00);
/*!40000 ALTER TABLE `acct_payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acct_system`
--

DROP TABLE IF EXISTS `acct_system`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acct_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_user_id` int(11) NOT NULL,
  `legal_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_system_user1_idx` (`owner_user_id`),
  CONSTRAINT `fk_system_user1` FOREIGN KEY (`owner_user_id`) REFERENCES `acct_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acct_system`
--

LOCK TABLES `acct_system` WRITE;
/*!40000 ALTER TABLE `acct_system` DISABLE KEYS */;
INSERT INTO `acct_system` VALUES (13,11,'Microsoft'),(14,10,'Google');
/*!40000 ALTER TABLE `acct_system` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acct_user`
--

DROP TABLE IF EXISTS `acct_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acct_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact_info_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_contact_info1_idx` (`contact_info_id`),
  CONSTRAINT `fk_user_contact_info1` FOREIGN KEY (`contact_info_id`) REFERENCES `acct_contact_info` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acct_user`
--

LOCK TABLES `acct_user` WRITE;
/*!40000 ALTER TABLE `acct_user` DISABLE KEYS */;
INSERT INTO `acct_user` VALUES (10,'john',NULL,25),(11,'rakesh','some email',26),(12,'test12344',NULL,32);
/*!40000 ALTER TABLE `acct_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inv_client`
--

DROP TABLE IF EXISTS `inv_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_client` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inv_client`
--

LOCK TABLES `inv_client` WRITE;
/*!40000 ALTER TABLE `inv_client` DISABLE KEYS */;
INSERT INTO `inv_client` VALUES (45,'Pear Company 33');
/*!40000 ALTER TABLE `inv_client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inv_invoice`
--

DROP TABLE IF EXISTS `inv_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_invoice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ref` varchar(255) DEFAULT NULL,
  `inv_client_id` int(11) DEFAULT NULL,
  `created` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inv_invoice`
--

LOCK TABLES `inv_invoice` WRITE;
/*!40000 ALTER TABLE `inv_invoice` DISABLE KEYS */;
INSERT INTO `inv_invoice` VALUES (68,'TBL1',45,'2016-10-26'),(69,'CHR2',45,'2016-10-26');
/*!40000 ALTER TABLE `inv_invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inv_invoice_line`
--

DROP TABLE IF EXISTS `inv_invoice_line`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_invoice_line` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `inv_invoice_id` int(11) DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inv_invoice_line`
--

LOCK TABLES `inv_invoice_line` WRITE;
/*!40000 ALTER TABLE `inv_invoice_line` DISABLE KEYS */;
INSERT INTO `inv_invoice_line` VALUES (125,68,'table',2,10.50),(126,68,'chair',10,3.25),(127,69,'chair',3,3.25),(128,69,'book',3,2.00);
/*!40000 ALTER TABLE `inv_invoice_line` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-10-26 13:09:43
