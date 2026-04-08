<?php
/**
 * Emlakçı - İlan Listesi
 */
require_once '../config.php';
require_once '../includes/auth.php';
requireEmlakci();

$user_id = $_SESSION['user_id'];

// Filtreleme
$filter_status = $_GET['status'] ?? 'all';
$where = "WHERE l.user_id = :uid AND l.user_type = 'emlakci'";
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
            max-width: 1400px;
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
            border-color: #1e88e5;
            color: #1e88e5;
        }

        .filter-tab.active {
            background: #1e88e5;
            color: white;
            border-color: #1e88e5;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .btn-add:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(30, 136, 229, 0.3);
        }

        .listings-table {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        .listings-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .listings-table th {
            background: #f8fafc;
            padding: 1rem;
            text-align: left;
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .listings-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
            color: #374151;
        }

        .listings-table tr:last-child td {
            border-bottom: none;
        }

        .listings-table tr:hover td {
            background: #fafbfc;
        }

        .listing-title-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .listing-thumb {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            object-fit: cover;
            background: #f1f5f9;
            flex-shrink: 0;
        }

        .listing-title-text {
            font-weight: 600;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 250px;
        }

        .status-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
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

        .action-btns {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.35rem 0.7rem;
            border-radius: 6px;
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

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #94a3b8;
        }

        .rejection-reason {
            font-size: 0.75rem;
            color: #dc2626;
            margin-top: 4px;
            max-width: 200px;
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 1rem;
            }

            .listings-table {
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/emlakci_header.php'; ?>

    <div class="page-container">
        <div class="page-header">
            <h1 class="page-title">🏠
                <?php echo $lang === 'tr' ? 'İlanlarım' : 'My Listings'; ?>
            </h1>
            <a href="/emlakci/listing_form" class="btn-add">➕
                <?php echo $lang === 'tr' ? 'Yeni İlan' : 'New Listing'; ?>
            </a>
        </div>

        <div class="filter-tabs">
            <a href="/emlakci/ilanlar" class="filter-tab <?php echo $filter_status === 'all' ? 'active' : ''; ?>">
                <?php echo $lang === 'tr' ? 'Tümü' : 'All'; ?> (
                <?php echo count($listings); ?>)
            </a>
            <a href="/emlakci/ilanlar?status=approved"
                class="filter-tab <?php echo $filter_status === 'approved' ? 'active' : ''; ?>">
                ✅
                <?php echo $lang === 'tr' ? 'Onaylı' : 'Approved'; ?>
            </a>
            <a href="/emlakci/ilanlar?status=pending"
                class="filter-tab <?php echo $filter_status === 'pending' ? 'active' : ''; ?>">
                ⏳
                <?php echo $lang === 'tr' ? 'Bekliyor' : 'Pending'; ?>
            </a>
            <a href="/emlakci/ilanlar?status=rejected"
                class="filter-tab <?php echo $filter_status === 'rejected' ? 'active' : ''; ?>">
                ❌
                <?php echo $lang === 'tr' ? 'Reddedildi' : 'Rejected'; ?>
            </a>
        </div>

        <div class="listings-table">
            <?php if (empty($listings)): ?>
                <div class="empty-state">
                    <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">📭</div>
                    <p>
                        <?php echo $lang === 'tr' ? 'Bu kategoride ilan bulunamadı.' : 'No listings found in this category.'; ?>
                    </p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>
                                <?php echo $lang === 'tr' ? 'İlan' : 'Listing'; ?>
                            </th>
                            <th>
                                <?php echo $lang === 'tr' ? 'Tip' : 'Type'; ?>
                            </th>
                            <th>
                                <?php echo $lang === 'tr' ? 'Fiyat' : 'Price'; ?>
                            </th>
                            <th>
                                <?php echo $lang === 'tr' ? 'Durum' : 'Status'; ?>
                            </th>
                            <th>
                                <?php echo $lang === 'tr' ? 'Tarih' : 'Date'; ?>
                            </th>
                            <th>
                                <?php echo $lang === 'tr' ? 'İşlemler' : 'Actions'; ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listings as $listing): ?>
                            <tr>
                                <td>
                                    <div class="listing-title-cell">
                                        <?php if (!empty($listing['image1'])): ?>
                                            <img src="/uploads/<?php echo htmlspecialchars($listing['image1']); ?>"
                                                class="listing-thumb" alt="">
                                        <?php else: ?>
                                            <div class="listing-thumb"
                                                style="display:flex;align-items:center;justify-content:center;font-size:1.2rem;">🏠
                                            </div>
                                        <?php endif; ?>
                                        <div class="listing-title-text">
                                            <?php echo htmlspecialchars($lang === 'tr' ? $listing['title_tr'] : $listing['title_en']); ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php echo ucfirst($listing['property_type']); ?>
                                </td>
                                <td style="font-weight: 600;">
                                    <?php echo number_format($listing['price'], 0, ',', '.') . ' TL'; ?>
                                </td>
                                <td>
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
                                    <?php if ($listing['approval_status'] === 'rejected' && !empty($listing['rejection_reason'])): ?>
                                        <div class="rejection-reason">📝
                                            <?php echo htmlspecialchars($listing['rejection_reason']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo date('d.m.Y', strtotime($listing['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="/emlakci/listing_form?id=<?php echo $listing['id']; ?>"
                                            class="btn-sm btn-edit">✏️
                                            <?php echo t('edit'); ?>
                                        </a>
                                        <a href="/emlakci/delete_listing?id=<?php echo $listing['id']; ?>"
                                            class="btn-sm btn-delete"
                                            onclick="return confirm('<?php echo $lang === 'tr' ? 'Bu ilanı silmek istediğinize emin misiniz?' : 'Are you sure?'; ?>')">
                                            🗑️
                                            <?php echo t('delete'); ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>