-- =====================================================
-- ADD STOCK TRANSFER PERMISSIONS
-- =====================================================

-- Insert permissions into permissions table
INSERT INTO permissions (name, guard_name, created_at, updated_at) VALUES
('stock_transfer.view', 'web', NOW(), NOW()),
('stock_transfer.create', 'web', NOW(), NOW()),
('stock_transfer.edit', 'web', NOW(), NOW()),
('stock_transfer.delete', 'web', NOW(), NOW()),
('stock_transfer.approve', 'web', NOW(), NOW()),
('stock_transfer.acknowledge', 'web', NOW(), NOW()),
('stock_transfer.cancel', 'web', NOW(), NOW()),
('stock_transfer.export', 'web', NOW(), NOW()),
('stock_transfer.report', 'web', NOW(), NOW());

-- Create role for stock transfer management
INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES
('Stock Transfer Manager', 'web', NOW(), NOW()),
('Stock Transfer Approver', 'web', NOW(), NOW());

-- Get role IDs
SET @transfer_manager_role_id = (SELECT id FROM roles WHERE name = 'Stock Transfer Manager' LIMIT 1);
SET @transfer_approver_role_id = (SELECT id FROM roles WHERE name = 'Stock Transfer Approver' LIMIT 1);

-- Assign permissions to Stock Transfer Manager role
INSERT INTO role_has_permissions (permission_id, role_id) 
SELECT p.id, @transfer_manager_role_id
FROM permissions p 
WHERE p.name IN (
    'stock_transfer.view',
    'stock_transfer.create',
    'stock_transfer.edit',
    'stock_transfer.acknowledge',
    'stock_transfer.export',
    'stock_transfer.report'
);

-- Assign permissions to Stock Transfer Approver role
INSERT INTO role_has_permissions (permission_id, role_id) 
SELECT p.id, @transfer_approver_role_id
FROM permissions p 
WHERE p.name IN (
    'stock_transfer.view',
    'stock_transfer.approve',
    'stock_transfer.cancel',
    'stock_transfer.export',
    'stock_transfer.report'
);

-- Assign Stock Transfer Manager role to existing admin users (assuming admin role exists)
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT @transfer_manager_role_id, 'App\\Models\\User', u.id
FROM users u
INNER JOIN model_has_roles mhr ON u.id = mhr.model_id
INNER JOIN roles r ON mhr.role_id = r.id
WHERE r.name = 'Admin' AND mhr.model_type = 'App\\Models\\User'
AND NOT EXISTS (
    SELECT 1 FROM model_has_roles mhr2 
    WHERE mhr2.role_id = @transfer_manager_role_id 
    AND mhr2.model_id = u.id 
    AND mhr2.model_type = 'App\\Models\\User'
);

-- Assign Stock Transfer Approver role to existing manager users (assuming manager role exists)
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT @transfer_approver_role_id, 'App\\Models\\User', u.id
FROM users u
INNER JOIN model_has_roles mhr ON u.id = mhr.model_id
INNER JOIN roles r ON mhr.role_id = r.id
WHERE r.name = 'Manager' AND mhr.model_type = 'App\\Models\\User'
AND NOT EXISTS (
    SELECT 1 FROM model_has_roles mhr2 
    WHERE mhr2.role_id = @transfer_approver_role_id 
    AND mhr2.model_id = u.id 
    AND mhr2.model_type = 'App\\Models\\User'
); 