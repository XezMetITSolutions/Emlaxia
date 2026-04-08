<?php
/**
 * Emlakçı Dashboard
 */
require_once '../config.php';
require_once '../includes/auth.php';
requireEmlakci();

try {
    $user_id = $_SESSION['user_id'];

    // İstatistikler
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM listings WHERE user_id = :uid AND user_type = 'emlakci'");
    $stmt->execute([':uid' => $user_id]);
    $total_listings = $stmt->fetch()['total'] ?? 0;

    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM listings WHERE user_id = :uid AND user_type = 'emlakci' AND status = 'active' AND approval_status = 'approved'");
    $stmt->execute([':uid' => $user_id]);
    $active_listings = $stmt->fetch()['total'] ?? 0;

    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM listings WHERE user_id = :uid AND user_type = 'emlakci' AND approval_status = 'pending'");
    $stmt->execute([':uid' => $user_id]);
    $pending_listings = $stmt->fetch()['total'] ?? 0;

    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM listings WHERE user_id = :uid AND user_type = 'emlakci' AND approval_status = 'rejected'");
    $stmt->execute([':uid' => $user_id]);
    $rejected_listings = $stmt->fetch()['total'] ?? 0;

    // Son ilanlar
    $stmt = $pdo->prepare("SELECT id, title_tr, title_en, property_type, price, status, approval_status, created_at, image1 
                            FROM listings WHERE user_id = :uid AND user_type = 'emlakci' 
                            ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([':uid' => $user_id]);
    $recent_listings = $stmt->fetchAll();

    // Gelen teklifler (son 5)
    $stmt = $pdo->prepare("SELECT o.*, l.title_tr, l.title_en 
                            FROM offers o 
                            JOIN listings l ON o.listing_id = l.id 
                            WHERE l.user_id = :uid AND l.user_type = 'emlakci'
                            ORDER BY o.created_at DESC LIMIT 5");
    $stmt->execute([':uid' => $user_id]);
    $recent_offers = $stmt->fetchAll();
    $total_offers = count($recent_offers);
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
        <?php echo $lang === 'tr' ? 'Emlakçı Paneli' : 'Agent Dashboard'; ?> - Emlaxia
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
            max-width: 1400px;
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
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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

        .stat-card.active {
            border-left: 4px solid #22c55e;
        }

        .stat-card.pending {
            border-left: 4px solid #f59e0b;
        }

        .stat-card.rejected {
            border-left: 4px solid #ef4444;
        }

        .stat-card.total {
            border-left: 4px solid #3b82f6;
        }

        .section-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .section-card {
            background: white;
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
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
            color: #1e88e5;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .section-link:hover {
            text-decoration: underline;
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
            white-space: nowrap;
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

        .offer-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f8fafc;
        }

        .offer-item:last-child {
            border-bottom: none;
        }

        .offer-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #0f172a;
        }

        .offer-amount {
            color: #16a34a;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .offer-listing {
            color: #64748b;
            font-size: 0.8rem;
            margin-top: 2px;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #94a3b8;
        }

        .empty-state-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
        }

        .btn-new-listing {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-new-listing:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(30, 136, 229, 0.3);
        }

        @media (max-width: 768px) {
            .section-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-container {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/emlakci_header.php'; ?>

    <div class="dashboard-container">
        <div
            style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <h1 class="dashboard-title">
                    <?php echo $lang === 'tr' ? 'Hoş Geldiniz' : 'Welcome'; ?>,
                    <?php echo htmlspecialchars($_SESSION['user_full_name'] ?? $_SESSION['user_username']); ?>! 👋
                </h1>
                <p class="dashboard-subtitle">
                    <?php echo $lang === 'tr' ? 'İlanlarınızı ve tekliflerinizi buradan yönetin.' : 'Manage your listings and offers here.'; ?>
                </p>
            </div>
            <a href="/emlakci/listing_form" class="btn-new-listing">
                ➕
                <?php echo $lang === 'tr' ? 'Yeni İlan Ekle' : 'Add New Listing'; ?>
            </a>
        </div>

        <!-- İstatistikler -->
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
                    <?php echo $lang === 'tr' ? 'Aktif İlan' : 'Active Listings'; ?>
                </div>
            </div>
            <div class="stat-card pending">
                <div class="stat-icon">⏳</div>
                <div class="stat-value">
                    <?php echo $pending_listings; ?>
                </div>
                <div class="stat-label">
                    <?php echo $lang === 'tr' ? 'Onay Bekleyen' : 'Pending Approval'; ?>
                </div>
            </div>
            <div class="stat-card rejected">
                <div class="stat-icon">❌</div>
                <div class="stat-value">
                    <?php echo $rejected_listings; ?>
                </div>
                <div class="stat-label">
                    <?php echo $lang === 'tr' ? 'Reddedilen' : 'Rejected'; ?>
                </div>
            </div>
        </div>

        <!-- Son İlanlar & Teklifler -->
        <div class="section-grid">
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">🏠
                        <?php echo $lang === 'tr' ? 'Son İlanlarım' : 'Recent Listings'; ?>
                    </h2>
                    <a href="/emlakci/ilanlar" class="section-link">
                        <?php echo $lang === 'tr' ? 'Tümünü Gör →' : 'View All →'; ?>
                    </a>
                </div>
                <?php if (empty($recent_listings)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📭</div>
                        <p>
                            <?php echo $lang === 'tr' ? 'Henüz ilan eklemediniz.' : 'No listings yet.'; ?>
                        </p>
                        <a href="/emlakci/listing_form" class="btn-new-listing"
                            style="margin-top: 1rem; display: inline-flex;">
                            ➕
                            <?php echo $lang === 'tr' ? 'İlk İlanınızı Ekleyin' : 'Add Your First Listing'; ?>
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($recent_listings as $listing): ?>
                        <div class="listing-item">
                            <?php if (!empty($listing['image1'])): ?>
                                <img src="/uploads/<?php echo htmlspecialchars($listing['image1']); ?>" class="listing-thumb"
                                    alt="">
                            <?php else: ?>
                                <div class="listing-thumb"
                                    style="display:flex;align-items:center;justify-content:center;font-size:1.5rem;">🏠</div>
                            <?php endif; ?>
                            <div class="listing-info">
                                <div class="listing-info-title">
                                    <?php echo htmlspecialchars($lang === 'tr' ? $listing['title_tr'] : $listing['title_en']); ?>
                                </div>
                                <div class="listing-info-meta">
                                    <?php echo number_format($listing['price'], 0, ',', '.') . ' TL'; ?>
                                </div>
                            </div>
                            <span class="status-badge status-<?php echo $listing['approval_status']; ?>">
                                <?php
                                $statuses = [
                                    'approved' => $lang === 'tr' ? 'Onaylı' : 'Approved',
                                    'pending' => $lang === 'tr' ? 'Bekliyor' : 'Pending',
                                    'rejected' => $lang === 'tr' ? 'Reddedildi' : 'Rejected',
                                ];
                                echo $statuses[$listing['approval_status']] ?? $listing['approval_status'];
                                ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">💬
                        <?php echo $lang === 'tr' ? 'Son Teklifler' : 'Recent Offers'; ?>
                    </h2>
                    <a href="/emlakci/teklifler" class="section-link">
                        <?php echo $lang === 'tr' ? 'Tümünü Gör →' : 'View All →'; ?>
                    </a>
                </div>
                <?php if (empty($recent_offers)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">💬</div>
                        <p>
                            <?php echo $lang === 'tr' ? 'Henüz teklif gelmedi.' : 'No offers yet.'; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recent_offers as $offer): ?>
                        <div class="offer-item">
                            <div class="offer-name">
                                <?php echo htmlspecialchars($offer['customer_name']); ?>
                            </div>
                            <div class="offer-amount">
                                <?php echo number_format($offer['offer_amount'], 0, ',', '.') . ' TL'; ?>
                            </div>
                            <div class="offer-listing">📌
                                <?php echo htmlspecialchars($lang === 'tr' ? $offer['title_tr'] : $offer['title_en']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>