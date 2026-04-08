<?php
/**
 * AJAX endpoint for saving listings (both create and update)
 */
require_once '../config.php';

// Admin kontrolü
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    // http_response_code(401); // Prevent HTML error page interception
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Please login again', 'redirect' => 'login.php']);
    exit;
}

// Auto-migration check for 'tarla' property type
try {
    $check_stmt = $pdo->query("SHOW COLUMNS FROM listings LIKE 'property_type'");
    $col_info = $check_stmt->fetch();
    if ($col_info && strpos($col_info['Type'], "'tarla'") === false) {
        // Add 'tarla' to property_type enum
        $pdo->exec("ALTER TABLE `listings` MODIFY COLUMN `property_type` ENUM('ev','daire','arsa','dukkan','villa','tarla') NOT NULL");
        // Add 'field' to property_type_en enum
        $pdo->exec("ALTER TABLE `listings` MODIFY COLUMN `property_type_en` ENUM('house','apartment','land','shop','villa','field') NOT NULL");
    }
} catch (Exception $e) {
    // Log error but continue, typical issue might be permissions but worth trying
    error_log("Auto-migration failed: " . $e->getMessage());
}

// JSON response header
header('Content-Type: application/json');

// Sadece POST request kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// ID kontrolü (edit mode için)
$id = $_POST['id'] ?? null;
$is_edit = !empty($id);

// Eğer edit modundaysa mevcut ilanı kontrol et
if ($is_edit) {
    $stmt = $pdo->prepare("SELECT id FROM listings WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $listing = $stmt->fetch();

    if (!$listing) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Listing not found']);
        exit;
    }
}

// Form verilerini al
$title_tr = $_POST['title_tr'] ?? '';
$title_en = $_POST['title_en'] ?? '';
$description_tr = $_POST['description_tr'] ?? '';
$description_en = $_POST['description_en'] ?? '';
$property_type = $_POST['property_type'] ?? '';
$listing_type = $_POST['listing_type'] ?? 'satilik';
$price = $_POST['price'] ?? 0;
// Remove thousands separators (dots) and convert decimal comma to dot
$price = str_replace('.', '', $price);
$price = str_replace(',', '.', $price);
$area = $_POST['area'] ?? null;
$rooms = $_POST['rooms'] ?? null;
$bathrooms = $_POST['bathrooms'] ?? null;
$address = $_POST['address'] ?? '';
$city = $_POST['city'] ?? '';
$district = $_POST['district'] ?? '';
$mahalle = $_POST['mahalle'] ?? '';
$status = $_POST['status'] ?? 'active';

// Arsa özel alanlar
$price_per_sqm = $_POST['price_per_sqm'] ?? null;
if ($price_per_sqm) {
    $price_per_sqm = str_replace('.', '', $price_per_sqm);
    $price_per_sqm = str_replace(',', '.', $price_per_sqm);
}
$imar_durumu = $_POST['imar_durumu'] ?? null;
if ($property_type === 'tarla') {
    $imar_durumu = 'Yok';
}
$tapu_durumu = $_POST['tapu_durumu'] ?? null;
$krediye_uygun = $_POST['krediye_uygun'] ?? null;
$takas = $_POST['takas'] ?? null;
$kat_karsiligi = $_POST['kat_karsiligi'] ?? null;
$ada_no = $_POST['ada_no'] ?? '';
$parsel_no = $_POST['parsel_no'] ?? '';
$cadde_uzerinde = $_POST['cadde_uzerinde'] ?? null;
$toplu_ulasim = $_POST['toplu_ulasim'] ?? null;
$merkeze_yakin = $_POST['merkeze_yakin'] ?? null;
$elektrik = $_POST['elektrik'] ?? null;
$su = $_POST['su'] ?? null;
$kanalizasyon = $_POST['kanalizasyon'] ?? null;
$dogalgaz = $_POST['dogalgaz'] ?? null;
$telefon = $_POST['telefon'] ?? null;
$yolu_acilmis = $_POST['yolu_acilmis'] ?? null;
$parselli = $_POST['parselli'] ?? null;
$ifrazli = $_POST['ifrazli'] ?? null;
$doga_manzara = $_POST['doga_manzara'] ?? null;
$manzara_tipi = $_POST['manzara_tipi'] ?? '';
$fiyat_turu = $_POST['fiyat_turu'] ?? null;
$notes = $_POST['notes'] ?? '';
$map_address = $_POST['map_address'] ?? '';

// SEO Fields
$slug = $_POST['slug'] ?? '';
$meta_title = $_POST['meta_title'] ?? '';
$meta_description = $_POST['meta_description'] ?? '';

