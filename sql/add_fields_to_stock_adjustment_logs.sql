-- Add previous_quantity, new_quantity, and reference_number columns to stock_adjustment_logs table
ALTER TABLE `stock_adjustment_logs` 
ADD COLUMN `previous_quantity` DECIMAL(10,2) DEFAULT 0.00 AFTER `store_id`,
ADD COLUMN `new_quantity` DECIMAL(10,2) DEFAULT 0.00 AFTER `previous_quantity`,
ADD COLUMN `reference_number` VARCHAR(50) DEFAULT NULL AFTER `notes`; 