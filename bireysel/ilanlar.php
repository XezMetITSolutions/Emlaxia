<?php
/**
 * Bireysel - İlan Listesi
 */
require_once '../config.php';
require_once '../includes/auth.php';
requireBireysel();

$user_id = $_SESSION['user_id'];
$filter_status = $_GET['status'] ?? 'all';
$where = "WHERE l.user_id = :uid AND l.user_type = 'bireysel'";
$params = [':uid' => $user_id];

if ($filter_status === 'approved') {
    $where .= " AND l.approval_status = 'approved'";
} elseif ($filter_status === 'pending') {
    $where .= " AND l.approval_status = 'pending'";
} elseif ($filter_status === 'rejected') {
    $where .= " AND l.approval_status = 'rejected'";
}

$stmt = $pdo->prepare("SELECT l.* FROM listings l $where ORDER BY l.created_at DESC");
$stmt->execute($params);
$listings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $lang === 'tr' ? 'İlanlarım' : 'My Listings'; ?> - Emlaxia
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

        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
        }

        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            color: #64748b;
            background: white;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .filter-tab:hover {
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .filter-tab.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .btn-add:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
        }

        .listing-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.25rem;
        }

        .listing-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .listing-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .listing-card-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #f1f5f9;
        }

        .listing-card-body {
            padding: 1.25rem;
        }

        .listing-card-title {
            font-weight: 600;
            font-size: 1rem;
            color: #0f172a;
            margin-bottom: 0.5rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .listing-card-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0.75rem;
        }

        .listing-card-price {
            font-weight: 700;
            font-size: 1.1rem;
            color: #0f172a;
        }

        .listing-card-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 0.75rem;
            border-top: 1px solid #f1f5f9;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-edit {
            background: #e0f2fe;
            color: #0284c7;
        }

        .btn-edit:hover {
            background: #bae6fd;
        }

        .btn-delete {
            background: #fef2f2;
            color: #dc2626;
        }

        .btn-delete:hover {
            background: #fecaca;
        }

        .status-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
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
            padding: 3rem;
            color: #94a3b8;
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            grid-column: 1/-1;
        }

        .rejection-reason {
            font-size: 0.75rem;
            color: #dc2626;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 1rem;
            }

            .listing-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/bireysel_header.php'; ?>

    <div class="page-container">
        <div class="page-header">
            <h1 class="page-title">🏠
                <?php echo $lang === 'tr' ? 'İlanlarım' : 'My Listings'; ?>
            </h1>
            <a href="/bireysel/listing_form" class="btn-add">➕
                <?php echo $lang === 'tr' ? 'İlan Ver' : 'Post Listing'; ?>
            </a>
        </div>

        <div class="filter-tabs">
            <a href="/bireysel/ilanlar" class="filter-tab <?php echo $filter_status === 'all' ? 'active' : ''; ?>">
                <?php echo $lang === 'tr' ? 'Tümü' : 'All'; ?>
            </a>
            <a href="/bireysel/ilanlar?status=approved"
                class="filter-tab <?php echo $filter_status === 'approved' ? 'active' : ''; ?>">✅
                <?php echo $lang === 'tr' ? 'Onaylı' : 'Approved'; ?>
            </a>
            <a href="/bireysel/ilanlar?status=pending"
                class="filter-tab <?php echo $filter_status === 'pending' ? 'active' : ''; ?>">⏳
                <?php echo $lang === 'tr' ? 'Bekliyor' : 'Pending'; ?>
            </a>
            <a href="/bireysel/ilanlar?status=rejected"
                class="filter-tab <?php echo $filter_status === 'rejected' ? 'active' : ''; ?>">❌
                <?php echo $lang === 'tr' ? 'Ret' : 'Rejected'; ?>
            </a>
        </div>

        <div class="listing-cards">
            <?php if (empty($listings)): ?>
                <div class="empty-state">
                    <div style="font-size:2.5rem;margin-bottom:0.75rem;">📭</div>
                    <p>
                        <?php echo $lang === 'tr' ? 'Bu kategoride ilan bulunamadı.' : 'No listings found.'; ?>
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($listings as $listing): ?>
                    <div class="listing-card">
                        <?php if (!empty($listing['image1'])): ?>
                            <img src="/uploads/<?php echo htmlspecialchars($listing['image1']); ?>" class="listing-card-img" alt="">
                        <?php else: ?>
                            <div class="listing-card-img"
                                style="display:flex;align-items:center;justify-content:center;font-size:3rem;">🏠</div>
                        <?php endif; ?>
                        <div class="listing-card-body">
                            <div class="listing-card-title">
                                <?php echo htmlspecialchars($lang === 'tr' ? $listing['title_tr'] : $listing['title_en']); ?>
                            </div>
                            <div class="listing-card-meta">
                                <span>
                                    <?php echo ucfirst($listing['property_type']); ?>
                                </span>
                                <span>
                                    <?php echo $listing['city']; ?>
                                </span>
                                <span class="status-badge status-<?php echo $listing['approval_status']; ?>">
                                    <?php echo ['approved' => $lang === 'tr' ? 'Onaylı' : 'Approved', 'pending' => $lang === 'tr' ? 'Bekliyor' : 'Pending', 'rejected' => $lang === 'tr' ? 'Reddedildi' : 'Rejected'][$listing['approval_status']] ?? ''; ?>
                                </span>
                            </div>
                            <div class="listing-card-price">
                                <?php echo number_format($listing['price'], 0, ',', '.'); ?> TL
                            </div>
                            <?php if ($listing['approval_status'] === 'rejected' && !empty($listing['rejection_reason'])): ?>
                                <div class="rejection-reason">📝
                                    <?php echo htmlspecialchars($listing['rejection_reason']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="listing-card-actions">
                                <a href="/bireysel/listing_form?id=<?php echo $listing['id']; ?>" class="btn-sm btn-edit">✏️
                                    <?php echo t('edit'); ?>
                                </a>
                                <a href="/bireysel/delete_listing?id=<?php echo $listing['id']; ?>" class="btn-sm btn-delete"
                                    onclick="return confirm('<?php echo $lang === 'tr' ? 'Bu ilanı silmek istediğinize emin misiniz?' : 'Are you sure?'; ?>')">🗑️
                                    <?php echo t('delete'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>