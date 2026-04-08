<?php
require_once '../config.php';

// Admin kontrolü
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login');
    exit;
}

// Role check
if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') {
    die($lang == 'tr' ? 'Bu sayfaya erişim yetkiniz yok.' : 'Access Denied.');
}

// Teklif durumu güncelleme
if (isset($_GET['update_status'])) {
    $offer_id = $_GET['offer_id'] ?? 0;
    $status = $_GET['status'] ?? '';

    if ($offer_id && in_array($status, ['pending', 'accepted', 'rejected'])) {
        $stmt = $pdo->prepare("UPDATE offers SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $offer_id]);
        $_SESSION['success_message'] = $lang == 'tr' ? 'Teklif durumu güncellendi' : 'Offer status updated';
        header('Location: offers');
        exit;
    }
}

// Teklifleri getir
$stmt = $pdo->query("
    SELECT o.*, l.title_tr, l.title_en, l.price as listing_price 
    FROM offers o 
    LEFT JOIN listings l ON o.listing_id = l.id 
    ORDER BY o.created_at DESC
");
$offers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('manage_offers'); ?> - Admin Panel</title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <?php include 'includes/admin_header.php'; ?>

    <main>
        <div class="container">
            <h1><?php echo t('manage_offers'); ?></h1>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?php echo $lang == 'tr' ? 'İlan' : 'Listing'; ?></th>
                            <th><?php echo t('customer_name'); ?></th>
                            <th><?php echo t('customer_email'); ?></th>
                            <th><?php echo t('customer_phone'); ?></th>
                            <th><?php echo $lang == 'tr' ? 'İlan Fiyatı' : 'Listing Price'; ?></th>
                            <th><?php echo t('offer_amount'); ?></th>
                            <th><?php echo t('offer_status'); ?></th>
                            <th><?php echo $lang == 'tr' ? 'Tarih' : 'Date'; ?></th>
                            <th><?php echo $lang == 'tr' ? 'İşlemler' : 'Actions'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($offers)): ?>
                            <tr>
                                <td colspan="10" class="text-center">
                                    <?php echo $lang == 'tr' ? 'Teklif bulunamadı' : 'No offers found'; ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($offers as $offer): ?>
                                <tr>
                                    <td><?php echo $offer['id']; ?></td>
                                    <td>
                                        <a href="/ilan.php?id=<?php echo $offer['listing_id']; ?>" target="_blank">
                                            <?php echo htmlspecialchars($offer['title_' . $lang]); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($offer['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($offer['customer_email']); ?></td>
                                    <td><?php echo htmlspecialchars($offer['customer_phone'] ?? '-'); ?></td>
                                    <td><?php echo number_format($offer['listing_price'], 2, ',', '.'); ?>
                                        <?php echo t('currency'); ?></td>
                                    <td><strong><?php echo number_format($offer['offer_amount'], 2, ',', '.'); ?>
                                            <?php echo t('currency'); ?></strong></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $offer['status']; ?>">
                                            <?php echo t($offer['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($offer['created_at'])); ?></td>
                                    <td>
                                        <div class="offer-actions">
                                            <?php if ($offer['status'] == 'pending'): ?>
                                                <a href="/admin/offers?update_status=1&offer_id=<?php echo $offer['id']; ?>&status=accepted"
                                                    class="btn btn-small btn-success"><?php echo t('accept'); ?></a>
                                                <a href="/admin/offers?update_status=1&offer_id=<?php echo $offer['id']; ?>&status=rejected"
                                                    class="btn btn-small btn-danger"><?php echo t('reject'); ?></a>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($offer['message']): ?>
                                            <div class="offer-message" style="margin-top: 5px; font-size: 12px; color: #666;">
                                                <strong><?php echo t('message'); ?>:</strong>
                                                <?php echo htmlspecialchars(substr($offer['message'], 0, 50)); ?>...
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="/assets/js/main.js"></script>
</body>

</html>