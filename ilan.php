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
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="container">
            <a href="/ilanlar" class="btn btn-secondary" style="margin-bottom: 20px;" data-i18n="back">←
                <?php echo t('back'); ?></a>


            <!-- Resimler Galerisi - Üstte -->
            <div class="property-gallery-section">
                <?php if (empty($images)): ?>
                    <div class="property-image-single">
                        <img src="/assets/images/placeholder.jpg" alt="No Image" class="property-main-image">
                    </div>
                <?php else: ?>
                    <div class="property-image-main">
                        <img src="/uploads/<?php echo htmlspecialchars($images[0]); ?>"
                            alt="<?php echo htmlspecialchars($listing['title_' . $lang]); ?>" class="property-main-image"
                            id="mainImage">
                        <?php if (count($images) > 1): ?>
                            <div class="image-counter">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                                <span>1 / <?php echo count($images); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (count($images) > 1): ?>
                        <div class="property-thumbnails">
                            <?php foreach ($images as $index => $img): ?>
                                <div class="thumbnail-wrapper <?php echo $index === 0 ? 'active' : ''; ?>"
                                    onclick="changeMainImage('/uploads/<?php echo htmlspecialchars($img); ?>', <?php echo $index; ?>, <?php echo count($images); ?>)">
                                    <img src="/uploads/<?php echo htmlspecialchars($img); ?>"
                                        alt="Thumbnail <?php echo $index + 1; ?>" class="thumbnail-img">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Video Section -->
            <?php if (!empty($listing['video'])): ?>
                <div class="property-video-section" style="margin-top: 2rem;">
                    <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--gray-900);">
                        <?php echo $lang == 'tr' ? 'Mülk Videosu' : 'Property Video'; ?>
                    </h2>
                    <div class="video-container"
                        style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: var(--radius-2xl); box-shadow: var(--shadow-xl);">
                        <video controls
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: var(--radius-2xl);">
                            <source src="/uploads/<?php echo htmlspecialchars($listing['video']); ?>" type="video/mp4">
                            <source src="/uploads/<?php echo htmlspecialchars($listing['video']); ?>" type="video/webm">
                            <source src="/uploads/<?php echo htmlspecialchars($listing['video']); ?>" type="video/ogg">
                            <?php echo $lang == 'tr' ? 'Tarayıcınız video oynatmayı desteklemiyor.' : 'Your browser does not support video playback.'; ?>
                        </video>
                    </div>
                </div>
            <?php endif; ?>

            <!-- İlan Bilgileri ve Detaylar - Altta -->
            <div class="property-content-section">
                <!-- Başlık ve Fiyat Kartı -->
                <div class="property-header-card">
                    <div class="property-header-top">
                        <span class="property-type-badge"><?php echo t($listing['property_type']); ?></span>
                        <?php if (!empty($listing['listing_type'])): ?>
                            <span class="property-type-badge"
                                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <?php echo t($listing['listing_type']); ?>
                            </span>
                        <?php endif; ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                            <h1><?php echo htmlspecialchars($listing['title_' . $lang]); ?></h1>
                            <button id="btn-favorite" class="btn-favorite <?php echo $is_favorite ? 'active' : ''; ?>" data-id="<?php echo $listing['id']; ?>" title="<?php echo $lang == 'tr' ? 'Favorilere Ekle' : 'Add to Favorites'; ?>">
                                <i class="<?php echo $is_favorite ? 'fas' : 'far'; ?> fa-heart"></i>
                            </button>
                        </div>
                        <p class="property-location-text">📍 <?php echo htmlspecialchars($listing['city'] . ' / ' . $listing['district'] . ' / ' . $listing['mahalle']); ?></p>
                    </div>
                    <div class="property-price-card">
                        <span class="price-value"><?php echo number_format($listing['price'], 0, ',', '.'); ?> <?php echo t('currency'); ?></span>
                        <?php if (!empty($listing['owner_type']) && $listing['owner_type'] == 'emlakci' && !empty($listing['komisyon_yuzdesi'])): ?>
                            <div class="commission-info">
                                <span class="commission-label"><?php echo $lang == 'tr' ? 'Komisyon' : 'Commission'; ?>:</span>
                                <span class="commission-value">%<?php echo number_format($listing['komisyon_yuzdesi'], 1); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- İlan Sahibi Bilgi Kartı kaldırıldı -->


            <!-- Temel Özellikler Kartı -->
            <div class="property-basic-card">
                <h2 class="card-title" data-i18n="basic_info">
                    <?php echo $lang == 'tr' ? 'Temel Bilgiler' : 'Basic Information'; ?>
                </h2>
                <div class="basic-info-grid">
                    <?php if ($listing['area']): ?>
                        <div class="info-item">
                            <div class="info-icon">📐</div>
                            <div class="info-content">
                                <span class="info-label" data-i18n-label="area"><?php echo t('area'); ?></span>
                                <span class="info-value"><?php echo $listing['area']; ?> m²</span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($listing['rooms']): ?>
                        <div class="info-item">
                            <div class="info-icon">🛏️</div>
                            <div class="info-content">
                                <span class="info-label" data-i18n-label="rooms"><?php echo t('rooms'); ?></span>
                                <span class="info-value"><?php echo $listing['rooms']; ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($listing['bathrooms']): ?>
                        <div class="info-item">
                            <div class="info-icon">🚿</div>
                            <div class="info-content">
                                <span class="info-label" data-i18n-label="bathrooms"><?php echo t('bathrooms'); ?></span>
                                <span class="info-value"><?php echo $listing['bathrooms']; ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($listing['city']): ?>
                        <div class="info-item">
                            <div class="info-icon">📍</div>
                            <div class="info-content">
                                <span class="info-label" data-i18n-label="location"><?php echo t('location'); ?></span>
                                <span
                                    class="info-value"><?php echo htmlspecialchars($listing['city']); ?><?php echo $listing['district'] ? ', ' . htmlspecialchars($listing['district']) : ''; ?><?php echo $listing['mahalle'] ? ', ' . htmlspecialchars($listing['mahalle']) : ''; ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($listing['address']): ?>
                        <div class="info-item info-item-full">
                            <div class="info-icon">🏠</div>
                            <div class="info-content">
                                <span class="info-label" data-i18n-label="address"><?php echo t('address'); ?></span>
                                <span class="info-value"><?php echo htmlspecialchars($listing['address']); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detaylı Özellikler -->
            <?php
            $property_type = $listing['property_type'];
            $is_land = ($property_type == 'arsa');
            $is_residential = in_array($property_type, ['ev', 'daire', 'villa']);
            $is_commercial = ($property_type == 'dukkan');
            ?>

            <!-- Arsa Özellikleri -->
            <?php if ($is_land): ?>
                <div class="property-details-section">
                    <h2><?php echo $lang == 'tr' ? 'Arsa Detayları' : 'Land Details'; ?></h2>
                    <div class="details-grid">
                        <?php if ($listing['price_per_sqm']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'm² Birim Fiyatı' : 'Price per m²'; ?>:</strong>
                                <span><?php echo number_format($listing['price_per_sqm'], 0, ',', '.'); ?> TL/m²</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['fiyat_turu']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Fiyat Türü' : 'Price Type'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['fiyat_turu']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['imar_durumu']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'İmar Durumu' : 'Zoning Status'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['imar_durumu']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['tapu_durumu']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Tapu Durumu' : 'Deed Status'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['tapu_durumu']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['krediye_uygun']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Krediye Uygun' : 'Loan Eligible'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['krediye_uygun']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['takas']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Takas' : 'Exchange'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['takas']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['ada_no']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Ada No' : 'Block No'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['ada_no']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['parsel_no']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Parsel No' : 'Parcel No'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['parsel_no']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Altyapı Özellikleri -->
                    <h3><?php echo $lang == 'tr' ? 'Altyapı Özellikleri' : 'Infrastructure Features'; ?></h3>
                    <div class="details-grid">
                        <?php
                        $infrastructure = [
                            'elektrik' => $lang == 'tr' ? 'Elektrik' : 'Electricity',
                            'su' => $lang == 'tr' ? 'Su' : 'Water',
                            'kanalizasyon' => $lang == 'tr' ? 'Kanalizasyon' : 'Sewer',
                            'dogalgaz' => $lang == 'tr' ? 'Doğalgaz' : 'Natural Gas',
                            'telefon' => $lang == 'tr' ? 'Telefon' : 'Telephone'
                        ];
                        foreach ($infrastructure as $key => $label):
                            if ($listing[$key] && $listing[$key] == 'Var'):
                                ?>
                                <div class="detail-item">
                                    <span class="check-icon">✓</span>
                                    <span><?php echo $label; ?></span>
                                </div>
                                <?php
                            endif;
                        endforeach;
                        ?>
                        <?php if ($listing['yolu_acilmis'] && $listing['yolu_acilmis'] == 'Evet'): ?>
                            <div class="detail-item">
                                <span class="check-icon">✓</span>
                                <span><?php echo $lang == 'tr' ? 'Yolu Açılmış' : 'Road Access'; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['parselli'] && $listing['parselli'] == 'Evet'): ?>
                            <div class="detail-item">
                                <span class="check-icon">✓</span>
                                <span><?php echo $lang == 'tr' ? 'Parselli' : 'Parcelled'; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['ifrazli'] && $listing['ifrazli'] == 'Evet'): ?>
                            <div class="detail-item">
                                <span class="check-icon">✓</span>
                                <span><?php echo $lang == 'tr' ? 'İfrazlı' : 'Subdivided'; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Konum Özellikleri -->
                    <h3><?php echo $lang == 'tr' ? 'Konum Özellikleri' : 'Location Features'; ?></h3>
                    <div class="details-grid">
                        <?php if ($listing['cadde_uzerinde'] && $listing['cadde_uzerinde'] == 'Evet'): ?>
                            <div class="detail-item">
                                <span class="check-icon">✓</span>
                                <span><?php echo $lang == 'tr' ? 'Cadde Üzerinde' : 'On Main Street'; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['toplu_ulasim'] && $listing['toplu_ulasim'] == 'Evet'): ?>
                            <div class="detail-item">
                                <span class="check-icon">✓</span>
                                <span><?php echo $lang == 'tr' ? 'Toplu Ulaşıma Yakın' : 'Near Public Transport'; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['merkeze_yakin'] && $listing['merkeze_yakin'] == 'Evet'): ?>
                            <div class="detail-item">
                                <span class="check-icon">✓</span>
                                <span><?php echo $lang == 'tr' ? 'Merkeze Yakın' : 'Near Center'; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['doga_manzara'] && $listing['doga_manzara'] == 'Evet'): ?>
                            <div class="detail-item">
                                <span class="check-icon">✓</span>
                                <span><?php echo $lang == 'tr' ? 'Doğa Manzaralı' : 'Nature View'; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['manzara_tipi']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Manzara Tipi' : 'View Type'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['manzara_tipi']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Konut Özellikleri (Ev, Daire, Villa) -->
            <?php if ($is_residential): ?>
                <div class="property-details-section">
                    <h2><?php echo $lang == 'tr' ? 'Konut Detayları' : 'Residential Details'; ?></h2>
                    <div class="details-grid">
                        <?php if ($listing['brut_metrekare']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Brüt m²' : 'Gross m²'; ?>:</strong>
                                <span><?php echo $listing['brut_metrekare']; ?> m²</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['net_metrekare']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Net m²' : 'Net m²'; ?>:</strong>
                                <span><?php echo $listing['net_metrekare']; ?> m²</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['oda_sayisi']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Oda Sayısı' : 'Number of Rooms'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['oda_sayisi']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['bina_yasi']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Bina Yaşı' : 'Building Age'; ?>:</strong>
                                <span><?php echo $listing['bina_yasi']; ?>
                                    <?php echo $lang == 'tr' ? 'Yıl' : 'Years'; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['bulundugu_kat']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Bulunduğu Kat' : 'Floor'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['bulundugu_kat']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['kat_sayisi']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Kat Sayısı' : 'Total Floors'; ?>:</strong>
                                <span><?php echo $listing['kat_sayisi']; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['isitma_turu']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Isıtma Türü' : 'Heating Type'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['isitma_turu']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['tapu_durumu_konut']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Tapu Durumu' : 'Deed Status'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['tapu_durumu_konut']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['kullanim_durumu']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Kullanım Durumu' : 'Usage Status'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['kullanim_durumu']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['esyali']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Eşyalı' : 'Furnished'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['esyali']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['krediye_uygun']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Krediye Uygun' : 'Loan Eligible'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['krediye_uygun']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['fiyat_turu']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Fiyat Türü' : 'Price Type'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['fiyat_turu']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Bina Özellikleri -->
                    <h3><?php echo $lang == 'tr' ? 'Bina Özellikleri' : 'Building Features'; ?></h3>
                    <div class="details-grid">
                        <?php
                        $building_features = [
                            'balkon' => $lang == 'tr' ? 'Balkon' : 'Balcony',
                            'asansor' => $lang == 'tr' ? 'Asansör' : 'Elevator',
                            'guvenlik' => $lang == 'tr' ? 'Güvenlik' : 'Security',
                            'kamera_sistemi' => $lang == 'tr' ? 'Kamera Sistemi' : 'Camera System',
                            'kapici' => $lang == 'tr' ? 'Kapıcı' : 'Concierge',
                            'isi_yalitim' => $lang == 'tr' ? 'Isı Yalıtımı' : 'Heat Insulation'
                        ];
                        foreach ($building_features as $key => $label):
                            if ($listing[$key] && $listing[$key] == 'Var'):
                                ?>
                                <div class="detail-item">
                                    <span class="check-icon">✓</span>
                                    <span><?php echo $label; ?></span>
                                </div>
                                <?php
                            endif;
                        endforeach;
                        ?>
                        <?php if ($listing['otopark'] && $listing['otopark'] != 'Yok'): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Otopark' : 'Parking'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['otopark']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['site_icinde'] && $listing['site_icinde'] == 'Evet'): ?>
                            <div class="detail-item">
                                <span class="check-icon">✓</span>
                                <span><?php echo $lang == 'tr' ? 'Site İçinde' : 'Gated Community'; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Konum Özellikleri -->
                    <h3><?php echo $lang == 'tr' ? 'Konum Özellikleri' : 'Location Features'; ?></h3>
                    <div class="details-grid">
                        <?php
                        $location_features = [
                            'okula_yakin' => $lang == 'tr' ? 'Okula Yakın' : 'Near School',
                            'hastaneye_yakin' => $lang == 'tr' ? 'Hastaneye Yakın' : 'Near Hospital',
                            'market_yakin' => $lang == 'tr' ? 'Market/AVM\'ye Yakın' : 'Near Market/Mall',
                            'toplu_ulasim' => $lang == 'tr' ? 'Toplu Ulaşıma Yakın' : 'Near Public Transport',
                            'cadde_uzerinde' => $lang == 'tr' ? 'Cadde Üzerinde' : 'On Main Street'
                        ];
                        foreach ($location_features as $key => $label):
                            if ($listing[$key] && $listing[$key] == 'Evet'):
                                ?>
                                <div class="detail-item">
                                    <span class="check-icon">✓</span>
                                    <span><?php echo $label; ?></span>
                                </div>
                                <?php
                            endif;
                        endforeach;
                        ?>
                        <?php if ($listing['merkeze_uzaklik']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Merkeze Uzaklık' : 'Distance to Center'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['merkeze_uzaklik']); ?> m</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['cephe']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Cephe' : 'Facing'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['cephe']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Site Özellikleri -->
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

                $has_site_features = false;
                foreach ($site_features as $key => $feature) {
                    if (!empty($listing[$key]) && $listing[$key] == 1) {
                        $has_site_features = true;
                        break;
                    }
                }

                if ($has_site_features):
                    ?>
                    <div class="property-details-section site-features-section">
                        <h2><?php echo $lang == 'tr' ? 'Site Özellikleri' : 'Site Amenities'; ?></h2>
                        <div class="details-grid">
                            <?php foreach ($site_features as $key => $feature): ?>
                                <?php if (!empty($listing[$key]) && $listing[$key] == 1): ?>
                                    <div class="detail-item">
                                        <span class="check-icon">✓</span>
                                        <span><?php echo $lang == 'tr' ? $feature['tr'] : $feature['en']; ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Ticari İşyeri Özellikleri (Dükkan) -->
            <?php if ($is_commercial): ?>
                <div class="property-details-section">
                    <h2><?php echo $lang == 'tr' ? 'İşyeri Detayları' : 'Commercial Property Details'; ?></h2>
                    <div class="details-grid">
                        <?php if ($listing['toplam_alan']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Toplam Alan' : 'Total Area'; ?>:</strong>
                                <span><?php echo $listing['toplam_alan']; ?> m²</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['kullanim_alani']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Kullanım Alanı' : 'Usable Area'; ?>:</strong>
                                <span><?php echo $listing['kullanim_alani']; ?> m²</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['zemin_turu']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Zemin Türü' : 'Floor Type'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['zemin_turu']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['on_cephe_uzunlugu']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Ön Cephe Uzunluğu' : 'Frontage Length'; ?>:</strong>
                                <span><?php echo $listing['on_cephe_uzunlugu']; ?> m</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['giris_yuksekligi']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Giriş Yüksekliği' : 'Entrance Height'; ?>:</strong>
                                <span><?php echo $listing['giris_yuksekligi']; ?> m</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['kullanim_durumu_ticari']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Kullanım Durumu' : 'Usage Status'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['kullanim_durumu_ticari']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['fiyat_turu']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Fiyat Türü' : 'Price Type'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['fiyat_turu']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- İşyeri Özellikleri -->
                    <h3><?php echo $lang == 'tr' ? 'İşyeri Özellikleri' : 'Commercial Features'; ?></h3>
                    <div class="details-grid">
                        <?php
                        $commercial_features = [
                            'wc_lavabo' => $lang == 'tr' ? 'WC/Lavabo' : 'WC/Sink',
                            'mutfak_ticari' => $lang == 'tr' ? 'Mutfak' : 'Kitchen',
                            'vitrin_cami' => $lang == 'tr' ? 'Vitrin Camı' : 'Display Window',
                            'yuk_asansor' => $lang == 'tr' ? 'Yük Asansörü' : 'Freight Elevator',
                            'depo_alani' => $lang == 'tr' ? 'Depo Alanı' : 'Storage Area',
                            'yalitim' => $lang == 'tr' ? 'Yalıtım' : 'Insulation'
                        ];
                        foreach ($commercial_features as $key => $label):
                            if ($listing[$key] && $listing[$key] == 'Var'):
                                ?>
                                <div class="detail-item">
                                    <span class="check-icon">✓</span>
                                    <span><?php echo $label; ?></span>
                                </div>
                                <?php
                            endif;
                        endforeach;
                        ?>
                        <?php if ($listing['tabela_gorunurluk']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Tabela Görünürlüğü' : 'Sign Visibility'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['tabela_gorunurluk']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['yaya_trafik']): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Yaya Trafiği' : 'Pedestrian Traffic'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['yaya_trafik']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['otopark'] && $listing['otopark'] != 'Yok'): ?>
                            <div class="detail-item">
                                <strong><?php echo $lang == 'tr' ? 'Otopark' : 'Parking'; ?>:</strong>
                                <span><?php echo htmlspecialchars($listing['otopark']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Konum Özellikleri -->
                    <h3><?php echo $lang == 'tr' ? 'Konum Özellikleri' : 'Location Features'; ?></h3>
                    <div class="details-grid">
                        <?php if ($listing['cadde_uzerinde'] && $listing['cadde_uzerinde'] == 'Evet'): ?>
                            <div class="detail-item">
                                <span class="check-icon">✓</span>
                                <span><?php echo $lang == 'tr' ? 'Cadde Üzerinde' : 'On Main Street'; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['ana_yola_cephe'] && $listing['ana_yola_cephe'] == 'Evet'): ?>
                            <div class="detail-item">
                                <span class="check-icon">✓</span>
                                <span><?php echo $lang == 'tr' ? 'Ana Yola Cephe' : 'Main Road Frontage'; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['toplu_ulasim'] && $listing['toplu_ulasim'] == 'Evet'): ?>
                            <div class="detail-item">
                                <span class="check-icon">✓</span>
                                <span><?php echo $lang == 'tr' ? 'Toplu Ulaşıma Yakın' : 'Near Public Transport'; ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($listing['merkeze_yakin'] && $listing['merkeze_yakin'] == 'Evet'): ?>
                            <div class="detail-item">
                                <span class="check-icon">✓</span>
                                <span><?php echo $lang == 'tr' ? 'Merkeze Yakın' : 'Near Center'; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Harita -->
            <?php if (!empty($listing['map_address'])): ?>
                <div class="property-description-card" style="margin-bottom: 2rem;">
                    <h2 class="card-title"><?php echo $lang == 'tr' ? 'Konum' : 'Location'; ?></h2>
                    <div class="map-container"
                        style="border-radius: 12px; overflow: hidden; height: 400px; box-shadow: var(--shadow-md);">
                        <iframe width="100%" height="100%" frameborder="0" style="border:0"
                            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyA_...&q=<?php echo urlencode($listing['map_address']); ?>"
                            allowfullscreen>
                        </iframe>
                        <div style="margin-top: 10px; font-size: 14px; color: #666;">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($listing['map_address']); ?>
                        </div>
                    </div>
                </div>
                <!-- Embed without API key fallback (since I don't have their API key, using the public embed URL) -->
                <script>
                    document.querySelector('.map-container iframe').src = "https://maps.google.com/maps?q=" + encodeURIComponent("<?php echo $listing['map_address']; ?>") + "&t=&z=13&ie=UTF8&iwloc=&output=embed";
                </script>
            <?php endif; ?>

            <!-- Açıklama -->
            <div class="property-description-card">
                <h2 class="card-title" data-i18n="description"><?php echo t('description'); ?></h2>
                <div class="description-content">
                    <p><?php echo nl2br(htmlspecialchars($listing['description_' . $lang])); ?></p>
                </div>
            </div>

            <!-- Notlar -->
            <?php if ($listing['notes']): ?>
                <div class="property-description-card">
                    <h2 class="card-title" data-i18n="notes">
                        <?php echo $lang == 'tr' ? 'Notlar ve Ek Bilgiler' : 'Notes and Additional Information'; ?>
                    </h2>
                    <div class="description-content">
                        <p><?php echo nl2br(htmlspecialchars($listing['notes'])); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Yorumlar Bölümü -->
            <div class="property-description-card comments-section" id="comments">
                <h2 class="card-title">💬 <?php echo $lang == 'tr' ? 'Yorumlar' : 'Comments'; ?> (<span id="comment-count"><?php echo count($comments); ?></span>)</h2>
                
                <div class="comments-list" id="comments-container">
                    <?php if (empty($comments)): ?>
                        <p class="no-comments"><?php echo $lang == 'tr' ? 'Bu ilan için henüz yorum yapılmamış.' : 'No comments for this listing yet.'; ?></p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment-item">
                                <div class="comment-header">
                                    <span class="comment-user">👤 <?php echo htmlspecialchars($comment['user_name']); ?></span>
                                    <span class="comment-date"><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></span>
                                </div>
                                <div class="comment-body">
                                    <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="add-comment-form">
                        <h3><?php echo $lang == 'tr' ? 'Yorum Yap' : 'Add Comment'; ?></h3>
                        <div class="form-group">
                            <textarea id="comment-content" rows="3" class="form-control" placeholder="<?php echo $lang == 'tr' ? 'Yorumunuzu buraya yazın...' : 'Write your comment here...'; ?>"></textarea>
                        </div>
                        <button id="btn-add-comment" class="btn btn-primary" style="margin-top: 10px;">
                            <i class="fas fa-comment"></i> <?php echo $lang == 'tr' ? 'Gönder' : 'Submit'; ?>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="login-prompt" style="padding: 1rem; background: var(--gray-50); border-radius: 8px; text-align: center;">
                        <p><?php echo $lang == 'tr' ? 'Yorum yapmak için lütfen giriş yapın.' : 'Please log in to leave a comment.'; ?></p>
                        <a href="/login" class="btn btn-secondary btn-small" style="margin-top: 10px;"><?php echo t('login'); ?></a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Teklif Formu -->
            <div class="offer-form-card">
                <h2 class="card-title" data-i18n="offer"><?php echo t('offer'); ?></h2>
                <form action="teklif.php" method="POST" class="offer-form-modern">
                    <input type="hidden" name="listing_id" value="<?php echo $listing['id']; ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label data-i18n-form-label="your_name"><?php echo t('your_name'); ?> *</label>
                            <input type="text" name="customer_name" required>
                        </div>

                        <div class="form-group">
                            <label data-i18n-form-label="your_email"><?php echo t('your_email'); ?> *</label>
                            <input type="email" name="customer_email" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label data-i18n-form-label="your_phone"><?php echo t('your_phone'); ?></label>
                            <input type="tel" name="customer_phone">
                        </div>

                        <div class="form-group">
                            <label data-i18n-form-label="offer_amount"><?php echo t('offer_amount'); ?>
                                (<?php echo t('currency'); ?>) *</label>
                            <input type="number" name="offer_amount" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label data-i18n-form-label="message"><?php echo t('message'); ?></label>
                        <textarea name="message" rows="4" data-i18n-placeholder="message"
                            placeholder="<?php echo $lang == 'tr' ? 'Mesajınızı buraya yazabilirsiniz...' : 'You can write your message here...'; ?>"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-large" data-i18n-button="submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                        <?php echo t('submit'); ?>
                    </button>
                </form>
                </div>

                <!-- Direkt Mesaj Formu (Sadece Üyeler İçin) -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="offer-form-card" style="margin-top: 2rem; border-top: 2px solid var(--gray-100); padding-top: 2rem;">
                    <h2 class="card-title">💬 <?php echo $lang == 'tr' ? 'İlan Sahibine Mesaj Gönder' : 'Message Owner'; ?></h2>
                    <form id="dm-form" class="offer-form-modern">
                        <input type="hidden" name="listing_id" value="<?php echo $listing['id']; ?>">
                        <div class="form-group">
                            <label><?php echo $lang == 'tr' ? 'Mesajınız' : 'Your Message'; ?></label>
                            <textarea name="message" rows="4" required placeholder="<?php echo $lang == 'tr' ? 'Sorularınızı buraya yazabilirsiniz...' : 'You can write your questions here...'; ?>"></textarea>
                        </div>
                        <button type="submit" class="btn btn-secondary btn-large">
                            <i class="fas fa-paper-plane" style="margin-right: 8px;"></i>
                            <?php echo $lang == 'tr' ? 'Mesaj Gönder' : 'Send Message'; ?>
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="offer-form-card" style="margin-top: 2rem; text-align: center; padding: 2rem; background: var(--gray-50);">
                    <p style="color: var(--gray-600); margin-bottom: 1rem;">
                        <?php echo $lang == 'tr' ? 'İlan sahibiyle mesajlaşmak için lütfen giriş yapın.' : 'Please log in to message the owner.'; ?>
                    </p>
                    <a href="/login" class="btn btn-primary"><?php echo $lang == 'tr' ? 'Giriş Yap' : 'Log In'; ?></a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        </div>
    </main>


    <?php include 'includes/footer.php'; ?>
    <script src="/assets/js/main.js"></script>
    <script>
        // Property ID'yi sakla
        const PROPERTY_ID = <?php echo $listing['id']; ?>;

        function changeMainImage(src, index, total) {
            const mainImage = document.getElementById('mainImage');
            mainImage.style.opacity = '0';

            setTimeout(() => {
                mainImage.src = src;
                mainImage.style.opacity = '1';

                // Image counter güncelle
                const counter = document.querySelector('.image-counter span');
                if (counter) {
                    counter.textContent = (index + 1) + ' / ' + total;
                }

                // Thumbnail aktif durumunu güncelle
                document.querySelectorAll('.thumbnail-wrapper').forEach((thumb, i) => {
                    thumb.classList.toggle('active', i === index);
                });
            }, 150);
        }

        // Property.php'ye özel içerik güncelleme
        function updatePageContentSpecific(newLang) {
            return new Promise((resolve, reject) => {
                console.log('Updating property content for language:', newLang);
                // Property içeriğini AJAX ile getir
                fetch(`/get_property_content.php?id=${PROPERTY_ID}&lang=${newLang}`)
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);
                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error('Response text:', text);
                                throw new Error('HTTP error! status: ' + response.status + ', body: ' + text);
                            });
                        }
                        return response.text().then(text => {
                            console.log('Response text:', text);
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('JSON parse error:', e);
                                console.error('Text was:', text);
                                throw new Error('Invalid JSON response');
                            }
                        });
                    })
                    .then(data => {
                        console.log('Received data:', data);
                        if (data && data.success) {
                            const content = data.content;
                            console.log('Content to update:', content);

                            if (!content) {
                                console.error('Content is empty!');
                                reject(new Error('Content is empty'));
                                return;
                            }

                            // Başlık ve tip güncelle
                            const titleElement = document.querySelector('.property-header-card h1');
                            if (titleElement) {
                                titleElement.textContent = content.title;
                                console.log('Title updated:', content.title);
                            }

                            const typeBadge = document.querySelector('.property-type-badge');
                            if (typeBadge) {
                                typeBadge.textContent = content.property_type;
                                console.log('Property type updated:', content.property_type);
                            }

                            // Fiyat etiketi
                            const priceLabel = document.querySelector('.price-label');
                            if (priceLabel) {
                                priceLabel.textContent = content.price_label;
                                console.log('Price label updated:', content.price_label);
                            }

                            // Temel bilgiler başlığı
                            const basicInfoTitle = document.querySelector('.property-basic-card .card-title');
                            if (basicInfoTitle) {
                                basicInfoTitle.textContent = content.basic_info;
                                console.log('Basic info title updated:', content.basic_info);
                            }

                            // Etiketleri güncelle
                            document.querySelectorAll('[data-i18n-label="area"]').forEach(el => {
                                el.textContent = content.area_label;
                            });
                            document.querySelectorAll('[data-i18n-label="rooms"]').forEach(el => {
                                el.textContent = content.rooms_label;
                            });
                            document.querySelectorAll('[data-i18n-label="bathrooms"]').forEach(el => {
                                el.textContent = content.bathrooms_label;
                            });
                            document.querySelectorAll('[data-i18n-label="location"]').forEach(el => {
                                el.textContent = content.location_label;
                            });
                            document.querySelectorAll('[data-i18n-label="address"]').forEach(el => {
                                el.textContent = content.address_label;
                            });

                            // Açıklama güncelle
                            const descriptionCard = document.querySelector('.property-description-card');
                            if (descriptionCard) {
                                const descTitle = descriptionCard.querySelector('.card-title');
                                if (descTitle) descTitle.textContent = content.description_label;

                                const descContent = descriptionCard.querySelector('.description-content p');
                                if (descContent) {
                                    descContent.innerHTML = content.description.replace(/\n/g, '<br>');
                                    console.log('Description updated');
                                }
                            }

                            // Notlar varsa güncelle
                            if (content.notes) {
                                const notesCards = document.querySelectorAll('.property-description-card');
                                if (notesCards.length > 1) {
                                    const notesCard = notesCards[1];
                                    const notesTitle = notesCard.querySelector('.card-title');
                                    if (notesTitle) notesTitle.textContent = content.notes_label;

                                    const notesContent = notesCard.querySelector('.description-content p');
                                    if (notesContent) notesContent.innerHTML = content.notes.replace(/\n/g, '<br>');
                                    console.log('Notes updated');
                                }
                            }

                            // Teklif formu etiketleri
                            const offerCard = document.querySelector('.offer-form-card');
                            if (offerCard) {
                                const offerTitle = offerCard.querySelector('.card-title');
                                if (offerTitle) offerTitle.textContent = content.offer_label;

                                const form = offerCard.querySelector('form');
                                if (form) {
                                    const nameLabel = form.querySelector('label[data-i18n-form-label="your_name"]');
                                    if (nameLabel) nameLabel.textContent = content.your_name_label + ' *';

                                    const emailLabel = form.querySelector('label[data-i18n-form-label="your_email"]');
                                    if (emailLabel) emailLabel.textContent = content.your_email_label + ' *';

                                    const phoneLabel = form.querySelector('label[data-i18n-form-label="your_phone"]');
                                    if (phoneLabel) phoneLabel.textContent = content.your_phone_label;

                                    const amountLabel = form.querySelector('label[data-i18n-form-label="offer_amount"]');
                                    if (amountLabel) amountLabel.textContent = content.offer_amount_label + ' (TL) *';

                                    const messageLabel = form.querySelector('label[data-i18n-form-label="message"]');
                                    if (messageLabel) messageLabel.textContent = content.message_label;

                                    const messagePlaceholder = form.querySelector('textarea[data-i18n-placeholder="message"]');
                                    if (messagePlaceholder) {
                                        messagePlaceholder.placeholder = newLang === 'tr'
                                            ? 'Mesajınızı buraya yazabilirsiniz...'
                                            : 'You can write your message here...';
                                    }

                                    const submitBtn = form.querySelector('button[data-i18n-button="submit"]');
                                    if (submitBtn) {
                                        const svg = submitBtn.querySelector('svg').outerHTML;
                                        submitBtn.innerHTML = svg + ' ' + content.submit_label;
                                    }
                                    console.log('Offer form updated');
                                }
                            }

                            // Detay bölümleri başlıkları
                            document.querySelectorAll('.property-details-section h2').forEach(h2 => {
                                const text = h2.textContent.trim();
                                if (text.includes('Arsa Detayları') || text.includes('Land Details')) {
                                    h2.textContent = content.land_details;
                                } else if (text.includes('Konut Detayları') || text.includes('Residential Details')) {
                                    h2.textContent = content.residential_details;
                                } else if (text.includes('İşyeri Detayları') || text.includes('Commercial')) {
                                    h2.textContent = content.commercial_details;
                                }
                            });

                            document.querySelectorAll('.property-details-section h3').forEach(h3 => {
                                const text = h3.textContent.trim();
                                if (text.includes('Altyapı') || text.includes('Infrastructure')) {
                                    h3.textContent = content.infrastructure;
                                } else if (text.includes('Bina Özellikleri') || text.includes('Building Features')) {
                                    h3.textContent = content.building_features;
                                } else if (text.includes('Konum Özellikleri') || text.includes('Location Features')) {
                                    h3.textContent = content.location_features;
                                }
                            });

                            // Yorumlar bölümü güncelle
                            const commentsTitle = document.querySelector('.comments-section h2');
                            if (commentsTitle) {
                                const count = commentsTitle.querySelector('#comment-count').textContent;
                                commentsTitle.innerHTML = `💬 ${content.comments_label} (<span id="comment-count">${count}</span>)`;
                            }

                            const addCommentTitle = document.querySelector('.add-comment-form h3');
                            if (addCommentTitle) addCommentTitle.textContent = content.add_comment_label;

                            const submitCommentBtn = document.getElementById('btn-add-comment');
                            if (submitCommentBtn) {
                                submitCommentBtn.innerHTML = `<i class="fas fa-comment"></i> ${content.submit_comment_label}`;
                            }

                            const commentTextarea = document.getElementById('comment-content');
                            if (commentTextarea) {
                                commentTextarea.placeholder = newLang === 'tr' ? 'Yorumunuzu buraya yazın...' : 'Write your comment here...';
                            }

                            const loginPrompt = document.querySelector('.login-prompt p');
                            if (loginPrompt) loginPrompt.textContent = content.login_prompt_label;

                            // No comments mesajı varsa güncelle
                            const noComments = document.querySelector('.no-comments');
                            if (noComments) {
                                noComments.textContent = newLang === 'tr' ? 'Bu ilan için henüz yorum yapılmamış.' : 'No comments for this listing yet.';
                            }

                            // Geri butonu
                            const backBtn = document.querySelector('a.btn-secondary[data-i18n="back"]');
                            if (backBtn) {
                                backBtn.innerHTML = `← ${content.back_label}`;
                            }

                            console.log('Property content updated successfully');
                            resolve();
                        } else {
                            console.error('Update failed:', data.message);
                            const errorMsg = data.message || 'Update failed';
                            reject(new Error(errorMsg));
                        }
                    })
                    .catch(error => {
                        console.error('Content update error:', error);
                        console.error('Error details:', error.message);
                        if (error.stack) {
                            console.error('Stack:', error.stack);
                        }
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
                        const noComments = container.querySelector('.no-comments');
                        if (noComments) noComments.remove();

                        const commentHtml = `
                            <div class="comment-item" style="animation: fadeIn 0.5s ease;">
                                <div class="comment-header">
                                    <span class="comment-user">👤 ${data.comment.user_name}</span>
                                    <span class="comment-date">${data.comment.created_at}</span>
                                </div>
                                <div class="comment-body">
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
    </script>
</body>

</html>