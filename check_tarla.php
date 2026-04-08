<?php
require_once 'config.php';
$stmt = $pdo->query("SELECT id, title_tr, slug, image1, property_type, status, approval_status FROM listings WHERE property_type = 'tarla'");
$rows = $stmt->fetchAll();
header('Content-Type: application/json');
echo json_encode($rows, JSON_PRETTY_PRINT);
