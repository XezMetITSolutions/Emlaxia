<?php
/**
 * Yeni Şifre Belirleme Sayfası
 */
ob_start();
require_once 'config.php';
require_once 'includes/auth.php';

if (checkUserAuth()) {
    header('Location: /');
    exit;
}

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$error = '';
$success = '';

if (empty($token)) {
    header('Location: /giris');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($password) || empty($password_confirm)) {
        $error = $lang === 'tr' ? 'Tüm alanları doldurunuz.' : 'All fields are required.';
    } elseif ($password !== $password_confirm) {
        $error = $lang === 'tr' ? 'Şifreler eşleşmiyor.' : 'Passwords do not match.';
    } else {
        $result = resetPassword($pdo, $token, $password);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
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
        <?php echo $lang === 'tr' ? 'Yeni Şifre Belirle' : 'Set New Password'; ?> - Emlaxia
    </title>
    <base href="/">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .login-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.97);
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #1e88e5, #43a047, #1e88e5);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo {
            max-width: 220px;
            height: auto;
            margin-bottom: 0.5rem;
        }

        .login-logo p {
            color: #64748b;
            margin-top: 0.5rem;
            font-size: 0.95rem;
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

        .form-group input {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.2s;
            background: #f8fafc;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1e88e5;
            background: white;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        }

        .btn-login {
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

        .btn-login:hover {
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

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #64748b;
        }

        .register-link a {
            color: #1e88e5;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-logo">
                <img src="/Logo.png" alt="Emlaxia Logo" class="brand-logo">
                <p>
                    <?php echo $lang === 'tr' ? 'Yeni şifrenizi belirleyin' : 'Set your new password'; ?>
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
                <div class="register-link">
                    <a href="/giris" class="btn-login" style="display:block; text-decoration:none;">
                        <?php echo $lang === 'tr' ? 'Giriş Yap' : 'Login'; ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
                <form method="POST" action="/sifre-sifirla">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Yeni Şifre' : 'New Password'; ?>
                        </label>
                        <input type="password" name="password" required autofocus minlength="6"
                            placeholder="<?php echo $lang === 'tr' ? 'En az 6 karakter' : 'At least 6 characters'; ?>">
                    </div>

                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'Yeni Şifre Tekrar' : 'Confirm New Password'; ?>
                        </label>
                        <input type="password" name="password_confirm" required minlength="6"
                            placeholder="<?php echo $lang === 'tr' ? 'Yeni şifrenizi tekrar girin' : 'Repeat your new password'; ?>">
                    </div>

                    <button type="submit" class="btn-login">
                        <?php echo $lang === 'tr' ? 'Şifreyi Güncelle' : 'Update Password'; ?>
                    </button>
                </form>
            <?php endif; ?>

            <?php if (!$success): ?>
                <div class="register-link">
                    <a href="/giris">←
                        <?php echo $lang === 'tr' ? 'Girişe Dön' : 'Back to Login'; ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php ob_end_flush(); ?>
