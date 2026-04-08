<?php
/**
 * One-time Migration Script: Consolidate Normal Member (uye) into Individual (bireysel)
 */
require_once '../config.php';

// Check admin auth or just run if in CLI
if (php_sapi_name() !== 'cli' && (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in'])) {
    die('Unauthorized.');
}

try {
    // 1. Update all users of type 'uye' to 'bireysel'
    $stmt = $pdo->prepare("UPDATE users SET user_type = 'bireysel' WHERE user_type = 'uye'");
    $stmt->execute();
    $updated_users = $stmt->rowCount();

    echo "Migration Successful!\n";
    echo "--------------------\n";
    echo "Users updated: $updated_users\n";
    echo "Normal Member (uye) model has been merged into Individual (bireysel).\n";

} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
?>
