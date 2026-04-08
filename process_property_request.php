<?php
/**
 * Özellik isteği formu işleme
 */
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Form verilerini al
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$property_type = $_POST['property_type'] ?? '';
$city = trim($_POST['city'] ?? '');
$district = trim($_POST['district'] ?? '');
$min_price = $_POST['min_price'] ?? null;
$max_price = $_POST['max_price'] ?? null;
$min_area = $_POST['min_area'] ?? null;
$max_area = $_POST['max_area'] ?? null;
$rooms = $_POST['rooms'] ?? '';
$bathrooms = $_POST['bathrooms'] ?? null;
$features = trim($_POST['features'] ?? '');
$additional_info = trim($_POST['additional_info'] ?? '');

// Validasyon
if (empty($name) || empty($email)) {
    $_SESSION['error_message'] = $lang == 'tr' 
        ? 'Lütfen adınızı ve e-posta adresinizi giriniz.' 
        : 'Please enter your name and email address.';
    header('Location: index.php#property-request');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = $lang == 'tr' 
        ? 'Geçerli bir e-posta adresi giriniz.' 
        : 'Please enter a valid email address.';
    header('Location: index.php#property-request');
    exit;
}

try {
    // Veritabanına kaydet
    $sql = "INSERT INTO property_requests (
        name, email, phone, property_type, city, district,
        min_price, max_price, min_area, max_area,
        rooms, bathrooms, features, additional_info, status
    ) VALUES (
        :name, :email, :phone, :property_type, :city, :district,
        :min_price, :max_price, :min_area, :max_area,
        :rooms, :bathrooms, :features, :additional_info, 'pending'
    )";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone ?: null,
        ':property_type' => $property_type ?: null,
        ':city' => $city ?: null,
        ':district' => $district ?: null,
        ':min_price' => $min_price ? (float)$min_price : null,
        ':max_price' => $max_price ? (float)$max_price : null,
        ':min_area' => $min_area ? (float)$min_area : null,
        ':max_area' => $max_area ? (float)$max_area : null,
        ':rooms' => $rooms ?: null,
        ':bathrooms' => $bathrooms ? (int)$bathrooms : null,
        ':features' => $features ?: null,
        ':additional_info' => $additional_info ?: null
    ]);
    
    $_SESSION['success_message'] = $lang == 'tr' 
        ? 'İsteğiniz başarıyla gönderildi! En kısa sürede size dönüş yapacağız.' 
        : 'Your request has been sent successfully! We will get back to you soon.';
    
} catch (PDOException $e) {
    $_SESSION['error_message'] = $lang == 'tr' 
        ? 'Bir hata oluştu. Lütfen tekrar deneyin.' 
        : 'An error occurred. Please try again.';
}

header('Location: index.php#property-request');
exit;
?>


