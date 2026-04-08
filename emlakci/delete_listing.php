<?php
require_once '../config.php';
require_once '../includes/auth.php';
requireEmlakci();

$id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

if ($id) {
    // Sadece kendi ilanını silebilir
    $stmt = $pdo->prepare("DELETE FROM listings WHERE id = :id AND user_id = :uid AND user_type = 'emlakci'");
    $stmt->execute([':id' => $id, ':uid' => $user_id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success_message'] = $lang === 'tr' ? 'İlan başarıyla silindi.' : 'Listing deleted successfully.';
    } else {
        $_SESSION['error_message'] = $lang === 'tr' ? 'İlan bulunamadı veya silme yetkiniz yok.' : 'Listing not found or access denied.';
    }
}

header('Location: /emlakci/ilanlar');
exit;
?>