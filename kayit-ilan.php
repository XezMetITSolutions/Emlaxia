<?php
/**
 * Kayıt Sayfası - Emlakçı ve Bireysel
 */
ob_start();
require_once 'config.php';
require_once 'includes/auth.php';

// Zaten giriş yapılmışsa yönlendir
if (checkUserAuth()) {
    $redirect = $_SESSION['user_type'] === 'emlakci' ? '/emlakci/dashboard' : '/bireysel/dashboard';
    header('Location: ' . $redirect);
    exit;
}

// Pre-select type from URL
$preselect_type = $_GET['type'] ?? '';
if (!isset($form_data['user_type']) && in_array($preselect_type, ['emlakci', 'bireysel', 'uye'])) {
    $form_data['user_type'] = $preselect_type;
}


$error = '';
$success = '';
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data = $_POST;

    // Şifre eşleşme kontrolü
    if (($form_data['password'] ?? '') !== ($form_data['password_confirm'] ?? '')) {
        $error = $lang === 'tr' ? 'Şifreler eşleşmiyor.' : 'Passwords do not match.';
    } else {
        $result = registerUser($pdo, $form_data);
        if ($result['success']) {
            $success = $result['message'];
            $form_data = []; // Formu temizle
        } else {
            $error = $result['message'];
        }
    }
}