// Generate slug if empty
if (empty($slug)) {
    $slug = createSlug($title_tr);
} else {
    $slug = createSlug($slug);
}

// Ensure unique slug
$originalSlug = $slug;
$counter = 1;
while (true) {
    if ($is_edit) {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM listings WHERE slug = :slug AND id != :id");
        $checkStmt->execute([':slug' => $slug, ':id' => $id]);
    } else {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM listings WHERE slug = :slug");
        $checkStmt->execute([':slug' => $slug]);
    }

    if ($checkStmt->fetchColumn() == 0) {
        break;
    }
    $slug = $originalSlug . '-' . $counter;
    $counter++;
}

function createSlug($str)
{
    $str = mb_strtolower($str, 'UTF-8');
    $str = str_replace(
        ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'],
        ['i', 'g', 'u', 's', 'o', 'c'],
        $str
    );
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    $str = trim($str, '-');
    return $str ?: 'property';
}

// Konut (Ev, Daire, Villa) özel alanlar
$brut_metrekare = $_POST['brut_metrekare'] ?? null;
$net_metrekare = $_POST['net_metrekare'] ?? null;
$oda_sayisi = $_POST['oda_sayisi'] ?? '';
$bina_yasi = $_POST['bina_yasi'] ?? null;
$bulundugu_kat = $_POST['bulundugu_kat'] ?? '';
$kat_sayisi = $_POST['kat_sayisi'] ?? null;
$isitma_turu = $_POST['isitma_turu'] ?? null;
$balkon = $_POST['balkon'] ?? null;
$esyali = $_POST['esyali'] ?? null;
$kullanim_durumu = $_POST['kullanim_durumu'] ?? null;
$tapu_durumu_konut = $_POST['tapu_durumu_konut'] ?? null;
$asansor = $_POST['asansor'] ?? null;
$otopark = $_POST['otopark'] ?? null;
$guvenlik = $_POST['guvenlik'] ?? null;
$site_icinde = $_POST['site_icinde'] ?? null;
$kamera_sistemi = $_POST['kamera_sistemi'] ?? null;
$kapici = $_POST['kapici'] ?? null;
$isi_yalitim = $_POST['isi_yalitim'] ?? null;
$okula_yakin = $_POST['okula_yakin'] ?? null;
$hastaneye_yakin = $_POST['hastaneye_yakin'] ?? null;
$market_yakin = $_POST['market_yakin'] ?? null;
$merkeze_uzaklik = $_POST['merkeze_uzaklik'] ?? '';
$cephe = $_POST['cephe'] ?? null;

// Ticari İşyeri (Dükkan) özel alanlar
$toplam_alan = $_POST['toplam_alan'] ?? null;
$kullanim_alani = $_POST['kullanim_alani'] ?? null;
$zemin_turu = $_POST['zemin_turu'] ?? null;
$on_cephe_uzunlugu = $_POST['on_cephe_uzunlugu'] ?? null;
$giris_yuksekligi = $_POST['giris_yuksekligi'] ?? null;
$wc_lavabo = $_POST['wc_lavabo'] ?? null;
$mutfak_ticari = $_POST['mutfak_ticari'] ?? null;
$vitrin_cami = $_POST['vitrin_cami'] ?? null;
$tabela_gorunurluk = $_POST['tabela_gorunurluk'] ?? null;
$yaya_trafik = $_POST['yaya_trafik'] ?? null;
$kullanim_durumu_ticari = $_POST['kullanim_durumu_ticari'] ?? null;
$yuk_asansor = $_POST['yuk_asansor'] ?? null;
$depo_alani = $_POST['depo_alani'] ?? null;
$yalitim = $_POST['yalitim'] ?? null;
$ana_yola_cephe = $_POST['ana_yola_cephe'] ?? null;

// İlan Sahibi ve Komisyon
$ilan_sahibi_turu = $_POST['ilan_sahibi_turu'] ?? null;
$komisyon_yuzdesi = $_POST['komisyon_yuzdesi'] ?? null;
// Eğer emlakçı değilse komisyon yüzdesini null yap
if ($ilan_sahibi_turu !== 'Emlakçı') {
    $komisyon_yuzdesi = null;
}

