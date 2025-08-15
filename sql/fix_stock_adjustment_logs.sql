DROP TABLE IF EXISTS `stock_adjustment_logs`;

CREATE TABLE `stock_adjustment_logs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `current_stock_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `store_id` INT(11) UNSIGNED NOT NULL,
  `previous_quantity` DECIMAL(10,2) DEFAULT 0.00,
  `new_quantity` DECIMAL(10,2) DEFAULT 0.00,
  `adjustment_quantity` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `adjustment_type` ENUM('increase', 'decrease') NOT NULL,
  `reason` VARCHAR(255) NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `reference_number` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_adjustment_logs_current_stock_id_foreign` (`current_stock_id`),
  KEY `stock_adjustment_logs_user_id_foreign` (`user_id`),
  KEY `stock_adjustment_logs_store_id_foreign` (`store_id`),
  CONSTRAINT `stock_adjustment_logs_current_stock_id_foreign`
    FOREIGN KEY (`current_stock_id`) REFERENCES `inv_current_stock` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_adjustment_logs_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_adjustment_logs_store_id_foreign`
    FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If the table exists, add the missing columns if they don't exist
ALTER TABLE `stock_adjustment_logs` 
ADD COLUMN IF NOT EXISTS `previous_quantity` DECIMAL(10,2) DEFAULT 0.00 AFTER `store_id`,
ADD COLUMN IF NOT EXISTS `new_quantity` DECIMAL(10,2) DEFAULT 0.00 AFTER `previous_quantity`,
ADD COLUMN IF NOT EXISTS `reference_number` VARCHAR(50) DEFAULT NULL AFTER `notes`;

-- Check and update the foreign key constraint if needed
-- First drop the constraint if it exists
ALTER TABLE `stock_adjustment_logs` 
DROP FOREIGN KEY IF EXISTS `stock_adjustment_logs_current_stock_id_foreign`;

-- Then add it back with the correct reference
ALTER TABLE `stock_adjustment_logs` 
ADD CONSTRAINT `stock_adjustment_logs_current_stock_id_foreign` 
FOREIGN KEY (`current_stock_id`) REFERENCES `inv_current_stock` (`id`) ON DELETE CASCADE;
