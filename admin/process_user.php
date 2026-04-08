<?php
require_once '../config.php';

// Admin check
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

// Role check
if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') {
    die($lang == 'tr' ? 'Bu işlem için yetkiniz yok.' : 'Permission denied.');
}

$action = $_REQUEST['action'] ?? '';

if ($action == 'save') {
    $id = $_POST['id'] ?? null;
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'junior_admin';

    try {
        if ($id) {
            // Update
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admins SET username = ?, password = ?, role = ? WHERE id = ?");
                $stmt->execute([$username, $hash, $role, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE admins SET username = ?, role = ? WHERE id = ?");
                $stmt->execute([$username, $role, $id]);
            }
            $_SESSION['success_message'] = $lang == 'tr' ? 'Kullanıcı güncellendi.' : 'User updated.';
        } else {
            // Create
            if (empty($password)) {
                die("Password required for new user");
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admins (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hash, $role]);
            $_SESSION['success_message'] = $lang == 'tr' ? 'Kullanıcı oluşturuldu.' : 'User created.';
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database Error: " . $e->getMessage();
    }

    header('Location: users.php');
    exit;

} elseif ($action == 'delete') {
    $id = $_GET['id'] ?? null;

    if ($id && $id != $_SESSION['admin_id']) { // Check self-delete
        try {
            $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success_message'] = $lang == 'tr' ? 'Kullanıcı silindi.' : 'User deleted.';
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Invalid ID or Self-Delete Attempt";
    }

    header('Location: users.php');
    exit;
}

header('Location: users.php');
?>