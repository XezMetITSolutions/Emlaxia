<?php
/**
 * AJAX endpoint to get index page content in different languages
 */
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config.php';

header('Content-Type: application/json');

try {
    $lang = $_GET['lang'] ?? $_POST['lang'] ?? 'tr';

    if (!in_array($lang, ['tr', 'en'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid language']);
        exit;
    }

    // Dil dosyasını yükle
    $_SESSION['lang'] = $lang;
    require_once 'lang.php';

    // Anasayfadaki ilanları çek (rastgele 3 tane)
    // Fetch all needed fields for a listing card
    $sql = "SELECT id, title_tr, title_en, property_type, slug, image1, listing_type, city, price, area, rooms, bathrooms, address, district, mahalle 
            FROM listings 
            WHERE status = 'active' AND approval_status = 'approved' 
            ORDER BY RAND() LIMIT 3";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // İlan verilerini formatla
    $listings_data = [];
    foreach ($listings as $listing) {
        // Property type'ı dil dosyasından çevir
        $property_type_value = $listing['property_type'] ?? '';
        $property_type_translated = t($property_type_value);
        
        $listings_data[] = [
            'id' => (int)$listing['id'],
            'title' => htmlspecialchars($listing['title_' . $lang] ?? $listing['title_tr'] ?? '', ENT_QUOTES, 'UTF-8'),
            'property_type' => htmlspecialchars($property_type_translated, ENT_QUOTES, 'UTF-8')
        ];
    }

    $response = [
        'success' => true,
        'content' => [
            'search_title' => $lang == 'tr' ? 'Arama' : 'Search',
            'property_type_label' => t('property_type'),
            'all_types' => $lang == 'tr' ? 'Tüm Türler' : 'All Types',
            'house' => t('house'),
            'apartment' => t('apartment'),
            'land' => t('land'),
            'shop' => t('shop'),
            'villa' => t('villa'),
            'location_label' => $lang == 'tr' ? 'Konum' : 'Location',
            'all_locations' => $lang == 'tr' ? 'Tüm Konumlar' : 'All Locations',
            'min_price_label' => $lang == 'tr' ? 'Minimum Fiyat' : 'Minimum Price',
            'max_price_label' => $lang == 'tr' ? 'Maksimum Fiyat' : 'Maximum Price',
            'min_area_label' => $lang == 'tr' ? 'Minimum m²' : 'Minimum m²',
            'max_area_label' => $lang == 'tr' ? 'Maksimum m²' : 'Maximum m²',
            'listing_no_label' => $lang == 'tr' ? 'İlan No' : 'Listing No',
            'search_button' => $lang == 'tr' ? 'Ara' : 'Search',
            'clear_button' => $lang == 'tr' ? 'Temizle' : 'Clear',
            'enter_placeholder' => $lang == 'tr' ? 'Giriniz' : 'Enter',
            'recent_listings' => $lang == 'tr' ? 'Güncel İlanlar' : 'Recent Listings',
            'view_all_listings' => $lang == 'tr' ? 'Tüm İlanları Gör' : 'View All Listings',
            'more_details' => $lang == 'tr' ? 'Daha Fazla Detay ▸' : 'More Details ▸',
            'no_listings' => t('no_listings'),
            'property_request_title' => $lang == 'tr' ? 'Hayalinizi Gerçeğe Dönüştürmek İster misiniz?' : 'Want to Turn Your Dreams Into Reality?',
            'property_request_subtitle' => $lang == 'tr' ? 'Aradığınız özelliklerdeki mülkü bizimle paylaşın, sizin için bulalım!' : 'Share the property features you\'re looking for, and we\'ll find it for you!',
            'your_name_label' => $lang == 'tr' ? 'Adınız Soyadınız' : 'Your Name',
            'your_email_label' => $lang == 'tr' ? 'E-posta Adresiniz' : 'Your Email',
            'your_phone_label' => $lang == 'tr' ? 'Telefon Numaranız' : 'Your Phone',
            'select_placeholder' => $lang == 'tr' ? 'Seçiniz' : 'Select',
            'city_placeholder' => $lang == 'tr' ? 'İstanbul, Ankara, İzmir...' : 'Istanbul, Ankara, Izmir...',
            'district_placeholder' => $lang == 'tr' ? 'İlçe' : 'District',
            'min_price_placeholder' => 'TL',
            'max_price_placeholder' => 'TL',
            'min_area_placeholder' => 'm²',
            'max_area_placeholder' => 'm²',
            'rooms_placeholder' => $lang == 'tr' ? '3+1, 4+1 vb.' : '3+1, 4+1 etc.',
            'bathrooms_placeholder' => $lang == 'tr' ? '2, 3 vb.' : '2, 3 etc.',
            'features_placeholder' => $lang == 'tr' ? 'Örn: Balkon, asansör, otopark, güvenlik, site içinde, deniz manzaralı...' : 'E.g.: Balcony, elevator, parking, security, gated community, sea view...',
            'additional_info_placeholder' => $lang == 'tr' ? 'Aradığınız mülkle ilgili ek bilgiler, özel istekleriniz...' : 'Additional information about the property you\'re looking for, special requests...',
            'submit_request' => $lang == 'tr' ? 'İsteğimi Gönder' : 'Submit My Request',
            'form_note' => $lang == 'tr' ? '* İşaretli alanlar zorunludur. İsteğinizi aldıktan sonra en kısa sürede size dönüş yapacağız.' : '* Required fields. We will get back to you as soon as possible after receiving your request.',
            'rooms_label' => t('rooms'),
            'bathrooms_label' => t('bathrooms'),
            'features_label' => $lang == 'tr' ? 'İstediğiniz Özellikler' : 'Desired Features',
            'additional_info_label' => $lang == 'tr' ? 'Ek Bilgiler veya Notlar' : 'Additional Information or Notes',
            'home' => t('home'),
            'listings_translation' => t('listings'),
            'contact' => t('contact'),
            'bedrooms_label' => $lang == 'tr' ? 'Yatak Odası' : 'Bedrooms',
            'listings' => $listings_data
        ]
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
