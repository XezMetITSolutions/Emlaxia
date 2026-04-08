<?php
require_once '../config.php';

// Admin kontrolü
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login');
    exit;
}

$id = $_GET['id'] ?? 0;
$is_edit = false;
$map_address = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM listings WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $listing = $stmt->fetch();
    $is_edit = true;
    $map_address = $listing['map_address'] ?? '';
}

// Şehir listesi (veritabanından)
$stmt = $pdo->query("SELECT id, il_adi FROM iller ORDER BY il_adi");
$cities = $stmt->fetchAll();

// Seçili şehre göre ilçeler
$districts = [];
if ($is_edit && !empty($listing['city'])) {
    $stmt = $pdo->prepare("SELECT id FROM iller WHERE il_adi = :city");
    $stmt->execute([':city' => $listing['city']]);
    $city_id = $stmt->fetchColumn();
    if ($city_id) {
        $stmt = $pdo->prepare("SELECT id, ilce_adi FROM ilceler WHERE il_id = :city_id ORDER BY ilce_adi");
        $stmt->execute([':city_id' => $city_id]);
        $districts = $stmt->fetchAll();
    }
}

// Seçili ilçeye göre mahalleler
$neighborhoods = [];
if ($is_edit && !empty($listing['district'])) {
    $stmt = $pdo->prepare("SELECT id FROM ilceler WHERE ilce_adi = :district");
    $stmt->execute([':district' => $listing['district']]);
    $district_id = $stmt->fetchColumn();
    if ($district_id) {
        $stmt = $pdo->prepare("SELECT id, mahalle_adi FROM mahalleler WHERE ilce_id = :district_id ORDER BY mahalle_adi LIMIT 500");
        $stmt->execute([':district_id' => $district_id]);
        $neighborhoods = $stmt->fetchAll();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title_tr = $_POST['title_tr'] ?? '';
    $title_en = $_POST['title_en'] ?? '';
    $description_tr = $_POST['description_tr'] ?? '';
    $description_en = $_POST['description_en'] ?? '';
    $property_type = $_POST['property_type'] ?? '';
    $price = $_POST['price'] ?? 0;
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
    $imar_durumu = $_POST['imar_durumu'] ?? null;
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

    // Resim yükleme (20 resme kadar destek)
    $images = [];
    for ($i = 1; $i <= 20; $i++) {
        if (!empty($_FILES['image' . $i]['name'])) {
            $filename = time() . '_' . basename($_FILES['image' . $i]['name']);
            $target = '../uploads/' . $filename;
            if (move_uploaded_file($_FILES['image' . $i]['tmp_name'], $target)) {
                $images['image' . $i] = $filename;
            }
        } elseif ($is_edit && $listing) {
            $images['image' . $i] = $listing['image' . $i] ?? null;
        }
    }

    if ($is_edit && $listing) {
        // Güncelle
        $sql = "UPDATE listings SET title_tr = :title_tr, title_en = :title_en, description_tr = :description_tr, 
                description_en = :description_en, property_type = :property_type, property_type_en = :property_type_en,
                price = :price, area = :area, rooms = :rooms, bathrooms = :bathrooms, address = :address, 
                city = :city, district = :district, mahalle = :mahalle, status = :status,
                price_per_sqm = :price_per_sqm, imar_durumu = :imar_durumu, tapu_durumu = :tapu_durumu,
                krediye_uygun = :krediye_uygun, takas = :takas, kat_karsiligi = :kat_karsiligi,
                ada_no = :ada_no, parsel_no = :parsel_no, cadde_uzerinde = :cadde_uzerinde,
                toplu_ulasim = :toplu_ulasim, merkeze_yakin = :merkeze_yakin, elektrik = :elektrik,
                su = :su, kanalizasyon = :kanalizasyon, dogalgaz = :dogalgaz, telefon = :telefon,
                yolu_acilmis = :yolu_acilmis, parselli = :parselli, ifrazli = :ifrazli,
                doga_manzara = :doga_manzara, manzara_tipi = :manzara_tipi, fiyat_turu = :fiyat_turu,
                notes = :notes,
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
                depo_alani = :depo_alani, yalitim = :yalitim, ana_yola_cephe = :ana_yola_cephe";

        for ($i = 1; $i <= 5; $i++) {
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
            ':id' => $id
        ];

        for ($i = 1; $i <= 5; $i++) {
            if (isset($images['image' . $i])) {
                $params[":image$i"] = $images['image' . $i];
            }
        }

        try {
            $stmt->execute($params);
            $_SESSION['success_message'] = t('listing_updated');
        } catch (PDOException $e) {
            $_SESSION['error_message'] = 'Hata: ' . $e->getMessage();
        }
    } else {
        // Yeni ilan ekle
        $sql = "INSERT INTO listings (title_tr, title_en, description_tr, description_en, property_type, property_type_en,
                price, area, rooms, bathrooms, address, city, district, mahalle, status, created_by,
                price_per_sqm, imar_durumu, tapu_durumu, krediye_uygun, takas, kat_karsiligi,
                ada_no, parsel_no, cadde_uzerinde, toplu_ulasim, merkeze_yakin, elektrik,
                su, kanalizasyon, dogalgaz, telefon, yolu_acilmis, parselli, ifrazli,
                doga_manzara, manzara_tipi, fiyat_turu, notes,
                brut_metrekare, net_metrekare, oda_sayisi, bina_yasi, bulundugu_kat, kat_sayisi,
                isitma_turu, balkon, esyali, kullanim_durumu, tapu_durumu_konut,
                asansor, otopark, guvenlik, site_icinde, kamera_sistemi, kapici, isi_yalitim,
                okula_yakin, hastaneye_yakin, market_yakin, merkeze_uzaklik, cephe,
                toplam_alan, kullanim_alani, zemin_turu, on_cephe_uzunlugu, giris_yuksekligi,
                wc_lavabo, mutfak_ticari, vitrin_cami, tabela_gorunurluk, yaya_trafik,
                kullanim_durumu_ticari, yuk_asansor, depo_alani, yalitim, ana_yola_cephe";

        if (!empty($images)) {
            foreach ($images as $key => $value) {
                $sql .= ", $key";
            }
        }

        $sql .= ") VALUES (:title_tr, :title_en, :description_tr, :description_en, :property_type, :property_type_en,
                :price, :area, :rooms, :bathrooms, :address, :city, :district, :mahalle, :status, :created_by,
                :price_per_sqm, :imar_durumu, :tapu_durumu, :krediye_uygun, :takas, :kat_karsiligi,
                :ada_no, :parsel_no, :cadde_uzerinde, :toplu_ulasim, :merkeze_yakin, :elektrik,
                :su, :kanalizasyon, :dogalgaz, :telefon, :yolu_acilmis, :parselli, :ifrazli,
                :doga_manzara, :manzara_tipi, :fiyat_turu, :notes,
                :brut_metrekare, :net_metrekare, :oda_sayisi, :bina_yasi, :bulundugu_kat, :kat_sayisi,
                :isitma_turu, :balkon, :esyali, :kullanim_durumu, :tapu_durumu_konut,
                :asansor, :otopark, :guvenlik, :site_icinde, :kamera_sistemi, :kapici, :isi_yalitim,
                :okula_yakin, :hastaneye_yakin, :market_yakin, :merkeze_uzaklik, :cephe,
                :toplam_alan, :kullanim_alani, :zemin_turu, :on_cephe_uzunlugu, :giris_yuksekligi,
                :wc_lavabo, :mutfak_ticari, :vitrin_cami, :tabela_gorunurluk, :yaya_trafik,
                :kullanim_durumu_ticari, :yuk_asansor, :depo_alani, :yalitim, :ana_yola_cephe";

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
            ':ana_yola_cephe' => $ana_yola_cephe ?: null
        ];

        if (!empty($images)) {
            foreach ($images as $key => $value) {
                $params[":$key"] = $value;
            }
        }

        try {
            $stmt->execute($params);
            $_SESSION['success_message'] = t('listing_added');
        } catch (PDOException $e) {
            $_SESSION['error_message'] = 'Hata: ' . $e->getMessage();
        }
    }

    header('Location: ilanlar');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? t('edit_listing') : t('new_listing'); ?> - Admin Panel</title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .form-section {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            padding: 2.5rem;
            border-radius: var(--radius-2xl);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .form-section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--gray-200);
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-section-title::before {
            content: '';
            width: 4px;
            height: 24px;
            background: var(--gradient-primary);
            border-radius: var(--radius-full);
        }

        .form-section.hidden {
            display: none;
        }

        .conditional-field {
            transition: all 0.3s ease;
        }

        .conditional-field.hidden {
            display: none;
            opacity: 0;
        }

        .price-calculator {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
        }

        .price-calculator input {
            flex: 1;
        }

        .calc-info {
            padding: 0.75rem;
            background: var(--gray-50);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-top: 0.5rem;
        }

        /* Site Özellikleri Grid */
        .site-features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .feature-checkbox {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .feature-checkbox:hover {
            border-color: var(--primary-color);
            background: rgba(30, 136, 229, 0.05);
        }

        .feature-checkbox input[type="checkbox"] {
            display: none;
        }

        .feature-checkbox input[type="checkbox"]:checked+label {
            color: var(--primary-color);
            font-weight: 600;
        }

        .feature-checkbox input[type="checkbox"]:checked+label i {
            color: var(--primary-color);
        }

        .feature-checkbox input[type="checkbox"]:checked~* {
            color: var(--primary-color);
        }

        .feature-checkbox:has(input[type="checkbox"]:checked) {
            border-color: var(--primary-color);
            background: rgba(30, 136, 229, 0.1);
        }

        .feature-checkbox label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            font-size: 0.9375rem;
            color: var(--gray-700);
            margin: 0;
            width: 100%;
        }

        .feature-checkbox label i {
            font-size: 1.25rem;
            color: var(--gray-400);
            width: 24px;
            text-align: center;
            transition: color 0.2s ease;
        }

        /* Multi Image Upload Styles */
        .multi-upload-dropzone {
            border: 3px dashed var(--gray-300);
            border-radius: var(--radius-2xl);
            padding: 3rem 2rem;
            text-align: center;
            background: linear-gradient(135deg, rgba(30, 136, 229, 0.02) 0%, rgba(30, 136, 229, 0.05) 100%);
            transition: all 0.3s ease;
            cursor: pointer;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .multi-upload-dropzone:hover {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(30, 136, 229, 0.05) 0%, rgba(30, 136, 229, 0.1) 100%);
        }

        .multi-upload-dropzone.dragover {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(30, 136, 229, 0.1) 0%, rgba(30, 136, 229, 0.15) 100%);
            transform: scale(1.01);
        }

        .dropzone-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .dropzone-content svg {
            color: var(--primary-color);
            opacity: 0.7;
        }

        .dropzone-content h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
        }

        .dropzone-content p {
            color: var(--gray-500);
            margin: 0;
        }

        .dropzone-content .upload-hint {
            font-size: 0.875rem;
            color: var(--gray-400);
            margin-top: 0.5rem;
        }

        /* Uploaded Images Container */
        .uploaded-images-container {
            margin-top: 2rem;
            background: rgba(255, 255, 255, 0.98);
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            border: 1px solid var(--gray-200);
        }

        .uploaded-images-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .uploaded-images-header h4 {
            display: flex;
            align-items: center;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
        }

        .main-image-hint {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            color: var(--gray-500);
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            background: rgba(30, 136, 229, 0.05);
            border-radius: var(--radius-lg);
            border-left: 3px solid var(--primary-color);
        }

        .uploaded-images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .uploaded-image-item {
            position: relative;
            border-radius: var(--radius-lg);
            overflow: hidden;
            aspect-ratio: 1;
            background: var(--gray-100);
            cursor: pointer;
            transition: all 0.2s ease;
            border: 3px solid transparent;
        }

        .uploaded-image-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .uploaded-image-item.main-image {
            border-color: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3);
        }

        .uploaded-image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .uploaded-image-item .image-actions {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .uploaded-image-item:hover .image-actions {
            opacity: 1;
        }

        .uploaded-image-item .btn-remove {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            z-index: 10;
        }

        .uploaded-image-item .btn-remove:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        .uploaded-image-item .main-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            gap: 4px;
            z-index: 10;
        }

        .uploaded-image-item .main-badge svg {
            width: 12px;
            height: 12px;
        }

        .uploaded-image-item .set-main-btn {
            position: absolute;
            bottom: 8px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.95);
            color: var(--gray-700);
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.35rem 0.75rem;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            opacity: 0;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .uploaded-image-item:hover .set-main-btn {
            opacity: 1;
        }

        .uploaded-image-item .set-main-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .uploaded-image-item.main-image .set-main-btn {
            display: none;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .uploaded-images-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }

            .multi-upload-dropzone {
                min-height: 200px;
                padding: 2rem 1rem;
            }

            .dropzone-content h3 {
                font-size: 1.125rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/admin_header.php'; ?>

    <main>
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h1 style="margin: 0;"><?php echo $is_edit ? t('edit_listing') : t('new_listing'); ?></h1>
                <a href="ilanlar.php" class="btn btn-secondary">← <?php echo t('back'); ?></a>
            </div>

            <form method="POST" enctype="multipart/form-data" class="listing-form" id="listingForm"
                action="process_listing.php">

                <!-- Temel Bilgiler -->
                <div class="form-section">
                    <div class="form-section-title">1.
                        <?php echo $lang == 'tr' ? 'Temel Bilgiler' : 'Basic Information'; ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Başlık (Türkçe)' : 'Title (Turkish)'; ?> *</label>
                            <input type="text" name="title_tr"
                                value="<?php echo htmlspecialchars($listing['title_tr'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Title (English)' : 'Title (English)'; ?> *</label>
                            <input type="text" name="title_en"
                                value="<?php echo htmlspecialchars($listing['title_en'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Açıklama (Türkçe)' : 'Description (Turkish)'; ?></label>
                            <textarea name="description_tr"
                                rows="4"><?php echo htmlspecialchars($listing['description_tr'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Description (English)' : 'Description (English)'; ?></label>
                            <textarea name="description_en"
                                rows="4"><?php echo htmlspecialchars($listing['description_en'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo t('listing_type'); ?> *</label>
                            <select name="listing_type" id="listing_type" required>
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="satilik" <?php echo ($listing['listing_type'] ?? 'satilik') == 'satilik' ? 'selected' : ''; ?>><?php echo t('for_sale'); ?></option>
                                <option value="kiralik" <?php echo ($listing['listing_type'] ?? '') == 'kiralik' ? 'selected' : ''; ?>><?php echo t('for_rent'); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo t('property_type'); ?> *</label>
                            <select name="property_type" id="property_type" required>
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="ev" <?php echo ($listing['property_type'] ?? '') == 'ev' ? 'selected' : ''; ?>><?php echo t('house'); ?></option>
                                <option value="daire" <?php echo ($listing['property_type'] ?? '') == 'daire' ? 'selected' : ''; ?>><?php echo t('apartment'); ?></option>
                                <option value="arsa" <?php echo ($listing['property_type'] ?? '') == 'arsa' ? 'selected' : ''; ?> data-hide-for-rent="true"><?php echo t('land'); ?></option>
                                <option value="tarla" <?php echo ($listing['property_type'] ?? '') == 'tarla' ? 'selected' : ''; ?> data-hide-for-rent="true"><?php echo $lang == 'tr' ? 'Tarla' : 'Field'; ?>
                                </option>
                                <option value="dukkan" <?php echo ($listing['property_type'] ?? '') == 'dukkan' ? 'selected' : ''; ?>><?php echo t('shop'); ?></option>
                                <option value="villa" <?php echo ($listing['property_type'] ?? '') == 'villa' ? 'selected' : ''; ?>><?php echo t('villa'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'İlan Sahibi' : 'Listing Owner'; ?></label>
                            <select name="ilan_sahibi_turu" id="ilan_sahibi_turu">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Sahibinden" <?php echo ($listing['ilan_sahibi_turu'] ?? '') == 'Sahibinden' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Sahibinden' : 'By Owner'; ?>
                                </option>
                                <option value="Emlakçı" <?php echo ($listing['ilan_sahibi_turu'] ?? '') == 'Emlakçı' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Emlakçı' : 'Real Estate Agent'; ?>
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo t('status'); ?> *</label>
                            <select name="status" required>
                                <option value="active" <?php echo ($listing['status'] ?? 'active') == 'active' ? 'selected' : ''; ?>><?php echo t('active'); ?></option>
                                <option value="sold" <?php echo ($listing['status'] ?? '') == 'sold' ? 'selected' : ''; ?>><?php echo t('sold'); ?></option>
                                <option value="pending" <?php echo ($listing['status'] ?? '') == 'pending' ? 'selected' : ''; ?>><?php echo t('pending'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group conditional-field" id="komisyon-field">
                            <label><?php echo $lang == 'tr' ? 'Komisyon Yüzdesi (%)' : 'Commission Percentage (%)'; ?></label>
                            <input type="number" name="komisyon_yuzdesi" id="komisyon_yuzdesi" step="0.01" min="0"
                                max="100" value="<?php echo $listing['komisyon_yuzdesi'] ?? ''; ?>"
                                placeholder="<?php echo $lang == 'tr' ? 'Örn: 2.5' : 'Ex: 2.5'; ?>">
                            <small
                                style="color: #666; display: block; margin-top: 0.25rem;"><?php echo $lang == 'tr' ? 'Emlakçı komisyon yüzdesini giriniz' : 'Enter the agent commission percentage'; ?></small>
                        </div>
                    </div>
                </div>

                <!-- Fiyat Bilgileri -->
                <div class="form-section">
                    <div class="form-section-title">2.
                        <?php echo $lang == 'tr' ? 'Fiyat Bilgileri' : 'Price Information'; ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Fiyat Türü' : 'Price Type'; ?></label>
                            <select name="fiyat_turu" id="fiyat_turu">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Genel Fiyat" <?php echo ($listing['fiyat_turu'] ?? '') == 'Genel Fiyat' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Genel Fiyat' : 'General Price'; ?>
                                </option>
                                <option value="m² Fiyatı" <?php echo ($listing['fiyat_turu'] ?? '') == 'm² Fiyatı' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'm² Fiyatı' : 'Price per m²'; ?>
                                </option>
                                <option value="Pazarlıklı" <?php echo ($listing['fiyat_turu'] ?? '') == 'Pazarlıklı' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Pazarlıklı' : 'Negotiable'; ?>
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo t('price'); ?> (<?php echo t('currency'); ?>) *</label>
                            <input type="text" name="price" id="total_price" step="0.01" min="0"
                                value="<?php echo $listing['price'] ? number_format($listing['price'], 0, ',', '.') : ''; ?>"
                                required>
                        </div>
                    </div>

                    <!-- Arsa için özel fiyat alanları -->
                    <div class="conditional-field" id="land-price-fields">
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Metrekare (m²)' : 'Square Meter (m²)'; ?> *</label>
                                <input type="number" name="area" id="land_area" step="0.01" min="0"
                                    value="<?php echo $listing['area'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'm² Birim Fiyatı (TL/m²)' : 'Price per m² (TL/m²)'; ?>
                                    *</label>
                                <input type="text" name="price_per_sqm" id="price_per_sqm" step="0.01" min="0"
                                    value="<?php echo $listing['price_per_sqm'] ? number_format($listing['price_per_sqm'], 0, ',', '.') : ''; ?>">
                            </div>
                        </div>
                        <div class="calc-info" id="price-calc-info">
                            <?php echo $lang == 'tr' ? 'Toplam fiyat otomatik hesaplanacak: m² × m² Fiyatı = Toplam Fiyat' : 'Total price will be calculated automatically: m² × Price per m² = Total Price'; ?>
                        </div>
                    </div>

                    <!-- Konut (Ev, Daire, Villa) için standart alanlar -->
                    <div class="conditional-field" id="residential-fields">
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Brüt m²' : 'Gross m²'; ?></label>
                                <input type="number" name="brut_metrekare" step="0.01" min="0"
                                    value="<?php echo $listing['brut_metrekare'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Net m²' : 'Net m²'; ?></label>
                                <input type="number" name="net_metrekare" step="0.01" min="0"
                                    value="<?php echo $listing['net_metrekare'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Oda Sayısı' : 'Room Count'; ?>
                                    (<?php echo $lang == 'tr' ? '3+1, 2+1' : '3+1, 2+1'; ?>)</label>
                                <input type="text" name="oda_sayisi"
                                    placeholder="<?php echo $lang == 'tr' ? '3+1' : '3+1'; ?>"
                                    value="<?php echo htmlspecialchars($listing['oda_sayisi'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Bina Yaşı' : 'Building Age'; ?></label>
                                <input type="number" name="bina_yasi" min="0"
                                    value="<?php echo $listing['bina_yasi'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Bulunduğu Kat' : 'Floor'; ?></label>
                                <input type="text" name="bulundugu_kat"
                                    placeholder="<?php echo $lang == 'tr' ? '2. Kat' : '2nd Floor'; ?>"
                                    value="<?php echo htmlspecialchars($listing['bulundugu_kat'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Kat Sayısı' : 'Total Floors'; ?></label>
                                <input type="number" name="kat_sayisi" min="0"
                                    value="<?php echo $listing['kat_sayisi'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Isıtma Türü' : 'Heating Type'; ?></label>
                                <select name="isitma_turu">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Kombi" <?php echo ($listing['isitma_turu'] ?? '') == 'Kombi' ? 'selected' : ''; ?>>Kombi</option>
                                    <option value="Merkezi" <?php echo ($listing['isitma_turu'] ?? '') == 'Merkezi' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Merkezi' : 'Central'; ?>
                                    </option>
                                    <option value="Soba" <?php echo ($listing['isitma_turu'] ?? '') == 'Soba' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Soba' : 'Stove'; ?></option>
                                    <option value="Klima" <?php echo ($listing['isitma_turu'] ?? '') == 'Klima' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Klima' : 'Air Conditioning'; ?>
                                    </option>
                                    <option value="Yok" <?php echo ($listing['isitma_turu'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'None'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo t('bathrooms'); ?></label>
                                <input type="number" name="bathrooms" id="bathrooms" min="0"
                                    value="<?php echo $listing['bathrooms'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Balkon' : 'Balcony'; ?></label>
                                <select name="balkon">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['balkon'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['balkon'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Eşyalı mı?' : 'Furnished?'; ?></label>
                                <select name="esyali">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['esyali'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['esyali'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Kullanım Durumu' : 'Usage Status'; ?></label>
                                <select name="kullanim_durumu">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Boş" <?php echo ($listing['kullanim_durumu'] ?? '') == 'Boş' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Boş' : 'Empty'; ?></option>
                                    <option value="Kiracılı" <?php echo ($listing['kullanim_durumu'] ?? '') == 'Kiracılı' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Kiracılı' : 'Tenanted'; ?>
                                    </option>
                                    <option value="Ev Sahibi Oturuyor" <?php echo ($listing['kullanim_durumu'] ?? '') == 'Ev Sahibi Oturuyor' ? 'selected' : ''; ?>>
                                        <?php echo $lang == 'tr' ? 'Ev Sahibi Oturuyor' : 'Owner Occupied'; ?>
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Tapu Durumu' : 'Deed Status'; ?></label>
                                <select name="tapu_durumu_konut">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Kat Mülkiyetli" <?php echo ($listing['tapu_durumu_konut'] ?? '') == 'Kat Mülkiyetli' ? 'selected' : ''; ?>>
                                        <?php echo $lang == 'tr' ? 'Kat Mülkiyetli' : 'Floor Ownership'; ?>
                                    </option>
                                    <option value="Kat İrtifaklı" <?php echo ($listing['tapu_durumu_konut'] ?? '') == 'Kat İrtifaklı' ? 'selected' : ''; ?>>
                                        <?php echo $lang == 'tr' ? 'Kat İrtifaklı' : 'Floor Easement'; ?>
                                    </option>
                                    <option value="Müstakil" <?php echo ($listing['tapu_durumu_konut'] ?? '') == 'Müstakil' ? 'selected' : ''; ?>>
                                        <?php echo $lang == 'tr' ? 'Müstakil' : 'Independent'; ?>
                                    </option>
                                    <option value="Hisseli" <?php echo ($listing['tapu_durumu_konut'] ?? '') == 'Hisseli' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hisseli' : 'Shared'; ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Ticari İşyeri (Dükkan) için standart alanlar -->
                    <div class="conditional-field" id="commercial-fields">
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Toplam Alan (m²)' : 'Total Area (m²)'; ?></label>
                                <input type="number" name="toplam_alan" step="0.01" min="0"
                                    value="<?php echo $listing['toplam_alan'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Kullanım Alanı (m²)' : 'Usable Area (m²)'; ?></label>
                                <input type="number" name="kullanim_alani" step="0.01" min="0"
                                    value="<?php echo $listing['kullanim_alani'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Zemin Türü' : 'Floor Type'; ?></label>
                                <select name="zemin_turu">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Zemin Kat" <?php echo ($listing['zemin_turu'] ?? '') == 'Zemin Kat' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Zemin Kat' : 'Ground Floor'; ?>
                                    </option>
                                    <option value="Bodrum" <?php echo ($listing['zemin_turu'] ?? '') == 'Bodrum' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Bodrum' : 'Basement'; ?>
                                    </option>
                                    <option value="Asma Kat" <?php echo ($listing['zemin_turu'] ?? '') == 'Asma Kat' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Asma Kat' : 'Mezzanine'; ?>
                                    </option>
                                    <option value="Ara Kat" <?php echo ($listing['zemin_turu'] ?? '') == 'Ara Kat' ? 'selected' : ''; ?>>
                                        <?php echo $lang == 'tr' ? 'Ara Kat' : 'Intermediate Floor'; ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Ön Cephe Uzunluğu (m)' : 'Frontage Length (m)'; ?></label>
                                <input type="number" name="on_cephe_uzunlugu" step="0.01" min="0"
                                    value="<?php echo $listing['on_cephe_uzunlugu'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Giriş Yüksekliği (m)' : 'Entrance Height (m)'; ?></label>
                                <input type="number" name="giris_yuksekligi" step="0.01" min="0"
                                    value="<?php echo $listing['giris_yuksekligi'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'WC / Lavabo' : 'WC / Sink'; ?></label>
                                <select name="wc_lavabo">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['wc_lavabo'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['wc_lavabo'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Mutfak' : 'Kitchen'; ?></label>
                                <select name="mutfak_ticari">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['mutfak_ticari'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['mutfak_ticari'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Vitrin Camı' : 'Display Window'; ?></label>
                                <select name="vitrin_cami">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['vitrin_cami'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['vitrin_cami'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Tabela Görünürlüğü' : 'Sign Visibility'; ?></label>
                                <select name="tabela_gorunurluk">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="İyi" <?php echo ($listing['tabela_gorunurluk'] ?? '') == 'İyi' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'İyi' : 'Good'; ?></option>
                                    <option value="Orta" <?php echo ($listing['tabela_gorunurluk'] ?? '') == 'Orta' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Orta' : 'Medium'; ?></option>
                                    <option value="Zayıf" <?php echo ($listing['tabela_gorunurluk'] ?? '') == 'Zayıf' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Zayıf' : 'Poor'; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Yaya Trafiği' : 'Pedestrian Traffic'; ?></label>
                                <select name="yaya_trafik">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Düşük" <?php echo ($listing['yaya_trafik'] ?? '') == 'Düşük' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Düşük' : 'Low'; ?></option>
                                    <option value="Orta" <?php echo ($listing['yaya_trafik'] ?? '') == 'Orta' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Orta' : 'Medium'; ?></option>
                                    <option value="Yüksek" <?php echo ($listing['yaya_trafik'] ?? '') == 'Yüksek' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yüksek' : 'High'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Kullanım Durumu' : 'Usage Status'; ?></label>
                                <select name="kullanim_durumu_ticari">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Boş" <?php echo ($listing['kullanim_durumu_ticari'] ?? '') == 'Boş' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Boş' : 'Empty'; ?></option>
                                    <option value="Kiracılı" <?php echo ($listing['kullanim_durumu_ticari'] ?? '') == 'Kiracılı' ? 'selected' : ''; ?>>
                                        <?php echo $lang == 'tr' ? 'Kiracılı' : 'Tenanted'; ?>
                                    </option>
                                    <option value="Mülk Sahibi Oturuyor" <?php echo ($listing['kullanim_durumu_ticari'] ?? '') == 'Mülk Sahibi Oturuyor' ? 'selected' : ''; ?>>
                                        <?php echo $lang == 'tr' ? 'Mülk Sahibi Oturuyor' : 'Owner Occupied'; ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tapu ve İmar Bilgileri (Sadece Arsa) -->
                <div class="form-section conditional-field" id="land-details-section">
                    <div class="form-section-title">3.
                        <?php echo $lang == 'tr' ? 'Tapu ve İmar Bilgileri' : 'Deed and Zoning Information'; ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group" id="imar-durumu-wrapper">
                            <label><?php echo $lang == 'tr' ? 'İmar Durumu' : 'Zoning Status'; ?></label>
                            <select name="imar_durumu">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Konut" <?php echo ($listing['imar_durumu'] ?? '') == 'Konut' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Konut' : 'Residential'; ?></option>
                                <option value="Ticari" <?php echo ($listing['imar_durumu'] ?? '') == 'Ticari' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Ticari' : 'Commercial'; ?></option>
                                <option value="Konut + Ticari" <?php echo ($listing['imar_durumu'] ?? '') == 'Konut + Ticari' ? 'selected' : ''; ?>>
                                    <?php echo $lang == 'tr' ? 'Konut + Ticari' : 'Residential + Commercial'; ?>
                                </option>
                                <option value="Turizm" <?php echo ($listing['imar_durumu'] ?? '') == 'Turizm' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Turizm' : 'Tourism'; ?></option>
                                <option value="Tarım" <?php echo ($listing['imar_durumu'] ?? '') == 'Tarım' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Tarım' : 'Agricultural'; ?></option>
                                <option value="Sanayi" <?php echo ($listing['imar_durumu'] ?? '') == 'Sanayi' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Sanayi' : 'Industrial'; ?></option>
                                <option value="Yok" <?php echo ($listing['imar_durumu'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'None'; ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Tapu Durumu' : 'Deed Status'; ?></label>
                            <select name="tapu_durumu">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Müstakil" <?php echo ($listing['tapu_durumu'] ?? '') == 'Müstakil' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Müstakil' : 'Independent'; ?>
                                </option>
                                <option value="Hisseli" <?php echo ($listing['tapu_durumu'] ?? '') == 'Hisseli' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hisseli' : 'Shared'; ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Ada No' : 'Block No'; ?></label>
                            <input type="text" name="ada_no"
                                value="<?php echo htmlspecialchars($listing['ada_no'] ?? ''); ?>"
                                placeholder="<?php echo $lang == 'tr' ? 'Ada numarası' : 'Block number'; ?>">
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Parsel No' : 'Parcel No'; ?></label>
                            <input type="text" name="parsel_no"
                                value="<?php echo htmlspecialchars($listing['parsel_no'] ?? ''); ?>"
                                placeholder="<?php echo $lang == 'tr' ? 'Parsel numarası' : 'Parcel number'; ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Krediye Uygunluk' : 'Loan Eligible'; ?></label>
                            <select name="krediye_uygun">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Evet" <?php echo ($listing['krediye_uygun'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                <option value="Hayır" <?php echo ($listing['krediye_uygun'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Takas' : 'Exchange'; ?></label>
                            <select name="takas">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Var" <?php echo ($listing['takas'] ?? '') == 'Var' ? 'selected' : ''; ?>>
                                    <?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?>
                                </option>
                                <option value="Yok" <?php echo ($listing['takas'] ?? '') == 'Yok' ? 'selected' : ''; ?>>
                                    <?php echo $lang == 'tr' ? 'Yok' : 'No'; ?>
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Kat Karşılığı' : 'Floor Exchange'; ?></label>
                            <select name="kat_karsiligi">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Evet" <?php echo ($listing['kat_karsiligi'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                <option value="Hayır" <?php echo ($listing['kat_karsiligi'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Bina Özellikleri (Konut ve Ticari) -->
                <div class="form-section conditional-field" id="building-features-section">
                    <div class="form-section-title">4.
                        <?php echo $lang == 'tr' ? 'Bina Özellikleri' : 'Building Features'; ?>
                    </div>

                    <!-- Konut için bina özellikleri -->
                    <div class="conditional-field" id="residential-building-fields">
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Asansör' : 'Elevator'; ?></label>
                                <select name="asansor">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['asansor'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['asansor'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Otopark' : 'Parking'; ?></label>
                                <select name="otopark">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Açık" <?php echo ($listing['otopark'] ?? '') == 'Açık' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Açık' : 'Open'; ?></option>
                                    <option value="Kapalı" <?php echo ($listing['otopark'] ?? '') == 'Kapalı' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Kapalı' : 'Closed'; ?></option>
                                    <option value="Yok" <?php echo ($listing['otopark'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Güvenlik' : 'Security'; ?></label>
                                <select name="guvenlik">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['guvenlik'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['guvenlik'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Site İçinde mi?' : 'In Gated Community?'; ?></label>
                                <select name="site_icinde">
                                    <!-- Price formatting script -->
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['site_icinde'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['site_icinde'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Kamera Sistemi' : 'CCTV System'; ?></label>
                                <select name="kamera_sistemi">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['kamera_sistemi'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['kamera_sistemi'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Kapıcı / Site Görevlisi' : 'Concierge / Security Guard'; ?></label>
                                <select name="kapici">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['kapici'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['kapici'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Isı Yalıtımı' : 'Thermal Insulation'; ?></label>
                                <select name="isi_yalitim">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['isi_yalitim'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['isi_yalitim'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Ticari İşyeri için bina özellikleri -->
                    <div class="conditional-field" id="commercial-building-fields">
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Asansör' : 'Elevator'; ?></label>
                                <select name="asansor">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['asansor'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['asansor'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Otopark' : 'Parking'; ?></label>
                                <select name="otopark">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Açık" <?php echo ($listing['otopark'] ?? '') == 'Açık' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Açık' : 'Open'; ?></option>
                                    <option value="Kapalı" <?php echo ($listing['otopark'] ?? '') == 'Kapalı' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Kapalı' : 'Closed'; ?></option>
                                    <option value="Yok" <?php echo ($listing['otopark'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Güvenlik' : 'Security'; ?></label>
                                <select name="guvenlik">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['guvenlik'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['guvenlik'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Kamera Sistemi' : 'CCTV System'; ?></label>
                                <select name="kamera_sistemi">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['kamera_sistemi'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['kamera_sistemi'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Yük Asansörü' : 'Freight Elevator'; ?></label>
                                <select name="yuk_asansor">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['yuk_asansor'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['yuk_asansor'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Depo Alanı' : 'Storage Area'; ?></label>
                                <select name="depo_alani">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['depo_alani'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['depo_alani'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Yalıtım' : 'Insulation'; ?></label>
                                <select name="yalitim">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Var" <?php echo ($listing['yalitim'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                    <option value="Yok" <?php echo ($listing['yalitim'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Site Özellikleri (Sadece Site İçindeki Konutlar için) -->
                <div class="form-section conditional-field" id="site-features-section">
                    <div class="form-section-title"><?php echo $lang == 'tr' ? 'Site Özellikleri' : 'Site Features'; ?>
                    </div>
                    <p style="color: var(--gray-600); margin-bottom: 1.5rem; font-size: 0.9375rem;">
                        <?php echo $lang == 'tr' ? 'Site içindeki mülkler için mevcut özellikleri işaretleyiniz.' : 'Check the available features for properties within the complex.'; ?>
                    </p>

                    <div class="site-features-grid">
                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_guvenlik" id="site_guvenlik" value="1" <?php echo ($listing['site_guvenlik'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_guvenlik">
                                <i class="fas fa-shield-alt"></i>
                                <?php echo $lang == 'tr' ? '24 Saat Güvenlik' : '24 Hour Security'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_spor_salonu" id="site_spor_salonu" value="1" <?php echo ($listing['site_spor_salonu'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_spor_salonu">
                                <i class="fas fa-dumbbell"></i>
                                <?php echo $lang == 'tr' ? 'Spor Salonu' : 'Gym'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_yuzme_havuzu" id="site_yuzme_havuzu" value="1" <?php echo ($listing['site_yuzme_havuzu'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_yuzme_havuzu">
                                <i class="fas fa-swimming-pool"></i>
                                <?php echo $lang == 'tr' ? 'Yüzme Havuzu' : 'Swimming Pool'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_cocuk_parki" id="site_cocuk_parki" value="1" <?php echo ($listing['site_cocuk_parki'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_cocuk_parki">
                                <i class="fas fa-child"></i>
                                <?php echo $lang == 'tr' ? 'Çocuk Parkı' : 'Playground'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_sauna" id="site_sauna" value="1" <?php echo ($listing['site_sauna'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_sauna">
                                <i class="fas fa-hot-tub"></i>
                                <?php echo $lang == 'tr' ? 'Sauna' : 'Sauna'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_turk_hamami" id="site_turk_hamami" value="1" <?php echo ($listing['site_turk_hamami'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_turk_hamami">
                                <i class="fas fa-spa"></i>
                                <?php echo $lang == 'tr' ? 'Türk Hamamı' : 'Turkish Bath'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_jenerator" id="site_jenerator" value="1" <?php echo ($listing['site_jenerator'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_jenerator">
                                <i class="fas fa-bolt"></i>
                                <?php echo $lang == 'tr' ? 'Jeneratör' : 'Generator'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_kapali_otopark" id="site_kapali_otopark" value="1" <?php echo ($listing['site_kapali_otopark'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_kapali_otopark">
                                <i class="fas fa-warehouse"></i>
                                <?php echo $lang == 'tr' ? 'Kapalı Otopark' : 'Indoor Parking'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_acik_otopark" id="site_acik_otopark" value="1" <?php echo ($listing['site_acik_otopark'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_acik_otopark">
                                <i class="fas fa-parking"></i>
                                <?php echo $lang == 'tr' ? 'Açık Otopark' : 'Open Parking'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_tenis_kortu" id="site_tenis_kortu" value="1" <?php echo ($listing['site_tenis_kortu'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_tenis_kortu">
                                <i class="fas fa-table-tennis"></i>
                                <?php echo $lang == 'tr' ? 'Tenis Kortu' : 'Tennis Court'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_basketbol_sahasi" id="site_basketbol_sahasi" value="1"
                                <?php echo ($listing['site_basketbol_sahasi'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_basketbol_sahasi">
                                <i class="fas fa-basketball-ball"></i>
                                <?php echo $lang == 'tr' ? 'Basketbol Sahası' : 'Basketball Court'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_market" id="site_market" value="1" <?php echo ($listing['site_market'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_market">
                                <i class="fas fa-shopping-cart"></i>
                                <?php echo $lang == 'tr' ? 'Market' : 'Market'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_kres" id="site_kres" value="1" <?php echo ($listing['site_kres'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_kres">
                                <i class="fas fa-baby"></i>
                                <?php echo $lang == 'tr' ? 'Kreş' : 'Nursery'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_cafe" id="site_cafe" value="1" <?php echo ($listing['site_cafe'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_cafe">
                                <i class="fas fa-coffee"></i>
                                <?php echo $lang == 'tr' ? 'Cafe' : 'Cafe'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_restorant" id="site_restorant" value="1" <?php echo ($listing['site_restorant'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_restorant">
                                <i class="fas fa-utensils"></i>
                                <?php echo $lang == 'tr' ? 'Restoran' : 'Restaurant'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_kuafor" id="site_kuafor" value="1" <?php echo ($listing['site_kuafor'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_kuafor">
                                <i class="fas fa-cut"></i>
                                <?php echo $lang == 'tr' ? 'Kuaför' : 'Hair Salon'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_toplanti_odasi" id="site_toplanti_odasi" value="1" <?php echo ($listing['site_toplanti_odasi'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_toplanti_odasi">
                                <i class="fas fa-users"></i>
                                <?php echo $lang == 'tr' ? 'Toplantı Odası' : 'Meeting Room'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_bahce" id="site_bahce" value="1" <?php echo ($listing['site_bahce'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_bahce">
                                <i class="fas fa-tree"></i>
                                <?php echo $lang == 'tr' ? 'Bahçe' : 'Garden'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_evcil_hayvan" id="site_evcil_hayvan" value="1" <?php echo ($listing['site_evcil_hayvan'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_evcil_hayvan">
                                <i class="fas fa-paw"></i>
                                <?php echo $lang == 'tr' ? 'Evcil Hayvan İzni' : 'Pet Friendly'; ?>
                            </label>
                        </div>

                        <div class="feature-checkbox">
                            <input type="checkbox" name="site_engelli_erisimi" id="site_engelli_erisimi" value="1" <?php echo ($listing['site_engelli_erisimi'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="site_engelli_erisimi">
                                <i class="fas fa-wheelchair"></i>
                                <?php echo $lang == 'tr' ? 'Engelli Erişimi' : 'Disabled Access'; ?>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Konum Bilgileri -->
                <div class="form-section">
                    <div class="form-section-title">5.
                        <?php echo $lang == 'tr' ? 'Konum Bilgileri' : 'Location Information'; ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'İl' : 'City'; ?></label>
                            <select name="city" id="city-select-admin">
                                <option value=""><?php echo $lang == 'tr' ? 'Şehir Seçiniz' : 'Select City'; ?></option>
                                <?php foreach ($cities as $city_option): ?>
                                    <option value="<?php echo htmlspecialchars($city_option['il_adi']); ?>" <?php echo ($listing['city'] ?? '') == $city_option['il_adi'] ? 'selected' : ''; ?>
                                        data-id="<?php echo $city_option['id']; ?>">
                                        <?php echo htmlspecialchars($city_option['il_adi']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'İlçe' : 'District'; ?></label>
                            <select name="district" id="district-select-admin">
                                <option value=""><?php echo $lang == 'tr' ? 'İlçe Seçiniz' : 'Select District'; ?>
                                </option>
                                <?php foreach ($districts as $district_option): ?>
                                    <option value="<?php echo htmlspecialchars($district_option['ilce_adi']); ?>" <?php echo ($listing['district'] ?? '') == $district_option['ilce_adi'] ? 'selected' : ''; ?>
                                        data-id="<?php echo $district_option['id']; ?>">
                                        <?php echo htmlspecialchars($district_option['ilce_adi']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Mahalle' : 'Neighborhood'; ?></label>
                            <select name="mahalle" id="mahalle-select-admin">
                                <option value="">
                                    <?php echo $lang == 'tr' ? 'Mahalle Seçiniz' : 'Select Neighborhood'; ?>
                                </option>
                                <?php foreach ($neighborhoods as $neighborhood_option): ?>
                                    <option value="<?php echo htmlspecialchars($neighborhood_option['mahalle_adi']); ?>"
                                        <?php echo ($listing['mahalle'] ?? '') == $neighborhood_option['mahalle_adi'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($neighborhood_option['mahalle_adi']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo t('address'); ?></label>
                        <input type="text" name="address"
                            value="<?php echo htmlspecialchars($listing['address'] ?? ''); ?>">
                    </div>

                    <!-- Konut için özel konum alanları -->
                    <div class="conditional-field" id="residential-location-fields">
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Cadde Üzerinde mi?' : 'On Street?'; ?></label>
                                <select name="cadde_uzerinde">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['cadde_uzerinde'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['cadde_uzerinde'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Toplu Ulaşıma Yakın mı?' : 'Near Public Transport?'; ?></label>
                                <select name="toplu_ulasim">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['toplu_ulasim'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['toplu_ulasim'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Okula Yakın mı?' : 'Near School?'; ?></label>
                                <select name="okula_yakin">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['okula_yakin'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['okula_yakin'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Hastaneye Yakın mı?' : 'Near Hospital?'; ?></label>
                                <select name="hastaneye_yakin">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['hastaneye_yakin'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['hastaneye_yakin'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Market / AVM\'ye Yakın mı?' : 'Near Market / Mall?'; ?></label>
                                <select name="market_yakin">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['market_yakin'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['market_yakin'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Merkeze Uzaklık (m)' : 'Distance to Center (m)'; ?></label>
                                <input type="text" name="merkeze_uzaklik"
                                    value="<?php echo htmlspecialchars($listing['merkeze_uzaklik'] ?? ''); ?>"
                                    placeholder="<?php echo $lang == 'tr' ? '500' : '500'; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Ticari İşyeri için özel konum alanları -->
                    <div class="conditional-field" id="commercial-location-fields">
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Cadde Üzerinde mi?' : 'On Street?'; ?></label>
                                <select name="cadde_uzerinde">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['cadde_uzerinde'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['cadde_uzerinde'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Toplu Ulaşıma Yakın mı?' : 'Near Public Transport?'; ?></label>
                                <select name="toplu_ulasim">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['toplu_ulasim'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['toplu_ulasim'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Ana Yola Cephe mi?' : 'Facing Main Road?'; ?></label>
                                <select name="ana_yola_cephe">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['ana_yola_cephe'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['ana_yola_cephe'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Merkeze Yakın mı?' : 'Near Center?'; ?></label>
                                <select name="merkeze_yakin">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['merkeze_yakin'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['merkeze_yakin'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Arsa için özel konum alanları -->
                    <div class="conditional-field" id="land-location-fields">
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Cadde Üzerinde mi?' : 'On Street?'; ?></label>
                                <select name="cadde_uzerinde">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['cadde_uzerinde'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['cadde_uzerinde'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Toplu Ulaşıma Yakın mı?' : 'Near Public Transport?'; ?></label>
                                <select name="toplu_ulasim">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['toplu_ulasim'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['toplu_ulasim'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Merkeze Yakın mı?' : 'Near Center?'; ?></label>
                                <select name="merkeze_yakin">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['merkeze_yakin'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['merkeze_yakin'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Altyapı Özellikleri (Sadece Arsa) -->
                <div class="form-section conditional-field" id="infrastructure-section">
                    <div class="form-section-title">5.
                        <?php echo $lang == 'tr' ? 'Altyapı Özellikleri' : 'Infrastructure Features'; ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Elektrik' : 'Electricity'; ?></label>
                            <select name="elektrik">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Var" <?php echo ($listing['elektrik'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                <option value="Yok" <?php echo ($listing['elektrik'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Su Hattı' : 'Water Line'; ?></label>
                            <select name="su">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Var" <?php echo ($listing['su'] ?? '') == 'Var' ? 'selected' : ''; ?>>
                                    <?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?>
                                </option>
                                <option value="Yok" <?php echo ($listing['su'] ?? '') == 'Yok' ? 'selected' : ''; ?>>
                                    <?php echo $lang == 'tr' ? 'Yok' : 'No'; ?>
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Kanalizasyon' : 'Sewer'; ?></label>
                            <select name="kanalizasyon">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Var" <?php echo ($listing['kanalizasyon'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                <option value="Yok" <?php echo ($listing['kanalizasyon'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Doğalgaz' : 'Natural Gas'; ?></label>
                            <select name="dogalgaz">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Var" <?php echo ($listing['dogalgaz'] ?? '') == 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                <option value="Yok" <?php echo ($listing['dogalgaz'] ?? '') == 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Telefon Hattı' : 'Phone Line'; ?></label>
                            <select name="telefon">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Var" <?php echo ($listing['telefon'] ?? '') == 'Var' ? 'selected' : ''; ?>>
                                    <?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?>
                                </option>
                                <option value="Yok" <?php echo ($listing['telefon'] ?? '') == 'Yok' ? 'selected' : ''; ?>>
                                    <?php echo $lang == 'tr' ? 'Yok' : 'No'; ?>
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Yolu Açılmış mı?' : 'Road Access?'; ?></label>
                            <select name="yolu_acilmis">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Evet" <?php echo ($listing['yolu_acilmis'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                <option value="Hayır" <?php echo ($listing['yolu_acilmis'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Parselli mi?' : 'Parcelled?'; ?></label>
                            <select name="parselli">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Evet" <?php echo ($listing['parselli'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                <option value="Hayır" <?php echo ($listing['parselli'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'İfrazlı mı?' : 'Subdivided?'; ?></label>
                            <select name="ifrazli">
                                <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                <option value="Evet" <?php echo ($listing['ifrazli'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                <option value="Hayır" <?php echo ($listing['ifrazli'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Manzara ve Cephe Özellikleri (Konut) -->
                <div class="form-section conditional-field" id="view-section">
                    <div class="form-section-title">6.
                        <?php echo $lang == 'tr' ? 'Manzara ve Cephe' : 'View and Orientation'; ?>
                    </div>

                    <!-- Konut için manzara -->
                    <div class="conditional-field" id="residential-view-fields">
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Cephe' : 'Orientation'; ?></label>
                                <select name="cephe">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Kuzey" <?php echo ($listing['cephe'] ?? '') == 'Kuzey' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Kuzey' : 'North'; ?></option>
                                    <option value="Güney" <?php echo ($listing['cephe'] ?? '') == 'Güney' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Güney' : 'South'; ?></option>
                                    <option value="Doğu" <?php echo ($listing['cephe'] ?? '') == 'Doğu' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Doğu' : 'East'; ?></option>
                                    <option value="Batı" <?php echo ($listing['cephe'] ?? '') == 'Batı' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Batı' : 'West'; ?></option>
                                    <option value="Güney-Doğu" <?php echo ($listing['cephe'] ?? '') == 'Güney-Doğu' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Güney-Doğu' : 'Southeast'; ?>
                                    </option>
                                    <option value="Güney-Batı" <?php echo ($listing['cephe'] ?? '') == 'Güney-Batı' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Güney-Batı' : 'Southwest'; ?>
                                    </option>
                                    <option value="Kuzey-Doğu" <?php echo ($listing['cephe'] ?? '') == 'Kuzey-Doğu' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Kuzey-Doğu' : 'Northeast'; ?>
                                    </option>
                                    <option value="Kuzey-Batı" <?php echo ($listing['cephe'] ?? '') == 'Kuzey-Batı' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Kuzey-Batı' : 'Northwest'; ?>
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Manzara' : 'View'; ?></label>
                                <input type="text" name="manzara_tipi"
                                    value="<?php echo htmlspecialchars($listing['manzara_tipi'] ?? ''); ?>"
                                    placeholder="<?php echo $lang == 'tr' ? 'Şehir, Doğa, Deniz, Dağ vb.' : 'City, Nature, Sea, Mountain etc.'; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Arsa için manzara -->
                    <div class="conditional-field" id="land-view-fields">
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Doğa Manzaralı mı?' : 'Nature View?'; ?></label>
                                <select name="doga_manzara">
                                    <option value=""><?php echo $lang == 'tr' ? 'Seçiniz' : 'Select'; ?></option>
                                    <option value="Evet" <?php echo ($listing['doga_manzara'] ?? '') == 'Evet' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Evet' : 'Yes'; ?></option>
                                    <option value="Hayır" <?php echo ($listing['doga_manzara'] ?? '') == 'Hayır' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Hayır' : 'No'; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $lang == 'tr' ? 'Manzara Tipi' : 'View Type'; ?></label>
                                <input type="text" name="manzara_tipi"
                                    value="<?php echo htmlspecialchars($listing['manzara_tipi'] ?? ''); ?>"
                                    placeholder="<?php echo $lang == 'tr' ? 'Deniz, Şehir, Dağ vb.' : 'Sea, City, Mountain etc.'; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notlar -->
                <div class="form-section">
                    <div class="form-section-title">7.
                        <?php echo $lang == 'tr' ? 'Notlar veya Ek Bilgi' : 'Notes or Additional Information'; ?>
                    </div>
                    <div class="form-group">
                        <textarea name="notes" rows="5"
                            placeholder="<?php echo $lang == 'tr' ? 'Notlar veya ek bilgi buraya yazılabilir...' : 'Notes or additional information can be written here...'; ?>"><?php echo htmlspecialchars($listing['notes'] ?? ''); ?></textarea>
                    </div>

                </div>

                <!-- Resimler -->
                <div class="form-section">
                    <div class="form-section-title">8. <?php echo t('images'); ?></div>
                    <p style="color: var(--gray-600); margin-bottom: 1rem; font-size: 0.9375rem;">
                        <?php echo $lang == 'tr' ? 'Maksimum 30 resim yükleyebilirsiniz. Resimleri sürükleyip bırakabilir veya tıklayarak seçebilirsiniz. Bir resme tıklayarak ana resim olarak belirleyebilirsiniz.' : 'You can upload up to 30 images. Drag and drop images or click to select. Click on an image to set it as the main image.'; ?>
                    </p>

                    <!-- Büyük Drop Zone -->
                    <div class="multi-upload-dropzone" id="multi-dropzone">
                        <div class="dropzone-content">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <h3><?php echo $lang == 'tr' ? 'Resimleri Buraya Sürükleyip Bırakın' : 'Drag & Drop Images Here'; ?>
                            </h3>
                            <p><?php echo $lang == 'tr' ? 'veya' : 'or'; ?></p>
                            <button type="button" class="btn btn-primary" id="browse-btn">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" style="margin-right: 8px;">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <?php echo $lang == 'tr' ? 'Dosya Seç' : 'Browse Files'; ?>
                            </button>
                            <p class="upload-hint">
                                <?php echo $lang == 'tr' ? 'PNG, JPG, JPEG, WEBP • Maks. 30 resim • Maks. 5MB/resim' : 'PNG, JPG, JPEG, WEBP • Max 30 images • Max 5MB each'; ?>
                            </p>
                        </div>
                        <input type="file" id="multi-image-input" accept="image/*" multiple style="display: none;">
                    </div>

                    <!-- Yüklenen Resimler Grid -->
                    <div class="uploaded-images-container" id="uploaded-images-container" style="display: none;">
                        <div class="uploaded-images-header">
                            <h4>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" style="margin-right: 8px;">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                                <?php echo $lang == 'tr' ? 'Yüklenen Resimler' : 'Uploaded Images'; ?> (<span
                                    id="image-count">0</span>/30)
                            </h4>
                            <button type="button" class="btn btn-secondary btn-sm" id="add-more-btn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" style="margin-right: 4px;">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <?php echo $lang == 'tr' ? 'Daha Fazla Ekle' : 'Add More'; ?>
                            </button>
                        </div>
                        <p class="main-image-hint">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" style="margin-right: 4px;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            <?php echo $lang == 'tr' ? 'Ana resmi belirlemek için bir resme tıklayın. Yıldızlı resim ana resim olarak gösterilecektir.' : 'Click on an image to set it as the main image. The starred image will be shown as the cover.'; ?>
                        </p>
                        <div class="uploaded-images-grid" id="uploaded-images-grid">
                            <!-- Dynamic images will be inserted here -->
                        </div>
                    </div>

                    <!-- Hidden inputs for storing image data -->
                    <input type="hidden" name="images_data" id="images-data-input" value="">
                    <input type="hidden" name="main_image_index" id="main-image-index" value="0">

                    <?php if ($is_edit): ?>
                        <!-- Existing images for edit mode -->
                        <?php
                        $existing_images = [];
                        for ($i = 1; $i <= 5; $i++) {
                            if (!empty($listing['image' . $i])) {
                                $existing_images[] = $listing['image' . $i];
                            }
                        }
                        ?>
                        <input type="hidden" id="existing-images-json" value='<?php echo json_encode($existing_images); ?>'>
                    <?php endif; ?>

                    <!-- Video Upload Section -->
                    <div class="form-section" style="margin-top: 2rem;">
                        <h3 style="margin-bottom: 1rem; font-size: 1.1rem;">
                            <?php echo $lang == 'tr' ? 'Video' : 'Video'; ?>
                        </h3>
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'İlan Videosu' : 'Property Video'; ?>
                                (<?php echo $lang == 'tr' ? 'İsteğe Bağlı' : 'Optional'; ?>)</label>
                            <?php if (!empty($listing['video'])): ?>
                                <div style="margin-bottom: 1rem;">
                                    <video width="400" controls style="border-radius: 8px;">
                                        <source src="/uploads/<?php echo htmlspecialchars($listing['video']); ?>"
                                            type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div style="margin-top: 0.5rem;">
                                        <label>
                                            <input type="checkbox" name="delete_video" value="1">
                                            <?php echo $lang == 'tr' ? 'Mevcut videoyu sil' : 'Delete existing video'; ?>
                                        </label>
                                    </div>
                                    <input type="hidden" name="existing_video"
                                        value="<?php echo htmlspecialchars($listing['video']); ?>">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="video" accept="video/mp4,video/webm,video/ogg"
                                class="form-control">
                            <small style="color: #666; display: block; margin-top: 0.5rem;">
                                <?php echo $lang == 'tr' ? 'Desteklenen formatlar: MP4, WebM, OGG. Maksimum boyut: 50MB' : 'Supported formats: MP4, WebM, OGG. Maximum size: 50MB'; ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- SEO Ayarları -->
                <div class="form-section">
                    <div class="form-section-title">
                        <span class="icon">🔍</span>
                        <?php echo $lang == 'tr' ? 'SEO Ayarları' : 'SEO Settings'; ?>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label><?php echo $lang == 'tr' ? 'URL Yapısı (Slug)' : 'URL Slug'; ?>
                                <small>(<?php echo $lang == 'tr' ? 'Boş bırakılırsa başlıktan otomatik oluşturulur' : 'Autogenerated from title if left empty'; ?>)</small></label>
                            <input type="text" name="slug" id="slug" value="<?php echo $listing['slug'] ?? ''; ?>"
                                placeholder="ornek-ilan-basligi">
                        </div>

                        <div class="form-group">
                            <label>Meta Title
                                <small>(<?php echo $lang == 'tr' ? 'Tarayıcı başlığı' : 'Browser title'; ?>)</small></label>
                            <input type="text" name="meta_title" value="<?php echo $listing['meta_title'] ?? ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Meta Description
                                <small>(<?php echo $lang == 'tr' ? 'Arama motoru açıklaması' : 'Search engine description'; ?>)</small></label>
                            <textarea name="meta_description"
                                rows="3"><?php echo $listing['meta_description'] ?? ''; ?></textarea>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary"><?php echo t('save'); ?></button>
                    <a href="ilanlar.php" class="btn btn-secondary"><?php echo t('cancel'); ?></a>
                </div>
            </form>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

    <script src="../assets/js/main.js"></script>
    <style>
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-lg);
            color: white;
            font-weight: 500;
            box-shadow: var(--shadow-xl);
            z-index: 10000;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
            max-width: 400px;
            word-wrap: break-word;
        }

        .toast-show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .toast-error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
    </style>
    <script>
        // Mülk tipine göre alanları göster/gizle
        const propertyTypeSelect = document.getElementById('property_type');
        const landPriceFields = document.getElementById('land-price-fields');
        const residentialFields = document.getElementById('residential-fields');
        const commercialFields = document.getElementById('commercial-fields');
        const landDetailsSection = document.getElementById('land-details-section');
        const infrastructureSection = document.getElementById('infrastructure-section');
        const buildingFeaturesSection = document.getElementById('building-features-section');
        const residentialBuildingFields = document.getElementById('residential-building-fields');
        const commercialBuildingFields = document.getElementById('commercial-building-fields');
        const viewSection = document.getElementById('view-section');
        const residentialViewFields = document.getElementById('residential-view-fields');
        const landViewFields = document.getElementById('land-view-fields');
        const landLocationFields = document.getElementById('land-location-fields');
        const residentialLocationFields = document.getElementById('residential-location-fields');
        const commercialLocationFields = document.getElementById('commercial-location-fields');
        const siteFeaturesSection = document.getElementById('site-features-section');
        const listingTypeSelect = document.getElementById('listing_type');

        const landArea = document.getElementById('land_area');
        const pricePerSqm = document.getElementById('price_per_sqm');
        const totalPrice = document.getElementById('total_price');

        // İlan Sahibi ve Komisyon alanları
        const ilanSahibiSelect = document.getElementById('ilan_sahibi_turu');
        const komisyonField = document.getElementById('komisyon-field');

        // Komisyon alanını göster/gizle
        function toggleKomisyonField() {
            if (ilanSahibiSelect && komisyonField) {
                if (ilanSahibiSelect.value === 'Emlakçı') {
                    komisyonField.classList.remove('hidden');
                } else {
                    komisyonField.classList.add('hidden');
                    // Değeri temizle
                    const komisyonInput = document.getElementById('komisyon_yuzdesi');
                    if (komisyonInput) komisyonInput.value = '';
                }
            }
        }

        // İlan Sahibi değiştiğinde komisyon alanını toggle et
        if (ilanSahibiSelect) {
            ilanSahibiSelect.addEventListener('change', toggleKomisyonField);
            // İlk yüklemede kontrol et
            toggleKomisyonField();
        }

        // Function to filter property types based on listing type
        function filterPropertyTypes() {
            const listingType = listingTypeSelect ? listingTypeSelect.value : 'satilik';
            const isRental = listingType === 'kiralik';

            if (propertyTypeSelect) {
                const options = propertyTypeSelect.querySelectorAll('option');
                options.forEach(option => {
                    if (option.hasAttribute('data-hide-for-rent') && isRental) {
                        option.style.display = 'none';
                        // If currently selected option should be hidden, reset  
                        if (option.selected) {
                            propertyTypeSelect.value = '';
                        }
                    } else {
                        option.style.display = '';
                    }
                });
            }
        }

        // Listen for listing type changes
        if (listingTypeSelect) {
            listingTypeSelect.addEventListener('change', filterPropertyTypes);
            // Run on page load
            filterPropertyTypes();
        }


        function toggleFields() {
            const propertyType = propertyTypeSelect.value;
            const isLand = propertyType === 'arsa' || propertyType === 'tarla';
            const isResidential = propertyType === 'ev' || propertyType === 'daire' || propertyType === 'villa';
            const isCommercial = propertyType === 'dukkan';

            // Fiyat alanları
            if (isLand) {
                landPriceFields.classList.remove('hidden');
                residentialFields.classList.add('hidden');
                commercialFields.classList.add('hidden');
                landDetailsSection.classList.remove('hidden');
                infrastructureSection.classList.remove('hidden');
                buildingFeaturesSection.classList.add('hidden');
                viewSection.classList.remove('hidden');
                landViewFields.classList.remove('hidden');
                residentialViewFields.classList.add('hidden');
                landLocationFields.classList.remove('hidden');
                residentialLocationFields.classList.add('hidden');
                commercialLocationFields.classList.add('hidden');
                if (siteFeaturesSection) siteFeaturesSection.classList.add('hidden');

                // Arsa için alan zorunlu
                if (landArea) landArea.required = true;
                if (pricePerSqm) pricePerSqm.required = true;

                // Tarla ise imar durumunu gizle
                const imarDurumuWrapper = document.getElementById('imar-durumu-wrapper');
                if (imarDurumuWrapper) {
                    if (propertyType === 'tarla') {
                        imarDurumuWrapper.classList.add('hidden');
                    } else {
                        imarDurumuWrapper.classList.remove('hidden');
                    }
                }
            } else if (isResidential) {
                landPriceFields.classList.add('hidden');
                residentialFields.classList.remove('hidden');
                commercialFields.classList.add('hidden');
                landDetailsSection.classList.add('hidden');
                infrastructureSection.classList.add('hidden');
                buildingFeaturesSection.classList.remove('hidden');
                residentialBuildingFields.classList.remove('hidden');
                commercialBuildingFields.classList.add('hidden');
                viewSection.classList.remove('hidden');
                residentialViewFields.classList.remove('hidden');
                landViewFields.classList.add('hidden');
                landLocationFields.classList.add('hidden');
                residentialLocationFields.classList.remove('hidden');
                commercialLocationFields.classList.add('hidden');
                if (siteFeaturesSection) siteFeaturesSection.classList.remove('hidden');

                // Konut için alanlar
                if (landArea) landArea.required = false;
                if (pricePerSqm) pricePerSqm.required = false;
            } else if (isCommercial) {
                landPriceFields.classList.add('hidden');
                residentialFields.classList.add('hidden');
                commercialFields.classList.remove('hidden');
                landDetailsSection.classList.add('hidden');
                infrastructureSection.classList.add('hidden');
                buildingFeaturesSection.classList.remove('hidden');
                residentialBuildingFields.classList.add('hidden');
                commercialBuildingFields.classList.remove('hidden');
                viewSection.classList.add('hidden');
                landViewFields.classList.add('hidden');
                residentialViewFields.classList.add('hidden');
                landLocationFields.classList.add('hidden');
                residentialLocationFields.classList.add('hidden');
                commercialLocationFields.classList.remove('hidden');
                if (siteFeaturesSection) siteFeaturesSection.classList.add('hidden');

                // Ticari için alanlar
                if (landArea) landArea.required = false;
                if (pricePerSqm) pricePerSqm.required = false;
            } else {
                // Varsayılan - tüm özel alanları gizle
                landPriceFields.classList.add('hidden');
                residentialFields.classList.add('hidden');
                commercialFields.classList.add('hidden');
                landDetailsSection.classList.add('hidden');
                infrastructureSection.classList.add('hidden');
                buildingFeaturesSection.classList.add('hidden');
                viewSection.classList.add('hidden');
                landLocationFields.classList.add('hidden');
                residentialLocationFields.classList.add('hidden');
                commercialLocationFields.classList.add('hidden');
                if (siteFeaturesSection) siteFeaturesSection.classList.add('hidden');
            }
        }

        // m² fiyatı hesaplama
        function calculateTotalPrice() {
            if (landArea && pricePerSqm && landArea.value && pricePerSqm.value) {
                // Parse values removing dots
                const area = parseFloat(landArea.value) || 0;
                const priceValueStr = pricePerSqm.value.replace(/\./g, '').replace(',', '.');
                const pricePerM2 = parseFloat(priceValueStr) || 0;
                const total = area * pricePerM2;

                if (total > 0) {
                    // Format total with dots
                    totalPrice.value = Math.floor(total).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    document.getElementById('price-calc-info').textContent =
                        'Hesaplanan Toplam Fiyat: ' + area.toLocaleString('tr-TR') + ' m² × ' +
                        pricePerM2.toLocaleString('tr-TR') + ' TL/m² = ' +
                        total.toLocaleString('tr-TR', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' TL';
                }
            }
        }

        // Format number inputs with thousands separator
        function setupPriceFormatting(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;

            input.addEventListener('input', function (e) {
                // Remove dots to work with raw value
                let val = input.value.replace(/\./g, '');
                // Allow digits and comma only
                val = val.replace(/[^0-9,]/g, '');

                // Split by comma to handle integer and decimal parts separately
                const parts = val.split(',');

                // Format integer part with dots
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                // Reassemble
                if (parts.length > 1) {
                    // Start of decimal part - limit to one comma
                    // We join the rest to avoid losing data if user types fast, but effectively only first comma counts as separator logic usually handling it
                    // Better approach: take integer part + comma + rest of string (digits)
                    input.value = parts[0] + ',' + parts.slice(1).join('');
                } else {
                    input.value = parts[0];
                }
            });
        }

        setupPriceFormatting('total_price');
        setupPriceFormatting('price_per_sqm');

        // İlk yüklemede alanları ayarla
        toggleFields();

        // Mülk tipi değiştiğinde
        propertyTypeSelect.addEventListener('change', toggleFields);

        // m² ve m² fiyatı değiştiğinde toplam fiyatı hesapla
        if (landArea) landArea.addEventListener('input', calculateTotalPrice);
        if (pricePerSqm) pricePerSqm.addEventListener('input', calculateTotalPrice);

        // İlk yüklemede hesapla (varsa değerler)
        calculateTotalPrice();
    </script>

    <script>
        // Slug generation
        const titleTrInput = document.getElementById('title_tr');
        const slugInput = document.getElementById('slug');

        if (titleTrInput && slugInput) {
            titleTrInput.addEventListener('blur', function () {
                if (!slugInput.value) {
                    const slug = createSlug(this.value);
                    slugInput.value = slug;
                }
            });
        }

        function createSlug(text) {
            const trMap = {
                'ç': 'c', 'ğ': 'g', 'ı': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u',
                'Ç': 'c', 'Ğ': 'g', 'İ': 'i', 'Ö': 'o', 'Ş': 's', 'Ü': 'u'
            };

            return text.split('').map(char => trMap[char] || char).join('')
                .toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
        }
    </script>




    <script>
        // Form elementlerini seç
        const listingForm = document.getElementById('listingForm');
        const submitButton = listingForm.querySelector('button[type="submit"]');
        const cancelButton = listingForm.querySelector('a.btn-secondary');

        // Toast notification fonksiyonu
        function showToast(message, type = 'success') {
            // Toast elementi oluştur veya bul
            let toast = document.getElementById('toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'toast';
                document.body.appendChild(toast);
            }

            toast.textContent = message;
            toast.className = `toast toast-${type} toast-show`;

            setTimeout(() => {
                toast.classList.remove('toast-show');
            }, 3000);
        }

        // ============================================
        // YENİ ÇOKLU RESİM YÜKLEME SİSTEMİ
        // ============================================

        const multiDropzone = document.getElementById('multi-dropzone');
        const multiImageInput = document.getElementById('multi-image-input');
        const browseBtn = document.getElementById('browse-btn');
        const addMoreBtn = document.getElementById('add-more-btn');
        const uploadedImagesContainer = document.getElementById('uploaded-images-container');
        const uploadedImagesGrid = document.getElementById('uploaded-images-grid');
        const imageCountSpan = document.getElementById('image-count');
        const imagesDataInput = document.getElementById('images-data-input');
        const mainImageIndexInput = document.getElementById('main-image-index');
        const existingImagesJson = document.getElementById('existing-images-json');

        const MAX_IMAGES = 30;
        const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
        const lang = '<?php echo $lang; ?>';

        // Yüklenen resimler dizisi
        let uploadedImages = [];
        let mainImageIndex = 0;

        // Mevcut resimleri yükle (edit modunda)
        if (existingImagesJson && existingImagesJson.value) {
            try {
                const existingImages = JSON.parse(existingImagesJson.value);
                existingImages.forEach((imagePath, index) => {
                    uploadedImages.push({
                        id: 'existing_' + index,
                        file: null,
                        preview: '/uploads/' + imagePath,
                        name: imagePath,
                        isExisting: true
                    });
                });
                if (uploadedImages.length > 0) {
                    renderUploadedImages();
                    updateUI();
                }
            } catch (e) {
                console.error('Error parsing existing images:', e);
            }
        }

        // Drag & Drop olayları
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            multiDropzone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            multiDropzone.addEventListener(eventName, () => {
                multiDropzone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            multiDropzone.addEventListener(eventName, () => {
                multiDropzone.classList.remove('dragover');
            }, false);
        });

        multiDropzone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        // Browse butonu
        browseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            multiImageInput.click();
        });

        // Dropzone'a tıklama
        multiDropzone.addEventListener('click', () => {
            multiImageInput.click();
        });

        // Daha fazla ekle butonu
        if (addMoreBtn) {
            addMoreBtn.addEventListener('click', () => {
                multiImageInput.click();
            });
        }

        // File input değişikliği
        multiImageInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
            multiImageInput.value = ''; // Reset input for re-selection
        });

        // Dosyaları işle
        function handleFiles(files) {
            const validFiles = [];

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                // Limit kontrolü
                if (uploadedImages.length + validFiles.length >= MAX_IMAGES) {
                    showToast(lang === 'tr'
                        ? `Maksimum ${MAX_IMAGES} resim yükleyebilirsiniz.`
                        : `You can upload maximum ${MAX_IMAGES} images.`, 'error');
                    break;
                }

                // Tip kontrolü
                if (!file.type.startsWith('image/')) {
                    showToast(lang === 'tr'
                        ? `"${file.name}" geçerli bir resim dosyası değil.`
                        : `"${file.name}" is not a valid image file.`, 'error');
                    continue;
                }

                // Boyut kontrolü
                if (file.size > MAX_FILE_SIZE) {
                    showToast(lang === 'tr'
                        ? `"${file.name}" çok büyük. Maksimum 5MB.`
                        : `"${file.name}" is too large. Maximum 5MB.`, 'error');
                    continue;
                }

                validFiles.push(file);
            }

            // Geçerli dosyaları işle
            validFiles.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    uploadedImages.push({
                        id: 'new_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                        file: file,
                        preview: e.target.result,
                        name: file.name,
                        isExisting: false
                    });
                    renderUploadedImages();
                    updateUI();
                };
                reader.readAsDataURL(file);
            });
        }

        // Yüklenen resimleri render et
        function renderUploadedImages() {
            uploadedImagesGrid.innerHTML = '';

            uploadedImages.forEach((image, index) => {
                const isMain = index === mainImageIndex;

                const item = document.createElement('div');
                item.className = `uploaded-image-item ${isMain ? 'main-image' : ''}`;
                item.dataset.index = index;

                item.innerHTML = `
                    <img src="${image.preview}" alt="${image.name}">
                    ${isMain ? `
                        <div class="main-badge">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            ${lang === 'tr' ? 'Ana Resim' : 'Main'}
                        </div>
                    ` : ''}
                    <button type="button" class="btn-remove" title="${lang === 'tr' ? 'Kaldır' : 'Remove'}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    ${!isMain ? `
                        <button type="button" class="set-main-btn">
                            ${lang === 'tr' ? '★ Ana Resim Yap' : '★ Set as Main'}
                        </button>
                    ` : ''}
                `;

                // Silme butonu
                item.querySelector('.btn-remove').addEventListener('click', (e) => {
                    e.stopPropagation();
                    removeUploadedImage(index);
                });

                // Ana resim yapma butonu
                const setMainBtn = item.querySelector('.set-main-btn');
                if (setMainBtn) {
                    setMainBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        setMainImage(index);
                    });
                }

                // Resme tıklayarak ana resim yapma
                item.addEventListener('click', () => {
                    if (!item.classList.contains('main-image')) {
                        setMainImage(index);
                    }
                });

                uploadedImagesGrid.appendChild(item);
            });

            // Form verilerini güncelle
            updateFormData();
        }

        // Resim silme
        function removeUploadedImage(index) {
            uploadedImages.splice(index, 1);

            // Ana resim silinmişse, ilk resmi ana yap
            if (mainImageIndex === index) {
                mainImageIndex = 0;
            } else if (mainImageIndex > index) {
                mainImageIndex--;
            }

            renderUploadedImages();
            updateUI();
        }

        // Ana resim belirleme
        function setMainImage(index) {
            mainImageIndex = index;
            renderUploadedImages();
            showToast(lang === 'tr' ? 'Ana resim değiştirildi.' : 'Main image changed.', 'success');
        }

        // UI güncelleme
        function updateUI() {
            const hasImages = uploadedImages.length > 0;

            // Container görünürlüğü
            uploadedImagesContainer.style.display = hasImages ? 'block' : 'none';

            // Sayaç güncelleme
            imageCountSpan.textContent = uploadedImages.length;

            // Dropzone görünürlüğü
            if (uploadedImages.length >= MAX_IMAGES) {
                multiDropzone.style.display = 'none';
            } else {
                multiDropzone.style.display = 'flex';
            }
        }

        // Form verilerini güncelle
        function updateFormData() {
            // Resimlerin bilgilerini JSON olarak kaydet
            const imageData = uploadedImages.map((img, index) => ({
                id: img.id,
                name: img.name,
                isExisting: img.isExisting,
                isMain: index === mainImageIndex
            }));

            imagesDataInput.value = JSON.stringify(imageData);
            mainImageIndexInput.value = mainImageIndex;
        }

        // Form gönderiminden önce resimleri ekle
        const originalFormSubmit = listingForm.onsubmit;

        listingForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(listingForm);

            // Edit modunda ID ekle
            <?php if ($is_edit): ?>
                formData.append('id', <?php echo $id; ?>);
            <?php endif; ?>

            // Yüklenen resim dosyalarını ekle
            uploadedImages.forEach((image, index) => {
                if (!image.isExisting && image.file) {
                    formData.append('uploaded_images[]', image.file);
                    formData.append('image_ids[]', image.id);
                }
            });

            // Ana resim indeksi
            formData.append('main_image_index', mainImageIndex);

            // Mevcut resimlerin listesi
            const existingImagesList = uploadedImages
                .filter(img => img.isExisting)
                .map(img => img.name);
            formData.append('existing_images_list', JSON.stringify(existingImagesList));

            // Butonu devre dışı bırak
            const originalText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = lang === 'tr'
                ? '<span class="loading-spinner"></span> Kaydediliyor...'
                : '<span class="loading-spinner"></span> Saving...';

            try {
                console.log('Starting request to process_listing.php...');
                const response = await fetch('admin/process_listing.php', {
                    method: 'POST',
                    body: formData
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', [...response.headers.entries()]);

                const rawText = await response.text();
                console.log('Raw response text:', rawText); // CRITICAL: This allows us to see the HTML error

                let data;
                try {
                    data = JSON.parse(rawText);
                } catch (e) {
                    console.error('JSON Parse Error:', e);
                    // Show the first 200 chars of the invalid response in the toast for immediate visibility
                    const errorPreview = rawText.substring(0, 200).replace(/</g, '&lt;');
                    showToast('Server returned Error: ' + response.status + ' (See Console)', 'error');

                    // Allow the user to see the HTML if it's an error page
                    if (rawText.toLowerCase().includes('duplicate entry')) {
                        showToast('Database Error: Duplicate Entry', 'error');
                    }

                    // Re-enable button
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                    return;
                }

                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || 'ilanlar.php';
                    }, 2000);
                } else {
                    showToast(data.message || (lang === 'tr' ? 'Bir hata oluştu' : 'An error occurred'), 'error');

                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000);
                    } else {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showToast(lang === 'tr' ? 'Bir hata oluştu' : 'An error occurred', 'error');
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });
    </script>
    <script src="/assets/js/location-filter.js"></script>
</body>

</html>