-- Create price_categories table if it doesn't exist
CREATE TABLE IF NOT EXISTS `price_categories` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create inv_stores table if it doesn't exist
CREATE TABLE IF NOT EXISTS `inv_stores` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `address` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create inv_suppliers table if it doesn't exist
CREATE TABLE IF NOT EXISTS `inv_suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create users table if it doesn't exist
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create import_history table
CREATE TABLE IF NOT EXISTS `import_history` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `store_id` bigint(20) UNSIGNED NOT NULL,
  `price_category_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `total_records` int(11) NOT NULL DEFAULT '0',
  `successful_records` int(11) NOT NULL DEFAULT '0',
  `failed_records` int(11) NOT NULL DEFAULT '0',
  `error_log` text,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `import_history_store_id_foreign` (`store_id`),
  KEY `import_history_price_category_id_foreign` (`price_category_id`),
  KEY `import_history_supplier_id_foreign` (`supplier_id`),
  KEY `import_history_created_by_foreign` (`created_by`),
  CONSTRAINT `import_history_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `inv_stores` (`id`),
  CONSTRAINT `import_history_price_category_id_foreign` FOREIGN KEY (`price_category_id`) REFERENCES `price_categories` (`id`),
  CONSTRAINT `import_history_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `inv_suppliers` (`id`),
  CONSTRAINT `import_history_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert a default user if none exists
INSERT INTO `users` (`name`, `email`, `password`, `created_at`, `updated_at`)
SELECT 'Admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()
WHERE NOT EXISTS (SELECT * FROM `users` LIMIT 1);

-- Insert a default store if none exists
INSERT INTO `inv_stores` (`name`, `code`, `status`, `created_at`, `updated_at`)
SELECT 'Main Store', 'MAIN', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT * FROM `inv_stores` LIMIT 1);

-- Insert a default price category if none exists
INSERT INTO `price_categories` (`name`, `description`, `status`, `created_at`, `updated_at`)
SELECT 'Default', 'Default price category', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT * FROM `price_categories` LIMIT 1);

-- Insert a default supplier if none exists
INSERT INTO `inv_suppliers` (`name`, `contact_person`, `phone`, `status`, `created_at`, `updated_at`)
SELECT 'Default Supplier', 'John Doe', '1234567890', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT * FROM `inv_suppliers` LIMIT 1); 