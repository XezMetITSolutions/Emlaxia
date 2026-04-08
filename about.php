<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'tr' ? 'Hakkımızda' : 'About Us'; ?> - Emlaxia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header {
            background: linear-gradient(rgba(0, 35, 71, 0.8), rgba(0, 35, 71, 0.8)), url('assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            padding: 80px 0;
            color: white;
            text-align: center;
        }
        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .breadcrumb {
            color: rgba(255,255,255,0.8);
        }
        .breadcrumb a {
            color: white;
            text-decoration: none;
        }
        .content-section {
            padding: 60px 0;
            background: white;
        }
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }
        @media (max-width: 768px) {
            .about-grid {
                grid-template-columns: 1fr;
            }
        }
        .about-image img {
            width: 100%;
            border-radius: 15px;
            box-shadow: var(--shadow-xl);
        }
        .about-text h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 2rem;
        }
        .about-text p {
            color: var(--gray-600);
            line-height: 1.8;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="page-header">
            <div class="container">
                <h1><?php echo $lang == 'tr' ? 'Hakkımızda' : 'About Us'; ?></h1>
                <div class="breadcrumb">
                    <a href="index.php"><?php echo t('home'); ?></a> / <span><?php echo $lang == 'tr' ? 'Hakkımızda' : 'About Us'; ?></span>
                </div>
            </div>
        </section>

        <section class="content-section">
            <div class="container">
                <div class="about-grid">
                    <div class="about-image">
                        <img src="assets/images/placeholder.jpg" alt="About Us">
                    </div>
                    <div class="about-text">
                        <h2>Emlaxia</h2>
                        <p>
                            <?php echo $lang == 'tr' 
                                ? '2010 yılından bu yana gayrimenkul sektöründe faaliyet gösteren Emlaxia, dürüstlük ve şeffaflık ilkeleriyle binlerce müşterisini hayallerindeki mülkle buluşturmuştur.' 
                                : 'Operating in the real estate sector since 2010, Emlaxia has brought thousands of customers together with their dream properties with the principles of honesty and transparency.'; ?>
                        </p>
                        <p>
                            <?php echo $lang == 'tr'
                                ? 'Profesyonel ekibimiz, pazar analizi, değerleme, pazarlama ve satış sonrası destek konularında uzmanlaşmıştır. Amacımız sadece mülk satmak değil, müşterilerimizle uzun vadeli güven ilişkisi kurmaktır.'
                                : 'Our professional team specializes in market analysis, valuation, marketing, and after-sales support. Our goal is not just to sell properties, but to establish long-term trust relationships with our customers.'; ?>
                        </p>
                        <a href="contact.php" class="btn btn-primary"><?php echo $lang == 'tr' ? 'Bize Ulaşın' : 'Contact Us'; ?></a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
