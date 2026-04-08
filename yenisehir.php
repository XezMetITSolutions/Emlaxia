<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'tr' ? 'Yenişehir Projesi' : 'Yenişehir Project'; ?> — Emlaxia.com</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .project-hero {
            background: linear-gradient(rgba(0, 35, 71, 0.8), rgba(0, 35, 71, 0.8)), url('assets/images/yenisehir-green-development.jpg');
            background-size: cover;
            background-position: center;
            padding: 150px 0;
            color: white;
            text-align: center;
        }
        .project-hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        .project-hero p {
            font-size: 1.4rem;
            opacity: 0.95;
            max-width: 900px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .detail-section {
            padding: 100px 0;
            background: white;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
            margin-bottom: 100px;
        }
        
        @media (max-width: 992px) {
            .detail-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            .detail-grid.reverse {
                display: flex;
                flex-direction: column-reverse;
            }
        }
        
        .detail-image {
            position: relative;
        }
        
        .detail-image img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            transition: transform 0.3s;
        }
        
        .detail-image:hover img {
            transform: translateY(-10px);
        }
        
        .detail-content h2 {
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 20px;
        }
        
        .detail-content h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 4px;
            background: var(--secondary-color);
        }
        
        .detail-content p {
            color: var(--gray-600);
            line-height: 1.9;
            margin-bottom: 25px;
            font-size: 1.1rem;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin-top: 40px;
        }
        
        .feature-list li {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            font-size: 1.2rem;
            color: var(--gray-800);
            font-weight: 500;
        }
        
        .feature-list i {
            color: var(--secondary-color);
            font-size: 1.5rem;
            background: rgba(253, 126, 20, 0.1);
            padding: 10px;
            border-radius: 50%;
        }
        
        .stats-bar {
            background: var(--primary-color);
            padding: 80px 0;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .stats-bar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('assets/images/pattern.png'); /* Optional texture */
            opacity: 0.05;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 40px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .stat-item h3 {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }
        
        .stat-item p {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        .vision-section {
            background: #f8fafc;
            padding: 100px 0;
            text-align: center;
        }
        
        .vision-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .vision-content h2 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 30px;
        }
        
        .vision-content p {
            font-size: 1.2rem;
            line-height: 1.8;
            color: var(--gray-600);
            margin-bottom: 40px;
        }
        
        .cta-box {
            background: white;
            padding: 60px;
            border-radius: 20px;
            text-align: center;
            margin-top: 60px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }
        
        .cta-box h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 2rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="project-hero">
            <div class="container">
                <h1><?php echo $lang == 'tr' ? 'Yenişehir: Geleceğin İstanbul\'u' : 'Yenişehir: Istanbul of the Future'; ?></h1>
                <p><?php echo $lang == 'tr' ? 'Modern şehirciliğin zirvesi, doğa ile teknolojinin mükemmel uyumu.' : 'The pinnacle of modern urbanism, the perfect harmony of nature and technology.'; ?></p>
            </div>
        </section>

        <section class="detail-section">
            <div class="container">
                <!-- Section 1: Strategic Importance -->
                <div class="detail-grid">
                    <div class="detail-image">
                        <img src="assets/images/yenisehir-canal-bridge.jpg" alt="Yenişehir Stratejik Konum">
                    </div>
                    <div class="detail-content">
                        <h2><?php echo $lang == 'tr' ? 'Stratejik Bir Merkez' : 'A Strategic Center'; ?></h2>
                        <p>
                            <?php echo $lang == 'tr' 
                                ? 'Yenişehir, İstanbul\'un kuzey aksında, küresel ticaretin yeni kalbi olmaya aday bir konumda yükseliyor. Kanal İstanbul projesinin çevresinde şekillenen bu devasa yaşam alanı, sadece bir konut projesi değil, aynı zamanda uluslararası bir ticaret ve lojistik üssüdür.' 
                                : 'Yenişehir is rising on the northern axis of Istanbul, in a position to become the new heart of global trade. This massive living space shaped around the Canal Istanbul project is not just a housing project, but also an international trade and logistics base.'; ?>
                        </p>
                        <p>
                            <?php echo $lang == 'tr'
                                ? 'İstanbul Havalimanı\'na olan yakınlığı ve Kuzey Marmara Otoyolu ile entegre ulaşım ağı, bölgeyi yatırımcılar için cazibe merkezi haline getiriyor. Burası, dünyanın her yerine sadece bir uçuş mesafesinde olan, global bir buluşma noktasıdır.'
                                : 'Its proximity to Istanbul Airport and its integrated transportation network with the Northern Marmara Highway make the region a center of attraction for investors. This is a global meeting point, just a flight away from anywhere in the world.'; ?>
                        </p>
                    </div>
                </div>

                <!-- Section 2: Life & Nature -->
                <div class="detail-grid reverse">
                    <div class="detail-content">
                        <h2><?php echo $lang == 'tr' ? 'Doğa ve Yaşamın Uyumu' : 'Harmony of Nature and Life'; ?></h2>
                        <p>
                            <?php echo $lang == 'tr'
                                ? 'Yenişehir, "Akıllı Şehir" konseptiyle tasarlanırken doğaya saygıyı merkeze almıştır. Geniş yeşil alanlar, ekolojik koridorlar ve sürdürülebilir mimari, burada yaşayanlara nefes alan bir şehir sunmaktadır.'
                                : 'While designing with the "Smart City" concept, Yenişehir has centered respect for nature. Large green areas, ecological corridors, and sustainable architecture offer a breathing city to those living here.'; ?>
                        </p>
                        <p>
                            <?php echo $lang == 'tr'
                                ? 'Yatay mimari anlayışıyla planlanan konut alanları, insan ölçeğinde, komşuluk ilişkilerini güçlendiren ve sosyal yaşamı destekleyen bir yapıdadır. Modern yaşamın tüm gereklilikleri, doğanın huzuruyla harmanlanmıştır.'
                                : 'Residential areas planned with a horizontal architecture approach are on a human scale, strengthening neighborhood relations and supporting social life. All the necessities of modern life are blended with the peace of nature.'; ?>
                        </p>
                    </div>
                    <div class="detail-image">
                        <img src="assets/images/yenisehir-smart-city.jpg" alt="Yenişehir Doğa">
                    </div>
                </div>
                
                <!-- Section 3: Investment Value -->
                <div class="detail-grid">
                    <div class="detail-image">
                        <img src="assets/images/yenisehir-investment-skyline.jpg" alt="Yenişehir Yatırım">
                    </div>
                    <div class="detail-content">
                        <h2><?php echo $lang == 'tr' ? 'Yüksek Yatırım Değeri' : 'High Investment Value'; ?></h2>
                        <p>
                            <?php echo $lang == 'tr' 
                                ? 'Gayrimenkul yatırımı, doğru lokasyon ve doğru zamanlama ile değer kazanır. Yenişehir, henüz gelişim aşamasında sunduğu fırsatlarla, yatırımcılarına bugünden geleceği satın alma imkanı tanıyor.' 
                                : 'Real estate investment gains value with the right location and right timing. With the opportunities it offers in the development stage, Yenişehir allows its investors to buy the future today.'; ?>
                        </p>
                        <ul class="feature-list">
                            <li><i class="fas fa-chart-line"></i> <?php echo $lang == 'tr' ? 'Hızla Artan Arsa Değerleri' : 'Rapidly Increasing Land Values'; ?></li>
                            <li><i class="fas fa-city"></i> <?php echo $lang == 'tr' ? 'Planlı Şehirleşme Garantisi' : 'Planned Urbanization Guarantee'; ?></li>
                            <li><i class="fas fa-globe"></i> <?php echo $lang == 'tr' ? 'Uluslararası İlgi Odağı' : 'International Focus of Interest'; ?></li>
                            <li><i class="fas fa-shield-alt"></i> <?php echo $lang == 'tr' ? 'Güvenli ve Hukuki Altyapı' : 'Secure and Legal Infrastructure'; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="stats-bar">
            <div class="container">
                <div class="stats-container">
                    <div class="stat-item">
                        <h3>2 Milyon</h3>
                        <p><?php echo $lang == 'tr' ? 'Hedeflenen Nüfus' : 'Target Population'; ?></p>
                    </div>
                    <div class="stat-item">
                        <h3>%60</h3>
                        <p><?php echo $lang == 'tr' ? 'Yeşil Alan Oranı' : 'Green Area Ratio'; ?></p>
                    </div>
                    <div class="stat-item">
                        <h3>15 dk</h3>
                        <p><?php echo $lang == 'tr' ? 'Havalimanına Ulaşım' : 'Transport to Airport'; ?></p>
                    </div>
                    <div class="stat-item">
                        <h3>∞</h3>
                        <p><?php echo $lang == 'tr' ? 'Yatırım Potansiyeli' : 'Investment Potential'; ?></p>
                    </div>
                </div>
            </div>
        </section>

        <section class="vision-section">
            <div class="container">
                <div class="vision-content">
                    <h2><?php echo $lang == 'tr' ? 'Geleceğe Yatırım Yapın' : 'Invest in the Future'; ?></h2>
                    <p>
                        <?php echo $lang == 'tr' 
                            ? 'Yenişehir projesi, sadece bir toprak parçası değil, çocuklarınıza bırakabileceğiniz en değerli mirastır. Bu büyük vizyonun bir parçası olmak ve sınırlı sayıdaki fırsatları değerlendirmek için geç kalmayın.' 
                            : 'The Yenişehir project is not just a piece of land, but the most valuable legacy you can leave to your children. Do not be late to be a part of this great vision and evaluate the limited opportunities.'; ?>
                    </p>
                    
                    <div class="cta-box">
                        <h2><?php echo $lang == 'tr' ? 'Detaylı Bilgi ve Sunum İçin' : 'For Detailed Information and Presentation'; ?></h2>
                        <p style="max-width: 600px; margin: 0 auto 30px; color: var(--gray-600);">
                            <?php echo $lang == 'tr' 
                                ? 'Uzman yatırım danışmanlarımız, size özel sunum ve bölge analizi için hazır. Hemen formu doldurun, sizi arayalım.' 
                                : 'Our expert investment consultants are ready for a special presentation and regional analysis for you. Fill out the form now, and we will call you.'; ?>
                        </p>
                        <a href="index.php#property-request" class="btn btn-primary btn-large"><?php echo $lang == 'tr' ? 'Yatırım Danışmanıyla Görüş' : 'Talk to Investment Consultant'; ?></a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
