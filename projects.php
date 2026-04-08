<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'tr' ? 'Projeler' : 'Projects'; ?> — Emlaxia.com</title>
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
        .projects-section {
            padding: 80px 0;
            background: white;
        }
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }
        .project-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: all 0.3s;
            background: white;
            border: 1px solid #eee;
        }
        .project-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }
        .project-image {
            height: 250px;
            background: #ddd;
            position: relative;
        }
        .project-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .project-status {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--secondary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .project-content {
            padding: 25px;
        }
        .project-content h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.4rem;
        }
        .project-location {
            color: var(--gray-500);
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        .project-desc {
            color: var(--gray-600);
            margin-bottom: 20px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="page-header">
            <div class="container">
                <h1><?php echo $lang == 'tr' ? 'Projeler' : 'Projects'; ?></h1>
                <div class="breadcrumb">
                    <a href="index.php"><?php echo t('home'); ?></a> / <span><?php echo $lang == 'tr' ? 'Projeler' : 'Projects'; ?></span>
                </div>
            </div>
        </section>

        <section class="projects-section">
            <div class="container">
                <div class="projects-grid">
                    <!-- Yenişehir Project -->
                    <div class="project-card">
                        <div class="project-image">
                            <img src="assets/images/placeholder.jpg" alt="Yenişehir Projesi">
                            <span class="project-status"><?php echo $lang == 'tr' ? 'Fırsat' : 'Opportunity'; ?></span>
                        </div>
                        <div class="project-content">
                            <h3><?php echo $lang == 'tr' ? 'Yenişehir Projesi' : 'Yenişehir Project'; ?></h3>
                            <div class="project-location"><i class="fas fa-map-marker-alt"></i> İstanbul, Arnavutköy</div>
                            <p class="project-desc">
                                <?php echo $lang == 'tr' ? 'Kanal İstanbul manzaralı, yüksek yatırım potansiyeline sahip arsa projesi.' : 'Land project with high investment potential and Canal Istanbul view.'; ?>
                            </p>
                            <a href="yenisehir.php" class="btn btn-secondary btn-small"><?php echo $lang == 'tr' ? 'Detaylı İncele' : 'View Details'; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
