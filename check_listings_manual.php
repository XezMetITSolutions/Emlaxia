<?php
require_once 'c:\Users\Anwender\Downloads\Emlaxia\config.php';
try {
    $stmt = $pdo->query("SELECT id, status, approval_status, listing_type FROM listings");
    $list = $stmt->fetchAll();
    echo "Total: " . count($list) . "\n";
    foreach ($list as $l) {
        echo "ID: {$l['id']}, Status: {$l['status']}, Approval: {$l['approval_status']}, Type: {$l['listing_type']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
