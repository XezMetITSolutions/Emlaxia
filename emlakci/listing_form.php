<?php
/**
 * Emlakçı - İlan Ekleme/Düzenleme Formu
 */
require_once '../config.php';
require_once '../includes/auth.php';
requireEmlakci();

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? 0;
$is_edit = false;
$listing = [];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM listings WHERE id = :id AND user_id = :uid AND user_type = 'emlakci'");
    $stmt->execute([':id' => $id, ':uid' => $user_id]);
    $listing = $stmt->fetch();
    if (!$listing) {
        $_SESSION['error_message'] = 'İlan bulunamadı veya erişim yetkiniz yok.';
        header('Location: /emlakci/ilanlar');
        exit;
    }
    $is_edit = true;
}

$stmt = $pdo->query("SELECT id, il_adi FROM iller ORDER BY il_adi");
$cities = $stmt->fetchAll();

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
    $lang = $_SESSION['lang'] ?? 'tr';
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'İlan Düzenle' : 'Yeni İlan Ekle'; ?> - Emlaxia</title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f1f5f9;
            margin: 0;
        }

        .page-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1.5rem;
        }

        .form-section {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        .form-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            color: #374151;
            margin-bottom: 0.4rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.2s;
            background: #f8fafc;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1e88e5;
            background: white;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn-submit {
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(30, 136, 229, 0.3);
        }

        .btn-cancel {
            padding: 0.75rem 1.5rem;
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
        }

        .info-banner {
            background: linear-gradient(135deg, rgba(30, 136, 229, 0.05), rgba(30, 136, 229, 0.1));
            border: 1px solid rgba(30, 136, 229, 0.2);
            border-left: 4px solid #1e88e5;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            color: #1e40af;
        }

        /* --- Premium Multi Image & Video Upload Styles --- */
        .multi-upload-dropzone {
            border: 3px dashed var(--gray-300);
            border-radius: var(--radius-2xl);
            padding: 4rem 2rem;
            text-align: center;
            background: rgba(255, 255, 255, 0.5);
            transition: var(--transition);
            cursor: pointer;
            min-height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .multi-upload-dropzone:hover {
            border-color: var(--primary-color);
            background: rgba(15, 18, 61, 0.02);
            transform: translateY(-2px);
        }

        .multi-upload-dropzone.dragover {
            border-color: var(--success-color);
            background: rgba(16, 185, 129, 0.05);
            transform: scale(1.02);
        }

        .dropzone-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            z-index: 1;
        }

        .dropzone-content i {
            font-size: 4rem;
            color: var(--primary-color);
            opacity: 0.8;
            transition: var(--transition);
        }

        .multi-upload-dropzone:hover .dropzone-content i {
            transform: scale(1.1) rotate(5deg);
        }

        .uploaded-images-container {
            margin-top: 2.5rem;
            background: white;
            border-radius: var(--radius-2xl);
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
        }

        .uploaded-images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1.5rem;
        }

        .uploaded-image-item {
            position: relative;
            border-radius: var(--radius-xl);
            overflow: hidden;
            aspect-ratio: 4/3;
            background: var(--gray-100);
            cursor: move;
            transition: var(--transition);
            border: 3px solid transparent;
            box-shadow: var(--shadow-md);
        }

        .uploaded-image-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .uploaded-image-item.main-image {
            border-color: var(--warning-color);
            transform: scale(1.02);
        }

        .uploaded-image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .uploaded-image-item .btn-remove {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--danger-color);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            z-index: 10;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
        }

        .uploaded-image-item .btn-remove:hover {
            transform: scale(1.1) rotate(90deg);
            background: #b91c1c;
        }

        .main-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: var(--warning-color);
            color: white;
            padding: 4px 10px;
            border-radius: var(--radius-full);
            font-size: 0.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: var(--shadow-md);
            z-index: 5;
        }

        .set-main-btn {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(15, 18, 61, 0.85);
            color: white;
            border: none;
            padding: 8px 0;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            opacity: 0;
            transition: var(--transition);
            backdrop-filter: blur(4px);
        }

        .uploaded-image-item:hover .set-main-btn {
            opacity: 1;
        }

        /* Video Section Styles */
        .video-upload-section {
            margin-top: 3rem;
            padding-top: 3rem;
            border-top: 2px dashed var(--gray-200);
        }

        .video-preview-container {
            margin-top: 1.5rem;
            background: var(--gray-900);
            border-radius: var(--radius-xl);
            overflow: hidden;
            position: relative;
            max-width: 500px;
            aspect-ratio: 16/9;
            box-shadow: var(--shadow-2xl);
        }

        .video-preview-container video {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .btn-remove-video {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--danger-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: var(--radius-lg);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            z-index: 10;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-remove-video:hover {
            background: #b91c1c;
            transform: scale(1.05);
        }

        .btn-add-more {
            padding: 0.5rem 1rem;
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-add-more:hover { background: #e2e8f0; }

        .conditional-field {
            display: none;
        }

        .conditional-field.visible {
            display: block;
        }

        /* Site Özellikleri Grid */
        .site-features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .feature-checkbox {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .feature-checkbox:hover {
            border-color: #1e88e5;
            background: rgba(30, 136, 229, 0.05);
        }

        .feature-checkbox input[type="checkbox"] {
            display: none;
        }

        .feature-checkbox label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            font-size: 0.9rem;
            color: #374151;
            margin: 0;
            width: 100%;
        }

        .feature-checkbox label i {
            font-size: 1.1rem;
            color: #94a3b8;
            width: 20px;
            text-align: center;
        }

        .feature-checkbox input[type="checkbox"]:checked+label {
            color: #1e88e5;
            font-weight: 600;
        }

        .feature-checkbox input[type="checkbox"]:checked+label i {
            color: #1e88e5;
        }

        .feature-checkbox:has(input[type="checkbox"]:checked) {
            border-color: #1e88e5;
            background: rgba(30, 136, 229, 0.1);
        }

        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 0.75rem;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 0.8rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .checkbox-item:hover {
            border-color: #1e88e5;
        }

        .checkbox-item input:checked+span {
            color: #1e88e5;
            font-weight: 600;
        }

        .saving-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .saving-overlay.active {
            display: flex;
        }

        .saving-box {
            background: white;
            border-radius: 16px;
            padding: 2rem 3rem;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
        }

        .saving-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e2e8f0;
            border-top-color: #1e88e5;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {

            .form-row,
            .form-row-3 {
                grid-template-columns: 1fr;
            }

            .page-container {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/emlakci_header.php'; ?>

    <div class="page-container">
        <h1 class="page-title"><?php echo $is_edit ? '✏️ İlan Düzenle' : '➕ Yeni İlan Ekle'; ?></h1>

        <div class="info-banner">
            ℹ️ İlanınız onay sürecinden geçecektir. Onaylandıktan sonra sitede yayınlanacaktır.
        </div>

        <form id="listingForm" enctype="multipart/form-data">
            <!-- Üst İşlem Butonları -->
            <div class="form-actions" style="margin-bottom: 2rem; padding: 1.5rem; background: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; font-size: 1rem; color: #64748b;">Hızlı İşlem:</h2>
                <div style="display: flex; gap: 1rem;">
                    <a href="/emlakci/ilanlar" class="btn-cancel">← İptal</a>
                    <button type="submit" class="btn-submit">
                        <?php echo $is_edit ? '💾 Güncelle' : '📤 İlanı Yayınla'; ?>
                    </button>
                </div>
            </div>

            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
            <?php endif; ?>
            <input type="hidden" name="panel_type" value="emlakci">

            <!-- 1. Temel Bilgiler -->
            <div class="form-section">
                <div class="form-section-title">📋 Temel Bilgiler</div>

                <div class="form-row">
                    <div class="form-group">
                        <label>İlan Tipi *</label>
                        <select name="listing_type" required>
                            <option value="satilik" <?php echo ($listing['listing_type'] ?? 'satilik') === 'satilik' ? 'selected' : ''; ?>>Satılık</option>
                            <option value="kiralik" <?php echo ($listing['listing_type'] ?? '') === 'kiralik' ? 'selected' : ''; ?>>Kiralık</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Mülk Tipi *</label>
                        <select name="property_type" id="propertyType" required onchange="updateConditionalFields()">
                            <option value="">Seçiniz</option>
                            <option value="ev" <?php echo ($listing['property_type'] ?? '') === 'ev' ? 'selected' : ''; ?>>Ev</option>
                            <option value="daire" <?php echo ($listing['property_type'] ?? '') === 'daire' ? 'selected' : ''; ?>>Daire</option>
                            <option value="villa" <?php echo ($listing['property_type'] ?? '') === 'villa' ? 'selected' : ''; ?>>Villa</option>
                            <option value="arsa" <?php echo ($listing['property_type'] ?? '') === 'arsa' ? 'selected' : ''; ?>>Arsa</option>
                            <option value="tarla" <?php echo ($listing['property_type'] ?? '') === 'tarla' ? 'selected' : ''; ?>>Tarla</option>
                            <option value="dukkan" <?php echo ($listing['property_type'] ?? '') === 'dukkan' ? 'selected' : ''; ?>>Dükkan</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Başlık (Türkçe) *</label>
                        <input type="text" name="title_tr" value="<?php echo htmlspecialchars($listing['title_tr'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Başlık (İngilizce)</label>
                        <input type="text" name="title_en" value="<?php echo htmlspecialchars($listing['title_en'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Açıklama (Türkçe)</label>
                        <textarea name="description_tr" rows="4"><?php echo htmlspecialchars($listing['description_tr'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Açıklama (İngilizce)</label>
                        <textarea name="description_en" rows="4"><?php echo htmlspecialchars($listing['description_en'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 2. Fiyat Bilgileri -->
            <div class="form-section">
                <div class="form-section-title">💰 Fiyat Bilgileri</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Fiyat Türü</label>
                        <select name="fiyat_turu">
                            <option value="">Seçiniz</option>
                            <option value="Genel Fiyat" <?php echo ($listing['fiyat_turu'] ?? '') === 'Genel Fiyat' ? 'selected' : ''; ?>>Genel Fiyat</option>
                            <option value="m² Fiyatı" <?php echo ($listing['fiyat_turu'] ?? '') === 'm² Fiyatı' ? 'selected' : ''; ?>>m² Fiyatı</option>
                            <option value="Pazarlıklı" <?php echo ($listing['fiyat_turu'] ?? '') === 'Pazarlıklı' ? 'selected' : ''; ?>>Pazarlıklı</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fiyat (TL) *</label>
                        <input type="text" name="price" id="totalPrice" value="<?php echo htmlspecialchars($listing['price'] ?? ''); ?>"
                            required>
                    </div>
                </div>

                <!-- Arsa için m² alanı -->
                <div class="conditional-field" id="land-price-fields">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Metrekare (m²)</label>
                            <input type="number" name="area" id="landArea" value="<?php echo $listing['area'] ?? ''; ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label>m² Birim Fiyatı (TL/m²)</label>
                            <input type="text" name="price_per_sqm" id="pricePerSqm"
                                value="<?php echo $listing['price_per_sqm'] ?? ''; ?>">
                        </div>
                    </div>
                    <div id="priceCalcInfo" style="font-size: 0.8rem; color: #1e88e5; margin-top: 0.5rem; font-weight: 500;"></div>
                </div>

                <!-- Konut alanları -->
                <div class="conditional-field" id="residential-fields">
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Brüt m²</label>
                            <input type="number" name="brut_metrekare"
                                value="<?php echo $listing['brut_metrekare'] ?? ''; ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label>Net m²</label>
                            <input type="number" name="net_metrekare"
                                value="<?php echo $listing['net_metrekare'] ?? ''; ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label>Oda Sayısı (3+1, 2+1)</label>
                            <input type="text" name="oda_sayisi" placeholder="3+1"
                                value="<?php echo htmlspecialchars($listing['oda_sayisi'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Bina Yaşı</label>
                            <input type="number" name="bina_yasi" value="<?php echo $listing['bina_yasi'] ?? ''; ?>"
                                min="0">
                        </div>
                        <div class="form-group">
                            <label>Bulunduğu Kat</label>
                            <input type="text" name="bulundugu_kat" placeholder="2. Kat"
                                value="<?php echo htmlspecialchars($listing['bulundugu_kat'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Kat Sayısı</label>
                            <input type="number" name="kat_sayisi" value="<?php echo $listing['kat_sayisi'] ?? ''; ?>"
                                min="0">
                        </div>
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Isıtma Türü</label>
                            <select name="isitma_turu">
                                <option value="">Seçiniz</option>
                                <?php foreach (['Kombi', 'Merkezi', 'Soba', 'Klima', 'Doğalgaz', 'Yerden Isıtma', 'Yok'] as $v): ?>
                                    <option value="<?php echo $v; ?>" <?php echo ($listing['isitma_turu'] ?? '') === $v ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Banyo Sayısı</label>
                            <input type="number" name="bathrooms" value="<?php echo $listing['bathrooms'] ?? ''; ?>"
                                min="0">
                        </div>
                        <div class="form-group">
                            <label>Balkon</label>
                            <select name="balkon">
                                <option value="">Seçiniz</option>
                                <option value="Var" <?php echo ($listing['balkon'] ?? '') === 'Var' ? 'selected' : ''; ?>>
                                    Var</option>
                                <option value="Yok" <?php echo ($listing['balkon'] ?? '') === 'Yok' ? 'selected' : ''; ?>>
                                    Yok</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Eşyalı mı?</label>
                            <select name="esyali">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['esyali'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['esyali'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kullanım Durumu</label>
                            <select name="kullanim_durumu">
                                <option value="">Seçiniz</option>
                                <option value="Boş" <?php echo ($listing['kullanim_durumu'] ?? '') === 'Boş' ? 'selected' : ''; ?>>Boş</option>
                                <option value="Kiracılı" <?php echo ($listing['kullanim_durumu'] ?? '') === 'Kiracılı' ? 'selected' : ''; ?>>Kiracılı</option>
                                <option value="Ev Sahibi Oturuyor" <?php echo ($listing['kullanim_durumu'] ?? '') === 'Ev Sahibi Oturuyor' ? 'selected' : ''; ?>>Ev Sahibi Oturuyor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tapu Durumu</label>
                            <select name="tapu_durumu_konut">
                                <option value="">Seçiniz</option>
                                <option value="Kat Mülkiyetli" <?php echo ($listing['tapu_durumu_konut'] ?? '') === 'Kat Mülkiyetli' ? 'selected' : ''; ?>>Kat Mülkiyetli</option>
                                <option value="Kat İrtifaklı" <?php echo ($listing['tapu_durumu_konut'] ?? '') === 'Kat İrtifaklı' ? 'selected' : ''; ?>>Kat İrtifaklı</option>
                                <option value="Müstakil" <?php echo ($listing['tapu_durumu_konut'] ?? '') === 'Müstakil' ? 'selected' : ''; ?>>Müstakil</option>
                                <option value="Hisseli" <?php echo ($listing['tapu_durumu_konut'] ?? '') === 'Hisseli' ? 'selected' : ''; ?>>Hisseli</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Dükkan alanları -->
                <div class="conditional-field" id="commercial-fields">
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Toplam Alan (m²)</label>
                            <input type="number" name="toplam_alan" value="<?php echo $listing['toplam_alan'] ?? ''; ?>"
                                min="0">
                        </div>
                        <div class="form-group">
                            <label>Kullanım Alanı (m²)</label>
                            <input type="number" name="kullanim_alani"
                                value="<?php echo $listing['kullanim_alani'] ?? ''; ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label>Zemin Türü</label>
                            <select name="zemin_turu">
                                <option value="">Seçiniz</option>
                                <?php foreach (['Zemin Kat', 'Bodrum', 'Asma Kat', 'Ara Kat'] as $v): ?>
                                    <option value="<?php echo $v; ?>" <?php echo ($listing['zemin_turu'] ?? '') === $v ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Ön Cephe Uzunluğu (m)</label>
                            <input type="number" name="on_cephe_uzunlugu"
                                value="<?php echo $listing['on_cephe_uzunlugu'] ?? ''; ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label>Giriş Yüksekliği (m)</label>
                            <input type="number" name="giris_yuksekligi"
                                value="<?php echo $listing['giris_yuksekligi'] ?? ''; ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label>WC / Lavabo</label>
                            <select name="wc_lavabo">
                                <option value="">Seçiniz</option>
                                <option value="Var" <?php echo ($listing['wc_lavabo'] ?? '') === 'Var' ? 'selected' : ''; ?>>Var</option>
                                <option value="Yok" <?php echo ($listing['wc_lavabo'] ?? '') === 'Yok' ? 'selected' : ''; ?>>Yok</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Vitrin Camı</label>
                            <select name="vitrin_cami">
                                <option value="">Seçiniz</option>
                                <option value="Var" <?php echo ($listing['vitrin_cami'] ?? '') === 'Var' ? 'selected' : ''; ?>>Var</option>
                                <option value="Yok" <?php echo ($listing['vitrin_cami'] ?? '') === 'Yok' ? 'selected' : ''; ?>>Yok</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tabela Görünürlüğü</label>
                            <select name="tabela_gorunurluk">
                                <option value="">Seçiniz</option>
                                <option value="İyi" <?php echo ($listing['tabela_gorunurluk'] ?? '') === 'İyi' ? 'selected' : ''; ?>>İyi</option>
                                <option value="Orta" <?php echo ($listing['tabela_gorunurluk'] ?? '') === 'Orta' ? 'selected' : ''; ?>>Orta</option>
                                <option value="Zayıf" <?php echo ($listing['tabela_gorunurluk'] ?? '') === 'Zayıf' ? 'selected' : ''; ?>>Zayıf</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Yaya Trafiği</label>
                            <select name="yaya_trafik">
                                <option value="">Seçiniz</option>
                                <option value="Düşük" <?php echo ($listing['yaya_trafik'] ?? '') === 'Düşük' ? 'selected' : ''; ?>>Düşük</option>
                                <option value="Orta" <?php echo ($listing['yaya_trafik'] ?? '') === 'Orta' ? 'selected' : ''; ?>>Orta</option>
                                <option value="Yüksek" <?php echo ($listing['yaya_trafik'] ?? '') === 'Yüksek' ? 'selected' : ''; ?>>Yüksek</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Yük Asansörü</label>
                            <select name="yuk_asansor">
                                <option value="">Seçiniz</option>
                                <option value="Var" <?php echo ($listing['yuk_asansor'] ?? '') === 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                <option value="Yok" <?php echo ($listing['yuk_asansor'] ?? '') === 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Depo Alanı</label>
                            <select name="depo_alani">
                                <option value="">Seçiniz</option>
                                <option value="Var" <?php echo ($listing['depo_alani'] ?? '') === 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                <option value="Yok" <?php echo ($listing['depo_alani'] ?? '') === 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Yalıtım</label>
                            <select name="yalitim">
                                <option value="">Seçiniz</option>
                                <option value="Var" <?php echo ($listing['yalitim'] ?? '') === 'Var' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Var' : 'Yes'; ?></option>
                                <option value="Yok" <?php echo ($listing['yalitim'] ?? '') === 'Yok' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Yok' : 'No'; ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Konum -->
            <div class="form-section">
                <div class="form-section-title">📍 Konum Bilgileri</div>
                <div class="form-row-3">
                    <div class="form-group">
                        <label>Şehir *</label>
                        <select name="city" id="citySelect" required>
                            <option value="">Şehir Seçiniz</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city['il_adi']); ?>"
                                    data-id="<?php echo $city['id']; ?>" <?php echo ($listing['city'] ?? '') === $city['il_adi'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($city['il_adi']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>İlçe</label>
                        <select name="district" id="districtSelect">
                            <option value="">İlçe Seçiniz</option>
                            <?php foreach ($districts as $d): ?>
                                <option value="<?php echo htmlspecialchars($d['ilce_adi']); ?>"
                                    data-id="<?php echo $d['id']; ?>" <?php echo ($listing['district'] ?? '') === $d['ilce_adi'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($d['ilce_adi']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Mahalle</label>
                        <select name="mahalle" id="neighborhoodSelect">
                            <option value="">Mahalle Seçiniz</option>
                            <?php foreach ($neighborhoods as $n): ?>
                                <option value="<?php echo htmlspecialchars($n['mahalle_adi']); ?>" <?php echo ($listing['mahalle'] ?? '') === $n['mahalle_adi'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($n['mahalle_adi']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Adres</label>
                    <input type="text" name="address"
                        value="<?php echo htmlspecialchars($listing['address'] ?? ''); ?>">
                </div>

                <!-- Konut için özel konum alanları -->
                <div class="conditional-field" id="residential-location-fields">
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Cadde Üzerinde mi?</label>
                            <select name="cadde_uzerinde">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['cadde_uzerinde'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['cadde_uzerinde'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Toplu Ulaşıma Yakın mı?</label>
                            <select name="toplu_ulasim">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['toplu_ulasim'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['toplu_ulasim'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Okula Yakın mı?</label>
                            <select name="okula_yakin">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['okula_yakin'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['okula_yakin'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Hastaneye Yakın mı?</label>
                            <select name="hastaneye_yakin">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['hastaneye_yakin'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['hastaneye_yakin'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Market / Alışveriş Merkezi Yakın mı?</label>
                            <select name="market_yakin">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['market_yakin'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['market_yakin'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Merkeze Uzaklık (km)</label>
                            <input type="number" name="merkeze_uzaklik" value="<?php echo $listing['merkeze_uzaklik'] ?? ''; ?>" min="0" step="0.1">
                        </div>
                    </div>
                </div>

                <!-- Ticari için özel konum alanları -->
                <div class="conditional-field" id="commercial-location-fields">
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Cadde Üzerinde mi?</label>
                            <select name="cadde_uzerinde">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['cadde_uzerinde'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['cadde_uzerinde'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Toplu Ulaşıma Yakın mı?</label>
                            <select name="toplu_ulasim">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['toplu_ulasim'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['toplu_ulasim'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Ana Yola Cephe</label>
                            <select name="ana_yola_cephe">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['ana_yola_cephe'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['ana_yola_cephe'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Merkeze Yakın mı?</label>
                            <select name="merkeze_yakin">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['merkeze_yakin'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['merkeze_yakin'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Arsa için özel konum alanları -->
                <div class="conditional-field" id="land-location-fields">
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Cadde Üzerinde mi?</label>
                            <select name="cadde_uzerinde">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['cadde_uzerinde'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['cadde_uzerinde'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Toplu Ulaşıma Yakın mı?</label>
                            <select name="toplu_ulasim">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['toplu_ulasim'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['toplu_ulasim'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Merkeze Yakın mı?</label>
                            <select name="merkeze_yakin">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['merkeze_yakin'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['merkeze_yakin'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Tapu ve İmar (Arsa/Tarla) -->
            <div class="form-section conditional-field" id="land-details-section">
                <div class="form-section-title">📄 Tapu ve İmar Bilgileri</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>İmar Durumu</label>
                        <select name="imar_durumu">
                            <option value="">Seçiniz</option>
                            <?php foreach (['Konut', 'Ticari', 'Konut + Ticari', 'Turizm', 'Tarım', 'Sanayi', 'Yok'] as $v): ?>
                                <option value="<?php echo $v; ?>" <?php echo ($listing['imar_durumu'] ?? '') === $v ? 'selected' : ''; ?>><?php echo $v; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tapu Durumu</label>
                        <select name="tapu_durumu">
                            <option value="">Seçiniz</option>
                            <option value="Müstakil" <?php echo ($listing['tapu_durumu'] ?? '') === 'Müstakil' ? 'selected' : ''; ?>>Müstakil</option>
                            <option value="Hisseli" <?php echo ($listing['tapu_durumu'] ?? '') === 'Hisseli' ? 'selected' : ''; ?>>Hisseli</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Ada No</label>
                        <input type="text" name="ada_no"
                            value="<?php echo htmlspecialchars($listing['ada_no'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Parsel No</label>
                        <input type="text" name="parsel_no"
                            value="<?php echo htmlspecialchars($listing['parsel_no'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row-3">
                    <div class="form-group">
                        <label>Krediye Uygun</label>
                        <select name="krediye_uygun">
                            <option value="">Seçiniz</option>
                            <option value="Evet" <?php echo ($listing['krediye_uygun'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                            <option value="Hayır" <?php echo ($listing['krediye_uygun'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Takas</label>
                        <select name="takas">
                            <option value="">Seçiniz</option>
                            <option value="Var" <?php echo ($listing['takas'] ?? '') === 'Var' ? 'selected' : ''; ?>>Var
                            </option>
                            <option value="Yok" <?php echo ($listing['takas'] ?? '') === 'Yok' ? 'selected' : ''; ?>>Yok
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kat Karşılığı</label>
                        <select name="kat_karsiligi">
                            <option value="">Seçiniz</option>
                            <option value="Evet" <?php echo ($listing['kat_karsiligi'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                            <option value="Hayır" <?php echo ($listing['kat_karsiligi'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 4.1. Altyapı Özellikleri (Arsa/Tarla) -->
            <div class="form-section conditional-field" id="infrastructure-section">
                <div class="form-section-title">🧱 Altyapı Özellikleri</div>
                <div class="form-row-3">
                    <div class="form-group">
                        <label>Elektrik</label>
                        <select name="elektrik">
                            <option value="">Seçiniz</option>
                            <option value="Var" <?php echo ($listing['elektrik'] ?? '') === 'Var' ? 'selected' : ''; ?>>Var</option>
                            <option value="Yok" <?php echo ($listing['elektrik'] ?? '') === 'Yok' ? 'selected' : ''; ?>>Yok</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Su Hattı</label>
                        <select name="su">
                            <option value="">Seçiniz</option>
                            <option value="Var" <?php echo ($listing['su'] ?? '') === 'Var' ? 'selected' : ''; ?>>Var</option>
                            <option value="Yok" <?php echo ($listing['su'] ?? '') === 'Yok' ? 'selected' : ''; ?>>Yok</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kanalizasyon</label>
                        <select name="kanalizasyon">
                            <option value="">Seçiniz</option>
                            <option value="Var" <?php echo ($listing['kanalizasyon'] ?? '') === 'Var' ? 'selected' : ''; ?>>Var</option>
                            <option value="Yok" <?php echo ($listing['kanalizasyon'] ?? '') === 'Yok' ? 'selected' : ''; ?>>Yok</option>
                        </select>
                    </div>
                </div>
                <div class="form-row-3">
                    <div class="form-group">
                        <label>Doğalgaz</label>
                        <select name="dogalgaz">
                            <option value="">Seçiniz</option>
                            <option value="Var" <?php echo ($listing['dogalgaz'] ?? '') === 'Var' ? 'selected' : ''; ?>>Var</option>
                            <option value="Yok" <?php echo ($listing['dogalgaz'] ?? '') === 'Yok' ? 'selected' : ''; ?>>Yok</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Telefon Hattı</label>
                        <select name="telefon">
                            <option value="">Seçiniz</option>
                            <option value="Var" <?php echo ($listing['telefon'] ?? '') === 'Var' ? 'selected' : ''; ?>>Var</option>
                            <option value="Yok" <?php echo ($listing['telefon'] ?? '') === 'Yok' ? 'selected' : ''; ?>>Yok</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Yolu Açılmış mı?</label>
                        <select name="yolu_acilmis">
                            <option value="">Seçiniz</option>
                            <option value="Evet" <?php echo ($listing['yolu_acilmis'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                            <option value="Hayır" <?php echo ($listing['yolu_acilmis'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Parselli mi?</label>
                        <select name="parselli">
                            <option value="">Seçiniz</option>
                            <option value="Evet" <?php echo ($listing['parselli'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                            <option value="Hayır" <?php echo ($listing['parselli'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>İfrazlı mı?</label>
                        <select name="ifrazli">
                            <option value="">Seçiniz</option>
                            <option value="Evet" <?php echo ($listing['ifrazli'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                            <option value="Hayır" <?php echo ($listing['ifrazli'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 5. Bina Özellikleri (Konut) -->
            <div class="form-section conditional-field" id="building-features-section">
                <div class="form-section-title">🏢 Bina Özellikleri</div>
                <div class="form-row-3">
                    <div class="form-group">
                        <label>Asansör</label>
                        <select name="asansor">
                            <option value="">Seçiniz</option>
                            <option value="Var" <?php echo ($listing['asansor'] ?? '') === 'Var' ? 'selected' : ''; ?>>Var
                            </option>
                            <option value="Yok" <?php echo ($listing['asansor'] ?? '') === 'Yok' ? 'selected' : ''; ?>>Yok
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Otopark</label>
                        <select name="otopark">
                            <option value="">Seçiniz</option>
                            <option value="Açık" <?php echo ($listing['otopark'] ?? '') === 'Açık' ? 'selected' : ''; ?>>
                                Açık</option>
                            <option value="Kapalı" <?php echo ($listing['otopark'] ?? '') === 'Kapalı' ? 'selected' : ''; ?>>Kapalı</option>
                            <option value="Yok" <?php echo ($listing['otopark'] ?? '') === 'Yok' ? 'selected' : ''; ?>>Yok
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Güvenlik</label>
                        <select name="guvenlik">
                            <option value="">Seçiniz</option>
                            <option value="Var" <?php echo ($listing['guvenlik'] ?? '') === 'Var' ? 'selected' : ''; ?>>
                                Var</option>
                            <option value="Yok" <?php echo ($listing['guvenlik'] ?? '') === 'Yok' ? 'selected' : ''; ?>>
                                Yok</option>
                        </select>
                    </div>
                </div>
                <div class="form-row-3">
                    <div class="form-group">
                        <label>Site İçinde mi?</label>
                        <select name="site_icinde" id="site_icinde_select">
                            <option value="">Seçiniz</option>
                            <option value="Evet" <?php echo ($listing['site_icinde'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                            <option value="Hayır" <?php echo ($listing['site_icinde'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kamera Sistemi</label>
                        <select name="kamera_sistemi">
                            <option value="">Seçiniz</option>
                            <option value="Var" <?php echo ($listing['kamera_sistemi'] ?? '') === 'Var' ? 'selected' : ''; ?>>Var</option>
                            <option value="Yok" <?php echo ($listing['kamera_sistemi'] ?? '') === 'Yok' ? 'selected' : ''; ?>>Yok</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kapıcı / Site Görevlisi</label>
                        <select name="kapici">
                            <option value="">Seçiniz</option>
                            <option value="Var" <?php echo ($listing['kapici'] ?? '') === 'Var' ? 'selected' : ''; ?>>Var
                            </option>
                            <option value="Yok" <?php echo ($listing['kapici'] ?? '') === 'Yok' ? 'selected' : ''; ?>>Yok
                            </option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Isı Yalıtımı</label>
                        <select name="isi_yalitim">
                            <option value="">Seçiniz</option>
                            <option value="Var" <?php echo ($listing['isi_yalitim'] ?? '') === 'Var' ? 'selected' : ''; ?>>Var</option>
                            <option value="Yok" <?php echo ($listing['isi_yalitim'] ?? '') === 'Yok' ? 'selected' : ''; ?>>Yok</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- 5. Site Özellikleri -->
            <div class="form-section conditional-field" id="site-features-grid-section">
                <div class="form-section-title">🏢 Site Özellikleri</div>
                <p style="color: #64748b; margin-bottom: 1.5rem; font-size: 0.85rem;">Site içindeki mülkler için mevcut özellikleri işaretleyiniz.</p>
                <div class="site-features-grid">
                    <?php 
                    $site_features = [
                        'site_guvenlik' => ['icon' => 'fa-shield-alt', 'tr' => '24 Saat Güvenlik', 'en' => '24 Hour Security'],
                        'site_spor_salonu' => ['icon' => 'fa-dumbbell', 'tr' => 'Spor Salonu', 'en' => 'Gym'],
                        'site_yuzme_havuzu' => ['icon' => 'fa-swimming-pool', 'tr' => 'Yüzme Havuzu', 'en' => 'Swimming Pool'],
                        'site_cocuk_parki' => ['icon' => 'fa-child', 'tr' => 'Çocuk Parkı', 'en' => 'Playground'],
                        'site_sauna' => ['icon' => 'fa-hot-tub', 'tr' => 'Sauna', 'en' => 'Sauna'],
                        'site_turk_hamami' => ['icon' => 'fa-spa', 'tr' => 'Türk Hamamı', 'en' => 'Turkish Bath'],
                        'site_jenerator' => ['icon' => 'fa-bolt', 'tr' => 'Jeneratör', 'en' => 'Generator'],
                        'site_kapali_otopark' => ['icon' => 'fa-warehouse', 'tr' => 'Kapalı Otopark', 'en' => 'Indoor Parking'],
                        'site_acik_otopark' => ['icon' => 'fa-parking', 'tr' => 'Açık Otopark', 'en' => 'Open Parking'],
                        'site_tenis_kortu' => ['icon' => 'fa-table-tennis', 'tr' => 'Tenis Kortu', 'en' => 'Tennis Court'],
                        'site_basketbol_sahasi' => ['icon' => 'fa-basketball-ball', 'tr' => 'Basketbol Sahası', 'en' => 'Basketball Court'],
                        'site_market' => ['icon' => 'fa-shopping-cart', 'tr' => 'Market', 'en' => 'Market'],
                        'site_kres' => ['icon' => 'fa-baby', 'tr' => 'Kreş', 'en' => 'Nursery'],
                        'site_cafe' => ['icon' => 'fa-coffee', 'tr' => 'Cafe', 'en' => 'Cafe'],
                        'site_restorant' => ['icon' => 'fa-utensils', 'tr' => 'Restoran', 'en' => 'Restaurant'],
                        'site_kuafor' => ['icon' => 'fa-cut', 'tr' => 'Kuaför', 'en' => 'Hair Salon'],
                        'site_toplanti_odasi' => ['icon' => 'fa-users', 'tr' => 'Toplantı Odası', 'en' => 'Meeting Room'],
                        'site_bahce' => ['icon' => 'fa-tree', 'tr' => 'Bahçe', 'en' => 'Garden'],
                        'site_evcil_hayvan' => ['icon' => 'fa-paw', 'tr' => 'Evcil Hayvan İzni', 'en' => 'Pet Friendly'],
                        'site_engelli_erisimi' => ['icon' => 'fa-wheelchair', 'tr' => 'Engelli Erişimi', 'en' => 'Disabled Access']
                    ];
                    
                    foreach ($site_features as $key => $data): ?>
                        <div class="feature-checkbox">
                            <input type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="1" <?php echo ($listing[$key] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label for="<?php echo $key; ?>">
                                <i class="fas <?php echo $data['icon']; ?>"></i>
                                <?php echo $lang == 'tr' ? $data['tr'] : $data['en']; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 5.1. Manzara ve Cephe (Konut/Arsa) -->
            <div class="form-section conditional-field" id="view-section">
                <div class="form-section-title">🌅 Manzara ve Cephe</div>
                
                <div class="conditional-field" id="residential-view-fields">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Cephe</label>
                            <select name="cephe">
                                <option value="">Seçiniz</option>
                                <?php foreach(['Kuzey','Güney','Doğu','Batı','Güney-Doğu','Güney-Batı','Kuzey-Doğu','Kuzey-Batı'] as $v): ?>
                                <option value="<?php echo $v; ?>" <?php echo ($listing['cephe'] ?? '') === $v ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Manzara</label>
                            <input type="text" name="manzara_tipi_konut" value="<?php echo htmlspecialchars($listing['manzara_tipi'] ?? ''); ?>" placeholder="Şehir, Doğa, Deniz vb.">
                        </div>
                    </div>
                </div>

                <div class="conditional-field" id="land-view-fields">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Doğa Manzaralı mı?</label>
                            <select name="doga_manzara">
                                <option value="">Seçiniz</option>
                                <option value="Evet" <?php echo ($listing['doga_manzara'] ?? '') === 'Evet' ? 'selected' : ''; ?>>Evet</option>
                                <option value="Hayır" <?php echo ($listing['doga_manzara'] ?? '') === 'Hayır' ? 'selected' : ''; ?>>Hayır</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Manzara Tipi</label>
                            <input type="text" name="manzara_tipi_arsa" value="<?php echo htmlspecialchars($listing['manzara_tipi'] ?? ''); ?>" placeholder="Deniz, Şehir, Dağ vb.">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. Resimler & Video -->
            <div class="form-section">
                <div class="form-section-title">🖼️ <?php echo $lang == 'tr' ? 'Medya Galerisi' : 'Media Gallery'; ?></div>
                <p style="color: #64748b; margin-bottom: 2rem; font-size: 0.95rem;">
                    <?php echo $lang == 'tr' ? 'İlanınızın kalitesini artırmak için en az 5 adet yüksek çözünürlüklü resim eklemenizi öneririz.' : 'We recommend adding at least 5 high-resolution images to increase the quality of your listing.'; ?>
                </p>

                <!-- Premium Drop Zone -->
                <div class="multi-upload-dropzone" id="multi-dropzone">
                    <div class="dropzone-content">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <h3><?php echo $lang == 'tr' ? 'Resimleri Buraya Sürükleyin' : 'Drag & Drop Images Here'; ?></h3>
                        <p><?php echo $lang == 'tr' ? 'veya' : 'or'; ?></p>
                        <button type="button" class="btn btn-primary" style="padding: 0.75rem 2.5rem;" onclick="document.getElementById('imageInput').click()">
                            <i class="fas fa-images"></i> <?php echo $lang == 'tr' ? 'Dosya Seç' : 'Browse Files'; ?>
                        </button>
                        <p class="upload-hint">PNG, JPG, WEBP • Max 10MB • <?php echo $lang == 'tr' ? 'En fazla 30 adet' : 'Up to 30 items'; ?></p>
                    </div>
                    <input type="file" id="imageInput" multiple accept="image/*" style="display: none;" onchange="handleImageSelect(event)">
                </div>

                <!-- Yüklenen Resimler Grid -->
                <div class="uploaded-images-container" id="uploaded-images-container" style="<?php echo ($is_edit && !empty($listing['image1'])) ? '' : 'display: none;'; ?>">
                    <div class="uploaded-images-header">
                        <h4>
                            <i class="fas fa-th-large"></i> <?php echo $lang == 'tr' ? 'Yüklenen Resimler' : 'Uploaded Images'; ?> 
                            (<span id="image-count">0</span>/30)
                        </h4>
                        <button type="button" class="btn btn-secondary btn-small" onclick="document.getElementById('imageInput').click()">
                            <i class="fas fa-plus"></i> <?php echo $lang == 'tr' ? 'Daha Fazla Ekle' : 'Add More'; ?>
                        </button>
                    </div>
                    <div class="main-image-hint">
                        <i class="fas fa-star"></i> <?php echo $lang == 'tr' ? 'Ana resim olarak belirlemek istediğiniz görselin üzerine tıklayın.' : 'Click on an image to set it as the main photo.'; ?>
                    </div>
                    <div class="uploaded-images-grid" id="previewGrid">
                        <!-- JS handles items -->
                    </div>
                </div>

                <!-- Video Upload Section -->
                <div class="video-upload-section">
                    <div class="form-section-title" style="border:none; margin-bottom: 0.5rem;">🎥 <?php echo $lang == 'tr' ? 'Video Tanıtımı (Opsiyonel)' : 'Video Tour (Optional)'; ?></div>
                    <p style="color: #64748b; margin-bottom: 1.5rem; font-size: 0.9rem;">
                        <?php echo $lang == 'tr' ? 'Video olan ilanlar %40 daha fazla ilgi görmektedir. Max: 50MB (MP4, WEBM)' : 'Listings with videos get 40% more engagement. Max: 50MB (MP4, WEBM)'; ?>
                    </p>

                    <div id="video-upload-box" class="multi-upload-dropzone" style="min-height: 180px; <?php echo (!empty($listing['video'])) ? 'display:none;' : ''; ?>">
                        <div class="dropzone-content" style="gap: 1rem;">
                            <i class="fas fa-film" style="font-size: 2.5rem;"></i>
                            <button type="button" class="btn btn-secondary btn-small" onclick="document.getElementById('videoInput').click()">
                                <i class="fas fa-plus"></i> <?php echo $lang == 'tr' ? 'Video Yükle' : 'Upload Video'; ?>
                            </button>
                        </div>
                        <input type="file" id="videoInput" name="video" accept="video/*" style="display: none;" onchange="handleVideoPreview(event)">
                    </div>

                    <div id="video-preview-wrapper" class="video-preview-container" style="<?php echo (empty($listing['video'])) ? 'display:none;' : ''; ?>">
                        <?php if (!empty($listing['video'])): ?>
                            <video src="/uploads/<?php echo $listing['video']; ?>" controls></video>
                        <?php else: ?>
                            <video controls></video>
                        <?php endif; ?>
                        <button type="button" class="btn-remove-video" onclick="removeVideo()">
                            <i class="fas fa-trash"></i> <?php echo $lang == 'tr' ? 'Videoyu Kaldır' : 'Remove Video'; ?>
                        </button>
                    </div>
                    <input type="hidden" name="delete_video" id="delete_video_input" value="0">
                </div>

                <?php if ($is_edit): ?>
                    <?php 
                    $existing_imgs = [];
                    for ($i = 1; $i <= 20; $i++) {
                        if (!empty($listing['image' . $i])) $existing_imgs[] = $listing['image' . $i];
                    }
                    ?>
                    <input type="hidden" id="existing-images-json" value='<?php echo json_encode($existing_imgs); ?>'>
                <?php endif; ?>
                <input type="hidden" name="existing_images_list" id="existingImagesList" value="">
                <input type="hidden" name="main_image_index" id="main_image_index" value="0">
            </div>

            <!-- 7. Ek Notlar -->
            <div class="form-section">
                <div class="form-section-title">📝 Ek Notlar</div>
                <div class="form-group">
                    <textarea name="notes" rows="3"
                        placeholder="İlan hakkında ek bilgiler..."><?php echo htmlspecialchars($listing['notes'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- İşlem Butonları -->
            <div class="form-actions">
                <a href="/emlakci/ilanlar" class="btn-cancel">← İptal</a>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <?php echo $is_edit ? '💾 Güncelle' : '📤 İlanı Yayınla'; ?>
                </button>
            </div>
        </form>
    </div>

    <div class="saving-overlay" id="savingOverlay">
        <div class="saving-box">
            <div class="saving-spinner"></div>
            <p style="font-weight: 600; color: #0f172a;">İlan kaydediliyor...</p>
        </div>
    </div>

    <script>
        function updateConditionalFields() {
            const type = document.getElementById('propertyType').value;
            const isKonut = ['ev', 'daire', 'villa'].includes(type);
            const isArsa = ['arsa', 'tarla'].includes(type);
            const isDukkan = type === 'dukkan';
            
            const siteIcinde = document.getElementById('site_icinde_select').value;

            document.getElementById('residential-fields').classList.toggle('visible', isKonut);
            document.getElementById('land-price-fields').classList.toggle('visible', isArsa);
            document.getElementById('commercial-fields').classList.toggle('visible', isDukkan);
            document.getElementById('land-details-section').classList.toggle('visible', isArsa);
            document.getElementById('building-features-section').classList.toggle('visible', isKonut || isDukkan);
            document.getElementById('infrastructure-section').classList.toggle('visible', isArsa);
            document.getElementById('view-section').classList.toggle('visible', isKonut || isArsa);
            document.getElementById('residential-view-fields').classList.toggle('visible', isKonut);
            document.getElementById('land-view-fields').classList.toggle('visible', isArsa);
            document.getElementById('residential-location-fields').classList.toggle('visible', isKonut);
            document.getElementById('commercial-location-fields').classList.toggle('visible', isDukkan);
            document.getElementById('land-location-fields').classList.toggle('visible', isArsa);

            // Site özellikleri (Sadece site içindeki konutlar için)
            document.getElementById('site-features-grid-section').classList.toggle('visible', isKonut && siteIcinde === 'Evet');
        }

        document.getElementById('site_icinde_select').addEventListener('change', updateConditionalFields);
        updateConditionalFields();
 
        const landArea = document.getElementById('landArea');
        const pricePerSqm = document.getElementById('pricePerSqm');
        const totalPrice = document.getElementById('totalPrice');

        function calculateTotalPrice() {
            if (landArea && pricePerSqm && totalPrice && landArea.value && pricePerSqm.value) {
                const area = parseFloat(landArea.value) || 0;
                const priceValueStr = pricePerSqm.value.replace(/\./g, '').replace(',', '.');
                const pricePerM2 = parseFloat(priceValueStr) || 0;
                const total = area * pricePerM2;

                if (total > 0) {
                    totalPrice.value = Math.floor(total).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    document.getElementById('priceCalcInfo').textContent = 
                        'Hesaplanan: ' + area.toLocaleString('tr-TR') + ' m² × ' + 
                        pricePerM2.toLocaleString('tr-TR') + ' TL = ' + 
                        total.toLocaleString('tr-TR', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' TL';
                }
            }
        }

        function setupPriceFormatting(input) {
            input.addEventListener('input', function (e) {
                let val = input.value.replace(/\./g, '');
                val = val.replace(/[^0-9,]/g, '');
                const parts = val.split(',');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                input.value = parts.length > 1 ? parts[0] + ',' + parts.slice(1).join('') : parts[0];
                calculateTotalPrice();
            });
        }

        if (totalPrice) setupPriceFormatting(totalPrice);
        if (pricePerSqm) setupPriceFormatting(pricePerSqm);
        if (landArea) landArea.addEventListener('input', calculateTotalPrice);
        
        calculateTotalPrice();



        let uploadedFiles = [];
        let mainImageIndex = 0;

        // Load existing images in edit mode
        window.addEventListener('load', () => {
            const existingJson = document.getElementById('existing-images-json');
            if (existingJson && existingJson.value) {
                try {
                    const images = JSON.parse(existingJson.value);
                    images.forEach((img, idx) => {
                        uploadedFiles.push({
                            file: null,
                            preview: '/uploads/' + img,
                            name: img,
                            isExisting: true
                        });
                    });
                    renderImages();
                } catch(e) { console.error(e); }
            }
        });

        function handleImageSelect(event) {
            const files = event.target.files;
            processFiles(files);
            event.target.value = '';
        }

        function processFiles(files) {
            Array.from(files).forEach(file => {
                if (uploadedFiles.length >= 30) return;
                const reader = new FileReader();
                reader.onload = (e) => {
                    uploadedFiles.push({
                        file: file,
                        preview: e.target.result,
                        name: file.name,
                        isExisting: false
                    });
                    renderImages();
                };
                reader.readAsDataURL(file);
            });
        }

        function renderImages() {
            const grid = document.getElementById('previewGrid');
            grid.innerHTML = '';
            
            uploadedFiles.forEach((img, idx) => {
                const isMain = idx === mainImageIndex;
                const div = document.createElement('div');
                div.className = `uploaded-image-item ${isMain ? 'main-image' : ''}`;
                div.onclick = () => setMainImage(idx);
                
                div.innerHTML = `
                    <img src="${img.preview}" alt="">
                    ${isMain ? `<div class="main-badge"><i class="fas fa-star"></i> ${ '<?php echo $lang == 'tr' ? 'Ana Resim' : 'Main Photo'; ?>' }</div>` : ''}
                    <button type="button" class="btn-remove" onclick="event.stopPropagation(); removeImage(${idx})">✕</button>
                    ${!isMain ? `<button type="button" class="set-main-btn">${ '<?php echo $lang == 'tr' ? 'Ana Resim Yap' : 'Set as Main'; ?>' }</button>` : ''}
                `;
                grid.appendChild(div);
            });
            
            updateImageCount();
        }

        function setMainImage(idx) {
            mainImageIndex = idx;
            renderImages();
        }

        function removeImage(idx) {
            uploadedFiles.splice(idx, 1);
            if (mainImageIndex === idx) mainImageIndex = 0;
            else if (mainImageIndex > idx) mainImageIndex--;
            renderImages();
        }

        function updateImageCount() {
            const count = uploadedFiles.length;
            document.getElementById('image-count').textContent = count;
            document.getElementById('uploaded-images-container').style.display = count > 0 ? 'block' : 'none';
        }

        // Drag and Drop support
        const dropzone = document.getElementById('multi-dropzone');
        if (dropzone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eName => {
                dropzone.addEventListener(eName, e => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eName => {
                dropzone.addEventListener(eName, () => dropzone.classList.add('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eName => {
                dropzone.addEventListener(eName, () => dropzone.classList.remove('dragover'), false);
            });

            dropzone.addEventListener('drop', e => {
                processFiles(e.dataTransfer.files);
            });
        }

        function handleVideoPreview(event) {
            const file = event.target.files[0];
            if (file) {
                const url = URL.createObjectURL(file);
                const wrapper = document.getElementById('video-preview-wrapper');
                const videoEl = wrapper.querySelector('video');
                videoEl.src = url;
                wrapper.style.display = 'block';
                document.getElementById('video-upload-box').style.display = 'none';
                document.getElementById('delete_video_input').value = '0';
            }
        }

        function removeVideo() {
            document.getElementById('videoInput').value = '';
            document.getElementById('video-preview-wrapper').style.display = 'none';
            document.getElementById('video-upload-box').style.display = 'flex';
            document.getElementById('delete_video_input').value = '1';
        }

        document.getElementById('listingForm').addEventListener('submit', function (e) {
            e.preventDefault();
            document.getElementById('savingOverlay').classList.add('active');
            
            const formData = new FormData(this);
            
            // Handle images
            const existingImages = [];
            formData.delete('uploaded_images[]'); // Clear default if any
            
            uploadedFiles.forEach((img, idx) => {
                if (img.isExisting) {
                    existingImages.push(img.name);
                } else {
                    formData.append('uploaded_images[]', img.file);
                }
            });
            
            formData.set('existing_images_list', JSON.stringify(existingImages));
            formData.set('main_image_index', mainImageIndex);

            fetch('/emlakci/process_listing.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    document.getElementById('savingOverlay').classList.remove('active');
                    if (data.success) {
                        window.location.href = '/emlakci/ilanlar';
                    } else {
                        alert(data.message || 'Bir hata oluştu');
                    }
                })
                .catch(err => {
                    document.getElementById('savingOverlay').classList.remove('active');
                    alert('Bir hata oluştu: ' + err.message);
                });
        });
    </script>
    <script src="/assets/js/location-filter.js"></script>
</body>

</html>