$csrf_token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $lang === 'tr' ? 'Kayıt Ol' : 'Register'; ?> - Emlaxia
    </title>
    <base href="/">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .register-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-container {
            width: 100%;
            max-width: 640px;
            background: rgba(255, 255, 255, 0.97);
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #1e88e5, #43a047, #1e88e5);
        }

        .register-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo {
            max-width: 220px;
            height: auto;
            margin-bottom: 0.5rem;
        }

        .register-logo p {
            color: #64748b;
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }

        .user-type-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .type-card {
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .type-card:hover {
            border-color: #1e88e5;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 136, 229, 0.15);
        }

        .type-card.active {
            border-color: #1e88e5;
            background: linear-gradient(135deg, rgba(30, 136, 229, 0.05), rgba(30, 136, 229, 0.1));
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.2);
        }

        .type-card .type-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
        }

        .type-card .type-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #0f172a;
            margin-bottom: 0.25rem;
        }

        .type-card .type-desc {
            font-size: 0.8rem;
            color: #64748b;
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
            font-size: 0.875rem;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.2s;
            background: #f8fafc;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1e88e5;
            background: white;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        }

        .emlakci-fields {
            display: none;
            background: linear-gradient(135deg, rgba(67, 160, 71, 0.05), rgba(67, 160, 71, 0.1));
            border: 1px solid rgba(67, 160, 71, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .emlakci-fields.visible {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .emlakci-fields h3 {
            font-size: 1rem;
            font-weight: 600;
            color: #2e7d32;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-register {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 0.5rem;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #1565c0, #0d47a1);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(30, 136, 229, 0.3);
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #64748b;
        }

        .login-link a {
            color: #1e88e5;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .required-star {
            color: #dc2626;
        }

        @media (max-width: 640px) {
            .register-container {
                padding: 2rem 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .user-type-selector {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="register-page">
        <div class="register-container">
            <div class="register-logo">
                <img src="/Logo.png" alt="Emlaxia Logo" class="brand-logo">
                <p>
                    <?php echo $lang === 'tr' ? 'Yeni hesap oluşturun' : 'Create a new account'; ?>
                </p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

                <form method="POST" action="/kayit-ilan" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="user_type" id="user_type"
                    value="<?php echo htmlspecialchars($form_data['user_type'] ?? ''); ?>">

                <!-- Kullanıcı Tipi Seçimi -->
                <div class="user-type-selector">
                    <div class="type-card <?php echo ($form_data['user_type'] ?? '') === 'bireysel' ? 'active' : ''; ?>"
                        onclick="selectType('bireysel')">
                        <div class="type-icon" style="color: #3b82f6;"><i class="fas fa-user"></i></div>
                        <div class="type-title">
                            <?php echo $lang === 'tr' ? 'Bireysel' : 'Individual'; ?>
                        </div>
                        <div class="type-desc">
                            <?php echo $lang === 'tr' ? 'Kendi mülkünüzü ilan verin' : 'Post your own property'; ?>
                        </div>
                    </div>
                    <div class="type-card <?php echo ($form_data['user_type'] ?? '') === 'emlakci' ? 'active' : ''; ?>"
                        onclick="selectType('emlakci')">
                        <div class="type-icon" style="color: #f59e0b;"><i class="fas fa-building"></i></div>
                        <div class="type-title">
                            <?php echo $lang === 'tr' ? 'Emlakçı' : 'Real Estate Agent'; ?>
                        </div>
                        <div class="type-desc">
                            <?php echo $lang === 'tr' ? 'Emlak ofisi olarak ilan verin' : 'Post as an office'; ?>
                        </div>
                    </div>
                </div>

                <!-- Temel Bilgiler -->
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Ad Soyad' : 'Full Name'; ?> <span
                                class="required-star">*</span>
                        </label>
                        <input type="text" name="full_name"
                            value="<?php echo htmlspecialchars($form_data['full_name'] ?? ''); ?>" required
                            placeholder="<?php echo $lang === 'tr' ? 'Adınız Soyadınız' : 'Your full name'; ?>">
                    </div>
                    <div class="form-group">
                        <label>
                            <?php echo t('username'); ?> <span class="required-star">*</span>
                        </label>
                        <input type="text" name="username"
                            value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required
                            placeholder="<?php echo $lang === 'tr' ? 'Kullanıcı adınız' : 'Your username'; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>E-posta <span class="required-star">*</span></label>
                        <input type="email" name="email"
                            value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required
                            placeholder="ornek@email.com">
                    </div>
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Telefon' : 'Phone'; ?>
                        </label>
                        <input type="tel" name="phone"
                            value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>"
                            placeholder="0555 555 5555">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <?php echo t('password'); ?> <span class="required-star">*</span>
                        </label>
                        <input type="password" name="password" required minlength="6"
                            placeholder="<?php echo $lang === 'tr' ? 'En az 6 karakter' : 'At least 6 characters'; ?>">
                    </div>
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Şifre Tekrar' : 'Confirm Password'; ?> <span
                                class="required-star">*</span>
                        </label>
                        <input type="password" name="password_confirm" required minlength="6"
                            placeholder="<?php echo $lang === 'tr' ? 'Şifrenizi tekrar giriniz' : 'Confirm your password'; ?>">
                    </div>
                </div>

                <!-- Emlakçı Ek Alanları -->
                <div class="emlakci-fields <?php echo ($form_data['user_type'] ?? '') === 'emlakci' ? 'visible' : ''; ?>"
                    id="emlakciFields">
                    <h3><i class="fas fa-building"></i>
                        <?php echo $lang === 'tr' ? 'Emlak Ofisi Bilgileri' : 'Real Estate Office Information'; ?>
                    </h3>
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Firma Adı' : 'Company Name'; ?> <span
                                class="required-star">*</span>
                        </label>
                        <input type="text" name="firma_adi"
                            value="<?php echo htmlspecialchars($form_data['firma_adi'] ?? ''); ?>"
                            placeholder="<?php echo $lang === 'tr' ? 'Emlak ofisi adınız' : 'Your office name'; ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>
                                <?php echo $lang === 'tr' ? 'Vergi Numarası' : 'Tax Number'; ?>
                            </label>
                            <input type="text" name="vergi_no"
                                value="<?php echo htmlspecialchars($form_data['vergi_no'] ?? ''); ?>"
                                placeholder="1234567890">
                        </div>
                        <div class="form-group">
                            <label>
                                <?php echo $lang === 'tr' ? 'Lisans Numarası' : 'License Number'; ?>
                            </label>
                            <input type="text" name="lisans_no"
                                value="<?php echo htmlspecialchars($form_data['lisans_no'] ?? ''); ?>"
                                placeholder="EM-12345">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Ofis Adresi' : 'Office Address'; ?>
                        </label>
                        <textarea name="ofis_adresi" rows="2"
                            placeholder="<?php echo $lang === 'tr' ? 'Ofisinizin tam adresi' : 'Full office address'; ?>"><?php echo htmlspecialchars($form_data['ofis_adresi'] ?? ''); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn-register" id="submitBtn" disabled>
                    <?php echo $lang === 'tr' ? 'Kayıt Ol' : 'Register'; ?>
                </button>
            </form>

            <div class="login-link">
                <?php echo $lang === 'tr' ? 'Zaten hesabınız var mı?' : 'Already have an account?'; ?>
                <a href="/giris">
                    <?php echo $lang === 'tr' ? 'Giriş Yap' : 'Login'; ?>
                </a>
            </div>

            <div class="login-link" style="margin-top: 0.75rem;">
                <a href="/">←
                    <?php echo $lang === 'tr' ? 'Ana Sayfaya Dön' : 'Back to Home'; ?>
                </a>
            </div>
        </div>
    </div>

    <script>
        function selectType(type) {
            document.getElementById('user_type').value = type;

            // Card'ları güncelle
            document.querySelectorAll('.type-card').forEach(card => card.classList.remove('active'));
            event.currentTarget.classList.add('active');

            // Emlakçı alanlarını göster/gizle
            const emlakciFields = document.getElementById('emlakciFields');
            if (type === 'emlakci') {
                emlakciFields.classList.add('visible');
            } else {
                emlakciFields.classList.remove('visible');
            }

            // Submit butonunu aktifle
            document.getElementById('submitBtn').disabled = false;
        }

        // Sayfa yüklendiğinde, eğer tip seçiliyse butonu aktifle
        if (document.getElementById('user_type').value) {
            document.getElementById('submitBtn').disabled = false;
        }
    </script>
</body>

</html>
<?php ob_end_flush(); ?>