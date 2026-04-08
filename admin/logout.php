<?php
// Output buffering başlat
ob_start();

require_once '../config.php';

// Tüm session değişkenlerini temizle
$_SESSION = array();

// Session cookie'yi sil
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Session'ı yok et
session_destroy();

// Buffer'ı temizle ve yönlendir
ob_end_clean();
header('Location: login');
exit;
?>

