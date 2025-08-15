-- Create the stock_adjustment_logs table with all required fields
CREATE TABLE IF NOT EXISTS `stock_adjustment_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `current_stock_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `adjustment_quantity` decimal(10,2) NOT NULL,
  `adjustment_type` enum('increase','decrease') NOT NULL,
  `reason` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `current_stock_id` (`current_stock_id`),
  KEY `user_id` (`user_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci; 