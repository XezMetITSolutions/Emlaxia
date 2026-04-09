<?php
require_once '../config.php';
echo "Admin Logged In: " . (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] ? "EVET" : "HAYIR");
echo "<br>Admin ID: " . ($_SESSION['admin_id'] ?? 'Yok');
echo "<br>PHP Version: " . phpversion();
?>
