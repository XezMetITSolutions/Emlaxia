<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$listing_id = $data['listing_id'] ?? 0;

if (!$listing_id) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz ilan']);
    exit;
}

try {
    // Favorilerde var mı kontrol et
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = :uid AND listing_id = :lid");
    $stmt->execute([':uid' => $user_id, ':lid' => $listing_id]);
    $favorite = $stmt->fetch();

    if ($favorite) {
        // Varsa kaldır
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE id = :id");
        $stmt->execute([':id' => $favorite['id']]);
        echo json_encode(['success' => true, 'is_favorite' => false]);
    } else {
        // Yoksa ekle
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, listing_id) VALUES (:uid, :lid)");
        $stmt->execute([':uid' => $user_id, ':lid' => $listing_id]);
        echo json_encode(['success' => true, 'is_favorite' => true]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Sunucu hatası']);
}
