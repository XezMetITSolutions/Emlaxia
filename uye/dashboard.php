<?php
/**
 * Normal Üye Dashboard
 */
require_once '../config.php';
require_once '../includes/auth.php';

// Herhangi bir giriş yapmış kullanıcı erişebilir, ama tipine göre dashboard'u özelleştirelim
requireUser();

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

try {
    // Toplam verdiğim teklifler
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM offers WHERE customer_email = :email");
    $stmt->execute([':email' => $user_email]);
    $total_my_offers = $stmt->fetch()['total'] ?? 0;

    // Favorilerim
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM favorites WHERE user_id = :uid");
    $stmt->execute([':uid' => $user_id]);
    $total_favorites = $stmt->fetch()['total'] ?? 0;

    // Mesajlarım (Okunmamış)
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM messages WHERE receiver_id = :uid AND is_read = 0");
    $stmt->execute([':uid' => $user_id]);
    $unread_messages = $stmt->fetch()['total'] ?? 0;

    // Son verdiğim teklifler
    $stmt = $pdo->prepare("SELECT o.*, l.title_tr, l.title_en, l.price, l.image1 
                           FROM offers o 
                           JOIN listings l ON o.listing_id = l.id 
                           WHERE o.customer_email = :email 
                           ORDER BY o.created_at DESC LIMIT 5");
    $stmt->execute([':email' => $user_email]);
    $recent_my_offers = $stmt->fetchAll();

} catch (Throwable $e) {
    die("Panel yüklenirken hata oluştu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'tr' ? 'Hesabım' : 'My Account'; ?> - Emlaxia</title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f8fafc; margin: 0; font-family: 'Inter', sans-serif; }
        .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 1rem; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .stat-info .value { font-size: 1.5rem; font-weight: 700; color: #0f172a; display: block; }
        .stat-info .label { font-size: 0.875rem; color: #64748b; }
        
        .panel-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
        .content-card { background: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; }
        .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .card-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0; }
        
        .offer-table { width: 100%; border-collapse: collapse; }
        .offer-table th { text-align: left; padding: 1rem 1.5rem; background: #f8fafc; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #64748b; }
        .offer-table td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        .offer-table tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>
    <?php include 'includes/uye_header.php'; ?>

    <div class="dashboard-container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">💼</div>
                <div class="stat-info">
                    <span class="value"><?php echo $total_my_offers; ?></span>
                    <span class="label"><?php echo $lang === 'tr' ? 'Verdiğim Teklifler' : 'My Offers'; ?></span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">❤️</div>
                <div class="stat-info">
                    <span class="value"><?php echo $total_favorites; ?></span>
                    <span class="label"><?php echo $lang === 'tr' ? 'Favori İlanlarım' : 'Favorite Listings'; ?></span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">✉️</div>
                <div class="stat-info">
                    <span class="value"><?php echo $unread_messages; ?></span>
                    <span class="label"><?php echo $lang === 'tr' ? 'Okunmamış Mesajlar' : 'Unread Messages'; ?></span>
                </div>
            </div>
        </div>

        <div class="panel-grid">
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title"><?php echo $lang === 'tr' ? 'Son Verdiğim Teklifler' : 'Recent Offers'; ?></h3>
                    <a href="/uye/tekliflerim" class="btn btn-sm btn-outline-primary"><?php echo $lang === 'tr' ? 'Tümünü Gör' : 'View All'; ?></a>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_my_offers)): ?>
                        <div style="padding: 3rem; text-align: center; color: #64748b;">
                            <?php echo $lang === 'tr' ? 'Henüz bir teklif vermediniz.' : 'You haven\'t made any offers yet.'; ?>
                        </div>
                    <?php else: ?>
                        <table class="offer-table">
                            <thead>
                                <tr>
                                    <th><?php echo t('title'); ?></th>
                                    <th><?php echo t('offer_amount'); ?></th>
                                    <th><?php echo t('status'); ?></th>
                                    <th><?php echo $lang === 'tr' ? 'Tarih' : 'Date'; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_my_offers as $offer): ?>
                                    <tr>
                                        <td style="font-weight: 600; color: #0f172a;">
                                            <a href="/ilan.php?id=<?php echo $offer['listing_id']; ?>" target="_blank">
                                                <?php echo htmlspecialchars($offer['title_' . $lang]); ?>
                                            </a>
                                        </td>
                                        <td><?php echo number_format($offer['offer_amount'], 0, ',', '.'); ?> TL</td>
                                        <td>
                                            <span class="badge badge-<?php echo $offer['status']; ?>">
                                                <?php echo t($offer['status']); ?>
                                            </span>
                                        </td>
                                        <td style="color: #64748b;">
                                            <?php echo date('d.m.Y H:i', strtotime($offer['created_at'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
