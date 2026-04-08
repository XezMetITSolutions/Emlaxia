<?php
/**
 * Bireysel Messages
 */
require_once '../config.php';
require_once '../includes/auth.php';
requireBireysel();

try {
    $user_id = $_SESSION['user_id'];

    // Mesajları getir
    // Not: Bu basit versiyon her mesajı tek tek listeler.
    $stmt = $pdo->prepare("
        SELECT m.*, 
               u_send.full_name as sender_name,
               u_recv.full_name as receiver_name,
               l.title_tr, l.title_en, l.slug, l.listing_type, l.property_type, l.city
        FROM messages m
        JOIN users u_send ON m.sender_id = u_send.id
        JOIN users u_recv ON m.receiver_id = u_recv.id
        LEFT JOIN listings l ON m.listing_id = l.id
        WHERE m.sender_id = :uid1 OR m.receiver_id = :uid2
        ORDER BY m.created_at DESC
    ");
    $stmt->execute([':uid1' => $user_id, ':uid2' => $user_id]);
    $messages = $stmt->fetchAll();

} catch (Throwable $e) {
    die("Error loading messages: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $lang === 'tr' ? 'Mesajlarım' : 'My Messages'; ?> - Emlaxia
    </title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f1f5f9; }
        .dashboard-container { max-width: 1000px; margin: 0 auto; padding: 2rem; }
        .page-title { font-size: 1.75rem; font-weight: 700; color: #0f172a; margin-bottom: 2rem; }

        .message-item {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
            display: flex;
            gap: 1.5rem;
            transition: all 0.2s;
        }

        .message-item:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

        .message-avatar {
            width: 50px; height: 50px; border-radius: 50%;
            background: var(--primary-color); color: white;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1.25rem; flex-shrink: 0;
        }

        .message-content { flex: 1; }

        .message-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem; }

        .message-user { font-weight: 700; color: #0f172a; }
        .message-date { font-size: 0.8rem; color: #94a3b8; }

        .message-listing { font-size: 0.85rem; color: #3b82f6; font-weight: 600; margin-bottom: 0.75rem; display: block; text-decoration: none; }
        .message-listing:hover { text-decoration: underline; }

        .message-text { color: #475569; line-height: 1.5; font-size: 0.95rem; background: #f8fafc; padding: 1rem; border-radius: 12px; }

        .badge-msg { font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 6px; font-weight: 700; margin-left: 0.5rem; }
        .badge-sent { background: #e0f2fe; color: #0369a1; }
        .badge-received { background: #f0fdf4; color: #16a34a; }

        .empty-state { text-align: center; padding: 4rem 2rem; background: white; border-radius: 20px; }
    </style>
</head>

<body>
    <?php include 'includes/bireysel_header.php'; ?>

    <div class="dashboard-container">
        <h1 class="page-title">✉️ <?php echo $lang === 'tr' ? 'Mesajlarım' : 'My Messages'; ?></h1>

        <?php if (empty($messages)): ?>
            <div class="empty-state">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📪</div>
                <h3><?php echo $lang === 'tr' ? 'Henüz mesajınız yok.' : 'You have no messages yet.'; ?></h3>
                <p style="color: #64748b; margin-top: 0.5rem;"><?php echo $lang === 'tr' ? 'İlan sahipleriyle iletişime geçtiğinizde mesajlarınız burada görünecek.' : 'Your messages will appear here when you contact listing owners.'; ?></p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $m): 
                $is_sent = ($m['sender_id'] == $user_id);
                $other_name = $is_sent ? $m['receiver_name'] : $m['sender_name'];
                $initial = mb_substr($other_name, 0, 1, 'UTF-8');
            ?>
                <div class="message-item">
                    <div class="message-avatar"><?php echo $initial; ?></div>
                    <div class="message-content">
                        <div class="message-header">
                            <div>
                                <span class="message-user"><?php echo htmlspecialchars($other_name); ?></span>
                                <span class="badge-msg <?php echo $is_sent ? 'badge-sent' : 'badge-received'; ?>">
                                    <?php echo $is_sent ? ($lang == 'tr' ? 'Gönderildi' : 'Sent') : ($lang == 'tr' ? 'Alındı' : 'Received'); ?>
                                </span>
                            </div>
                            <span class="message-date"><?php echo date('d.m.Y H:i', strtotime($m['created_at'])); ?></span>
                        </div>

                        <?php if ($m['listing_id']): ?>
                            <a href="/detay/<?php echo $m['listing_type'] . '/' . $m['property_type'] . '/' . $m['city'] . '/' . $m['slug']; ?>" class="message-listing">
                                🏠 <?php echo htmlspecialchars($lang == 'tr' ? $m['title_tr'] : $m['title_en']); ?>
                            </a>
                        <?php endif; ?>

                        <div class="message-text">
                            <?php echo nl2br(htmlspecialchars($m['message'])); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="/assets/js/main.js"></script>
</body>

</html>
