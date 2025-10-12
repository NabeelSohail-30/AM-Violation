-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 11, 2025 at 04:28 PM
-- Server version: 8.0.40
-- PHP Version: 8.1.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `civic_alert`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint UNSIGNED NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2021_11_09_064224_create_user_profiles_table', 1),
(5, '2021_11_11_110731_create_permission_tables', 1),
(6, '2021_11_16_114009_create_media_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 6),
(3, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 9),
(3, 'App\\Models\\User', 10),
(3, 'App\\Models\\User', 11),
(3, 'App\\Models\\User', 12),
(3, 'App\\Models\\User', 13),
(3, 'App\\Models\\User', 14),
(3, 'App\\Models\\User', 15),
(3, 'App\\Models\\User', 16),
(3, 'App\\Models\\User', 17),
(3, 'App\\Models\\User', 18),
(3, 'App\\Models\\User', 19),
(3, 'App\\Models\\User', 20),
(3, 'App\\Models\\User', 21),
(3, 'App\\Models\\User', 22),
(3, 'App\\Models\\User', 23),
(3, 'App\\Models\\User', 24),
(3, 'App\\Models\\User', 25),
(3, 'App\\Models\\User', 26),
(3, 'App\\Models\\User', 27),
(3, 'App\\Models\\User', 28),
(3, 'App\\Models\\User', 29),
(3, 'App\\Models\\User', 30),
(3, 'App\\Models\\User', 31),
(3, 'App\\Models\\User', 32),
(3, 'App\\Models\\User', 33),
(3, 'App\\Models\\User', 34),
(3, 'App\\Models\\User', 35),
(3, 'App\\Models\\User', 36),
(3, 'App\\Models\\User', 37),
(3, 'App\\Models\\User', 38),
(3, 'App\\Models\\User', 39),
(3, 'App\\Models\\User', 40),
(3, 'App\\Models\\User', 41),
(3, 'App\\Models\\User', 42),
(3, 'App\\Models\\User', 43);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `title`, `guard_name`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'role', 'Role', 'web', NULL, '2025-06-01 11:28:29', '2025-06-01 11:28:29'),
(2, 'role-add', 'Role Add', 'web', 1, '2025-06-01 11:28:29', '2025-06-01 11:28:29'),
(3, 'role-list', 'Role List', 'web', 1, '2025-06-01 11:28:29', '2025-06-01 11:28:29'),
(4, 'permission', 'Permission', 'web', NULL, '2025-06-01 11:28:29', '2025-06-01 11:28:29'),
(5, 'permission-add', 'Permission Add', 'web', 4, '2025-06-01 11:28:29', '2025-06-01 11:28:29'),
(6, 'permission-list', 'Permission List', 'web', 4, '2025-06-01 11:28:30', '2025-06-01 11:28:30');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `title`, `guard_name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Admin', 'web', 1, '2025-06-01 11:28:30', '2025-06-01 11:28:30'),
(2, 'demo_admin', 'Demo Admin', 'web', 1, '2025-06-01 11:28:30', '2025-06-01 11:28:30'),
(3, 'user', 'User', 'web', 1, '2025-06-01 11:28:30', '2025-06-01 11:28:30');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `user_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `phone_number`, `email_verified_at`, `user_type`, `password`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'systemadmin', 'System', 'Admin', 'admin@admin.com', '+12398190255', '2025-06-01 11:28:31', 'admin', '$2y$10$zEf3t.NZvZTfCn5gOZP3qu9Ewf0FXXVQHVCkVgH9LoYhdkkF0XAvu', 'active', NULL, '2025-06-01 11:28:31', '2025-06-01 11:28:31');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_addr_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street_addr_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_code` bigint DEFAULT NULL,
  `facebook_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkdin_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `company_name`, `street_addr_1`, `street_addr_2`, `phone_number`, `alt_phone_number`, `country`, `state`, `city`, `pin_code`, `facebook_url`, `instagram_url`, `twitter_url`, `linkdin_url`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Civic Alert', NULL, NULL, NULL, NULL, 'USA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-01 11:28:41', '2025-06-01 11:28:41');

-- --------------------------------------------------------

--
-- Table structure for table `violation_api`
--

