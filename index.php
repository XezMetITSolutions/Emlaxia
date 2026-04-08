<?php
require_once 'config.php';

// Filtreleme parametreleri
$property_type = $_GET['property_type'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$min_area = $_GET['min_area'] ?? '';
$max_area = $_GET['max_area'] ?? '';
$city = $_GET['city'] ?? '';
$district = $_GET['district'] ?? '';
$listing_id = $_GET['listing_id'] ?? '';
$status = $_GET['status'] ?? 'satilik'; // Default to satilik

// SQL sorgusu oluştur
$sql = "SELECT l.*, u.user_type as owner_type, u.firma_adi 
        FROM listings l 
        LEFT JOIN users u ON l.user_id = u.id 
        WHERE l.status = 'active' AND l.approval_status = 'approved'";
$params = [];

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

if ($min_area) {
    $sql .= " AND area >= :min_area";
    $params[':min_area'] = $min_area;
}

if ($max_area) {
    $sql .= " AND area <= :max_area";
    $params[':max_area'] = $max_area;
}

if ($city) {
    $sql .= " AND city = :city";
    $params[':city'] = $city;
}

if ($district) {
    $sql .= " AND district = :district";
    $params[':district'] = $district;
}

if ($listing_id) {
    $sql .= " AND id = :listing_id";
    $params[':listing_id'] = $listing_id;
}

// Eğer filtre yoksa rastgele 3 ilan göster, varsa filtreye göre göster
if (empty($params)) {
    $sql .= " ORDER BY RAND() LIMIT 3";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} else {
    $sql .= " ORDER BY created_at DESC LIMIT 3";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
}

$featured_listings = $stmt->fetchAll();

// Kullanıcı favorilerini getir
$user_favs = [];
if (isset($_SESSION['user_id'])) {
    $stmt_favs = $pdo->prepare("SELECT listing_id FROM favorites WHERE user_id = :uid");
    $stmt_favs->execute([':uid' => $_SESSION['user_id']]);
    $user_favs = $stmt_favs->fetchAll(PDO::FETCH_COLUMN);
}

// İstatistikler
$stmt = $pdo->query("SELECT COUNT(*) as total FROM listings WHERE status = 'active' AND approval_status = 'approved'");
$total_listings = $stmt->fetch()['total'];

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
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'tr' ? 'Ev, Arsa, Tarla Aramaya Son — Emlaxia.com' : 'End Search for House, Land, Field — Emlaxia.com'; ?></title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isTr = '<?php echo $lang; ?>' === 'tr';
            const trTitles = [
                'Ev Aramaya Son — Emlaxia.com',
                'Arsa Aramaya Son — Emlaxia.com',
                'Tarla Aramaya Son — Emlaxia.com'
            ];
            const enTitles = [
                'End Search for House — Emlaxia.com',
                'End Search for Land — Emlaxia.com',
                'End Search for Field — Emlaxia.com'
            ];
            const titles = isTr ? trTitles : enTitles;
            let index = 0;
            
            setInterval(() => {
                document.title = titles[index];
                index = (index + 1) % titles.length;
            }, 3000);
        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo CSS_VERSION; ?>">
    <link rel="stylesheet" href="assets/css/premium-home.css?v=<?php echo CSS_VERSION; ?>">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="main-hero" style="background-image: url('assets/images/luxury_real_estate_hero.jpg');">
            <div class="hero-overlay"></div>
            <div class="container">
                <div class="hero-content">
                    <h1><?php echo $lang == 'tr' ? 'Hayalinizdeki <span>Evi</span><br>Bizimle Keşfedin' : 'Discover Your Dream <span>Home</span><br>With Us'; ?></h1>
                    <p><?php echo $lang == 'tr' ? 'Türkiye\'nin en kaliteli emlak portföyü ile hayallerinizi gerçeğe dönüştürüyoruz. Sizin için sadece en iyisini seçiyoruz.' : 'We turn your dreams into reality with Turkey\'s highest quality real estate portfolio. We select only the best for you.'; ?></p>
                    
                    <div class="search-box-container">
                        <form method="GET" action="ilanlar" id="homepage-search-form">
                            <div class="search-form-grid">
                                <div class="form-group">
                                    <label><?php echo t('listing_type'); ?></label>
                                    <select name="listing_type" class="form-control-premium">
                                        <option value="satilik"><?php echo t('for_sale'); ?></option>
                                        <option value="kiralik"><?php echo t('for_rent'); ?></option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label><?php echo t('property_type'); ?></label>
                                    <select name="property_type" class="form-control-premium">
                                        <option value="all"><?php echo $lang == 'tr' ? 'Tüm Türler' : 'All Types'; ?></option>
                                        <option value="ev"><?php echo t('house'); ?></option>
                                        <option value="daire"><?php echo t('apartment'); ?></option>
                                        <option value="arsa"><?php echo t('land'); ?></option>
                                        <option value="tarla"><?php echo t('tarla'); ?></option>
                                        <option value="villa"><?php echo t('villa'); ?></option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label><?php echo $lang == 'tr' ? 'Şehir' : 'City'; ?></label>
                                    <select name="city" id="city-select-home" class="form-control-premium">
                                        <option value=""><?php echo $lang == 'tr' ? 'Tüm Şehirler' : 'All Cities'; ?></option>
                                        <?php foreach ($cities as $city_option): ?>
                                            <option value="<?php echo htmlspecialchars($city_option['il_adi']); ?>" data-id="<?php echo $city_option['id']; ?>">
                                                <?php echo htmlspecialchars($city_option['il_adi']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn-search-premium" style="width: 100%;">
                                        <i class="fas fa-search"></i> <?php echo $lang == 'tr' ? 'İLAN BUL' : 'FIND ADS'; ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Category Shortcuts -->
        <section class="categories-preview">
            <div class="container">
                <div class="categories-grid">
                    <a href="/ilanlar?property_type=daire" class="category-item">
                        <div class="category-icon"><i class="fas fa-building"></i></div>
                        <span><?php echo t('apartment'); ?></span>
                    </a>
                    <a href="/ilanlar?property_type=villa" class="category-item">
                        <div class="category-icon"><i class="fas fa-home"></i></div>
                        <span><?php echo t('villa'); ?></span>
                    </a>
                    <a href="/ilanlar?property_type=arsa" class="category-item">
                        <div class="category-icon"><i class="fas fa-map-marked-alt"></i></div>
                        <span><?php echo t('land'); ?></span>
                    </a>
                    <a href="/ilanlar?property_type=tarla" class="category-item">
                        <div class="category-icon"><i class="fas fa-seedling"></i></div>
                        <span><?php echo t('tarla'); ?></span>
                    </a>
                    <a href="/yenisehir" class="category-item">
                        <div class="category-icon"><i class="fas fa-city"></i></div>
                        <span><?php echo $lang == 'tr' ? 'Projeler' : 'Projects'; ?></span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Featured Listings -->
        <section style="padding: 100px 0;">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title-premium"><?php echo $lang == 'tr' ? 'Seçkin Portföyümüz' : 'Our Exclusive Portfolio'; ?></h2>
                    <p style="color: var(--premium-text-muted);"><?php echo $lang == 'tr' ? 'Sizin için özenle seçilmiş, yüksek yatırım değerli ilanlar.' : 'Carefully selected listings with high investment value for you.'; ?></p>
                </div>
                
                <div class="listings-grid">
                    <?php if (empty($featured_listings)): ?>
                        <p class="no-results"><?php echo t('no_listings'); ?></p>
                    <?php else: ?>
                        <?php foreach ($featured_listings as $listing): ?>
                            <?php 
                                $listing_title = !empty($listing['title_' . $lang]) ? $listing['title_' . $lang] : t('property');
                                $url = !empty($listing['slug']) ? '/detay/satilik/' . strtolower($listing['property_type']) . '/' . urlencode($listing['city']) . '/' . $listing['slug'] : '/ilan.php?id=' . $listing['id'];
                            ?>
                            <div class="listing-card-premium">
                                <div class="image-container">
                                    <a href="<?php echo $url; ?>">
                                        <img src="<?php echo !empty($listing['image1']) ? '/uploads/' . $listing['image1'] : '/assets/images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($listing_title); ?>">
                                    </a>
                                    <div class="badge-premium"><?php echo t($listing['property_type']); ?></div>
                                    <div class="price-tag"><?php echo number_format($listing['price'], 0, ',', '.'); ?> <?php echo t('currency'); ?></div>
                                </div>
                                <div class="card-body">
                                    <div class="location-premium">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($listing['city']); ?> / <?php echo htmlspecialchars($listing['district']); ?>
                                    </div>
                                    <h3 class="card-title">
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
                                    <a href="<?php echo $url; ?>" class="btn-card">
                                        <?php echo $lang == 'tr' ? 'İncele' : 'Detailed View'; ?> <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="text-center" style="margin-top: 60px;">
                    <a href="/ilanlar" class="btn-search-premium" style="display: inline-flex; width: auto; padding: 16px 48px; text-decoration: none;">
                        <?php echo $lang == 'tr' ? 'TÜM İLANLARI GÖR' : 'SEE ALL LISTINGS'; ?>
                    </a>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats-premium">
            <div class="container">
                <div class="search-form-grid text-center">
                    <div class="stat-item">
                        <h3><?php echo number_format($total_listings + 1240, 0, ',', '.'); ?>+</h3>
                        <p><?php echo $lang == 'tr' ? 'Aktif İlan' : 'Active Listings'; ?></p>
                    </div>
                    <div class="stat-item">
                        <h3>500+</h3>
                        <p><?php echo $lang == 'tr' ? 'Mutlu Aile' : 'Happy Families'; ?></p>
                    </div>
                    <div class="stat-item">
                        <h3>15+</h3>
                        <p><?php echo $lang == 'tr' ? 'Yıllık Deneyim' : 'Years Experience'; ?></p>
                    </div>
                    <div class="stat-item">
                        <h3>24/7</h3>
                        <p><?php echo $lang == 'tr' ? 'Kesintisiz Destek' : 'Full Support'; ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact CTA -->
        <section style="padding: 120px 0; background: white;">
            <div class="container">
                <div style="background: var(--premium-bg); border-radius: 40px; padding: 80px 40px; text-align: center; position: relative; overflow: hidden;">
                    <div style="position: relative; z-index: 2;">
                        <h2 class="premium-heading" style="font-size: 3rem; margin-bottom: 24px;"><?php echo $lang == 'tr' ? 'Gayrimenkulünüzü Değerinde <br><span>Birlikte Satalım</span>' : 'Let\'s Sell Your Property <br><span>Together Properly</span>'; ?></h2>
                        <p style="max-width: 600px; margin: 0 auto 40px; color: var(--premium-text-muted); font-size: 1.1rem;">
                            <?php echo $lang == 'tr' ? 'Uzman ekibimizle ilanınızı binlerce alıcıya ulaştırıyoruz. Hemen bizimle iletişime geçin.' : 'We bring your listing to thousands of buyers with our expert team. Contact us now.'; ?>
                        </p>
                        <a href="/kayit" class="btn-search-premium" style="display: inline-flex; width: auto; padding: 18px 48px; text-decoration: none;">
                            <i class="fas fa-plus-circle"></i> <?php echo $lang == 'tr' ? 'ÜCRETSİZ İLAN VER' : 'POST FREE AD'; ?>
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/location-filter.js"></script>
</body>
</html>
