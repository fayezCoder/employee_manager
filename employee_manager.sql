-- MariaDB dump 10.19-11.8.8-MariaDB, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: employee_manager
-- ------------------------------------------------------
-- Server version	11.8.8-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `attendance_audit_logs`
--

DROP TABLE IF EXISTS `attendance_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attendance_record_id` bigint(20) unsigned NOT NULL,
  `admin_id` bigint(20) unsigned NOT NULL,
  `field_changed` varchar(50) NOT NULL,
  `old_value` datetime DEFAULT NULL,
  `new_value` datetime DEFAULT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `attendance_audit_logs_attendance_record_id_foreign` (`attendance_record_id`),
  KEY `attendance_audit_logs_admin_id_foreign` (`admin_id`),
  CONSTRAINT `attendance_audit_logs_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_audit_logs_attendance_record_id_foreign` FOREIGN KEY (`attendance_record_id`) REFERENCES `attendance_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_audit_logs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `attendance_audit_logs` WRITE;
/*!40000 ALTER TABLE `attendance_audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `attendance_records`
--

DROP TABLE IF EXISTS `attendance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `check_in` datetime NOT NULL,
  `check_out` datetime DEFAULT NULL,
  `duration_minutes` int(10) unsigned DEFAULT NULL,
  `status` enum('open','complete','incomplete') NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_records_user_id_date_unique` (`user_id`,`date`),
  KEY `attendance_records_date_index` (`date`),
  KEY `attendance_records_status_index` (`status`),
  KEY `attendance_records_user_id_date_index` (`user_id`,`date`),
  CONSTRAINT `attendance_records_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_records`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `attendance_records` WRITE;
