<?php
/**
 * Migration: Favorites and Comments tables
 */
require_once '../config.php';

// Admin kontrolü
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    die('Unauthorized');
}

$results = [];

try {
    // 1. Favorites tablosu
    $pdo->exec("CREATE TABLE IF NOT EXISTS favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        listing_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY user_listing (user_id, listing_id),
        CONSTRAINT fk_fav_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_fav_listing FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $results[] = "✅ 'favorites' tablosu oluşturuldu.";

    // 2. Comments tablosu
    $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        listing_id INT NOT NULL,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_comm_listing FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
        CONSTRAINT fk_comm_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $results[] = "✅ 'comments' tablosu oluşturuldu.";

    echo "<h1>Migration Tamamlandı</h1>";
    foreach ($results as $r) {
        echo "<p>$r</p>";
    }
    echo "<br><a href='dashboard'>Dashboard'a Dön</a>";

} catch (PDOException $e) {
    die("❌ Migration hatası: " . $e->getMessage());
}
?>
