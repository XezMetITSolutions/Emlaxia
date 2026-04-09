<?php
require_once 'config.php';

$id = $_GET['id'] ?? 0;
$slug = $_GET['slug'] ?? '';

if ($slug) {
    $stmt = $pdo->prepare("SELECT l.*, u.full_name as owner_name, u.phone as owner_phone, u.email as owner_email, u.firma_adi, u.logo as owner_logo, u.user_type as owner_type, u.bio as owner_bio 
                            FROM listings l 
                            LEFT JOIN users u ON l.user_id = u.id 
                            WHERE l.slug = :slug");
    $stmt->execute([':slug' => $slug]);
} else {
    $stmt = $pdo->prepare("SELECT l.*, u.full_name as owner_name, u.phone as owner_phone, u.email as owner_email, u.firma_adi, u.logo as owner_logo, u.user_type as owner_type, u.bio as owner_bio 
                            FROM listings l 
                            LEFT JOIN users u ON l.user_id = u.id 
                            WHERE l.id = :id");
    $stmt->execute([':id' => $id]);
}

$listing = $stmt->fetch();

if (!$listing) {
    echo "İlan bulunamadı. Aranan slug: " . htmlspecialchars($slug);
    // header('Location: ilanlar.php');
    exit;
}

// İlan resimleri (20 resme kadar destek)
$images = [];
for ($i = 1; $i <= 20; $i++) {
    if (!empty($listing['image' . $i])) {
        $images[] = $listing['image' . $i];
    }
}

// Favori kontrolü
$is_favorite = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = :uid AND listing_id = :lid");
    $stmt->execute([':uid' => $_SESSION['user_id'], ':lid' => $listing['id']]);
    if ($stmt->fetch()) {
        $is_favorite = true;
    }
}

// Yorumları getir
$stmt = $pdo->prepare("SELECT c.*, u.full_name as user_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.listing_id = :lid AND c.status = 'approved' ORDER BY c.created_at DESC");
$stmt->execute([':lid' => $listing['id']]);
$comments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($listing['meta_title'] ?: $listing['title_' . $lang]); ?> — Emlaxia.com</title>
    <?php 
        $meta_desc = htmlspecialchars($listing['meta_description'] ?: mb_substr(strip_tags($listing['description_' . $lang]), 0, 160));
        $meta_image = !empty($images) ? "https://emlaxia.com/uploads/" . htmlspecialchars($images[0]) : "https://emlaxia.com/Logo.png";
        $current_url = "https://emlaxia.com" . $_SERVER['REQUEST_URI'];
    ?>
    <meta name="description" content="<?php echo $meta_desc; ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $current_url; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($listing['title_' . $lang]); ?>">
    <meta property="og:description" content="<?php echo $meta_desc; ?>">
    <meta property="og:image" content="<?php echo $meta_image; ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo $current_url; ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($listing['title_' . $lang]); ?>">
    <meta property="twitter:description" content="<?php echo $meta_desc; ?>">
    <meta property="twitter:image" content="<?php echo $meta_image; ?>">

    <base href="/">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?php echo CSS_VERSION; ?>">
    <link rel="stylesheet" href="/assets/css/property-detail.css?v=<?php echo CSS_VERSION; ?>">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- ═══════ CINEMATIC GALLERY ═══════ -->
        <div class="pd-gallery">
            <?php if (empty($images)): ?>
                <div class="pd-gallery-main">
                    <img src="/assets/images/placeholder.jpg" alt="No Image" id="pdMainImage">
                </div>
            <?php else: ?>
                <a href="/ilanlar" class="pd-back-btn" data-i18n="back">
                    <i class="fas fa-arrow-left"></i>
                    <?php echo t('back'); ?>
                </a>
                <div class="pd-gallery-main" onclick="openLightbox(currentImageIndex)">
                    <img src="/uploads/<?php echo htmlspecialchars($images[0]); ?>"
                        alt="<?php echo htmlspecialchars($listing['title_' . $lang]); ?>"
                        id="pdMainImage">
                    <?php if (count($images) > 1): ?>
                        <div class="pd-gallery-counter" id="pdGalleryCounter">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <span>1 / <?php echo count($images); ?></span>
                        </div>
                    <?php endif; ?>
                    <button class="pd-gallery-fullscreen" onclick="event.stopPropagation(); openLightbox(currentImageIndex)" title="<?php echo $lang == 'tr' ? 'Tam Ekran' : 'Full Screen'; ?>">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="pd-thumbnails">
                        <?php foreach ($images as $index => $img): ?>
                            <div class="pd-thumb <?php echo $index === 0 ? 'active' : ''; ?>"
                                onclick="changeImage(<?php echo $index; ?>)">
                                <img src="/uploads/<?php echo htmlspecialchars($img); ?>"
                                    alt="Thumbnail <?php echo $index + 1; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="container">
            <!-- ═══════ HEADER: TITLE + PRICE ═══════ -->
            <section class="pd-header-section">
                <div class="pd-header-grid">
                    <div class="pd-title-area">
                        <div class="pd-badges">
                            <span class="pd-badge pd-badge-type"><?php echo t($listing['property_type']); ?></span>
                            <?php if (!empty($listing['listing_type'])): ?>
                                <span class="pd-badge pd-badge-listing"><?php echo t($listing['listing_type']); ?></span>
                            <?php endif; ?>
                        </div>
                        <h1 class="pd-title"><?php echo htmlspecialchars($listing['title_' . $lang]); ?></h1>
                        <div class="pd-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($listing['city'] . ' / ' . $listing['district'] . ' / ' . $listing['mahalle']); ?></span>
                        </div>
                    </div>
                    <div class="pd-price-card">
                        <button id="btn-favorite" class="pd-favorite-btn <?php echo $is_favorite ? 'active' : ''; ?>" data-id="<?php echo $listing['id']; ?>" title="<?php echo $lang == 'tr' ? 'Favorilere Ekle' : 'Add to Favorites'; ?>">
                            <i class="<?php echo $is_favorite ? 'fas' : 'far'; ?> fa-heart"></i>
                        </button>
                        <span class="pd-price-label"><?php echo $lang == 'tr' ? 'Satış Fiyatı' : 'Sale Price'; ?></span>
                        <div class="pd-price-amount"><?php echo number_format($listing['price'], 0, ',', '.'); ?> <?php echo t('currency'); ?></div>
                        <?php if (!empty($listing['owner_type']) && $listing['owner_type'] == 'emlakci' && !empty($listing['komisyon_yuzdesi'])): ?>
                            <div class="pd-commission">
                                <span class="pd-commission-label"><?php echo $lang == 'tr' ? 'Komisyon' : 'Commission'; ?>:</span>
                                <span class="pd-commission-value">%<?php echo number_format($listing['komisyon_yuzdesi'], 1); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="pd-price-actions">
                            <a href="#pd-offer" class="pd-btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                <?php echo $lang == 'tr' ? 'Teklif Ver' : 'Make Offer'; ?>
                            </a>
                            <?php if (!empty($listing['owner_phone'])): ?>
                                <a href="tel:<?php echo htmlspecialchars($listing['owner_phone']); ?>" class="pd-btn-outline">
                                    <i class="fas fa-phone"></i>
                                    <?php echo $lang == 'tr' ? 'Hemen Ara' : 'Call Now'; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════ QUICK STATS BAR ═══════ -->
            <?php
            $stats_items = [];
            if ($listing['area']) $stats_items[] = ['icon' => '📐', 'label' => t('area'), 'value' => $listing['area'] . ' m²'];
            if ($listing['rooms']) $stats_items[] = ['icon' => '🛏️', 'label' => t('rooms'), 'value' => $listing['rooms']];
            if ($listing['bathrooms']) $stats_items[] = ['icon' => '🚿', 'label' => t('bathrooms'), 'value' => $listing['bathrooms']];
            if (!empty($listing['bina_yasi'])) $stats_items[] = ['icon' => '🏗️', 'label' => $lang == 'tr' ? 'Bina Yaşı' : 'Building Age', 'value' => $listing['bina_yasi'] . ' ' . ($lang == 'tr' ? 'Yıl' : 'Yr')];
            if (!empty($listing['kat_sayisi'])) $stats_items[] = ['icon' => '🏢', 'label' => $lang == 'tr' ? 'Kat Sayısı' : 'Floors', 'value' => $listing['kat_sayisi']];
            ?>
            <?php if (!empty($stats_items)): ?>
                <div class="pd-quick-stats">
                    <?php foreach ($stats_items as $stat): ?>
                        <div class="pd-stat-item">
                            <div class="pd-stat-icon"><?php echo $stat['icon']; ?></div>
                            <div class="pd-stat-content">
                                <span class="pd-stat-label"><?php echo $stat['label']; ?></span>
                                <span class="pd-stat-value"><?php echo $stat['value']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- ═══════ VIDEO SECTION ═══════ -->
            <?php if (!empty($listing['video'])): ?>
                <div class="pd-section-card pd-video-section">
                    <h2 class="pd-section-title">
                        <span>🎬</span>
                        <?php echo $lang == 'tr' ? 'Mülk Videosu' : 'Property Video'; ?>
                    </h2>
                    <div class="pd-video-container">
                        <video controls>
                            <source src="/uploads/<?php echo htmlspecialchars($listing['video']); ?>" type="video/mp4">
                            <source src="/uploads/<?php echo htmlspecialchars($listing['video']); ?>" type="video/webm">
                            <?php echo $lang == 'tr' ? 'Tarayıcınız video oynatmayı desteklemiyor.' : 'Your browser does not support video playback.'; ?>
                        </video>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ═══════ MAIN CONTENT LAYOUT ═══════ -->
            <div class="pd-content-layout">
                <!-- LEFT COLUMN -->
                <div class="pd-main-content">

                    <?php
                    $property_type = $listing['property_type'];
                    $is_land = ($property_type == 'arsa');
                    $is_residential = in_array($property_type, ['ev', 'daire', 'villa']);
                    $is_commercial = ($property_type == 'dukkan');
                    ?>

                    <!-- ── Konut Detayları ── -->
                    <?php if ($is_residential): ?>
                        <div class="pd-section-card">
                            <h2 class="pd-section-title">
                                <span>🏠</span>
                                <?php echo $lang == 'tr' ? 'Konut Detayları' : 'Residential Details'; ?>
                            </h2>
                            <div class="pd-details-grid">
                                <?php if ($listing['brut_metrekare']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Brüt m²' : 'Gross m²'; ?></span>
                                        <span class="pd-detail-value"><?php echo $listing['brut_metrekare']; ?> m²</span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['net_metrekare']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Net m²' : 'Net m²'; ?></span>
                                        <span class="pd-detail-value"><?php echo $listing['net_metrekare']; ?> m²</span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['oda_sayisi']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Oda Sayısı' : 'Rooms'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['oda_sayisi']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['bina_yasi']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Bina Yaşı' : 'Building Age'; ?></span>
                                        <span class="pd-detail-value"><?php echo $listing['bina_yasi']; ?> <?php echo $lang == 'tr' ? 'Yıl' : 'Years'; ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['bulundugu_kat']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Bulunduğu Kat' : 'Floor'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['bulundugu_kat']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['kat_sayisi']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Kat Sayısı' : 'Total Floors'; ?></span>
                                        <span class="pd-detail-value"><?php echo $listing['kat_sayisi']; ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['isitma_turu']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Isıtma Türü' : 'Heating'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['isitma_turu']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['tapu_durumu_konut']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Tapu Durumu' : 'Deed Status'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['tapu_durumu_konut']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['kullanim_durumu']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Kullanım Durumu' : 'Usage'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['kullanim_durumu']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['esyali']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Eşyalı' : 'Furnished'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['esyali']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['krediye_uygun']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Krediye Uygun' : 'Loan Eligible'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['krediye_uygun']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['fiyat_turu']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Fiyat Türü' : 'Price Type'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['fiyat_turu']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Bina Özellikleri -->
                        <?php
                        $building_features = [];
                        $bf_map = [
                            'balkon' => $lang == 'tr' ? 'Balkon' : 'Balcony',
                            'asansor' => $lang == 'tr' ? 'Asansör' : 'Elevator',
                            'guvenlik' => $lang == 'tr' ? 'Güvenlik' : 'Security',
                            'kamera_sistemi' => $lang == 'tr' ? 'Kamera Sistemi' : 'Camera System',
                            'kapici' => $lang == 'tr' ? 'Kapıcı' : 'Concierge',
                            'isi_yalitim' => $lang == 'tr' ? 'Isı Yalıtımı' : 'Heat Insulation'
                        ];
                        foreach ($bf_map as $key => $label) {
                            if ($listing[$key] && $listing[$key] == 'Var') $building_features[] = $label;
                        }
                        if ($listing['site_icinde'] && $listing['site_icinde'] == 'Evet') $building_features[] = $lang == 'tr' ? 'Site İçinde' : 'Gated Community';
                        if ($listing['otopark'] && $listing['otopark'] != 'Yok') $building_features[] = ($lang == 'tr' ? 'Otopark: ' : 'Parking: ') . htmlspecialchars($listing['otopark']);

                        $location_features = [];
                        $lf_map = [
                            'okula_yakin' => $lang == 'tr' ? 'Okula Yakın' : 'Near School',
                            'hastaneye_yakin' => $lang == 'tr' ? 'Hastaneye Yakın' : 'Near Hospital',
                            'market_yakin' => $lang == 'tr' ? "Market/AVM'ye Yakın" : 'Near Market/Mall',
                            'toplu_ulasim' => $lang == 'tr' ? 'Toplu Ulaşıma Yakın' : 'Near Public Transport',
                            'cadde_uzerinde' => $lang == 'tr' ? 'Cadde Üzerinde' : 'On Main Street'
                        ];
                        foreach ($lf_map as $key => $label) {
                            if ($listing[$key] && $listing[$key] == 'Evet') $location_features[] = $label;
                        }
                        if ($listing['cephe']) $location_features[] = ($lang == 'tr' ? 'Cephe: ' : 'Facing: ') . htmlspecialchars($listing['cephe']);
                        ?>

                        <?php if (!empty($building_features) || !empty($location_features)): ?>
                            <div class="pd-section-card">
                                <?php if (!empty($building_features)): ?>
                                    <h2 class="pd-section-title">
                                        <span>🏗️</span>
                                        <?php echo $lang == 'tr' ? 'Bina Özellikleri' : 'Building Features'; ?>
                                    </h2>
                                    <div class="pd-features-wrap">
                                        <?php foreach ($building_features as $feat): ?>
                                            <div class="pd-feature-tag">
                                                <span class="check">✓</span>
                                                <?php echo $feat; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($location_features)): ?>
                                    <h2 class="pd-section-title" style="margin-top: 2rem;">
                                        <span>📍</span>
                                        <?php echo $lang == 'tr' ? 'Konum Özellikleri' : 'Location Features'; ?>
                                    </h2>
                                    <div class="pd-features-wrap">
                                        <?php foreach ($location_features as $feat): ?>
                                            <div class="pd-feature-tag">
                                                <span class="check">✓</span>
                                                <?php echo $feat; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- ── Arsa Detayları ── -->
                    <?php if ($is_land): ?>
                        <div class="pd-section-card">
                            <h2 class="pd-section-title">
                                <span>🌳</span>
                                <?php echo $lang == 'tr' ? 'Arsa Detayları' : 'Land Details'; ?>
                            </h2>
                            <div class="pd-details-grid">
                                <?php if ($listing['price_per_sqm']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'm² Birim Fiyatı' : 'Price per m²'; ?></span>
                                        <span class="pd-detail-value"><?php echo number_format($listing['price_per_sqm'], 0, ',', '.'); ?> TL/m²</span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['fiyat_turu']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Fiyat Türü' : 'Price Type'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['fiyat_turu']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['imar_durumu']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'İmar Durumu' : 'Zoning Status'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['imar_durumu']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['tapu_durumu']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Tapu Durumu' : 'Deed Status'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['tapu_durumu']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['krediye_uygun']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Krediye Uygun' : 'Loan Eligible'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['krediye_uygun']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['takas']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Takas' : 'Exchange'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['takas']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['ada_no']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Ada No' : 'Block No'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['ada_no']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['parsel_no']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Parsel No' : 'Parcel No'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['parsel_no']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Altyapı -->
                            <?php
                            $infra = [];
                            $infra_map = [
                                'elektrik' => $lang == 'tr' ? 'Elektrik' : 'Electricity',
                                'su' => $lang == 'tr' ? 'Su' : 'Water',
                                'kanalizasyon' => $lang == 'tr' ? 'Kanalizasyon' : 'Sewer',
                                'dogalgaz' => $lang == 'tr' ? 'Doğalgaz' : 'Natural Gas',
                                'telefon' => $lang == 'tr' ? 'Telefon' : 'Telephone'
                            ];
                            foreach ($infra_map as $key => $label) {
                                if ($listing[$key] && $listing[$key] == 'Var') $infra[] = $label;
                            }
                            if ($listing['yolu_acilmis'] && $listing['yolu_acilmis'] == 'Evet') $infra[] = $lang == 'tr' ? 'Yolu Açılmış' : 'Road Access';
                            if ($listing['parselli'] && $listing['parselli'] == 'Evet') $infra[] = $lang == 'tr' ? 'Parselli' : 'Parcelled';
                            if ($listing['ifrazli'] && $listing['ifrazli'] == 'Evet') $infra[] = $lang == 'tr' ? 'İfrazlı' : 'Subdivided';
                            ?>
                            <?php if (!empty($infra)): ?>
                                <h2 class="pd-section-title" style="margin-top: 2rem;">
                                    <span>⚡</span>
                                    <?php echo $lang == 'tr' ? 'Altyapı Özellikleri' : 'Infrastructure'; ?>
                                </h2>
                                <div class="pd-features-wrap">
                                    <?php foreach ($infra as $feat): ?>
                                        <div class="pd-feature-tag">
                                            <span class="check">✓</span>
                                            <?php echo $feat; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Arsa Konum -->
                            <?php
                            $land_loc = [];
                            if ($listing['cadde_uzerinde'] && $listing['cadde_uzerinde'] == 'Evet') $land_loc[] = $lang == 'tr' ? 'Cadde Üzerinde' : 'On Main Street';
                            if ($listing['toplu_ulasim'] && $listing['toplu_ulasim'] == 'Evet') $land_loc[] = $lang == 'tr' ? 'Toplu Ulaşıma Yakın' : 'Near Public Transport';
                            if ($listing['merkeze_yakin'] && $listing['merkeze_yakin'] == 'Evet') $land_loc[] = $lang == 'tr' ? 'Merkeze Yakın' : 'Near Center';
                            if ($listing['doga_manzara'] && $listing['doga_manzara'] == 'Evet') $land_loc[] = $lang == 'tr' ? 'Doğa Manzaralı' : 'Nature View';
                            if ($listing['manzara_tipi']) $land_loc[] = ($lang == 'tr' ? 'Manzara: ' : 'View: ') . htmlspecialchars($listing['manzara_tipi']);
                            ?>
                            <?php if (!empty($land_loc)): ?>
                                <h2 class="pd-section-title" style="margin-top: 2rem;">
                                    <span>📍</span>
                                    <?php echo $lang == 'tr' ? 'Konum Özellikleri' : 'Location Features'; ?>
                                </h2>
                                <div class="pd-features-wrap">
                                    <?php foreach ($land_loc as $feat): ?>
                                        <div class="pd-feature-tag">
                                            <span class="check">✓</span>
                                            <?php echo $feat; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- ── Ticari İşyeri Detayları ── -->
                    <?php if ($is_commercial): ?>
                        <div class="pd-section-card">
                            <h2 class="pd-section-title">
                                <span>🏪</span>
                                <?php echo $lang == 'tr' ? 'İşyeri Detayları' : 'Commercial Details'; ?>
                            </h2>
                            <div class="pd-details-grid">
                                <?php if ($listing['toplam_alan']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Toplam Alan' : 'Total Area'; ?></span>
                                        <span class="pd-detail-value"><?php echo $listing['toplam_alan']; ?> m²</span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['kullanim_alani']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Kullanım Alanı' : 'Usable Area'; ?></span>
                                        <span class="pd-detail-value"><?php echo $listing['kullanim_alani']; ?> m²</span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['zemin_turu']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Zemin Türü' : 'Floor Type'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['zemin_turu']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['on_cephe_uzunlugu']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Ön Cephe Uzunluğu' : 'Frontage'; ?></span>
                                        <span class="pd-detail-value"><?php echo $listing['on_cephe_uzunlugu']; ?> m</span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['giris_yuksekligi']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Giriş Yüksekliği' : 'Entry Height'; ?></span>
                                        <span class="pd-detail-value"><?php echo $listing['giris_yuksekligi']; ?> m</span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['kullanim_durumu_ticari']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Kullanım Durumu' : 'Usage'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['kullanim_durumu_ticari']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['fiyat_turu']): ?>
                                    <div class="pd-detail-item">
                                        <span class="pd-detail-label"><?php echo $lang == 'tr' ? 'Fiyat Türü' : 'Price Type'; ?></span>
                                        <span class="pd-detail-value"><?php echo htmlspecialchars($listing['fiyat_turu']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- İşyeri Özellikleri -->
                            <?php
                            $comm_feats = [];
                            $cf_map = [
                                'wc_lavabo' => 'WC/Lavabo',
                                'mutfak_ticari' => $lang == 'tr' ? 'Mutfak' : 'Kitchen',
                                'vitrin_cami' => $lang == 'tr' ? 'Vitrin Camı' : 'Display Window',
                                'yuk_asansor' => $lang == 'tr' ? 'Yük Asansörü' : 'Freight Elevator',
                                'depo_alani' => $lang == 'tr' ? 'Depo Alanı' : 'Storage Area',
                                'yalitim' => $lang == 'tr' ? 'Yalıtım' : 'Insulation'
                            ];
                            foreach ($cf_map as $key => $label) {
                                if ($listing[$key] && $listing[$key] == 'Var') $comm_feats[] = $label;
                            }
                            if ($listing['otopark'] && $listing['otopark'] != 'Yok') $comm_feats[] = ($lang == 'tr' ? 'Otopark: ' : 'Parking: ') . htmlspecialchars($listing['otopark']);
                            if ($listing['cadde_uzerinde'] && $listing['cadde_uzerinde'] == 'Evet') $comm_feats[] = $lang == 'tr' ? 'Cadde Üzerinde' : 'On Main Street';
                            if ($listing['ana_yola_cephe'] && $listing['ana_yola_cephe'] == 'Evet') $comm_feats[] = $lang == 'tr' ? 'Ana Yola Cephe' : 'Main Road Frontage';
                            if ($listing['toplu_ulasim'] && $listing['toplu_ulasim'] == 'Evet') $comm_feats[] = $lang == 'tr' ? 'Toplu Ulaşıma Yakın' : 'Near Public Transport';
                            if ($listing['merkeze_yakin'] && $listing['merkeze_yakin'] == 'Evet') $comm_feats[] = $lang == 'tr' ? 'Merkeze Yakın' : 'Near Center';
                            ?>
                            <?php if (!empty($comm_feats)): ?>
                                <h2 class="pd-section-title" style="margin-top: 2rem;">
                                    <span>✨</span>
                                    <?php echo $lang == 'tr' ? 'Özellikler' : 'Features'; ?>
                                </h2>
                                <div class="pd-features-wrap">
                                    <?php foreach ($comm_feats as $feat): ?>
                                        <div class="pd-feature-tag">
                                            <span class="check">✓</span>
                                            <?php echo $feat; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- ── Site Özellikleri ── -->
                    <?php
                    $site_features = [
                        'site_guvenlik' => ['icon' => '🛡️', 'tr' => '24 Saat Güvenlik', 'en' => '24h Security'],
                        'site_spor_salonu' => ['icon' => '🏋️', 'tr' => 'Spor Salonu', 'en' => 'Gym'],
                        'site_yuzme_havuzu' => ['icon' => '🏊', 'tr' => 'Yüzme Havuzu', 'en' => 'Swimming Pool'],
                        'site_cocuk_parki' => ['icon' => '🧒', 'tr' => 'Çocuk Parkı', 'en' => 'Playground'],
                        'site_sauna' => ['icon' => '♨️', 'tr' => 'Sauna', 'en' => 'Sauna'],
                        'site_turk_hamami' => ['icon' => '🧖', 'tr' => 'Türk Hamamı', 'en' => 'Turkish Bath'],
                        'site_jenerator' => ['icon' => '⚡', 'tr' => 'Jeneratör', 'en' => 'Generator'],
                        'site_kapali_otopark' => ['icon' => '🏠', 'tr' => 'Kapalı Otopark', 'en' => 'Indoor Parking'],
                        'site_acik_otopark' => ['icon' => '🅿️', 'tr' => 'Açık Otopark', 'en' => 'Outdoor Parking'],
                        'site_tenis_kortu' => ['icon' => '🎾', 'tr' => 'Tenis Kortu', 'en' => 'Tennis Court'],
                        'site_basketbol_sahasi' => ['icon' => '🏀', 'tr' => 'Basketbol Sahası', 'en' => 'Basketball Court'],
                        'site_market' => ['icon' => '🛒', 'tr' => 'Market', 'en' => 'Market'],
                        'site_kres' => ['icon' => '👶', 'tr' => 'Kreş', 'en' => 'Daycare'],
                        'site_cafe' => ['icon' => '☕', 'tr' => 'Cafe', 'en' => 'Cafe'],
                        'site_restorant' => ['icon' => '🍽️', 'tr' => 'Restoran', 'en' => 'Restaurant'],
                        'site_kuafor' => ['icon' => '💇', 'tr' => 'Kuaför', 'en' => 'Hairdresser'],
                        'site_toplanti_odasi' => ['icon' => '👥', 'tr' => 'Toplantı Odası', 'en' => 'Meeting Room'],
                        'site_bahce' => ['icon' => '🌳', 'tr' => 'Bahçe', 'en' => 'Garden'],
                        'site_evcil_hayvan' => ['icon' => '🐾', 'tr' => 'Evcil Hayvan İzni', 'en' => 'Pet Friendly'],
                        'site_engelli_erisimi' => ['icon' => '♿', 'tr' => 'Engelli Erişimi', 'en' => 'Disabled Access']
                    ];

                    $active_site_features = [];
                    foreach ($site_features as $key => $feature) {
                        if (!empty($listing[$key]) && $listing[$key] == 1) {
                            $active_site_features[] = $feature;
                        }
                    }
                    ?>
                    <?php if (!empty($active_site_features)): ?>
                        <div class="pd-section-card">
                            <h2 class="pd-section-title">
                                <span>🏘️</span>
                                <?php echo $lang == 'tr' ? 'Site Özellikleri' : 'Site Amenities'; ?>
                            </h2>
                            <div class="pd-site-features">
                                <?php foreach ($active_site_features as $feature): ?>
                                    <div class="pd-site-feature">
                                        <span class="check-icon">✓</span>
                                        <span><?php echo $feature['icon']; ?> <?php echo $lang == 'tr' ? $feature['tr'] : $feature['en']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ── Harita ── -->
                    <?php if (!empty($listing['map_address'])): ?>
                        <div class="pd-section-card">
                            <h2 class="pd-section-title">
                                <span>🗺️</span>
                                <?php echo $lang == 'tr' ? 'Konum' : 'Location'; ?>
                            </h2>
                            <div class="pd-map-container">
                                <iframe allowfullscreen loading="lazy"
                                    src="https://maps.google.com/maps?q=<?php echo urlencode($listing['map_address']); ?>&t=&z=13&ie=UTF8&iwloc=&output=embed">
                                </iframe>
                            </div>
                            <div class="pd-map-address">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($listing['map_address']); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ── Açıklama ── -->
                    <div class="pd-section-card">
                        <h2 class="pd-section-title" data-i18n="description">
                            <span>📝</span>
                            <?php echo t('description'); ?>
                        </h2>
                        <div class="pd-description-text">
                            <p><?php echo nl2br(htmlspecialchars($listing['description_' . $lang])); ?></p>
                        </div>
                    </div>

                    <!-- ── Notlar ── -->
                    <?php if ($listing['notes']): ?>
                        <div class="pd-section-card">
                            <h2 class="pd-section-title" data-i18n="notes">
                                <span>📋</span>
                                <?php echo $lang == 'tr' ? 'Notlar ve Ek Bilgiler' : 'Notes'; ?>
                            </h2>
                            <div class="pd-description-text">
                                <p><?php echo nl2br(htmlspecialchars($listing['notes'])); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ── Yorumlar ── -->
                    <div class="pd-comments-card" id="comments">
                        <h2 class="pd-section-title">
                            <span>💬</span>
                            <?php echo $lang == 'tr' ? 'Yorumlar' : 'Comments'; ?> (<span id="comment-count"><?php echo count($comments); ?></span>)
                        </h2>

                        <div class="pd-comment-list" id="comments-container">
                            <?php if (empty($comments)): ?>
                                <p class="pd-no-comments"><?php echo $lang == 'tr' ? 'Bu ilan için henüz yorum yapılmamış.' : 'No comments yet.'; ?></p>
                            <?php else: ?>
                                <?php foreach ($comments as $comment): ?>
                                    <div class="pd-comment-item">
                                        <div class="pd-comment-meta">
                                            <div class="pd-comment-author">
                                                <div class="pd-comment-avatar"><?php echo mb_substr($comment['user_name'], 0, 1, 'UTF-8'); ?></div>
                                                <span class="pd-comment-name"><?php echo htmlspecialchars(maskName($comment['user_name'])); ?></span>
                                            </div>
                                            <span class="pd-comment-date"><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></span>
                                        </div>
                                        <div class="pd-comment-text">
                                            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="pd-comment-form">
                                <textarea id="comment-content" 
                                    placeholder="<?php echo $lang == 'tr' ? 'Yorumunuzu buraya yazın...' : 'Write your comment here...'; ?>"></textarea>
                                <button id="btn-add-comment" class="pd-comment-submit">
                                    <i class="fas fa-comment"></i>
                                    <?php echo $lang == 'tr' ? 'Gönder' : 'Submit'; ?>
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="pd-login-prompt" style="margin-top: 1.5rem;">
                                <p><?php echo $lang == 'tr' ? 'Yorum yapmak için lütfen giriş yapın.' : 'Please log in to comment.'; ?></p>
                                <a href="/login" class="pd-login-link"><?php echo t('login'); ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- RIGHT SIDEBAR -->
                <div class="pd-sidebar">
                    <!-- Teklif Formu -->
                    <div class="pd-sidebar-card" id="pd-offer">
                        <h3 class="pd-sidebar-title" data-i18n="offer">
                            <i class="fas fa-paper-plane" style="color: #D3AF37;"></i>
                            <?php echo t('offer'); ?>
                        </h3>
                        <form action="teklif.php" method="POST" class="pd-offer-form">
                            <input type="hidden" name="listing_id" value="<?php echo $listing['id']; ?>">
                            <div class="pd-form-row">
                                <div class="pd-form-group">
                                    <label data-i18n-form-label="your_name"><?php echo t('your_name'); ?> *</label>
                                    <input type="text" name="customer_name" required>
                                </div>
                                <div class="pd-form-group">
                                    <label data-i18n-form-label="your_email"><?php echo t('your_email'); ?> *</label>
                                    <input type="email" name="customer_email" required>
                                </div>
                            </div>
                            <div class="pd-form-row">
                                <div class="pd-form-group">
                                    <label data-i18n-form-label="your_phone"><?php echo t('your_phone'); ?></label>
                                    <input type="tel" name="customer_phone">
                                </div>
                                <div class="pd-form-group">
                                    <label data-i18n-form-label="offer_amount"><?php echo t('offer_amount'); ?> *</label>
                                    <input type="number" name="offer_amount" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="pd-form-group">
                                <label data-i18n-form-label="message"><?php echo t('message'); ?></label>
                                <textarea name="message" rows="3" data-i18n-placeholder="message"
                                    placeholder="<?php echo $lang == 'tr' ? 'Mesajınızı buraya yazabilirsiniz...' : 'Write your message here...'; ?>"></textarea>
                            </div>
                            <button type="submit" class="pd-submit-btn" data-i18n-button="submit">
                                <i class="fas fa-paper-plane"></i>
                                <?php echo t('submit'); ?>
                            </button>
                        </form>
                    </div>

                    <!-- Direkt Mesaj -->
                    <?php if ($listing['user_id']): ?>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="pd-dm-card">
                                <h3 class="pd-sidebar-title">
                                    <i class="fas fa-comment-dots" style="color: #D3AF37;"></i>
                                    <?php echo $lang == 'tr' ? 'İlan Sahibine Mesaj' : 'Message Owner'; ?>
                                </h3>
                                <form id="dm-form" class="pd-dm-form">
                                    <input type="hidden" name="listing_id" value="<?php echo $listing['id']; ?>">
                                    <div class="pd-form-group">
                                        <textarea name="message" rows="3" required 
                                            placeholder="<?php echo $lang == 'tr' ? 'Sorularınızı buraya yazabilirsiniz...' : 'Write your questions here...'; ?>"></textarea>
                                    </div>
                                    <button type="submit" class="pd-submit-btn" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                                        <i class="fas fa-paper-plane"></i>
                                        <?php echo $lang == 'tr' ? 'Mesaj Gönder' : 'Send Message'; ?>
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="pd-sidebar-card" style="text-align: center;">
                                <p style="color: var(--gray-500); margin-bottom: 1rem; font-size: 0.9375rem;">
                                    <?php echo $lang == 'tr' ? 'İlan sahibiyle mesajlaşmak için giriş yapın.' : 'Log in to message the owner.'; ?>
                                </p>
                                <a href="/login" class="pd-login-link"><?php echo $lang == 'tr' ? 'Giriş Yap' : 'Log In'; ?></a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- ═══════ LIGHTBOX ═══════ -->
    <?php if (!empty($images) && count($images) > 0): ?>
    <div class="pd-lightbox" id="pdLightbox">
        <button class="pd-lightbox-close" onclick="closeLightbox()">
            <i class="fas fa-times"></i>
        </button>
        <?php if (count($images) > 1): ?>
            <button class="pd-lightbox-nav pd-lightbox-prev" onclick="navigateLightbox(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="pd-lightbox-nav pd-lightbox-next" onclick="navigateLightbox(1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        <?php endif; ?>
        <img id="pdLightboxImage" src="" alt="">
        <div class="pd-lightbox-counter" id="pdLightboxCounter"></div>
    </div>
    <?php endif; ?>

    <?php include 'includes/footer.php'; ?>
    <script src="/assets/js/main.js"></script>
    <script>
        // Property ID
        const PROPERTY_ID = <?php echo $listing['id']; ?>;

        // Utility for masking names
        function maskName(name) {
            if (!name) return "";
            return name.split(' ').map(part => {
                const arr = Array.from(part);
                if (arr.length > 1) {
                    return arr[0] + '*'.repeat(arr.length - 1);
                }
                return part;
            }).join(' ');
        }

        // Image gallery data
        const galleryImages = [
            <?php foreach ($images as $img): ?>
                '/uploads/<?php echo htmlspecialchars($img); ?>',
            <?php endforeach; ?>
        ];
        let currentImageIndex = 0;

        function changeImage(index) {
            if (index < 0 || index >= galleryImages.length) return;
            currentImageIndex = index;

            const mainImg = document.getElementById('pdMainImage');
            mainImg.style.opacity = '0';
            mainImg.style.transform = 'scale(1.02)';

            setTimeout(() => {
                mainImg.src = galleryImages[index];
                mainImg.style.opacity = '1';
                mainImg.style.transform = 'scale(1)';

                // Update counter
                const counter = document.getElementById('pdGalleryCounter');
                if (counter) {
                    counter.querySelector('span').textContent = (index + 1) + ' / ' + galleryImages.length;
                }

                // Update thumbnails
                document.querySelectorAll('.pd-thumb').forEach((thumb, i) => {
                    thumb.classList.toggle('active', i === index);
                });

                // Scroll thumbnail into view
                const activeThumb = document.querySelectorAll('.pd-thumb')[index];
                if (activeThumb) {
                    activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
            }, 200);
        }

        // Lightbox
        function openLightbox(index) {
            const lb = document.getElementById('pdLightbox');
            const img = document.getElementById('pdLightboxImage');
            const counter = document.getElementById('pdLightboxCounter');

            img.src = galleryImages[index];
            if (counter) counter.textContent = (index + 1) + ' / ' + galleryImages.length;
            lb.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            document.getElementById('pdLightbox').classList.remove('active');
            document.body.style.overflow = '';
        }

        function navigateLightbox(dir) {
            currentImageIndex = (currentImageIndex + dir + galleryImages.length) % galleryImages.length;
            const img = document.getElementById('pdLightboxImage');
            const counter = document.getElementById('pdLightboxCounter');
            img.src = galleryImages[currentImageIndex];
            if (counter) counter.textContent = (currentImageIndex + 1) + ' / ' + galleryImages.length;
            changeImage(currentImageIndex);
        }

        // Close lightbox on ESC
        document.addEventListener('keydown', (e) => {
            const lb = document.getElementById('pdLightbox');
            if (!lb) return;
            if (e.key === 'Escape') closeLightbox();
            if (lb.classList.contains('active')) {
                if (e.key === 'ArrowLeft') navigateLightbox(-1);
                if (e.key === 'ArrowRight') navigateLightbox(1);
            }
        });

        // Close lightbox on background click
        const pdLightbox = document.getElementById('pdLightbox');
        if (pdLightbox) {
            pdLightbox.addEventListener('click', (e) => {
                if (e.target === pdLightbox) closeLightbox();
            });
        }

        // Property.php'ye özel içerik güncelleme
        function updatePageContentSpecific(newLang) {
            return new Promise((resolve, reject) => {
                console.log('Updating property content for language:', newLang);
                fetch(`/get_property_content.php?id=${PROPERTY_ID}&lang=${newLang}`)
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error('HTTP error! status: ' + response.status);
                            });
                        }
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                throw new Error('Invalid JSON response');
                            }
                        });
                    })
                    .then(data => {
                        if (data && data.success) {
                            const content = data.content;
                            if (!content) { reject(new Error('Content is empty')); return; }

                            // Title
                            const titleEl = document.querySelector('.pd-title');
                            if (titleEl) titleEl.textContent = content.title;

                            // Type badge
                            const typeBadge = document.querySelector('.pd-badge-type');
                            if (typeBadge) typeBadge.textContent = content.property_type;

                            // Description
                            const descCards = document.querySelectorAll('.pd-description-text');
                            if (descCards.length > 0) {
                                descCards[0].querySelector('p').innerHTML = content.description.replace(/\n/g, '<br>');
                            }

                            // Back button
                            const backBtn = document.querySelector('.pd-back-btn');
                            if (backBtn) {
                                backBtn.innerHTML = `<i class="fas fa-arrow-left"></i> ${content.back_label}`;
                            }

                            console.log('Property content updated successfully');
                            resolve();
                        } else {
                            reject(new Error(data.message || 'Update failed'));
                        }
                    })
                    .catch(error => {
                        console.error('Content update error:', error);
                        reject(error);
                    });
            });
        }

        // Favori İşlemi
        const btnFavorite = document.getElementById('btn-favorite');
        if (btnFavorite) {
            btnFavorite.addEventListener('click', function() {
                const listingId = this.dataset.id;
                
                fetch('/api/toggle_favorite.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ listing_id: listingId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.classList.toggle('active', data.is_favorite);
                        const icon = this.querySelector('i');
                        if (data.is_favorite) {
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                        } else {
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                        }
                    } else {
                        if (data.message && data.message.includes('giriş')) {
                            window.location.href = '/login';
                        } else {
                            alert(data.message || 'Bir hata oluştu');
                        }
                    }
                });
            });
        }

        // Yorum Ekleme
        const btnAddComment = document.getElementById('btn-add-comment');
        if (btnAddComment) {
            btnAddComment.addEventListener('click', function() {
                const contentEl = document.getElementById('comment-content');
                const content = contentEl.value.trim();
                if (!content) return;

                this.disabled = true;
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';

                fetch('/api/add_comment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        listing_id: PROPERTY_ID,
                        content: content
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const container = document.getElementById('comments-container');
                        const noComments = container.querySelector('.pd-no-comments');
                        if (noComments) noComments.remove();

                        const initial = data.comment.user_name ? data.comment.user_name.charAt(0) : '?';
                        const commentHtml = `
                            <div class="pd-comment-item" style="animation: fadeUpIn 0.5s ease;">
                                <div class="pd-comment-meta">
                                    <div class="pd-comment-author">
                                        <div class="pd-comment-avatar">${initial}</div>
                                        <span class="pd-comment-name">${maskName(data.comment.user_name)}</span>
                                    </div>
                                    <span class="pd-comment-date">${data.comment.created_at}</span>
                                </div>
                                <div class="pd-comment-text">
                                    ${data.comment.content.replace(/\n/g, '<br>')}
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('afterbegin', commentHtml);
                        
                        const countEl = document.getElementById('comment-count');
                        countEl.textContent = parseInt(countEl.textContent) + 1;
                        contentEl.value = '';
                    } else {
                        alert(data.message);
                    }
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = originalText;
                });
            });
        }

        // Intersection Observer for card animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.pd-section-card, .pd-comments-card').forEach(card => {
            card.style.animationPlayState = 'paused';
            observer.observe(card);
        });
    </script>
</body>

</html>