/*!40000 ALTER TABLE `attendance_records` DISABLE KEYS */;
INSERT INTO `attendance_records` VALUES
(1,2,'2026-10-06','2026-10-06 05:00:00','2026-10-06 13:30:00',510,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(2,2,'2026-10-07','2026-10-07 05:15:00','2026-10-07 14:00:00',525,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(3,3,'2026-10-06','2026-10-06 05:05:00','2026-10-06 13:32:00',507,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(4,3,'2026-10-07','2026-10-07 05:15:00','2026-10-07 14:00:00',525,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(5,4,'2026-10-06','2026-10-06 05:10:00','2026-10-06 13:34:00',504,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(6,4,'2026-10-07','2026-10-07 05:15:00','2026-10-07 14:00:00',525,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(7,5,'2026-10-06','2026-10-06 05:15:00','2026-10-06 13:36:00',501,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(8,5,'2026-10-07','2026-10-07 05:15:00','2026-10-07 14:00:00',525,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(9,6,'2026-10-06','2026-10-06 05:20:00','2026-10-06 13:38:00',498,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(10,6,'2026-10-07','2026-10-07 05:15:00','2026-10-07 14:00:00',525,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(11,7,'2026-10-06','2026-10-06 05:25:00','2026-10-06 13:40:00',495,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(12,7,'2026-10-07','2026-10-07 05:15:00','2026-10-07 14:00:00',525,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(13,8,'2026-10-06','2026-10-06 05:30:00','2026-10-06 13:42:00',492,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(14,8,'2026-10-07','2026-10-07 05:15:00','2026-10-07 14:00:00',525,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(15,9,'2026-10-06','2026-10-06 05:35:00','2026-10-06 13:44:00',489,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(16,9,'2026-10-07','2026-10-07 05:15:00','2026-10-07 14:00:00',525,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(17,2,'2026-10-08','2026-10-08 05:30:00','2026-10-08 13:45:00',495,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(18,3,'2026-10-08','2026-10-08 05:30:00','2026-10-08 13:45:00',495,'complete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(19,4,'2026-10-08','2026-10-08 06:00:00',NULL,NULL,'incomplete','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(20,2,'2026-10-09','2026-10-09 05:10:00',NULL,NULL,'open','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(21,3,'2026-10-09','2026-10-09 05:20:00',NULL,NULL,'open','2026-10-08 19:42:39','2026-10-08 19:42:39'),
(22,5,'2026-10-09','2026-10-09 05:40:00',NULL,NULL,'open','2026-10-08 19:42:39','2026-10-08 19:42:39');
/*!40000 ALTER TABLE `attendance_records` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES
('ntham-alhdor-cache-saea@example.com|127.0.0.1','i:1;',1791500182),
('ntham-alhdor-cache-saea@example.com|127.0.0.1:timer','i:1791500182;',1791500182);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES
(1,'الموارد البشرية','2026-10-08 19:42:36','2026-10-08 19:42:36'),
(2,'تقنية المعلومات','2026-10-08 19:42:36','2026-10-08 19:42:36'),
(3,'العمليات والتطوير','2026-10-08 19:42:36','2026-10-08 19:42:36');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2024_01_01_000001_create_departments_table',1),
(5,'2024_01_01_000002_add_fields_to_users_table',1),
(6,'2024_01_01_000003_create_attendance_records_table',1),
(7,'2024_01_01_000004_create_attendance_audit_logs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('CvM1msU8fL1KfJ9RCCYLwc68bHXDj2qGG4r54zwz',NULL,'127.0.0.1','Mediapartners-Google','eyJfdG9rZW4iOiJvcjdxMTM4TDNUYVdOZnJkamVzZ084VW1vZ3psZTNhM1NsZzVEbGxFIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2VpZ2h0LXNocmltcHMtZmxvdy5sb2NhLmx0XC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1791501250),
('J0fPPujaP9w6rs1DqG0TIHZuA3xvCu5fqETSnXv7',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI4eXdYWThNR1ZYcGZYYVFKSHoyQnpZZWt0NVRNT29tREQ2SjE4YldRIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2VpZ2h0LXNocmltcHMtZmxvdy5sb2NhLmx0XC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1791501228),
('P4GkzpAk5ybC5jDml2SuX1am0FqSkFd3JwwDn0ml',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJ5d0dtU0VwbnRUVlI1S3dMdEJ3OFBlekw1RUFudDNhbEt2WWV6dGtNIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1791501265),
('RdaEQH0dOgwhvpJKo40r0IjJ9E0QRVu5dlLU8dIc',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJwMFNTM0xhM09GWHdQaXk4TVFFRVA4dUpXbmF2ZTA4YjM5cHF2Z2ltIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9yZXBvcnRzP2RhdGVfZnJvbT0mZGF0ZV90bz0mZGVwYXJ0bWVudF9pZD0mc3RhdHVzPSZ1c2VyX2lkPSIsInJvdXRlIjoicmVwb3J0cy5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6IjhhNGY1MThiNWJlNjc1OTAwODM2YzUyMWViNGFmMjE3MjI5NWQwYThkNGNmZjFkM2Q2ZmM3M2EyZjQzMTE1OGUifQ==',1791501343),
('unVFQWs6E5sTvOIUbIsTnWSalYoKrDinD7OV4iEv',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI5dTBtSGNKS01XS0w0bENwNldXWkE1RVlMWjVMT05JbXdYd2lBcmNDIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2VtcGxveWVlcyIsInJvdXRlIjoiZW1wbG95ZWVzLmluZGV4In0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6IjhhNGY1MThiNWJlNjc1OTAwODM2YzUyMWViNGFmMjE3MjI5NWQwYThkNGNmZjFkM2Q2ZmM3M2EyZjQzMTE1OGUifQ==',1791500213);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','employee') NOT NULL DEFAULT 'employee',
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `force_password_change` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_department_id_index` (`department_id`),
  KEY `users_is_active_index` (`is_active`),
  KEY `users_role_index` (`role`),
  CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'مدير النظام','admin@example.com',NULL,'$2y$12$zIJgDOFQuXqhJm.r7zfmle/D8L8ud9UDzvWL2ys8njxGfPzWQWl56',NULL,'2026-10-08 19:42:36','2026-10-08 19:42:36','admin',1,1,0),
(2,'أحمد علي','ahmed@example.com',NULL,'$2y$12$adxM3gQmukXig4FIXGEijOLiJg67up0DI5.FF6cte1EoNBWU6O08a',NULL,'2026-10-08 19:42:36','2026-10-08 19:42:36','employee',2,1,0),
(3,'سارة محمد','sara@example.com',NULL,'$2y$12$F5D2xKuSEiwBSTXTQd9DXusQ7a10KdT1eYO8rC4IwhNTo/BNx2ocW',NULL,'2026-10-08 19:42:37','2026-10-08 19:42:37','employee',1,1,0),
(4,'خالد عبد الله','khaled@example.com',NULL,'$2y$12$8B3M0pSxbv.X8iM4CRrqtuKNYrIPNA1KghB/KkH49J0kbqVBJ6/8W',NULL,'2026-10-08 19:42:37','2026-10-08 19:42:37','employee',3,1,0),
(5,'فاطمة الزهراء','fatima@example.com',NULL,'$2y$12$JWvp1RNSf8YNxJnIOnNTS.BAnxIOx.wigSVMLPTm3Mky4GkKExWdq',NULL,'2026-10-08 19:42:37','2026-10-08 19:42:37','employee',2,1,0),
(6,'عمر الفاروق','omar@example.com',NULL,'$2y$12$ov.yz7c1rn02AjtVRfYefuwcSCy3lsqTZX4NyDkBbRAGVFpI7BZEa',NULL,'2026-10-08 19:42:38','2026-10-08 19:42:38','employee',3,1,0),
(7,'مريم حسن','maryam@example.com',NULL,'$2y$12$gCTHRJvcvSQAvhESmgUDbOnD6OOYGJaYMXxHx1niJWvXI1bjJ.d4a',NULL,'2026-10-08 19:42:38','2026-10-08 19:42:38','employee',1,1,0),
(8,'يوسف إبراهيم','youssef@example.com',NULL,'$2y$12$7h5pc8rsWpNG7t0tbdjJCexjTQKXzXVrDXn2VIuuHYuv1lZc5dvGW',NULL,'2026-10-08 19:42:38','2026-10-08 19:42:38','employee',2,1,0),
(9,'نورة التميمي','noura@example.com',NULL,'$2y$12$OHQx.fX6i5AA1tUGXQ9SkuM7V5Dw.vCg4dNxaKluvvBG7I45nlue6',NULL,'2026-10-08 19:42:39','2026-10-08 19:42:39','employee',3,1,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-10-09  4:44:07
