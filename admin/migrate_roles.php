<?php
require_once '../config.php';

try {
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM admins LIKE 'role'");
    $exists = $stmt->fetch();

    if (!$exists) {
        $pdo->exec("ALTER TABLE admins ADD COLUMN role VARCHAR(50) DEFAULT 'admin'");
        echo "Successfully added 'role' column to admins table.<br>";

        // Update existing admin to be 'admin' (just to be safe)
        $pdo->exec("UPDATE admins SET role = 'admin' WHERE role IS NULL");
        echo "Updated existing users to have 'admin' role.<br>";
    } else {
        echo "'role' column already exists.<br>";
    }

    echo "<a href='dashboard.php'>Go to Dashboard</a>";

} catch (PDOException $e) {
    die("Error during migration: " . $e->getMessage());
}
?>