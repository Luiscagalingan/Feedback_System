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
  `college` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `program` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('active','inactive','archived') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `non_academic_teachers`
--

DROP TABLE IF EXISTS `non_academic_teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `non_academic_teachers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `office_key` varchar(80) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `office_category` enum('academic','nonacademic') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'nonacademic',
  `service` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('active','inactive','archived') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `non_academic_office_unique` (`office_key`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `college` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `section` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `otp` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('active','archived') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `archived_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `archived_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archived_students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `student_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `program` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `section` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `college` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `otp` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_feedback`
--

-- One normalized table stores submissions for every academic and non-academic office.
CREATE TABLE IF NOT EXISTS `office_feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `category` enum('academic','nonacademic') COLLATE utf8mb4_general_ci NOT NULL,
  `office_key` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
  `office_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `section_title` varchar(180) COLLATE utf8mb4_general_ci NOT NULL,
  `responses_json` text COLLATE utf8mb4_general_ci NOT NULL,
  `rating_average` decimal(4,2) NOT NULL,
  `positive_feedback_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `neutral_feedback_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `negative_feedback_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `answer_text` text COLLATE utf8mb4_general_ci,
  `review_result` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('submitted','pending') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'submitted',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `office_feedback_category_idx` (`category`),
  KEY `office_feedback_student_idx` (`student_id`),
  KEY `office_feedback_student_created_idx` (`student_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `role` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `user_name` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
  `module` varchar(80) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'system',
  `status` enum('success','failed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'success',
  `details` text COLLATE utf8mb4_general_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `audit_created_idx` (`created_at`),
  KEY `audit_role_user_idx` (`role`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-13 23:57:45
