<?php
/**
 * Emlakçı - Teklifler Sayfası
 */
require_once '../config.php';
require_once '../includes/auth.php';
requireEmlakci();

$user_id = $_SESSION['user_id'];

// Teklif durumu güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $offer_id = $_POST['offer_id'] ?? 0;
    $action = $_POST['action'];

    if (in_array($action, ['accepted', 'rejected']) && $offer_id) {
        // Teklif kendi ilanına ait mi kontrol et
        $stmt = $pdo->prepare("SELECT o.id FROM offers o JOIN listings l ON o.listing_id = l.id WHERE o.id = :oid AND l.user_id = :uid");
        $stmt->execute([':oid' => $offer_id, ':uid' => $user_id]);
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("UPDATE offers SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $action, ':id' => $offer_id]);
            $_SESSION['success_message'] = $lang === 'tr' ? 'Teklif durumu güncellendi.' : 'Offer status updated.';
        }
    }
    header('Location: /emlakci/teklifler');
    exit;
}

// Teklifleri getir
$stmt = $pdo->prepare("SELECT o.*, l.title_tr, l.title_en, l.price as listing_price, l.image1
                        FROM offers o 
                        JOIN listings l ON o.listing_id = l.id 
                        WHERE l.user_id = :uid AND l.user_type = 'emlakci'
                        ORDER BY o.created_at DESC");
$stmt->execute([':uid' => $user_id]);
$offers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $lang === 'tr' ? 'Teklifler' : 'Offers'; ?> - Emlaxia
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

        .offers-grid {
            display: grid;
            gap: 1rem;
        }

        .offer-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 1.5rem;
            align-items: center;
        }

        .offer-listing-thumb {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            object-fit: cover;
            background: #f1f5f9;
        }

        .offer-details {
            min-width: 0;
        }

        .offer-listing-title {
            font-weight: 600;
            font-size: 1rem;
            color: #0f172a;
            margin-bottom: 0.25rem;
        }

        .offer-customer {
            color: #64748b;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .offer-customer strong {
            color: #374151;
        }

        .offer-amounts {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            font-size: 0.85rem;
            flex-wrap: wrap;
        }

        .offer-amount-label {
            color: #64748b;
        }

        .offer-amount-value {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .offer-amount-value.original {
            color: #374151;
        }

        .offer-amount-value.offered {
            color: #16a34a;
        }

        .offer-actions {
            display: flex;
            gap: 0.5rem;
            flex-direction: column;
        }

        .btn-accept,
        .btn-reject {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-accept {
            background: #dcfce7;
            color: #16a34a;
        }

        .btn-accept:hover {
            background: #bbf7d0;
        }

        .btn-reject {
            background: #fef2f2;
            color: #dc2626;
        }

        .btn-reject:hover {
            background: #fecaca;
        }

        .status-badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-accepted {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-rejected {
            background: #fef2f2;
            color: #dc2626;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #94a3b8;
        }

        .offer-date {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .offer-card {
                grid-template-columns: 1fr;
            }

            .page-container {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/emlakci_header.php'; ?>

    <div class="page-container">
        <h1 class="page-title">💬
            <?php echo $lang === 'tr' ? 'Gelen Teklifler' : 'Received Offers'; ?> (
            <?php echo count($offers); ?>)
        </h1>

        <div class="offers-grid">
            <?php if (empty($offers)): ?>
                <div class="empty-state" style="background:white; border-radius:16px; border:1px solid #e2e8f0;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">💬</div>
                    <p style="font-size: 1.1rem; font-weight: 500;">
                        <?php echo $lang === 'tr' ? 'Henüz teklif almadınız.' : 'No offers received yet.'; ?>
                    </p>
                    <p style="font-size: 0.85rem;">
                        <?php echo $lang === 'tr' ? 'İlanlarınıza teklif geldiğinde burada görünecektir.' : 'Offers for your listings will appear here.'; ?>
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($offers as $offer): ?>
                    <div class="offer-card">
                        <?php if (!empty($offer['image1'])): ?>
                            <img src="/uploads/<?php echo htmlspecialchars($offer['image1']); ?>" class="offer-listing-thumb"
                                alt="">
                        <?php else: ?>
                            <div class="offer-listing-thumb"
                                style="display:flex;align-items:center;justify-content:center;font-size:1.5rem;">🏠</div>
                        <?php endif; ?>

                        <div class="offer-details">
                            <div class="offer-listing-title">
                                <?php echo htmlspecialchars($lang === 'tr' ? $offer['title_tr'] : $offer['title_en']); ?>
                            </div>
                            <div class="offer-customer">
                                👤 <strong>
                                    <?php echo htmlspecialchars($offer['name']); ?>
                                </strong>
                                &nbsp;|&nbsp; 📧
                                <?php echo htmlspecialchars($offer['email']); ?>
                                <?php if (!empty($offer['phone'])): ?>
                                    &nbsp;|&nbsp; 📱
                                    <?php echo htmlspecialchars($offer['phone']); ?>
                                <?php endif; ?>
                            </div>
                            <div class="offer-amounts">
                                <div>
                                    <span class="offer-amount-label">
                                        <?php echo $lang === 'tr' ? 'İlan Fiyatı:' : 'Listing Price:'; ?>
                                    </span>
                                    <span class="offer-amount-value original">
                                        <?php echo number_format($offer['listing_price'], 0, ',', '.'); ?> TL
                                    </span>
                                </div>
                                <div>
                                    <span class="offer-amount-label">
                                        <?php echo $lang === 'tr' ? 'Teklif:' : 'Offer:'; ?>
                                    </span>
                                    <span class="offer-amount-value offered">
                                        <?php echo number_format($offer['amount'], 0, ',', '.'); ?> TL
                                    </span>
                                </div>
                                <span class="status-badge status-<?php echo $offer['status']; ?>">
                                    <?php
                                    $statuses = ['pending' => $lang === 'tr' ? 'Bekliyor' : 'Pending', 'accepted' => $lang === 'tr' ? 'Kabul Edildi' : 'Accepted', 'rejected' => $lang === 'tr' ? 'Reddedildi' : 'Rejected'];
                                    echo $statuses[$offer['status']] ?? $offer['status'];
                                    ?>
                                </span>
                            </div>
                            <?php if (!empty($offer['message'])): ?>
                                <div
                                    style="margin-top: 0.5rem; font-size: 0.85rem; color: #475569; background: #f8fafc; padding: 0.5rem 0.75rem; border-radius: 8px;">
                                    💬
                                    <?php echo htmlspecialchars($offer['message']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="offer-date">📅
                                <?php echo date('d.m.Y H:i', strtotime($offer['created_at'])); ?>
                            </div>
                        </div>

                        <?php if ($offer['status'] === 'pending'): ?>
                            <div class="offer-actions">
                                <form method="POST" style="display:contents;">
                                    <input type="hidden" name="offer_id" value="<?php echo $offer['id']; ?>">
                                    <button type="submit" name="action" value="accepted" class="btn-accept">✅
                                        <?php echo $lang === 'tr' ? 'Kabul Et' : 'Accept'; ?>
                                    </button>
                                    <button type="submit" name="action" value="rejected" class="btn-reject">❌
                                        <?php echo $lang === 'tr' ? 'Reddet' : 'Reject'; ?>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>