// Site Özellikleri
$site_guvenlik = isset($_POST['site_guvenlik']) ? 1 : 0;
$site_spor_salonu = isset($_POST['site_spor_salonu']) ? 1 : 0;
$site_yuzme_havuzu = isset($_POST['site_yuzme_havuzu']) ? 1 : 0;
$site_cocuk_parki = isset($_POST['site_cocuk_parki']) ? 1 : 0;
$site_sauna = isset($_POST['site_sauna']) ? 1 : 0;
$site_turk_hamami = isset($_POST['site_turk_hamami']) ? 1 : 0;
$site_jenerator = isset($_POST['site_jenerator']) ? 1 : 0;
$site_kapali_otopark = isset($_POST['site_kapali_otopark']) ? 1 : 0;
$site_acik_otopark = isset($_POST['site_acik_otopark']) ? 1 : 0;
$site_tenis_kortu = isset($_POST['site_tenis_kortu']) ? 1 : 0;
$site_basketbol_sahasi = isset($_POST['site_basketbol_sahasi']) ? 1 : 0;
$site_market = isset($_POST['site_market']) ? 1 : 0;
$site_kres = isset($_POST['site_kres']) ? 1 : 0;
$site_cafe = isset($_POST['site_cafe']) ? 1 : 0;
$site_restorant = isset($_POST['site_restorant']) ? 1 : 0;
$site_kuafor = isset($_POST['site_kuafor']) ? 1 : 0;
$site_toplanti_odasi = isset($_POST['site_toplanti_odasi']) ? 1 : 0;
$site_bahce = isset($_POST['site_bahce']) ? 1 : 0;
$site_evcil_hayvan = isset($_POST['site_evcil_hayvan']) ? 1 : 0;
$site_engelli_erisimi = isset($_POST['site_engelli_erisimi']) ? 1 : 0;

// Property type EN eşlemesi
$property_types_en = [
    'ev' => 'house',
    'daire' => 'apartment',
    'arsa' => 'land',
    'tarla' => 'field',
    'dukkan' => 'shop',
    'villa' => 'villa'
];
$property_type_en = $property_types_en[$property_type] ?? 'house';

// Listing type validation
if (!in_array($listing_type, ['satilik', 'kiralik'])) {
    $listing_type = 'satilik';
}

// Resim yükleme - Yeni Çoklu Sistem
$images = [];
$main_image_index = isset($_POST['main_image_index']) ? (int) $_POST['main_image_index'] : 0;

// Yeni yüklenen resimler
$uploaded_images = $_FILES['uploaded_images'] ?? null;
$image_ids = $_POST['image_ids'] ?? [];

// Mevcut resimlerin listesi (edit modunda korunacak olanlar)
$existing_images_list = [];
if (!empty($_POST['existing_images_list'])) {
    $existing_images_list = json_decode($_POST['existing_images_list'], true) ?? [];
}

// Tüm resimleri birleştir
$all_images = [];

// Önce mevcut resimleri ekle
foreach ($existing_images_list as $existing_image) {
    $all_images[] = $existing_image;
}

