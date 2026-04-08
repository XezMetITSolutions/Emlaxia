<?php
// Debug Page for User 'Thebest57' Issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

echo "<h1>Debug Report for 'Thebest57' Listing Issue</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";

echo "<h2>1. Session Data</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>2. Authentication Check</h2>";
$admin_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
if ($admin_logged_in) {
    echo "<p style='color:green'>[PASS] Admin is logged in.</p>";
    echo "<p>ID: " . ($_SESSION['admin_id'] ?? 'N/A') . "</p>";
    echo "<p>Username: " . ($_SESSION['admin_username'] ?? 'N/A') . "</p>";
    echo "<p>Role: " . ($_SESSION['admin_role'] ?? 'N/A') . "</p>";
} else {
    echo "<p style='color:red'>[FAIL] Admin check failed. Session 'admin_logged_in' is NOT set or false.</p>";
}

echo "<h2>3. File Existence Check</h2>";
$files = [
    '../config.php',
    'process_listing.php',
    'listing_form.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color:green'>[PASS] File found: $file</p>";
    } else {
        echo "<p style='color:red'>[FAIL] File NOT found: $file</p>";
    }
}

echo "<h2>4. Database Connection & Schema</h2>";
try {
    if (file_exists('../config.php')) {
        require_once '../config.php';
        if (isset($pdo)) {
            echo "<p style='color:green'>[PASS] Database connection established via config.php.</p>";

            // Check 'listings' table columns
            echo "<h3>Listings Table Columns:</h3>";
            $stmt = $pdo->query("SHOW COLUMNS FROM listings");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<textarea style='width:100%; height:100px;'>" . implode(", ", $columns) . "</textarea>";

            // Check if 'Thebest57' exists
            echo "<h3>User 'Thebest57' Check:</h3>";
            $checkUser = $pdo->prepare("SELECT * FROM admins WHERE username = 'Thebest57'");
            $checkUser->execute();
            $user = $checkUser->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                echo "<p style='color:green'>[FOUND] User 'Thebest57' exists in 'admins' table.</p>";
                echo "<pre>" . print_r($user, true) . "</pre>";
            } else {
                echo "<p style='color:orange'>[INFO] User 'Thebest57' NOT found in 'admins' table. Checking 'users' table (if exists)...</p>";
                try {
                    $checkUser2 = $pdo->prepare("SELECT * FROM users WHERE username = 'Thebest57'");
                    $checkUser2->execute();
                    $user2 = $checkUser2->fetch(PDO::FETCH_ASSOC);
                    if ($user2) {
                        echo "<p style='color:green'>[FOUND] User 'Thebest57' exists in 'users' table.</p>";
                        echo "<pre>" . print_r($user2, true) . "</pre>";
                    } else {
                        echo "<p style='color:red'>[FAIL] User 'Thebest57' not found in database.</p>";
                    }
                } catch (Exception $e) {
                    echo "<p>No 'users' table found or error: " . $e->getMessage() . "</p>";
                }
            }

        } else {
            echo "<p style='color:red'>[FAIL] \$pdo variable not set after including config.php.</p>";
        }
    } else {
        echo "<p style='color:red'>[FAIL] Cannot include config.php (file missing).</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>[ERROR] Database error: " . $e->getMessage() . "</p>";
}

echo "<h2>5. Request Method Test</h2>";
echo "<p>Current Request Method: " . $_SERVER['REQUEST_METHOD'] . "</p>";
?>
<form action="process_listing.php" method="POST" target="_blank">
    <h3>Manual POST Test to process_listing.php</h3>
    <p>Clicking this will send a POST request with minimal data to process_listing.php in a new tab. You should see JSON
        output.</p>
    <input type="hidden" name="title_tr" value="Debug Listing Title">
    <input type="hidden" name="price" value="1000">
    <input type="hidden" name="listing_type" value="satilik">
    <input type="hidden" name="property_type" value="daire">
    <button type="submit">Send Test POST</button>
</form>