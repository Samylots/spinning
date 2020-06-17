
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

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(25) DEFAULT NULL,
  `units` float DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities`
--

LOCK TABLES `activities` WRITE;
/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
INSERT INTO `activities` VALUES (1,'Spinning',1,1),(2,'Spin/Rame*',1.5,1),(3,'Rame',1,1),(4,'ESSENTRICS',1,1),(5,'Rame/Spin*',1.5,1),(6,'Pilates/Stretching 1',1,1),(7,'Pilates/Stretching 2',1,1),(8,'Rame (groupe privé)',1,1),(9,'Pilates/Stretching',1,1),(10,'Spinning (privé)',1,1);
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pictures`
--

DROP TABLE IF EXISTS `pictures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pictures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `url` varchar(50) DEFAULT NULL,
  `slider` bool,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pictures`
--

LOCK TABLES `pictures` WRITE;
/*!40000 ALTER TABLE `pictures` DISABLE KEYS */;
INSERT INTO `pictures` VALUES (1,'spinning',NULL,'spinning.jpg',false,1),(2,'rame',NULL,'rame.jpg',false,1),
(3,'pila',NULL,'pila.jpg',false,1);
/*!40000 ALTER TABLE `pictures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_types`
--

DROP TABLE IF EXISTS `activity_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL,
  `places` tinyint(2) DEFAULT NULL,
  `color` varchar(10) DEFAULT NULL,
  `description` longtext,
  `picture_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_activity_type_picture` (`picture_id`),
  CONSTRAINT `FK_activity_type_picture` FOREIGN KEY (`picture_id`) REFERENCES `pictures` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_types`
--

LOCK TABLES `activity_types` WRITE;
/*!40000 ALTER TABLE `activity_types` DISABLE KEYS */;
INSERT INTO `activity_types` VALUES (1,'Spinning',18,'#a10000','Spinnig (Cardio-Vélo): Entraînement Cardio-vasculaire 
par intervalles sur vélo stationnaire en groupe sur une musique entraînante. Favorise aussi le renforcement musculaire 
par différents positions et exercices.',1,1),
(2,'Rame',12,'#007ea1','Rame (Cardio-Rameur): Entraînement sur rameur en 
groupe avec musique. Entraînement cardio-vasculaire et musculaire, sollicite Tous le corps + favorise une bonne posture 
et développe la flexibilité.',2,1),
(3,'ESSENTRICS',10,'#d1d1d1','Force, flexibilité, mobilité, agilité, guérison, équilibrer, 
renforcer et délier le corps en plus de libérer les tensions par des mouvements fluides et continu. Apprenti entraîneur en 
formation pour en savoir plus: Essentrics.com',NULL,1),
(4,'Pilates/Stretching',8,'#00c40d','Pilates/Stretching: Entraînement 
en groupe sur une musique apaisante. Par différentes positions et/ou à l\'aide de différents accessoires, le pilates permet 
de renforcer les muscles, favorise une bonne posture et développe la flexibilité.',NULL,1),
(5,'Spinning (privé)',18,'#8500ad',NULL,NULL,1),
(6,'Spinning (Privé)',18,'#9400cf',NULL,NULL,0),
(7,'Rame (Privé)',12, '#33eeff',NULL,NULL,1);
/*!40000 ALTER TABLE `activity_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activities_activity_types`
--

DROP TABLE IF EXISTS `activities_activity_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activities_activity_types` (
  `activity_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `time` timestamp NOT NULL,
  PRIMARY KEY (`activity_type_id`,`activity_id`),
  KEY `FK_exercises_activity` (`activity_id`),
  CONSTRAINT `FK_exercises_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`),
  CONSTRAINT `FK_exercises_activity_type` FOREIGN KEY (`activity_type_id`) REFERENCES `activity_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities_activity_types`
--

LOCK TABLES `activities_activity_types` WRITE;
/*!40000 ALTER TABLE `activities_activity_types` DISABLE KEYS */;
INSERT INTO `activities_activity_types` VALUES (1,1,'2016-03-25 04:32:06'),(1,2,'2016-03-25 04:32:06'),
(1,5,'2016-03-25 07:50:41'),(2,2,'2016-03-25 08:29:48'),(2,3,'2016-03-25 07:49:48'),(2,5,'2016-03-25 07:50:39'),
(2,8,'2016-03-25 07:53:03'),(3,4,'2016-03-25 07:50:09'),(4,6,'2016-03-25 07:51:57'),(4,7,'2016-03-25 07:52:19'),
(4,9,'2016-03-25 08:17:36'),(5,10,'2016-03-26 10:46:57');
/*!40000 ALTER TABLE `activities_activity_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coache_presentations`
--

DROP TABLE IF EXISTS `coache_presentations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coache_presentations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `description` longtext,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `days`
--

DROP TABLE IF EXISTS `days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `days`
--

LOCK TABLES `days` WRITE;
/*!40000 ALTER TABLE `days` DISABLE KEYS */;
INSERT INTO `days` VALUES (1,'Dimanche'),(2,'Lundi'),(3,'Mardi'),(4,'Mercredi'),(5,'Jeudi'),(6,'Vendredi'),(7,'Samedi');
/*!40000 ALTER TABLE `days` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `subscription_places_date` date NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  `total_weeks` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member_types`
--

DROP TABLE IF EXISTS `member_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_types`
--

LOCK TABLES `member_types` WRITE;
/*!40000 ALTER TABLE `member_types` DISABLE KEYS */;
INSERT INTO `member_types` VALUES (1,'membre',1),(2,'entraîneur',1),(3,'administrateur',1);
/*!40000 ALTER TABLE `member_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` tinytext,
  `postal_code` varchar(7) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `nickname` varchar(20) DEFAULT NULL,
  `password` blob,
  `active` tinyint(1) DEFAULT '1',
  `last_activity` timestamp NULL DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `picture_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_member_type` (`type_id`),
  KEY `FK_member_picture` (`picture_id`),
  CONSTRAINT `FK_member_picture` FOREIGN KEY (`picture_id`) REFERENCES `pictures` (`id`),
  CONSTRAINT `FK_member_type` FOREIGN KEY (`type_id`) REFERENCES `member_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (1,'Hélène','Lessard','heless@hotmail.com','4182151692','1980-04-09','2','G5Y3B8','2016-04-09 08:00:00',
'HeLess','$2y$10$Lex51gQIWb7Aoj2fJp6w3utpkyWd409Pct2v8OeY9dOrLDi7cxZDS',1,'2016-04-21 22:18:10',3,NULL),(2,'Samuel','Beaudoin',
'samapoil9@hotmail.com','4186255260','1996-05-22','1','G0R1S0','2016-04-09 08:00:00','Samylots',
'$2y$10$G.Hop58XwfvGCilopGDce.RZxL0Q5Sf4pKp09Vk7VojvKz7WPtRgS',1,'2016-04-09 08:00:00',2,NULL),(3,'Mario','Dufresne',
'mdufresne@cegepba.qc.ca','4181111111','2011-01-01','1','G5Y 3B8','2016-04-21 13:00:26','mdufresne',
'$2y$10$sEFzLRHAj26kd.G69yIcmu68zybFzlYgER22ka379doEp2N/MjCHy',1,'2016-04-21 14:42:51',1,NULL),(4,'Yvan','Paquin',
'ypaquin@cegepba.qc.ca','4182222222','2011-01-01','1','G5Y3B8','2016-04-21 13:02:05','ypaquin',
'$2y$10$oMD5/WAK/CCWwBmgPbagZ.KDRP2r8vbOaoOjBVQ2h/ngfvclUymte',1,'2016-04-21 13:02:20',1,NULL),(5,'Luce','Thibaudeau',
'lthibaudeau@cegepba.qc.ca','4183333333','2011-01-01','2','G5Y3B8','2016-04-21 13:03:21','lthibaudeau',
'$2y$10$DVFOT0qfbXlHXc80Yj0mheeSJTLEzUgLVpTSyV9/pW1CKvZ4VopPW',1,'2016-04-21 13:03:30',1,NULL),(6,'Claude','Bernard',
'cbernard@cegepba.qc.ca','4185555555','2011-01-01','1','G5Y1S0','2016-04-21 14:09:02','cbernard',
'$2y$10$hYu8bkZJ.89WHqisGNcz8uK1SbbXIkIZ5m40ehxiunYrqhMQGamMS',1,'2016-04-21 22:03:14',1,NULL),(7,'Marthe','Goulet',
'mgoulet@cegepba.qc.ca','4186666666','2011-01-01','2','G5Y1S0','2016-04-21 14:44:19','mgoulet',
'$2y$10$7M5seEgrqirhnLbIKW5Pjud6DifcexZuBlRqqC8ar2jirC0sRFOMG',1,NULL,1,NULL);
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(25) NOT NULL,
  `description` varchar(25) DEFAULT NULL,
  `minimumAge` int(11) DEFAULT NULL,
  `type` varchar(1) DEFAULT NULL,
  `value` float DEFAULT NULL,
  `start` date NOT NULL,
  `expiration` date NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_discount_member` (`member_id`),
  KEY `FK_discount_company` (`company_id`),
  CONSTRAINT `FK_discount_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `FK_discount_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subscription_types`
--

DROP TABLE IF EXISTS `subscription_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscription_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registration_deadline` time(5) DEFAULT NULL,
  `cancellation_deadline` time(5) DEFAULT NULL,
  `meetings_allowed` tinyint(2) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `limit_registration_advance` int(11) DEFAULT NULL,
  `weekly` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription_types`
--

LOCK TABLES `subscription_types` WRITE;
/*!40000 ALTER TABLE `subscription_types` DISABLE KEYS */;
INSERT INTO `subscription_types` VALUES (1,'00:15:00.00000','02:00:00.00000',1,1,2,1),(2,'00:15:00.00000','02:00:00.00000',2,1,2,1),
(3,'00:15:00.00000','02:00:00.00000',3,1,2,1),(4,'00:15:00.00000','02:00:00.00000',4,1,2,1),
(5,'00:15:00.00000','02:00:00.00000',5,1,2,NULL),(6,'00:15:00.00000','02:00:00.00000',10,1,2,NULL),
(7,'00:15:00.00000','02:00:00.00000',20,1,2,NULL),(8,'00:15:00.00000','02:00:00.00000',30,1,2,NULL),
(9,'00:15:00.00000','02:00:00.00000',1,1,2,NULL),(10,'00:15:00.00000','02:00:00.00000',2,1,2,NULL);
/*!40000 ALTER TABLE `subscription_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription_receipts`
--

DROP TABLE IF EXISTS `subscription_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscription_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meetings` float NOT NULL,
  `meetings_left` float NOT NULL DEFAULT 0,
  `price` float NOT NULL,
  `tps` float NOT NULL,
  `tvq` float NOT NULL,
  `purchase_date` timestamp NOT NULL,
  `expiration` timestamp NOT NULL,
  `cancellation_deadline` timestamp NULL DEFAULT NULL,
  `paid_date` timestamp NULL DEFAULT NULL,
  `refund_date` timestamp NULL DEFAULT NULL,
  `refund_value` float DEFAULT NULL,
  `member_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_receipt_member` (`member_id`),
  KEY `FK_receipt_type` (`type_id`),
  KEY `FK_receipt_discount` (`discount_id`),
  KEY `FK_receipt_session` (`session_id`),
  CONSTRAINT `FK_receipt_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`),
  CONSTRAINT `FK_receipt_discount` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`),
  CONSTRAINT `FK_receipt_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  CONSTRAINT `FK_receipt_type` FOREIGN KEY (`type_id`) REFERENCES `subscription_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `periods`
--

DROP TABLE IF EXISTS `periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start` time(5) DEFAULT NULL,
  `end` time(5) DEFAULT NULL,
  `day_id` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  `session_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `special` bool NOT NULL DEFAULT FALSE,
  `subscription_places` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_period_session` (`session_id`),
  KEY `FK_period_activity` (`activity_id`),
  KEY `FK_period_day` (`day_id`),
  CONSTRAINT `FK_period_day` FOREIGN KEY (`day_id`) REFERENCES `days` (`id`),
  CONSTRAINT `FK_period_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`),
  CONSTRAINT `FK_period_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meetings`
--

DROP TABLE IF EXISTS `meetings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meetings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week` tinyint(2) DEFAULT NULL,
  `start` time(5) DEFAULT NULL,
  `end` time(5) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `period_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_meeting_period` (`period_id`),
  CONSTRAINT `FK_meeting_period` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=532 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meetings_subscription_receipts`
--

DROP TABLE IF EXISTS `meetings_subscription_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meetings_subscription_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscription_receipt_id` int(11) NOT NULL,
  `meeting_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `waiting` tinyint(1) DEFAULT '0',
  `registred` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`,`meeting_id`,`subscription_receipt_id`),
  KEY `FK_registrations_meeting` (`meeting_id`),
  KEY `FK_registrations_subscription_receipt` (`subscription_receipt_id`),
  CONSTRAINT `FK_registrations_subscription_receipt` FOREIGN KEY (`subscription_receipt_id`) REFERENCES `subscription_receipts` (`id`),
  CONSTRAINT `FK_registrations_meeting` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `credits`
--

DROP TABLE IF EXISTS `credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `credits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_credits` int(11) DEFAULT '0',
  `subscription_receipt_id` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_credits_receipt` (`subscription_receipt_id`),
  CONSTRAINT `FK_credits_receipt` FOREIGN KEY (`subscription_receipt_id`) REFERENCES `subscription_receipts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `members_meetings`
--

DROP TABLE IF EXISTS `members_meetings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members_meetings` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `meeting_id` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`member_id`,`meeting_id`),
  KEY `FK_supervisors_neeting` (`meeting_id`),
  CONSTRAINT `FK_supervisors_neeting` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`),
  CONSTRAINT `FK_supervisors_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(25) DEFAULT NULL,
  `description` tinytext,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `members_permissions`
--

DROP TABLE IF EXISTS `members_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members_permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `answer` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`permission_id`,`member_id`),
  KEY `FK_authorisations_member` (`member_id`),
  CONSTRAINT `FK_authorisations_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  CONSTRAINT `FK_authorisations_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `qaaps`
--

DROP TABLE IF EXISTS `qaaps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qaaps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_name` varchar(50) DEFAULT NULL,
  `answer` tinyint(1) DEFAULT NULL,
  `expiration` timestamp NULL DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `members_qaaps`
--

DROP TABLE IF EXISTS `members_qaaps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members_qaaps` (
  `qaap_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`qaap_id`,`member_id`),
  KEY `FK_qaap_answers_member` (`member_id`),
  CONSTRAINT `FK_qaap_answers_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  CONSTRAINT `FK_qaap_answers_qaap` FOREIGN KEY (`qaap_id`) REFERENCES `qaaps` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` longtext,
  `title` varchar(50) DEFAULT NULL,
  `url` varchar(25) DEFAULT NULL,
  `total_views` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menuitems`
--

DROP TABLE IF EXISTS `menuitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menuitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `sequence` int(3) DEFAULT NULL,
  `page_id` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_menu_page` (`page_id`),
  CONSTRAINT `FK_menu_page` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news_types`
--

DROP TABLE IF EXISTS `news_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` longtext,
  `date` timestamp NULL DEFAULT NULL,
  `type_id` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_news_type` (`type_id`),
  CONSTRAINT `FK_news_type` FOREIGN KEY (`type_id`) REFERENCES `news_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `periods_subscription_receipts`
--

DROP TABLE IF EXISTS `periods_subscription_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periods_subscription_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `period_id` int(11) NOT NULL,
  `subscription_receipt_id` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`,`period_id`,`subscription_receipt_id`),
  KEY `FK_subscriptions_period` (`period_id`),
  KEY `FK_subscriptions_receipt` (`subscription_receipt_id`),
  CONSTRAINT `FK_subscriptions_receipt` FOREIGN KEY (`subscription_receipt_id`) REFERENCES `subscription_receipts` (`id`),
  CONSTRAINT `FK_subscriptions_period` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `discounts_subscription_types`
--

DROP TABLE IF EXISTS `discounts_subscription_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discounts_subscription_types` (
  `subscription_type_id` int(11) NOT NULL,
  `discount_id` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`discount_id`,`subscription_type_id`),
  KEY `FK_discounts_subscription_type` (`subscription_type_id`),
  CONSTRAINT `FK_discounts_session` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`),
  CONSTRAINT `FK_discounts_subscription_type` FOREIGN KEY (`subscription_type_id`) REFERENCES `subscription_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions_subscription_types`
--

DROP TABLE IF EXISTS `sessions_subscription_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions_subscription_types` (
  `subscription_type_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `rate` float DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`session_id`,`subscription_type_id`),
  KEY `FK_session_rates_subscription_type` (`subscription_type_id`),
  CONSTRAINT `FK_session_rates_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`),
  CONSTRAINT `FK_session_rates_subscription_type` FOREIGN KEY (`subscription_type_id`) REFERENCES `subscription_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxe_types`
--

DROP TABLE IF EXISTS `taxe_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxe_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxe_types`
--

LOCK TABLES `taxe_types` WRITE;
/*!40000 ALTER TABLE `taxe_types` DISABLE KEYS */;
INSERT INTO `taxe_types` VALUES (1,'TPS',1),(2,'TVQ',1),(3,'FORFAITS MIXTES',1);
/*!40000 ALTER TABLE `taxe_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxes`
--

DROP TABLE IF EXISTS `taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `percentage_taxe` float DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `taxe_type_id` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_taxe_type` (`taxe_type_id`),
  CONSTRAINT `FK_taxe_type` FOREIGN KEY (`taxe_type_id`) REFERENCES `taxe_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxes`
--

LOCK TABLES `taxes` WRITE;
/*!40000 ALTER TABLE `taxes` DISABLE KEYS */;
INSERT INTO `taxes` VALUES (1,5,'2008-01-01 05:00:00',1,1),(2,9.975,'2013-01-01 05:00:00',2,1),(3,25,'2016-01-01 05:00:00',3,1);
/*!40000 ALTER TABLE `taxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `url` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

