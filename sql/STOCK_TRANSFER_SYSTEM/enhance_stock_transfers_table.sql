-- =====================================================
-- ENHANCE STOCK TRANSFERS TABLE
-- =====================================================

-- Add new columns to stock_transfers table
-- =====================================================
-- =====================================================
-- ENHANCE inv_stock_transfers TABLE (SKIPPING DUPLICATES)
-- =====================================================
ALTER TABLE inv_stock_transfers 
ADD COLUMN approved_by INT(11) NULL,
ADD COLUMN approved_at TIMESTAMP NULL,
ADD COLUMN acknowledged_by INT(11) NULL,
ADD COLUMN acknowledged_at TIMESTAMP NULL,
ADD COLUMN cancelled_by INT(11) NULL,
ADD COLUMN cancelled_at TIMESTAMP NULL,
ADD COLUMN cancellation_reason TEXT NULL,
ADD COLUMN notes TEXT NULL,
ADD COLUMN priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
ADD COLUMN estimated_delivery_date DATE NULL,
ADD COLUMN actual_delivery_date DATE NULL,
ADD COLUMN transfer_method ENUM('hand_delivery', 'courier', 'internal_transport', 'other') DEFAULT 'hand_delivery',
ADD COLUMN tracking_number VARCHAR(100) NULL,
ADD COLUMN insurance_required BOOLEAN DEFAULT FALSE,
ADD COLUMN insurance_value DECIMAL(15,2) NULL,
ADD COLUMN created_by INT(11) NOT NULL;



-- Step 1: Ensure column types match (unsigned)
ALTER TABLE inv_stock_transfers 
MODIFY COLUMN approved_by INT(11) UNSIGNED NULL,
MODIFY COLUMN acknowledged_by INT(11) UNSIGNED NULL,
MODIFY COLUMN cancelled_by INT(11) UNSIGNED NULL,
MODIFY COLUMN created_by INT(11) UNSIGNED NOT NULL,
MODIFY COLUMN updated_by INT(11) UNSIGNED NULL;

