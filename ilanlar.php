<?php
require_once 'config.php';

// Filtreleme parametreleri
$property_type = $_GET['property_type'] ?? '';
$listing_type = $_GET['listing_type'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$city = $_GET['city'] ?? '';
$district = $_GET['district'] ?? '';
$mahalle = $_GET['mahalle'] ?? '';
$owner_type_filter = $_GET['owner_type'] ?? '';

// Kullanıcı favorilerini getir
$user_favs = [];
if (isset($_SESSION['user_id'])) {
    $stmt_favs = $pdo->prepare("SELECT listing_id FROM favorites WHERE user_id = :uid");
    $stmt_favs->execute([':uid' => $_SESSION['user_id']]);
    $user_favs = $stmt_favs->fetchAll(PDO::FETCH_COLUMN);
}

// Validate listing_type
if ($listing_type && !in_array($listing_type, ['satilik', 'kiralik'])) {
    $listing_type = '';
}

// Validate property_type
$valid_property_types = ['ev', 'daire', 'arsa', 'tarla', 'dukkan', 'villa'];
if ($property_type && $property_type != 'all' && !in_array($property_type, $valid_property_types)) {
    $property_type = '';
}

// SQL sorgusu oluştur
$sql = "SELECT l.*, u.user_type as owner_type, u.firma_adi 
        FROM listings l 
        LEFT JOIN users u ON l.user_id = u.id 
        WHERE l.status = 'active' AND l.approval_status = 'approved'";
$params = [];

if ($listing_type) {
    $sql .= " AND listing_type = :listing_type";
    $params[':listing_type'] = $listing_type;
}

if ($property_type && $property_type != 'all') {
    $sql .= " AND property_type = :property_type";
    $params[':property_type'] = $property_type;
}

if ($min_price) {
    $sql .= " AND price >= :min_price";
    $params[':min_price'] = $min_price;
}

if ($max_price) {
    $sql .= " AND price <= :max_price";
    $params[':max_price'] = $max_price;
}

if ($city) {
    $sql .= " AND city = :city";
    $params[':city'] = $city;
}

if ($district) {
    $sql .= " AND district = :district";
    $params[':district'] = $district;
}

if ($mahalle) {
    $sql .= " AND l.mahalle = :mahalle";
    $params[':mahalle'] = $mahalle;
}

if ($owner_type_filter) {
    $sql .= " AND u.user_type = :owner_type";
    $params[':owner_type'] = $owner_type_filter;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$listings = $stmt->fetchAll();

// Şehir listesi (veritabanından)
$stmt = $pdo->query("SELECT id, il_adi FROM iller ORDER BY il_adi");
$cities = $stmt->fetchAll();

// Seçili şehre göre ilçeler
$districts = [];
if ($city) {
    $stmt = $pdo->prepare("SELECT id FROM iller WHERE il_adi = :city");
    $stmt->execute([':city' => $city]);
    $city_id = $stmt->fetchColumn();
    if ($city_id) {
        $stmt = $pdo->prepare("SELECT id, ilce_adi FROM ilceler WHERE il_id = :city_id ORDER BY ilce_adi");
        $stmt->execute([':city_id' => $city_id]);
        $districts = $stmt->fetchAll();
    }
}

// Seçili ilçeye göre mahalleler
$neighborhoods = [];
if ($district) {
    $stmt = $pdo->prepare("SELECT id FROM ilceler WHERE ilce_adi = :district");
    $stmt->execute([':district' => $district]);
    $district_id = $stmt->fetchColumn();
    if ($district_id) {
        $stmt = $pdo->prepare("SELECT id, mahalle_adi FROM mahalleler WHERE ilce_id = :district_id ORDER BY mahalle_adi LIMIT 500");
        $stmt->execute([':district_id' => $district_id]);
        $neighborhoods = $stmt->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('listings'); ?> — Emlaxia.com</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?php echo CSS_VERSION; ?>">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="container">
            <h1><?php echo t('listings'); ?></h1>

            <div class="filters">
                <form method="GET" action="ilanlar" class="filter-form">
                    <div class="filter-group">
                        <label><?php echo t('listing_type'); ?></label>
                        <select name="listing_type" id="listing-type-listings">
                            <option value=""><?php echo $lang == 'tr' ? 'Tümü' : 'All'; ?></option>
                            <option value="satilik" <?php echo $listing_type == 'satilik' ? 'selected' : ''; ?>><?php echo t('for_sale'); ?></option>
                            <option value="kiralik" <?php echo $listing_type == 'kiralik' ? 'selected' : ''; ?>><?php echo t('for_rent'); ?></option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label><?php echo t('property_type'); ?></label>
                        <select name="property_type" id="property-type-listings">
                            <option value="all"><?php echo t('all'); ?></option>
                            <option value="ev" <?php echo $property_type == 'ev' ? 'selected' : ''; ?>><?php echo t('house'); ?></option>
                            <option value="daire" <?php echo $property_type == 'daire' ? 'selected' : ''; ?>><?php echo t('apartment'); ?></option>
                            <option value="arsa" <?php echo $property_type == 'arsa' ? 'selected' : ''; ?> data-hide-for-rent="true"><?php echo t('land'); ?></option>
                            <option value="tarla" <?php echo $property_type == 'tarla' ? 'selected' : ''; ?> data-hide-for-rent="true"><?php echo t('tarla'); ?></option>
                            <option value="dukkan" <?php echo $property_type == 'dukkan' ? 'selected' : ''; ?>><?php echo t('shop'); ?></option>
                            <option value="villa" <?php echo $property_type == 'villa' ? 'selected' : ''; ?>><?php echo t('villa'); ?></option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label><?php echo t('price'); ?> (Min)</label>
                        <input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" placeholder="0">
                    </div>

                    <div class="filter-group">
                        <label><?php echo t('price'); ?> (Max)</label>
                        <input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" placeholder="999999999">
                    </div>

                    <div class="filter-group">
                        <label><?php echo t('city'); ?></label>
                        <select name="city" id="city-select">
                            <option value=""><?php echo $lang == 'tr' ? 'Şehir Seçiniz' : 'Select City'; ?></option>
                            <?php foreach ($cities as $city_option): ?>
                                <option value="<?php echo htmlspecialchars($city_option['il_adi']); ?>" 
                                    <?php echo $city == $city_option['il_adi'] ? 'selected' : ''; ?>
                                    data-id="<?php echo $city_option['id']; ?>">
                                    <?php echo htmlspecialchars($city_option['il_adi']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label><?php echo t('district'); ?></label>
                        <select name="district" id="district-select">
                            <option value=""><?php echo $lang == 'tr' ? 'İlçe Seçiniz' : 'Select District'; ?></option>
                            <?php foreach ($districts as $district_option): ?>
                                <option value="<?php echo htmlspecialchars($district_option['ilce_adi']); ?>" 
                                    <?php echo $district == $district_option['ilce_adi'] ? 'selected' : ''; ?>
                                    data-id="<?php echo $district_option['id']; ?>">
                                    <?php echo htmlspecialchars($district_option['ilce_adi']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label><?php echo $lang == 'tr' ? 'Mahalle' : 'Neighborhood'; ?></label>
                        <select name="mahalle" id="mahalle-select">
                            <option value=""><?php echo $lang == 'tr' ? 'Mahalle Seçiniz' : 'Select Neighborhood'; ?></option>
                            <?php foreach ($neighborhoods as $neighborhood_option): ?>
                                <option value="<?php echo htmlspecialchars($neighborhood_option['mahalle_adi']); ?>" 
                                    <?php echo $mahalle == $neighborhood_option['mahalle_adi'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($neighborhood_option['mahalle_adi']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label><?php echo $lang == 'tr' ? 'Kimden' : 'From'; ?></label>
                        <select name="owner_type">
                            <option value=""><?php echo $lang == 'tr' ? 'Tümü' : 'All'; ?></option>
                            <option value="emlakci" <?php echo $owner_type_filter == 'emlakci' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Emlakçıdan' : 'Agency'; ?></option>
                            <option value="bireysel" <?php echo $owner_type_filter == 'bireysel' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Sahibinden' : 'By Owner'; ?></option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <button type="submit" class="btn btn-primary"><?php echo t('filter'); ?></button>
                        <a href="ilanlar" class="btn btn-secondary"><?php echo $lang == 'tr' ? 'Temizle' : 'Clear'; ?></a>
                    </div>
                </form>
            </div>

            <!-- İlanlar -->
            <div class="listings-grid">
                <?php if (empty($listings)): ?>
                    <p class="no-results"><?php echo t('no_listings'); ?></p>
                <?php else: ?>
                    <?php foreach ($listings as $listing): ?>
                        <div class="listing-card">
                            <?php 
                            // Yeni SEO-friendly URL formatı
                            if (!empty($listing['slug'])) {
                                $listing_type = !empty($listing['listing_type']) ? $listing['listing_type'] : 'satilik';
                                $listing_type = strtolower(trim($listing_type));
                                
                                $property_type_slug = !empty($listing['property_type']) ? $listing['property_type'] : 'konut';
                                $property_type_slug = strtolower(trim($property_type_slug));
                                
                                // Şehir adını URL-friendly hale getir
                                $city_raw = !empty($listing['city']) ? $listing['city'] : 'turkiye';
                                $city = mb_strtolower($city_raw, 'UTF-8');
                                $city = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'g', 'u', 's', 'o', 'c'], $city);
                                $city = preg_replace('/[^a-z0-9]+/', '-', $city);
                                $city = trim($city, '-');
                                
                                $url = '/detay/' . $listing_type . '/' . $property_type_slug . '/' . $city . '/' . $listing['slug'];
                            } else {
                                $url = '/ilan.php?id=' . $listing['id'];
                            }
                            ?>
                            <a href="<?php echo $url; ?>" class="listing-image-link">
                                <div class="owner-type-badge <?php echo $listing['owner_type'] ?? 'admin'; ?>" style="position: absolute; bottom: 10px; left: 10px; background: rgba(15,18,61,0.8); color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px; z-index: 5;">
                                    <?php if (($listing['owner_type'] ?? 'admin') === 'emlakci'): ?>
                                        <i class="fas fa-building"></i> <?php echo $lang == 'tr' ? 'Emlakçıdan' : 'Agency'; ?>
                                    <?php elseif (($listing['owner_type'] ?? 'admin') === 'bireysel'): ?>
                                        <i class="fas fa-user"></i> <?php echo $lang == 'tr' ? 'Sahibinden' : 'By Owner'; ?>
                                    <?php else: ?>
                                        <i class="fas fa-check-circle"></i> Emlaxia
                                    <?php endif; ?>
                                </div>
                                <button class="btn-favorite <?php echo in_array($listing['id'], $user_favs) ? 'active' : ''; ?>" data-id="<?php echo $listing['id']; ?>">
                                    <i class="<?php echo in_array($listing['id'], $user_favs) ? 'fas' : 'far'; ?> fa-heart"></i>
                                </button>
                                <?php if ($listing['image1']): ?>
                                    <img src="/uploads/<?php echo htmlspecialchars($listing['image1']); ?>" alt="<?php echo htmlspecialchars($listing['title_' . $lang]); ?>">
                                <?php else: ?>
                                    <img src="/assets/images/placeholder.jpg" alt="No Image">
                                <?php endif; ?>
                            </a>
                            <div class="listing-info">
                                <span class="property-type"><?php echo t($listing['property_type']); ?></span>
                                <?php $listing_title = !empty($listing['title_' . $lang]) ? $listing['title_' . $lang] : t('property'); ?>
                                <h3><a href="<?php echo $url; ?>" class="listing-title-link"><?php echo htmlspecialchars($listing_title); ?></a></h3>
                                <p class="price"><?php echo number_format($listing['price'], 2, ',', '.'); ?> <?php echo t('currency'); ?></p>
                                <?php if ($listing['area']): ?>
                                    <p class="area"><?php echo t('area'); ?>: <?php echo $listing['area']; ?> m²</p>
                                <?php endif; ?>
                                <?php if ($listing['rooms']): ?>
                                    <p class="rooms"><?php echo t('rooms'); ?>: <?php echo $listing['rooms']; ?></p>
                                <?php endif; ?>
                                <?php if ($listing['city']): ?>
                                    <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($listing['city']); ?><?php echo $listing['district'] ? ', ' . htmlspecialchars($listing['district']) : ''; ?></p>
                                <?php endif; ?>
                                <a href="<?php echo $url; ?>" class="btn btn-secondary"><?php echo t('details'); ?></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>


    <?php include 'includes/footer.php'; ?>
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/location-filter.js"></script>
    <script>
        // Listings page - filter property types based on listing type
        const listingTypeListings = document.getElementById('listing-type-listings');
        const propertyTypeListings = document.getElementById('property-type-listings');
        
        function filterListingsPropertyTypes() {
            const listingType = listingTypeListings ? listingTypeListings.value : '';
            const isRental = listingType === 'kiralik';
            
            if (propertyTypeListings) {
                const options = propertyTypeListings.querySelectorAll('option');
                options.forEach(option => {
                    if (option.hasAttribute('data-hide-for-rent') && isRental) {
                        option.style.display = 'none';
                        if (option.selected) {
                            propertyTypeListings.value = 'all';
                        }
                    } else {
                        option.style.display = '';
                    }
                });
            }
        }
        
        if (listingTypeListings) {
            listingTypeListings.addEventListener('change', filterListingsPropertyTypes);
            filterListingsPropertyTypes();
        }
    </script>
</body>
</html>

