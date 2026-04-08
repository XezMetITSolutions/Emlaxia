<?php
require_once 'config.php';

try {
    // Add reset_token and reset_expires to users table if they don't exist
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_token VARCHAR(255) NULL AFTER activation_token");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_expires DATETIME NULL AFTER reset_token");
    
    echo "Database updated successfully: Added reset_token and reset_expires columns.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columns already exist.";
    } else {
        echo "Error updated database: " . $e->getMessage();
    }
}
?>
