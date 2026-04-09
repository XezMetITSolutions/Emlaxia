<?php
require_once dirname(__DIR__) . '/config.php';

echo "Starting Emlaxia user creation and migration...\n";

try {
    // 1. Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'Emlaxia'");
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        $userId = $user['id'];
        echo "User 'Emlaxia' already exists with ID: $userId\n";
    } else {
        // Create new user
        $password = 'Ibocan19905757';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, email, password, full_name, user_type, status, email_verified) 
                VALUES ('Emlaxia', 'info@emlaxia.com', :password, 'Emlaxia', 'emlakci', 'active', 1)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':password' => $hashedPassword]);
        
        $userId = $pdo->lastInsertId();
        echo "Created user 'Emlaxia' with ID: $userId\n";
    }

    // 2. Migrate ownerless listings
    $stmt = $pdo->prepare("UPDATE listings SET user_id = :userId, user_type = 'emlakci' WHERE user_id IS NULL");
    $stmt->execute([':userId' => $userId]);
    $affected = $stmt->rowCount();

    echo "Successfully migrated $affected listings to 'Emlaxia'.\n";
    echo "Done.\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
