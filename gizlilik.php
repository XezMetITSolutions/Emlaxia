<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'tr' ? 'Gizlilik Politikası' : 'Privacy Policy'; ?> - Emlaxia</title>
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
                <h1><?php echo $lang == 'tr' ? 'Gizlilik Politikası' : 'Privacy Policy'; ?></h1>
            </div>
        </section>

        <section class="content-section">
            <div class="container">
                <div class="legal-content">
                    <p><strong>Son Güncellenme:</strong> 12 Ocak 2026</p>
                    
                    <h2>1. Giriş</h2>
                    <p>Emlaxia olarak, kullanıcılarımızın gizliliğine büyük önem veriyoruz. Bu Gizlilik Politikası, kişisel verilerinizin nasıl toplandığını, kullanıldığını, saklandığını ve korunduğunu açıklamaktadır.</p>
                    <p>Platformumuzu kullanarak, bu gizlilik politikasında belirtilen uygulamaları kabul etmiş olursunuz.</p>
                    
                    <h2>2. Toplanan Veriler</h2>
                    <p>Platform kullanımınız sırasında aşağıdaki kişisel veriler toplanabilir:</p>
                    <ul>
                        <li><strong>Kimlik Bilgileri:</strong> Ad, soyad, T.C. kimlik numarası</li>
                        <li><strong>İletişim Bilgileri:</strong> E-posta adresi, telefon numarası, adres</li>
                        <li><strong>Finansal Bilgiler:</strong> Bütçe tercihleri, ödeme bilgileri</li>
                        <li><strong>Gayrimenkul Tercihleri:</strong> Arama kriterleri, favori ilanlar, görüntüleme geçmişi</li>
                        <li><strong>Teknik Veriler:</strong> IP adresi, tarayıcı türü, cihaz bilgileri, çerez verileri</li>
                        <li><strong>Konum Verileri:</strong> GPS konumu (izniniz dahilinde)</li>
                    </ul>
                    
                    <h2>3. Verilerin Kullanım Amaçları</h2>
                    <p>Toplanan kişisel verileriniz aşağıdaki amaçlarla kullanılır:</p>
                    <ul>
                        <li>Gayrimenkul arama ve eşleştirme hizmetleri sunmak</li>
                        <li>İlan sahipleri ile iletişim kurmak</li>
                        <li>Danışmanlık hizmetleri sağlamak</li>
                        <li>Platform güvenliğini sağlamak ve dolandırıcılığı önlemek</li>
                        <li>Kullanıcı deneyimini iyileştirmek ve kişiselleştirmek</li>
                        <li>Yasal yükümlülükleri yerine getirmek</li>
                        <li>Pazarlama ve bilgilendirme faaliyetleri (onayınız dahilinde)</li>
                    </ul>
                    
                    <h2>4. Veri Güvenliği</h2>
                    <p>Kişisel verilerinizin güvenliğini sağlamak için aşağıdaki önlemleri alıyoruz:</p>
                    <ul>
                        <li>SSL şifreleme teknolojisi kullanımı</li>
                        <li>Güvenli sunucu altyapısı</li>
                        <li>Düzenli güvenlik denetimleri</li>
                        <li>Yetkisiz erişime karşı koruma sistemleri</li>
                        <li>Personel eğitimi ve gizlilik taahhütleri</li>
                    </ul>
                    
                    <h2>5. Çerezler (Cookies)</h2>
                    <p>Platformumuz, kullanıcı deneyimini iyileştirmek için çerezler kullanmaktadır. Çerezler hakkında detaylı bilgi için <a href="cookies.php">Çerez Politikası</a> sayfamızı ziyaret edebilirsiniz.</p>
                    <p>Tarayıcı ayarlarınızdan çerezleri yönetebilir veya reddedebilirsiniz, ancak bu durumda bazı platform özelliklerinden faydalanamayabilirsiniz.</p>
                    
                    <h2>6. Verilerin Paylaşımı</h2>
                    <p>Kişisel verileriniz, aşağıdaki durumlar haricinde üçüncü şahıslarla paylaşılmaz:</p>
                    <ul>
                        <li>Açık rızanızın bulunması</li>
                        <li>Yasal zorunluluklar ve mahkeme kararları</li>
                        <li>Hizmet sağlayıcılarımız (ödeme işlemcileri, hosting sağlayıcıları) ile sınırlı paylaşım</li>
                        <li>İlan sahipleri ile iletişim kurabilmeniz için gerekli bilgiler</li>
                    </ul>
                    
                    <h2>7. Üçüncü Taraf Hizmetler</h2>
                    <p>Platformumuz, harita hizmetleri, analitik araçlar ve sosyal medya entegrasyonları gibi üçüncü taraf hizmetler kullanabilir. Bu hizmetlerin kendi gizlilik politikaları bulunmaktadır.</p>
                    
                    <h2>8. Veri Saklama Süresi</h2>
                    <p>Kişisel verileriniz, hizmet sunumu için gerekli olduğu sürece veya yasal saklama yükümlülüklerimiz gereği saklanır. Hesabınızı sildiğinizde, verileriniz yasal zorunluluklar dışında silinir.</p>
                    
                    <h2>9. Haklarınız</h2>
                    <p>KVKK kapsamında aşağıdaki haklara sahipsiniz:</p>
                    <ul>
                        <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
                        <li>İşlenmişse buna ilişkin bilgi talep etme</li>
                        <li>Verilerin işlenme amacını ve bunların amacına uygun kullanılıp kullanılmadığını öğrenme</li>
                        <li>Yurt içinde veya yurt dışında aktarıldığı üçüncü kişileri bilme</li>
                        <li>Verilerin eksik veya yanlış işlenmiş olması halinde düzeltilmesini isteme</li>
                        <li>Verilerin silinmesini veya yok edilmesini isteme</li>
                        <li>İşlenen verilerin münhasıran otomatik sistemler ile analiz edilmesi nedeniyle aleyhinize bir sonucun ortaya çıkmasına itiraz etme</li>
                    </ul>
                    
                    <h2>10. Çocukların Gizliliği</h2>
                    <p>Platformumuz 18 yaşından küçük kişilere yönelik değildir. Bilerek 18 yaşından küçük kişilerden veri toplamıyoruz.</p>
                    
                    <h2>11. Politika Değişiklikleri</h2>
                    <p>Bu Gizlilik Politikası zaman zaman güncellenebilir. Önemli değişiklikler hakkında kullanıcılarımızı bilgilendireceğiz.</p>
                    
                    <h2>12. İletişim</h2>
                    <p>Gizlilik politikamız veya kişisel verileriniz hakkında sorularınız için:</p>
                    <p><strong>E-posta:</strong> privacy@emlaxia.com</p>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
