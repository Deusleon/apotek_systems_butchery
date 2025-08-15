-- =====================================================
-- SAMPLE DATA FOR STOCK TRANSFER SYSTEM
-- =====================================================

-- Insert sample stock transfers
INSERT INTO inv_stock_transfers (
    from_store, to_store, transfer_date, status, priority, 
    transfer_method, notes, created_by, created_at
) VALUES
(1, 2, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'completed', 'normal', 'hand_delivery', 'Regular stock replenishment', 1, NOW()),
(2, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'acknowledged', 'high', 'courier', 'Urgent medication transfer', 1, NOW()),
(1, 3, CURDATE(), 'approved', 'normal', 'internal_transport', 'Monthly stock distribution', 1, NOW()),
(3, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'assigned', 'low', 'hand_delivery', 'Overstock redistribution', 1, NOW()),
(2, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'created', 'urgent', 'courier', 'Emergency medication request', 1, NOW());

-- Insert sample audit logs
INSERT INTO inv_stock_transfer_audit_logs (
    transfer_id, user_id, action, old_status, new_status, 
    old_data, new_data, ip_address, user_agent, created_at
) VALUES
(1, 1, 'created', NULL, 'created', NULL, '{"from_store": 1, "to_store": 2}', '192.168.1.100', 'Mozilla/5.0', NOW()),
(1, 1, 'approved', 'created', 'approved', '{"status": "created"}', '{"status": "approved"}', '192.168.1.100', 'Mozilla/5.0', NOW()),
(1, 1, 'completed', 'approved', 'completed', '{"status": "approved"}', '{"status": "completed"}', '192.168.1.100', 'Mozilla/5.0', NOW()),
(2, 1, 'created', NULL, 'created', NULL, '{"from_store": 2, "to_store": 1}', '192.168.1.100', 'Mozilla/5.0', NOW()),
(2, 1, 'approved', 'created', 'approved', '{"status": "created"}', '{"status": "approved"}', '192.168.1.100', 'Mozilla/5.0', NOW()),
(2, 1, 'acknowledged', 'approved', 'acknowledged', '{"status": "approved"}', '{"status": "acknowledged"}', '192.168.1.100', 'Mozilla/5.0', NOW());

-- Insert sample notifications
INSERT INTO inv_stock_transfer_notifications (
    transfer_id, user_id, notification_type, title, message, 
    is_read, email_sent, created_at
) VALUES
(1, 1, 'transfer_created', 'New Stock Transfer Created', 'Transfer #1 has been created from Store 1 to Store 2', FALSE, FALSE, NOW()),
(1, 1, 'transfer_approved', 'Stock Transfer Approved', 'Transfer #1 has been approved and is ready for processing', FALSE, FALSE, NOW()),
(1, 1, 'transfer_completed', 'Stock Transfer Completed', 'Transfer #1 has been completed successfully', FALSE, FALSE, NOW()),
(2, 1, 'transfer_created', 'New Stock Transfer Created', 'Transfer #2 has been created from Store 2 to Store 1', FALSE, FALSE, NOW()),
(2, 1, 'transfer_approved', 'Stock Transfer Approved', 'Transfer #2 has been approved and is ready for processing', FALSE, FALSE, NOW()),
(2, 1, 'transfer_acknowledged', 'Stock Transfer Acknowledged', 'Transfer #2 has been acknowledged by destination store', FALSE, FALSE, NOW()); 

-- =====================================================
-- ENHANCE inv_stock_transfers TABLE
-- =====================================================

ALTER TABLE inv_stock_transfers 
    ADD COLUMN status ENUM('created', 'assigned', 'approved', 'in_transit', 'acknowledged', 'completed', 'cancelled') DEFAULT 'created',
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
    ADD COLUMN created_by INT(11) NOT NULL,
    ADD COLUMN updated_by INT(11) NULL,
    ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Add indexes
