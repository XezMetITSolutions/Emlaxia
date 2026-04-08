<?php
/**
 * Authentication Fonksiyonları
 * Emlakçı ve Bireysel kullanıcılar için ortak auth sistemi
 */

/**
 * Kullanıcı kaydı
 */
function registerUser($pdo, $data)
{
    // Validasyon
    if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['user_type'])) {
        return ['success' => false, 'message' => 'Tüm zorunlu alanları doldurunuz.'];
    }

    if (!in_array($data['user_type'], ['emlakci', 'bireysel', 'uye'])) {
        return ['success' => false, 'message' => 'Geçersiz kullanıcı tipi.'];
    }

    if (strlen($data['password']) < 6) {
        return ['success' => false, 'message' => 'Şifre en az 6 karakter olmalıdır.'];
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Geçerli bir e-posta adresi giriniz.'];
    }

    // Tekil kontrol - username
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute([':username' => $data['username']]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Bu kullanıcı adı zaten kullanılıyor.'];
    }

    // Tekil kontrol - email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $data['email']]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Bu e-posta adresi zaten kayıtlı.'];
    }

    // Kayıt
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password, phone, full_name, user_type, firma_adi, vergi_no, lisans_no, ofis_adresi, status)
            VALUES (:username, :email, :password, :phone, :full_name, :user_type, :firma_adi, :vergi_no, :lisans_no, :ofis_adresi, 'pending')";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username' => trim($data['username']),
        ':email' => trim($data['email']),
        ':password' => $hashedPassword,
        ':phone' => trim($data['phone'] ?? ''),
        ':full_name' => trim($data['full_name'] ?? ''),
        ':user_type' => $data['user_type'],
        ':firma_adi' => trim($data['firma_adi'] ?? ''),
        ':vergi_no' => trim($data['vergi_no'] ?? ''),
        ':lisans_no' => trim($data['lisans_no'] ?? ''),
        ':ofis_adresi' => trim($data['ofis_adresi'] ?? ''),
    ]);

    return ['success' => true, 'message' => 'Kayıt başarılı! Hesabınız onay bekliyor.', 'user_id' => $pdo->lastInsertId()];
}

/**
 * Kullanıcı girişi
 */
function loginUser($pdo, $username, $password)
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (LOWER(username) = LOWER(:uname) OR LOWER(email) = LOWER(:email_input))");
    $stmt->execute([':uname' => $username, ':email_input' => $username]);
    $user = $stmt->fetch();

    if (!$user) {
        return ['success' => false, 'message' => 'Kullanıcı adı veya şifre hatalı.'];
    }

    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Kullanıcı adı veya şifre hatalı.'];
    }

    if ($user['status'] === 'pending') {
        return ['success' => false, 'message' => 'Hesabınız henüz onaylanmadı. Lütfen onay için bekleyiniz.'];
    }

    if ($user['status'] === 'suspended') {
        return ['success' => false, 'message' => 'Hesabınız askıya alınmıştır. Lütfen yönetici ile iletişime geçiniz.'];
    }

    if ($user['status'] === 'rejected') {
        return ['success' => false, 'message' => 'Hesabınız reddedilmiştir. Lütfen yönetici ile iletişime geçiniz.'];
    }

    // Başarılı giriş - session ayarla
    $_SESSION['user_logged_in'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_username'] = $user['username'] ?? '';
    $_SESSION['user_type'] = $user['user_type'] ?? 'bireysel';
    $_SESSION['user_full_name'] = $user['full_name'] ?? '';
    $_SESSION['user_email'] = $user['email'] ?? '';

    // Son giriş tarihini güncelle (hata olsa bile girişe engel olma)
    try {
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        $stmt->execute([':id' => $user['id']]);
    } catch (Throwable $e) {
        // Hata loglanabilir ama kullanıcıyı engellememeliyiz
    }

    return ['success' => true, 'message' => 'Giriş başarılı!', 'user' => $user];
}

/**
 * Kullanıcı oturumunu kontrol et
 */
function checkUserAuth()
{
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

/**
 * Kullanıcı tipini kontrol et
 */
function checkUserType($type)
{
    return checkUserAuth() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === $type;
}

/**
 * Emlakçı yetkisi kontrolü - redirect ile
 */
function requireEmlakci()
{
    if (!checkUserType('emlakci')) {
        header('Location: /giris');
        exit;
    }
}

/**
 * Bireysel yetkisi kontrolü - redirect ile
 */
function requireBireysel()
{
    if (!checkUserType('bireysel')) {
        header('Location: /giris');
        exit;
    }
}

/**
 * Herhangi bir kullanıcı yetkisi kontrolü - redirect ile
 */
function requireUser()
{
    if (!checkUserAuth()) {
        header('Location: /giris');
        exit;
    }
}

/**
 * Kullanıcı çıkışı
 */
function logoutUser()
{
    unset($_SESSION['user_logged_in']);
    unset($_SESSION['user_id']);
    unset($_SESSION['user_username']);
    unset($_SESSION['user_type']);
    unset($_SESSION['user_full_name']);
    unset($_SESSION['user_email']);
}

/**
 * CSRF token oluştur
 */
function generateCsrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF token doğrula
 */
function validateCsrfToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>