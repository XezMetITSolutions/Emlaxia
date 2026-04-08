<?php
require_once 'config.php';
$output = '';
try {
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    $output = print_r($columns, true);
} catch (Exception $e) {
    $output = "Error: " . $e->getMessage();
}
file_put_contents('scratch/schema_output.txt', $output);
