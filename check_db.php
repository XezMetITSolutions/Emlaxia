<?php
require_once 'config.php';
try {
    $stmt = $pdo->query("DESCRIBE listings");
    $columns = $stmt->fetchAll();
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>