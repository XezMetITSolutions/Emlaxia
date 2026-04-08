<?php
/**
 * Üyenin Favori İlanları
 */
require_once '../config.php';
require_once '../includes/auth.php';

requireUser();

$user_id = $_SESSION['user_id'];

try {
    // Favori ilanları getir
    $stmt = $pdo->prepare("SELECT f.*, l.title_tr, l.title_en, l.price, l.image1, l.property_type, l.listing_type, l.city 
                           FROM favorites f 
                           JOIN listings l ON f.listing_id = l.id 
                           WHERE f.user_id = :uid 
                           ORDER BY f.created_at DESC");
    $stmt->execute([':uid' => $user_id]);
    $favorites = $stmt->fetchAll();
} catch (Throwable $e) {
    die("Favoriler yüklenirken hata oluştu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'tr' ? 'Favorilerim' : 'My Favorites'; ?> - Emlaxia</title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f8fafc; margin: 0; font-family: 'Inter', sans-serif; }
        .dashboard-container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        .page-header { margin-bottom: 2rem; }
        .page-title { font-size: 1.75rem; font-weight: 800; color: #0f172a; }
        
        .fav-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
        .fav-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: transform 0.2s; position: relative; }
        .fav-card:hover { transform: translateY(-4px); }
        .fav-img { width: 100%; height: 200px; object-fit: cover; }
        .fav-body { padding: 1.25rem; }
        .fav-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0 0 0.5rem 0; height: 1.4em; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .fav-price { font-size: 1.2rem; font-weight: 700; color: #10b981; }
        .fav-remove { position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.9); color: #ef4444; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; }
    </style>
</head>
<body>
    <?php include 'includes/uye_header.php'; ?>

    <div class="dashboard-container">
        <div class="page-header">
            <h1 class="page-title"><?php echo $lang === 'tr' ? 'Favorilerim' : 'Favorites'; ?></h1>
        </div>

        <div class="fav-grid">
            <?php if (empty($favorites)): ?>
                <div style="grid-column: 1/-1; background: white; padding: 4rem; text-align: center; border-radius: 16px;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">❤️</div>
                    <p style="color: #64748b;"><?php echo $lang === 'tr' ? 'Henüz favori ilanınız yok.' : 'You haven\'t added any favorites yet.'; ?></p>
                    <a href="/ilanlar" class="btn btn-primary" style="margin-top: 1rem;"><?php echo $lang === 'tr' ? 'İlanları İncele' : 'Browse Listings'; ?></a>
                </div>
            <?php else: ?>
                <?php foreach ($favorites as $fav): ?>
                    <div class="fav-card">
                        <a href="/ilan.php?id=<?php echo $fav['listing_id']; ?>">
                            <img src="<?php echo !empty($fav['image1']) ? '/uploads/' . $fav['image1'] : '/assets/images/placeholder.jpg'; ?>" class="fav-img" alt="">
                        </a>
                        <div class="fav-body">
                            <h4 class="fav-title"><?php echo htmlspecialchars($fav['title_' . $lang]); ?></h4>
                            <div class="fav-price"><?php echo number_format($fav['price'], 0, ',', '.'); ?> TL</div>
                            <div style="font-size: 0.85rem; color: #64748b; margin-top: 0.5rem;">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($fav['city']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
