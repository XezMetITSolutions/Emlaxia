<?php
/**
 * Bireysel Dashboard
 */
require_once '../config.php';
require_once '../includes/auth.php';
requireBireysel();

try {
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM listings WHERE user_id = :uid AND user_type = 'bireysel'");
    $stmt->execute([':uid' => $user_id]);
    $total_listings = $stmt->fetch()['total'] ?? 0;

    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM listings WHERE user_id = :uid AND user_type = 'bireysel' AND status = 'active' AND approval_status = 'approved'");
    $stmt->execute([':uid' => $user_id]);
    $active_listings = $stmt->fetch()['total'] ?? 0;

    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM listings WHERE user_id = :uid AND user_type = 'bireysel' AND approval_status = 'pending'");
    $stmt->execute([':uid' => $user_id]);
    $pending_listings = $stmt->fetch()['total'] ?? 0;

    $stmt = $pdo->prepare("SELECT * FROM listings WHERE user_id = :uid AND user_type = 'bireysel' ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([':uid' => $user_id]);
    $recent_listings = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT o.*, l.title_tr, l.title_en FROM offers o JOIN listings l ON o.listing_id = l.id WHERE l.user_id = :uid AND l.user_type = 'bireysel' ORDER BY o.created_at DESC LIMIT 5");
    $stmt->execute([':uid' => $user_id]);
    $recent_offers = $stmt->fetchAll();

    // Favori sayısı
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM favorites WHERE user_id = :uid");
    $stmt->execute([':uid' => $user_id]);
    $total_favorites = $stmt->fetch()['total'] ?? 0;

    // Okunmamış mesaj sayısı
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM messages WHERE receiver_id = :uid AND is_read = 0");
    $stmt->execute([':uid' => $user_id]);
    $unread_messages = $stmt->fetch()['total'] ?? 0;

    // Son favoriler
    $stmt = $pdo->prepare("SELECT f.*, l.title_tr, l.title_en, l.price, l.image1 FROM favorites f JOIN listings l ON f.listing_id = l.id WHERE f.user_id = :uid ORDER BY f.created_at DESC LIMIT 3");
    $stmt->execute([':uid' => $user_id]);
    $recent_favorites = $stmt->fetchAll();
} catch (Throwable $e) {
    die("Panel verileri yüklenirken bir hata oluştu: " . $e->getMessage() . " - Lütfen migration çalıştığınızdan emin olun.");
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $lang === 'tr' ? 'Bireysel Panel' : 'Individual Dashboard'; ?> - Emlaxia
    </title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f1f5f9;
            margin: 0;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .dashboard-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .dashboard-subtitle {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .stat-card.total {
            border-left: 4px solid #3b82f6;
        }

        .stat-card.active {
            border-left: 4px solid #22c55e;
        }

        .stat-card.pending {
            border-left: 4px solid #f59e0b;
        }

        .section-card {
            background: white;
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #0f172a;
        }

        .section-link {
            color: #3b82f6;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .listing-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f8fafc;
        }

        .listing-item:last-child {
            border-bottom: none;
        }

        .listing-thumb {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            object-fit: cover;
            background: #f1f5f9;
            flex-shrink: 0;
        }

        .listing-info {
            flex: 1;
            min-width: 0;
        }

        .listing-info-title {
            font-weight: 600;
            font-size: 0.9rem;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .listing-info-meta {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 2px;
        }

        .status-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
        }

        .status-approved {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-rejected {
            background: #fef2f2;
            color: #dc2626;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #94a3b8;
        }

        .btn-new-listing {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-new-listing:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/bireysel_header.php'; ?>

    <div class="dashboard-container">
        <div
            style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1rem;">
            <div>
                <h1 class="dashboard-title">
                    <?php echo $lang === 'tr' ? 'Hoş Geldiniz' : 'Welcome'; ?>,
                    <?php echo htmlspecialchars($_SESSION['user_full_name'] ?? $_SESSION['user_username']); ?>! 👋
                </h1>
                <p class="dashboard-subtitle">
                    <?php echo $lang === 'tr' ? 'Mülkünüzü kolayca ilan verin ve takip edin.' : 'Easily list and track your property.'; ?>
                </p>
            </div>
            <a href="/bireysel/listing_form" class="btn-new-listing">➕
                <?php echo $lang === 'tr' ? 'İlan Ver' : 'Post Listing'; ?>
            </a>
        </div>

        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon">📋</div>
                <div class="stat-value">
                    <?php echo $total_listings; ?>
                </div>
                <div class="stat-label">
                    <?php echo $lang === 'tr' ? 'Toplam İlan' : 'Total Listings'; ?>
                </div>
            </div>
            <div class="stat-card active">
                <div class="stat-icon">✅</div>
                <div class="stat-value">
                    <?php echo $active_listings; ?>
                </div>
                <div class="stat-label">
                    <?php echo $lang === 'tr' ? 'Aktif İlan' : 'Active'; ?>
                </div>
            </div>
            <div class="stat-card pending">
                <div class="stat-icon">⏳</div>
                <div class="stat-value">
                    <?php echo $pending_listings; ?>
                </div>
                <div class="stat-label">
                    <?php echo $lang === 'tr' ? 'Onay Bekleyen' : 'Pending'; ?>
                </div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #f43f5e;">
                <div class="stat-icon">❤️</div>
                <div class="stat-value">
                    <?php echo $total_favorites; ?>
                </div>
                <div class="stat-label">
                    <?php echo $lang === 'tr' ? 'Favoriler' : 'Favorites'; ?>
                </div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
                <div class="stat-icon">✉️</div>
                <div class="stat-value">
                    <?php echo $unread_messages; ?>
                </div>
                <div class="stat-label">
                    <?php echo $lang === 'tr' ? 'Yeni Mesaj' : 'New Messages'; ?>
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">🏠
                    <?php echo $lang === 'tr' ? 'İlanlarım' : 'My Listings'; ?>
                </h2>
                <a href="/bireysel/ilanlar" class="section-link">
                    <?php echo $lang === 'tr' ? 'Tümünü Gör →' : 'View All →'; ?>
                </a>
            </div>
            <?php if (empty($recent_listings)): ?>
                <div class="empty-state">
                    <div style="font-size:2.5rem;margin-bottom:0.75rem;">📭</div>
                    <p>
                        <?php echo $lang === 'tr' ? 'Henüz ilan eklemediniz.' : 'No listings yet.'; ?>
                    </p>
                    <a href="/bireysel/listing_form" class="btn-new-listing" style="margin-top:1rem;">➕
                        <?php echo $lang === 'tr' ? 'İlk İlanınızı Ekleyin' : 'Add Your First Listing'; ?>
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($recent_listings as $l): ?>
                    <div class="listing-item">
                        <?php if (!empty($l['image1'])): ?>
                            <img src="/uploads/<?php echo htmlspecialchars($l['image1']); ?>" class="listing-thumb" alt="">
                        <?php else: ?>
                            <div class="listing-thumb"
                                style="display:flex;align-items:center;justify-content:center;font-size:1.5rem;">🏠</div>
                        <?php endif; ?>
                        <div class="listing-info">
                            <div class="listing-info-title">
                                <?php echo htmlspecialchars($lang === 'tr' ? $l['title_tr'] : $l['title_en']); ?>
                            </div>
                            <div class="listing-info-meta">
                                <?php echo number_format($l['price'], 0, ',', '.') . ' TL'; ?>
                            </div>
                        </div>
                        <span class="status-badge status-<?php echo $l['approval_status']; ?>">
                            <?php echo ['approved' => $lang === 'tr' ? 'Onaylı' : 'Approved', 'pending' => $lang === 'tr' ? 'Bekliyor' : 'Pending', 'rejected' => $lang === 'tr' ? 'Reddedildi' : 'Rejected'][$l['approval_status']] ?? $l['approval_status']; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Son Favoriler ve Hızlı Linkler -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div class="section-card" style="margin-bottom: 0;">
                <div class="section-header">
                    <h2 class="section-title">❤️ <?php echo $lang === 'tr' ? 'Son Favorilerim' : 'Recent Favorites'; ?></h2>
                    <a href="/bireysel/favorites" class="section-link"><?php echo $lang === 'tr' ? 'Tümü →' : 'All →'; ?></a>
                </div>
                <?php if (empty($recent_favorites)): ?>
                    <p style="color: #94a3b8; font-size: 0.9rem; text-align: center; padding: 1rem;">
                        <?php echo $lang === 'tr' ? 'Henüz favori eklemediniz.' : 'No favorites yet.'; ?>
                    </p>
                <?php else: ?>
                    <?php foreach ($recent_favorites as $fav): ?>
                        <div class="listing-item">
                            <div class="listing-info">
                                <div class="listing-info-title"><?php echo htmlspecialchars($lang == 'tr' ? $fav['title_tr'] : $fav['title_en']); ?></div>
                                <div class="listing-info-meta"><?php echo number_format($fav['price'], 0, ',', '.') . ' TL'; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="section-card" style="margin-bottom: 0;">
                <div class="section-header">
                    <h2 class="section-title">✉️ <?php echo $lang === 'tr' ? 'Hızlı İşlemler' : 'Quick Actions'; ?></h2>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <a href="/bireysel/mesajlar" class="btn-new-listing" style="background: white; border: 1px solid #e2e8f0; color: #0f172a; justify-content: flex-start;">
                        ✉️ <?php echo $lang === 'tr' ? 'Mesajlarımı Oku' : 'Read Messages'; ?>
                    </a>
                    <a href="/bireysel/teklifler" class="btn-new-listing" style="background: white; border: 1px solid #e2e8f0; color: #0f172a; justify-content: flex-start;">
                        💼 <?php echo $lang === 'tr' ? 'Gelen Teklifleri Gör' : 'View Offers'; ?>
                    </a>
                    <a href="/bireysel/profil" class="btn-new-listing" style="background: white; border: 1px solid #e2e8f0; color: #0f172a; justify-content: flex-start;">
                        ⚙️ <?php echo $lang === 'tr' ? 'Profilimi Düzenle' : 'Edit Profile'; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>