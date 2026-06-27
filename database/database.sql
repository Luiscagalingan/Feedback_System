-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: test
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_teachers`
--

DROP TABLE IF EXISTS `academic_teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_teachers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_teachers`
--

LOCK TABLES `academic_teachers` WRITE;
/*!40000 ALTER TABLE `academic_teachers` DISABLE KEYS */;
INSERT INTO `academic_teachers` VALUES (1,'dean','dean@gmail.com','Deantan1','2025-10-23 12:35:36');
/*!40000 ALTER TABLE `academic_teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'23-00000','Administrator','Admin1','2025-10-19 09:34:49');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facility_feedback`
--

DROP TABLE IF EXISTS `facility_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facility_feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(45) DEFAULT NULL,
  `question_name` varchar(45) DEFAULT NULL,
  `classroom_cleanliness` decimal(50,0) DEFAULT '0',
  `hallway_cleanliness` decimal(50,0) DEFAULT '0',
  `facility_satisfaction` decimal(50,0) DEFAULT '0',
  `positive_feedback` text,
  `negative_feedback` text,
  `answer_text` text,
  `review_result` varchar(45) DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facility_feedback`
--

LOCK TABLES `facility_feedback` WRITE;
/*!40000 ALTER TABLE `facility_feedback` DISABLE KEYS */;
INSERT INTO `facility_feedback` VALUES (1,'anonymous',NULL,5,4,4,'','','This is slow and Ugly','Negative','2025-11-13 14:33:07'),(2,'anonymous',NULL,5,2,3,'','','This is something','Positive','2025-11-13 15:32:57');
/*!40000 ALTER TABLE `facility_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback_summary`
--

DROP TABLE IF EXISTS `feedback_summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedback_summary` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `positive_feedback_percentage` decimal(5,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback_summary`
--

