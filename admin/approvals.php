<?php
/**
 * Admin - İlan Onay Sistemi
 */
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

// Onay/Red işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $listing_id = $_POST['listing_id'] ?? 0;
    $action = $_POST['action'] ?? '';
    $reason = trim($_POST['rejection_reason'] ?? '');

    if ($listing_id && in_array($action, ['approve', 'reject'])) {
        if ($action === 'approve') {
            $pdo->prepare("UPDATE listings SET approval_status = 'approved' WHERE id = :id")->execute([':id' => $listing_id]);
            $_SESSION['success_message'] = 'İlan onaylandı!';
        } else {
            $pdo->prepare("UPDATE listings SET approval_status = 'rejected', rejection_reason = :reason WHERE id = :id")
                ->execute([':reason' => $reason, ':id' => $listing_id]);
            $_SESSION['success_message'] = 'İlan reddedildi.';
        }
    }
    header('Location: approvals.php');
    exit;
}

// Bekleyen ilanları getir
$filter = $_GET['filter'] ?? 'pending';
$where = '';
if ($filter === 'pending')
    $where = "AND l.approval_status = 'pending'";
elseif ($filter === 'approved')
    $where = "AND l.approval_status = 'approved' AND l.user_type != 'admin'";
elseif ($filter === 'rejected')
    $where = "AND l.approval_status = 'rejected'";

$listings = [];
$pending_count = 0;
$approved_count = 0;
$rejected_count = 0;
$error_message = '';