CREATE INDEX idx_inv_stock_transfers_status ON inv_stock_transfers(status);
CREATE INDEX idx_inv_stock_transfers_created_by ON inv_stock_transfers(created_by);
CREATE INDEX idx_inv_stock_transfers_approved_by ON inv_stock_transfers(approved_by);
CREATE INDEX idx_inv_stock_transfers_acknowledged_by ON inv_stock_transfers(acknowledged_by);
CREATE INDEX idx_inv_stock_transfers_priority ON inv_stock_transfers(priority);
CREATE INDEX idx_inv_stock_transfers_from_store ON inv_stock_transfers(from_store);
CREATE INDEX idx_inv_stock_transfers_to_store ON inv_stock_transfers(to_store);

-- Foreign key constraints (make sure referenced users exist)
ALTER TABLE inv_stock_transfers
    ADD CONSTRAINT fk_inv_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    ADD CONSTRAINT fk_inv_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    ADD CONSTRAINT fk_inv_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    ADD CONSTRAINT fk_inv_acknowledged_by FOREIGN KEY (acknowledged_by) REFERENCES users(id) ON DELETE SET NULL,
    ADD CONSTRAINT fk_inv_cancelled_by FOREIGN KEY (cancelled_by) REFERENCES users(id) ON DELETE SET NULL;

-- Set default created_by where missing
UPDATE inv_stock_transfers SET created_by = 17 WHERE created_by IS NULL;

-- =====================================================
-- CREATE inv_stock_transfer_audit_logs TABLE
-- =====================================================

