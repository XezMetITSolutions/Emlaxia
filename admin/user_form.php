<?php
require_once '../config.php';

// Admin check
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

// Role check
if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') {
    die($lang == 'tr' ? 'Bu sayfaya erişim yetkiniz yok.' : 'Access Denied.');
}

$id = $_GET['id'] ?? null;
$user = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch();

    if (!$user) {
        die("User not found");
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $id ? ($lang == 'tr' ? 'Kullanıcı Düzenle' : 'Edit User') : ($lang == 'tr' ? 'Yeni Kullanıcı' : 'New User'); ?>
        - Admin Panel
    </title>
    <base href="/">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <?php include 'includes/admin_header.php'; ?>

    <main>
        <div class="container">
            <h1>
                <?php echo $id ? ($lang == 'tr' ? 'Kullanıcı Düzenle' : 'Edit User') : ($lang == 'tr' ? 'Yeni Kullanıcı' : 'New User'); ?>
            </h1>

            <div class="form-container" style="max-width: 500px; margin: 0 auto;">
                <form action="admin/process_user.php" method="POST">
                    <?php if ($id): ?>
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <?php endif; ?>
                    <input type="hidden" name="action" value="save">

                    <div class="form-group">
                        <label>
                            <?php echo t('username'); ?>
                        </label>
                        <input type="text" name="username"
                            value="<?php echo $user ? htmlspecialchars($user['username']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>
                            <?php echo t('password'); ?>
                            <?php echo $id ? ($lang == 'tr' ? '(Değiştirmek istemiyorsanız boş bırakın)' : '(Leave blank if not changing)') : ''; ?>
                        </label>
                        <input type="password" name="password" <?php echo $id ? '' : 'required'; ?>>
                    </div>

                    <div class="form-group">
                        <label>
                            <?php echo $lang == 'tr' ? 'Rol' : 'Role'; ?>
                        </label>
                        <select name="role" class="form-control" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            <option value="admin" <?php echo ($user && ($user['role'] ?? '') == 'admin') ? 'selected' : ''; ?>>Admin (Full Access)</option>
                            <option value="junior_admin" <?php echo ($user && ($user['role'] ?? '') == 'junior_admin') ? 'selected' : ''; ?>>Junior Admin (Restricted)</option>
                        </select>
                    </div>

                    <div class="form-actions" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">
                            <?php echo t('save'); ?>
                        </button>
                        <a href="admin/users.php" class="btn btn-secondary">
                            <?php echo t('cancel'); ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="/assets/js/main.js"></script>
</body>

</html>