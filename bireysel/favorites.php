<?php
/**
 * Bireysel Favorites
 */
require_once '../config.php';
require_once '../includes/auth.php';
requireBireysel();

try {
    $user_id = $_SESSION['user_id'];

    // Favorileri getir
    $stmt = $pdo->prepare("
        SELECT f.id as fav_id, l.* 
        FROM favorites f 
        JOIN listings l ON f.listing_id = l.id 
        WHERE f.user_id = :uid 
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([':uid' => $user_id]);
    $favorites = $stmt->fetchAll();

} catch (Throwable $e) {
    die("Error loading favorites: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $lang === 'tr' ? 'Favorilerim' : 'My Favorites'; ?> - Emlaxia
    </title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f1f5f9; }
        .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .page-title { font-size: 1.75rem; font-weight: 700; color: #0f172a; margin-bottom: 2rem; }
        
        .favorites-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .fav-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
            position: relative;
        }

        .fav-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); }

        .fav-image { width: 100%; height: 200px; object-fit: cover; }

        .fav-content { padding: 1.25rem; }

        .fav-title { font-weight: 700; font-size: 1.1rem; color: #0f172a; margin-bottom: 0.5rem; display: block; text-decoration: none; }
        .fav-price { font-size: 1.25rem; font-weight: 800; color: var(--primary-color); margin-bottom: 0.5rem; }
        .fav-meta { font-size: 0.85rem; color: #64748b; margin-bottom: 1rem; }

        .btn-remove-fav {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ef4444;
            cursor: pointer;
            transition: all 0.2s;
            z-index: 5;
        }

        .btn-remove-fav:hover { background: #ef4444; color: white; transform: scale(1.1); }

        .price-change-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        .price-down { background: #dcfce7; color: #16a34a; }
        .price-up { background: #fef2f2; color: #dc2626; }

        .empty-state { text-align: center; padding: 4rem 2rem; background: white; border-radius: 20px; border: 2px dashed #e2e8f0; }
    </style>
</head>

<body>
    <?php include 'includes/bireysel_header.php'; ?>

    <div class="dashboard-container">
        <h1 class="page-title">❤️ <?php echo $lang === 'tr' ? 'Favorilerim' : 'My Favorites'; ?></h1>

        <?php if (empty($favorites)): ?>
            <div class="empty-state">
                <div style="font-size: 4rem; margin-bottom: 1rem;">💔</div>
                <h3><?php echo $lang === 'tr' ? 'Henüz favori ilanınız yok.' : 'You have no favorite listings yet.'; ?></h3>
                <p style="color: #64748b; margin-top: 0.5rem;"><?php echo $lang === 'tr' ? 'Keşfetmeye başlayın ve beğendiğiniz ilanları buraya ekleyin.' : 'Start exploring and add the listings you like here.'; ?></p>
                <a href="/ilanlar" class="btn btn-primary" style="margin-top: 1.5rem;"><?php echo $lang === 'tr' ? 'İlanları İncele' : 'Browse Listings'; ?></a>
            </div>
        <?php else: ?>
            <div class="favorites-grid">
                <?php foreach ($favorites as $f): ?>
                    <div class="fav-card" id="fav-<?php echo $f['id']; ?>">
                        <button class="btn-remove-fav" onclick="toggleFav(<?php echo $f['id']; ?>)" title="<?php echo $lang === 'tr' ? 'Favorilerden Kaldır' : 'Remove from Favorites'; ?>">
                            <i class="fas fa-heart"></i>
                        </button>
                        
                        <?php if (!empty($f['image1'])): ?>
                            <img src="/uploads/<?php echo htmlspecialchars($f['image1']); ?>" class="fav-image" alt="">
                        <?php else: ?>
                            <div class="fav-image" style="background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 3rem;">🏠</div>
                        <?php endif; ?>

                        <div class="fav-content">
                            <a href="/detay/<?php echo $f['listing_type'] . '/' . $f['property_type'] . '/' . $f['city'] . '/' . $f['slug']; ?>" class="fav-title">
                                <?php echo htmlspecialchars($lang === 'tr' ? $f['title_tr'] : $f['title_en']); ?>
                            </a>
                            <div class="fav-price">
                                <?php echo number_format($f['price'], 0, ',', '.') . ' TL'; ?>
                            </div>
                            
                            <?php 
                            // Fiyat değişimi kontrolü
                            $stmt_hist = $pdo->prepare("SELECT * FROM listing_price_history WHERE listing_id = :lid ORDER BY changed_at DESC LIMIT 1");
                            $stmt_hist->execute([':lid' => $f['id']]);
                            $history = $stmt_hist->fetch();
                            
                            if ($history):
                                $diff = $f['price'] - $history['old_price'];
                                if ($diff != 0):
                            ?>
                                <div class="price-change-badge <?php echo $diff < 0 ? 'price-down' : 'price-up'; ?>">
                                    <?php if ($diff < 0): ?>
                                        📉 <?php echo $lang == 'tr' ? 'Fiyat Düştü!' : 'Price Dropped!'; ?> 
                                        (<?php echo number_format($history['old_price'], 0, ',', '.') . ' TL'; ?> →)
                                    <?php else: ?>
                                        📈 <?php echo $lang == 'tr' ? 'Fiyat Arttı' : 'Price Increased'; ?>
                                    <?php endif; ?>
                                </div>
                            <?php 
                                endif;
                            endif; 
                            ?>

                            <div class="fav-meta" style="margin-top: 1rem;">
                                📍 <?php echo htmlspecialchars($f['city'] . ' / ' . $f['district']); ?>
                            </div>
                            
                            <a href="/detay/<?php echo $f['listing_type'] . '/' . $f['property_type'] . '/' . $f['city'] . '/' . $f['slug']; ?>" class="btn btn-secondary btn-small btn-block">
                                <?php echo $lang === 'tr' ? 'Detayları Gör' : 'View Details'; ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="/assets/js/main.js"></script>
    <script>
        function toggleFav(id) {
            if (!confirm('<?php echo $lang == 'tr' ? 'Bu ilanı favorilerden kaldırmak istediğinize emin misiniz?' : 'Are you sure you want to remove this from favorites?'; ?>')) return;
            
            fetch('/api/toggle_favorite.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ listing_id: id })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById('fav-' + id);
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        card.remove();
                        if (document.querySelectorAll('.fav-card').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
            });
        }
    </script>
</body>

</html>
