<?php
/**
 * Üyenin Verdiği Teklifler
 */
require_once '../config.php';
require_once '../includes/auth.php';

requireUser();

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

try {
    // Verdiğim tüm teklifleri getir
    $stmt = $pdo->prepare("SELECT o.*, l.title_tr, l.title_en, l.price, l.image1 
                           FROM offers o 
                           JOIN listings l ON o.listing_id = l.id 
                           WHERE o.customer_email = :email 
                           ORDER BY o.created_at DESC");
    $stmt->execute([':email' => $user_email]);
    $my_offers = $stmt->fetchAll();
} catch (Throwable $e) {
    die("Teklifler yüklenirken hata oluştu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'tr' ? 'Verdiğim Teklifler' : 'My Offers'; ?> - Emlaxia</title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f8fafc; margin: 0; font-family: 'Inter', sans-serif; }
        .dashboard-container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        .page-header { margin-bottom: 2rem; }
        .page-title { font-size: 1.75rem; font-weight: 800; color: #0f172a; }
        
        .offers-list { display: grid; gap: 1rem; }
        .offer-item { background: white; border-radius: 16px; padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .offer-info { display: flex; align-items: center; gap: 1.5rem; }
        .offer-img { width: 80px; height: 60px; border-radius: 8px; object-fit: cover; }
        .offer-details h4 { margin: 0 0 0.5rem 0; font-size: 1.1rem; color: #0f172a; }
        .offer-meta { font-size: 0.85rem; color: #64748b; }
        
        .offer-actions { text-align: right; }
        .price-tag { display: block; font-size: 1.25rem; font-weight: 700; color: #10b981; margin-bottom: 0.5rem; }
        
        @media (max-width: 768px) {
            .offer-item { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .offer-actions { width: 100%; text-align: left; }
        }
    </style>
</head>
<body>
    <?php include 'uye/includes/uye_header.php'; ?>

    <div class="dashboard-container">
        <div class="page-header">
            <h1 class="page-title"><?php echo $lang === 'tr' ? 'Verdiğim Teklifler' : 'My Offers'; ?></h1>
        </div>

        <div class="offers-list">
            <?php if (empty($my_offers)): ?>
                <div style="background: white; padding: 4rem; text-align: center; border-radius: 16px;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">💼</div>
                    <p style="color: #64748b;"><?php echo $lang === 'tr' ? 'Henüz bir teklif vermediniz.' : 'You haven\'t made any offers yet.'; ?></p>
                    <a href="/ilanlar" class="btn btn-primary" style="margin-top: 1rem;"><?php echo $lang === 'tr' ? 'İlanları İncele' : 'Browse Listings'; ?></a>
                </div>
            <?php else: ?>
                <?php foreach ($my_offers as $offer): ?>
                    <div class="offer-item">
                        <div class="offer-info">
                            <img src="<?php echo !empty($offer['image1']) ? '/uploads/' . $offer['image1'] : '/assets/images/placeholder.jpg'; ?>" class="offer-img" alt="">
                            <div class="offer-details">
                                <h4><?php echo htmlspecialchars($offer['title_' . $lang]); ?></h4>
                                <div class="offer-meta">
                                    <span>📅 <?php echo date('d.m.Y H:i', strtotime($offer['created_at'])); ?></span>
                                    <span style="margin-left: 1rem;">🆔 İlan ID: #<?php echo $offer['listing_id']; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="offer-actions">
                            <span class="price-tag"><?php echo number_format($offer['offer_amount'], 0, ',', '.'); ?> TL</span>
                            <span class="badge badge-<?php echo $offer['status']; ?>"><?php echo t($offer['status']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
