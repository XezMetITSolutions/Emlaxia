<?php
/**
 * Bireysel - Profil
 */
require_once '../config.php';
require_once '../includes/auth.php';
requireBireysel();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_profile') {
        $stmt = $pdo->prepare("UPDATE users SET full_name=:fn, phone=:ph WHERE id=:id");
        $stmt->execute([':fn' => trim($_POST['full_name'] ?? ''), ':ph' => trim($_POST['phone'] ?? ''), ':id' => $user_id]);
        $_SESSION['user_full_name'] = trim($_POST['full_name'] ?? '');
        $success = $lang === 'tr' ? 'Profil güncellendi.' : 'Profile updated.';
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch();
    } elseif ($action === 'change_password') {
        $cur = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $conf = $_POST['confirm_password'] ?? '';
        if (!password_verify($cur, $user['password']))
            $error = $lang === 'tr' ? 'Mevcut şifre hatalı.' : 'Wrong password.';
        elseif ($new !== $conf)
            $error = $lang === 'tr' ? 'Şifreler eşleşmiyor.' : 'Passwords mismatch.';
        elseif (strlen($new) < 6)
            $error = $lang === 'tr' ? 'Şifre en az 6 karakter.' : 'Min 6 chars.';
        else {
            $pdo->prepare("UPDATE users SET password=:p WHERE id=:id")->execute([':p' => password_hash($new, PASSWORD_DEFAULT), ':id' => $user_id]);
            $success = $lang === 'tr' ? 'Şifre değiştirildi.' : 'Password changed.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $lang === 'tr' ? 'Profil' : 'Profile'; ?> - Emlaxia
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
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1.5rem;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            color: #374151;
            margin-bottom: 0.4rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.9rem;
            background: #f8fafc;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
        }

        .btn-save {
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .info {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            font-size: 0.85rem;
            color: #64748b;
        }

        .info span {
            background: #f1f5f9;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
        }

        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/bireysel_header.php'; ?>
    <div class="page-container">
        <h1 class="page-title">⚙️
            <?php echo $lang === 'tr' ? 'Profil Ayarları' : 'Profile'; ?>
        </h1>
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="info">
                <span>👤
                    <?php echo htmlspecialchars($user['username']); ?>
                </span>
                <span>📧
                    <?php echo htmlspecialchars($user['email']); ?>
                </span>
                <span>🔵 Bireysel</span>
            </div>
        </div>

        <form method="POST">
            <input type="hidden" name="action" value="update_profile">
            <div class="card">
                <div class="card-title">📋
                    <?php echo $lang === 'tr' ? 'Kişisel Bilgiler' : 'Personal Info'; ?>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>
                            <?php echo $lang === 'tr' ? 'Ad Soyad' : 'Full Name'; ?>
                        </label><input type="text" name="full_name"
                            value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>"></div>
                    <div class="form-group"><label>
                            <?php echo $lang === 'tr' ? 'Telefon' : 'Phone'; ?>
                        </label><input type="tel" name="phone"
                            value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"></div>
                </div>
            </div>
            <button type="submit" class="btn-save">💾
                <?php echo $lang === 'tr' ? 'Kaydet' : 'Save'; ?>
            </button>
        </form>

        <form method="POST" style="margin-top:1.5rem;">
            <input type="hidden" name="action" value="change_password">
            <div class="card">
                <div class="card-title">🔒
                    <?php echo $lang === 'tr' ? 'Şifre Değiştir' : 'Change Password'; ?>
                </div>
                <div class="form-group"><label>
                        <?php echo $lang === 'tr' ? 'Mevcut Şifre' : 'Current'; ?>
                    </label><input type="password" name="current_password" required></div>
                <div class="form-row">
                    <div class="form-group"><label>
                            <?php echo $lang === 'tr' ? 'Yeni Şifre' : 'New'; ?>
                        </label><input type="password" name="new_password" required minlength="6"></div>
                    <div class="form-group"><label>
                            <?php echo $lang === 'tr' ? 'Tekrar' : 'Confirm'; ?>
                        </label><input type="password" name="confirm_password" required minlength="6"></div>
                </div>
            </div>
            <button type="submit" class="btn-save">🔒
                <?php echo $lang === 'tr' ? 'Değiştir' : 'Change'; ?>
            </button>
        </form>
    </div>
</body>

</html>