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
    <link rel="stylesheet" href="/assets/css/premium-home.css?v=<?php echo CSS_VERSION; ?>">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="subpage-header">
            <div class="container">
                <h1><?php echo t('listings'); ?></h1>
                <p style="opacity: 0.8;"><?php echo count($listings); ?> <?php echo $lang == 'tr' ? 'aktif ilan listeleniyor' : 'active listings found'; ?></p>
            </div>
        </section>

        <div class="container" style="padding-bottom: 100px;">
            <div class="listings-container">
                <!-- Sidebar Filter -->
                <aside class="filter-sidebar-premium">
                    <h2><i class="fas fa-filter"></i> <?php echo t('filter'); ?></h2>
                    <form method="GET" action="ilanlar" class="filter-form-modern">
                        <div class="filter-group-premium">
                            <label><?php echo t('listing_type'); ?></label>
                            <select name="listing_type" id="listing-type-listings" class="filter-input-premium">
                                <option value=""><?php echo $lang == 'tr' ? 'Tümü' : 'All'; ?></option>
                                <option value="satilik" <?php echo $listing_type == 'satilik' ? 'selected' : ''; ?>><?php echo t('for_sale'); ?></option>
                                <option value="kiralik" <?php echo $listing_type == 'kiralik' ? 'selected' : ''; ?>><?php echo t('for_rent'); ?></option>
                            </select>
                        </div>
                        
                        <div class="filter-group-premium">
                            <label><?php echo t('property_type'); ?></label>
                            <select name="property_type" id="property-type-listings" class="filter-input-premium">
                                <option value="all"><?php echo t('all'); ?></option>
                                <option value="ev" <?php echo $property_type == 'ev' ? 'selected' : ''; ?>><?php echo t('house'); ?></option>
                                <option value="daire" <?php echo $property_type == 'daire' ? 'selected' : ''; ?>><?php echo t('apartment'); ?></option>
                                <option value="arsa" <?php echo $property_type == 'arsa' ? 'selected' : ''; ?> data-hide-for-rent="true"><?php echo t('land'); ?></option>
                                <option value="tarla" <?php echo $property_type == 'tarla' ? 'selected' : ''; ?> data-hide-for-rent="true"><?php echo t('tarla'); ?></option>
                                <option value="dukkan" <?php echo $property_type == 'dukkan' ? 'selected' : ''; ?>><?php echo t('shop'); ?></option>
                                <option value="villa" <?php echo $property_type == 'villa' ? 'selected' : ''; ?>><?php echo t('villa'); ?></option>
                            </select>
                        </div>

                        <div class="filter-group-premium">
                            <label><?php echo t('price'); ?> (Min/Max)</label>
                            <div style="display: flex; gap: 8px;">
                                <input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" class="filter-input-premium" placeholder="Min">
                                <input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" class="filter-input-premium" placeholder="Max">
                            </div>
                        </div>

                        <div class="filter-group-premium">
                            <label><?php echo t('city'); ?></label>
                            <select name="city" id="city-select" class="filter-input-premium">
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

                        <div class="filter-group-premium">
                            <label><?php echo t('district'); ?></label>
                            <select name="district" id="district-select" class="filter-input-premium">
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

                        <div class="filter-group-premium">
                            <label><?php echo $lang == 'tr' ? 'Kimden' : 'From'; ?></label>
                            <select name="owner_type" class="filter-input-premium">
                                <option value=""><?php echo $lang == 'tr' ? 'Tümü' : 'All'; ?></option>
                                <option value="emlakci" <?php echo $owner_type_filter == 'emlakci' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Emlakçıdan' : 'Agency'; ?></option>
                                <option value="bireysel" <?php echo $owner_type_filter == 'bireysel' ? 'selected' : ''; ?>><?php echo $lang == 'tr' ? 'Sahibinden' : 'By Owner'; ?></option>
                            </select>
                        </div>

                        <button type="submit" class="btn-filter-apply"><?php echo t('filter'); ?></button>
                        <a href="ilanlar" class="btn-filter-clear"><?php echo $lang == 'tr' ? 'Temizle' : 'Clear Filters'; ?></a>
                    </form>
                </aside>

                <!-- Listings Grid -->
                <div class="listings-grid-wrapper">
                    <div class="listings-grid" style="margin-top: 0;">
                        <?php if (empty($listings)): ?>
                            <div style="background: white; padding: 60px; border-radius: 24px; text-align: center; box-shadow: var(--premium-shadow-soft);">
                                <i class="fas fa-search" style="font-size: 3rem; color: #EEE; margin-bottom: 20px;"></i>
                                <p class="no-results"><?php echo t('no_listings'); ?></p>
                                <a href="ilanlar" style="color: var(--premium-gold); font-weight: 700;"><?php echo $lang == 'tr' ? 'Tüm İlanları Gör' : 'View All Listings'; ?></a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($listings as $listing): ?>
                                <?php 
                                    if (!empty($listing['slug'])) {
                                        $l_type = !empty($listing['listing_type']) ? $listing['listing_type'] : 'satilik';
                                        $p_type_slug = !empty($listing['property_type']) ? $listing['property_type'] : 'konut';
                                        
                                        $city_raw = !empty($listing['city']) ? $listing['city'] : 'turkiye';
                                        $city_slug = mb_strtolower($city_raw, 'UTF-8');
                                        $city_slug = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'g', 'u', 's', 'o', 'c'], $city_slug);
                                        $city_slug = preg_replace('/[^a-z0-9]+/', '-', $city_slug);
                                        $city_slug = trim($city_slug, '-');
                                        
                                        $url = '/detay/' . $l_type . '/' . $p_type_slug . '/' . $city_slug . '/' . $listing['slug'];
                                    } else {
                                        $url = '/ilan.php?id=' . $listing['id'];
                                    }
                                    $listing_title = !empty($listing['title_' . $lang]) ? $listing['title_' . $lang] : t('property');
                                ?>
                                <div class="listing-card-premium">
                                    <div class="image-container">
                                        <a href="<?php echo $url; ?>">
                                            <img src="<?php echo !empty($listing['image1']) ? '/uploads/' . $listing['image1'] : '/assets/images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($listing_title); ?>">
                                        </a>
                                        <div class="badge-premium"><?php echo t($listing['property_type']); ?></div>
                                        <div class="price-tag"><?php echo number_format($listing['price'], 0, ',', '.'); ?> <?php echo t('currency'); ?></div>
                                        
                                        <button class="btn-favorite-premium <?php echo in_array($listing['id'], $user_favs) ? 'active' : ''; ?>" data-id="<?php echo $listing['id']; ?>" style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); color: white; border: 1px solid rgba(255,255,255,0.3); width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                                            <i class="<?php echo in_array($listing['id'], $user_favs) ? 'fas' : 'far'; ?> fa-heart"></i>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="location-premium">
                                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($listing['city']); ?> / <?php echo htmlspecialchars($listing['district']); ?>
                                        </div>
                                        <h3 class="card-title" style="margin-bottom: 12px; height: auto;">
                                            <a href="<?php echo $url; ?>" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($listing_title); ?></a>
                                        </h3>
                                        <div class="feature-pill-group">
                                            <?php if ($listing['area']): ?>
                                                <div class="feature-pill"><i class="fas fa-vector-square"></i> <?php echo $listing['area']; ?> m²</div>
                                            <?php endif; ?>
                                            <?php if ($listing['rooms']): ?>
                                                <div class="feature-pill"><i class="fas fa-bed"></i> <?php echo $listing['rooms']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #F5F5F5;">
                                            <span style="font-size: 0.75rem; color: var(--premium-text-muted); font-weight: 600;">
                                                <?php if (($listing['owner_type'] ?? 'admin') === 'emlakci'): ?>
                                                    <i class="fas fa-building"></i> <?php echo htmlspecialchars($listing['firma_adi'] ?: t('agency')); ?>
                                                <?php else: ?>
                                                    <i class="fas fa-user"></i> <?php echo $lang == 'tr' ? 'Sahibinden' : 'By Owner'; ?>
                                                <?php endif; ?>
                                            </span>
                                            <a href="<?php echo $url; ?>" style="color: var(--premium-navy); font-weight: 800; text-decoration: none; font-size: 0.85rem;">
                                                <?php echo $lang == 'tr' ? 'DETAY' : 'DETAILS'; ?> <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/location-filter.js"></script>
    <script>
        // Filter property types based on listing type
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

        // Favoriye ekleme işlemi
        document.querySelectorAll('.btn-favorite-premium').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const listingId = this.dataset.id;
                const icon = this.querySelector('i');
                
                fetch('/api/favorite.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'listing_id=' + listingId
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (data.action === 'added') {
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                            this.classList.add('active');
                        } else {
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                            this.classList.remove('active');
                        }
                    } else if (data.status === 'error' && data.message === 'login_required') {
                        window.location.href = '/giris';
                    }
                });
            });
        });
    </script>
</body>
</html>
