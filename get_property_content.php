<?php
/**
 * AJAX endpoint to get property content in different languages
 */
require_once 'config.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? $_POST['id'] ?? 0;
$lang = $_GET['lang'] ?? $_POST['lang'] ?? 'tr';

if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

if (!in_array($lang, ['tr', 'en'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid language']);
    exit;
}

try {
    // Önce dil dosyasını yükle
    $_SESSION['lang'] = $lang;
    require_once 'lang.php';
    
    $stmt = $pdo->prepare("SELECT * FROM listings WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $listing = $stmt->fetch();
    
    if (!$listing) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Listing not found']);
        exit;
    }
    
    $response = [
        'success' => true,
        'content' => [
            'title' => htmlspecialchars($listing['title_' . $lang] ?? $listing['title_tr'] ?? ''),
            'property_type' => t($listing['property_type'] ?? ''),
            'description' => htmlspecialchars($listing['description_' . $lang] ?? $listing['description_tr'] ?? ''),
            'notes' => $listing['notes'] ? htmlspecialchars($listing['notes']) : null,
            'price_label' => $lang == 'tr' ? 'Fiyat' : 'Price',
            'basic_info' => $lang == 'tr' ? 'Temel Bilgiler' : 'Basic Information',
            'description_label' => t('description'),
            'notes_label' => $lang == 'tr' ? 'Notlar ve Ek Bilgiler' : 'Notes and Additional Information',
            'offer_label' => t('offer'),
            'submit_label' => t('submit'),
            'your_name_label' => t('your_name'),
            'your_email_label' => t('your_email'),
            'your_phone_label' => t('your_phone'),
            'offer_amount_label' => t('offer_amount'),
            'message_label' => t('message'),
            'area_label' => t('area'),
            'rooms_label' => t('rooms'),
            'bathrooms_label' => t('bathrooms'),
            'location_label' => t('location'),
            'address_label' => t('address'),
            'back_label' => t('back'),
            // Detay etiketleri
            'land_details' => $lang == 'tr' ? 'Arsa Detayları' : 'Land Details',
            'residential_details' => $lang == 'tr' ? 'Konut Detayları' : 'Residential Details',
            'commercial_details' => $lang == 'tr' ? 'İşyeri Detayları' : 'Commercial Property Details',
            'infrastructure' => $lang == 'tr' ? 'Altyapı Özellikleri' : 'Infrastructure Features',
            'building_features' => $lang == 'tr' ? 'Bina Özellikleri' : 'Building Features',
            'location_features' => $lang == 'tr' ? 'Konum Özellikleri' : 'Location Features',
            'comments_label' => $lang == 'tr' ? 'Yorumlar' : 'Comments',
            'add_comment_label' => $lang == 'tr' ? 'Yorum Yap' : 'Add Comment',
            'submit_comment_label' => $lang == 'tr' ? 'Gönder' : 'Submit',
            'login_prompt_label' => $lang == 'tr' ? 'Yorum yapmak için lütfen giriş yapın.' : 'Please log in to leave a comment.',
        ]
    ];
    
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>

