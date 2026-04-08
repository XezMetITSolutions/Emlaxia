<?php
// Output buffering başlat (header göndermeden önce output engellemek için)
ob_start();

require_once '../config.php';

// Zaten giriş yapılmışsa dashboard'a yönlendir
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    ob_end_clean();
    header('Location: dashboard');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE LOWER(username) = LOWER(:username)");
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'] ?? 'admin'; // Default to admin if not set

            // Session'ı hemen kaydet
            session_write_close();

            // Buffer'ı temizle ve yönlendir
            ob_end_clean();
            header('Location: dashboard');
            exit;
        } else {
            $error = t('login_error');
        }
    } else {
        $current_lang = $_SESSION['lang'] ?? 'tr';
        $error = $current_lang == 'tr' ? 'Kullanıcı adı ve şifre gereklidir' : 'Username and password are required';
    }
}

// $lang değişkenini tanımla
$lang = $_SESSION['lang'] ?? 'tr';
?>
<?php
// ... (PHP logic stays the same)
$istanbul_images = [
    'https://images.unsplash.com/photo-1541432901042-2d8bd64b4a9b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80',
    'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80',
    'https://images.unsplash.com/photo-1550439062-609e1531270e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80',
    'https://images.unsplash.com/photo-1527838832702-585f23df267f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80',
    'https://images.unsplash.com/photo-1600832415849-cbe24c4eeb5b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80'
];
$random_bg = $istanbul_images[array_rand($istanbul_images)];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('admin_login'); ?> - Emlaxia</title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        .login-wrapper {
            height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('<?php echo $random_bg; ?>') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        .login-wrapper::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(15, 18, 61, 0.8) 0%, rgba(10, 13, 46, 0.4) 100%);
            z-index: 1;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            width: 100%;
            max-width: 420px;
            padding: 3rem;
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 2;
            text-align: center;
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-logo {
            margin-bottom: 2rem;
        }

        .login-logo img {
            height: 70px;
            width: auto;
        }

        .login-card h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0F123D;
            margin-bottom: 0.5rem;
        }

        .login-card p.subtitle {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .form-group-modern {
            text-align: left;
            margin-bottom: 1.5rem;
        }

        .form-group-modern label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 0.5rem;
            margin-left: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .input-wrapper input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border-radius: 1rem;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            font-size: 1rem;
            color: #1e293b;
            transition: all 0.3s;
            box-sizing: border-box;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #D3AF37;
            background: white;
            box-shadow: 0 0 0 4px rgba(211, 175, 55, 0.1);
        }

        .btn-login-modern {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #0F123D 0%, #1a1f52 100%);
            color: white;
            border: none;
            border-radius: 1rem;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            box-shadow: 0 10px 15px -3px rgba(15, 18, 61, 0.3);
        }

        .btn-login-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(15, 18, 61, 0.4);
            filter: brightness(1.2);
        }

        .back-link {
            display: inline-block;
            margin-top: 2rem;
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #D3AF37;
        }

        .alert-modern {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <img src="/Logo.png" alt="Emlaxia">
            </div>
            <h1><?php echo t('admin_login'); ?></h1>
            <p class="subtitle">Emlaxia Yönetim Paneline Hoş Geldiniz</p>

            <?php if ($error): ?>
                <div class="alert-modern"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="admin/login">
                <div class="form-group-modern">
                    <label><?php echo t('username'); ?></label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" required autofocus placeholder="Kullanıcı Adı">
                    </div>
                </div>
                <div class="form-group-modern">
                    <label><?php echo t('password'); ?></label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" required placeholder="Şifre">
                    </div>
                </div>
                <button type="submit" class="btn-login-modern">
                    <?php echo t('login'); ?> <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                </button>
            </form>

            <a href="/index.php" class="back-link">
                <i class="fas fa-chevron-left" style="margin-right: 5px;"></i> 
                <?php echo $lang == 'tr' ? 'Ana Sayfaya Dön' : 'Back to Home'; ?>
            </a>
        </div>
    </div>
</body>

</html>