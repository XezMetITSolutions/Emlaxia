<?php
/**
 * İlan Ver - Yönlendirme Sayfası
 */
require_once 'config.php';
require_once 'includes/auth.php';

// Eğer zaten giriş yapmışsa direkt ilan verme sayfasına veya panele atalım
if (checkUserAuth()) {
    $redirect = $_SESSION['user_type'] === 'emlakci' ? '/emlakci/dashboard' : '/bireysel/dashboard';
    header('Location: ' . $redirect);
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang === 'tr' ? 'İlan Ver' : 'Post Ad'; ?> - Emlaxia</title>
    <base href="/">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .post-ad-page {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            padding: 2rem;
        }

        .post-ad-container {
            max-width: 800px;
            width: 100%;
            text-align: center;
        }

        .post-ad-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #0F123D;
            margin-bottom: 1rem;
        }

        .post-ad-subtitle {
            font-size: 1.1rem;
            color: #64748b;
            margin-bottom: 3rem;
        }

        .post-ad-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .option-card {
            background: white;
            padding: 3rem 2rem;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .option-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: #D3AF37;
        }

        .option-icon {
            font-size: 3.5rem;
            color: #D3AF37;
            margin-bottom: 1.5rem;
        }

        .option-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0F123D;
            margin-bottom: 1rem;
        }

        .option-desc {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn-option {
            width: 100%;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.2s;
        }

        .btn-has-account {
            background: #0F123D;
            color: white;
        }

        .btn-no-account {
            background: #D3AF37;
            color: #0F123D;
        }

        @media (max-width: 768px) {
            .post-ad-options {
                grid-template-columns: 1fr;
            }
            .post-ad-title {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main class="post-ad-page">
        <div class="post-ad-container">
            <h1 class="post-ad-title">
                <?php echo $lang === 'tr' ? 'Hemen İlan Vermeye Başlayın' : 'Start Posting Ads Now'; ?>
            </h1>
            <p class="post-ad-subtitle">
                <?php echo $lang === 'tr' ? 'Mülkünüzü milyonlarla buluşturmak için ilk adımı atın.' : 'Take the first step to bring your property together with millions.'; ?>
            </p>

            <div class="post-ad-options">
                <!-- Hesabım Var -->
                <a href="/giris" class="option-card">
                    <div class="option-icon"><i class="fas fa-user-check"></i></div>
                    <div class="option-title"><?php echo $lang === 'tr' ? 'Hesabım Var' : 'I Have an Account'; ?></div>
                    <p class="option-desc">
                        <?php echo $lang === 'tr' ? 'Mevcut bilgilerinizle giriş yapın ve hızlıca ilanınızı oluşturun.' : 'Log in with your current information and quickly create your ad.'; ?>
                    </p>
                    <div class="btn btn-option btn-has-account"><?php echo $lang === 'tr' ? 'Giriş Yap' : 'Login'; ?></div>
                </a>

                <!-- Hesabım Yok -->
                <a href="/kayit-ilan" class="option-card">
                    <div class="option-icon"><i class="fas fa-user-plus"></i></div>
                    <div class="option-title"><?php echo $lang === 'tr' ? 'Hesabım Yok' : 'I Don\'t Have an Account'; ?></div>
                    <p class="option-desc">
                        <?php echo $lang === 'tr' ? 'Ücretsiz olarak kayıt olun, bireysel veya kurumsal avantajlardan yararlanın.' : 'Register for free, take advantage of individual or corporate benefits.'; ?>
                    </p>
                    <div class="btn btn-option btn-no-account"><?php echo $lang === 'tr' ? 'Üye Ol ve İlan Ver' : 'Register and Post Ad'; ?></div>
                </a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="/assets/js/main.js"></script>
</body>

</html>
