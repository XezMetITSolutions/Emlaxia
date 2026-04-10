<?php
/**
 * Listing Debugger - Shows all columns and values for a listing
 * This page ensures no fields are missed in the SQL queries.
 */
require_once '../config.php';
require_once '../includes/auth.php';

// Auth check
if (!isAdmin() && !checkUserType('emlakci')) {
    die('Unauthorized');
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<h1>Please provide an ID (?id=XX)</h1>";
    // Show last 5 listings as examples
    $stmt = $pdo->query("SELECT id, title_tr FROM listings ORDER BY id DESC LIMIT 5");
    echo "<ul>";
    while($row = $stmt->fetch()) {
        echo "<li><a href='?id={$row['id']}'>ID: {$row['id']} - " . htmlspecialchars($row['title_tr']) . "</a></li>";
    }
    echo "</ul>";
    exit;
}

try {
    // 1. Get all column names from the database
    $q = $pdo->query("DESCRIBE listings");
    $columns = $q->fetchAll(PDO::FETCH_ASSOC);

    // 2. Fetch the listing with SELECT *
    $stmt = $pdo->prepare("SELECT * FROM listings WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        die("Listing $id not found.");
    }

    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Listing Full Data Check - ID: $id</title>
        <style>
            body { font-family: sans-serif; padding: 20px; line-height: 1.5; background: #f4f7f9; }
            table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
            th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
            th { background: #1e88e5; color: white; }
            tr:hover { background: #f1f5f9; }
            .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
            .null { color: #94a3b8; font-style: italic; }
            .empty { color: #f59e0b; }
            .h1 { margin-bottom: 20px; }
            .summary { margin-bottom: 20px; background: #e0f2fe; padding: 15px; border-radius: 8px; border: 1px solid #bae6fd; }
        </style>
    </head>
    <body>
        <h1>Listing Data Audit (ID: $id)</h1>
        <div class='summary'>
            <strong>Total Columns in DB:</strong> " . count($columns) . "<br>
            <strong>Title (TR):</strong> " . htmlspecialchars($listing['title_tr']) . "<br>
            <strong>Status:</strong> {$listing['status']}
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Column Name</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>";

    $i = 1;
    foreach ($columns as $col) {
        $name = $col['Field'];
        $type = $col['Type'];
        $val = $listing[$name];
        
        $status = "<span class='badge' style='background:#10b981; color:white;'>OK</span>";
        $displayVal = htmlspecialchars($val);

        if ($val === null) {
            $status = "<span class='badge' style='background:#f1f5f9; color:#64748b;'>NULL</span>";
            $displayVal = "<span class='null'>null</span>";
        } elseif ($val === '') {
            $status = "<span class='badge' style='background:#fffbeb; color:#d97706;'>EMPTY</span>";
            $displayVal = "<span class='empty'>'' (Empty String)</span>";
        }

        echo "<tr>
                <td>$i</td>
                <td><strong>$name</strong></td>
                <td><code>$type</code></td>
                <td>$displayVal</td>
                <td>$status</td>
              </tr>";
        $i++;
    }

    echo "</tbody>
        </table>
        
        <div style='margin-top: 30px; padding: 20px; background: #fff; border-radius: 8px;'>
            <h3>System Status Check</h3>
            <p>This page performs a <code>SELECT *</code> query to ensure that the database returns every single column defined in the schema.</p>
            <ul>
                <li>Missing fields in SQL queries (like UPDATE or INSERT) can be detected by comparing this list with the SQL code.</li>
                <li>Current photo count limit: 10</li>
                <li>Current video support: 1</li>
            </ul>
        </div>
    </body>
    </html>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
