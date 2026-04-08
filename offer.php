<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $listing_id = $_POST['listing_id'] ?? 0;
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_email = $_POST['customer_email'] ?? '';
    $customer_phone = $_POST['customer_phone'] ?? '';
    $offer_amount = $_POST['offer_amount'] ?? 0;
    $message = $_POST['message'] ?? '';

    // Teklifi kaydet
    $stmt = $pdo->prepare("INSERT INTO offers (listing_id, customer_name, customer_email, customer_phone, offer_amount, message) VALUES (:listing_id, :customer_name, :customer_email, :customer_phone, :offer_amount, :message)");
    
    try {
        $stmt->execute([
            ':listing_id' => $listing_id,
            ':customer_name' => $customer_name,
            ':customer_email' => $customer_email,
            ':customer_phone' => $customer_phone,
            ':offer_amount' => $offer_amount,
            ':message' => $message
        ]);

        $_SESSION['success_message'] = t('offer_sent');
        header('Location: property.php?id=' . $listing_id);
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = $lang == 'tr' ? 'Teklif gönderilirken bir hata oluştu.' : 'An error occurred while sending the offer.';
        header('Location: property.php?id=' . $listing_id);
        exit;
    }
} else {
    header('Location: ilanlar.php');
    exit;
}
?>


