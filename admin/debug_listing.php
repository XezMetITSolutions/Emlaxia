<?php
require_once '../config.php';

// Admin yetkisi kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Yetkisiz erişim.");
}

$id = $_GET['id'] ?? 30;

try {
    $stmt = $pdo->prepare("SELECT * FROM listings WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $listing = $stmt->fetch();
} catch (Exception $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

if (!$listing) {
    die("İlan bulunamadı (ID: $id)");
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Debug İlan ID: <?php echo $id; ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1, h2 { color: #333; }
        pre { background: #eee; padding: 15px; overflow-x: auto; border-radius: 4px; font-size: 12px; }
        ul { list-style: none; padding: 0; }
        li { padding: 8px; margin: 4px 0; background: #f9f9f9; border-radius: 4px; }
        .exists { color: green; font-weight: bold; }
        .missing { color: red; font-weight: bold; }
        a { color: #0066cc; }
        .info { background: #e3f2fd; padding: 10px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 Debug İlan ID: <?php echo htmlspecialchars($id); ?></h1>
    
    <h2>📄 Veritabanı Kaydı:</h2>
    <pre><?php print_r($listing); ?></pre>
    
    <h2>🖼️ Medya Dosyaları Kontrolü:</h2>
    <ul>
    <?php
    $found_any = false;
    for ($i = 1; $i <= 20; $i++) {
        $field = 'image' . $i;
        if (isset($listing[$field]) && !empty($listing[$field])) {
            $val = $listing[$field];
            $path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $val;
            $exists = file_exists($path);
            $status = $exists ? '<span class="exists">VAR ✓</span>' : '<span class="missing">YOK ✗</span>';
            echo "<li><strong>$field:</strong> $val ($status) - <a href='/uploads/$val' target='_blank'>Görüntüle</a></li>";
            $found_any = true;
        }
    }
    
    if (isset($listing['video']) && !empty($listing['video'])) {
        $v_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $listing['video'];
        $v_exists = file_exists($v_path);
        $v_status = $v_exists ? '<span class="exists">VAR ✓</span>' : '<span class="missing">YOK ✗</span>';
        echo "<li><strong>video:</strong> " . htmlspecialchars($listing['video']) . " ($v_status) - <a href='/uploads/" . htmlspecialchars($listing['video']) . "' target='_blank'>Görüntüle</a></li>";
        $found_any = true;
    }
    
    if (!$found_any) {
        echo "<li><span class='missing'>Bu ilanda hiç medya dosyası tanımlanmamış!</span></li>";
    }
    ?>
    </ul>
    
    <h2>📂 Uploads Klasörü Kontrolü:</h2>
    <?php
    $uploads_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
    if (is_dir($uploads_path)) {
        echo "<div class='info'>✓ Uploads klasörü mevcut: $uploads_path</div>";
        $files = scandir($uploads_path);
        $file_count = count($files) - 2; // . ve .. hariç
        echo "<div class='info'>Klasördeki dosya sayısı: $file_count</div>";
    } else {
        echo "<div class='info' style='background:#ffebee;'>✗ Uploads klasörü bulunamadı!</div>";
    }
    ?>
    
    <h2>🔧 Sunucu Bilgileri:</h2>
    <div class="info">
        <strong>DOCUMENT_ROOT:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?><br>
        <strong>PHP_SELF:</strong> <?php echo $_SERVER['PHP_SELF']; ?><br>
        <strong>SERVER_SOFTWARE:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor'; ?>
    </div>
    
    <hr>
    <p><a href="listing_form.php?id=<?php echo $id; ?>">← İlan Formuna Git</a> | <a href="ilanlar.php">← İlanlar Listesine Git</a></p>
</div>
</body>
</html>
