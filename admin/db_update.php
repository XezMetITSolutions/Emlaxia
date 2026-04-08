<?php
// Database Update Script
require_once '../config.php';

header('Content-Type: text/html; charset=utf-8');
echo "<h1>Database Update: Add map_address</h1>";

try {
    // 1. Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM listings LIKE 'map_address'");
    $exists = $stmt->fetch();

    if ($exists) {
        echo "<p style='color:orange'>Column 'map_address' already exists.</p>";
    } else {
        // 2. Add the column
        // We'll add it after 'address' for logical grouping, or just at the end.
        $sql = "ALTER TABLE listings ADD COLUMN map_address VARCHAR(255) DEFAULT NULL AFTER address";
        $pdo->exec($sql);
        echo "<p style='color:green'>[SUCCESS] Column 'map_address' added successfully.</p>";
    }

    // 3. Verify
    $stmt = $pdo->query("SHOW COLUMNS FROM listings LIKE 'map_address'");
    $check = $stmt->fetch();
    if ($check) {
        echo "<p>Verification: Column exists.</p>";
    } else {
        echo "<p style='color:red'>Verification Failed!</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color:red'>Database Error: " . $e->getMessage() . "</p>";
}
?>
<p><a href="listing_form.php">Go back to Add Listing</a></p>