CREATE TABLE `violation_api` (
  `id` int NOT NULL,
  `violation_type` int NOT NULL,
  `last_fetched_at` datetime DEFAULT NULL,
  `last_fetch_count` int NOT NULL DEFAULT '0',
  `url` text NOT NULL,
  `fetch_param` text NOT NULL,
  `address_fields` text NOT NULL,
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_by` int NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `violation_api`
--

INSERT INTO `violation_api` (`id`, `violation_type`, `last_fetched_at`, `last_fetch_count`, `url`, `fetch_param`, `address_fields`, `is_active`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(1, 1, '2025-09-21 14:59:00', 17, 'https://data.cityofnewyork.us/resource/3h2n-5cm9.json', '$where=issue_date', 'house_number,street,boro', 1, 1, '2025-09-20 15:44:57', 1, '2025-09-21 14:59:00'),
(2, 1, '2025-09-21 14:59:00', 0, 'https://data.cityofnewyork.us/resource/6bgk-3dad.json', '$where=issue_date', 'respondent_house_number,respondent_street,boro', 1, 1, '2025-09-20 15:44:57', 1, '2025-09-21 14:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `violation_records`
--

CREATE TABLE `violation_records` (
  `id` int NOT NULL,
  `violation_type` int NOT NULL,
  `violation_api` int NOT NULL,
  `issue_date` date DEFAULT NULL,
  `address1` text NOT NULL,
  `address2` text NOT NULL,
  `state` varchar(100) DEFAULT NULL,
  `json` json NOT NULL,
  `is_address_verify` tinyint NOT NULL DEFAULT '0' COMMENT '0 = pending, 1 = verify, 2 = not verify',
  `is_send_mail` tinyint NOT NULL DEFAULT '0' COMMENT '0 = pending, 1 = send, 2 = not send',
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_by` int NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `violation_records`
--

INSERT INTO `violation_records` (`id`, `violation_type`, `violation_api`, `issue_date`, `address1`, `address2`, `state`, `json`, `is_address_verify`, `is_send_mail`, `is_active`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(1, 1, 1, '2025-09-19', '32-12 BROADWAY', 'Queens', 'NY', '{\"bin\": \"4008329\", \"lot\": \"00037\", \"boro\": \"4\", \"block\": \"00612\", \"state\": \"NY\", \"number\": \"V091925IMEGNCY19579\", \"street\": \"BROADWAY\", \"issue_date\": \"20250919\", \"description\": \"REMEDY: INSTALL SIDEWALK SHED\", \"house_number\": \"32-12\", \"violation_type\": \"IMEGNCY-IMMEDIATE EMERGENCY                                             ANYTHINGOPTIONAL\", \"isn_dob_bis_viol\": \"2794652\", \"violation_number\": \"19579\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"IMEGNCY\"}', 1, 0, 1, 1, '2025-09-21 19:58:43', NULL, NULL),
(2, 1, 1, '2025-09-19', '3152 HULL AVENUE', 'Bronx', 'NY', '{\"bin\": \"2018411\", \"lot\": \"00016\", \"boro\": \"2\", \"block\": \"03349\", \"state\": \"NY\", \"number\": \"V091925CFA0701DD\", \"street\": \"HULL AVENUE\", \"issue_date\": \"20250919\", \"description\": \"PARTIAL VACATE ORDER DUE TO IMMEDIATELY HAZARDOUS CONDITIONS SUCH AS            CRACKED & DISPLACED WINDOW SILLS MULTI LOCS @ EXP4 SOUTH COURTYARD              CONDITIONS HAVE THEREFORE MADE THE REAR COURTYARD AT EXP#4 SOUTH                UNABLE TO BE OCCUPIED. REMEDY: VACATE REAR COURTYARD @ EXP4 S                   COURTYARD TO BE USED IN EMERGENCIES ONLY\", \"house_number\": \"3152\", \"violation_type\": \"C-CONSTRUCTION                                                    OTHER   OPTIONAL\", \"isn_dob_bis_viol\": \"2794630\", \"violation_number\": \"FA0701DD\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"C\"}', 1, 0, 1, 1, '2025-09-21 19:58:44', NULL, NULL),
(3, 1, 1, '2025-09-19', '1 UTICA WALK', 'Queens', 'NY', '{\"bin\": \"4465663\", \"lot\": \"00400\", \"boro\": \"4\", \"block\": \"16350\", \"state\": \"NY\", \"number\": \"V091925IMEGNCY19577\", \"street\": \"UTICA WALK\", \"issue_date\": \"20250919\", \"description\": \"REMEDY: DEMOLISH STRUCTURE AND GRADE SITE\", \"house_number\": \"1\", \"violation_type\": \"IMEGNCY-IMMEDIATE EMERGENCY                                             ANYTHINGOPTIONAL\", \"isn_dob_bis_viol\": \"2794643\", \"violation_number\": \"19577\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"IMEGNCY\"}', 1, 0, 1, 1, '2025-09-21 19:58:46', NULL, NULL),
(4, 1, 1, '2025-09-19', '51-41 GAR 48 STREET', 'Queens', 'NY', '{\"bin\": \"4548142\", \"lot\": \"00006\", \"boro\": \"4\", \"block\": \"02306\", \"state\": \"NY\", \"number\": \"V091925IMEGNCY19570\", \"street\": \"48 STREET\", \"issue_date\": \"20250919\", \"description\": \"REMEDY: INSTALLED CONSTRUCTION FENCE\", \"house_number\": \"51-41 GAR\", \"violation_type\": \"IMEGNCY-IMMEDIATE EMERGENCY                                             ANYTHINGOPTIONAL\", \"isn_dob_bis_viol\": \"2794635\", \"violation_number\": \"19570\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"IMEGNCY\"}', 2, 0, 1, 1, '2025-09-21 19:58:46', NULL, NULL),
(5, 1, 1, '2025-09-19', '321 WILLOUGHBY AVENUE', 'Brooklyn', 'NY', '{\"bin\": \"3054954\", \"lot\": \"00053\", \"boro\": \"3\", \"block\": \"01912\", \"state\": \"NY\", \"number\": \"V091925C25-02180\", \"street\": \"WILLOUGHBY AVENUE\", \"issue_date\": \"20250919\", \"description\": \"ATOI OBSERVED AT EXP 3 A 18\'WX35\'LX60\'H HORIZONTAL EXTENSION. ALSO A            TWO STORY VERTICAL EXTENSION. NO STRUCTURAL PLANS ON FILE. STRUCTURAL           STABILITY IS IN QUESTION FOR EXISTING BLDG AND NEW FOUNDATION. ALSO             FOR THE LIGHT GAUGE STEEL FRAMING AND C JOISTS THROUGHOUT.\", \"house_number\": \"321\", \"violation_type\": \"C-CONSTRUCTION                                                    OTHER   OPTIONAL\", \"isn_dob_bis_viol\": \"2794651\", \"violation_number\": \"25-02180\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"C\"}', 1, 0, 1, 1, '2025-09-21 19:58:50', NULL, NULL),
(6, 1, 1, '2025-09-19', '499 VAN BRUNT STREET', 'Brooklyn', 'NY', '{\"bin\": \"3390948\", \"lot\": \"00001\", \"boro\": \"3\", \"block\": \"00612\", \"state\": \"NY\", \"number\": \"V091925C25-02177\", \"street\": \"VAN BRUNT STREET\", \"issue_date\": \"20250919\", \"description\": \"STRUCTURE RENDERED NON-COMPLIANT. AT TIME OF INSPECTION RESPONDING TO           A FIRE INCIDENT AT ADJACENT PROPERTY 491 VAN BRUNT STREET, OBSERVED             EXCESSIVE WATER DAMAGE THROUGHOUT BUILDING. ALSO OBSERVED MISSING               WINDOWS AND DAMAGES WALLS THROUGH BUILDING.\", \"house_number\": \"499\", \"violation_type\": \"C-CONSTRUCTION                                                    OTHER   OPTIONAL\", \"isn_dob_bis_viol\": \"2794639\", \"violation_number\": \"25-02177\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"C\"}', 1, 0, 1, 1, '2025-09-21 19:58:51', NULL, NULL),
(7, 1, 1, '2025-09-19', '51-49 GAR 48 STREET', 'Queens', 'NY', '{\"bin\": \"4473561\", \"lot\": \"00002\", \"boro\": \"4\", \"block\": \"02306\", \"state\": \"NY\", \"number\": \"V091925IMEGNCY19574\", \"street\": \"48 STREET\", \"issue_date\": \"20250919\", \"description\": \"REMEDY: INSTALLED CONSTRUCTION FENCE\", \"house_number\": \"51-49 GAR\", \"violation_type\": \"IMEGNCY-IMMEDIATE EMERGENCY                                             ANYTHINGOPTIONAL\", \"isn_dob_bis_viol\": \"2794640\", \"violation_number\": \"19574\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"IMEGNCY\"}', 2, 0, 1, 1, '2025-09-21 19:58:52', NULL, NULL),
(8, 1, 1, '2025-09-19', '497 VAN BRUNT STREET', 'Brooklyn', 'NY', '{\"bin\": \"3336702\", \"lot\": \"00001\", \"boro\": \"3\", \"block\": \"00612\", \"state\": \"NY\", \"number\": \"V091925C25-02179\", \"street\": \"VAN BRUNT STREET\", \"issue_date\": \"20250919\", \"description\": \"STRUCTURE RENDERED NON-COMPLIANT. AT TIME OF INSPECTION RESPONDING TO           A FIRE INCIDENT AT ADJACENT PROPERTY 491 VAN BRUNT STREET, OBSERVED             EXCESSIVE WATER DAMAGE THROUGHOUT BUILDING. ALSO OBSERVED MISSING               WINDOWS, DAMAGED WALLS THROUGHOUT BUILDING AND AN OPEN ROOF LEAVING             BUILDING EXPOSED TO ALL ELEMENTS.\", \"house_number\": \"497\", \"violation_type\": \"C-CONSTRUCTION                                                    OTHER   OPTIONAL\", \"isn_dob_bis_viol\": \"2794644\", \"violation_number\": \"25-02179\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"C\"}', 2, 0, 1, 1, '2025-09-21 19:58:53', NULL, NULL),
(9, 1, 1, '2025-09-19', '296 ST MARKS PLACE', 'Staten Island', 'NY', '{\"bin\": \"5000315\", \"lot\": \"00033\", \"boro\": \"5\", \"block\": \"00019\", \"state\": \"NY\", \"number\": \"V091925C25-02174\", \"street\": \"ST MARKS PLACE\", \"issue_date\": \"20250919\", \"description\": \"BOROUGH COMMISSIONER HAS ORDERED ALL WORK STOPPED UNDER PERMIT                  #S01237877-I1 ON 9/18/25. INTENT TO REVOKE APPROVAL(S) AND PERMIT(S).           STOP ALL WORK AND MAKE SITE SAFE.\", \"house_number\": \"296\", \"violation_type\": \"C-CONSTRUCTION                                                    OTHER   OPTIONAL\", \"isn_dob_bis_viol\": \"2794631\", \"violation_number\": \"25-02174\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"C\"}', 1, 0, 1, 1, '2025-09-21 19:58:54', NULL, NULL),
(10, 1, 1, '2025-09-19', '80 BAY STREET LANDING', 'Staten Island', 'NY', '{\"bin\": \"5089353\", \"lot\": \"07501\", \"boro\": \"5\", \"block\": \"00001\", \"state\": \"NY\", \"number\": \"V091925C25-02173\", \"street\": \"BAY STREET LANDING\", \"issue_date\": \"20250919\", \"description\": \"REQUESTING AN ENGINEER REPORT DUE TO 8TH FLOOR SUSTAINING WATER DAMAGE          ON THE CEILING. AN ENGINEERING REPORT IS REQUIRED TO ASSESS AND                 RECTIFY THE DAMAGES PROPERTY HAS SUSTAINED.\", \"house_number\": \"80\", \"violation_type\": \"C-CONSTRUCTION                                                    OTHER   OPTIONAL\", \"isn_dob_bis_viol\": \"2794633\", \"violation_number\": \"25-02173\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"C\"}', 1, 0, 1, 1, '2025-09-21 19:58:54', NULL, NULL),
(11, 1, 1, '2025-09-19', '51-51 GAR 48 STREET', 'Queens', 'NY', '{\"bin\": \"4473560\", \"lot\": \"00001\", \"boro\": \"4\", \"block\": \"02306\", \"state\": \"NY\", \"number\": \"V091925IMEGNCY19575\", \"street\": \"48 STREET\", \"issue_date\": \"20250919\", \"description\": \"REMEDY: INSTALLED CONSTRUCTION FENCE\", \"house_number\": \"51-51 GAR\", \"violation_type\": \"IMEGNCY-IMMEDIATE EMERGENCY                                             ANYTHINGOPTIONAL\", \"isn_dob_bis_viol\": \"2794641\", \"violation_number\": \"19575\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"IMEGNCY\"}', 2, 0, 1, 1, '2025-09-21 19:58:55', NULL, NULL),
(12, 1, 1, '2025-09-19', '51-43 GAR 48 STREET', 'Queens', 'NY', '{\"bin\": \"4473564\", \"lot\": \"00005\", \"boro\": \"4\", \"block\": \"02306\", \"state\": \"NY\", \"number\": \"V091925IMEGNCY19571\", \"street\": \"48 STREET\", \"issue_date\": \"20250919\", \"description\": \"REMEDY: INSTALL CONSTRUCTION FENCE\", \"house_number\": \"51-43 GAR\", \"violation_type\": \"IMEGNCY-IMMEDIATE EMERGENCY                                             ANYTHINGOPTIONAL\", \"isn_dob_bis_viol\": \"2794636\", \"violation_number\": \"19571\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"IMEGNCY\"}', 2, 0, 1, 1, '2025-09-21 19:58:56', NULL, NULL),
(13, 1, 1, '2025-09-19', '51-45 GAR 48 STREET', 'Queens', 'NY', '{\"bin\": \"4473563\", \"lot\": \"00004\", \"boro\": \"4\", \"block\": \"02306\", \"state\": \"NY\", \"number\": \"V091925IMEGNCY19572\", \"street\": \"48 STREET\", \"issue_date\": \"20250919\", \"description\": \"REMEDY: INSTALL CONSTRUCTION FENCE\", \"house_number\": \"51-45 GAR\", \"violation_type\": \"IMEGNCY-IMMEDIATE EMERGENCY                                             ANYTHINGOPTIONAL\", \"isn_dob_bis_viol\": \"2794637\", \"violation_number\": \"19572\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"IMEGNCY\"}', 2, 0, 1, 1, '2025-09-21 19:58:57', NULL, NULL),
(14, 1, 1, '2025-09-19', '51-39 GAR 48 STREET', 'Queens', 'NY', '{\"bin\": \"4548147\", \"lot\": \"00007\", \"boro\": \"4\", \"block\": \"02306\", \"state\": \"NY\", \"number\": \"V091925IMEGNCY19569\", \"street\": \"48 STREET\", \"issue_date\": \"20250919\", \"description\": \"REMEDY: INSTALLED CONSTRUCTION FENCE\", \"house_number\": \"51-39 GAR\", \"violation_type\": \"IMEGNCY-IMMEDIATE EMERGENCY                                             ANYTHINGOPTIONAL\", \"isn_dob_bis_viol\": \"2794634\", \"violation_number\": \"19569\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"IMEGNCY\"}', 2, 0, 1, 1, '2025-09-21 19:58:58', NULL, NULL),
(15, 1, 1, '2025-09-19', '4817 CHURCH AVENUE', 'Brooklyn', 'NY', '{\"bin\": \"3102053\", \"lot\": \"00038\", \"boro\": \"3\", \"block\": \"04674\", \"state\": \"NY\", \"number\": \"V091925C25-02176\", \"street\": \"CHURCH AVENUE\", \"issue_date\": \"20250919\", \"description\": \"STRUCTURE RENDERED NON-COMPLIANT. OBSERVED AT EXPOSURE #1 VEHICLE               IMPACTED FRONT ROLL DOWN GATE TO COMMERCIAL SPACE AND INTO VACANT               STORE. NO STRUCTURAL DAMAGE OBSERVED, ONLY DAMAGE IS TO ROLL DOWN               GATE.\", \"house_number\": \"4817\", \"violation_type\": \"C-CONSTRUCTION                                                    OTHER   OPTIONAL\", \"isn_dob_bis_viol\": \"2794638\", \"violation_number\": \"25-02176\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"C\"}', 1, 0, 1, 1, '2025-09-21 19:58:59', NULL, NULL),
(16, 1, 1, '2025-09-19', '818 EAST 16 STREET', 'Brooklyn', 'NY', '{\"bin\": \"3179353\", \"lot\": \"00014\", \"boro\": \"3\", \"block\": \"06699\", \"state\": \"NY\", \"number\": \"V091925C25-02178\", \"street\": \"EAST 16 STREET\", \"issue_date\": \"20250919\", \"description\": \"STRUCTURE RENDERED NON-COMPLIANT DUE TO FIRE. AT TIME OF INSPECTION             OBSERVED AT EXPOSURE 1(FRONT) 2ND FLOOR WINDOW FRAME WOOD DIVIDER               DISCONNECTED. AT 2ND FLOOR PARAPET BRICK JOINTS MISSING MORTAR                  CREATING HAZARDS AT PUBLIC RIGHT AWAY.\", \"house_number\": \"818\", \"violation_type\": \"C-CONSTRUCTION                                                    OTHER   OPTIONAL\", \"isn_dob_bis_viol\": \"2794642\", \"violation_number\": \"25-02178\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"C\"}', 1, 0, 1, 1, '2025-09-21 19:58:59', NULL, NULL),
(17, 1, 1, '2025-09-19', '51-29 GAR 48 STREET', 'Queens', 'NY', '{\"bin\": \"4473572\", \"lot\": \"00110\", \"boro\": \"4\", \"block\": \"02306\", \"state\": \"NY\", \"number\": \"V091925IMEGNCY19567\", \"street\": \"48 STREET\", \"issue_date\": \"20250919\", \"description\": \"REMEDY: INSTALL CONSTRUCTION FENCE\", \"house_number\": \"51-29 GAR\", \"violation_type\": \"IMEGNCY-IMMEDIATE EMERGENCY                                             ANYTHINGOPTIONAL\", \"isn_dob_bis_viol\": \"2794632\", \"violation_number\": \"19567\", \"violation_category\": \"V-DOB VIOLATION - ACTIVE\", \"violation_type_code\": \"IMEGNCY\"}', 2, 0, 1, 1, '2025-09-21 19:59:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `violation_type`
--

CREATE TABLE `violation_type` (
  `id` int NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_by` int NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `violation_type`
--

INSERT INTO `violation_type` (`id`, `title`, `description`, `is_active`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(1, 'DOB Violations', 'DOB - DEPARTMENT OF BUILDINGS (Primary Source)', 1, 1, '2025-09-20 19:50:17', NULL, NULL),
(2, 'ECB Violations', 'ECB - ENVIRONMENTAL CONTROL BOARD', 1, 1, '2025-09-20 19:50:48', NULL, NULL),
(3, 'HPD Violations', ' HPD - HOUSING PRESERVATION & DEVELOPMENT', 1, 1, '2025-09-20 19:50:48', NULL, NULL),
(4, 'Fire Violations', 'FDNY - FIRE DEPARTMENT', 1, 1, '2025-09-20 19:51:20', NULL, NULL),
(5, 'DOT Violations (Sidewalk, Street Work, etc.)', 'DOT - DEPARTMENT OF TRANSPORTATION (As Requested)', 1, 1, '2025-09-20 19:51:20', NULL, NULL),
(6, 'DEP Violations', 'DEP - DEPARTMENT OF ENVIRONMENTAL PROTECTION', 1, 1, '2025-09-20 19:52:35', NULL, NULL),
(7, 'DSNY Violations', 'DSNY - DEPARTMENT OF SANITATION', 1, 1, '2025-09-20 19:52:35', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_uuid_unique` (`uuid`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `violation_api`
--
ALTER TABLE `violation_api`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `violation_records`
--
ALTER TABLE `violation_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `violation_type`
--
ALTER TABLE `violation_type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `violation_api`
--
ALTER TABLE `violation_api`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `violation_records`
--
ALTER TABLE `violation_records`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `violation_type`
--
ALTER TABLE `violation_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
