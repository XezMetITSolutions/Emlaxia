<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın']);
    exit;
}

$sender_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

$listing_id = $data['listing_id'] ?? 0;
$message = $data['message'] ?? '';

if (!$listing_id || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz veri']);
    exit;
}

try {
    // Ilan sahibini bul
    $stmt = $pdo->prepare("SELECT user_id FROM listings WHERE id = :id");
    $stmt->execute([':id' => $listing_id]);
    $listing = $stmt->fetch();

    if (!$listing) {
        echo json_encode(['success' => false, 'message' => 'İlan bulunamadı']);
        exit;
    }

    $receiver_id = $listing['user_id'];
    
    if (!$receiver_id) {
        echo json_encode(['success' => false, 'message' => 'Bu ilan için mesaj gönderilemez (İlan sahibi bulunamadı).']);
        exit;
    }

    if ($sender_id == $receiver_id) {
        echo json_encode(['success' => false, 'message' => 'Kendi ilanınıza mesaj gönderemezsiniz']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, listing_id, message) VALUES (:sid, :rid, :lid, :msg)");
    $stmt->execute([
        ':sid' => $sender_id,
        ':rid' => $receiver_id,
        ':lid' => $listing_id,
        ':msg' => $message
    ]);

    echo json_encode(['success' => true, 'message' => 'Mesajınız gönderildi']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Sunucu hatası: ' . $e->getMessage()]);
}
