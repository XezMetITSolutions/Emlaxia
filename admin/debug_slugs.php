<?php
require_once '../config.php';

// Admin yetkisi kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

function createSlug($str, $options = array()) {
    $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
    $defaults = array(
        'delimiter' => '-',
        'limit' => null,
        'lowercase' => true,
        'replacements' => array(),
        'transliterate' => true,
    );
    $options = array_merge($defaults, $options);
    $char_map = array(
        // Latin
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
        'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
        'ß' => 'ss',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
        'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
        'ÿ' => 'y',
        // Turkish
        'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
        'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
    );
    $str = str_replace(array_keys($char_map), $char_map, $str);
    $str = preg_replace('/[^\p{L}\p{N}]+/u', $options['delimiter'], $str);
    $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
    $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
    $str = trim($str, $options['delimiter']);
    return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}

$message = '';
if (isset($_POST['fix_slugs'])) {
    $stmt = $pdo->query("SELECT id, title_tr FROM listings");
    $listings = $stmt->fetchAll();
    $updated = 0;

    foreach ($listings as $listing) {
        $new_slug = createSlug($listing['title_tr']);
        
        // Benzersiz yap
        $original_slug = $new_slug;
        $counter = 1;
        while (true) {
            $check = $pdo->prepare("SELECT id FROM listings WHERE slug = ? AND id != ?");
            $check->execute([$new_slug, $listing['id']]);
            if (!$check->fetch()) break;
            $new_slug = $original_slug . '-' . $counter;
            $counter++;
        }

        $update = $pdo->prepare("UPDATE listings SET slug = ? WHERE id = ?");
        if ($update->execute([$new_slug, $listing['id']])) {
            $updated++;
        }
    }
    $message = "Başarılı! $updated ilan için slug oluşturuldu/güncellendi.";
}

$stmt = $pdo->query("SELECT id, title_tr, slug FROM listings ORDER BY id DESC");
$listings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Slug Debug Paneli</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #eee; }
        .btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .message { padding: 15px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Slug Debug & Fix Paneli</h1>
        
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <button type="submit" name="fix_slugs" class="btn">Tüm Slugları Yeniden Oluştur / Düzelt</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Başlık (TR)</th>
                    <th>Slug</th>
                    <th>Önizleme Linki</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listings as $l): ?>
                <tr>
                    <td><?php echo $l['id']; ?></td>
                    <td><?php echo htmlspecialchars($l['title_tr']); ?></td>
                    <td><code><?php echo htmlspecialchars($l['slug']); ?></code></td>
                    <td>
                        <?php if ($l['slug']): ?>
                            <a href="/ilan/<?php echo $l['slug']; ?>" target="_blank">Eski Link</a> | 
                            <a href="/ilanlar/satilik/villa/sehir/<?php echo $l['slug']; ?>" target="_blank">Yeni Link (Örnek)</a>
                        <?php else: ?>
                            Slug yok!
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p><a href="dashboard.php">← Dashboard'a Dön</a></p>
    </div>
</body>
</html>
