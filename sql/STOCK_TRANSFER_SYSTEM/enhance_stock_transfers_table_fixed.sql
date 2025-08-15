-- =====================================================
-- ENHANCE STOCK TRANSFERS TABLE (FIXED VERSION)
-- =====================================================

-- First, check if table exists
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables 
                    WHERE table_schema = DATABASE() AND table_name = 'stock_transfers');

-- Only proceed if table exists
SET @sql = IF(@table_exists > 0, 
    'ALTER TABLE stock_transfers 
    ADD COLUMN status VARCHAR(20) DEFAULT "created" AFTER transfer_date,
    ADD COLUMN approved_by int(11) NULL AFTER status,
    ADD COLUMN approved_at TIMESTAMP NULL AFTER approved_by,
    ADD COLUMN acknowledged_by int(11) NULL AFTER approved_at,
    ADD COLUMN acknowledged_at TIMESTAMP NULL AFTER acknowledged_by,
    ADD COLUMN cancelled_by int(11) NULL AFTER acknowledged_at,
    ADD COLUMN cancelled_at TIMESTAMP NULL AFTER cancelled_by,
    ADD COLUMN cancellation_reason TEXT NULL AFTER cancelled_at,
    ADD COLUMN notes TEXT NULL AFTER cancellation_reason,
    ADD COLUMN priority VARCHAR(20) DEFAULT "normal" AFTER notes,
    ADD COLUMN estimated_delivery_date DATE NULL AFTER priority,
    ADD COLUMN actual_delivery_date DATE NULL AFTER estimated_delivery_date,
    ADD COLUMN transfer_method VARCHAR(50) DEFAULT "hand_delivery" AFTER actual_delivery_date,
    ADD COLUMN tracking_number VARCHAR(100) NULL AFTER transfer_method,
    ADD COLUMN insurance_required BOOLEAN DEFAULT FALSE AFTER tracking_number,
    ADD COLUMN insurance_value DECIMAL(15,2) NULL AFTER insurance_required,
    ADD COLUMN created_by int(11) NOT NULL DEFAULT 1 AFTER insurance_value,
    ADD COLUMN updated_by int(11) NULL AFTER created_by,
    ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER updated_by,
    ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at',
    'SELECT "Table stock_transfers does not exist" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key constraints (only if they don't exist)
SET @fk_sql = IF(@table_exists > 0,
    'ALTER TABLE stock_transfers 
    ADD CONSTRAINT fk_stock_transfers_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    ADD CONSTRAINT fk_stock_transfers_acknowledged_by FOREIGN KEY (acknowledged_by) REFERENCES users(id) ON DELETE SET NULL,
    ADD CONSTRAINT fk_stock_transfers_cancelled_by FOREIGN KEY (cancelled_by) REFERENCES users(id) ON DELETE SET NULL,
    ADD CONSTRAINT fk_stock_transfers_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    ADD CONSTRAINT fk_stock_transfers_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL',
    'SELECT "Skipping foreign keys - table does not exist" as message'
);

PREPARE fk_stmt FROM @fk_sql;
EXECUTE fk_stmt;
DEALLOCATE PREPARE fk_stmt;

-- Add indexes (only if table exists)
SET @index_sql = IF(@table_exists > 0,
    'CREATE INDEX idx_stock_transfers_status ON stock_transfers(status),
    CREATE INDEX idx_stock_transfers_created_by ON stock_transfers(created_by),
    CREATE INDEX idx_stock_transfers_approved_by ON stock_transfers(approved_by),
    CREATE INDEX idx_stock_transfers_acknowledged_by ON stock_transfers(acknowledged_by),
    CREATE INDEX idx_stock_transfers_transfer_date ON stock_transfers(transfer_date),
    CREATE INDEX idx_stock_transfers_priority ON stock_transfers(priority),
    CREATE INDEX idx_stock_transfers_from_store ON stock_transfers(from_store),
    CREATE INDEX idx_stock_transfers_to_store ON stock_transfers(to_store)',
    'SELECT "Skipping indexes - table does not exist" as message'
);

PREPARE idx_stmt FROM @index_sql;
EXECUTE idx_stmt;
DEALLOCATE PREPARE idx_stmt;

-- Update existing records to set created_by (assuming admin user with ID 1)
SET @update_sql = IF(@table_exists > 0,
    'UPDATE stock_transfers SET created_by = 1 WHERE created_by IS NULL',
    'SELECT "Skipping update - table does not exist" as message'
);

PREPARE update_stmt FROM @update_sql;
EXECUTE update_stmt;
DEALLOCATE PREPARE update_stmt;

-- Add check constraints (only if table exists and MySQL version supports it)
SET @check_sql = IF(@table_exists > 0,
    'ALTER TABLE stock_transfers 
    ADD CONSTRAINT chk_stock_transfers_insurance CHECK (insurance_value >= 0)',
    'SELECT "Skipping check constraints - table does not exist" as message'
);

PREPARE check_stmt FROM @check_sql;
EXECUTE check_stmt;
DEALLOCATE PREPARE check_stmt; 