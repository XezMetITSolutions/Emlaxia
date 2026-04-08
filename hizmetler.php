<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'tr' ? 'Hizmetlerimiz' : 'Services'; ?> - Emlaxia</title>
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
        .services-section {
            padding: 80px 0;
            background: #f8fafc;
        }
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        .service-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: var(--shadow-md);
            transition: all 0.3s;
            text-align: center;
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }
        .service-icon {
            width: 80px;
            height: 80px;
            background: var(--gray-100);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: var(--secondary-color);
            font-size: 2rem;
            transition: all 0.3s;
        }
        .service-card:hover .service-icon {
            background: var(--secondary-color);
            color: white;
        }
        .service-card h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.4rem;
        }
        .service-card p {
            color: var(--gray-600);
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="page-header">
            <div class="container">
                <h1><?php echo $lang == 'tr' ? 'Hizmetlerimiz' : 'Services'; ?></h1>
                <div class="breadcrumb">
                    <a href="index.php"><?php echo t('home'); ?></a> / <span><?php echo $lang == 'tr' ? 'Hizmetlerimiz' : 'Services'; ?></span>
                </div>
            </div>
        </section>

        <section class="services-section">
            <div class="container">
                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3><?php echo $lang == 'tr' ? 'Alım & Satım Danışmanlığı' : 'Buying & Selling Consultancy'; ?></h3>
                        <p><?php echo $lang == 'tr' ? 'Gayrimenkul alım ve satım süreçlerinizde profesyonel danışmanlık hizmeti sunuyoruz.' : 'We offer professional consultancy services in your real estate buying and selling processes.'; ?></p>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3><?php echo $lang == 'tr' ? 'Ekspertiz & Değerleme' : 'Appraisal & Valuation'; ?></h3>
                        <p><?php echo $lang == 'tr' ? 'Mülkünüzün gerçek piyasa değerini belirleyerek en doğru fiyatla işlem görmesini sağlıyoruz.' : 'We ensure that your property is traded at the most accurate price by determining its real market value.'; ?></p>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h3><?php echo $lang == 'tr' ? 'Pazarlama & Tanıtım' : 'Marketing & Promotion'; ?></h3>
                        <p><?php echo $lang == 'tr' ? 'Mülkünüzü en etkili kanallarda tanıtarak potansiyel alıcılara hızlıca ulaştırıyoruz.' : 'We promote your property in the most effective channels and reach potential buyers quickly.'; ?></p>
                    </div>

                    <!-- Rental Management removed -->

                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <h3><?php echo $lang == 'tr' ? 'Proje Danışmanlığı' : 'Project Consultancy'; ?></h3>
                        <p><?php echo $lang == 'tr' ? 'İnşaat projelerinizin satış ve pazarlama süreçlerini baştan sona yönetiyoruz.' : 'We manage the sales and marketing processes of your construction projects from start to finish.'; ?></p>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h3><?php echo $lang == 'tr' ? 'Yatırım Danışmanlığı' : 'Investment Consultancy'; ?></h3>
                        <p><?php echo $lang == 'tr' ? 'Geleceğe dönük karlı gayrimenkul yatırımları yapmanız için size rehberlik ediyoruz.' : 'We guide you to make profitable real estate investments for the future.'; ?></p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
