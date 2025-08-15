-- Create stock_adjustment_logs table
CREATE TABLE `stock_adjustment_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `current_stock_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `store_id` bigint(20) UNSIGNED NOT NULL,
  `adjustment_quantity` decimal(10,2) NOT NULL,
  `adjustment_type` varchar(255) NOT NULL COMMENT 'increase or decrease',
  `reason` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_adjustment_logs_current_stock_id_foreign` (`current_stock_id`),
  KEY `stock_adjustment_logs_user_id_foreign` (`user_id`),
  KEY `stock_adjustment_logs_store_id_foreign` (`store_id`),
  CONSTRAINT `stock_adjustment_logs_current_stock_id_foreign` FOREIGN KEY (`current_stock_id`) REFERENCES `current_stocks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_adjustment_logs_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_adjustment_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 