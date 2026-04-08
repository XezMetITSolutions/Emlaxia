<?php
require_once '../config.php';
require_once '../includes/auth.php';
requireBireysel();
$id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM listings WHERE id = :id AND user_id = :uid AND user_type = 'bireysel'");
    $stmt->execute([':id' => $id, ':uid' => $user_id]);
    $_SESSION['success_message'] = $stmt->rowCount() > 0 ? ($lang === 'tr' ? 'İlan silindi.' : 'Deleted.') : ($lang === 'tr' ? 'İlan bulunamadı.' : 'Not found.');
}
header('Location: /bireysel/ilanlar');
exit;
?>