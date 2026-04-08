<?php
require_once 'c:\Users\Anwender\Downloads\Emlaxia\config.php';
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM listings");
    $total = $stmt->fetch()['total'];
    echo "Total listings: " . $total . "\n";

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM listings WHERE status = 'active'");
    $active = $stmt->fetch()['total'];
    echo "Active listings: " . $active . "\n";

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM listings WHERE approval_status = 'approved'");
    $approved = $stmt->fetch()['total'];
    echo "Approved listings: " . $approved . "\n";

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM listings WHERE status = 'active' AND approval_status = 'approved'");
    $both = $stmt->fetch()['total'];
    echo "Active and Approved listings: " . $both . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
