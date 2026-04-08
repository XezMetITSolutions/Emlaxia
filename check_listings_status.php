<?php
require_once 'c:\Users\Anwender\Downloads\Emlaxia\config.php';
try {
    $stmt = $pdo->query("SELECT id, status, approval_status FROM listings LIMIT 5");
    $listings = $stmt->fetchAll();
    foreach ($listings as $listing) {
        echo "ID: " . $listing['id'] . ", Status: " . $listing['status'] . ", Approval Status: " . $listing['approval_status'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
