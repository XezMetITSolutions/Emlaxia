<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'tr' ? 'Kullanım Koşulları' : 'Terms of Use'; ?> - Emlaxia</title>
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
                <h1><?php echo $lang == 'tr' ? 'Kullanım Koşulları' : 'Terms of Use'; ?></h1>
            </div>
        </section>

        <section class="content-section">
            <div class="container">
                <div class="legal-content">
                    <p><strong>Son Güncellenme:</strong> 12 Ocak 2026</p>
                    
                    <h2>1. Genel Hükümler</h2>
                    <p>İşbu Kullanım Koşulları, Emlaxia web sitesi ve mobil uygulamalarının (bundan böyle "Platform" olarak anılacaktır) kullanımına ilişkin şartları belirlemektedir. Platformu kullanarak, bu koşulları kabul etmiş sayılırsınız.</p>
                    <p>Emlaxia, gayrimenkul alım-satım, kiralama ve danışmanlık hizmetleri sunan bir platformdur. Platform üzerinden sunulan tüm hizmetler, işbu kullanım koşullarına tabidir.</p>
                    
                    <h2>2. Hizmetlerin Kullanımı</h2>
                    <p>Platform üzerinden aşağıdaki hizmetlerden yararlanabilirsiniz:</p>
                    <ul>
                        <li>Gayrimenkul ilanlarını görüntüleme ve arama</li>
                        <li>İlan sahipleri ile iletişime geçme</li>
                        <li>Gayrimenkul danışmanlığı talep etme</li>
                        <li>Yatırım projeleri hakkında bilgi alma</li>
                        <li>Emlak değerleme hizmetlerinden faydalanma</li>
                    </ul>
                    <p>Platformu kullanırken, yürürlükteki tüm yasalara ve düzenlemelere uymayı kabul edersiniz.</p>
                    
                    <h2>3. Kullanıcı Sorumlulukları</h2>
                    <p>Platform kullanıcıları olarak:</p>
                    <ul>
                        <li>Doğru ve güncel bilgiler sağlamakla yükümlüsünüz</li>
                        <li>Yanıltıcı, sahte veya hukuka aykırı içerik paylaşmayacağınızı taahhüt edersiniz</li>
                        <li>Diğer kullanıcıların haklarına saygı göstereceksiniz</li>
                        <li>Platform güvenliğini tehlikeye atacak eylemlerden kaçınacaksınız</li>
                        <li>Hesap bilgilerinizin gizliliğini koruyacaksınız</li>
                    </ul>
                    
                    <h2>4. İlan Verme Koşulları</h2>
                    <p>Platform üzerinden ilan vermek isteyen kullanıcılar:</p>
                    <ul>
                        <li>İlanın konusu olan gayrimenkulün yasal sahibi veya yetkili temsilcisi olmalıdır</li>
                        <li>Gerçek, doğru ve güncel bilgiler paylaşmalıdır</li>
                        <li>Telif hakkı ihlali içermeyen görseller kullanmalıdır</li>
                        <li>İlan ücretlerini zamanında ödemelidir</li>
                    </ul>
                    <p>Emlaxia, kurallara aykırı ilanları önceden haber vermeksizin kaldırma hakkını saklı tutar.</p>
                    
                    <h2>5. Fikri Mülkiyet Hakları</h2>
                    <p>Platform üzerindeki tüm içerik, tasarım, logo, yazılım ve diğer materyaller Emlaxia'nın veya lisans verenlerin mülkiyetindedir. İzinsiz kullanım, kopyalama veya dağıtım yasaktır.</p>
                    
                    <h2>6. Sorumluluk Sınırlamaları</h2>
                    <p>Emlaxia, platform üzerinde yer alan ilanların doğruluğunu garanti etmez. İlan sahipleri, paylaştıkları bilgilerin doğruluğundan sorumludur.</p>
                    <p>Platform aracılığıyla gerçekleştirilen işlemlerden doğabilecek zararlardan Emlaxia sorumlu tutulamaz. Kullanıcılar, işlemlerini kendi sorumlulukları altında gerçekleştirirler.</p>
                    
                    <h2>7. Hizmetin Değiştirilmesi ve Sonlandırılması</h2>
                    <p>Emlaxia, platform üzerindeki hizmetleri önceden haber vermeksizin değiştirme, askıya alma veya sonlandırma hakkını saklı tutar.</p>
                    
                    <h2>8. Uygulanacak Hukuk ve Yetki</h2>
                    <p>İşbu Kullanım Koşulları, Türkiye Cumhuriyeti yasalarına tabidir. Platformun kullanımından doğabilecek uyuşmazlıklarda İstanbul Mahkemeleri ve İcra Daireleri yetkilidir.</p>
                    
                    <h2>9. İletişim</h2>
                    <p>Kullanım koşulları hakkında sorularınız için bizimle iletişime geçebilirsiniz:</p>
                    <p><strong>E-posta:</strong> info@emlaxia.com</p>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
