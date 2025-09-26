/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.10-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: saas
-- ------------------------------------------------------
-- Server version	10.11.10-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `analytics`
--

DROP TABLE IF EXISTS `analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `analytics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` varchar(50) NOT NULL,
  `edition_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `page_url` varchar(500) DEFAULT NULL,
  `referrer` varchar(500) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_edition_id` (`edition_id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analytics`
--

LOCK TABLES `analytics` WRITE;
/*!40000 ALTER TABLE `analytics` DISABLE KEYS */;
INSERT INTO `analytics` VALUES
(1,'edition_view',236,17,NULL,'49.204.238.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','sess_68ce3ffcba8aa0.85376128','2025-09-20 05:47:40'),
(2,'edition_view',236,17,NULL,'49.204.238.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_b2d6c1f957fcbb9f14c782779a9dae26','2025-09-20 05:49:22'),
(3,'edition_view',236,17,NULL,'49.204.238.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_b2d6c1f957fcbb9f14c782779a9dae26','2025-09-20 05:49:23'),
(4,'edition_view',236,17,NULL,'49.204.238.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_b2d6c1f957fcbb9f14c782779a9dae26','2025-09-20 05:49:24'),
(5,'edition_view',236,17,NULL,'49.204.238.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_b10b7e6ab14fc3177d18702b1ba7b4b2','2025-09-20 05:49:26'),
(6,'edition_view',236,17,NULL,'49.204.238.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_b2d6c1f957fcbb9f14c782779a9dae26','2025-09-20 05:50:45'),
(7,'edition_view',236,17,NULL,'49.204.238.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_b2d6c1f957fcbb9f14c782779a9dae26','2025-09-20 05:50:45'),
(8,'edition_view',236,17,NULL,'49.204.238.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_b2d6c1f957fcbb9f14c782779a9dae26','2025-09-20 05:50:50'),
(9,'edition_view',236,17,NULL,'49.204.238.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_b2d6c1f957fcbb9f14c782779a9dae26','2025-09-20 05:50:50'),
(10,'edition_view',236,17,NULL,'49.204.238.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_b2d6c1f957fcbb9f14c782779a9dae26','2025-09-20 05:50:50'),
(11,'edition_view',236,17,NULL,'49.204.238.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_b2d6c1f957fcbb9f14c782779a9dae26','2025-09-20 05:50:55'),
(12,'edition_view',236,17,NULL,'157.50.159.8','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236','','temp_6cc00522af107dcaed644f714e785e95','2025-09-20 05:51:27'),
(13,'edition_view',236,17,NULL,'157.50.159.8','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236&page=1','','temp_6cc00522af107dcaed644f714e785e95','2025-09-20 05:51:51'),
(14,'edition_view',236,17,NULL,'157.50.159.8','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236','','temp_6cc00522af107dcaed644f714e785e95','2025-09-20 05:54:39'),
(15,'page_view',236,NULL,NULL,'192.168.1.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',NULL,NULL,'session_123','2025-09-20 06:03:32'),
(16,'page_view',235,NULL,NULL,'192.168.1.2','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',NULL,NULL,'session_124','2025-09-20 06:03:32'),
(17,'page_view',236,NULL,NULL,'192.168.1.3','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',NULL,NULL,'session_125','2025-09-20 06:03:32'),
(18,'page_view',237,NULL,NULL,'192.168.1.4','Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X)',NULL,NULL,'session_126','2025-09-20 06:03:32'),
(19,'page_view',236,NULL,NULL,'192.168.1.5','Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0',NULL,NULL,'session_127','2025-09-20 06:03:32'),
(20,'edition_view',236,17,NULL,'49.204.238.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_b2d6c1f957fcbb9f14c782779a9dae26','2025-09-20 06:03:52'),
(21,'edition_view',236,17,NULL,'49.204.238.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_b2d6c1f957fcbb9f14c782779a9dae26','2025-09-20 06:03:53'),
(22,'edition_view',236,17,NULL,'157.50.156.35','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236','','temp_8c6c49df77e54e3ee7a17a147621adce','2025-09-20 06:05:41'),
(23,'edition_view',236,17,NULL,'157.50.156.35','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236','','temp_8c6c49df77e54e3ee7a17a147621adce','2025-09-20 06:06:27'),
(24,'edition_view',236,17,NULL,'149.57.180.32','Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0','/public/edition.php?id=236','','temp_e4f9f2cd59baa643c9f0473161a24d0e','2025-09-20 08:11:55'),
(25,'edition_view',236,17,NULL,'69.62.72.139','aaPanel','/public/edition.php?id=236','','temp_d66e0b1ca6689f5bc1c0a05e4daa93b9','2025-09-20 09:07:18'),
(26,'edition_view',236,17,NULL,'149.57.180.85','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_12a5f61da66332d5cd6c31365f77e6a5','2025-09-20 09:47:53'),
(27,'edition_view',236,17,NULL,'23.27.145.34','Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0','/public/edition.php?id=236','','temp_916e8d84bc972ba6a33a843eac1a7a4d','2025-09-20 12:32:12'),
(28,'edition_view',236,17,NULL,'23.27.145.213','Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0','/public/edition.php?id=236','','temp_565e8c14fa2d67978419c1305f1685dd','2025-09-20 13:32:04'),
(29,'edition_view',236,17,NULL,'178.128.200.49','Mozilla/5.0 (X11; Linux x86_64; rv:139.0) Gecko/20100101 Firefox/139.0','/public/edition.php?id=236','','temp_116021ae4d8aebcfd64ced23ffd7d09a','2025-09-20 14:55:23'),
(30,'edition_view',236,17,NULL,'185.203.132.199','Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236','https://saas.todom.fun/public/','temp_3278b4665f096467cd34225160f18d70','2025-09-21 05:32:51'),
(31,'edition_view',236,17,NULL,'69.62.72.139','aaPanel','/public/edition.php?id=236','','temp_c838daaa896dc1e445356465cf729d61','2025-09-21 09:34:49'),
(32,'edition_view',236,17,NULL,'35.240.111.70','Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)','/public/edition.php?id=236','https://saas.todom.fun/public/','temp_70105aeced75fe05c0acaac2e4960e99','2025-09-21 16:36:58'),
(33,'edition_view',236,17,NULL,'34.142.145.245','Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)','/public/edition.php?id=236','https://saas.todom.fun/public/','temp_3820bcd52d49c1b905656d6f1242acdd','2025-09-21 18:15:25'),
(34,'edition_view',236,17,NULL,'69.62.72.139','aaPanel','/public/edition.php?id=236','','temp_49945b41ed35b0ef59c0a12112be694c','2025-09-22 10:02:22'),
(35,'edition_view',236,17,NULL,'20.171.207.16','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.2; +https://openai.com/gptbot)','/public/edition.php?id=236','','temp_de8455a26d9ce8b5fdaab45246bd6ecd','2025-09-23 00:23:20'),
(36,'edition_view',236,17,NULL,'49.37.157.153','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236','','temp_cc00d6ad59f50636ac35f6ce58ebcca4','2025-09-23 03:45:49'),
(37,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_cf0302114d56fb17c5269a27a0ae9885','2025-09-23 06:32:08'),
(38,'edition_view',236,17,NULL,'49.204.238.148','WhatsApp/2.2535.3 W','/public/edition.php?id=236','','temp_430c910020cbd29a91e77a34a1459962','2025-09-23 06:32:14'),
(39,'edition_view',236,17,NULL,'49.204.238.148','WhatsApp/2.2535.3 W','/public/edition.php?id=236','','temp_430c910020cbd29a91e77a34a1459962','2025-09-23 06:35:09'),
(40,'edition_view',236,17,NULL,'124.123.188.247','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236','','temp_d22dad9ddf5e16ff9ea17f86957f01b0','2025-09-23 06:35:13'),
(41,'edition_view',236,17,NULL,'124.123.188.247','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_8f4fce1185db4a1ac7ee0ed3b9767bd9','2025-09-23 06:36:47'),
(42,'edition_view',236,17,NULL,'23.27.145.35','Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0','/public/edition.php?id=236','','temp_2fa4b54365b699f324bbf5023e568e8c','2025-09-23 08:06:13'),
(43,'edition_view',236,17,NULL,'69.62.72.139','aaPanel','/public/edition.php?id=236','','temp_834c8d1077b4a08689323b15873cd480','2025-09-23 10:30:01'),
(44,'edition_view',236,17,NULL,'23.27.145.171','Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0','/public/edition.php?id=236','','temp_92c46fca29290c4327338af01e57c3f1','2025-09-23 13:10:17'),
(45,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=1&ide_webview_request_time=1758635770557','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 13:56:12'),
(46,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 13:56:37'),
(47,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 13:56:40'),
(48,'edition_view',236,17,NULL,'49.204.238.84','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236','','temp_5d810daf6b1d205a72cd4d9edea8900c','2025-09-23 13:58:20'),
(49,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=1&ide_webview_request_time=1758635969753','temp_033a0a5ae50c9e5d2de94be8c7e6ad1f','2025-09-23 13:59:31'),
(50,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1','/public/edition.php?id=236','','temp_033a0a5ae50c9e5d2de94be8c7e6ad1f','2025-09-23 14:00:15'),
(51,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_033a0a5ae50c9e5d2de94be8c7e6ad1f','2025-09-23 14:00:18'),
(52,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:01:08'),
(53,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:01:09'),
(54,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:01:12'),
(55,'edition_view',236,17,NULL,'49.204.238.84','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236&page=2','','temp_5d810daf6b1d205a72cd4d9edea8900c','2025-09-23 14:01:36'),
(56,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:01:44'),
(57,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:01:45'),
(58,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:01:48'),
(59,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:02:01'),
(60,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:02:02'),
(61,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:02:04'),
(62,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=1&ide_webview_request_time=1758636530662','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:08:52'),
(63,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:08:55'),
(64,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:09:00'),
(65,'edition_view',236,17,NULL,'49.204.238.84','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236&page=2','','temp_5d810daf6b1d205a72cd4d9edea8900c','2025-09-23 14:09:06'),
(66,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=1&ide_webview_request_time=1758636616662','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:10:18'),
(67,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:10:38'),
(68,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:10:41'),
(69,'edition_view',236,17,NULL,'49.204.238.84','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236&page=2','','temp_5d810daf6b1d205a72cd4d9edea8900c','2025-09-23 14:10:43'),
(70,'edition_view',236,17,NULL,'49.204.238.84','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236&page=2','','temp_5d810daf6b1d205a72cd4d9edea8900c','2025-09-23 14:11:18'),
(71,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=1&ide_webview_request_time=1758637136813','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:18:58'),
(72,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:19:43'),
(73,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:19:46'),
(74,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:19:58'),
(75,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:20:01'),
(76,'edition_view',236,17,NULL,'49.204.238.84','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236&page=1','','temp_5d810daf6b1d205a72cd4d9edea8900c','2025-09-23 14:20:04'),
(77,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:23:26'),
(78,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:23:28'),
(79,'edition_view',236,17,NULL,'49.204.238.84','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236&page=1','','temp_5d810daf6b1d205a72cd4d9edea8900c','2025-09-23 14:23:46'),
(80,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:26:18'),
(81,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:26:21'),
(82,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:26:24'),
(83,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:26:28'),
(84,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=1&ide_webview_request_time=1758637595856','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:26:37'),
(85,'edition_view',236,17,NULL,'49.204.238.84','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236&page=1','','temp_5d810daf6b1d205a72cd4d9edea8900c','2025-09-23 14:26:39'),
(86,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=1&ide_webview_request_time=1758637767729','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:29:29'),
(87,'edition_view',236,17,NULL,'49.204.238.84','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236&page=1','','temp_5d810daf6b1d205a72cd4d9edea8900c','2025-09-23 14:29:50'),
(88,'edition_view',236,17,NULL,'49.204.238.84','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236&page=1','','temp_5d810daf6b1d205a72cd4d9edea8900c','2025-09-23 14:29:59'),
(89,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=1&ide_webview_request_time=1758637767729','temp_a3f1b880a01fb6bd8137cd79ef502398','2025-09-23 14:34:31'),
(90,'edition_view',236,17,NULL,'49.204.238.84','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236&page=1','','temp_5d810daf6b1d205a72cd4d9edea8900c','2025-09-23 14:34:33'),
(91,'edition_view',236,17,NULL,'49.204.238.84','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236','','temp_5d810daf6b1d205a72cd4d9edea8900c','2025-09-23 14:36:07'),
(92,'edition_view',236,17,NULL,'69.62.72.139','aaPanel','/public/edition.php?id=236','','temp_41497d2d8d6829f5184d2a4ae0477f40','2025-09-24 10:57:34'),
(93,'edition_view',236,17,NULL,'157.50.151.114','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236','','temp_c2fa535eae5bc2f771f4da1a8135ce1b','2025-09-24 14:34:34'),
(94,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_9bf45334953efbf393141a34710e397b','2025-09-24 15:04:14'),
(95,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_9bf45334953efbf393141a34710e397b','2025-09-24 15:04:17'),
(96,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_6e71ec2f11266f2af0c3cdddd6b5c891','2025-09-24 15:04:28'),
(97,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_6e71ec2f11266f2af0c3cdddd6b5c891','2025-09-24 15:05:31'),
(98,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_9bf45334953efbf393141a34710e397b','2025-09-24 15:07:45'),
(99,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_9bf45334953efbf393141a34710e397b','2025-09-24 15:07:48'),
(100,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236&page=2','','temp_6e71ec2f11266f2af0c3cdddd6b5c891','2025-09-24 15:07:57'),
(101,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_9bf45334953efbf393141a34710e397b','2025-09-24 15:09:42'),
(102,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_9bf45334953efbf393141a34710e397b','2025-09-24 15:09:45'),
(103,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_9bf45334953efbf393141a34710e397b','2025-09-24 15:14:25'),
(104,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_9bf45334953efbf393141a34710e397b','2025-09-24 15:14:28'),
(105,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236&page=1','','temp_6e71ec2f11266f2af0c3cdddd6b5c891','2025-09-24 15:14:48'),
(106,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_9bf45334953efbf393141a34710e397b','2025-09-24 15:17:03'),
(107,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_9bf45334953efbf393141a34710e397b','2025-09-24 15:17:06'),
(108,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236&page=2','','temp_6e71ec2f11266f2af0c3cdddd6b5c891','2025-09-24 15:17:39'),
(109,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_9bf45334953efbf393141a34710e397b','2025-09-24 15:21:32'),
(110,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_9bf45334953efbf393141a34710e397b','2025-09-24 15:21:35'),
(111,'edition_view',236,17,NULL,'49.37.157.153','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36','/public/edition.php?id=236','','temp_dc6e7a42c88151eb8e418b78cb123121','2025-09-24 17:13:53'),
(112,'edition_view',236,17,NULL,'124.123.188.247','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_a08f6c251465fbe813b2b5519047a350','2025-09-24 17:17:12'),
(113,'edition_view',236,17,NULL,'223.228.122.91','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236&page=3','','temp_eddd0626bfeda7b0da423fa7c8172125','2025-09-25 06:35:17'),
(114,'edition_view',236,17,NULL,'223.228.122.91','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236&page=3','','temp_eddd0626bfeda7b0da423fa7c8172125','2025-09-25 06:35:21'),
(115,'edition_view',236,17,NULL,'34.53.104.198','Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)','/public/edition.php?id=236','https://saas.todom.fun/public/','temp_760039448dc9c85b6e59bc4e547e265d','2025-09-25 10:46:34'),
(116,'edition_view',236,17,NULL,'34.126.156.196','Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)','/public/edition.php?id=236','https://saas.todom.fun/public/','temp_ff74e8c4271a3a7110a502ea23244fed','2025-09-25 11:15:26'),
(117,'edition_view',236,17,NULL,'69.62.72.139','aaPanel','/public/edition.php?id=236','','temp_91b9dccff125cbb700a25f9bf8564d39','2025-09-25 11:25:12'),
(118,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_5cf89f9cca050c34808fa1d81febe0c1','2025-09-25 13:22:55'),
(119,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_5cf89f9cca050c34808fa1d81febe0c1','2025-09-25 13:47:39'),
(120,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_0506752a179dbaf6f1424f6ceea8b465','2025-09-25 13:51:26'),
(121,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_0506752a179dbaf6f1424f6ceea8b465','2025-09-25 13:51:29'),
(122,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_0506752a179dbaf6f1424f6ceea8b465','2025-09-25 13:54:14'),
(123,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_0506752a179dbaf6f1424f6ceea8b465','2025-09-25 13:54:17'),
(124,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=1&ide_webview_request_time=1758808665522','temp_0506752a179dbaf6f1424f6ceea8b465','2025-09-25 13:57:47'),
(125,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_5cf89f9cca050c34808fa1d81febe0c1','2025-09-25 13:58:16'),
(126,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_5cf89f9cca050c34808fa1d81febe0c1','2025-09-25 13:59:40'),
(127,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_5cf89f9cca050c34808fa1d81febe0c1','2025-09-25 14:05:06'),
(128,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_0506752a179dbaf6f1424f6ceea8b465','2025-09-25 14:08:52'),
(129,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_0506752a179dbaf6f1424f6ceea8b465','2025-09-25 14:08:59'),
(130,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_5cf89f9cca050c34808fa1d81febe0c1','2025-09-25 14:09:19'),
(131,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_5cf89f9cca050c34808fa1d81febe0c1','2025-09-25 14:09:20'),
(132,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_5cf89f9cca050c34808fa1d81febe0c1','2025-09-25 14:09:22'),
(133,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_5cf89f9cca050c34808fa1d81febe0c1','2025-09-25 14:09:25'),
(134,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_0506752a179dbaf6f1424f6ceea8b465','2025-09-25 14:09:52'),
(135,'edition_view',236,17,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','http://localhost:8000/public/edition.php?id=236','temp_0506752a179dbaf6f1424f6ceea8b465','2025-09-25 14:09:56'),
(136,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','/public/edition.php?id=236','','temp_5cf89f9cca050c34808fa1d81febe0c1','2025-09-25 14:11:34'),
(137,'edition_view',236,17,NULL,'49.204.238.148','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36','/public/edition.php?id=236','','temp_937508e0c269f8f22a4f23fdc56fe80d','2025-09-25 14:17:08');
/*!40000 ALTER TABLE `analytics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `analytics_daily`
--

