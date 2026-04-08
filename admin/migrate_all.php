<?php
require_once '../config.php';

// Admin kontrolü (opsiyonel ama güvenlik için iyi)
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    die("Yetkisiz erişim. Lütfen admin girişi yapın.");
}

try {
    // 1. Favoriler Tablosu
    $pdo->exec("CREATE TABLE IF NOT EXISTS favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        listing_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY user_listing (user_id, listing_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "Favorites tablosu kontrol edildi/oluşturuldu.<br>";

    // 2. Yorumlar Tablosu
    $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        listing_id INT NOT NULL,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "Comments tablosu kontrol edildi/oluşturuldu.<br>";

    // 3. Mesajlar Tablosu
    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        listing_id INT NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "Messages tablosu kontrol edildi/oluşturuldu.<br>";

    // 4. Teklifler Tablosu
    $pdo->exec("CREATE TABLE IF NOT EXISTS offers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        listing_id INT NOT NULL,
        customer_name VARCHAR(255) NOT NULL,
        customer_email VARCHAR(255) NOT NULL,
        customer_phone VARCHAR(50),
        offer_amount DECIMAL(15,2) NOT NULL,
        message TEXT,
        status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "Offers tablosu kontrol edildi/oluşturuldu.<br>";

    echo "<h3>Tüm tablolar başarıyla güncellendi!</h3>";
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}
