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
-- Table structure for table `board_type_ref`
--

DROP TABLE IF EXISTS `board_type_ref`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `board_type_ref` (
                                  `idx` int NOT NULL AUTO_INCREMENT,
                                  `board_type` varchar(50) NOT NULL DEFAULT '' COMMENT 'board_type',
                                  `model_name` varchar(50) NOT NULL DEFAULT '' COMMENT '모델명',
                                  `maker` varchar(50) NOT NULL DEFAULT '' COMMENT '제조사',
                                  `use_count` int NOT NULL DEFAULT '0' COMMENT '데이터 양',
                                  `data1` varchar(50) NOT NULL DEFAULT '' COMMENT 'data1',
                                  `data2` varchar(50) NOT NULL DEFAULT '' COMMENT 'data2',
                                  `data3` varchar(50) NOT NULL DEFAULT '' COMMENT 'data3',
                                  `data4` varchar(50) NOT NULL DEFAULT '' COMMENT 'data4',
                                  `data5` varchar(50) NOT NULL DEFAULT '' COMMENT 'data5',
                                  `data6` varchar(50) NOT NULL DEFAULT '' COMMENT 'data6',
                                  `data7` varchar(50) NOT NULL DEFAULT '' COMMENT 'data7',
                                  `data8` varchar(50) NOT NULL DEFAULT '' COMMENT 'data8',
                                  `created_at` datetime NOT NULL COMMENT '등록일',
                                  PRIMARY KEY (`idx`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `board_type_ref`
--

LOCK TABLES `board_type_ref` WRITE;
/*!40000 ALTER TABLE `board_type_ref` DISABLE KEYS */;
INSERT INTO `board_type_ref` VALUES (2,'1','GW','보드랩',0,'','','','','','','','','2023-07-24 11:43:25'),(3,'2','백엽산','보드랩',7,'온도','습도','co2','Pm0.1','Pm2.5','pm10','조도','','2023-07-24 11:43:25'),(4,'3','축산보드','보드랩',7,'Analog1','Analog2','Analog3','Analog4','Analog5','','','','2023-07-24 11:43:25'),(5,'4','농사보드','보드랩',7,'i2c','Analog1','Analog2','Analog3','Analog4','Analog5','','','2023-07-24 11:43:25'),(6,'5','온도보드','보드랩',8,'온도','습도','온도','습도','온도','습도','온도','습도','2023-07-24 11:43:25'),(7,'6','수량보드','보드랩',2,'수량','구경','','','','','','','2023-07-24 11:43:25'),(8,'21','WIFI DS18b20','안단테',1,'온도','','','','','','','','2023-07-24 11:43:25'),(9,'22','WIFI AM2305b','안단테',2,'온도','습도','','','','','','','2023-07-24 11:43:25'),(10,'23','WIFI SHT30','안단테',2,'온도','습도','','','','','','','2023-07-24 11:43:25'),(11,'24','WIFI 냉장고','안단테',2,'온도','습도','','','','','','','2023-07-24 11:43:25'),(12,'31','LORA-G NO','안단테',0,'','','','','','','','','2023-07-24 11:43:25'),(13,'32','LORA DS18b20','안단테',1,'온도','','','','','','','','2023-07-24 11:43:25'),(14,'33','LORA AM2305b','안단테',2,'온도','습도','','','','','','','2023-07-24 11:43:25'),(15,'34','LORA SHT30','안단테',2,'온도','습도','','','','','','','2023-07-24 11:43:25'),(16,'35','LORA 계량기','안단테',1,'수량','','','','','','','','2023-07-24 11:43:25'),(17,'36','LORA 로드셀','안단테',1,'무게','','','','','','','','2023-07-24 11:43:25'),(18,'37','LORA CT','안단테',1,'단전','','','','','','','','2023-07-24 11:43:25'),(19,'38','LORA co2','안단테',1,'Co2','','','','','','','','2023-07-24 11:43:25'),(20,'41','LORA 릴레이','안단테',1,'릴레이','','','','','','','','2023-07-24 11:43:25');
/*!40000 ALTER TABLE `board_type_ref` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `farm`
--

DROP TABLE IF EXISTS `farm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farm` (
                        `idx` int NOT NULL AUTO_INCREMENT,
                        `farm_name` varchar(50) NOT NULL DEFAULT '' COMMENT '농장이름',
                        `farm_ceo` varchar(50) DEFAULT '' COMMENT '농장대표자 이름',
                        `farm_address` varchar(255) DEFAULT '' COMMENT '농장주소',
                        `created_at` datetime DEFAULT NULL COMMENT '등록일',
                        PRIMARY KEY (`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `farm`
--

LOCK TABLES `farm` WRITE;
/*!40000 ALTER TABLE `farm` DISABLE KEYS */;
/*!40000 ALTER TABLE `farm` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Dumping data for table `member`
--

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
INSERT INTO `member` VALUES (1,'admin','admin','$2y$10$NuF8o/YhmQnRt4o3Cu.9fOZ070QVfIi/u4rK5nDpPIDvHKGCO5rTm','','','admin',16,'2023-07-20 15:07:20');
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-07-24 12:18:40
