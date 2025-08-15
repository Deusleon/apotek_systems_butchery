-- =====================================================
-- CREATE AUDIT LOGS TABLE FOR inv_stock_transfers
-- =====================================================
CREATE TABLE inv_stock_transfer_audit_logs (
    id INT(11) NOT NULL AUTO_INCREMENT,
    transfer_id INT(11) NOT NULL,  -- ✅ must match inv_stock_transfers.id (signed)
    user_id INT(10) UNSIGNED NOT NULL, -- ✅ match users.id which is unsigned
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

    CONSTRAINT fk_audit_logs_transfer_id 
        FOREIGN KEY (transfer_id) REFERENCES inv_stock_transfers(id) ON DELETE CASCADE,
    CONSTRAINT fk_audit_logs_user_id 
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



ALTER TABLE inv_stock_transfer_audit_logs 
MODIFY COLUMN action VARCHAR(50) NOT NULL COMMENT 'Action performed (e.g., created, updated)',
MODIFY COLUMN old_status VARCHAR(20) NULL COMMENT 'Previous transfer status',
MODIFY COLUMN new_status VARCHAR(20) NULL COMMENT 'New transfer status',
MODIFY COLUMN old_data TEXT NULL COMMENT 'Snapshot of data before change',
MODIFY COLUMN new_data TEXT NULL COMMENT 'Snapshot of data after change';
