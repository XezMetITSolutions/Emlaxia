<?php
/**
 * Migration: Move ALL listings to Emlaxia user account
 */
require_once '../config.php';

// Admin check
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    die('Unauthorized - Please login to admin panel first.');
}

try {
    // 1. Find Emlaxia user ID
    $stmt = $pdo->prepare("SELECT id, username, full_name FROM users WHERE username = 'Emlaxia' OR email = 'info@emlaxia.com' LIMIT 1");
    $stmt->execute();
    $emlaxia = $stmt->fetch();

    if (!$emlaxia) {
        die("<h1>Error: Emlaxia user not found</h1><p>Please ensure a user with username 'Emlaxia' exists.</p>");
    }

    $targetId = $emlaxia['id'];
    $adminId = $_SESSION['admin_id'];

    // 2. Perform migration
    // user_id -> Emlaxia User ID (from users table)
    // created_by -> Active Admin ID (from admins table) - to satisfy foreign key constraints
    $sql = "UPDATE listings SET 
            user_id = :tid1, 
            user_type = 'emlakci', 
            ilan_sahibi_turu = 'Emlakçı', 
            created_by = :aid1,
            approval_status = 'approved'
            WHERE user_id != :tid2 OR user_id IS NULL";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tid1' => $targetId,
        ':aid1' => $adminId,
        ':tid2' => $targetId
    ]);
    $affectedListings = $stmt->rowCount();

    // 3. Optional: Migrate other entities if they are linked to old users but should now be "received" by Emlaxia
    // Only listings ownership is changed here as per common request

    echo "<!DOCTYPE html>
    <html lang='tr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Emlaxia Merkezi Yönetim Migration</title>
        <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
        <style>
            :root {
                --primary: #0F123D;
                --accent: #D3AF37;
                --success: #10b981;
                --bg: #f8fafc;
            }
            body { 
                font-family: 'Inter', sans-serif; 
                padding: 0; 
                margin: 0; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                min-height: 100vh;
                background-color: var(--bg);
            }
            .card { 
                max-width: 550px; 
                width: 90%;
                background: white; 
                padding: 3rem; 
                border-radius: 2.5rem; 
                box-shadow: 0 25px 50px -12px rgba(15, 18, 61, 0.15); 
                border: 1px solid rgba(255, 255, 255, 0.3);
                text-align: center;
                animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            }
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .icon-wrapper {
                width: 80px;
                height: 80px;
                background: #ecfdf5;
                color: var(--success);
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                margin: 0 auto 1.5rem;
                font-size: 2.5rem;
            }
            h1 { 
                color: var(--primary); 
                margin: 0 0 1rem; 
                font-weight: 800; 
                font-size: 1.75rem;
            }
            p {
                color: #64748b;
                line-height: 1.6;
                margin-bottom: 2rem;
            }
            .stats-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 1rem;
                margin-bottom: 2.5rem;
            }
            .stat-card {
                background: #f1f5f9;
                padding: 1.5rem;
                border-radius: 1.5rem;
                transition: transform 0.2s;
            }
            .stat-value {
                display: block;
                font-size: 2rem;
                font-weight: 800;
                color: #2563eb;
            }
            .stat-label {
                font-size: 0.85rem;
                color: #64748b;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .btn { 
                display: inline-flex; 
                align-items: center;
                gap: 0.75rem;
                padding: 1rem 2rem; 
                background: var(--primary); 
                color: white; 
                text-decoration: none; 
                border-radius: 1rem; 
                font-weight: 700; 
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 10px 15px -3px rgba(15, 18, 61, 0.25);
            }
            .btn:hover { 
                transform: translateY(-3px); 
                box-shadow: 0 20px 25px -5px rgba(15, 18, 61, 0.3);
                filter: brightness(1.1);
            }
            .info-text {
                margin-top: 1.5rem;
                font-size: 0.8rem;
                color: #94a3b8;
            }
        </style>
    </head>
    <body>
        <div class='card'>
            <div class='icon-wrapper'>
                <i class='fas fa-check-circle'></i>
            </div>
            <h1>Konsolidasyon Tamamlandı</h1>
            <p>Sistemdeki bütün ilanlar merkezi <strong>Emlaxia</strong> hesabı altına başarıyla toplandı.</p>
            
            <div class='stats-grid'>
                <div class='stat-card'>
                    <span class='stat-value'>$affectedListings</span>
                    <span class='stat-label'>Taşınan İlan Sayısı</span>
                </div>
            </div>

            <a href='ilanlar.php' class='btn'>
                <i class='fas fa-th-list'></i> İlanları Yönet
            </a>

            <div class='info-text'>
                <i class='fas fa-info-circle'></i> Bu işlem geri alınamaz. Bütün ilanlar artık Emlaxia (ID $targetId) adına yayınlanmaktadır.
            </div>
        </div>
    </body>
    </html>";

} catch (PDOException $e) {
    die("<div style='font-family:sans-serif; padding:50px; text-align:center;'>
            <h1 style='color:#ef4444;'>❌ Migration Hatası</h1>
            <p style='color:#64748b;'>" . htmlspecialchars($e->getMessage()) . "</p>
            <a href='dashboard.php' style='color:#2563eb; text-decoration:none; font-weight:bold;'>Geri Dön</a>
         </div>");
}
