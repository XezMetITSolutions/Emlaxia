<?php
require_once '../config.php';

// Admin kontrolü
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

// İstatistikler
$stmt = $pdo->query("SELECT COUNT(*) as total FROM listings");
$total_listings = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM listings WHERE status = 'active'");
$active_listings = $stmt->fetch()['total'];

if (($_SESSION['admin_role'] ?? 'admin') === 'admin'):

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM offers WHERE status = 'pending'");
    $pending_offers = $stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM offers");
    $total_offers = $stmt->fetch()['total'];

    // Bekleyen kullanıcılar (onay bekleyenler)
    $stmt = $pdo->query("SELECT * FROM users WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5");
    $pending_users = $stmt->fetchAll();
endif;
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('dashboard'); ?> - Admin Panel</title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <?php include 'includes/admin_header.php'; ?>

    <main class="admin-main">
        <div class="container container-dashboard">
            <div class="dashboard-header">
                <div class="welcome-text">
                    <h1><?php echo t('dashboard'); ?></h1>
                    <p><?php echo $lang == 'tr' ? 'Hoş geldiniz, site istatistiklerini buradan takip edebilirsiniz.' : 'Welcome, you can track site statistics here.'; ?></p>
                </div>
                <div class="header-actions">
                    <a href="admin/listing_form.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> <?php echo t('new_listing'); ?>
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="admin-stats-modern">
                <div class="stat-card-modern stat-listings">
                    <div class="stat-icon"><i class="fas fa-home"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $total_listings; ?></h3>
                        <p><?php echo $lang == 'tr' ? 'Toplam İlan' : 'Total Listings'; ?></p>
                    </div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>

                <div class="stat-card-modern stat-active">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $active_listings; ?></h3>
                        <p><?php echo $lang == 'tr' ? 'Aktif İlan' : 'Active Listings'; ?></p>
                    </div>
                    <div class="stat-trend trend-neutral">
                        <i class="fas fa-check"></i>
                    </div>
                </div>

                <?php if (($_SESSION['admin_role'] ?? 'admin') === 'admin'): ?>
                    <div class="stat-card-modern stat-pending">
                        <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        <div class="stat-info">
                            <h3><?php echo $pending_offers; ?></h3>
                            <p><?php echo $lang == 'tr' ? 'Bekleyen Teklif' : 'Pending Offers'; ?></p>
                        </div>
                        <div class="stat-trend trend-warning">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </div>

                    <div class="stat-card-modern stat-total-offers">
                        <div class="stat-icon"><i class="fas fa-hand-holding-usd"></i></div>
                        <div class="stat-info">
                            <h3><?php echo $total_offers; ?></h3>
                            <p><?php echo $lang == 'tr' ? 'Toplam Teklif' : 'Total Offers'; ?></p>
                        </div>
                        <div class="stat-trend trend-info">
                            <i class="fas fa-history"></i>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions & Recent Content -->
            <div class="dashboard-grid">
                <div class="dashboard-card-section">
                    <div class="section-title">
                        <h2><?php echo $lang == 'tr' ? 'Hızlı Erişim' : 'Quick Access'; ?></h2>
                    </div>
                    <div class="quick-access-grid">
                        <a href="admin/ilanlar" class="qa-item">
                            <div class="qa-icon"><i class="fas fa-list"></i></div>
                            <span><?php echo t('manage_listings'); ?></span>
                        </a>
                        <a href="admin/approvals" class="qa-item">
                            <div class="qa-icon"><i class="fas fa-clipboard-check"></i></div>
                            <span><?php echo $lang == 'tr' ? 'İlan Onayları' : 'Listing Approvals'; ?></span>
                        </a>
                        <?php if (($_SESSION['admin_role'] ?? 'admin') === 'admin'): ?>
                            <a href="admin/offers" class="qa-item">
                                <div class="qa-icon"><i class="fas fa-envelope-open-text"></i></div>
                                <span><?php echo t('manage_offers'); ?></span>
                            </a>
                            <a href="admin/manage_users" class="qa-item">
                                <div class="qa-icon"><i class="fas fa-users-cog"></i></div>
                                <span><?php echo $lang == 'tr' ? 'Kullanıcı Yönetimi' : 'User Management'; ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dashboard-card-section">
                    <div class="section-title" style="display: flex; justify-content: space-between; align-items: center;">
                        <h2><?php echo $lang == 'tr' ? 'Onay Bekleyen Kullanıcılar' : 'Pending Users'; ?></h2>
                        <a href="admin/manage_users" style="font-size: 0.8rem; color: var(--accent); font-weight: 700; text-decoration: none;">Tümü →</a>
                    </div>
                    <div class="pending-users-list">
                        <?php if (empty($pending_users)): ?>
                            <p style="color: #64748b; font-size: 0.9rem; font-style: italic; text-align: center; padding: 1rem;">
                                <?php echo $lang == 'tr' ? 'Bekleyen kullanıcı yok.' : 'No pending users.'; ?>
                            </p>
                        <?php else: ?>
                            <?php foreach ($pending_users as $user): ?>
                                <div class="pending-user-item">
                                    <div class="user-avatar-mini" style="background: <?php echo $user['user_type'] == 'emlakci' ? '#eff6ff' : '#f0fdf4'; ?>; color: <?php echo $user['user_type'] == 'emlakci' ? '#1d4ed8' : '#15803d'; ?>;">
                                        <?php echo mb_substr($user['full_name'] ?: $user['username'], 0, 1); ?>
                                    </div>
                                    <div class="user-info-mini">
                                        <span class="user-name-mini"><?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?></span>
                                        <span class="user-type-badge-mini <?php echo $user['user_type']; ?>">
                                            <?php echo $user['user_type'] == 'emlakci' ? 'Emlakçı' : 'Bireysel'; ?>
                                        </span>
                                    </div>
                                    <div class="user-action-mini">
                                        <a href="admin/user_form.php?id=<?php echo $user['id']; ?>" class="btn-mini-view"><i class="fas fa-edit"></i></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        :root {
            --primary: #0F123D;
            --accent: #D3AF37;
            --bg: #f8fafc;
        }

        body {
            background-color: var(--bg);
        }

        .container-dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .welcome-text h1 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .welcome-text p {
            color: #64748b;
            font-size: 1rem;
        }

        .admin-stats-modern {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card-modern {
            background: white;
            padding: 1.5rem;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            border: 1px solid #e2e8f0;
            transition: transform 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card-modern:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1.25rem;
        }

        .stat-listings .stat-icon { background: #eff6ff; color: #2563eb; }
        .stat-active .stat-icon { background: #f0fdf4; color: #16a34a; }
        .stat-pending .stat-icon { background: #fffbeb; color: #d97706; }
        .stat-total-offers .stat-icon { background: #fdf2f8; color: #db2777; }

        .stat-info h3 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
            line-height: 1;
        }

        .stat-info p {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 0.25rem;
        }

        .stat-trend {
            margin-left: auto;
            font-size: 1.2rem;
            opacity: 0.2;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .dashboard-card-section {
            background: white;
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
            border: 1px solid #e2e8f0;
        }

        .section-title {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .section-title h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
        }

        .quick-access-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .qa-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem;
            background: #f8fafc;
            border-radius: 1rem;
            text-decoration: none;
            transition: all 0.2s;
            text-align: center;
            border: 1px solid transparent;
        }

        .qa-item:hover {
            background: #fff;
            border-color: var(--accent);
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        .qa-icon {
            font-size: 1.75rem;
            color: var(--primary);
            margin-bottom: 0.75rem;
        }

        .qa-item span {
            font-weight: 600;
            color: #475569;
            font-size: 0.9rem;
        }

        .pending-users-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .pending-user-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 12px;
            transition: all 0.2s;
        }

        .pending-user-item:hover {
            transform: translateX(5px);
            background: #fff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .user-avatar-mini {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .user-info-mini {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .user-name-mini {
            font-size: 0.9rem;
            font-weight: 700;
            color: #1e293b;
        }

        .user-type-badge-mini {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            width: fit-content;
        }

        .user-type-badge-mini.emlakci {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .user-type-badge-mini.bireysel {
            background: #dcfce7;
            color: #15803d;
        }

        .btn-mini-view {
            color: #d3af37;
            padding: 5px;
            transition: all 0.2s;
        }

        .btn-mini-view:hover {
            color: #0f123d;
        }

        @media (max-width: 992px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <?php include '../includes/footer.php'; ?>
    <script src="/assets/js/main.js"></script>
</body>

</html>