DROP TABLE IF EXISTS `analytics_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `analytics_daily` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `edition_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `total_views` int(11) DEFAULT 0,
  `unique_visitors` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_daily_stats` (`date`,`event_type`,`edition_id`,`category_id`),
  KEY `idx_date` (`date`),
  KEY `idx_event_type` (`event_type`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analytics_daily`
--

LOCK TABLES `analytics_daily` WRITE;
/*!40000 ALTER TABLE `analytics_daily` DISABLE KEYS */;
INSERT INTO `analytics_daily` VALUES
(1,'2025-09-20','edition_view',236,17,24,11,'2025-09-20 05:47:40','2025-09-20 14:55:23'),
(2,'2025-09-21','edition_view',236,17,2,2,'2025-09-21 05:32:52','2025-09-21 09:34:49'),
(3,'2025-09-22','edition_view',236,17,3,2,'2025-09-21 16:36:58','2025-09-22 10:02:22'),
(4,'2025-09-23','edition_view',236,17,57,12,'2025-09-23 00:23:20','2025-09-23 14:36:07'),
(5,'2025-09-24','edition_view',236,17,19,4,'2025-09-24 10:57:34','2025-09-24 15:21:35'),
(6,'2025-09-25','edition_view',236,17,27,8,'2025-09-24 17:13:53','2025-09-25 14:17:08');
/*!40000 ALTER TABLE `analytics_daily` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `area_mappings`
--

DROP TABLE IF EXISTS `area_mappings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `area_mappings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edition_image_id` int(11) NOT NULL,
  `x` decimal(10,2) NOT NULL,
  `y` decimal(10,2) NOT NULL,
  `width` decimal(10,2) NOT NULL,
  `height` decimal(10,2) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_edition_image_id` (`edition_image_id`),
  CONSTRAINT `fk_area_mappings_edition_image` FOREIGN KEY (`edition_image_id`) REFERENCES `edition_images` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `area_mappings`
--

LOCK TABLES `area_mappings` WRITE;
/*!40000 ALTER TABLE `area_mappings` DISABLE KEYS */;
INSERT INTO `area_mappings` VALUES
(87,3088,15.00,15.65,23.04,11.26,'Area 2','uploads/areamaps/area_3088_2_1758727056.jpg','2025-09-24 15:17:36','2025-09-24 15:17:36'),
(88,3088,38.65,15.47,23.04,11.49,'Area 1','uploads/areamaps/area_3088_1_1758727056.jpg','2025-09-24 15:17:36','2025-09-24 15:17:36'),
(89,3087,2.55,1.67,94.46,9.35,'Area 1','uploads/areamaps/area_3087_1_1758809100.jpg','2025-09-25 14:05:00','2025-09-25 14:05:00'),
(90,3087,68.47,11.14,28.14,7.62,'Area 4','uploads/areamaps/area_3087_4_1758809100.jpg','2025-09-25 14:05:00','2025-09-25 14:05:00'),
(91,3087,32.94,11.26,35.18,7.56,'Area 3','uploads/areamaps/area_3087_3_1758809100.jpg','2025-09-25 14:05:00','2025-09-25 14:05:00'),
(92,3087,2.68,11.20,30.08,7.68,'Area 2','uploads/areamaps/area_3087_2_1758809100.jpg','2025-09-25 14:05:00','2025-09-25 14:05:00'),
(93,3087,62.14,43.07,34.48,14.90,'Area 7','uploads/areamaps/area_3087_7_1758809100.jpg','2025-09-25 14:05:00','2025-09-25 14:05:00'),
(94,3087,2.99,58.31,11.79,21.02,'Area 8','uploads/areamaps/area_3087_8_1758809100.jpg','2025-09-25 14:05:00','2025-09-25 14:05:00'),
(95,3087,2.73,79.62,35.62,19.00,'Area 11','uploads/areamaps/area_3087_11_1758809100.jpg','2025-09-25 14:05:00','2025-09-25 14:05:00'),
(96,3087,38.57,79.85,22.96,18.71,'Area 12','uploads/areamaps/area_3087_12_1758809100.jpg','2025-09-25 14:05:00','2025-09-25 14:05:00'),
(97,3087,50.35,58.37,46.88,21.13,'Area 10','uploads/areamaps/area_3087_10_1758809100.jpg','2025-09-25 14:05:00','2025-09-25 14:05:00'),
(98,3087,61.79,79.79,35.00,18.88,'Area 13','uploads/areamaps/area_3087_13_1758809100.jpg','2025-09-25 14:05:00','2025-09-25 14:05:00'),
(99,3087,14.91,58.37,35.18,20.96,'Area 9','uploads/areamaps/area_3087_9_1758809100.jpg','2025-09-25 14:05:00','2025-09-25 14:05:00'),
(100,3087,62.05,19.28,35.18,23.67,'Area 6','uploads/areamaps/area_3087_6_1758809100.jpg','2025-09-25 14:05:00','2025-09-25 14:05:00'),
(101,3087,2.90,19.34,58.66,38.80,'Area 5','uploads/areamaps/area_3087_5_1758809100.jpg','2025-09-25 14:05:00','2025-09-25 14:05:00');
/*!40000 ALTER TABLE `area_mappings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
(3,'Telanagana','telanagana','','../../../uploads/categories/page_7.jpg',0,'2025-02-18 11:15:50','2025-02-20 15:11:21'),
(7,'Vijaywada','vijaywada','','../../../uploads/categories/15231525-page-1.jpg',0,'2025-02-18 12:37:08','2025-02-20 15:11:38'),
(13,'Andhra Pradesh','andhra-pradesh','','../../../uploads/categories/download.png',0,'2025-02-18 13:55:37','2025-02-20 15:11:04'),
(17,'-','-','','../../../uploads/categories/prayatnam1.png',0,'2025-02-24 07:20:58','2025-02-24 07:20:58');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clipped_images`
--

DROP TABLE IF EXISTS `clipped_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clipped_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edition_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `clip_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `edition_id` (`edition_id`),
  KEY `image_id` (`image_id`),
  CONSTRAINT `clipped_images_ibfk_1` FOREIGN KEY (`edition_id`) REFERENCES `editions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clipped_images_ibfk_2` FOREIGN KEY (`image_id`) REFERENCES `edition_images` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=507 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clipped_images`
--

LOCK TABLES `clipped_images` WRITE;
/*!40000 ALTER TABLE `clipped_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `clipped_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `color_schema`
--

DROP TABLE IF EXISTS `color_schema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `color_schema` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `color_schema`
--

LOCK TABLES `color_schema` WRITE;
/*!40000 ALTER TABLE `color_schema` DISABLE KEYS */;
INSERT INTO `color_schema` VALUES
(1,'primary_color','#381c87','2025-02-18 10:29:18','2025-09-19 15:53:27'),
(2,'secondary_color','#ffc107','2025-02-18 10:29:18','2025-02-18 10:29:30'),
(3,'background_color','#ffffff','2025-02-18 10:29:18','2025-02-18 10:29:30'),
(4,'text_color','#000000','2025-02-18 10:29:18','2025-02-20 14:24:53'),
(5,'link_color','#1976d2','2025-02-18 10:29:18','2025-02-18 10:29:30'),
(6,'header_background','#ffffff','2025-02-18 10:29:18','2025-09-20 05:24:30'),
(7,'footer_background','#333333','2025-02-18 10:29:18','2025-02-18 10:29:18'),
(8,'header_text_color','#0055ff','2025-09-19 16:06:32','2025-09-19 16:07:19'),
(9,'secondary_header_background','#7338cc','2025-09-20 05:20:33','2025-09-20 05:27:49'),
(10,'secondary_header_text_color','#ffffff','2025-09-20 05:20:33','2025-09-20 05:24:38'),
(11,'secondary_header_button_color','#3498db','2025-09-20 05:20:33','2025-09-20 05:26:03');
/*!40000 ALTER TABLE `color_schema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cropped_images`
--

DROP TABLE IF EXISTS `cropped_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cropped_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area_mapping_id` int(11) DEFAULT NULL,
  `edition_image_id` int(11) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `area_label` varchar(255) DEFAULT NULL,
  `crop_coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`crop_coordinates`)),
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_area_mapping_id` (`area_mapping_id`),
  KEY `idx_edition_image_id` (`edition_image_id`),
  CONSTRAINT `fk_cropped_images_area_mapping` FOREIGN KEY (`area_mapping_id`) REFERENCES `area_mappings` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_cropped_images_edition_image` FOREIGN KEY (`edition_image_id`) REFERENCES `edition_images` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cropped_images`
--

LOCK TABLES `cropped_images` WRITE;
/*!40000 ALTER TABLE `cropped_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `cropped_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `edition_images`
--

DROP TABLE IF EXISTS `edition_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `edition_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edition_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `edition_id` (`edition_id`),
  CONSTRAINT `edition_images_ibfk_1` FOREIGN KEY (`edition_id`) REFERENCES `editions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3099 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `edition_images`
--

LOCK TABLES `edition_images` WRITE;
/*!40000 ALTER TABLE `edition_images` DISABLE KEYS */;
INSERT INTO `edition_images` VALUES
(3087,236,'/uploads/editions/the-indian-express-2025-09-15/page_1.jpg','2025-09-15 17:38:16',1),
(3088,236,'/uploads/editions/the-indian-express-2025-09-15/page_2.jpg','2025-09-15 17:38:16',2),
(3089,236,'/uploads/editions/the-indian-express-2025-09-15/page_3.jpg','2025-09-15 17:38:16',3),
(3090,236,'/uploads/editions/the-indian-express-2025-09-15/page_4.jpg','2025-09-15 17:38:16',4),
(3091,236,'/uploads/editions/the-indian-express-2025-09-15/page_5.jpg','2025-09-15 17:38:16',5),
(3092,236,'/uploads/editions/the-indian-express-2025-09-15/page_6.jpg','2025-09-15 17:38:16',6),
(3093,236,'/uploads/editions/the-indian-express-2025-09-15/page_7.jpg','2025-09-15 17:38:16',7),
(3094,236,'/uploads/editions/the-indian-express-2025-09-15/page_8.jpg','2025-09-15 17:38:16',8),
(3095,236,'/uploads/editions/the-indian-express-2025-09-15/page_9.jpg','2025-09-15 17:38:16',9),
(3096,236,'/uploads/editions/the-indian-express-2025-09-15/page_10.jpg','2025-09-15 17:38:16',10),
(3097,236,'/uploads/editions/the-indian-express-2025-09-15/page_11.jpg','2025-09-15 17:38:16',11),
(3098,236,'/uploads/editions/the-indian-express-2025-09-15/page_12.jpg','2025-09-15 17:38:16',12);
/*!40000 ALTER TABLE `edition_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `editions`
--

DROP TABLE IF EXISTS `editions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `editions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `edition_date` date DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `editions_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=237 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `editions`
--

LOCK TABLES `editions` WRITE;
/*!40000 ALTER TABLE `editions` DISABLE KEYS */;
INSERT INTO `editions` VALUES
(236,'The Indian Express','the-indian-express','',17,'2025-09-15',1,'2025-09-15 17:34:35','2025-09-15 17:34:35');
/*!40000 ALTER TABLE `editions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('category','edition','page','custom') NOT NULL,
  `object_id` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `order_number` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`),
  CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES
(24,2,'jay','edition',1,NULL,0,'2025-02-18 12:18:27','2025-02-18 12:18:27'),
(25,2,'andhra','category',2,NULL,1,'2025-02-18 12:18:27','2025-02-18 12:18:27'),
(26,2,'Tamil Nadu','category',5,NULL,2,'2025-02-18 12:18:27','2025-02-18 12:18:27'),
(27,2,'Game','page',3,NULL,3,'2025-02-18 12:18:27','2025-02-18 12:18:27'),
(42,1,'Home','custom',NULL,'#',0,'2025-04-21 15:35:19','2025-04-21 15:35:19');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `location` enum('header','footer') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES
(1,'Header Menu','header','2025-02-18 05:20:21','2025-02-18 05:20:21'),
(2,'Footer Menu','footer','2025-02-18 05:20:21','2025-02-18 05:20:21');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES
(1,'Contact us','<p>Normal Text Making Testing</p>','contact-us','2025-02-18 04:50:14','2025-02-18 04:50:14');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES
(1,'site_name','The Daily Guardian','2025-02-16 15:18:33','2025-09-24 15:17:18'),
(2,'site_favicon','/uploads/settings/prayatnam pdf 23.07.2025 (1).pdf','2025-02-16 15:18:33','2025-07-23 01:47:32'),
(3,'site_logo','/uploads/settings/pp.png','2025-02-16 15:18:33','2025-09-15 17:41:52'),
(4,'site_email','admin@example.com','2025-02-16 15:18:33','2025-09-24 15:17:18'),
(5,'meta_title','Welcome to My Website','2025-02-16 15:18:33','2025-09-24 15:17:18'),
(6,'meta_description','This is the default meta description for my website.','2025-02-16 15:18:33','2025-09-24 15:17:18'),
(7,'area_mapping_logo','/uploads/settings/pp.png','2025-02-16 15:34:50','2025-09-15 17:41:52'),
(10,'homepage_single_edition_id','','2025-02-17 15:02:14','2025-02-17 15:44:52'),
(11,'display_type','edition_page','2025-02-18 11:06:06','2025-09-15 17:44:25'),
(12,'edition_category','17','2025-02-18 11:06:06','2025-02-24 07:22:37'),
(35,'header_background_image','header_background.png','2025-09-19 16:29:10','2025-09-19 16:29:10'),
(36,'area_mapping_logo_position','bottom','2025-09-24 15:16:36','2025-09-24 15:17:18');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','editor') DEFAULT 'editor',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'admin','$2y$10$866doiVolklBGt12.NdnauOsiww/tAvFd4GE3vh5mWOr.dustxLRe','admin','2025-02-15 15:11:59','2025-02-27 05:56:00');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-25 14:18:00
