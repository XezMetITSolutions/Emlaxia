<?php
require_once '../config.php';
require_once '../includes/auth.php';
requireBireysel();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $offer_id = $_POST['offer_id'] ?? 0;
    $action = $_POST['action'];
    if (in_array($action, ['accepted', 'rejected']) && $offer_id) {
        $stmt = $pdo->prepare("SELECT o.id FROM offers o JOIN listings l ON o.listing_id = l.id WHERE o.id = :oid AND l.user_id = :uid");
        $stmt->execute([':oid' => $offer_id, ':uid' => $user_id]);
        if ($stmt->fetch()) {
            $pdo->prepare("UPDATE offers SET status = :s WHERE id = :id")->execute([':s' => $action, ':id' => $offer_id]);
            $_SESSION['success_message'] = $lang === 'tr' ? 'Teklif durumu güncellendi.' : 'Offer updated.';
        }
    }
    header('Location: /bireysel/teklifler');
    exit;
}

$stmt = $pdo->prepare("SELECT o.*, l.title_tr, l.title_en, l.price as listing_price, l.image1
                        FROM offers o JOIN listings l ON o.listing_id = l.id 
                        WHERE l.user_id = :uid AND l.user_type = 'bireysel' ORDER BY o.created_at DESC");
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1.5rem;
        }

        .offer-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            display: flex;
            gap: 1.25rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .offer-thumb {
            width: 65px;
            height: 65px;
            border-radius: 10px;
            object-fit: cover;
            background: #f1f5f9;
            flex-shrink: 0;
        }

        .offer-info {
            flex: 1;
            min-width: 200px;
        }

        .offer-title {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.25rem;
        }

        .offer-customer {
            font-size: 0.85rem;
            color: #64748b;
        }

        .offer-amounts {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .offer-original {
            color: #374151;
        }

        .offer-offered {
            color: #16a34a;
            font-weight: 700;
            font-size: 1rem;
        }

        .status-badge {
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

        .offer-actions form {
            display: flex;
            gap: 0.5rem;
        }

        .btn-accept {
            padding: 0.4rem 0.8rem;
            background: #dcfce7;
            color: #16a34a;
            border: none;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-reject {
            padding: 0.4rem 0.8rem;
            background: #fef2f2;
            color: #dc2626;
            border: none;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
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
            .page-container {
                padding: 1rem;
            }

            .offer-card {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/bireysel_header.php'; ?>
    <div class="page-container">
        <h1 class="page-title">💬
            <?php echo $lang === 'tr' ? 'Gelen Teklifler' : 'Offers'; ?> (
            <?php echo count($offers); ?>)
        </h1>
        <?php if (empty($offers)): ?>
            <div class="empty-state">
                <div style="font-size:3rem;margin-bottom:1rem;">💬</div>
                <p>
                    <?php echo $lang === 'tr' ? 'Henüz teklif almadınız.' : 'No offers yet.'; ?>
                </p>
            </div>
        <?php else:
            foreach ($offers as $o): ?>
                <div class="offer-card">
                    <?php if (!empty($o['image1'])): ?><img src="/uploads/<?php echo htmlspecialchars($o['image1']); ?>"
                            class="offer-thumb" alt="">
                    <?php else: ?>
                        <div class="offer-thumb" style="display:flex;align-items:center;justify-content:center;font-size:1.5rem;">🏠
                        </div>
                    <?php endif; ?>
                    <div class="offer-info">
                        <div class="offer-title">
                            <?php echo htmlspecialchars($lang === 'tr' ? $o['title_tr'] : $o['title_en']); ?>
                        </div>
                        <div class="offer-customer">👤
                            <?php echo htmlspecialchars($o['name']); ?> | 📧
                            <?php echo htmlspecialchars($o['email']); ?>
                        </div>
                        <div class="offer-amounts">
                            <span class="offer-original">
                                <?php echo number_format($o['listing_price'], 0, ',', '.'); ?> TL
                            </span>
                            <span class="offer-offered">→
                                <?php echo number_format($o['amount'], 0, ',', '.'); ?> TL
                            </span>
                            <span class="status-badge status-<?php echo $o['status']; ?>">
                                <?php echo ['pending' => 'Bekliyor', 'accepted' => 'Kabul', 'rejected' => 'Red'][$o['status']] ?? $o['status']; ?>
                            </span>
                        </div>
                    </div>
                    <?php if ($o['status'] === 'pending'): ?>
                        <div class="offer-actions">
                            <form method="POST"><input type="hidden" name="offer_id" value="<?php echo $o['id']; ?>">
                                <button name="action" value="accepted" class="btn-accept">✅ Kabul</button>
                                <button name="action" value="rejected" class="btn-reject">❌ Red</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; endif; ?>
    </div>
</body>

</html>