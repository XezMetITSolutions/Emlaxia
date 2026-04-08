<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'tr' ? 'Çerez Politikası' : 'Cookie Policy'; ?> - Emlaxia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header {
            background: var(--primary-dark);
            padding: 40px 0;
            color: white;
            text-align: center;
        }
        .content-section {
            padding: 60px 0;
            background: white;
        }
        .legal-content h2 {
            color: var(--primary-color);
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .legal-content p {
            color: var(--gray-600);
            line-height: 1.8;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="page-header">
            <div class="container">
                <h1><?php echo $lang == 'tr' ? 'Çerez Politikası' : 'Cookie Policy'; ?></h1>
            </div>
        </section>

        <section class="content-section">
            <div class="container">
                <div class="legal-content">
                    <p><strong>Son Güncellenme:</strong> 12 Ocak 2026</p>
                    
                    <h2>1. Çerez Nedir?</h2>
                    <p>Çerezler (cookies), web sitelerini ziyaret ettiğinizde tarayıcınız aracılığıyla cihazınıza (bilgisayar, tablet, akıllı telefon vb.) kaydedilen küçük metin dosyalarıdır. Çerezler, web sitesinin düzgün çalışmasını sağlamak, kullanıcı deneyimini iyileştirmek ve site kullanımı hakkında bilgi toplamak amacıyla kullanılır.</p>
                    <p>Emlaxia olarak, platformumuzun etkin ve güvenli bir şekilde çalışmasını sağlamak, kullanıcı deneyimini geliştirmek ve size daha iyi hizmet sunabilmek için çerezler kullanmaktayız.</p>
                    
                    <h2>2. Çerez Türleri</h2>
                    <p>Platformumuzda kullanılan çerezler, işlevlerine ve kullanım sürelerine göre farklı kategorilere ayrılmaktadır:</p>
                    
                    <h3>2.1. Zorunlu Çerezler</h3>
                    <p>Bu çerezler, web sitemizin temel işlevlerini yerine getirmesi için gereklidir ve devre dışı bırakılamazlar. Güvenli oturum açma, form gönderimi ve tercih ayarlarınızın hatırlanması gibi temel işlevleri sağlarlar.</p>
                    <ul>
                        <li>Oturum yönetimi çerezleri</li>
                        <li>Güvenlik çerezleri</li>
                        <li>Yük dengeleme çerezleri</li>
                    </ul>
                    
                    <h3>2.2. Fonksiyonel Çerezler</h3>
                    <p>Bu çerezler, web sitesinin gelişmiş özelliklerini ve kişiselleştirme seçeneklerini sağlar. Dil tercihiniz, bölge ayarlarınız ve önceki aramalarınız gibi bilgileri hatırlar.</p>
                    <ul>
                        <li>Dil ve bölge tercihleri</li>
                        <li>Arama filtreleri ve sıralama tercihleri</li>
                        <li>Favori ilanlar ve kayıtlı aramalar</li>
                        <li>Harita görünüm tercihleri</li>
                    </ul>
                    
                    <h3>2.3. Performans ve Analitik Çerezler</h3>
                    <p>Bu çerezler, web sitemizin nasıl kullanıldığına dair bilgi toplar. Hangi sayfaların ziyaret edildiği, ne kadar süre kalındığı ve hangi bağlantılara tıklandığı gibi anonim istatistikler sağlar. Bu bilgiler, sitemizi iyileştirmemize yardımcı olur.</p>
                    <ul>
                        <li>Google Analytics çerezleri</li>
                        <li>Sayfa görüntüleme istatistikleri</li>
                        <li>Kullanıcı davranış analizi</li>
                        <li>Hata raporlama çerezleri</li>
                    </ul>
                    
                    <h3>2.4. Hedefleme ve Reklam Çerezleri</h3>
                    <p>Bu çerezler, size ilgi alanlarınıza uygun reklamlar göstermek için kullanılır. Ayrıca, reklam kampanyalarının etkinliğini ölçmemize yardımcı olur. Bu çerezler, onayınız olmadan kullanılmaz.</p>
                    <ul>
                        <li>Kişiselleştirilmiş reklam çerezleri</li>
                        <li>Yeniden hedefleme çerezleri</li>
                        <li>Sosyal medya entegrasyon çerezleri</li>
                        <li>Reklam performans ölçüm çerezleri</li>
                    </ul>
                    
                    <h2>3. Kullanılan Çerezlerin Amaçları</h2>
                    <p>Emlaxia platformunda çerezler aşağıdaki amaçlarla kullanılmaktadır:</p>
                    <ul>
                        <li><strong>Güvenlik:</strong> Hesabınızın güvenliğini sağlamak ve yetkisiz erişimi önlemek</li>
                        <li><strong>Oturum Yönetimi:</strong> Giriş durumunuzu korumak ve platformda gezinmenizi kolaylaştırmak</li>
                        <li><strong>Tercih Yönetimi:</strong> Dil, para birimi ve görünüm tercihlerinizi hatırlamak</li>
                        <li><strong>Arama Geçmişi:</strong> Önceki aramalarınızı ve filtrelerinizi kaydetmek</li>
                        <li><strong>Performans İyileştirme:</strong> Site hızını ve performansını optimize etmek</li>
                        <li><strong>Analiz:</strong> Site kullanımını analiz ederek kullanıcı deneyimini iyileştirmek</li>
                        <li><strong>Kişiselleştirme:</strong> Size özel içerik ve öneriler sunmak</li>
                        <li><strong>Pazarlama:</strong> İlgi alanlarınıza uygun reklamlar göstermek (onayınız dahilinde)</li>
                    </ul>
                    
                    <h2>4. Üçüncü Taraf Çerezleri</h2>
                    <p>Platformumuz, aşağıdaki üçüncü taraf hizmetlerden çerezler kullanabilir:</p>
                    <ul>
                        <li><strong>Google Analytics:</strong> Web sitesi trafiği ve kullanıcı davranışı analizi</li>
                        <li><strong>Google Maps:</strong> Harita ve konum hizmetleri</li>
                        <li><strong>Facebook Pixel:</strong> Reklam kampanyaları ve dönüşüm takibi</li>
                        <li><strong>YouTube:</strong> Video içerik gösterimi</li>
                        <li><strong>Ödeme Sağlayıcıları:</strong> Güvenli ödeme işlemleri</li>
                    </ul>
                    <p>Bu üçüncü taraf hizmetlerin kendi gizlilik politikaları ve çerez kullanım koşulları bulunmaktadır.</p>
                    
                    <h2>5. Çerezleri Nasıl Yönetebilirsiniz?</h2>
                    <p>Çerezleri kontrol etmek ve yönetmek için aşağıdaki seçeneklere sahipsiniz:</p>
                    
                    <h3>5.1. Tarayıcı Ayarları</h3>
                    <p>Çoğu web tarayıcısı, çerezleri otomatik olarak kabul edecek şekilde ayarlanmıştır. Tarayıcı ayarlarınızdan:</p>
                    <ul>
                        <li>Tüm çerezleri engelleyebilir</li>
                        <li>Sadece üçüncü taraf çerezleri engelleyebilir</li>
                        <li>Mevcut çerezleri silebilir</li>
                        <li>Çerez kabul etmeden önce uyarı alabilirsiniz</li>
                    </ul>
                    
                    <h3>5.2. Tarayıcı Bazlı Çerez Yönetimi</h3>
                    <p><strong>Google Chrome:</strong> Ayarlar → Gizlilik ve güvenlik → Çerezler ve diğer site verileri<br>
                    <strong>Mozilla Firefox:</strong> Ayarlar → Gizlilik ve Güvenlik → Çerezler ve Site Verileri<br>
                    <strong>Safari:</strong> Tercihler → Gizlilik → Çerezleri Engelle<br>
                    <strong>Microsoft Edge:</strong> Ayarlar → Gizlilik, arama ve hizmetler → Çerezler</p>
                    
                    <h3>5.3. Çerez Tercih Merkezi</h3>
                    <p>Platformumuzda yer alan çerez tercih merkezinden, zorunlu çerezler dışındaki tüm çerez kategorileri için tercihlerinizi belirleyebilirsiniz.</p>
                    
                    <h2>6. Çerezleri Reddetmenin Sonuçları</h2>
                    <p>Çerezleri tamamen veya kısmen reddederseniz:</p>
                    <ul>
                        <li>Platformun bazı özellikleri düzgün çalışmayabilir</li>
                        <li>Tercihleriniz hatırlanmayabilir</li>
                        <li>Oturum açma işlemleri sorun yaşayabilir</li>
                        <li>Kişiselleştirilmiş içerik göremeyebilirsiniz</li>
                        <li>Site performansı etkilenebilir</li>
                    </ul>
                    
                    <h2>7. Çerez Politikası Güncellemeleri</h2>
                    <p>Bu Çerez Politikası, yasal düzenlemeler veya platform değişiklikleri doğrultusunda güncellenebilir. Önemli değişiklikler hakkında kullanıcılarımızı bilgilendireceğiz.</p>
                    
                    <h2>8. İletişim</h2>
                    <p>Çerez politikamız hakkında sorularınız için bizimle iletişime geçebilirsiniz:</p>
                    <p><strong>E-posta:</strong> privacy@emlaxia.com</p>
                    
                    <p style="margin-top: 30px; padding: 20px; background: #f8fafc; border-left: 4px solid var(--primary-color);">
                        <strong>Not:</strong> Platformumuzu kullanmaya devam ederek, bu Çerez Politikası'nda açıklanan çerez kullanımını kabul etmiş olursunuz. Detaylı bilgi için <a href="privacy.php">Gizlilik Politikası</a> ve <a href="kvkk.php">KVKK Aydınlatma Metni</a> sayfalarımızı inceleyebilirsiniz.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
