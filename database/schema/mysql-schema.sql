/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `abbreviations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `abbreviations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `abbreviation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_term` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `abbreviations_abbreviation_unique` (`abbreviation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `active_ingredient_drug_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `active_ingredient_drug_class` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `active_ingredient_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `drug_class_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_dc_unique` (`active_ingredient_id`,`drug_class_id`),
  KEY `active_ingredient_drug_class_drug_class_id_foreign` (`drug_class_id`),
  CONSTRAINT `active_ingredient_drug_class_active_ingredient_id_foreign` FOREIGN KEY (`active_ingredient_id`) REFERENCES `active_ingredients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `active_ingredient_drug_class_drug_class_id_foreign` FOREIGN KEY (`drug_class_id`) REFERENCES `drug_classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `active_ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `active_ingredients` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `indications` longtext COLLATE utf8mb4_unicode_ci,
  `contraindications` longtext COLLATE utf8mb4_unicode_ci,
  `precautions` longtext COLLATE utf8mb4_unicode_ci,
  `side_effects` longtext COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `active_ingredients_slug_unique` (`slug`),
  FULLTEXT KEY `active_ingredients_name_fulltext` (`name`,`name_ar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alternatives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alternatives` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternative_product_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('commercial','therapeutic','economic') COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_alternative_unique` (`product_id`,`alternative_product_id`),
  KEY `alternatives_alternative_product_id_foreign` (`alternative_product_id`),
  CONSTRAINT `alternatives_alternative_product_id_foreign` FOREIGN KEY (`alternative_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `alternatives_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `anatomical_structures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `anatomical_structures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `body_system_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `canonical_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `anatomical_structures_canonical_name_unique` (`canonical_name`),
  KEY `anatomical_structures_body_system_id_foreign` (`body_system_id`),
  KEY `anatomical_structures_parent_id_foreign` (`parent_id`),
  CONSTRAINT `anatomical_structures_body_system_id_foreign` FOREIGN KEY (`body_system_id`) REFERENCES `body_systems` (`id`) ON DELETE CASCADE,
  CONSTRAINT `anatomical_structures_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `anatomical_structures` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `content_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'markdown',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `articleable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `articleable_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `articles_slug_unique` (`slug`),
  UNIQUE KEY `articles_unique_articleable` (`articleable_type`,`articleable_id`),
  KEY `articles_articleable_type_articleable_id_index` (`articleable_type`,`articleable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blog_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_categories` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogs` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blog_category_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `excerpt_ar` text COLLATE utf8mb4_unicode_ci,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_ar` longtext COLLATE utf8mb4_unicode_ci,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_id` bigint unsigned DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blogs_slug_unique` (`slug`),
  KEY `blogs_blog_category_id_foreign` (`blog_category_id`),
  KEY `blogs_author_id_foreign` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `body_systems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `body_systems` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `canonical_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `body_systems_canonical_name_unique` (`canonical_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `clinical_course_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clinical_course_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `canonical_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_peracute` tinyint(1) NOT NULL DEFAULT '0',
  `is_acute` tinyint(1) NOT NULL DEFAULT '0',
  `is_subacute` tinyint(1) NOT NULL DEFAULT '0',
  `is_chronic` tinyint(1) NOT NULL DEFAULT '0',
  `is_progressive` tinyint(1) NOT NULL DEFAULT '0',
  `is_fatal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clinical_course_types_slug_unique` (`slug`),
  UNIQUE KEY `clinical_course_types_canonical_name_unique` (`canonical_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `clinical_severity_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clinical_severity_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `canonical_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity_score` tinyint unsigned NOT NULL,
  `is_emergency` tinyint(1) NOT NULL DEFAULT '0',
  `is_life_threatening` tinyint(1) NOT NULL DEFAULT '0',
  `is_high_mortality` tinyint(1) NOT NULL DEFAULT '0',
  `requires_isolation` tinyint(1) NOT NULL DEFAULT '0',
  `requires_rapid_intervention` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clinical_severity_levels_slug_unique` (`slug`),
  UNIQUE KEY `clinical_severity_levels_canonical_name_unique` (`canonical_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `clinical_signs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clinical_signs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `anatomical_structure_id` bigint unsigned DEFAULT NULL,
  `finding_id` bigint unsigned NOT NULL,
  `modifier_id` bigint unsigned DEFAULT NULL,
  `severity_level_id` bigint unsigned DEFAULT NULL,
  `canonical_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `semantic_slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'clinical',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_clinical_sign_composition` (`finding_id`,`modifier_id`,`anatomical_structure_id`),
  UNIQUE KEY `clinical_signs_semantic_unique` (`anatomical_structure_id`,`finding_id`,`modifier_id`),
  KEY `clinical_signs_modifier_id_foreign` (`modifier_id`),
  KEY `clinical_signs_severity_level_id_foreign` (`severity_level_id`),
  KEY `clinical_signs_semantic_slug_index` (`semantic_slug`),
  CONSTRAINT `clinical_signs_anatomical_structure_id_foreign` FOREIGN KEY (`anatomical_structure_id`) REFERENCES `anatomical_structures` (`id`) ON DELETE SET NULL,
  CONSTRAINT `clinical_signs_finding_id_foreign` FOREIGN KEY (`finding_id`) REFERENCES `findings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clinical_signs_modifier_id_foreign` FOREIGN KEY (`modifier_id`) REFERENCES `modifiers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `clinical_signs_severity_level_id_foreign` FOREIGN KEY (`severity_level_id`) REFERENCES `clinical_severity_levels` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_company_id` char(26) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_type` enum('manufacturer','agent','distributor','marketing') COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `governorate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_maps_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coverage_area` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_slug_unique` (`slug`),
  KEY `companies_parent_company_id_foreign` (`parent_company_id`),
  FULLTEXT KEY `companies_name_fulltext` (`name`,`name_ar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `companies_temp_update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies_temp_update` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `company_merge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_merge` (
  `old_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`old_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `contact_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_submissions` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `contraindications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contraindications` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contraindications_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `differential_syndrome_clinical_sign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `differential_syndrome_clinical_sign` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `differential_syndrome_id` bigint unsigned NOT NULL,
  `clinical_sign_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dscs_syndrome_fk` (`differential_syndrome_id`),
  KEY `dscs_sign_fk` (`clinical_sign_id`),
  CONSTRAINT `dscs_sign_fk` FOREIGN KEY (`clinical_sign_id`) REFERENCES `clinical_signs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dscs_syndrome_fk` FOREIGN KEY (`differential_syndrome_id`) REFERENCES `differential_syndromes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `differential_syndrome_disease`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `differential_syndrome_disease` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `differential_syndrome_id` bigint unsigned NOT NULL,
  `disease_id` bigint unsigned NOT NULL,
  `boost_score` int NOT NULL DEFAULT '15',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dsd_syndrome_fk` (`differential_syndrome_id`),
  KEY `differential_syndrome_disease_disease_id_foreign` (`disease_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `differential_syndromes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `differential_syndromes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disease_classifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disease_classifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `disease_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `infectiousness` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transmissibility` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etiology_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occurrence_patterns` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `disease_courses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `notifiable` tinyint(1) NOT NULL DEFAULT '0',
  `zoonotic` tinyint(1) NOT NULL DEFAULT '0',
  `oie_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `disease_classifications_disease_id_unique` (`disease_id`),
  CONSTRAINT `disease_classifications_disease_id_foreign` FOREIGN KEY (`disease_id`) REFERENCES `diseases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `disease_classifications_chk_1` CHECK (json_valid(`occurrence_patterns`)),
  CONSTRAINT `disease_classifications_chk_2` CHECK (json_valid(`disease_courses`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disease_clinical_course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disease_clinical_course` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `disease_id` bigint unsigned NOT NULL,
  `clinical_course_type_id` bigint unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '1',
  `confidence_score` tinyint unsigned NOT NULL DEFAULT '100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dcc_unique` (`disease_id`,`clinical_course_type_id`),
  KEY `disease_clinical_course_clinical_course_type_id_foreign` (`clinical_course_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disease_clinical_sign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disease_clinical_sign` (
  `disease_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `clinical_sign_id` bigint unsigned NOT NULL,
  `weight` int NOT NULL DEFAULT '5',
  `is_specific` tinyint(1) NOT NULL DEFAULT '0',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_pathognomonic` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`disease_id`,`clinical_sign_id`),
  KEY `disease_clinical_sign_clinical_sign_id_foreign` (`clinical_sign_id`),
  CONSTRAINT `disease_clinical_sign_clinical_sign_id_foreign` FOREIGN KEY (`clinical_sign_id`) REFERENCES `clinical_signs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `disease_clinical_sign_disease_id_foreign` FOREIGN KEY (`disease_id`) REFERENCES `diseases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disease_host_species`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disease_host_species` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `disease_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `host_species_id` bigint unsigned NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `susceptibility` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary_host` tinyint(1) NOT NULL DEFAULT '0',
  `is_reservoir` tinyint(1) NOT NULL DEFAULT '0',
  `is_vector` tinyint(1) NOT NULL DEFAULT '0',
  `is_carrier` tinyint(1) NOT NULL DEFAULT '0',
  `is_incidental_host` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `disease_host_species_disease_id_host_species_id_unique` (`disease_id`,`host_species_id`),
  KEY `disease_host_species_host_species_id_foreign` (`host_species_id`),
  CONSTRAINT `disease_host_species_disease_id_foreign` FOREIGN KEY (`disease_id`) REFERENCES `diseases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `disease_host_species_host_species_id_foreign` FOREIGN KEY (`host_species_id`) REFERENCES `host_species` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disease_microorganism`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disease_microorganism` (
  `disease_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `microorganism_id` bigint unsigned NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cause',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`disease_id`,`microorganism_id`),
  KEY `disease_microorganism_microorganism_id_foreign` (`microorganism_id`),
  CONSTRAINT `disease_microorganism_disease_id_foreign` FOREIGN KEY (`disease_id`) REFERENCES `diseases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `disease_microorganism_microorganism_id_foreign` FOREIGN KEY (`microorganism_id`) REFERENCES `microorganisms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disease_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disease_product` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `disease_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `disease_product_disease_id_product_id_unique` (`disease_id`,`product_id`),
  KEY `disease_product_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disease_reservoir`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disease_reservoir` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `disease_id` bigint unsigned NOT NULL,
  `reservoir_id` bigint unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `is_confirmed` tinyint(1) NOT NULL DEFAULT '1',
  `confidence_score` tinyint unsigned NOT NULL DEFAULT '100',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `disease_reservoir_disease_id_reservoir_id_unique` (`disease_id`,`reservoir_id`),
  KEY `disease_reservoir_reservoir_id_foreign` (`reservoir_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disease_severity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disease_severity` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `disease_id` bigint unsigned NOT NULL,
  `severity_level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '1',
  `confidence_score` tinyint unsigned NOT NULL DEFAULT '100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dsv_unique` (`disease_id`) USING BTREE,
  UNIQUE KEY `unique_disease_severity` (`disease_id`,`severity_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disease_species`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disease_species` (
  `disease_id` bigint unsigned NOT NULL,
  `species_id` bigint unsigned NOT NULL,
  `susceptibility` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`disease_id`,`species_id`),
  KEY `disease_species_species_id_foreign` (`species_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disease_transmission_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disease_transmission_routes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `disease_id` bigint unsigned NOT NULL,
  `transmission_route_id` bigint unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `is_confirmed` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `disease_transmission_unique` (`disease_id`,`transmission_route_id`),
  KEY `disease_transmission_routes_transmission_route_id_foreign` (`transmission_route_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disease_transmission_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disease_transmission_type` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `disease_id` bigint unsigned NOT NULL,
  `transmission_type_id` bigint unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `is_confirmed` tinyint(1) NOT NULL DEFAULT '1',
  `confidence_score` tinyint unsigned NOT NULL DEFAULT '100',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `disease_transmission_type_disease_id_transmission_type_id_unique` (`disease_id`,`transmission_type_id`),
  KEY `disease_transmission_type_transmission_type_id_foreign` (`transmission_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disease_vector`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disease_vector` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `disease_id` bigint unsigned NOT NULL,
  `vector_id` bigint unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `is_confirmed` tinyint(1) NOT NULL DEFAULT '1',
  `confidence_score` tinyint unsigned NOT NULL DEFAULT '100',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `disease_vector_disease_id_vector_id_unique` (`disease_id`,`vector_id`),
  KEY `disease_vector_vector_id_foreign` (`vector_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `diseases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diseases` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `knowledge_payload` json DEFAULT NULL,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `diseases_name_unique` (`name`),
  UNIQUE KEY `diseases_slug_unique` (`slug`),
  FULLTEXT KEY `diseases_name_fulltext` (`name`,`name_ar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dosage_forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dosage_forms` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dosage_forms_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dosages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dosages` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `species_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosage` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `route` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dosages_product_id_foreign` (`product_id`),
  KEY `dosages_species_id_foreign` (`species_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `drug_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drug_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drug_categories_name_en_unique` (`name_en`),
  UNIQUE KEY `drug_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `drug_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drug_classes` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drug_classes_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `drug_interactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drug_interactions` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_ingredient_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `interacting_drug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity` enum('minor','moderate','major') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'moderate',
  `effect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `recommendation` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `drug_interactions_active_ingredient_id_foreign` (`active_ingredient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `drug_therapeutic_use`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drug_therapeutic_use` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `drug_id` bigint unsigned NOT NULL,
  `therapeutic_use_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drug_therapeutic_use_drug_id_therapeutic_use_id_unique` (`drug_id`,`therapeutic_use_id`),
  KEY `drug_therapeutic_use_therapeutic_use_id_foreign` (`therapeutic_use_id`),
  CONSTRAINT `drug_therapeutic_use_drug_id_foreign` FOREIGN KEY (`drug_id`) REFERENCES `drugs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drug_therapeutic_use_therapeutic_use_id_foreign` FOREIGN KEY (`therapeutic_use_id`) REFERENCES `therapeutic_uses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `drugs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drugs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `drug_category_id` bigint unsigned DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `mechanism_of_action` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drugs_name_en_unique` (`name_en`),
  UNIQUE KEY `drugs_slug_unique` (`slug`),
  KEY `drugs_drug_category_id_foreign` (`drug_category_id`),
  CONSTRAINT `drugs_drug_category_id_foreign` FOREIGN KEY (`drug_category_id`) REFERENCES `drug_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `findings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `findings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `canonical_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ontology_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_general_sign` tinyint(1) NOT NULL DEFAULT '0',
  `is_noisy` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `findings_canonical_name_unique` (`canonical_name`),
  UNIQUE KEY `findings_slug_unique` (`slug`),
  KEY `findings_parent_id_foreign` (`parent_id`),
  CONSTRAINT `findings_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `findings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `host_species`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `host_species` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `canonical_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taxonomy_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_domestic` tinyint(1) NOT NULL DEFAULT '0',
  `is_wildlife` tinyint(1) NOT NULL DEFAULT '0',
  `is_human` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `host_species_canonical_name_unique` (`canonical_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `import_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `import_jobs` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_type` enum('pdf','docx','html','json','txt') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','processing','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `total_products` int unsigned NOT NULL DEFAULT '0',
  `imported_products` int unsigned NOT NULL DEFAULT '0',
  `failed_products` int unsigned NOT NULL DEFAULT '0',
  `extracted_json` longtext COLLATE utf8mb4_unicode_ci,
  `error_message` longtext COLLATE utf8mb4_unicode_ci,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `indications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `indications` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `indications_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `medical_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medical_articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `disease_id` char(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `species` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `article_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disease_reference',
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `summary` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `medical_articles_slug_unique` (`slug`),
  KEY `medical_articles_disease_id_foreign` (`disease_id`),
  CONSTRAINT `medical_articles_disease_id_foreign` FOREIGN KEY (`disease_id`) REFERENCES `diseases` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `microorganisms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `microorganisms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `normalized_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kingdom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phylum` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `family` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `genus` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `species` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `microorganism_type` enum('bacteria','fungus','virus','parasite','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bacteria',
  `is_pathogenic` tinyint(1) NOT NULL DEFAULT '0',
  `searchable_text` longtext COLLATE utf8mb4_unicode_ci,
  `json_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `source_json_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `microorganisms_slug_unique` (`slug`),
  KEY `microorganisms_name_index` (`name`),
  KEY `microorganisms_normalized_name_index` (`normalized_name`),
  KEY `microorganisms_kingdom_index` (`kingdom`),
  KEY `microorganisms_genus_index` (`genus`),
  KEY `microorganisms_species_index` (`species`),
  KEY `microorganisms_microorganism_type_index` (`microorganism_type`),
  CONSTRAINT `microorganisms_chk_1` CHECK (json_valid(`json_data`)),
  CONSTRAINT `microorganisms_chk_2` CHECK (json_valid(`tags`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modifiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modifiers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `canonical_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modifier_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_noisy` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modifiers_canonical_name_unique` (`canonical_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `precautions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `precautions` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `precautions_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_active_ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_active_ingredient` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_ingredient_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strength` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_ai_unique` (`product_id`,`active_ingredient_id`),
  KEY `product_active_ingredient_active_ingredient_id_foreign` (`active_ingredient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_company` (
  `product_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('manufacturer','agent','distributor','marketing') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `product_company_product_id_company_id_role_unique` (`product_id`,`company_id`,`role`),
  KEY `product_company_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_documents` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('leaflet','datasheet','brochure','certificate') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_documents_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trade_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trade_name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosage_form_id` char(26) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_type` enum('pharmaceutical','vaccine','supplement','feed_additive','disinfectant','biological') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pharmaceutical',
  `description` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `package_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `storage_conditions` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_dosage_form_id_foreign` (`dosage_form_id`),
  KEY `products_created_by_foreign` (`created_by`),
  FULLTEXT KEY `products_trade_name_fulltext` (`trade_name`,`trade_name_ar`),
  CONSTRAINT `products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reservoirs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservoirs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `canonical_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_wildlife` tinyint(1) NOT NULL DEFAULT '0',
  `is_domestic` tinyint(1) NOT NULL DEFAULT '0',
  `is_bird` tinyint(1) NOT NULL DEFAULT '0',
  `is_rodent` tinyint(1) NOT NULL DEFAULT '0',
  `is_ruminant` tinyint(1) NOT NULL DEFAULT '0',
  `is_human_related` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservoirs_slug_unique` (`slug`),
  UNIQUE KEY `reservoirs_canonical_name_unique` (`canonical_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `side_effects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `side_effects` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `side_effects_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `species`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `species` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `description_ar` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `species_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `synonyms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `synonyms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `term` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `normalized_term` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `synonymable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `synonymable_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_synonym_mapping` (`normalized_term`,`synonymable_type`,`synonymable_id`),
  KEY `synonyms_synonymable_type_synonymable_id_index` (`synonymable_type`,`synonymable_id`),
  KEY `synonyms_normalized_term_index` (`normalized_term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `therapeutic_uses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `therapeutic_uses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `therapeutic_uses_name_en_unique` (`name_en`),
  UNIQUE KEY `therapeutic_uses_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `transmission_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transmission_routes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `canonical_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `definition` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transmission_routes_slug_unique` (`slug`),
  UNIQUE KEY `transmission_routes_canonical_name_unique` (`canonical_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `transmission_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transmission_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `canonical_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_airborne` tinyint(1) NOT NULL DEFAULT '0',
  `is_vector_borne` tinyint(1) NOT NULL DEFAULT '0',
  `is_foodborne` tinyint(1) NOT NULL DEFAULT '0',
  `is_waterborne` tinyint(1) NOT NULL DEFAULT '0',
  `is_fomite` tinyint(1) NOT NULL DEFAULT '0',
  `is_vertical` tinyint(1) NOT NULL DEFAULT '0',
  `is_direct_contact` tinyint(1) NOT NULL DEFAULT '0',
  `is_indirect_contact` tinyint(1) NOT NULL DEFAULT '0',
  `is_venereal` tinyint(1) NOT NULL DEFAULT '0',
  `is_zoonotic_related` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transmission_types_slug_unique` (`slug`),
  UNIQUE KEY `transmission_types_canonical_name_unique` (`canonical_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vectors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vectors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `canonical_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_arthropod` tinyint(1) NOT NULL DEFAULT '0',
  `is_tick` tinyint(1) NOT NULL DEFAULT '0',
  `is_mosquito` tinyint(1) NOT NULL DEFAULT '0',
  `is_fly` tinyint(1) NOT NULL DEFAULT '0',
  `is_mechanical` tinyint(1) NOT NULL DEFAULT '0',
  `is_biological` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vectors_slug_unique` (`slug`),
  UNIQUE KEY `vectors_canonical_name_unique` (`canonical_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `veterinary_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `veterinary_projects` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'feasibility_study',
  `sector` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `veterinary_projects_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `withdrawal_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `withdrawal_periods` (
  `id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `species_id` char(26) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meat_days` int DEFAULT NULL,
  `milk_days` int DEFAULT NULL,
  `egg_days` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `withdrawal_periods_product_id_foreign` (`product_id`),
  KEY `withdrawal_periods_species_id_foreign` (`species_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_06_14_122147_create_species_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_06_14_124817_create_dosage_forms_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_06_14_125803_create_drug_classes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_06_14_133257_create_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_06_14_140135_create_active_ingredients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_06_14_140754_create_active_ingredient_drug_class_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_06_14_141103_create_products_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2026_06_14_154505_create_product_active_ingredient_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2026_06_14_154817_create_product_images_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2026_06_14_155012_create_product_documents_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2026_06_14_155257_create_dosages_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_06_14_155345_create_withdrawal_periods_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_06_14_155422_create_alternatives_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_06_15_060712_create_indications_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_06_15_060713_create_contraindications_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_06_15_060714_create_precautions_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_06_15_060715_create_side_effects_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2026_06_15_090524_create_drug_interactions_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2026_06_16_003649_create_diseases_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2026_06_16_003910_create_disease_product_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2026_06_16_011743_create_import_jobs_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2026_06_16_023150_add_is_approved_to_import_jobs_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2026_06_16_131228_create_product_company_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2026_07_11_221438_add_fulltext_indexes_to_search_tables',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2026_07_11_232220_create_contact_submissions_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2026_07_12_111729_create_blog_categories_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2026_07_12_111730_create_blogs_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2026_07_21_152308_add_status_and_review_fields_to_products_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2026_07_14_105812_drop_company_id_from_products_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2026_07_24_123843_add_phone_to_users_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2026_07_13_120540_add_role_to_users_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2026_08_01_000000_create_body_systems_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2026_08_01_000001_create_anatomical_structures_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2026_08_01_000002_create_findings_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2026_08_01_000003_create_modifiers_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2026_08_01_000004_create_clinical_signs_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2026_08_01_000005_create_disease_species_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2026_08_01_000006_create_disease_clinical_sign_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2026_08_01_000007_create_synonyms_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2026_08_01_000008_create_abbreviations_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2026_08_01_000009_create_disease_classifications_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2026_08_01_000010_create_transmission_ontology_tables',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2026_08_01_000011_create_host_species_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2026_08_01_000012_create_disease_host_species_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2026_08_01_000013_create_transmission_type_ontology_tables',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2026_08_01_000014_create_reservoir_and_vector_ontology_tables',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2026_08_01_000015_create_clinical_severity_ontology_tables',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2026_08_01_000016_add_slug_to_findings_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2026_08_01_000017_add_is_general_sign_to_findings_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2026_08_01_000018_add_is_pathognomonic_to_disease_clinical_sign_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2026_08_01_000019_create_differential_syndromes_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2026_08_01_000020_create_differential_syndrome_clinical_sign_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2026_08_01_000021_create_differential_syndrome_disease_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2026_08_01_000022_create_medical_articles_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2026_08_01_000024_create_microorganisms_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2026_08_01_000025_add_slug_to_microorganisms_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2026_08_01_000026_add_type_to_anatomical_structures_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2026_08_01_000027_add_ontology_type_to_findings_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2026_08_01_000028_add_modifier_group_to_modifiers_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2026_08_01_000029_add_severity_level_id_to_clinical_signs_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2026_08_01_000030_add_semantic_unique_index_to_clinical_signs_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2026_08_01_000031_add_source_type_to_synonyms_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2026_08_01_000032_add_parent_id_to_findings_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2026_08_01_000033_add_is_noisy_to_findings_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2026_08_01_000034_add_semantic_slug_to_clinical_signs_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2026_08_01_000035_add_index_to_semantic_slug_in_clinical_signs_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2026_08_01_000036_add_is_noisy_to_modifiers_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2026_08_01_000023_add_knowledge_payload_to_diseases_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2026_08_03_111709_create_disease_microorganism_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2026_08_03_111710_create_veterinary_projects_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2026_08_03_114057_fix_disease_relation_columns_to_ulid',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2026_08_05_000000_add_arabic_display_names_to_drugs_ontology_tables',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2026_08_05_000001_add_display_name_ar_to_modifiers_table',22);
