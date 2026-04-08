<?php
/**
 * Emlakçı - İlan İşleme (AJAX endpoint)
 * Admin'in process_listing.php'sini temel alır ama emlakçıya özel kısıtlamalar içerir
 */
require_once '../config.php';
require_once '../includes/auth.php';

// Emlakçı kontrolü
if (!checkUserType('emlakci')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$user_id = $_SESSION['user_id'];
$id = $_POST['id'] ?? null;
$is_edit = !empty($id);

// Edit modunda sadece kendi ilanını düzenleyebilir
if ($is_edit) {
    $stmt = $pdo->prepare("SELECT id FROM listings WHERE id = :id AND user_id = :uid AND user_type = 'emlakci'");
    $stmt->execute([':id' => $id, ':uid' => $user_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Listing not found or access denied']);
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
$notes = $_POST['notes'] ?? '';

// Arsa alanları
$imar_durumu = $_POST['imar_durumu'] ?? null;
$tapu_durumu = $_POST['tapu_durumu'] ?? null;
$krediye_uygun = $_POST['krediye_uygun'] ?? null;
$ada_no = $_POST['ada_no'] ?? '';
$parsel_no = $_POST['parsel_no'] ?? '';

// Konut alanları
$brut_metrekare = $_POST['brut_metrekare'] ?? null;
$net_metrekare = $_POST['net_metrekare'] ?? null;
$oda_sayisi = $_POST['oda_sayisi'] ?? '';
$bina_yasi = $_POST['bina_yasi'] ?? null;
$bulundugu_kat = $_POST['bulundugu_kat'] ?? '';
$kat_sayisi = $_POST['kat_sayisi'] ?? null;
$isitma_turu = $_POST['isitma_turu'] ?? null;
$esyali = $_POST['esyali'] ?? null;
$balkon = $_POST['balkon'] ?? null;
$kullanim_durumu = $_POST['kullanim_durumu'] ?? null;
$tapu_durumu_konut = $_POST['tapu_durumu_konut'] ?? null;
$fiyat_turu = $_POST['fiyat_turu'] ?? null;
$price_per_sqm = str_replace(['.', ','], ['', '.'], $_POST['price_per_sqm'] ?? '0');

// Dükkan / Commercial fields
$toplam_alan = $_POST['toplam_alan'] ?? null;
$kullanim_alani = $_POST['kullanim_alani'] ?? null;
$zemin_turu = $_POST['zemin_turu'] ?? null;
$on_cephe_uzunlugu = $_POST['on_cephe_uzunlugu'] ?? null;
$giris_yuksekligi = $_POST['giris_yuksekligi'] ?? null;
$wc_lavabo = $_POST['wc_lavabo'] ?? null;
$vitrin_cami = $_POST['vitrin_cami'] ?? null;
$tabela_gorunurluk = $_POST['tabela_gorunurluk'] ?? null;
$yaya_trafik = $_POST['yaya_trafik'] ?? null;

// New fields
$elektrik = $_POST['elektrik'] ?? null;
$su = $_POST['su'] ?? null;
$kanalizasyon = $_POST['kanalizasyon'] ?? null;
$dogalgaz = $_POST['dogalgaz'] ?? null;
$telefon = $_POST['telefon'] ?? null;
$yolu_acilmis = $_POST['yolu_acilmis'] ?? null;
$parselli = $_POST['parselli'] ?? null;
$ifrazli = $_POST['ifrazli'] ?? null;
$cephe = $_POST['cephe'] ?? null;
$manzara_tipi = $_POST['manzara_tipi_konut'] ?: ($_POST['manzara_tipi_arsa'] ?: '');
$doga_manzara = $_POST['doga_manzara'] ?? null;

// Eklenen yeni alanlar (Commercial & Site Features)
$yuk_asansor = $_POST['yuk_asansor'] ?? null;
$depo_alani = $_POST['depo_alani'] ?? null;
$yalitim = $_POST['yalitim'] ?? null;
$site_icinde = $_POST['site_icinde'] ?? null;

// Bina özellikleri
$asansor = $_POST['asansor'] ?? null;
$otopark = $_POST['otopark'] ?? null;
$guvenlik = $_POST['guvenlik'] ?? null;
$kamera_sistemi = $_POST['kamera_sistemi'] ?? null;
$kapici = $_POST['kapici'] ?? null;
$isi_yalitim = $_POST['isi_yalitim'] ?? null;

// Konut ek alanlar
$takas = $_POST['takas'] ?? null;
$kat_karsiligi = $_POST['kat_karsiligi'] ?? null;

// Konum özellikleri
$cadde_uzerinde = $_POST['cadde_uzerinde'] ?? null;
$toplu_ulasim = $_POST['toplu_ulasim'] ?? null;
$merkeze_yakin = $_POST['merkeze_yakin'] ?? null;
$okula_yakin = $_POST['okula_yakin'] ?? null;
$hastaneye_yakin = $_POST['hastaneye_yakin'] ?? null;
$market_yakin = $_POST['market_yakin'] ?? null;
$merkeze_uzaklik = $_POST['merkeze_uzaklik'] ?? null;
$ana_yola_cephe = $_POST['ana_yola_cephe'] ?? null;

// Ticari alanlar
$mutfak_ticari = $_POST['mutfak_ticari'] ?? null;
$kullanim_durumu_ticari = $_POST['kullanim_durumu_ticari'] ?? null;

$site_features = [
    'site_guvenlik', 'site_spor_salonu', 'site_yuzme_havuzu', 'site_cocuk_parki',
    'site_sauna', 'site_turk_hamami', 'site_jenerator', 'site_kapali_otopark',
    'site_acik_otopark', 'site_tenis_kortu', 'site_basketbol_sahasi', 'site_market',
    'site_kres', 'site_cafe', 'site_restorant', 'site_kuafor', 'site_toplanti_odasi',
    'site_bahce', 'site_evcil_hayvan', 'site_engelli_erisimi'
];
$site_feature_vals = [];
foreach ($site_features as $feature) {
    $site_feature_vals[$feature] = isset($_POST[$feature]) ? 1 : 0;
}

// Property type EN
$property_types_en = [
    'ev' => 'house',
    'daire' => 'apartment',
    'arsa' => 'land',
    'tarla' => 'field',
    'dukkan' => 'shop',
    'villa' => 'villa'
];
$property_type_en = $property_types_en[$property_type] ?? 'house';

if (!in_array($listing_type, ['satilik', 'kiralik'])) {
    $listing_type = 'satilik';
}

// Slug oluştur
$slug = createSlug($title_tr);
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
    if ($checkStmt->fetchColumn() == 0)
        break;
    $slug = $originalSlug . '-' . $counter;
    $counter++;
}

function createSlug($str)
{
    $str = mb_strtolower($str, 'UTF-8');
    $str = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'g', 'u', 's', 'o', 'c'], $str);
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    $str = trim($str, '-');
    return $str ?: 'property';
}

// Resim yükleme
$all_images = [];
if (!empty($_POST['existing_images_list'])) {
    $all_images = json_decode($_POST['existing_images_list'], true) ?? [];
}

$uploaded_images = $_FILES['uploaded_images'] ?? null;
if ($uploaded_images && !empty($uploaded_images['name'])) {
    for ($i = 0; $i < count($uploaded_images['name']); $i++) {
        if (!empty($uploaded_images['name'][$i]) && $uploaded_images['error'][$i] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($uploaded_images['name'][$i], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $filename = time() . '_' . uniqid() . '_' . basename($uploaded_images['name'][$i]);
                if (move_uploaded_file($uploaded_images['tmp_name'][$i], '../uploads/' . $filename)) {
                    $all_images[] = $filename;
                }
            }
        }
    }
}

// Ana Resim Sıralaması
$main_image_index = isset($_POST['main_image_index']) ? (int)$_POST['main_image_index'] : 0;
if ($main_image_index > 0 && isset($all_images[$main_image_index])) {
    $main_image = $all_images[$main_image_index];
    array_splice($all_images, $main_image_index, 1);
    array_unshift($all_images, $main_image);
}

$images = [];
for ($i = 1; $i <= 20; $i++) {
    $images['image' . $i] = $all_images[$i - 1] ?? null;
}

try {
    if ($is_edit) {
        $sql = "UPDATE listings SET title_tr = :title_tr, title_en = :title_en, 
                description_tr = :description_tr, description_en = :description_en,
                property_type = :property_type, property_type_en = :property_type_en,
                listing_type = :listing_type, price = :price, area = :area,
                rooms = :rooms, bathrooms = :bathrooms, address = :address,
                city = :city, district = :district, mahalle = :mahalle,
                status = :status, slug = :slug, notes = :notes,
                imar_durumu = :imar_durumu, tapu_durumu = :tapu_durumu,
                krediye_uygun = :krediye_uygun, ada_no = :ada_no, parsel_no = :parsel_no,
                brut_metrekare = :brut_metrekare, net_metrekare = :net_metrekare,
                bulundugu_kat = :bulundugu_kat, kat_sayisi = :kat_sayisi,
                isitma_turu = :isitma_turu, esyali = :esyali,
                balkon = :balkon, kullanim_durumu = :kullanim_durumu,
                tapu_durumu_konut = :tapu_durumu_konut,
                takas = :takas, kat_karsiligi = :kat_karsiligi,
                asansor = :asansor, otopark = :otopark, guvenlik = :guvenlik,
                kamera_sistemi = :kamera_sistemi, kapici = :kapici, isi_yalitim = :isi_yalitim,
                cadde_uzerinde = :cadde_uzerinde, toplu_ulasim = :toplu_ulasim,
                merkeze_yakin = :merkeze_yakin, okula_yakin = :okula_yakin,
                hastaneye_yakin = :hastaneye_yakin, market_yakin = :market_yakin,
                merkeze_uzaklik = :merkeze_uzaklik, ana_yola_cephe = :ana_yola_cephe,
                mutfak_ticari = :mutfak_ticari, kullanim_durumu_ticari = :kullanim_durumu_ticari,
                fiyat_turu = :fiyat_turu, price_per_sqm = :price_per_sqm,
                toplam_alan = :toplam_alan, kullanim_alani = :kullanim_alani,
                zemin_turu = :zemin_turu, on_cephe_uzunlugu = :on_cephe_uzunlugu,
                giris_yuksekligi = :giris_yuksekligi, wc_lavabo = :wc_lavabo,
                vitrin_cami = :vitrin_cami, tabela_gorunurluk = :tabela_gorunurluk,
                yaya_trafik = :yaya_trafik,
                elektrik = :elektrik, su = :su, kanalizasyon = :kanalizasyon,
                dogalgaz = :dogalgaz, telefon = :telefon, yolu_acilmis = :yolu_acilmis,
                parselli = :parselli, ifrazli = :ifrazli, cephe = :cephe,
                manzara_tipi = :manzara_tipi, doga_manzara = :doga_manzara,
                yuk_asansor = :yuk_asansor, depo_alani = :depo_alani, yalitim = :yalitim,
                site_icinde = :site_icinde,
                site_guvenlik = :site_guvenlik, site_spor_salonu = :site_spor_salonu,
                site_yuzme_havuzu = :site_yuzme_havuzu, site_cocuk_parki = :site_cocuk_parki,
                site_sauna = :site_sauna, site_turk_hamami = :site_turk_hamami,
                site_jenerator = :site_jenerator, site_kapali_otopark = :site_kapali_otopark,
                site_acik_otopark = :site_acik_otopark, site_tenis_kortu = :site_tenis_kortu,
                site_basketbol_sahasi = :site_basketbol_sahasi, site_market = :site_market,
                site_kres = :site_kres, site_cafe = :site_cafe,
                site_restorant = :site_restorant, site_kuafor = :site_kuafor,
                site_toplanti_odasi = :site_toplanti_odasi, site_bahce = :site_bahce,
                site_evcil_hayvan = :site_evcil_hayvan, site_engelli_erisimi = :site_engelli_erisimi,
                approval_status = 'pending'";

        for ($i = 1; $i <= 20; $i++)
            $sql .= ", image$i = :image$i";
        $sql .= " WHERE id = :id AND user_id = :uid";

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
            ':slug' => $slug,
            ':notes' => $notes,
            ':imar_durumu' => $imar_durumu ?: null,
            ':tapu_durumu' => $tapu_durumu ?: null,
            ':krediye_uygun' => $krediye_uygun ?: null,
            ':ada_no' => $ada_no,
            ':parsel_no' => $parsel_no,
            ':brut_metrekare' => $brut_metrekare ?: null,
            ':net_metrekare' => $net_metrekare ?: null,
            ':oda_sayisi' => $oda_sayisi,
            ':bina_yasi' => $bina_yasi ?: null,
            ':bulundugu_kat' => $bulundugu_kat,
            ':kat_sayisi' => $kat_sayisi ?: null,
            ':isitma_turu' => $isitma_turu ?: null,
            ':esyali' => $esyali ?: null,
            ':balkon' => $balkon,
            ':kullanim_durumu' => $kullanim_durumu,
            ':tapu_durumu_konut' => $tapu_durumu_konut,
            ':takas' => $takas,
            ':kat_karsiligi' => $kat_karsiligi,
            ':asansor' => $asansor,
            ':otopark' => $otopark,
            ':guvenlik' => $guvenlik,
            ':kamera_sistemi' => $kamera_sistemi,
            ':kapici' => $kapici,
            ':isi_yalitim' => $isi_yalitim,
            ':cadde_uzerinde' => $cadde_uzerinde,
            ':toplu_ulasim' => $toplu_ulasim,
            ':merkeze_yakin' => $merkeze_yakin,
            ':okula_yakin' => $okula_yakin,
            ':hastaneye_yakin' => $hastaneye_yakin,
            ':market_yakin' => $market_yakin,
            ':merkeze_uzaklik' => $merkeze_uzaklik ?: null,
            ':ana_yola_cephe' => $ana_yola_cephe,
            ':mutfak_ticari' => $mutfak_ticari,
            ':kullanim_durumu_ticari' => $kullanim_durumu_ticari,
            ':fiyat_turu' => $fiyat_turu,
            ':price_per_sqm' => $price_per_sqm ?: null,
            ':toplam_alan' => $toplam_alan,
            ':kullanim_alani' => $kullanim_alani,
            ':zemin_turu' => $zemin_turu,
            ':on_cephe_uzunlugu' => $on_cephe_uzunlugu,
            ':giris_yuksekligi' => $giris_yuksekligi,
            ':wc_lavabo' => $wc_lavabo,
            ':vitrin_cami' => $vitrin_cami,
            ':tabela_gorunurluk' => $tabela_gorunurluk,
            ':yaya_trafik' => $yaya_trafik,
            ':elektrik' => $elektrik,
            ':su' => $su,
            ':kanalizasyon' => $kanalizasyon,
            ':dogalgaz' => $dogalgaz,
            ':telefon' => $telefon,
            ':yolu_acilmis' => $yolu_acilmis,
            ':parselli' => $parselli,
            ':ifrazli' => $ifrazli,
            ':cephe' => $cephe,
            ':manzara_tipi' => $manzara_tipi,
            ':doga_manzara' => $doga_manzara,
            ':yuk_asansor' => $yuk_asansor,
            ':depo_alani' => $depo_alani,
            ':yalitim' => $yalitim,
            ':site_icinde' => $site_icinde,
            ':site_guvenlik' => $site_feature_vals['site_guvenlik'],
            ':site_spor_salonu' => $site_feature_vals['site_spor_salonu'],
            ':site_yuzme_havuzu' => $site_feature_vals['site_yuzme_havuzu'],
            ':site_cocuk_parki' => $site_feature_vals['site_cocuk_parki'],
            ':site_sauna' => $site_feature_vals['site_sauna'],
            ':site_turk_hamami' => $site_feature_vals['site_turk_hamami'],
            ':site_jenerator' => $site_feature_vals['site_jenerator'],
            ':site_kapali_otopark' => $site_feature_vals['site_kapali_otopark'],
            ':site_acik_otopark' => $site_feature_vals['site_acik_otopark'],
            ':site_tenis_kortu' => $site_feature_vals['site_tenis_kortu'],
            ':site_basketbol_sahasi' => $site_feature_vals['site_basketbol_sahasi'],
            ':site_market' => $site_feature_vals['site_market'],
            ':site_kres' => $site_feature_vals['site_kres'],
            ':site_cafe' => $site_feature_vals['site_cafe'],
            ':site_restorant' => $site_feature_vals['site_restorant'],
            ':site_kuafor' => $site_feature_vals['site_kuafor'],
            ':site_toplanti_odasi' => $site_feature_vals['site_toplanti_odasi'],
            ':site_bahce' => $site_feature_vals['site_bahce'],
            ':site_evcil_hayvan' => $site_feature_vals['site_evcil_hayvan'],
            ':site_engelli_erisimi' => $site_feature_vals['site_engelli_erisimi'],
            ':id' => $id,
            ':uid' => $user_id
        ];

        for ($i = 1; $i <= 20; $i++)
            $params[":image$i"] = $images["image$i"];

        $stmt->execute($params);
        echo json_encode(['success' => true, 'message' => 'İlan güncellendi! Onay sürecine gönderildi.', 'id' => $id]);

    } else {
        // INSERT - Yeni ilan
        $sql = "INSERT INTO listings (title_tr, title_en, description_tr, description_en,
                property_type, property_type_en, listing_type, price, area, rooms, bathrooms,
                address, city, district, mahalle, status, slug, notes,
                imar_durumu, tapu_durumu, krediye_uygun, ada_no, parsel_no,
                brut_metrekare, net_metrekare, oda_sayisi, bina_yasi,
                bulundugu_kat, kat_sayisi, isitma_turu, esyali,
                balkon, kullanim_durumu, tapu_durumu_konut,
                takas, kat_karsiligi,
                asansor, otopark, guvenlik, kamera_sistemi, kapici, isi_yalitim,
                cadde_uzerinde, toplu_ulasim, merkeze_yakin, okula_yakin,
                hastaneye_yakin, market_yakin, merkeze_uzaklik, ana_yola_cephe,
                mutfak_ticari, kullanim_durumu_ticari,
                fiyat_turu, price_per_sqm,
                toplam_alan, kullanim_alani, zemin_turu, on_cephe_uzunlugu,
                giris_yuksekligi, wc_lavabo, vitrin_cami, tabela_gorunurluk, yaya_trafik,
                elektrik, su, kanalizasyon, dogalgaz, telefon, yolu_acilmis, parselli, ifrazli,
                cephe, manzara_tipi, doga_manzara,
                yuk_asansor, depo_alani, yalitim, site_icinde,
                site_guvenlik, site_spor_salonu, site_yuzme_havuzu, site_cocuk_parki,
                site_sauna, site_turk_hamami, site_jenerator, site_kapali_otopark,
                site_acik_otopark, site_tenis_kortu, site_basketbol_sahasi, site_market,
                site_kres, site_cafe, site_restorant, site_kuafor,
                site_toplanti_odasi, site_bahce, site_evcil_hayvan, site_engelli_erisimi,
                user_id, user_type, created_by, approval_status, ilan_sahibi_turu";

        for ($i = 1; $i <= 20; $i++)
            $sql .= ", image$i";

        $sql .= ") VALUES (:title_tr, :title_en, :description_tr, :description_en,
                :property_type, :property_type_en, :listing_type, :price, :area, :rooms, :bathrooms,
                :address, :city, :district, :mahalle, :status, :slug, :notes,
                :imar_durumu, :tapu_durumu, :krediye_uygun, :ada_no, :parsel_no,
                :brut_metrekare, :net_metrekare, :oda_sayisi, :bina_yasi,
                :bulundugu_kat, :kat_sayisi, :isitma_turu, :esyali,
                :balkon, :kullanim_durumu, :tapu_durumu_konut,
                :takas, :kat_karsiligi,
                :asansor, :otopark, :guvenlik, :kamera_sistemi, :kapici, :isi_yalitim,
                :cadde_uzerinde, :toplu_ulasim, :merkeze_yakin, :okula_yakin,
                :hastaneye_yakin, :market_yakin, :merkeze_uzaklik, :ana_yola_cephe,
                :mutfak_ticari, :kullanim_durumu_ticari,
                :fiyat_turu, :price_per_sqm,
                :toplam_alan, :kullanim_alani, :zemin_turu, :on_cephe_uzunlugu,
                :giris_yuksekligi, :wc_lavabo, :vitrin_cami, :tabela_gorunurluk, :yaya_trafik,
                :elektrik, :su, :kanalizasyon, :dogalgaz, :telefon, :yolu_acilmis, :parselli, :ifrazli,
                :cephe, :manzara_tipi, :doga_manzara,
                :yuk_asansor, :depo_alani, :yalitim, :site_icinde,
                :site_guvenlik, :site_spor_salonu, :site_yuzme_havuzu, :site_cocuk_parki,
                :site_sauna, :site_turk_hamami, :site_jenerator, :site_kapali_otopark,
                :site_acik_otopark, :site_tenis_kortu, :site_basketbol_sahasi, :site_market,
                :site_kres, :site_cafe, :site_restorant, :site_kuafor,
                :site_toplanti_odasi, :site_bahce, :site_evcil_hayvan, :site_engelli_erisimi,
                :user_id, 'emlakci', :created_by, 'pending', 'Emlakçı'";
        for ($i = 1; $i <= 20; $i++)
            $sql .= ", :image$i";
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
            ':slug' => $slug,
            ':notes' => $notes,
            ':imar_durumu' => $imar_durumu ?: null,
            ':tapu_durumu' => $tapu_durumu ?: null,
            ':krediye_uygun' => $krediye_uygun ?: null,
            ':ada_no' => $ada_no,
            ':parsel_no' => $parsel_no,
            ':brut_metrekare' => $brut_metrekare ?: null,
            ':net_metrekare' => $net_metrekare ?: null,
            ':oda_sayisi' => $oda_sayisi,
            ':bina_yasi' => $bina_yasi ?: null,
            ':bulundugu_kat' => $bulundugu_kat,
            ':kat_sayisi' => $kat_sayisi ?: null,
            ':isitma_turu' => $isitma_turu ?: null,
            ':esyali' => $esyali ?: null,
            ':balkon' => $balkon,
            ':kullanim_durumu' => $kullanim_durumu,
            ':tapu_durumu_konut' => $tapu_durumu_konut,
            ':takas' => $takas,
            ':kat_karsiligi' => $kat_karsiligi,
            ':asansor' => $asansor,
            ':otopark' => $otopark,
            ':guvenlik' => $guvenlik,
            ':kamera_sistemi' => $kamera_sistemi,
            ':kapici' => $kapici,
            ':isi_yalitim' => $isi_yalitim,
            ':cadde_uzerinde' => $cadde_uzerinde,
            ':toplu_ulasim' => $toplu_ulasim,
            ':merkeze_yakin' => $merkeze_yakin,
            ':okula_yakin' => $okula_yakin,
            ':hastaneye_yakin' => $hastaneye_yakin,
            ':market_yakin' => $market_yakin,
            ':merkeze_uzaklik' => $merkeze_uzaklik ?: null,
            ':ana_yola_cephe' => $ana_yola_cephe,
            ':mutfak_ticari' => $mutfak_ticari,
            ':kullanim_durumu_ticari' => $kullanim_durumu_ticari,
            ':fiyat_turu' => $fiyat_turu,
            ':price_per_sqm' => $price_per_sqm ?: null,
            ':toplam_alan' => $toplam_alan,
            ':kullanim_alani' => $kullanim_alani,
            ':zemin_turu' => $zemin_turu,
            ':on_cephe_uzunlugu' => $on_cephe_uzunlugu,
            ':giris_yuksekligi' => $giris_yuksekligi,
            ':wc_lavabo' => $wc_lavabo,
            ':vitrin_cami' => $vitrin_cami,
            ':tabela_gorunurluk' => $tabela_gorunurluk,
            ':yaya_trafik' => $yaya_trafik,
            ':elektrik' => $elektrik,
            ':su' => $su,
            ':kanalizasyon' => $kanalizasyon,
            ':dogalgaz' => $dogalgaz,
            ':telefon' => $telefon,
            ':yolu_acilmis' => $yolu_acilmis,
            ':parselli' => $parselli,
            ':ifrazli' => $ifrazli,
            ':cephe' => $cephe,
            ':manzara_tipi' => $manzara_tipi,
            ':doga_manzara' => $doga_manzara,
            ':yuk_asansor' => $yuk_asansor,
            ':depo_alani' => $depo_alani,
            ':yalitim' => $yalitim,
            ':site_icinde' => $site_icinde,
            ':site_guvenlik' => $site_feature_vals['site_guvenlik'],
            ':site_spor_salonu' => $site_feature_vals['site_spor_salonu'],
            ':site_yuzme_havuzu' => $site_feature_vals['site_yuzme_havuzu'],
            ':site_cocuk_parki' => $site_feature_vals['site_cocuk_parki'],
            ':site_sauna' => $site_feature_vals['site_sauna'],
            ':site_turk_hamami' => $site_feature_vals['site_turk_hamami'],
            ':site_jenerator' => $site_feature_vals['site_jenerator'],
            ':site_kapali_otopark' => $site_feature_vals['site_kapali_otopark'],
            ':site_acik_otopark' => $site_feature_vals['site_acik_otopark'],
            ':site_tenis_kortu' => $site_feature_vals['site_tenis_kortu'],
            ':site_basketbol_sahasi' => $site_feature_vals['site_basketbol_sahasi'],
            ':site_market' => $site_feature_vals['site_market'],
            ':site_kres' => $site_feature_vals['site_kres'],
            ':site_cafe' => $site_feature_vals['site_cafe'],
            ':site_restorant' => $site_feature_vals['site_restorant'],
            ':site_kuafor' => $site_feature_vals['site_kuafor'],
            ':site_toplanti_odasi' => $site_feature_vals['site_toplanti_odasi'],
            ':site_bahce' => $site_feature_vals['site_bahce'],
            ':site_evcil_hayvan' => $site_feature_vals['site_evcil_hayvan'],
            ':site_engelli_erisimi' => $site_feature_vals['site_engelli_erisimi'],
            ':user_id' => $user_id,
            ':created_by' => $user_id
        ];

        if (!empty($images)) {
            foreach ($images as $key => $value) {
                $params[":$key"] = $value;
            }
        }

        $stmt->execute($params);
        $id = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'message' => 'İlan eklendi! Onay sürecine gönderildi.', 'id' => $id]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
}
?>