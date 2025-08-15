-- =====================================================
-- TEST SQL SYNTAX FOR STOCK TRANSFER SYSTEM
-- =====================================================

-- Test 1: Check if stock_transfers table exists
SELECT COUNT(*) as table_exists FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'stock_transfers';

-- Test 2: Check if required columns exist in stock_transfers
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
FROM information_schema.columns 
WHERE table_schema = DATABASE() 
AND table_name = 'stock_transfers' 
AND column_name IN ('status', 'approved_by', 'created_by', 'priority');

-- Test 3: Check if audit logs table exists
SELECT COUNT(*) as audit_table_exists FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'stock_transfer_audit_logs';

-- Test 4: Check if notifications table exists
SELECT COUNT(*) as notifications_table_exists FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'stock_transfer_notifications';

-- Test 5: Check if permissions exist
SELECT COUNT(*) as permissions_exist FROM permissions 
WHERE name LIKE 'stock_transfer.%';

-- Test 6: Check if roles exist
SELECT COUNT(*) as roles_exist FROM roles 
WHERE name IN ('Stock Transfer Manager', 'Stock Transfer Approver');

-- Test 7: Verify foreign key constraints
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE table_schema = DATABASE() 
AND table_name = 'stock_transfers' 
AND referenced_table_name IS NOT NULL;

-- Test 8: Check indexes
SHOW INDEX FROM stock_transfers;

-- Test 9: Verify ENUM values
SELECT COLUMN_NAME, COLUMN_TYPE 
FROM information_schema.columns 
WHERE table_schema = DATABASE() 
AND table_name = 'stock_transfers' 
AND data_type = 'enum'; 