-- Step 2: Add foreign key constraints
ALTER TABLE inv_stock_transfers 
ADD CONSTRAINT fk_inv_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_inv_acknowledged_by FOREIGN KEY (acknowledged_by) REFERENCES users(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_inv_cancelled_by FOREIGN KEY (cancelled_by) REFERENCES users(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_inv_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
ADD CONSTRAINT fk_inv_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL;


-- 3. Add indexes for performance
CREATE INDEX idx_inv_stock_transfers_status ON inv_stock_transfers(status);
CREATE INDEX idx_inv_stock_transfers_created_by ON inv_stock_transfers(created_by);
CREATE INDEX idx_inv_stock_transfers_approved_by ON inv_stock_transfers(approved_by);
CREATE INDEX idx_inv_stock_transfers_acknowledged_by ON inv_stock_transfers(acknowledged_by);
CREATE INDEX idx_inv_stock_transfers_transfer_date ON inv_stock_transfers(transfer_date);
CREATE INDEX idx_inv_stock_transfers_priority ON inv_stock_transfers(priority);
CREATE INDEX idx_inv_stock_transfers_from_store ON inv_stock_transfers(from_store);
CREATE INDEX idx_inv_stock_transfers_to_store ON inv_stock_transfers(to_store);

-- 4. Set default created_by where missing
UPDATE inv_stock_transfers SET created_by = 1 WHERE created_by IS NULL;

-- 5. Add triggers to simulate check constraints
DELIMITER //

-- Trigger: estimated_delivery_date >= transfer_date (INSERT)
CREATE TRIGGER trg_chk_inv_estimated_date_insert
BEFORE INSERT ON inv_stock_transfers
FOR EACH ROW
BEGIN
  IF NEW.estimated_delivery_date IS NOT NULL AND NEW.estimated_delivery_date < NEW.transfer_date THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Estimated delivery date cannot be before transfer date';
  END IF;
END;
//

-- Trigger: estimated_delivery_date >= transfer_date (UPDATE)
CREATE TRIGGER trg_chk_inv_estimated_date_update
BEFORE UPDATE ON inv_stock_transfers
FOR EACH ROW
BEGIN
  IF NEW.estimated_delivery_date IS NOT NULL AND NEW.estimated_delivery_date < NEW.transfer_date THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Estimated delivery date cannot be before transfer date';
  END IF;
END;
//

-- Trigger: insurance_value >= 0 (INSERT)
CREATE TRIGGER trg_chk_inv_insurance_value_insert
BEFORE INSERT ON inv_stock_transfers
FOR EACH ROW
BEGIN
  IF NEW.insurance_value IS NOT NULL AND NEW.insurance_value < 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Insurance value cannot be negative';
  END IF;
END;
//

-- Trigger: insurance_value >= 0 (UPDATE)
CREATE TRIGGER trg_chk_inv_insurance_value_update
BEFORE UPDATE ON inv_stock_transfers
FOR EACH ROW
BEGIN
  IF NEW.insurance_value IS NOT NULL AND NEW.insurance_value < 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Insurance value cannot be negative';
  END IF;
END;
//

DELIMITER ;

-- 6. Add comments to columns
ALTER TABLE inv_stock_transfers 
MODIFY COLUMN status ENUM('created', 'assigned', 'approved', 'in_transit', 'acknowledged', 'completed', 'cancelled') DEFAULT 'created' COMMENT 'Transfer workflow status',
MODIFY COLUMN priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal' COMMENT 'Transfer priority level',
MODIFY COLUMN transfer_method ENUM('hand_delivery', 'courier', 'internal_transport', 'other') DEFAULT 'hand_delivery' COMMENT 'Method of transfer';

-- Add foreign key constraints
ALTER TABLE stock_transfers 
ADD CONSTRAINT fk_stock_transfers_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_stock_transfers_acknowledged_by FOREIGN KEY (acknowledged_by) REFERENCES users(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_stock_transfers_cancelled_by FOREIGN KEY (cancelled_by) REFERENCES users(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_stock_transfers_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
ADD CONSTRAINT fk_stock_transfers_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL;

-- Add indexes for better performance
CREATE INDEX idx_stock_transfers_status ON stock_transfers(status);
CREATE INDEX idx_stock_transfers_created_by ON stock_transfers(created_by);
CREATE INDEX idx_stock_transfers_approved_by ON stock_transfers(approved_by);
CREATE INDEX idx_stock_transfers_acknowledged_by ON stock_transfers(acknowledged_by);
CREATE INDEX idx_stock_transfers_transfer_date ON stock_transfers(transfer_date);
CREATE INDEX idx_stock_transfers_priority ON stock_transfers(priority);
CREATE INDEX idx_stock_transfers_from_store ON stock_transfers(from_store);
CREATE INDEX idx_stock_transfers_to_store ON stock_transfers(to_store);

-- Update existing records to set created_by (assuming admin user with ID 1)
UPDATE stock_transfers SET created_by = 1 WHERE created_by IS NULL;

-- Add check constraints for data integrity
ALTER TABLE stock_transfers 
ADD CONSTRAINT chk_stock_transfers_dates CHECK (estimated_delivery_date >= transfer_date),
ADD CONSTRAINT chk_stock_transfers_insurance CHECK (insurance_value >= 0);

-- Add comments for documentation
ALTER TABLE stock_transfers 
MODIFY COLUMN status ENUM('created', 'assigned', 'approved', 'in_transit', 'acknowledged', 'completed', 'cancelled') DEFAULT 'created' COMMENT 'Transfer workflow status',
MODIFY COLUMN priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal' COMMENT 'Transfer priority level',
MODIFY COLUMN transfer_method ENUM('hand_delivery', 'courier', 'internal_transport', 'other') DEFAULT 'hand_delivery' COMMENT 'Method of transfer'; 