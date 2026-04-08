<?php
/**
 * Email Doğrulama (Aktivasyon) Sayfası
 */
require_once 'config.php';

$message = '';
$status = 'error'; // error, success

$token = $_GET['token'] ?? '';

if (empty($token)) {
    $message = 'Geçersiz aktivasyon kodu.';
} else {
    // Tokanı kontrol et
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE activation_token = :token AND email_verified = 0");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch();

    if ($user) {
        // Kullanıcıyı doğrula
        $update = $pdo->prepare("UPDATE users SET email_verified = 1, activation_token = NULL, status = 'active' WHERE id = :id");
        $update->execute([':id' => $user['id']]);

        $status = 'success';
        $message = 'Hesabınız başarıyla doğrulandı! Artık giriş yapabilirsiniz.';
    } else {
        $message = 'Aktivasyon kodu geçersiz veya hesap zaten doğrulanmış.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesap Doğrulama - Emlaxia</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .verify-card {
            background: white;
            padding: 3rem;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
        .icon.success { color: #16a34a; }
        .icon.error { color: #dc2626; }
        h1 { font-size: 1.5rem; margin-bottom: 1rem; color: #0f172a; }
        p { color: #475569; margin-bottom: 2rem; line-height: 1.6; }
        .btn {
            display: inline-block;
            background: #1e88e5;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn:hover { background: #1565c0; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="verify-card">
        <?php if ($status === 'success'): ?>
            <div class="icon success">✅</div>
        <?php else: ?>
            <div class="icon error">❌</div>
        <?php endif; ?>
        
        <h1>Hesap Doğrulama</h1>
        <p><?php echo $message; ?></p>
        
        <a href="/giris" class="btn">Giriş Yap</a>
    </div>
</body>
</html>
