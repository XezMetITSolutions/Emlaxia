<?php
/**
 * Üyenin Mesajları
 */
require_once '../config.php';
require_once '../includes/auth.php';

requireUser();

$user_id = $_SESSION['user_id'];

try {
    // Mesajları getir (Biraz basitleştirilmiş bir sürüm)
    $stmt = $pdo->prepare("SELECT m.*, u.full_name as sender_name 
                           FROM messages m 
                           LEFT JOIN users u ON m.sender_id = u.id 
                           WHERE m.receiver_id = :uid 
                           ORDER BY m.created_at DESC");
    $stmt->execute([':uid' => $user_id]);
    $messages = $stmt->fetchAll();
} catch (Throwable $e) {
    // Hata durumunda boş döndür (Eğer mesaj tablosu yoksa veya farklıysa)
    $messages = [];
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'tr' ? 'Mesajlarım' : 'My Messages'; ?> - Emlaxia</title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f8fafc; margin: 0; font-family: 'Inter', sans-serif; }
        .dashboard-container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        .page-header { margin-bottom: 2rem; }
        .page-title { font-size: 1.75rem; font-weight: 800; color: #0f172a; }
        
        .msg-list { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .msg-item { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: all 0.2s; }
        .msg-item:hover { background: #f8fafc; }
        .msg-item.unread { border-left: 4px solid #3b82f6; }
        .msg-header { display: flex; justify-content: space-between; margin-bottom: 0.5rem; }
        .msg-sender { font-weight: 700; color: #0f172a; }
        .msg-date { font-size: 0.8rem; color: #64748b; }
        .msg-snippet { color: #475569; font-size: 0.95rem; }
    </style>
</head>
<body>
    <?php include 'includes/uye_header.php'; ?>

    <div class="dashboard-container">
        <div class="page-header">
            <h1 class="page-title"><?php echo $lang === 'tr' ? 'Mesajlarım' : 'Messages'; ?></h1>
        </div>

        <div class="msg-list">
            <?php if (empty($messages)): ?>
                <div style="padding: 4rem; text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">✉️</div>
                    <p style="color: #64748b;"><?php echo $lang === 'tr' ? 'Henüz mesajınız yok.' : 'You have no messages yet.'; ?></p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="msg-item <?php echo $msg['is_read'] == 0 ? 'unread' : ''; ?>">
                        <div class="msg-header">
                            <span class="msg-sender"><?php echo htmlspecialchars($msg['sender_name'] ?? 'Destek'); ?></span>
                            <span class="msg-date"><?php echo date('d.m.Y H:i', strtotime($msg['created_at'])); ?></span>
                        </div>
                        <div class="msg-snippet"><?php echo htmlspecialchars($msg['message']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
