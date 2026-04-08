<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Lütfen giriş yapın']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$listing_id = $data['listing_id'] ?? 0;
$content = trim($data['content'] ?? '');

if (!$listing_id || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz veri']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO comments (listing_id, user_id, content, status) VALUES (:lid, :uid, :content, 'approved')");
    $stmt->execute([
        ':lid' => $listing_id,
        ':uid' => $user_id,
        ':content' => $content
    ]);

    $comment_id = $pdo->lastInsertId();
    
    // Get the new comment with user details
    $stmt = $pdo->prepare("SELECT c.*, u.full_name as user_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.id = :id");
    $stmt->execute([':id' => $comment_id]);
    $new_comment = $stmt->fetch();

    echo json_encode([
        'success' => true, 
        'message' => 'Yorumunuz başarıyla eklendi',
        'comment' => [
            'id' => $new_comment['id'],
            'user_name' => $new_comment['user_name'],
            'content' => $new_comment['content'],
            'created_at' => date('d.m.Y H:i', strtotime($new_comment['created_at']))
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Sunucu hatası: ' . $e->getMessage()]);
}