CREATE TABLE IF NOT EXISTS inv_stock_transfer_audit_logs (
    id INT(11) NOT NULL AUTO_INCREMENT,
    transfer_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    action VARCHAR(50) NOT NULL,
    old_status VARCHAR(20) NULL,
    new_status VARCHAR(20) NULL,
    old_data TEXT NULL,
    new_data TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_transfer_id (transfer_id),
    KEY idx_user_id (user_id),
    KEY idx_action (action),
    KEY idx_created_at (created_at),
    CONSTRAINT fk_audit_logs_transfer_id FOREIGN KEY (transfer_id) REFERENCES inv_stock_transfers(id) ON DELETE CASCADE,
    CONSTRAINT fk_audit_logs_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CREATE inv_stock_transfer_notifications TABLE
-- =====================================================

CREATE TABLE IF NOT EXISTS inv_stock_transfer_notifications (
    id INT(11) NOT NULL AUTO_INCREMENT,
    transfer_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    notification_type ENUM(
      'transfer_created',
      'transfer_assigned',
      'transfer_approved',
      'transfer_in_transit',
      'transfer_acknowledged',
      'transfer_completed',
      'transfer_cancelled',
      'transfer_reminder'
    ) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    email_sent BOOLEAN DEFAULT FALSE,
    email_sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_transfer_id (transfer_id),
    KEY idx_user_id (user_id),
    KEY idx_notification_type (notification_type),
    KEY idx_is_read (is_read),
    KEY idx_created_at (created_at),
    CONSTRAINT fk_notifications_transfer_id FOREIGN KEY (transfer_id) REFERENCES inv_stock_transfers(id) ON DELETE CASCADE,
    CONSTRAINT fk_notifications_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TRIGGERS for CHECK constraints simulation
-- =====================================================

DELIMITER //

-- estimated_delivery_date >= transfer_date (INSERT)
CREATE TRIGGER trg_inv_estimated_date_insert
BEFORE INSERT ON inv_stock_transfers
FOR EACH ROW
BEGIN
  IF NEW.estimated_delivery_date IS NOT NULL AND NEW.transfer_date IS NOT NULL AND NEW.estimated_delivery_date < NEW.transfer_date THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Estimated delivery date cannot be before transfer date';
  END IF;
END;
//

-- estimated_delivery_date >= transfer_date (UPDATE)
CREATE TRIGGER trg_inv_estimated_date_update
BEFORE UPDATE ON inv_stock_transfers
FOR EACH ROW
BEGIN
  IF NEW.estimated_delivery_date IS NOT NULL AND NEW.transfer_date IS NOT NULL AND NEW.estimated_delivery_date < NEW.transfer_date THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Estimated delivery date cannot be before transfer date';
  END IF;
END;
//

-- insurance_value >= 0 (INSERT)
CREATE TRIGGER trg_inv_insurance_value_insert
BEFORE INSERT ON inv_stock_transfers
FOR EACH ROW
BEGIN
  IF NEW.insurance_value IS NOT NULL AND NEW.insurance_value < 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Insurance value cannot be negative';
  END IF;
END;
//

-- insurance_value >= 0 (UPDATE)
CREATE TRIGGER trg_inv_insurance_value_update
BEFORE UPDATE ON inv_stock_transfers
FOR EACH ROW
BEGIN
  IF NEW.insurance_value IS NOT NULL AND NEW.insurance_value < 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Insurance value cannot be negative';
  END IF;
END;
//

DELIMITER ;

-- =====================================================
-- SAMPLE DATA (using user_id = 17, transfer_id = 7 and 8)
-- =====================================================

INSERT INTO inv_stock_transfers (
    from_store, to_store, transfer_date, status, priority, 
    transfer_method, notes, created_by, created_at
) VALUES
(1, 2, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'completed', 'normal', 'hand_delivery', 'Regular stock replenishment', 17, NOW()),
(2, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'acknowledged', 'high', 'courier', 'Urgent medication transfer', 17, NOW()),
(1, 3, CURDATE(), 'approved', 'normal', 'internal_transport', 'Monthly stock distribution', 17, NOW()),
(3, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'assigned', 'low', 'hand_delivery', 'Overstock redistribution', 17, NOW()),
(2, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'created', 'urgent', 'courier', 'Emergency medication request', 17, NOW());

INSERT INTO inv_stock_transfer_audit_logs (
    transfer_id, user_id, action, old_status, new_status, 
    old_data, new_data, ip_address, user_agent, created_at
) VALUES
(7, 17, 'created', NULL, 'created', NULL, '{"from_store": 1, "to_store": 2}', '192.168.1.100', 'Mozilla/5.0', NOW()),
(7, 17, 'approved', 'created', 'approved', '{"status": "created"}', '{"status": "approved"}', '192.168.1.100', 'Mozilla/5.0', NOW()),
(7, 17, 'completed', 'approved', 'completed', '{"status": "approved"}', '{"status": "completed"}', '192.168.1.100', 'Mozilla/5.0', NOW()),
(8, 17, 'created', NULL, 'created', NULL, '{"from_store": 2, "to_store": 1}', '192.168.1.100', 'Mozilla/5.0', NOW()),
(8, 17, 'approved', 'created', 'approved', '{"status": "created"}', '{"status": "approved"}', '192.168.1.100', 'Mozilla/5.0', NOW()),
(8, 17, 'acknowledged', 'approved', 'acknowledged', '{"status": "approved"}', '{"status": "acknowledged"}', '192.168.1.100', 'Mozilla/5.0', NOW());

INSERT INTO inv_stock_transfer_notifications (
    transfer_id, user_id, notification_type, title, message, 
    is_read, email_sent, created_at
) VALUES
(7, 17, 'transfer_created', 'New Stock Transfer Created', 'Transfer #7 has been created from Store 1 to Store 2', FALSE, FALSE, NOW()),
(7, 17, 'transfer_approved', 'Stock Transfer Approved', 'Transfer #7 has been approved and is ready for processing', FALSE, FALSE, NOW()),
(7, 17, 'transfer_completed', 'Stock Transfer Completed', 'Transfer #7 has been completed successfully', FALSE, FALSE, NOW()),
(8, 17, 'transfer_created', 'New Stock Transfer Created', 'Transfer #8 has been created from Store 2 to Store 1', FALSE, FALSE, NOW()),
(8, 17, 'transfer_approved', 'Stock Transfer Approved', 'Transfer #8 has been approved and is ready for processing', FALSE, FALSE, NOW()),
(8, 17, 'transfer_acknowledged', 'Stock Transfer Acknowledged', 'Transfer #8 has been acknowledged by destination store', FALSE, FALSE, NOW());
