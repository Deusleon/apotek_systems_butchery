-- =====================================================
-- CREATE NOTIFICATIONS TABLE FOR inv_stock_transfers
-- =====================================================
CREATE TABLE inv_stock_transfer_notifications (
    id INT(11) NOT NULL AUTO_INCREMENT,
    transfer_id INT(11) NOT NULL,  -- Must match inv_stock_transfers.id
    user_id INT(10) UNSIGNED NOT NULL,  -- Must match users.id
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

    CONSTRAINT fk_notifications_transfer_id 
        FOREIGN KEY (transfer_id) REFERENCES inv_stock_transfers(id) ON DELETE CASCADE,
    CONSTRAINT fk_notifications_user_id 
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