// Sonra yeni yüklenen resimleri işle
if ($uploaded_images && !empty($uploaded_images['name'])) {
    $upload_count = count($uploaded_images['name']);

    for ($i = 0; $i < $upload_count; $i++) {
        if (!empty($uploaded_images['name'][$i]) && $uploaded_images['error'][$i] === UPLOAD_ERR_OK) {
            $file_extension = strtolower(pathinfo($uploaded_images['name'][$i], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($file_extension, $allowed_extensions)) {
                $filename = time() . '_' . uniqid() . '_' . basename($uploaded_images['name'][$i]);
                $target = '../uploads/' . $filename;

                if (move_uploaded_file($uploaded_images['tmp_name'][$i], $target)) {
                    $all_images[] = $filename;
                }
            }
        }
    }
}

// Ana resmi ilk sıraya al
if ($main_image_index > 0 && isset($all_images[$main_image_index])) {
    $main_image = $all_images[$main_image_index];
    array_splice($all_images, $main_image_index, 1);
    array_unshift($all_images, $main_image);
}

// İlk 20 resmi image1-20 sütunlarına ata
for ($i = 0; $i < 20; $i++) {
    if (isset($all_images[$i])) {
        $images['image' . ($i + 1)] = $all_images[$i];
    }
}

// Eski sistem için fallback - image1-20 doğrudan yüklendiyse
for ($i = 1; $i <= 20; $i++) {
    if (!isset($images['image' . $i]) && !empty($_FILES['image' . $i]['name'])) {
        $filename = time() . '_' . basename($_FILES['image' . $i]['name']);
        $target = '../uploads/' . $filename;
        if (move_uploaded_file($_FILES['image' . $i]['tmp_name'], $target)) {
            $images['image' . $i] = $filename;
        }
    } elseif (!isset($images['image' . $i]) && $is_edit && isset($listing)) {
        // Mevcut resmi koru (eğer yeni yükleme yapılmadıysa)
        $stmt = $pdo->prepare("SELECT image$i FROM listings WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $current_image = $stmt->fetchColumn();
        if ($current_image && in_array($current_image, $existing_images_list)) {
            $images['image' . $i] = $current_image;
        }
    }
}

// Video yükleme
$video = null;
if (!empty($_FILES['video']['name'])) {
    $allowed_extensions = ['mp4', 'webm', 'ogg'];
    $file_extension = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));

    if (in_array($file_extension, $allowed_extensions)) {
        // Check file size (50MB max)
        if ($_FILES['video']['size'] <= 50 * 1024 * 1024) {
            $filename = time() . '_video_' . basename($_FILES['video']['name']);
            $target = '../uploads/' . $filename;
            if (move_uploaded_file($_FILES['video']['tmp_name'], $target)) {
                $video = $filename;
            }
        }
    }
} elseif ($is_edit) {
    // Keep existing video if not deleting
    if (isset($_POST['delete_video']) && $_POST['delete_video'] == '1') {
        // Delete the video file
        $stmt = $pdo->prepare("SELECT video FROM listings WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $existing_video = $stmt->fetchColumn();
        if ($existing_video && file_exists('../uploads/' . $existing_video)) {
            unlink('../uploads/' . $existing_video);
        }
        $video = null;
    } else {
        // Keep existing video
        $stmt = $pdo->prepare("SELECT video FROM listings WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $video = $stmt->fetchColumn();
    }
}

try {
    $current_lang = $_SESSION['lang'] ?? 'tr';

    if ($is_edit) {
        // UPDATE
        $sql = "UPDATE listings SET title_tr = :title_tr, title_en = :title_en, description_tr = :description_tr, 
                description_en = :description_en, property_type = :property_type, property_type_en = :property_type_en,
                listing_type = :listing_type, price = :price, area = :area, rooms = :rooms, bathrooms = :bathrooms, address = :address, 
                city = :city, district = :district, mahalle = :mahalle, status = :status,
                price_per_sqm = :price_per_sqm, imar_durumu = :imar_durumu, tapu_durumu = :tapu_durumu,
                krediye_uygun = :krediye_uygun, takas = :takas, kat_karsiligi = :kat_karsiligi,
                ada_no = :ada_no, parsel_no = :parsel_no, cadde_uzerinde = :cadde_uzerinde,
                toplu_ulasim = :toplu_ulasim, merkeze_yakin = :merkeze_yakin, elektrik = :elektrik,
                su = :su, kanalizasyon = :kanalizasyon, dogalgaz = :dogalgaz, telefon = :telefon,
                yolu_acilmis = :yolu_acilmis, parselli = :parselli, ifrazli = :ifrazli,
                doga_manzara = :doga_manzara, manzara_tipi = :manzara_tipi, fiyat_turu = :fiyat_turu,
                notes = :notes,
                slug = :slug, map_address = :map_address, meta_title = :meta_title, meta_description = :meta_description,
                brut_metrekare = :brut_metrekare, net_metrekare = :net_metrekare, oda_sayisi = :oda_sayisi,
                bina_yasi = :bina_yasi, bulundugu_kat = :bulundugu_kat, kat_sayisi = :kat_sayisi,
                isitma_turu = :isitma_turu, balkon = :balkon, esyali = :esyali,
                kullanim_durumu = :kullanim_durumu, tapu_durumu_konut = :tapu_durumu_konut,
                asansor = :asansor, otopark = :otopark, guvenlik = :guvenlik, site_icinde = :site_icinde,
                kamera_sistemi = :kamera_sistemi, kapici = :kapici, isi_yalitim = :isi_yalitim,
                okula_yakin = :okula_yakin, hastaneye_yakin = :hastaneye_yakin, market_yakin = :market_yakin,
                merkeze_uzaklik = :merkeze_uzaklik, cephe = :cephe,
                toplam_alan = :toplam_alan, kullanim_alani = :kullanim_alani, zemin_turu = :zemin_turu,
                on_cephe_uzunlugu = :on_cephe_uzunlugu, giris_yuksekligi = :giris_yuksekligi,
                wc_lavabo = :wc_lavabo, mutfak_ticari = :mutfak_ticari, vitrin_cami = :vitrin_cami,
                tabela_gorunurluk = :tabela_gorunurluk, yaya_trafik = :yaya_trafik,
                kullanim_durumu_ticari = :kullanim_durumu_ticari, yuk_asansor = :yuk_asansor,
                depo_alani = :depo_alani, yalitim = :yalitim, ana_yola_cephe = :ana_yola_cephe, video = :video,
                ilan_sahibi_turu = :ilan_sahibi_turu, komisyon_yuzdesi = :komisyon_yuzdesi,
                site_guvenlik = :site_guvenlik, site_spor_salonu = :site_spor_salonu, site_yuzme_havuzu = :site_yuzme_havuzu,
                site_cocuk_parki = :site_cocuk_parki, site_sauna = :site_sauna, site_turk_hamami = :site_turk_hamami,
                site_jenerator = :site_jenerator, site_kapali_otopark = :site_kapali_otopark, site_acik_otopark = :site_acik_otopark,
                site_tenis_kortu = :site_tenis_kortu, site_basketbol_sahasi = :site_basketbol_sahasi, site_market = :site_market,
                site_kres = :site_kres, site_cafe = :site_cafe, site_restorant = :site_restorant, site_kuafor = :site_kuafor,
                site_toplanti_odasi = :site_toplanti_odasi, site_bahce = :site_bahce, site_evcil_hayvan = :site_evcil_hayvan,
                site_engelli_erisimi = :site_engelli_erisimi";

        for ($i = 1; $i <= 20; $i++) {
            if (isset($images['image' . $i])) {
                $sql .= ", image$i = :image$i";
            }
        }

        $sql .= " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $params = [
            ':title_tr' => $title_tr,
            ':title_en' => $title_en,
            ':description_tr' => $description_tr,
            ':description_en' => $description_en,
            ':property_type' => $property_type,
            ':property_type_en' => $property_type_en,
            ':listing_type' => $listing_type,
            ':price' => $price,
            ':area' => $area ?: null,
            ':rooms' => $rooms ?: null,
            ':bathrooms' => $bathrooms ?: null,
            ':address' => $address,
            ':city' => $city,
            ':district' => $district,
            ':mahalle' => $mahalle,
            ':status' => $status,
            ':price_per_sqm' => $price_per_sqm ?: null,
            ':imar_durumu' => $imar_durumu ?: null,
            ':tapu_durumu' => $tapu_durumu ?: null,
            ':krediye_uygun' => $krediye_uygun ?: null,
            ':takas' => $takas ?: null,
            ':kat_karsiligi' => $kat_karsiligi ?: null,
            ':ada_no' => $ada_no,
            ':parsel_no' => $parsel_no,
            ':cadde_uzerinde' => $cadde_uzerinde ?: null,
            ':toplu_ulasim' => $toplu_ulasim ?: null,
            ':merkeze_yakin' => $merkeze_yakin ?: null,
            ':elektrik' => $elektrik ?: null,
            ':su' => $su ?: null,
            ':kanalizasyon' => $kanalizasyon ?: null,
            ':dogalgaz' => $dogalgaz ?: null,
            ':telefon' => $telefon ?: null,
            ':yolu_acilmis' => $yolu_acilmis ?: null,
            ':parselli' => $parselli ?: null,
            ':ifrazli' => $ifrazli ?: null,
            ':doga_manzara' => $doga_manzara ?: null,
            ':manzara_tipi' => $manzara_tipi,
            ':fiyat_turu' => $fiyat_turu ?: null,
            ':notes' => $notes,
            ':slug' => $slug,
            ':map_address' => $map_address,
            ':meta_title' => $meta_title,
            ':meta_description' => $meta_description,
            ':brut_metrekare' => $brut_metrekare ?: null,
            ':net_metrekare' => $net_metrekare ?: null,
            ':oda_sayisi' => $oda_sayisi,
            ':bina_yasi' => $bina_yasi ?: null,
            ':bulundugu_kat' => $bulundugu_kat,
            ':kat_sayisi' => $kat_sayisi ?: null,
            ':isitma_turu' => $isitma_turu ?: null,
            ':balkon' => $balkon ?: null,
            ':esyali' => $esyali ?: null,
            ':kullanim_durumu' => $kullanim_durumu ?: null,
            ':tapu_durumu_konut' => $tapu_durumu_konut ?: null,
            ':asansor' => $asansor ?: null,
            ':otopark' => $otopark ?: null,
            ':guvenlik' => $guvenlik ?: null,
            ':site_icinde' => $site_icinde ?: null,
            ':kamera_sistemi' => $kamera_sistemi ?: null,
            ':kapici' => $kapici ?: null,
            ':isi_yalitim' => $isi_yalitim ?: null,
            ':okula_yakin' => $okula_yakin ?: null,
            ':hastaneye_yakin' => $hastaneye_yakin ?: null,
            ':market_yakin' => $market_yakin ?: null,
            ':merkeze_uzaklik' => $merkeze_uzaklik,
            ':cephe' => $cephe ?: null,
            ':toplam_alan' => $toplam_alan ?: null,
            ':kullanim_alani' => $kullanim_alani ?: null,
            ':zemin_turu' => $zemin_turu ?: null,
            ':on_cephe_uzunlugu' => $on_cephe_uzunlugu ?: null,
            ':giris_yuksekligi' => $giris_yuksekligi ?: null,
            ':wc_lavabo' => $wc_lavabo ?: null,
            ':mutfak_ticari' => $mutfak_ticari ?: null,
            ':vitrin_cami' => $vitrin_cami ?: null,
            ':tabela_gorunurluk' => $tabela_gorunurluk ?: null,
            ':yaya_trafik' => $yaya_trafik ?: null,
            ':kullanim_durumu_ticari' => $kullanim_durumu_ticari ?: null,
            ':yuk_asansor' => $yuk_asansor ?: null,
            ':depo_alani' => $depo_alani ?: null,
            ':yalitim' => $yalitim ?: null,
            ':ana_yola_cephe' => $ana_yola_cephe ?: null,
            ':video' => $video,
            ':ilan_sahibi_turu' => $ilan_sahibi_turu ?: null,
            ':komisyon_yuzdesi' => $komisyon_yuzdesi ?: null,
            ':site_guvenlik' => $site_guvenlik,
            ':site_spor_salonu' => $site_spor_salonu,
            ':site_yuzme_havuzu' => $site_yuzme_havuzu,
            ':site_cocuk_parki' => $site_cocuk_parki,
            ':site_sauna' => $site_sauna,
            ':site_turk_hamami' => $site_turk_hamami,
            ':site_jenerator' => $site_jenerator,
            ':site_kapali_otopark' => $site_kapali_otopark,
            ':site_acik_otopark' => $site_acik_otopark,
            ':site_tenis_kortu' => $site_tenis_kortu,
            ':site_basketbol_sahasi' => $site_basketbol_sahasi,
            ':site_market' => $site_market,
            ':site_kres' => $site_kres,
            ':site_cafe' => $site_cafe,
            ':site_restorant' => $site_restorant,
            ':site_kuafor' => $site_kuafor,
            ':site_toplanti_odasi' => $site_toplanti_odasi,
            ':site_bahce' => $site_bahce,
            ':site_evcil_hayvan' => $site_evcil_hayvan,
            ':site_engelli_erisimi' => $site_engelli_erisimi,
            ':id' => $id
        ];

        for ($i = 1; $i <= 20; $i++) {
            if (isset($images['image' . $i])) {
                $params[":image$i"] = $images['image' . $i];
            }
        }

        $stmt->execute($params);
        $message = $current_lang == 'tr' ? 'İlan başarıyla güncellendi!' : 'Listing updated successfully!';

    } else {
        // INSERT
        $sql = "INSERT INTO listings (title_tr, title_en, description_tr, description_en, property_type, property_type_en,
                listing_type, price, area, rooms, bathrooms, address, city, district, mahalle, status, created_by,
                price_per_sqm, imar_durumu, tapu_durumu, krediye_uygun, takas, kat_karsiligi,
                ada_no, parsel_no, cadde_uzerinde, toplu_ulasim, merkeze_yakin, elektrik,
                su, kanalizasyon, dogalgaz, telefon, yolu_acilmis, parselli, ifrazli,
                doga_manzara, manzara_tipi, fiyat_turu, notes, map_address, slug, meta_title, meta_description,
                brut_metrekare, net_metrekare, oda_sayisi, bina_yasi, bulundugu_kat, kat_sayisi,
                isitma_turu, balkon, esyali, kullanim_durumu, tapu_durumu_konut,
                asansor, otopark, guvenlik, site_icinde, kamera_sistemi, kapici, isi_yalitim,
                okula_yakin, hastaneye_yakin, market_yakin, merkeze_uzaklik, cephe,
                toplam_alan, kullanim_alani, zemin_turu, on_cephe_uzunlugu, giris_yuksekligi,
                wc_lavabo, mutfak_ticari, vitrin_cami, tabela_gorunurluk, yaya_trafik,
                kullanim_durumu_ticari, yuk_asansor, depo_alani, yalitim, ana_yola_cephe, video,
                ilan_sahibi_turu, komisyon_yuzdesi,
                site_guvenlik, site_spor_salonu, site_yuzme_havuzu, site_cocuk_parki, site_sauna, site_turk_hamami,
                site_jenerator, site_kapali_otopark, site_acik_otopark, site_tenis_kortu, site_basketbol_sahasi, site_market,
                site_kres, site_cafe, site_restorant, site_kuafor, site_toplanti_odasi, site_bahce, site_evcil_hayvan, site_engelli_erisimi";

        if (!empty($images)) {
            foreach ($images as $key => $value) {
                $sql .= ", $key";
            }
        }

        $sql .= ") VALUES (:title_tr, :title_en, :description_tr, :description_en, :property_type, :property_type_en,
                :listing_type, :price, :area, :rooms, :bathrooms, :address, :city, :district, :mahalle, :status, :created_by,
                :price_per_sqm, :imar_durumu, :tapu_durumu, :krediye_uygun, :takas, :kat_karsiligi,
                :ada_no, :parsel_no, :cadde_uzerinde, :toplu_ulasim, :merkeze_yakin, :elektrik,
                :su, :kanalizasyon, :dogalgaz, :telefon, :yolu_acilmis, :parselli, :ifrazli,
                :doga_manzara, :manzara_tipi, :fiyat_turu, :notes, :map_address, :slug, :meta_title, :meta_description,
                :brut_metrekare, :net_metrekare, :oda_sayisi, :bina_yasi, :bulundugu_kat, :kat_sayisi,
                :isitma_turu, :balkon, :esyali, :kullanim_durumu, :tapu_durumu_konut,
                :asansor, :otopark, :guvenlik, :site_icinde, :kamera_sistemi, :kapici, :isi_yalitim,
                :okula_yakin, :hastaneye_yakin, :market_yakin, :merkeze_uzaklik, :cephe,
                :toplam_alan, :kullanim_alani, :zemin_turu, :on_cephe_uzunlugu, :giris_yuksekligi,
                :wc_lavabo, :mutfak_ticari, :vitrin_cami, :tabela_gorunurluk, :yaya_trafik,
                :kullanim_durumu_ticari, :yuk_asansor, :depo_alani, :yalitim, :ana_yola_cephe, :video,
                :ilan_sahibi_turu, :komisyon_yuzdesi,
                :site_guvenlik, :site_spor_salonu, :site_yuzme_havuzu, :site_cocuk_parki, :site_sauna, :site_turk_hamami,
                :site_jenerator, :site_kapali_otopark, :site_acik_otopark, :site_tenis_kortu, :site_basketbol_sahasi, :site_market,
                :site_kres, :site_cafe, :site_restorant, :site_kuafor, :site_toplanti_odasi, :site_bahce, :site_evcil_hayvan, :site_engelli_erisimi";

        if (!empty($images)) {
            foreach ($images as $key => $value) {
                $sql .= ", :$key";
            }
        }

        $sql .= ")";

        $stmt = $pdo->prepare($sql);
        $params = [
            ':title_tr' => $title_tr,
            ':title_en' => $title_en,
            ':description_tr' => $description_tr,
            ':description_en' => $description_en,
            ':property_type' => $property_type,
            ':property_type_en' => $property_type_en,
            ':listing_type' => $listing_type,
            ':price' => $price,
            ':area' => $area ?: null,
            ':rooms' => $rooms ?: null,
            ':bathrooms' => $bathrooms ?: null,
            ':address' => $address,
            ':city' => $city,
            ':district' => $district,
            ':mahalle' => $mahalle,
            ':status' => $status,
            ':created_by' => $_SESSION['admin_id'],
            ':price_per_sqm' => $price_per_sqm ?: null,
            ':imar_durumu' => $imar_durumu ?: null,
            ':tapu_durumu' => $tapu_durumu ?: null,
            ':krediye_uygun' => $krediye_uygun ?: null,
            ':takas' => $takas ?: null,
            ':kat_karsiligi' => $kat_karsiligi ?: null,
            ':ada_no' => $ada_no,
            ':parsel_no' => $parsel_no,
            ':cadde_uzerinde' => $cadde_uzerinde ?: null,
            ':toplu_ulasim' => $toplu_ulasim ?: null,
            ':merkeze_yakin' => $merkeze_yakin ?: null,
            ':elektrik' => $elektrik ?: null,
            ':su' => $su ?: null,
            ':kanalizasyon' => $kanalizasyon ?: null,
            ':dogalgaz' => $dogalgaz ?: null,
            ':telefon' => $telefon ?: null,
            ':yolu_acilmis' => $yolu_acilmis ?: null,
            ':parselli' => $parselli ?: null,
            ':ifrazli' => $ifrazli ?: null,
            ':doga_manzara' => $doga_manzara ?: null,
            ':manzara_tipi' => $manzara_tipi,
            ':fiyat_turu' => $fiyat_turu ?: null,
            ':notes' => $notes,
            ':slug' => $slug,
            ':map_address' => $map_address,
            ':meta_title' => $meta_title,
            ':meta_description' => $meta_description,
            ':brut_metrekare' => $brut_metrekare ?: null,
            ':net_metrekare' => $net_metrekare ?: null,
            ':oda_sayisi' => $oda_sayisi,
            ':bina_yasi' => $bina_yasi ?: null,
            ':bulundugu_kat' => $bulundugu_kat,
            ':kat_sayisi' => $kat_sayisi ?: null,
            ':isitma_turu' => $isitma_turu ?: null,
            ':balkon' => $balkon ?: null,
            ':esyali' => $esyali ?: null,
            ':kullanim_durumu' => $kullanim_durumu ?: null,
            ':tapu_durumu_konut' => $tapu_durumu_konut ?: null,
            ':asansor' => $asansor ?: null,
            ':otopark' => $otopark ?: null,
            ':guvenlik' => $guvenlik ?: null,
            ':site_icinde' => $site_icinde ?: null,
            ':kamera_sistemi' => $kamera_sistemi ?: null,
            ':kapici' => $kapici ?: null,
            ':isi_yalitim' => $isi_yalitim ?: null,
            ':okula_yakin' => $okula_yakin ?: null,
            ':hastaneye_yakin' => $hastaneye_yakin ?: null,
            ':market_yakin' => $market_yakin ?: null,
            ':merkeze_uzaklik' => $merkeze_uzaklik,
            ':cephe' => $cephe ?: null,
            ':toplam_alan' => $toplam_alan ?: null,
            ':kullanim_alani' => $kullanim_alani ?: null,
            ':zemin_turu' => $zemin_turu ?: null,
            ':on_cephe_uzunlugu' => $on_cephe_uzunlugu ?: null,
            ':giris_yuksekligi' => $giris_yuksekligi ?: null,
            ':wc_lavabo' => $wc_lavabo ?: null,
            ':mutfak_ticari' => $mutfak_ticari ?: null,
            ':vitrin_cami' => $vitrin_cami ?: null,
            ':tabela_gorunurluk' => $tabela_gorunurluk ?: null,
            ':yaya_trafik' => $yaya_trafik ?: null,
            ':kullanim_durumu_ticari' => $kullanim_durumu_ticari ?: null,
            ':yuk_asansor' => $yuk_asansor ?: null,
            ':depo_alani' => $depo_alani ?: null,
            ':yalitim' => $yalitim ?: null,
            ':ana_yola_cephe' => $ana_yola_cephe ?: null,
            ':video' => $video,
            ':ilan_sahibi_turu' => $ilan_sahibi_turu ?: null,
            ':komisyon_yuzdesi' => $komisyon_yuzdesi ?: null,
            ':site_guvenlik' => $site_guvenlik,
            ':site_spor_salonu' => $site_spor_salonu,
            ':site_yuzme_havuzu' => $site_yuzme_havuzu,
            ':site_cocuk_parki' => $site_cocuk_parki,
            ':site_sauna' => $site_sauna,
            ':site_turk_hamami' => $site_turk_hamami,
            ':site_jenerator' => $site_jenerator,
            ':site_kapali_otopark' => $site_kapali_otopark,
            ':site_acik_otopark' => $site_acik_otopark,
            ':site_tenis_kortu' => $site_tenis_kortu,
            ':site_basketbol_sahasi' => $site_basketbol_sahasi,
            ':site_market' => $site_market,
            ':site_kres' => $site_kres,
            ':site_cafe' => $site_cafe,
            ':site_restorant' => $site_restorant,
            ':site_kuafor' => $site_kuafor,
            ':site_toplanti_odasi' => $site_toplanti_odasi,
            ':site_bahce' => $site_bahce,
            ':site_evcil_hayvan' => $site_evcil_hayvan,
            ':site_engelli_erisimi' => $site_engelli_erisimi
        ];

        if (!empty($images)) {
            foreach ($images as $key => $value) {
                $params[":$key"] = $value;
            }
        }

        $stmt->execute($params);
        $id = $pdo->lastInsertId();
        $message = $current_lang == 'tr' ? 'İlan başarıyla eklendi!' : 'Listing added successfully!';
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'id' => $id,
        'redirect' => 'ilanlar.php'
    ]);

} catch (PDOException $e) {
    $current_lang = $_SESSION['lang'] ?? 'tr';
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $current_lang == 'tr' ? 'Bir hata oluştu: ' . $e->getMessage() : 'An error occurred: ' . $e->getMessage()
    ]);
}
?>