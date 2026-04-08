<?php
/**
 * Şifremi Unuttum Sayfası
 */
ob_start();
require_once 'config.php';
require_once 'includes/auth.php';

if (checkUserAuth()) {
    header('Location: /');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = $lang === 'tr' ? 'Geçerli bir e-posta adresi giriniz.' : 'Please enter a valid email address.';
        } else {
            $result = requestPasswordReset($pdo, $email);
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }
    } else {
        $error = $lang === 'tr' ? 'E-posta adresi gereklidir.' : 'Email address is required.';
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $lang === 'tr' ? 'Şifremi Unuttum' : 'Forgot Password'; ?> - Emlaxia
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
                    <?php echo $lang === 'tr' ? 'Şifrenizi sıfırlayın' : 'Reset your password'; ?>
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

            <?php if (!$success): ?>
                <form method="POST" action="/sifremi-unuttum">
                    <div class="form-group">
                        <label>
                            <?php echo $lang === 'tr' ? 'E-posta Adresi' : 'Email Address'; ?>
                        </label>
                        <input type="email" name="email" required autofocus
                            placeholder="<?php echo $lang === 'tr' ? 'E-posta adresiniz' : 'Your email address'; ?>">
                    </div>

                    <button type="submit" class="btn-login">
                        <?php echo $lang === 'tr' ? 'Sıfırlama Bağlantısı Gönder' : 'Send Reset Link'; ?>
                    </button>
                </form>
            <?php endif; ?>

            <div class="register-link">
                <a href="/giris">←
                    <?php echo $lang === 'tr' ? 'Girişe Dön' : 'Back to Login'; ?>
                </a>
            </div>
        </div>
    </div>
</body>

</html>
<?php ob_end_flush(); ?>
