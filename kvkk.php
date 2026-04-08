<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'tr' ? 'KVKK Aydınlatma Metni' : 'KVKK Text'; ?> - Emlaxia</title>
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
                <h1><?php echo $lang == 'tr' ? 'KVKK Aydınlatma Metni' : 'KVKK Text'; ?></h1>
            </div>
        </section>

        <section class="content-section">
            <div class="container">
                <div class="legal-content">
                    <p><strong>Son Güncellenme:</strong> 12 Ocak 2026</p>
                    
                    <h2>1. Veri Sorumlusu</h2>
                    <p>6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") uyarınca, kişisel verileriniz; veri sorumlusu olarak Emlaxia tarafından aşağıda açıklanan kapsamda işlenebilecektir.</p>
                    <p><strong>Veri Sorumlusu:</strong> Emlaxia<br>
                    <strong>E-posta:</strong> kvkk@emlaxia.com</p>
                    
                    <h2>2. Kişisel Verilerin İşlenme Amaçları</h2>
                    <p>Kişisel verileriniz, KVKK'nın 5. ve 6. maddelerinde belirtilen kişisel veri işleme şartları dahilinde aşağıdaki amaçlarla işlenebilmektedir:</p>
                    <ul>
                        <li>Gayrimenkul alım-satım ve kiralama hizmetlerinin sunulması</li>
                        <li>Müşteri ilişkileri yönetimi ve müşteri memnuniyetinin sağlanması</li>
                        <li>Gayrimenkul danışmanlık hizmetlerinin verilmesi</li>
                        <li>İlan sahipleri ile potansiyel alıcı/kiracıların eşleştirilmesi</li>
                        <li>Sözleşme süreçlerinin yürütülmesi</li>
                        <li>Yasal yükümlülüklerin yerine getirilmesi</li>
                        <li>Finans ve muhasebe işlemlerinin gerçekleştirilmesi</li>
                        <li>İletişim faaliyetlerinin yürütülmesi</li>
                        <li>Pazarlama ve tanıtım faaliyetlerinin gerçekleştirilmesi (açık rızanız dahilinde)</li>
                        <li>Platform güvenliğinin sağlanması ve dolandırıcılığın önlenmesi</li>
                        <li>Hukuki uyuşmazlıkların yönetimi</li>
                    </ul>
                    
                    <h2>3. İşlenen Kişisel Veri Kategorileri</h2>
                    <p>Platform kullanımınız kapsamında işlenen kişisel veri kategorileri:</p>
                    <ul>
                        <li><strong>Kimlik Bilgileri:</strong> Ad, soyad, T.C. kimlik numarası, doğum tarihi</li>
                        <li><strong>İletişim Bilgileri:</strong> Telefon numarası, e-posta adresi, adres bilgileri</li>
                        <li><strong>Müşteri İşlem Bilgileri:</strong> Arama kriterleri, favori ilanlar, görüntüleme geçmişi, teklif bilgileri</li>
                        <li><strong>Finansal Bilgiler:</strong> Bütçe tercihleri, ödeme bilgileri, fatura bilgileri</li>
                        <li><strong>İşlem Güvenliği Bilgileri:</strong> IP adresi, çerez kayıtları, log kayıtları</li>
                        <li><strong>Görsel ve İşitsel Kayıtlar:</strong> Profil fotoğrafı, gayrimenkul görselleri</li>
                        <li><strong>Konum Verileri:</strong> GPS konum bilgileri (izniniz dahilinde)</li>
                    </ul>
                    
                    <h2>4. Kişisel Verilerin Aktarılması</h2>
                    <p>Kişisel verileriniz, KVKK'nın 8. ve 9. maddelerinde belirtilen kişisel veri işleme şartları ve amaçları çerçevesinde:</p>
                    <ul>
                        <li>İş ortaklarımıza (gayrimenkul değerleme şirketleri, hukuk büroları vb.)</li>
                        <li>Hizmet sağlayıcılarımıza (bulut hizmet sağlayıcıları, ödeme kuruluşları vb.)</li>
                        <li>Yasal yükümlülüklerimiz gereği kamu kurum ve kuruluşlarına</li>
                        <li>İlan sahipleri ile potansiyel alıcı/kiracılar arasında iletişim sağlanması amacıyla</li>
                    </ul>
                    <p>aktarılabilmektedir.</p>
                    
                    <h2>5. Kişisel Veri Toplamanın Yöntemi ve Hukuki Sebebi</h2>
                    <p>Kişisel verileriniz, elektronik ortamda, web sitesi ve mobil uygulama üzerinden, otomatik veya otomatik olmayan yöntemlerle toplanmaktadır.</p>
                    <p><strong>Hukuki Sebepler:</strong></p>
                    <ul>
                        <li>Bir sözleşmenin kurulması veya ifasıyla doğrudan doğruya ilgili olması</li>
                        <li>Veri sorumlusunun hukuki yükümlülüğünü yerine getirebilmesi için zorunlu olması</li>
                        <li>İlgili kişinin temel hak ve özgürlüklerine zarar vermemek kaydıyla, veri sorumlusunun meşru menfaatleri için veri işlenmesinin zorunlu olması</li>
                        <li>Açık rızanızın bulunması</li>
                    </ul>
                    
                    <h2>6. Kişisel Veri Sahibinin Hakları</h2>
                    <p>KVKK'nın 11. maddesi uyarınca, kişisel veri sahipleri olarak aşağıdaki haklara sahipsiniz:</p>
                    <ul>
                        <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
                        <li>Kişisel verileriniz işlenmişse buna ilişkin bilgi talep etme</li>
                        <li>Kişisel verilerinizin işlenme amacını ve bunların amacına uygun kullanılıp kullanılmadığını öğrenme</li>
                        <li>Yurt içinde veya yurt dışında kişisel verilerinizin aktarıldığı üçüncü kişileri bilme</li>
                        <li>Kişisel verilerinizin eksik veya yanlış işlenmiş olması hâlinde bunların düzeltilmesini isteme</li>
                        <li>KVKK'nın 7. maddesinde öngörülen şartlar çerçevesinde kişisel verilerinizin silinmesini veya yok edilmesini isteme</li>
                        <li>Düzeltme, silme ve yok edilme işlemlerinin kişisel verilerin aktarıldığı üçüncü kişilere bildirilmesini isteme</li>
                        <li>İşlenen verilerin münhasıran otomatik sistemler vasıtasıyla analiz edilmesi suretiyle aleyhinize bir sonucun ortaya çıkmasına itiraz etme</li>
                        <li>Kişisel verilerinizin kanuna aykırı olarak işlenmesi sebebiyle zarara uğramanız hâlinde zararın giderilmesini talep etme</li>
                    </ul>
                    
                    <h2>7. Başvuru Yöntemi</h2>
                    <p>Yukarıda belirtilen haklarınızı kullanmak için başvurunuzu aşağıdaki yöntemlerle iletebilirsiniz:</p>
                    <ul>
                        <li><strong>Yazılı Başvuru:</strong> İstanbul, Türkiye adresine kimliğinizi tespit edici belgeler ile bizzat elden iletebilir veya noter kanalıyla gönderebilirsiniz</li>
                        <li><strong>Elektronik Başvuru:</strong> kvkk@emlaxia.com adresine güvenli elektronik imzalı veya mobil imzalı olarak iletebilirsiniz</li>
                        <li><strong>Kayıtlı Elektronik Posta (KEP):</strong> Şirketimizin KEP adresine güvenli elektronik imza, mobil imza ile veya başvuru sahibi tarafından Emlaxia sisteminde kayıtlı bulunan elektronik posta adresini kullanmak suretiyle iletebilirsiniz</li>
                    </ul>
                    <p>Başvurularınız, talebin niteliğine göre en kısa sürede ve en geç 30 (otuz) gün içinde ücretsiz olarak sonuçlandırılacaktır. İşlemin ayrıca bir maliyeti gerektirmesi hâlinde, Kişisel Verileri Koruma Kurulu tarafından belirlenen tarifedeki ücret alınabilir.</p>
                    
                    <h2>8. Veri Güvenliği</h2>
                    <p>Emlaxia olarak, kişisel verilerinizin güvenliğini sağlamak için gerekli teknik ve idari tedbirleri almaktayız. Kişisel verilerinizin hukuka aykırı olarak işlenmesini ve erişilmesini önlemek, muhafazasını sağlamak amacıyla uygun güvenlik düzeyini temin etmeye yönelik gerekli teknik ve idari tedbirler alınmaktadır.</p>
                    
                    <h2>9. İletişim</h2>
                    <p>KVKK kapsamındaki haklarınız ve kişisel verilerinizin işlenmesi hakkında detaylı bilgi için:</p>
                    <p><strong>E-posta:</strong> kvkk@emlaxia.com</p>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
