<?php
/**
 * Emlakçı - Profil Sayfası
 */
require_once '../config.php';
require_once '../includes/auth.php';
requireEmlakci();

$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini getir
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $firma_adi = trim($_POST['firma_adi'] ?? '');
        $vergi_no = trim($_POST['vergi_no'] ?? '');
        $lisans_no = trim($_POST['lisans_no'] ?? '');
        $ofis_adresi = trim($_POST['ofis_adresi'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $website = trim($_POST['website'] ?? '');

        // Logo yükleme
        $logo = $user['logo'];
        if (!empty($_FILES['logo']['name'])) {
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $filename = 'logo_' . $user_id . '_' . time() . '.' . $ext;
                $target = '../uploads/' . $filename;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
                    $logo = $filename;
                }
            }
        }

        $stmt = $pdo->prepare("UPDATE users SET full_name = :full_name, phone = :phone, 
                firma_adi = :firma_adi, vergi_no = :vergi_no, lisans_no = :lisans_no,
                ofis_adresi = :ofis_adresi, bio = :bio, website = :website, logo = :logo
                WHERE id = :id");
        $stmt->execute([
            ':full_name' => $full_name,
            ':phone' => $phone,
            ':firma_adi' => $firma_adi,
            ':vergi_no' => $vergi_no,
            ':lisans_no' => $lisans_no,
            ':ofis_adresi' => $ofis_adresi,
            ':bio' => $bio,
            ':website' => $website,
            ':logo' => $logo,
            ':id' => $user_id
        ]);

        $_SESSION['user_full_name'] = $full_name;
        $success = $lang === 'tr' ? 'Profil başarıyla güncellendi.' : 'Profile updated successfully.';

        // Refresh user data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch();

    } elseif ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $user['password'])) {
            $error = $lang === 'tr' ? 'Mevcut şifre hatalı.' : 'Current password is incorrect.';
        } elseif ($new !== $confirm) {
            $error = $lang === 'tr' ? 'Yeni şifreler eşleşmiyor.' : 'New passwords do not match.';
        } elseif (strlen($new) < 6) {
            $error = $lang === 'tr' ? 'Şifre en az 6 karakter olmalıdır.' : 'Password must be at least 6 characters.';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->execute([':password' => password_hash($new, PASSWORD_DEFAULT), ':id' => $user_id]);
            $success = $lang === 'tr' ? 'Şifre başarıyla değiştirildi.' : 'Password changed successfully.';
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
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1.5rem;
        }

        .profile-section {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        .profile-section-title {
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

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.9rem;
            background: #f8fafc;
            box-sizing: border-box;
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1e88e5;
            background: white;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        }

        .btn-save {
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-save:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(30, 136, 229, 0.3);
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
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

        .logo-preview {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
            margin-bottom: 0.75rem;
            border: 2px solid #e2e8f0;
        }

        .account-info {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            font-size: 0.85rem;
            color: #64748b;
        }

        .account-info span {
            background: #f1f5f9;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
        }

        @media (max-width: 640px) {
            .form-row {
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
        <h1 class="page-title">👤
            <?php echo $lang === 'tr' ? 'Profil Ayarları' : 'Profile Settings'; ?>
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

        <!-- Hesap Bilgisi -->
        <div class="profile-section">
            <div class="account-info">
                <span>👤
                    <?php echo htmlspecialchars($user['username']); ?>
                </span>
                <span>📧
                    <?php echo htmlspecialchars($user['email']); ?>
                </span>
                <span>🏢
                    <?php echo $lang === 'tr' ? 'Emlakçı Hesabı' : 'Agent Account'; ?>
                </span>
                <span>📅
                    <?php echo date('d.m.Y', strtotime($user['created_at'])); ?>
                </span>
            </div>
        </div>

        <!-- Profil Düzenleme -->
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_profile">

            <div class="profile-section">
                <div class="profile-section-title">📋
                    <?php echo $lang === 'tr' ? 'Kişisel Bilgiler' : 'Personal Information'; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Ad Soyad' : 'Full Name'; ?>
                        </label>
                        <input type="text" name="full_name"
                            value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Telefon' : 'Phone'; ?>
                        </label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <div class="profile-section-title">🏢
                    <?php echo $lang === 'tr' ? 'Firma Bilgileri' : 'Company Information'; ?>
                </div>

                <div class="form-group">
                    <label>
                        <?php echo $lang === 'tr' ? 'Firma Logo' : 'Company Logo'; ?>
                    </label>
                    <?php if (!empty($user['logo'])): ?>
                        <img src="/uploads/<?php echo htmlspecialchars($user['logo']); ?>" class="logo-preview" alt="Logo">
                    <?php endif; ?>
                    <input type="file" name="logo" accept="image/*">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Firma Adı' : 'Company Name'; ?>
                        </label>
                        <input type="text" name="firma_adi"
                            value="<?php echo htmlspecialchars($user['firma_adi'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Vergi No' : 'Tax Number'; ?>
                        </label>
                        <input type="text" name="vergi_no"
                            value="<?php echo htmlspecialchars($user['vergi_no'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Lisans No' : 'License Number'; ?>
                        </label>
                        <input type="text" name="lisans_no"
                            value="<?php echo htmlspecialchars($user['lisans_no'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Website</label>
                        <input type="url" name="website" value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>"
                            placeholder="https://...">
                    </div>
                </div>
                <div class="form-group">
                    <label>
                        <?php echo $lang === 'tr' ? 'Ofis Adresi' : 'Office Address'; ?>
                    </label>
                    <textarea name="ofis_adresi"
                        rows="2"><?php echo htmlspecialchars($user['ofis_adresi'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <?php echo $lang === 'tr' ? 'Hakkımızda' : 'About Us'; ?>
                    </label>
                    <textarea name="bio" rows="3"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn-save">💾
                <?php echo $lang === 'tr' ? 'Profili Güncelle' : 'Update Profile'; ?>
            </button>
        </form>

        <!-- Şifre Değiştirme -->
        <form method="POST" style="margin-top: 1.5rem;">
            <input type="hidden" name="action" value="change_password">
            <div class="profile-section">
                <div class="profile-section-title">🔒
                    <?php echo $lang === 'tr' ? 'Şifre Değiştir' : 'Change Password'; ?>
                </div>
                <div class="form-group">
                    <label>
                        <?php echo $lang === 'tr' ? 'Mevcut Şifre' : 'Current Password'; ?>
                    </label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Yeni Şifre' : 'New Password'; ?>
                        </label>
                        <input type="password" name="new_password" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Şifre Tekrar' : 'Confirm Password'; ?>
                        </label>
                        <input type="password" name="confirm_password" required minlength="6">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-save">🔒
                <?php echo $lang === 'tr' ? 'Şifreyi Değiştir' : 'Change Password'; ?>
            </button>
        </form>
    </div>
</body>

</html>