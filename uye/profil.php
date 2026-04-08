<?php
/**
 * Üye Profil Yönetimi
 */
require_once '../config.php';
require_once '../includes/auth.php';

requireUser();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    try {
        if (!empty($new_password)) {
            if (strlen($new_password) < 6) {
                throw new Exception($lang === 'tr' ? 'Yeni şifre en az 6 karakter olmalıdır.' : 'New password must be at least 6 characters.');
            }
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET full_name = :full_name, phone = :phone, password = :password WHERE id = :id");
            $stmt->execute([':full_name' => $full_name, ':phone' => $phone, ':password' => $hashed, ':id' => $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name = :full_name, phone = :phone WHERE id = :id");
            $stmt->execute([':full_name' => $full_name, ':phone' => $phone, ':id' => $user_id]);
        }
        
        $_SESSION['user_full_name'] = $full_name;
        $success = $lang === 'tr' ? 'Profiliniz başarıyla güncellendi.' : 'Profile updated successfully.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Güncel kullanıcı bilgilerini çek
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'tr' ? 'Profil Ayarları' : 'Profile Settings'; ?> - Emlaxia</title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f8fafc; margin: 0; font-family: 'Inter', sans-serif; }
        .dashboard-container { max-width: 800px; margin: 2rem auto; padding: 0 2rem; }
        .page-header { margin-bottom: 2rem; }
        .page-title { font-size: 1.75rem; font-weight: 800; color: #0f172a; }
        
        .profile-card { background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151; }
        .form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; box-sizing: border-box; }
        .form-control:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
        .alert-success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .btn-save { background: #1e88e5; color: white; border: none; padding: 0.75rem 2rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-save:hover { background: #1565c0; }
    </style>
</head>
<body>
    <?php include 'uye/includes/uye_header.php'; ?>

    <div class="dashboard-container">
        <div class="page-header">
            <h1 class="page-title"><?php echo $lang === 'tr' ? 'Profil Ayarları' : 'Profile Settings'; ?></h1>
        </div>

        <div class="profile-card">
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label><?php echo $lang === 'tr' ? 'Ad Soyad' : 'Full Name'; ?></label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label><?php echo $lang === 'tr' ? 'E-posta (Değiştirilemez)' : 'Email (Cannot be changed)'; ?></label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled style="background:#f1f5f9;">
                </div>
                <div class="form-group">
                    <label><?php echo $lang === 'tr' ? 'Telefon' : 'Phone'; ?></label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                <hr style="margin: 2rem 0; border: none; border-top: 1px solid #f1f5f9;">
                <div class="form-group">
                    <label><?php echo $lang === 'tr' ? 'Yeni Şifre (Sadece değiştirmek istiyorsanız doldurun)' : 'New Password (Leave blank to keep current)'; ?></label>
                    <input type="password" name="new_password" class="form-control" placeholder="******">
                </div>
                <button type="submit" class="btn-save">
                    <?php echo $lang === 'tr' ? 'Güncelle' : 'Update Profile'; ?>
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
