<?php
/**
 * Admin - Emlakçı/Bireysel Kullanıcı Yönetimi
 */
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}
if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') {
    die('Access Denied.');
}

// Durum güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $uid = $_POST['user_id'] ?? 0;
    $action = $_POST['action'];

    if ($uid && in_array($action, ['activate', 'suspend', 'reject', 'delete', 'change_type'])) {
        if ($action === 'activate') {
            $pdo->prepare("UPDATE users SET status = 'active', email_verified = 1 WHERE id = :id")->execute([':id' => $uid]);
            $_SESSION['success_message'] = 'Kullanıcı aktifleştirildi ve e-postası doğrulandı.';
        } elseif ($action === 'suspend') {
            $pdo->prepare("UPDATE users SET status = 'suspended' WHERE id = :id")->execute([':id' => $uid]);
            $_SESSION['success_message'] = 'Kullanıcı askıya alındı.';
        } elseif ($action === 'reject') {
            $pdo->prepare("UPDATE users SET status = 'rejected' WHERE id = :id")->execute([':id' => $uid]);
            $_SESSION['success_message'] = 'Kullanıcı reddedildi.';
        } elseif ($action === 'delete') {
            try {
                $pdo->prepare("UPDATE listings SET user_id = NULL WHERE user_id = :id")->execute([':id' => $uid]);
            } catch (Exception $e) {
            }
            $pdo->prepare("DELETE FROM users WHERE id = :id")->execute([':id' => $uid]);
            $_SESSION['success_message'] = 'Kullanıcı silindi.';
        } elseif ($action === 'change_type') {
            $new_type = $_POST['new_type'] ?? '';
            if (in_array($new_type, ['emlakci', 'bireysel'])) {
                $pdo->prepare("UPDATE users SET user_type = :type WHERE id = :id")->execute([':type' => $new_type, ':id' => $uid]);
                $_SESSION['success_message'] = 'Kullanıcı tipi güncellendi.';
            }
        }
    }
    header('Location: manage_users.php');
    exit;
}

$filter = $_GET['filter'] ?? 'all';
$where = '';
if ($filter === 'emlakci')
    $where = "AND user_type = 'emlakci'";
elseif ($filter === 'bireysel')
    $where = "AND user_type = 'bireysel'";
elseif ($filter === 'bireysel')
    $where = "AND user_type = 'bireysel'";
elseif ($filter === 'pending')
    $where = "AND status = 'pending'";

$users = [];
$total = 0;
$pending = 0;
$emlakci_count = 0;
$bireysel_count = 0;
$bireysel_count = 0;

