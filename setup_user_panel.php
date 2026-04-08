<?php
require_once 'config.php';

$sql = "
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    listing_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, listing_id)
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    listing_id INT DEFAULT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS listing_price_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    old_price DECIMAL(15,2),
    new_price DECIMAL(15,2),
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

try {
    $pdo->exec($sql);
    echo "Tables created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
