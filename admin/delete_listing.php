<?php
/**
 * AJAX endpoint for deleting listings
 */
require_once '../config.php';

// Admin kontrolü
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    // http_response_code(401); // Prevent HTML error page interception
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Please login again', 'redirect' => 'login.php']);
    exit;
}

// Role check
if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') {
    // http_response_code(403); // Prevent HTML error page interception
    echo json_encode(['success' => false, 'message' => 'Permission denied']);
    exit;
}

// JSON response header
header('Content-Type: application/json');

// Sadece POST request kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // http_response_code(405); // Prevent HTML error page interception
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// ID kontrolü
$id = $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    // http_response_code(400); // Prevent HTML error page interception
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

try {
    // İlanın resimlerini de sil (opsiyonel)
    $stmt = $pdo->prepare("SELECT image1, image2, image3, image4, image5 FROM listings WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $listing = $stmt->fetch();

    if (!$listing) {
        // http_response_code(404); // Prevent HTML error page interception
        echo json_encode(['success' => false, 'message' => 'Listing not found']);
        exit;
    }

    // İlanı sil
    $stmt = $pdo->prepare("DELETE FROM listings WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // Resimleri de sil (opsiyonel)
    $uploads_dir = __DIR__ . '/../uploads/';
    for ($i = 1; $i <= 5; $i++) {
        if (!empty($listing['image' . $i])) {
            $image_path = $uploads_dir . $listing['image' . $i];
            if (file_exists($image_path)) {
                @unlink($image_path);
            }
        }
    }

    // Dil ayarını al
    $current_lang = $_SESSION['lang'] ?? 'tr';

    echo json_encode([
        'success' => true,
        'message' => $current_lang == 'tr' ? 'İlan başarıyla silindi' : 'Listing deleted successfully'
    ]);

} catch (PDOException $e) {
    // Dil ayarını al
    $current_lang = $_SESSION['lang'] ?? 'tr';

    // http_response_code(500); // Prevent HTML error page interception
    echo json_encode([
        'success' => false,
        'message' => $current_lang == 'tr' ? 'Silme işlemi sırasında hata oluştu' : 'Error occurred while deleting'
    ]);
}
?>