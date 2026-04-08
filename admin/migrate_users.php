<?php
/**
 * Migration: Users tablosu ve listings güncellemeleri
 * Emlakçı ve Bireysel kullanıcı panelleri için veritabanı altyapısı
 */
require_once '../config.php';

// Admin kontrolü
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    die('Unauthorized');
}

$results = [];

try {
    // 1. Users tablosu oluştur veya eksik kolonları ekle
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $results[] = "✅ 'users' tablosu kontrol edildi.";

    $user_columns = [
        'phone' => "VARCHAR(20)",
        'full_name' => "VARCHAR(255)",
        'user_type' => "ENUM('emlakci', 'bireysel') NOT NULL",
        'firma_adi' => "VARCHAR(255) NULL",
        'vergi_no' => "VARCHAR(50) NULL",
        'lisans_no' => "VARCHAR(100) NULL",
        'ofis_adresi' => "TEXT NULL",
        'logo' => "VARCHAR(255) NULL",
        'profil_foto' => "VARCHAR(255) NULL",
        'bio' => "TEXT NULL",
        'website' => "VARCHAR(255) NULL",
        'status' => "ENUM('pending', 'active', 'suspended', 'rejected') DEFAULT 'pending'",
        'email_verified' => "TINYINT(1) DEFAULT 0",
        'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        'last_login' => "TIMESTAMP NULL"
    ];

    foreach ($user_columns as $col => $definition) {
        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE '$col'");
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE users ADD COLUMN $col $definition");
            $results[] = "✅ 'users' tablosuna '$col' kolonu eklendi.";
        }
    }

    // 2. listings tablosuna user_id kolonu ekle
    $stmt = $pdo->query("SHOW COLUMNS FROM listings LIKE 'user_id'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE listings ADD COLUMN user_id INT NULL AFTER created_by");
        $results[] = "✅ 'listings' tablosuna 'user_id' kolonu eklendi.";
    } else {
        $results[] = "ℹ️ 'user_id' kolonu zaten mevcut.";
    }

    // 3. listings tablosuna user_type kolonu ekle
    $stmt = $pdo->query("SHOW COLUMNS FROM listings LIKE 'user_type'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE listings ADD COLUMN user_type ENUM('admin','emlakci','bireysel') DEFAULT 'admin' AFTER user_id");
        $results[] = "✅ 'listings' tablosuna 'user_type' kolonu eklendi.";
    } else {
        $results[] = "ℹ️ 'user_type' kolonu zaten mevcut.";
    }

    // 4. listings tablosuna approval_status kolonu ekle
    $stmt = $pdo->query("SHOW COLUMNS FROM listings LIKE 'approval_status'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE listings ADD COLUMN approval_status ENUM('pending','approved','rejected') DEFAULT 'pending' AFTER status");
        $results[] = "✅ 'listings' tablosuna 'approval_status' kolonu eklendi (Varsayılan: pending).";
    } else {
        $results[] = "ℹ️ 'approval_status' kolonu zaten mevcut.";
    }

    // 5. listings tablosuna rejection_reason kolonu ekle
    $stmt = $pdo->query("SHOW COLUMNS FROM listings LIKE 'rejection_reason'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE listings ADD COLUMN rejection_reason TEXT NULL AFTER approval_status");
        $results[] = "✅ 'listings' tablosuna 'rejection_reason' kolonu eklendi.";
    } else {
        $results[] = "ℹ️ 'rejection_reason' kolonu zaten mevcut.";
    }

    // 6. listings tablosuna ilan_sahibi_turu kolonu ekle
    $stmt = $pdo->query("SHOW COLUMNS FROM listings LIKE 'ilan_sahibi_turu'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE listings ADD COLUMN ilan_sahibi_turu VARCHAR(50) NULL DEFAULT 'Admin' AFTER rejection_reason");
        $results[] = "✅ 'listings' tablosuna 'ilan_sahibi_turu' kolonu eklendi.";
    } else {
        $results[] = "ℹ️ 'ilan_sahibi_turu' kolonu zaten mevcut.";
    }

    // 7. Mevcut ilanları admin olarak işaretle
    $pdo->exec("UPDATE listings SET user_type = 'admin', approval_status = 'approved' WHERE user_type IS NULL OR user_type = ''");
    $results[] = "✅ Mevcut ilanlar 'admin' tipi ve 'approved' statüsü ile güncellendi.";

    // 7. Foreign key eklemeyi dene (hata olursa sessizce geç)
    try {
        $pdo->exec("ALTER TABLE listings ADD CONSTRAINT fk_listings_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL");
        $results[] = "✅ Foreign key constraint eklendi.";
    } catch (Exception $e) {
        $results[] = "ℹ️ Foreign key zaten mevcut veya eklenemedi: " . $e->getMessage();
    }

    echo "<h1>Migration Tamamlandı</h1>";
    foreach ($results as $r) {
        echo "<p>$r</p>";
    }
    echo "<br><a href='dashboard'>Dashboard'a Dön</a>";

} catch (PDOException $e) {
    die("❌ Migration hatası: " . $e->getMessage());
}
?>