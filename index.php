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
            
            // Başlangıç başlığı zaten ayarlandı, interval ile döngüye başla
            setInterval(() => {
                document.title = titles[index];
                index = (index + 1) % titles.length;
            }, 3000); // 3 saniyede bir değiştir
        });
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo CSS_VERSION; ?>">
    <!-- <link rel="stylesheet" href="assets/css/homepage-modern.css"> REMOVED FOR FLAT DESIGN -->
    <style>
        :root {
            /* Override Gradients with Solid Colors */
            --gradient-primary: #0F123D;
            --gradient-secondary: #D3AF37;
            --gradient-accent: #D3AF37;
            --gradient-dark: #0A0D2E;
            
            /* New Primary Colors */
            --primary-color: #0F123D;
            --primary-dark: #0A0D2E;
            --primary-light: #1A1F52;
            
            --primary-bg: #ffffff;
            --secondary-bg: #f8f9fa;
            --text-main: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
        }

        body {
            background-color: #f9fafb;
            background-image: none; /* Remove any body gradients */
        }

        /* Hero Section */
        .main-hero {
            position: relative;
            background-image: url('assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 18, 61, 0.85); /* #0F123D with opacity */
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
        }

        .hero-content {
            text-align: center;
            color: white;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .hero-content p {
            font-size: 1.25rem;
            margin-bottom: 3rem;
            font-weight: 400;
            opacity: 0.9;
        }

        /* Search Box - Floating Style */
        .search-box {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 1000px;
            margin: 0 auto;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--gray-700);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            color: var(--text-main);
            background-color: #fff;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .search-form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .btn-search-hero {
            background-color: var(--primary-color); /* Solid color */
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            height: 52px; /* Match input height + borders */
            font-size: 1rem;
            transition: background-color 0.2s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-search-hero:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Section Styling */
        section {
            padding: 100px 0;
        }

        .section-title {
            color: var(--text-main);
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .section-subtitle {
            color: var(--text-secondary);
            margin-bottom: 4rem;
            font-size: 1.1rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Listings */
        .featured-listings {
            background-color: var(--primary-bg);
        }

        .listings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        .listing-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); /* Very subtle shadow */
        }

        .listing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: var(--secondary-color);
        }

        .listing-image-link {
            position: relative;
            display: block;
            height: 250px;
            overflow: hidden;
        }

        .listing-image-link img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .listing-card:hover .listing-image-link img {
            transform: scale(1.05);
        }

        .listing-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background-color: var(--secondary-color); /* Solid Gold */
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            z-index: 5;
        }

        .owner-type-badge {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: rgba(15, 18, 61, 0.85);
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 5;
            backdrop-filter: blur(4px);
        }

        .owner-type-badge.emlakci { border-left: 3px solid #16a34a; }
        .owner-type-badge.bireysel { border-left: 3px solid #2563eb; }
        .owner-type-badge.admin { border-left: 3px solid #D3AF37; }

        .listing-info {
            padding: 25px;
        }

        .listing-info h3 {
            font-size: 1.25rem;
            margin-bottom: 10px;
        }
        
        .listing-info h3 a {
            color: var(--text-main);
            text-decoration: none;
            transition: color 0.2s;
        }

        .listing-info h3 a:hover {
            color: var(--primary-color);
        }

        .location {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .price {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .property-features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.9rem;
            color: var(--text-secondary);
            gap: 5px;
        }
        
        .feature-item i {
            font-size: 1.1rem;
            color: var(--primary-color);
        }

        .btn-view-details {
            background-color: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            width: 100%;
            margin-top: 20px;
            border-radius: 6px;
            padding: 10px;
            font-weight: 600;
            transition: all 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-view-details:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-large {
            padding: 15px 40px;
            font-size: 1.1rem;
        }

        /* Why Us */
        .why-us-section {
            background-color: var(--secondary-bg);
        }

        .features-grid-new {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
        }

        .feature-box {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s;
            border: 1px solid transparent;
        }

        .feature-box:hover {
            transform: translateY(-10px);
            border-color: var(--border-color);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .feature-icon-new {
            width: 80px;
            height: 80px;
            background-color: rgba(15, 18, 61, 0.08); /* #0F123D with opacity */
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
            transition: all 0.3s;
        }

        .feature-box:hover .feature-icon-new {
            background-color: var(--primary-color);
            color: white;
        }

        .feature-box h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--text-main);
        }

        .feature-box p {
            color: var(--text-secondary);
            line-height: 1.6;
        }


        /* Responsive */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.2rem;
            }
            
            .search-box {
                padding: 1.5rem;
                margin: 0 1rem;
            }

            .main-hero {
                height: auto;
                padding: 120px 0 60px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="main-hero">
            <div class="container">
                <div class="hero-content">
                    <h1><?php echo $lang == 'tr' ? 'Hayalinizi Gerçeğe Dönüştürün' : 'Turn Your Dreams Into Reality'; ?></h1>
                    <p><?php echo $lang == 'tr' ? 'Binlerce ilan arasından size en uygun olanı seçin' : 'Choose the one that suits you best among thousands of listings'; ?></p>
                    
                    <div class="search-box">
                        <!-- Tabs removed as per request -->
                        
                        <form method="GET" action="ilanlar" class="search-form-new" id="homepage-search-form">
                            <div class="search-form-grid">
                                <div class="form-group">
                                    <label><?php echo t('listing_type'); ?></label>
                                    <select name="listing_type" id="listing-type-home" class="form-control">
                                        <option value=""><?php echo $lang == 'tr' ? 'Tümü' : 'All'; ?></option>
                                        <option value="satilik"><?php echo t('for_sale'); ?></option>
                                        <option value="kiralik"><?php echo t('for_rent'); ?></option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label><?php echo t('property_type'); ?></label>
                                    <select name="property_type" id="property-type-home" class="form-control">
                                        <option value="all"><?php echo $lang == 'tr' ? 'Tüm Türler' : 'All Types'; ?></option>
                                        <option value="ev" <?php echo $property_type == 'ev' ? 'selected' : ''; ?>><?php echo t('house'); ?></option>
                                        <option value="daire" <?php echo $property_type == 'daire' ? 'selected' : ''; ?>><?php echo t('apartment'); ?></option>
                                        <option value="arsa" <?php echo $property_type == 'arsa' ? 'selected' : ''; ?> data-hide-for-rent="true"><?php echo t('land'); ?></option>
<option value="tarla" <?php echo $property_type == 'tarla' ? 'selected' : ''; ?> data-hide-for-rent="true"><?php echo t('tarla'); ?></option>
                                        <option value="dukkan" <?php echo $property_type == 'dukkan' ? 'selected' : ''; ?>><?php echo t('shop'); ?></option>
                                        <option value="villa" <?php echo $property_type == 'villa' ? 'selected' : ''; ?>><?php echo t('villa'); ?></option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label><?php echo $lang == 'tr' ? 'Şehir' : 'City'; ?></label>
                                    <select name="city" id="city-select-home" class="form-control">
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

                                <div class="form-group">
                                    <label><?php echo $lang == 'tr' ? 'İlçe' : 'District'; ?></label>
                                    <select name="district" id="district-select-home" class="form-control">
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

                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn-search-hero" style="width: 100%;">
                                        <i class="fas fa-search"></i> <?php echo $lang == 'tr' ? 'ARA' : 'SEARCH'; ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Listings -->
        <section class="featured-listings">
            <div class="container">
                <h2 class="section-title text-center"><?php echo $lang == 'tr' ? 'Öne Çıkan İlanlar' : 'Featured Listings'; ?></h2>
                <p class="section-subtitle text-center"><?php echo $lang == 'tr' ? 'Sizin için seçtiğimiz en özel fırsatları inceleyin.' : 'Check out the most special opportunities we have chosen for you.'; ?></p>
                
                <div class="listings-grid">
                    <?php if (empty($featured_listings)): ?>
                        <p class="no-results" data-i18n="no_listings"><?php echo t('no_listings'); ?></p>
                    <?php else: ?>
                        <?php foreach ($featured_listings as $listing): ?>
                            <div class="listing-card" data-listing-id="<?php echo $listing['id']; ?>">
                                <?php 
                                // Yeni SEO-friendly URL formatı: detay/{listing_type}/{property_type}/{city}/{slug}
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
                                    
                                    $listing_title = !empty($listing['title_' . $lang]) ? $listing['title_' . $lang] : t('property');
                                    $url = '/detay/' . $listing_type . '/' . $property_type_slug . '/' . $city . '/' . $listing['slug'];
                                } else {
                                    $url = '/ilan.php?id=' . $listing['id'];
                                }
                                ?>
                                <a href="<?php echo $url; ?>" class="listing-image-link">
                                    <div class="listing-badge"><?php echo t($listing['property_type']); ?></div>
                                    <div class="owner-type-badge <?php echo $listing['owner_type'] ?? 'admin'; ?>">
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

                                    <img src="<?php echo !empty($listing['image1']) ? '/uploads/' . $listing['image1'] : '/assets/images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($listing_title); ?>" loading="lazy">
                                </a>
                                <div class="listing-info">
                                    <h3><a href="<?php echo $url; ?>" class="listing-title-link"><?php echo htmlspecialchars($listing_title); ?></a></h3>
                                    <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($listing['city']); ?> / <?php echo htmlspecialchars($listing['district']); ?></p>
                                    <p class="price"><?php echo number_format($listing['price'], 0, ',', '.'); ?> <?php echo t('currency'); ?></p>
                                    
                                    <div class="property-features-grid">
                                        <?php if ($listing['area']): ?>
                                            <div class="feature-item">
                                                <i class="fas fa-ruler-combined"></i>
                                                <span><?php echo $listing['area']; ?> m²</span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($listing['rooms']): ?>
                                            <div class="feature-item">
                                                <i class="fas fa-bed"></i>
                                                <span><?php echo $listing['rooms']; ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($listing['bathrooms']): ?>
                                            <div class="feature-item">
                                                <i class="fas fa-bath"></i>
                                                <span><?php echo $listing['bathrooms']; ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <a href="<?php echo $url; ?>" class="btn btn-secondary btn-view-details">
                                        <?php echo $lang == 'tr' ? 'Detayları Gör' : 'View Details'; ?> <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="text-center" style="margin-top: 40px;">
                    <a href="ilanlar.php" class="btn btn-primary btn-large"><?php echo $lang == 'tr' ? 'Tüm İlanları İncele' : 'View All Listings'; ?></a>
                </div>
            </div>
        </section>

        <!-- Why Us Section -->
        <section class="why-us-section">
            <div class="container">
                <h2 class="section-title"><?php echo $lang == 'tr' ? 'Neden Biz?' : 'Why Us?'; ?></h2>
                <p class="section-subtitle"><?php echo $lang == 'tr' ? 'Emlak sektöründeki deneyimimiz ve güvenilirliğimizle yanınızdayız.' : 'We are with you with our experience and reliability in the real estate sector.'; ?></p>
                
                <div class="features-grid-new">
                    <div class="feature-box">
                        <div class="feature-icon-new">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3><?php echo $lang == 'tr' ? 'Güvenilir Hizmet' : 'Reliable Service'; ?></h3>
                        <p><?php echo $lang == 'tr' ? 'Tüm işlemlerinizde şeffaflık ve güven ilkesiyle hareket ediyoruz.' : 'We act with the principle of transparency and trust in all your transactions.'; ?></p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon-new">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3><?php echo $lang == 'tr' ? 'Uzman Kadro' : 'Expert Team'; ?></h3>
                        <p><?php echo $lang == 'tr' ? 'Alanında uzman danışmanlarımızla size en doğru yönlendirmeleri yapıyoruz.' : 'We make the right guidance for you with our expert consultants.'; ?></p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon-new">
                            <i class="fas fa-home"></i>
                        </div>
                        <h3><?php echo $lang == 'tr' ? 'Geniş Portföy' : 'Wide Portfolio'; ?></h3>
                        <p><?php echo $lang == 'tr' ? 'Her bütçeye ve ihtiyaca uygun zengin gayrimenkul seçenekleri sunuyoruz.' : 'We offer rich real estate options suitable for every budget and need.'; ?></p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon-new">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3><?php echo $lang == 'tr' ? '7/24 Destek' : '24/7 Support'; ?></h3>
                        <p><?php echo $lang == 'tr' ? 'Satış öncesi ve sonrası her zaman yanınızdayız.' : 'We are always with you before and after sales.'; ?></p>
                    </div>
                </div>
            </div>
        </section>



        <!-- Call to Action -->
        <section style="background: var(--primary-color); padding: 60px 0; color: white; text-align: center;">
            <div class="container">
                <h2 style="font-size: 2.5rem; margin-bottom: 20px;"><?php echo $lang == 'tr' ? 'Hayalinizi Gerçeğe Dönüştürmeye Hazır mısınız?' : 'Ready to Turn Your Dreams Into Reality?'; ?></h2>
                <p style="font-size: 1.2rem; margin-bottom: 30px; opacity: 0.9;"><?php echo $lang == 'tr' ? 'Hemen bizimle iletişime geçin, size en uygun seçenekleri sunalım.' : 'Contact us now, let us offer you the most suitable options.'; ?></p>
                <a href="#property-request" class="btn btn-secondary btn-large" style="background: var(--secondary-color); color: white; border: none;"><?php echo $lang == 'tr' ? 'Bize Ulaşın' : 'Contact Us'; ?></a>
            </div>
        </section>

        <!-- Property Request Form (Hidden or Bottom) -->
        <section id="property-request" class="property-request-section" style="background: #f8fafc; padding: 80px 0;">
            <div class="container">
                <div class="property-request-content" style="max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: var(--shadow-lg);">
                    <div class="property-request-header text-center mb-4">
                        <h2 style="color: var(--primary-color); margin-bottom: 10px;"><?php echo $lang == 'tr' ? 'Bize Ulaşın' : 'Contact Us'; ?></h2>
                        <p><?php echo $lang == 'tr' ? 'Sorularınız için aşağıdaki formu doldurabilirsiniz.' : 'You can fill out the form below for your questions.'; ?></p>
                    </div>
                    <form method="POST" action="process_property_request.php" class="property-request-form">
                        <!-- Existing form fields... simplified for this view -->
                        <div class="form-group mb-3">
                            <label><?php echo $lang == 'tr' ? 'Adınız Soyadınız' : 'Your Name'; ?></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label><?php echo $lang == 'tr' ? 'Telefon' : 'Phone'; ?></label>
                            <input type="tel" name="phone" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label><?php echo $lang == 'tr' ? 'Mesajınız' : 'Your Message'; ?></label>
                            <textarea name="message" class="form-control" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block"><?php echo $lang == 'tr' ? 'Gönder' : 'Send'; ?></button>
                    </form>
                </div>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/location-filter.js"></script>
    <script>
        // Homepage search form - filter property types based on listing type
        const listingTypeHome = document.getElementById('listing-type-home');
        const propertyTypeHome = document.getElementById('property-type-home');
        
        function filterHomePropertyTypes() {
            const listingType = listingTypeHome ? listingTypeHome.value : '';
            const isRental = listingType === 'kiralik';
            
            if (propertyTypeHome) {
                const options = propertyTypeHome.querySelectorAll('option');
                options.forEach(option => {
                    if (option.hasAttribute('data-hide-for-rent') && isRental) {
                        option.style.display = 'none';
                        if (option.selected) {
                            propertyTypeHome.value = 'all';
                        }
                    } else {
                        option.style.display = '';
                    }
                });
            }
        }
        
        if (listingTypeHome) {
            listingTypeHome.addEventListener('change', filterHomePropertyTypes);
            filterHomePropertyTypes();
        }
        
        function setSearchType(type) {
            document.getElementById('search_status').value = type;
            document.querySelectorAll('.search-tab').forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
