<?php
require_once '../config.php';

try {
    // activation_token ekle
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'activation_token'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE users ADD COLUMN activation_token VARCHAR(255) NULL AFTER email_verified");
        echo "✅ 'activation_token' kolonu başarıyla eklendi.<br>";
    } else {
        echo "ℹ️ 'activation_token' kolonu zaten mevcut.<br>";
    }

    // user_type ENUM güncelle (uye kaldırıldı)
    $pdo->exec("ALTER TABLE users MODIFY COLUMN user_type ENUM('emlakci', 'bireysel') NOT NULL");
    echo "✅ 'user_type' ENUM başarıyla güncellendi (uye kaldırıldı).<br>";

    echo "<h1>Veritabanı Düzeltme Tamamlandı</h1>";
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage();
}
