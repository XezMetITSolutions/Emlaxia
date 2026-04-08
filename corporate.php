<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'tr' ? 'Kurumsal - Emlaxia | Gayrimenkulde Güvenin Adresi' : 'Corporate - Emlaxia | Your Trusted Real Estate Partner'; ?></title>
    <meta name="description" content="<?php echo $lang == 'tr' ? 'Emlaxia, Türkiye\'nin dinamik gayrimenkul pazarında yenilikçi bir soluk getirmek amacıyla kurulmuş, hızla yükselen bir gayrimenkul yatırım ve danışmanlık firmasıdır.' : 'Emlaxia is a rapidly rising real estate investment and consulting firm established to bring an innovative breath to Turkey\'s dynamic real estate market.'; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Hero Section */
        .corporate-hero {
            background: linear-gradient(135deg, rgba(15, 18, 61, 0.95) 0%, rgba(10, 13, 46, 0.9) 100%), 
                        url('assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 120px 0 100px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .corporate-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 50%, rgba(211, 175, 55, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(211, 175, 55, 0.1) 0%, transparent 40%);
            pointer-events: none;
        }
        
        .corporate-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #D3AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInUp 0.8s ease-out;
        }
        
        .corporate-hero .tagline {
            font-size: 1.4rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 700px;
            margin: 0 auto;
            font-weight: 300;
            letter-spacing: 0.5px;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }
        
        .breadcrumb {
            margin-top: 2rem;
            color: rgba(255,255,255,0.7);
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }
        
        .breadcrumb a {
            color: #D3AF37;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .breadcrumb a:hover {
            color: white;
        }

        /* About Introduction Section */
        .about-intro {
            padding: 100px 0;
            background: linear-gradient(180deg, #f8f9fc 0%, #ffffff 100%);
            position: relative;
        }
        
        .about-intro::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23D3AF37' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 1;
        }
        
        .intro-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
            position: relative;
        }
        
        .intro-text h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: #0F123D;
            margin-bottom: 30px;
            position: relative;
        }
        
        .intro-text h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #D3AF37, transparent);
        }
        
        .intro-text p {
            color: #555;
            line-height: 1.9;
            margin-bottom: 20px;
            font-size: 1.05rem;
        }
        
        .intro-visual {
            position: relative;
        }
        
        .intro-visual-card {
            background: linear-gradient(145deg, #0F123D 0%, #1a1f4e 100%);
            border-radius: 24px;
            padding: 50px;
            box-shadow: 0 30px 80px rgba(15, 18, 61, 0.25);
            position: relative;
            overflow: hidden;
        }
        
        .intro-visual-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(211, 175, 55, 0.2) 0%, transparent 70%);
        }
        
        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .stat-item {
            text-align: center;
            padding: 25px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            border: 1px solid rgba(211, 175, 55, 0.2);
            transition: all 0.3s ease;
        }
        
        .stat-item:hover {
            background: rgba(211, 175, 55, 0.1);
            transform: translateY(-5px);
        }
        
        .stat-item .number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #D3AF37;
            font-family: 'Playfair Display', serif;
            display: block;
        }
        
        .stat-item .label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 8px;
        }

        /* Portfolio Pillars Section */
        .portfolio-section {
            padding: 100px 0;
            background: #0F123D;
            position: relative;
            overflow: hidden;
        }
        
        .portfolio-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 80%;
            height: 200%;
            background: radial-gradient(ellipse, rgba(211, 175, 55, 0.08) 0%, transparent 60%);
            pointer-events: none;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 70px;
            position: relative;
        }
        
        .section-header .subtitle {
            color: #D3AF37;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 15px;
        }
        
        .section-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            color: white;
            margin-bottom: 20px;
        }
        
        .section-header .description {
            color: rgba(255, 255, 255, 0.7);
            max-width: 600px;
            margin: 0 auto;
            font-size: 1.1rem;
        }
        
        .pillars-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        
        .pillar-card {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 24px;
            padding: 50px;
            border: 1px solid rgba(211, 175, 55, 0.15);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        .pillar-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #D3AF37, #f0d77a);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }
        
        .pillar-card:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .pillar-card:hover::before {
            transform: scaleX(1);
        }
        
        .pillar-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #D3AF37 0%, #f0d77a 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .pillar-icon i {
            font-size: 1.8rem;
            color: #0F123D;
        }
        
        .pillar-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            color: white;
            margin-bottom: 20px;
        }
        
        .pillar-card p {
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.8;
            font-size: 1rem;
        }
        
        .pillar-card .location-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(211, 175, 55, 0.15);
            color: #D3AF37;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.85rem;
            margin-top: 25px;
        }

        /* Why Choose Us Section */
        .why-section {
            padding: 100px 0;
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fc 100%);
        }
        
        .why-header {
            text-align: center;
            margin-bottom: 70px;
        }
        
        .why-header .subtitle {
            color: #D3AF37;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 15px;
        }
        
        .why-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            color: #0F123D;
        }
        
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
        }
        
        .benefit-card {
            background: white;
            border-radius: 20px;
            padding: 45px 35px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(15, 18, 61, 0.08);
            border: 1px solid rgba(15, 18, 61, 0.05);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        .benefit-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #D3AF37, #f0d77a);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        
        .benefit-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 60px rgba(15, 18, 61, 0.15);
        }
        
        .benefit-card:hover::after {
            transform: scaleX(1);
        }
        
        .benefit-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #0F123D 0%, #1a1f4e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #D3AF37;
            box-shadow: 0 8px 25px rgba(15, 18, 61, 0.25);
        }
        
        .benefit-card h3 {
            font-size: 1.4rem;
            color: #0F123D;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .benefit-card p {
            color: #666;
            line-height: 1.7;
            font-size: 0.95rem;
        }

        /* Vision Section */
        .vision-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #0F123D 0%, #1a1f4e 100%);
            position: relative;
            overflow: hidden;
        }
        
        .vision-section::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(211, 175, 55, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .vision-content {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
            position: relative;
        }
        
        .vision-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #D3AF37 0%, #f0d77a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 40px;
            box-shadow: 0 15px 50px rgba(211, 175, 55, 0.3);
        }
        
        .vision-icon i {
            font-size: 2.5rem;
            color: #0F123D;
        }
        
        .vision-content h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: white;
            margin-bottom: 30px;
        }
        
        .vision-content p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1.2rem;
            line-height: 1.9;
            margin-bottom: 25px;
        }
        
        .vision-highlight {
            background: rgba(211, 175, 55, 0.1);
            border-left: 4px solid #D3AF37;
            padding: 25px 35px;
            border-radius: 0 12px 12px 0;
            margin-top: 40px;
            text-align: left;
        }
        
        .vision-highlight p {
            font-size: 1.1rem;
            font-style: italic;
            margin: 0;
        }

        /* CTA Section */
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #D3AF37 0%, #b8941d 100%);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%230F123D' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
        
        .cta-content {
            position: relative;
        }
        
        .cta-content h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: #0F123D;
            margin-bottom: 20px;
        }
        
        .cta-content p {
            color: rgba(15, 18, 61, 0.8);
            font-size: 1.15rem;
            margin-bottom: 35px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: #0F123D;
            color: white;
            padding: 18px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.05rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(15, 18, 61, 0.3);
        }
        
        .cta-btn:hover {
            background: #1a1f4e;
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(15, 18, 61, 0.4);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .corporate-hero h1 {
                font-size: 2.5rem;
            }
            
            .intro-content {
                grid-template-columns: 1fr;
                gap: 50px;
            }
            
            .pillars-grid {
                grid-template-columns: 1fr;
            }
            
            .benefits-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .corporate-hero {
                padding: 80px 0 60px;
            }
            
            .corporate-hero h1 {
                font-size: 2rem;
            }
            
            .corporate-hero .tagline {
                font-size: 1.1rem;
            }
            
            .section-header h2,
            .why-header h2,
            .vision-content h2,
            .intro-text h2 {
                font-size: 2rem;
            }
            
            .about-intro,
            .portfolio-section,
            .why-section,
            .vision-section {
                padding: 60px 0;
            }
            
            .pillar-card,
            .benefit-card {
                padding: 35px 25px;
            }
            
            .stat-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="corporate-hero">
            <div class="container">
                <h1><?php echo $lang == 'tr' ? 'Emlaxia' : 'Emlaxia'; ?></h1>
                <p class="tagline">
                    <?php echo $lang == 'tr' 
                        ? 'Gayrimenkulde Tecrübe ve Geleceğin Buluşma Noktası' 
                        : 'Where Experience Meets the Future of Real Estate'; ?>
                </p>
                <div class="breadcrumb">
                    <a href="index.php"><?php echo t('home'); ?></a> / <span><?php echo $lang == 'tr' ? 'Kurumsal' : 'Corporate'; ?></span>
                </div>
            </div>
        </section>

        <!-- About Introduction Section -->
        <section class="about-intro">
            <div class="container">
                <div class="intro-content">
                    <div class="intro-text">
                        <h2><?php echo $lang == 'tr' ? 'Hakkımızda' : 'About Us'; ?></h2>
                        <p>
                            <?php echo $lang == 'tr' 
                                ? 'Emlaxia, Türkiye\'nin dinamik gayrimenkul pazarında yenilikçi bir soluk getirmek amacıyla kurulmuş, hızla yükselen bir gayrimenkul yatırım ve danışmanlık firmasıdır. Gücümüzü; sektörün nabzını on yıllardır tutan deneyimli gayrimenkul danışmanlarımızdan ve Türkiye\'nin en stratejik bölgelerine odaklanan geniş portföyümüzden alıyoruz.' 
                                : 'Emlaxia is a rapidly rising real estate investment and consulting firm established to bring an innovative breath to Turkey\'s dynamic real estate market. We draw our strength from our experienced real estate consultants who have been tracking the pulse of the sector for decades and our extensive portfolio focused on Turkey\'s most strategic regions.'; ?>
                        </p>
                        <p>
                            <?php echo $lang == 'tr' 
                                ? 'Emlaxia olarak, sadece emlak satmıyor; ihtiyaca yönelik, veriye dayalı ve yüksek kazanç potansiyelli çözümler üretiyoruz. Portföyümüzü iki ana sütun üzerine inşa ettik: İstanbul Yenişehir bölgesi ve Karadeniz\'in eşsiz yatırım potansiyeli.' 
                                : 'At Emlaxia, we don\'t just sell real estate; we create need-oriented, data-driven, and high-profit potential solutions. We have built our portfolio on two main pillars: the Istanbul Yenişehir region and the unique investment potential of the Black Sea coast.'; ?>
                        </p>
                    </div>
                    <div class="intro-visual">
                        <div class="intro-visual-card">
                            <div class="stat-grid">
                                <div class="stat-item">
                                    <span class="number">10+</span>
                                    <span class="label"><?php echo $lang == 'tr' ? 'Yıllık Deneyim' : 'Years of Experience'; ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="number">500+</span>
                                    <span class="label"><?php echo $lang == 'tr' ? 'Başarılı İşlem' : 'Successful Transactions'; ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="number">2</span>
                                    <span class="label"><?php echo $lang == 'tr' ? 'Stratejik Bölge' : 'Strategic Regions'; ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="number">%100</span>
                                    <span class="label"><?php echo $lang == 'tr' ? 'Müşteri Memnuniyeti' : 'Customer Satisfaction'; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Portfolio Pillars Section -->
        <section class="portfolio-section">
            <div class="container">
                <div class="section-header">
                    <div class="subtitle"><?php echo $lang == 'tr' ? 'Uzmanlık Alanlarımız' : 'Our Areas of Expertise'; ?></div>
                    <h2><?php echo $lang == 'tr' ? 'Sınırları Aşan Portföy, Uzman Bakış Açısı' : 'Portfolio Beyond Borders, Expert Perspective'; ?></h2>
                    <p class="description">
                        <?php echo $lang == 'tr' 
                            ? 'İki stratejik bölgede geniş yelpazede hizmet sunuyoruz' 
                            : 'We serve a wide range in two strategic regions'; ?>
                    </p>
                </div>
                
                <div class="pillars-grid">
                    <div class="pillar-card">
                        <div class="pillar-icon">
                            <i class="fas fa-city"></i>
                        </div>
                        <h3><?php echo $lang == 'tr' ? 'İstanbul Yenişehir Vizyonu' : 'Istanbul Yenişehir Vision'; ?></h3>
                        <p>
                            <?php echo $lang == 'tr' 
                                ? 'İstanbul\'un gelecekteki merkezi Yenişehir bölgesinde, modern şehircilik projelerinden stratejik ticari alanlara kadar en özel yatırım fırsatlarını sunuyoruz. Bölgenin gelişimini anlık takip ederek sizi geleceğin kazananları arasına taşıyoruz.' 
                                : 'In the Yenişehir region, Istanbul\'s future center, we offer the most exclusive investment opportunities from modern urban projects to strategic commercial areas. We carry you among the winners of the future by tracking the development of the region in real-time.'; ?>
                        </p>
                        <div class="location-tag">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo $lang == 'tr' ? 'İstanbul, Yenişehir' : 'Istanbul, Yenişehir'; ?></span>
                        </div>
                    </div>
                    
                    <div class="pillar-card">
                        <div class="pillar-icon">
                            <i class="fas fa-mountain"></i>
                        </div>
                        <h3><?php echo $lang == 'tr' ? 'Karadeniz\'in Yatırım Potansiyeli' : 'Black Sea Investment Potential'; ?></h3>
                        <p>
                            <?php echo $lang == 'tr' 
                                ? 'Doğanın kalbinde, Karadeniz\'in dört bir yanındaki verimli arazilerden özel konut projelerine kadar geniş bir yelpazede hizmet veriyoruz. Bölgenin yerel dinamiklerine hakim ekibimizle, Karadeniz\'de doğru yatırımı yapmanızı sağlıyoruz.' 
                                : 'In the heart of nature, we serve a wide range from fertile lands all around the Black Sea to exclusive residential projects. With our team that dominates the local dynamics of the region, we ensure that you make the right investment in the Black Sea.'; ?>
                        </p>
                        <div class="location-tag">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo $lang == 'tr' ? 'Karadeniz Bölgesi' : 'Black Sea Region'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us Section -->
        <section class="why-section">
            <div class="container">
                <div class="why-header">
                    <div class="subtitle"><?php echo $lang == 'tr' ? 'Farkımız' : 'Our Difference'; ?></div>
                    <h2><?php echo $lang == 'tr' ? 'Neden Emlaxia\'yı Tercih Etmelisiniz?' : 'Why Choose Emlaxia?'; ?></h2>
                </div>
                
                <div class="benefits-grid">
                    <div class="benefit-card">
                        <div class="benefit-number">1</div>
                        <h3><?php echo $lang == 'tr' ? 'Yılların Deneyimi' : 'Years of Experience'; ?></h3>
                        <p>
                            <?php echo $lang == 'tr' 
                                ? 'Ekibimiz, Türkiye emlak piyasasında yüzlerce başarılı işleme imza atmış, hukuki süreçlerden pazar analizine kadar her konuda uzmanlaşmış kıdemli danışmanlardan oluşur.' 
                                : 'Our team consists of senior consultants who have completed hundreds of successful transactions in the Turkish real estate market, specialized in everything from legal processes to market analysis.'; ?>
                        </p>
                    </div>
                    
                    <div class="benefit-card">
                        <div class="benefit-number">2</div>
                        <h3><?php echo $lang == 'tr' ? 'Geniş ve Çeşitlendirilmiş Yelpaze' : 'Wide and Diversified Portfolio'; ?></h3>
                        <p>
                            <?php echo $lang == 'tr' 
                                ? 'Lüks konuttan stratejik arsalara, ticari mülklerden butik projelere kadar her bütçeye ve her hedefe uygun bir portföy sunuyoruz.' 
                                : 'We offer a portfolio suitable for every budget and every goal, from luxury residences to strategic lands, from commercial properties to boutique projects.'; ?>
                        </p>
                    </div>
                    
                    <div class="benefit-card">
                        <div class="benefit-number">3</div>
                        <h3><?php echo $lang == 'tr' ? 'Şeffaf ve Stratejik Danışmanlık' : 'Transparent and Strategic Consulting'; ?></h3>
                        <p>
                            <?php echo $lang == 'tr' 
                                ? 'Yatırım süreçlerinin her adımında dürüstlük ilkesiyle hareket ediyoruz. Karmaşık süreçleri sizin için kolaylaştırıyor, kararlarınızı somut verilerle destekliyoruz.' 
                                : 'We act with honesty at every step of the investment process. We simplify complex processes for you and support your decisions with concrete data.'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vision Section -->
        <section class="vision-section">
            <div class="container">
                <div class="vision-content">
                    <div class="vision-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h2><?php echo $lang == 'tr' ? 'Vizyonumuz' : 'Our Vision'; ?></h2>
                    <p>
                        <?php echo $lang == 'tr' 
                            ? 'Türkiye\'nin gayrimenkul potansiyelini, global standartlarda bir hizmet anlayışıyla birleştirmek. Emlaxia ismini güvenin ve doğru yatırımın sembolü haline getirerek, İstanbul\'un modern yüzünden Karadeniz\'in eşsiz doğasına kadar her noktada değer üretmeye devam etmek.' 
                            : 'To combine Turkey\'s real estate potential with a global standard of service. To continue creating value at every point from Istanbul\'s modern face to the unique nature of the Black Sea, by making the Emlaxia name a symbol of trust and the right investment.'; ?>
                    </p>
                    <div class="vision-highlight">
                        <p>
                            <?php echo $lang == 'tr' 
                                ? 'Siz de Türkiye\'nin parlayan yıldızında yerinizi almak ve profesyonel bir rehberle doğru yatırımı yapmak için Emlaxia uzmanlığıyla tanışın.' 
                                : 'Meet with Emlaxia expertise to take your place in Turkey\'s shining star and make the right investment with a professional guide.'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content">
                    <h2><?php echo $lang == 'tr' ? 'Yatırımınızı Birlikte Planlayalım' : 'Let\'s Plan Your Investment Together'; ?></h2>
                    <p>
                        <?php echo $lang == 'tr' 
                            ? 'Profesyonel ekibimizle ihtiyaçlarınıza en uygun gayrimenkul çözümlerini keşfedin.' 
                            : 'Discover the most suitable real estate solutions for your needs with our professional team.'; ?>
                    </p>
                    <a href="index.php#property-request" class="cta-btn">
                        <?php echo $lang == 'tr' ? 'Bize Ulaşın' : 'Contact Us'; ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
    
    <script>
        // Scroll animation
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            document.querySelectorAll('.pillar-card, .benefit-card, .intro-text, .intro-visual-card').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