try {
    // Check if required columns exist
    $has_approval = false;
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM listings LIKE 'approval_status'");
        $has_approval = (bool) $chk->fetch();
    } catch (Exception $e) {
    }

    if ($has_approval) {
        // Check if users table exists
        $has_users = false;
        try {
            $pdo->query("SELECT 1 FROM users LIMIT 1");
            $has_users = true;
        } catch (Exception $e) {
        }

        if ($has_users) {
            $stmt = $pdo->query("SELECT l.*, u.full_name as user_full_name, u.username as user_username, u.user_type as u_user_type, u.firma_adi
                                  FROM listings l 
                                  LEFT JOIN users u ON l.user_id = u.id 
                                  WHERE l.user_type IN ('emlakci', 'bireysel') $where
                                  ORDER BY l.created_at DESC");
        } else {
            $stmt = $pdo->query("SELECT l.*, NULL as user_full_name, NULL as user_username, NULL as u_user_type, NULL as firma_adi
                                  FROM listings l 
                                  WHERE l.user_type IN ('emlakci', 'bireysel') $where
                                  ORDER BY l.created_at DESC");
        }
        $listings = $stmt->fetchAll();

        $pending_count = $pdo->query("SELECT COUNT(*) FROM listings WHERE user_type IN ('emlakci','bireysel') AND approval_status = 'pending'")->fetchColumn();
        $approved_count = $pdo->query("SELECT COUNT(*) FROM listings WHERE user_type IN ('emlakci','bireysel') AND approval_status = 'approved'")->fetchColumn();
        $rejected_count = $pdo->query("SELECT COUNT(*) FROM listings WHERE user_type IN ('emlakci','bireysel') AND approval_status = 'rejected'")->fetchColumn();
    } else {
        $error_message = 'approval_status kolonu bulunamadı. Lütfen <a href="migrate_users.php">migration</a> çalıştırın.';
    }
} catch (PDOException $e) {
    $error_message = 'Veritabanı hatası: ' . $e->getMessage() . '<br>Lütfen <a href="migrate_users.php">migration</a> çalıştırın.';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $lang === 'tr' ? 'İlan Onayları' : 'Listing Approvals'; ?> - Admin Panel
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

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1.5rem;
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

        .filter-count {
            background: rgba(0, 0, 0, 0.1);
            padding: 0.1rem 0.4rem;
            border-radius: 10px;
            font-size: 0.75rem;
            margin-left: 0.25rem;
        }

        .filter-tab.active .filter-count {
            background: rgba(255, 255, 255, 0.3);
        }

        .approval-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            display: grid;
            grid-template-columns: 80px 1fr auto;
            gap: 1.5rem;
            align-items: start;
        }

        .approval-thumb {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
            background: #f1f5f9;
        }

        .approval-info {
            min-width: 0;
        }

        .approval-title {
            font-weight: 600;
            font-size: 1.05rem;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .approval-meta {
            font-size: 0.85rem;
            color: #64748b;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 0.5rem;
        }

        .approval-price {
            font-weight: 700;
            color: #0f172a;
            font-size: 1.1rem;
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
        }

        .user-badge.emlakci {
            background: #dcfce7;
            color: #16a34a;
        }

        .user-badge.bireysel {
            background: #dbeafe;
            color: #2563eb;
        }

        .approval-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            min-width: 150px;
        }

        .btn-approve {
            padding: 0.6rem 1rem;
            background: #22c55e;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .btn-approve:hover {
            background: #16a34a;
        }

        .btn-reject-show {
            padding: 0.6rem 1rem;
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .reject-form {
            display: none;
            margin-top: 0.5rem;
        }

        .reject-form.visible {
            display: block;
        }

        .reject-form textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #fecaca;
            border-radius: 8px;
            font-size: 0.85rem;
            box-sizing: border-box;
            resize: vertical;
        }

        .btn-reject {
            padding: 0.5rem 1rem;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.8rem;
            margin-top: 0.5rem;
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
        }

        @media (max-width: 768px) {
            .approval-card {
                grid-template-columns: 1fr;
            }

            .page-container {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/admin_header.php'; ?>

    <div class="page-container">
        <?php if (!empty($error_message)): ?>
            <div style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:1rem 1.5rem;border-radius:12px;margin-bottom:1.5rem;font-weight:500;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <h1 class="page-title">📋
            <?php echo $lang === 'tr' ? 'İlan Onayları' : 'Listing Approvals'; ?>
        </h1>

        <div class="filter-tabs">
            <a href="approvals.php?filter=pending"
                class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                ⏳
                <?php echo $lang === 'tr' ? 'Bekleyen' : 'Pending'; ?> <span class="filter-count">
                    <?php echo $pending_count; ?>
                </span>
            </a>
            <a href="approvals.php?filter=approved"
                class="filter-tab <?php echo $filter === 'approved' ? 'active' : ''; ?>">
                ✅
                <?php echo $lang === 'tr' ? 'Onaylanan' : 'Approved'; ?> <span class="filter-count">
                    <?php echo $approved_count; ?>
                </span>
            </a>
            <a href="approvals.php?filter=rejected"
                class="filter-tab <?php echo $filter === 'rejected' ? 'active' : ''; ?>">
                ❌
                <?php echo $lang === 'tr' ? 'Reddedilen' : 'Rejected'; ?> <span class="filter-count">
                    <?php echo $rejected_count; ?>
                </span>
            </a>
        </div>

        <?php if (empty($listings)): ?>
            <div class="empty-state">
                <div style="font-size:3rem;margin-bottom:1rem;">✨</div>
                <p style="font-size:1.1rem;font-weight:500;">
                    <?php echo $lang === 'tr' ? 'Bu kategoride ilan yok.' : 'No listings in this category.'; ?>
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($listings as $l): ?>
                <div class="approval-card">
                    <?php if (!empty($l['image1'])): ?>
                        <img src="/uploads/<?php echo htmlspecialchars($l['image1']); ?>" class="approval-thumb" alt="">
                    <?php else: ?>
                        <div class="approval-thumb" style="display:flex;align-items:center;justify-content:center;font-size:2rem;">
                            🏠</div>
                    <?php endif; ?>

                    <div class="approval-info">
                        <div class="approval-title">
                            <?php echo htmlspecialchars($l['title_tr']); ?>
                        </div>
                        <div class="approval-meta">
                            <span class="user-badge <?php echo $l['user_type']; ?>">
                                <?php echo $l['user_type'] === 'emlakci' ? '🏢 Emlakçı' : '👤 Bireysel'; ?>
                            </span>
                            <span>👤
                                <?php echo htmlspecialchars($l['user_full_name'] ?? $l['user_username'] ?? 'Bilinmeyen'); ?>
                            </span>
                            <?php if (!empty($l['firma_adi'])): ?><span>🏢
                                    <?php echo htmlspecialchars($l['firma_adi']); ?>
                                </span>
                            <?php endif; ?>
                            <span>📍
                                <?php echo htmlspecialchars($l['city'] . ' / ' . $l['district']); ?>
                            </span>
                            <span>📅
                                <?php echo date('d.m.Y', strtotime($l['created_at'])); ?>
                            </span>
                        </div>
                        <div class="approval-price">
                            <?php echo number_format($l['price'], 0, ',', '.'); ?> TL
                        </div>
                        <?php if (!empty($l['description_tr'])): ?>
                            <p style="font-size:0.85rem;color:#475569;margin-top:0.5rem;line-height:1.5;">
                                <?php echo htmlspecialchars(mb_substr($l['description_tr'], 0, 200)); ?>...
                            </p>
                        <?php endif; ?>
                        <?php if ($l['approval_status'] === 'rejected' && !empty($l['rejection_reason'])): ?>
                            <div
                                style="margin-top:0.5rem;padding:0.5rem;background:#fef2f2;border-radius:8px;font-size:0.85rem;color:#dc2626;">
                                📝 Red Sebebi:
                                <?php echo htmlspecialchars($l['rejection_reason']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="approval-actions">
                        <span class="status-badge status-<?php echo $l['approval_status']; ?>">
                            <?php echo ['pending' => '⏳ Bekliyor', 'approved' => '✅ Onaylı', 'rejected' => '❌ Reddedildi'][$l['approval_status']] ?? ''; ?>
                        </span>

                        <a href="/admin/listing_form.php?id=<?php echo $l['id']; ?>"
                            style="font-size:0.8rem;color:#1e88e5;text-decoration:none;" target="_blank">👁️ İlanı Gör</a>

                        <?php if ($l['approval_status'] === 'pending'): ?>
                            <form method="POST">
                                <input type="hidden" name="listing_id" value="<?php echo $l['id']; ?>">
                                <button type="submit" name="action" value="approve" class="btn-approve">✅ Onayla</button>
                            </form>
                            <button class="btn-reject-show" onclick="this.nextElementSibling.classList.toggle('visible')">❌
                                Reddet</button>
                            <div class="reject-form">
                                <form method="POST">
                                    <input type="hidden" name="listing_id" value="<?php echo $l['id']; ?>">
                                    <textarea name="rejection_reason" rows="2"
                                        placeholder="Red sebebi yazın..."><?php echo ''; ?></textarea>
                                    <button type="submit" name="action" value="reject" class="btn-reject">Reddet</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>