LOCK TABLES `feedback_summary` WRITE;
/*!40000 ALTER TABLE `feedback_summary` DISABLE KEYS */;
INSERT INTO `feedback_summary` VALUES (1,'Positive Feedback',85.50),(2,'Facilities Feedback',78.25),(3,'Neutral Feedback',10.00),(4,'Negative Feedback',4.25);
/*!40000 ALTER TABLE `feedback_summary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guard_feedback`
--

DROP TABLE IF EXISTS `guard_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guard_feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `question_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `positive_feedback_percentage` decimal(5,2) DEFAULT '0.00',
  `neutral_feedback_percentage` decimal(5,2) DEFAULT '0.00',
  `negative_feedback_percentage` decimal(5,2) DEFAULT '0.00',
  `answer_text` text COLLATE utf8mb4_general_ci,
  `review_result` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guard_feedback`
--

LOCK TABLES `guard_feedback` WRITE;
/*!40000 ALTER TABLE `guard_feedback` DISABLE KEYS */;
INSERT INTO `guard_feedback` VALUES (1,'23-01039','General Security Experience',0.00,0.00,0.00,'he',NULL,'2025-10-13 14:01:44'),(2,'20-00389','General Security Experience',0.00,0.00,0.00,'This is lovely',NULL,'2025-11-13 14:29:16'),(3,'20-00389','General Security Experience',0.00,0.00,0.00,'This is lovely','Positive','2025-11-13 14:33:07'),(4,'20-00389','General Security Experience',0.00,0.00,0.00,'This is slow and ugly','Negative','2025-11-13 15:31:59');
/*!40000 ALTER TABLE `guard_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `learning_feedback`
--

DROP TABLE IF EXISTS `learning_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `learning_feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `question_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `positive_feedback_percentage` decimal(5,2) DEFAULT '0.00',
  `neutral_feedback_percentage` decimal(5,2) DEFAULT '0.00',
  `negative_feedback_percentage` decimal(5,2) DEFAULT '0.00',
  `answer_text` text COLLATE utf8mb4_general_ci,
  `review_result` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `learning_feedback_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `learning_feedback`
--

LOCK TABLES `learning_feedback` WRITE;
/*!40000 ALTER TABLE `learning_feedback` DISABLE KEYS */;
INSERT INTO `learning_feedback` VALUES (1,'23-01039','General Learning Experience',85.67,10.33,4.00,'I like how the professor uses practical examples.','Positive','2025-11-12 14:49:49'),(2,'23-01022','General Learning Experience',70.00,20.00,10.00,'Class is okay but needs better pacing.',NULL,'2025-10-13 13:26:41'),(3,'23-01021','General Learning Experience',90.00,5.00,5.00,'Everything is explained clearly.',NULL,'2025-10-13 13:26:41'),(4,'23-01020','General Learning Experience',60.00,25.00,15.00,'Sometimes lessons are confusing, but still fine.',NULL,'2025-10-13 13:26:41'),(5,'23-01039','General Learning Experience',100.00,0.00,0.00,'he',NULL,'2025-10-13 13:27:57'),(6,'23-01039','General Learning Experience',100.00,0.00,0.00,'he',NULL,'2025-10-13 13:28:16'),(7,'23-01039','General Learning Experience',100.00,0.00,0.00,'',NULL,'2025-10-13 13:39:08'),(8,'23-01039','General Learning Experience',100.00,0.00,0.00,'',NULL,'2025-10-23 15:47:54'),(9,'23-01039','General Learning Experience',100.00,0.00,0.00,'asd',NULL,'2025-11-11 09:32:44'),(10,'20-00389','General Learning Experience',50.00,0.00,33.33,'asdasda',NULL,'2025-11-11 11:19:10'),(11,'20-00389','General Learning Experience',100.00,0.00,0.00,'asdasda','Neutral','2025-11-12 14:37:07'),(12,'20-00389','General Learning Experience',26.67,0.00,66.67,'This is Lovely','Neutral','2025-11-13 12:45:16'),(13,'20-00389','General Learning Experience',50.00,0.00,33.33,'This is Lovely','Neutral','2025-11-13 14:04:49'),(14,'20-00389','General Learning Experience',66.67,0.00,0.00,'this is lovely','Neutral','2025-11-13 14:07:08'),(15,'20-00389','General Learning Experience',50.00,0.00,33.33,'this is lovely','Neutral','2025-11-13 14:10:25'),(16,'20-00389','General Learning Experience',100.00,0.00,0.00,'This is lovely','Neutral','2025-11-13 14:11:40'),(17,'20-00389','General Learning Experience',50.00,0.00,33.33,'This is lovely','Neutral','2025-11-13 14:14:52'),(18,'20-00389','General Learning Experience',50.00,0.00,33.33,'This is lovely','Neutral','2025-11-13 14:15:44'),(19,'20-00389','General Learning Experience',50.00,0.00,33.33,'This is lovely','Neutral','2025-11-13 14:16:45'),(20,'20-00389','General Learning Experience',100.00,0.00,0.00,'this is lovely','Neutral','2025-11-13 14:17:16'),(21,'20-00389','General Learning Experience',66.67,0.00,33.33,'this is lovely','Neutral','2025-11-13 14:18:03'),(22,'20-00389','General Learning Experience',100.00,0.00,0.00,'This is lovely','Neutral','2025-11-13 14:19:01'),(23,'20-00389','General Learning Experience',83.33,0.00,0.00,'This is lovely','Neutral','2025-11-13 14:23:03'),(24,'20-00389','General Learning Experience',66.67,0.00,33.33,'This is lovely','Neutral','2025-11-13 14:25:08'),(25,'20-00389','General Learning Experience',83.33,0.00,0.00,'This is lovely','Positive','2025-11-13 14:25:53'),(26,'20-00389','General Learning Experience',100.00,0.00,0.00,'This is slow and Ugly','Negative','2025-11-13 15:31:33'),(27,'20-00389','General Learning Experience',50.00,0.00,33.33,'Maganda yung system','Positive','2025-11-13 15:33:39');
/*!40000 ALTER TABLE `learning_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `non_academic_teachers`
--

DROP TABLE IF EXISTS `non_academic_teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `non_academic_teachers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `non_academic_teachers`
--

LOCK TABLES `non_academic_teachers` WRITE;
/*!40000 ALTER TABLE `non_academic_teachers` DISABLE KEYS */;
INSERT INTO `non_academic_teachers` VALUES (1,'lowes','lowes@gmail.com','$2y$10$mQSBpLuGnpf9n2YV2dMzKulRaq1LDQMKtDSJkiY1TANEgl8UHmAMW','2025-10-24 09:13:50'),(2,'test','test@gmail.com','admin','2025-10-24 09:13:50');
/*!40000 ALTER TABLE `non_academic_teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `student_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `program` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `section` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `otp` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'23-01039','Luis Cagalingan','BSIT','BSIT 3D','cagalinganluis@gmail.com','Cagalingan17','2025-10-12 13:21:45','956090'),(2,'23-01022','Lans Alonzo','BSIT','BSIT 3D','alonzolans@gmail.com','Alonzo123','2025-10-13 13:02:26',NULL),(3,'23-01021','Maverick Mabingnay','BSIT','BSIT 3D','mabingnaymav@gmail.com','Mabingnay123','2025-10-13 13:04:22',NULL),(4,'23-01020','Kathleen Abalos','BSIT','BSIT 3D','abaloskath@gmail.com','Abalos123','2025-10-13 13:05:50',NULL),(11,'20-00389','test','BSIT','BSIT 4A','test@gmail.com','admin','2025-10-13 13:05:50',NULL);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_feedback`
--

DROP TABLE IF EXISTS `test_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(45) DEFAULT NULL,
  `review` varchar(45) DEFAULT NULL,
  `comment` text,
  `comment_review` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_feedback`
--

LOCK TABLES `test_feedback` WRITE;
/*!40000 ALTER TABLE `test_feedback` DISABLE KEYS */;
INSERT INTO `test_feedback` VALUES (12,'20-00389','positive','I love how the professor uses real world examples','Positive'),(13,'20-00389','positive','I love how the professor uses real world examples','Positive'),(14,'20-00389','positive','sasasa','Neutral'),(15,'20-00389','positive','sasasa','Neutral');
/*!40000 ALTER TABLE `test_feedback` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-13 23:57:35
