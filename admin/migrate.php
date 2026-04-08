<?php
require_once '../config.php';

// Admin kontrolü
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login');
    exit;
}

$migrations_run = [];
$errors = [];

// Migration 1: Add listing_type column
} catch (PDOException $e) {
    $errors[] = "✗ listing_type sütunu eklenirken hata: " . $e->getMessage();
}

// Migration 2: Add map_address column
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM listings LIKE 'map_address'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `listings` ADD `map_address` TEXT NULL AFTER `notes`");
        $migrations_run[] = "✓ map_address sütunu başarıyla eklendi";
    } else {
        $migrations_run[] = "✓ map_address sütunu zaten mevcut";
    }
} catch (PDOException $e) {
    $errors[] = "✗ map_address sütunu eklenirken hata: " . $e->getMessage();
}

// Add more migrations here as needed in the future
// Migration 2: ...
// Migration 3: ...

$has_errors = count($errors) > 0;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Migration - Emlaxia Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .migration-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .migration-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .migration-header h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .migration-status {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .migration-status.success {
            background: #d1fae5;
            border: 2px solid #10b981;
            color: #065f46;
        }
        
        .migration-status.error {
            background: #fee2e2;
            border: 2px solid #ef4444;
            color: #991b1b;
        }
        
        .migration-status ul {
            list-style: none;
            padding: 0;
            margin: 10px 0 0 0;
        }
        
        .migration-status li {
            padding: 8px 0;
            font-family: 'Courier New', monospace;
        }
        
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <div class="migration-container">
        <div class="migration-header">
            <h1>🔧 Database Migration</h1>
            <p>Veritabanı güncellemeleri yapılıyor...</p>
        </div>
        
        <?php if (!$has_errors): ?>
            <div class="migration-status success">
                <h3>✅ Migration Başarılı!</h3>
                <ul>
                    <?php foreach ($migrations_run as $migration): ?>
                        <li><?php echo $migration; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="migration-status error">
                <h3>❌ Migration Hatası!</h3>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php if (count($migrations_run) > 0): ?>
                    <h4 style="margin-top: 20px;">Başarılı olanlar:</h4>
                    <ul>
                        <?php foreach ($migrations_run as $migration): ?>
                            <li><?php echo $migration; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="btn-container">
            <a href="dashboard.php" class="btn btn-primary">Admin Panele Dön</a>
        </div>
    </div>
</body>
</html>
