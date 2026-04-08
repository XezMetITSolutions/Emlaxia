<?php
require_once '../config.php';

// Admin check
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

// Role check - Only Super Admin can see this page
if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') {
    die($lang == 'tr' ? 'Bu sayfaya erişim yetkiniz yok.' : 'Access Denied.');
}

// Fetch users
$stmt = $pdo->query("SELECT * FROM admins ORDER BY id ASC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'tr' ? 'Kullanıcı Yönetimi' : 'User Management'; ?> - Admin Panel</title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .role-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold; }
        .role-admin { background-color: #e7f3ff; color: #0056b3; }
        .role-junior_admin { background-color: #e6fffa; color: #047481; }
    </style>
</head>
<body>
    <?php include 'includes/admin_header.php'; ?>

    <main>
        <div class="container">
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1><?php echo $lang == 'tr' ? 'Kullanıcı Yönetimi' : 'User Management'; ?></h1>
                <a href="admin/user_form.php" class="btn btn-primary"><?php echo $lang == 'tr' ? 'Yeni Kullanıcı' : 'New User'; ?></a>
            </div>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?php echo t('username'); ?></th>
                            <th><?php echo $lang == 'tr' ? 'Rol' : 'Role'; ?></th>
                            <th><?php echo $lang == 'tr' ? 'Oluşturulma Tarihi' : 'Created At'; ?></th>
                            <th><?php echo $lang == 'tr' ? 'İşlemler' : 'Actions'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo $user['role'] ?? 'admin'; ?>">
                                        <?php echo ucfirst($user['role'] ?? 'admin'); ?>
                                    </span>
                                </td>
                                <td><?php echo isset($user['created_at']) ? date('d.m.Y', strtotime($user['created_at'])) : '-'; ?></td>
                                <td>
                                    <a href="admin/user_form.php?id=<?php echo $user['id']; ?>" class="btn btn-small btn-primary"><?php echo t('edit'); ?></a>
                                    <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                        <a href="admin/process_user.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('<?php echo $lang == 'tr' ? 'Bu kullanıcıyı silmek istediğinize emin misiniz?' : 'Are you sure you want to delete this user?'; ?>')"><?php echo t('delete'); ?></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="/assets/js/main.js"></script>
</body>
</html>
