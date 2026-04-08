<?php
// Debug Page for Delete Listing Issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

echo "<h1>Debug Report for Delete Listing Issue</h1>";
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
    echo "<p style='color:red'>[FAIL] Admin check failed.</p>";
}

echo "<h2>3. Available Listings</h2>";
try {
    if (file_exists('../config.php')) {
        require_once '../config.php';

        $stmt = $pdo->query("SELECT id, title_tr, title_en, created_at FROM listings ORDER BY id DESC LIMIT 10");
        $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($listings) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Title (TR)</th><th>Title (EN)</th><th>Created</th></tr>";
            foreach ($listings as $listing) {
                echo "<tr>";
                echo "<td>" . $listing['id'] . "</td>";
                echo "<td>" . htmlspecialchars($listing['title_tr']) . "</td>";
                echo "<td>" . htmlspecialchars($listing['title_en']) . "</td>";
                echo "<td>" . $listing['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No listings found.</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

echo "<h2>4. Test Delete Operation</h2>";
echo "<p>Select a listing ID to test deletion (THIS WILL NOT ACTUALLY DELETE, just simulate the request):</p>";

if (isset($_POST['test_delete'])) {
    $test_id = $_POST['test_id'] ?? '';
    echo "<h3>Simulating DELETE request for ID: " . htmlspecialchars($test_id) . "</h3>";

    // Simulate what delete_listing.php does
    echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>";

    // Check 1: Session
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        echo "<p style='color:red'>FAIL: Session check failed (would return 401)</p>";
    } else {
        echo "<p style='color:green'>PASS: Session check passed</p>";
    }

    // Check 2: Role
    if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') {
        echo "<p style='color:red'>FAIL: Role check failed (would return 403)</p>";
    } else {
        echo "<p style='color:green'>PASS: Role check passed</p>";
    }

    // Check 3: ID validation
    if (!$test_id || !is_numeric($test_id)) {
        echo "<p style='color:red'>FAIL: Invalid ID (would return 400)</p>";
    } else {
        echo "<p style='color:green'>PASS: Valid ID</p>";

        // Check 4: Listing exists
        try {
            $stmt = $pdo->prepare("SELECT id, image1, image2, image3, image4, image5 FROM listings WHERE id = :id");
            $stmt->execute([':id' => $test_id]);
            $listing = $stmt->fetch();

            if (!$listing) {
                echo "<p style='color:red'>FAIL: Listing not found (would return 404)</p>";
            } else {
                echo "<p style='color:green'>PASS: Listing found</p>";
                echo "<p><strong>NOTE: Actual deletion is DISABLED in this debug page.</strong></p>";
                echo "<p>If this were real, the response would be:</p>";
                echo "<pre>" . json_encode(['success' => true, 'message' => 'İlan başarıyla silindi'], JSON_PRETTY_PRINT) . "</pre>";
            }
        } catch (Exception $e) {
            echo "<p style='color:red'>Database Error: " . $e->getMessage() . "</p>";
        }
    }
    echo "</div>";
}
?>

<form method="POST">
    <label>Listing ID to test: </label>
    <input type="number" name="test_id" required>
    <button type="submit" name="test_delete">Test Delete (Safe - No Actual Deletion)</button>
</form>

<hr>

<h2>5. Direct Test to delete_listing.php</h2>
<p>This will actually call delete_listing.php. <strong style="color:red">WARNING: This WILL delete the listing!</strong>
</p>

<form action="delete_listing.php" method="POST" target="_blank"
    onsubmit="return confirm('Are you SURE you want to delete this listing? This is REAL!');">
    <label>Listing ID to DELETE: </label>
    <input type="number" name="id" required>
    <button type="submit" style="background: red; color: white;">REAL DELETE (Opens in new tab)</button>
</form>