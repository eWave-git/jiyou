-- MySQL dump 10.13  Distrib 8.0.32, for macos13 (x86_64)
--
-- Host: 127.0.0.1    Database: new_ewave
-- ------------------------------------------------------
-- Server version	8.0.33

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `farm`
--

DROP TABLE IF EXISTS `farm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farm` (
  `idx` int NOT NULL AUTO_INCREMENT,
  `farm_name` varchar(50) NOT NULL DEFAULT '' COMMENT '농장이름',
  `farm_address` varchar(255) DEFAULT '' COMMENT '농장주소',
  `created_at` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `member` (
  `idx` int NOT NULL AUTO_INCREMENT,
  `member_id` varchar(50) NOT NULL DEFAULT '' COMMENT '회원아이디',
  `member_name` varchar(50) NOT NULL DEFAULT '' COMMENT '회원이름',
  `member_password` varchar(255) NOT NULL DEFAULT '' COMMENT '회원비밀번호',
  `member_email` varchar(255) NOT NULL DEFAULT '' COMMENT '회원이메일',
  `member_phone` varchar(50) NOT NULL DEFAULT '' COMMENT '회원휴대폰번호',
  `member_type` enum('admin','manager','user') NOT NULL DEFAULT 'admin' COMMENT '회원타입',
  `member_farm_idx` int NOT NULL DEFAULT '0' COMMENT '농장 인덱스 번호',
  `created_at` datetime NOT NULL COMMENT '등록일',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-07-21 10:55:45
