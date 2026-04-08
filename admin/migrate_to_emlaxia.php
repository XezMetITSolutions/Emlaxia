<?php
/**
 * Migration Script: Transfer all listings to Emlaxia (User ID 8)
 */
require_once '../config.php';

// Admin kontrolü - Güvenlik için admin girişi şart
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    die('<h1>Yetkisiz Erişim</h1><p>Bu işlemi gerçekleştirmek için admin girişi yapmalısınız.</p>');
}

try {
    // Önce kullanıcı 8'in varlığını kontrol edelim
    $userCheck = $pdo->prepare("SELECT id, name, user_type FROM users WHERE id = 8");
    $userCheck->execute();
    $emlaxia = $userCheck->fetch();

    if (!$emlaxia) {
        die("<h1>Hata: Kullanıcı 8 Bulunamadı</h1><p>Sistemde ID'si 8 olan bir kullanıcı mevcut değil. Lütfen önce Emlaxia hesabını oluşturun.</p>");
    }

    // Migration işlemi
    // user_id: Sahibi
    // user_type: Emlakçı olarak işaretle
    // ilan_sahibi_turu: 'Emlakçı' (Arayüzde gösterilen metin)
    // created_by: Oluşturan (Merkezi yönetim için 8 yapılıyor)
    // approval_status: Hepsi yayına alınsın diye 'approved' yapıyoruz
    
    $sql = "UPDATE listings SET 
            user_id = 8, 
            user_type = 'emlakci', 
            ilan_sahibi_turu = 'Emlakçı', 
            created_by = 8,
            approval_status = 'approved'
            WHERE user_id != 8 OR user_id IS NULL";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $affectedRows = $stmt->rowCount();

    echo "<!DOCTYPE html>
    <html lang='tr'>
    <head>
        <meta charset='UTF-8'>
        <title>Migration Tamamlandı</title>
        <style>
            body { font-family: sans-serif; padding: 50px; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 30px; border-radius: 10px; background: #f9f9f9; }
            h1 { color: #059669; }
            .stats { font-size: 1.2rem; margin: 20px 0; font-weight: bold; }
            .btn { display: inline-block; padding: 10px 20px; background: #059669; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>✅ Migration Başarıyla Tamamlandı</h1>
            <p>Bütün ilanlar <strong>Emlaxia (ID 8)</strong> hesabına aktarıldı.</p>
            <div class='stats'>Güncellenen İlan Sayısı: $affectedRows</div>
            <p>İşlem detayları:</p>
            <ul>
                <li>user_id = 8</li>
                <li>user_type = 'emlakci'</li>
                <li>ilan_sahibi_turu = 'Emlakçı'</li>
                <li>approval_status = 'approved'</li>
            </ul>
            <a href='dashboard.php' class='btn'>Dashboard'a Dön</a>
        </div>
    </body>
    </html>";

} catch (PDOException $e) {
    die("<h1>❌ Migration Hatası</h1><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
?>