try {
    // Check if user_id column exists in listings table
    $has_user_id = false;
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM listings LIKE 'user_id'");
        $has_user_id = (bool) $chk->fetch();
    } catch (Exception $e) {
    }

    if ($has_user_id) {
        $stmt = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM listings WHERE user_id = u.id) as listing_count
                              FROM users u WHERE 1=1 $where ORDER BY u.created_at DESC");
    } else {
        $stmt = $pdo->query("SELECT u.*, 0 as listing_count
                              FROM users u WHERE 1=1 $where ORDER BY u.created_at DESC");
    }
    $users = $stmt->fetchAll();

    $total = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $pending = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
    $emlakci_count = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'emlakci'")->fetchColumn();
    $bireysel_count = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'bireysel'")->fetchColumn();
    $bireysel_count = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'bireysel'")->fetchColumn();
} catch (PDOException $e) {
    // users tablosu mevcut olmayabilir
    $error_message = 'Veritabanı hatası: ' . $e->getMessage() . '<br>Lütfen önce <a href="migrate_users.php">migration</a> çalıştırın.';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $lang === 'tr' ? 'Kullanıcı Yönetimi' : 'User Management'; ?> - Admin
    </title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f1f5f9;
            margin: 0;
        }

        .page-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1.5rem;
        }

        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            color: #64748b;
            background: white;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .filter-tab:hover {
            border-color: #1e88e5;
            color: #1e88e5;
        }

        .filter-tab.active {
            background: #1e88e5;
            color: white;
            border-color: #1e88e5;
        }

        .filter-count {
            background: rgba(0, 0, 0, 0.1);
            padding: 0.1rem 0.4rem;
            border-radius: 10px;
            font-size: 0.75rem;
            margin-left: 0.25rem;
        }

        .filter-tab.active .filter-count {
            background: rgba(255, 255, 255, 0.3);
        }

        .users-table {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8fafc;
            padding: 1rem;
            text-align: left;
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
            color: #374151;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #fafbfc;
        }

        .user-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
        }

        .badge-emlakci {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-bireysel {
            background: #dbeafe;
            color: #2563eb;
        }

        .badge-uye {
            background: #f1f5f9;
            color: #64748b;
        }

        .status-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
        }

        .status-active {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-suspended {
            background: #fef2f2;
            color: #dc2626;
        }

        .status-rejected {
            background: #f1f5f9;
            color: #64748b;
        }

        .action-btns {
            display: flex;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-activate {
            background: #dcfce7;
            color: #16a34a;
        }

        .btn-suspend {
            background: #fef3c7;
            color: #d97706;
        }

        .btn-del {
            background: #fef2f2;
            color: #dc2626;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #94a3b8;
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 1rem;
            }

            .users-table {
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/admin_header.php'; ?>

    <div class="page-container">
        <?php if (!empty($error_message)): ?>
            <div
                style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:1rem 1.5rem;border-radius:12px;margin-bottom:1.5rem;font-weight:500;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <h1 class="page-title">👥
            <?php echo $lang === 'tr' ? 'Kullanıcı Yönetimi' : 'User Management'; ?>
        </h1>

        <div class="filter-tabs">
            <a href="/admin/manage_users" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
                Tümü <span class="filter-count">
                    <?php echo $total; ?>
                </span>
            </a>
            <a href="/admin/manage_users?filter=pending"
                class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                ⏳ Onay Bekleyen <span class="filter-count">
                    <?php echo $pending; ?>
                </span>
            </a>
            <a href="/admin/manage_users?filter=emlakci"
                class="filter-tab <?php echo $filter === 'emlakci' ? 'active' : ''; ?>">
                🏢 Emlakçı <span class="filter-count">
                    <?php echo $emlakci_count; ?>
                </span>
            </a>
            <a href="/admin/manage_users?filter=bireysel"
                class="filter-tab <?php echo $filter === 'bireysel' ? 'active' : ''; ?>">
                👤 Bireysel <span class="filter-count">
                    <?php echo $bireysel_count; ?>
                </span>
            </a>
        </div>

        <div class="users-table">
            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <div style="font-size:3rem;margin-bottom:1rem;">👥</div>
                    <p>
                        <?php echo $lang === 'tr' ? 'Kullanıcı bulunamadı.' : 'No users found.'; ?>
                    </p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kullanıcı</th>
                            <th>E-posta</th>
                            <th>Tip</th>
                            <th>Firma</th>
                            <th>İlan</th>
                            <th>Durum</th>
                            <th>E-Posta Onay</th>
                            <th>Aktivasyon Linki</th>
                            <th>Kayıt</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <?php echo $u['id']; ?>
                                </td>
                                <td style="font-weight:600;">
                                    <?php echo htmlspecialchars($u['full_name'] ?: $u['username']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($u['email']); ?>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <input type="hidden" name="action" value="change_type">
                                        <select name="new_type" onchange="confirmTypeChange(this, '<?php echo $u['user_type']; ?>')" 
                                            style="padding: 4px 8px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.75rem; cursor: pointer; font-weight: 600; outline: none; transition: all 0.2s;
                                            background: <?php 
                                                echo $u['user_type'] === 'emlakci' ? '#dcfce7' : ($u['user_type'] === 'bireysel' ? '#dbeafe' : '#f1f5f9'); 
                                            ?>; 
                                            color: <?php 
                                                echo $u['user_type'] === 'emlakci' ? '#16a34a' : ($u['user_type'] === 'bireysel' ? '#2563eb' : '#64748b'); 
                                            ?>;">
                                            <option value="bireysel" <?php echo $u['user_type'] === 'bireysel' ? 'selected' : ''; ?>>👤 Bireysel</option>
                                            <option value="emlakci" <?php echo $u['user_type'] === 'emlakci' ? 'selected' : ''; ?>>🏢 Emlakçı</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($u['firma_adi'] ?? '-'); ?>
                                </td>
                                <td style="text-align:center;">
                                    <?php echo $u['listing_count']; ?>
                                </td>
                                <td><span class="status-badge status-<?php echo $u['status']; ?>">
                                        <?php echo ['pending' => 'Onay Bekliyor', 'active' => 'Panel Aktif', 'suspended' => 'Askıda', 'rejected' => 'Reddedildi'][$u['status']] ?? $u['status']; ?>
                                    </span></td>
                                <td>
                                    <span class="status-badge" style="background: <?php echo $u['email_verified'] ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo $u['email_verified'] ? '#16a34a' : '#991b1b'; ?>;">
                                        <?php echo $u['email_verified'] ? '✅ Doğrulandı' : '⏳ Bekliyor'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!$u['email_verified'] && !empty($u['activation_token'])): ?>
                                        <?php 
                                            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                                            $host = $_SERVER['HTTP_HOST'];
                                            $link = "$protocol://$host/verify.php?token=" . $u['activation_token'];
                                        ?>
                                        <div style="display:flex; gap:5px; align-items:center;">
                                            <a href="<?php echo $link; ?>" target="_blank" class="btn-sm" style="background:#e0f2fe; color:#0369a1; text-decoration:none;">Aç</a>
                                            <button onclick="copyToClipboard('<?php echo $link; ?>')" class="btn-sm" style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;">Kopyala</button>
                                        </div>
                                    <?php else: ?>
                                        <span style="color:#94a3b8;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo date('d.m.Y', strtotime($u['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <?php if ($u['status'] !== 'active'): ?>
                                            <form method="POST" style="display:inline;"><input type="hidden" name="user_id"
                                                    value="<?php echo $u['id']; ?>">
                                                <button name="action" value="activate" class="btn-sm btn-activate">✅ Aktif</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($u['status'] !== 'suspended'): ?>
                                            <form method="POST" style="display:inline;"><input type="hidden" name="user_id"
                                                    value="<?php echo $u['id']; ?>">
                                                <button name="action" value="suspend" class="btn-sm btn-suspend">⏸️ Askı</button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" style="display:inline;"
                                            onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <button name="action" value="delete" class="btn-sm btn-del">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function confirmTypeChange(select, currentType) {
            const types = {
                'uye': '👥 Normal Üye',
                'bireysel': '👤 Bireysel',
                'emlakci': '🏢 Emlakçı'
            };
            const newTypeLabel = types[select.value];
            
            Swal.fire({
                title: 'Emin misiniz?',
                text: `Kullanıcı tipini "${newTypeLabel}" olarak değiştirmek üzeresiniz.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1e88e5',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Evet, Değiştir',
                cancelButtonText: 'Vazgeç',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    select.form.submit();
                } else {
                    select.value = currentType;
                }
            });
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Link kopyalandı!',
                    showConfirmButton: false,
                    timer: 2000
                });
            }).catch(err => {
                console.error('Hata:', err);
            });
        }

        // Show success message if exists
        <?php if (isset($_SESSION['success_message'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Başarılı',
                text: '<?php echo $_SESSION['success_message']; ?>',
                timer: 3000,
                confirmButtonColor: '#1e88e5'
            });
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
    </script>
</body>

</html>