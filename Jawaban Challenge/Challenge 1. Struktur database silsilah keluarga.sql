CREATE TABLE `person` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('1','2') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '1: MEN, 2: WOMEN',
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `level` smallint(5) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `person_parent_id_foreign` (`parent_id`),
  CONSTRAINT `person_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `person` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
