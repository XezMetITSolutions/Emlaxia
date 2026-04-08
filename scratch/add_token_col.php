<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'activation_token'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE users ADD COLUMN activation_token VARCHAR(255) NULL AFTER email_verified");
        echo "✅ 'activation_token' kolonu eklendi.\n";
    } else {
        echo "ℹ️ 'activation_token' kolonu zaten mevcut.\n";
    }
} catch (PDOException $e) {
    die("❌ Hata: " . $e->getMessage());
}
?>
