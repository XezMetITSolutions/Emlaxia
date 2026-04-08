<?php
/**
 * AJAX endpoint for changing language
 */
session_start();

header('Content-Type: application/json');

// Sadece POST request kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Dil parametresini al
$newLang = $_POST['lang'] ?? $_GET['lang'] ?? '';

// Dil kontrolü
if (!in_array($newLang, ['tr', 'en'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid language']);
    exit;
}

// Session'a kaydet
$_SESSION['lang'] = $newLang;

// Başarılı yanıt
echo json_encode([
    'success' => true,
    'lang' => $newLang,
    'message' => $newLang == 'tr' ? 'Dil Türkçe olarak değiştirildi' : 'Language changed to English'
]);
?>


