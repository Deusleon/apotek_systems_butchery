-- =====================================================
-- STOCK TRANSFER STATUS WORKFLOW PERMISSIONS
-- =====================================================

-- Insert permissions for status workflow
INSERT INTO permissions (name, guard_name, created_at, updated_at) VALUES
('assign_transfers', 'web', NOW(), NOW()),
('approve_transfers', 'web', NOW(), NOW()),
('acknowledge_transfers', 'web', NOW(), NOW()),
('complete_transfers', 'web', NOW(), NOW()),
('manage_transfers', 'web', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- Assign permissions to roles
-- Admin role gets all permissions
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id
FROM permissions p, roles r
WHERE p.name IN ('assign_transfers', 'approve_transfers', 'acknowledge_transfers', 'complete_transfers', 'manage_transfers')
AND r.name = 'Admin'
ON DUPLICATE KEY UPDATE permission_id = permission_id;

-- Manager role gets most permissions
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id
FROM permissions p, roles r
WHERE p.name IN ('assign_transfers', 'approve_transfers', 'acknowledge_transfers', 'manage_transfers')
AND r.name = 'Manager'
ON DUPLICATE KEY UPDATE permission_id = permission_id;

-- Store Manager role gets acknowledgment and management permissions
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id
FROM permissions p, roles r
WHERE p.name IN ('acknowledge_transfers', 'manage_transfers')
AND r.name = 'Store Manager'
ON DUPLICATE KEY UPDATE permission_id = permission_id;

-- User role gets basic acknowledgment permission
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id
FROM permissions p, roles r
WHERE p.name IN ('acknowledge_transfers')
AND r.name = 'User'
ON DUPLICATE KEY UPDATE permission_id = permission_id; 