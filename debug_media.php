<?php
// debug_media.php
// Place this in your root directory (same level as index.php)

$uploadDir = __DIR__ . '/uploads';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Uploads Debugger</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .file-list { font-family: monospace; }
        .exists { color: green; font-weight: bold; }
        .missing { color: red; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>";

echo "<h1>Uploads Directory Debugger</h1>";
echo "<p><strong>Checking Directory:</strong> " . htmlspecialchars($uploadDir) . "</p>";

if (!is_dir($uploadDir)) {
    echo "<h2 class='missing'>CRITICAL: Directory does not exist!</h2>";
    exit;
} else {
    echo "<p class='exists'>Directory exists.</p>";
}

// Check specific files if requested
if (isset($_GET['check'])) {
    $fileToCheck = $_GET['check'];
    $fullPath = $uploadDir . '/' . $fileToCheck;
    echo "<h3>Checking specific file: " . htmlspecialchars($fileToCheck) . "</h3>";
    if (file_exists($fullPath)) {
        echo "<p class='exists'>FILE EXISTS!</p>";
        echo "Size: " . filesize($fullPath) . " bytes<br>";
        echo "Perms: " . substr(sprintf('%o', fileperms($fullPath)), -4);
    } else {
        echo "<p class='missing'>FILE NOT FOUND</p>";
    }
    echo "<hr>";
}

// List all files
$files = scandir($uploadDir);
$fileDetails = [];

foreach ($files as $f) {
    if ($f == '.' || $f == '..')
        continue;
    $path = $uploadDir . '/' . $f;
    $fileDetails[] = [
        'name' => $f,
        'time' => filemtime($path),
        'size' => filesize($path)
    ];
}

// Sort by time descending (newest first)
usort($fileDetails, function ($a, $b) {
    return $b['time'] - $a['time'];
});

echo "<h3>All Files in Uploads (" . count($fileDetails) . ")</h3>";
echo "<table>
    <thead>
        <tr>
            <th>Filename</th>
            <th>Modified Date</th>
            <th>Size (Bytes)</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>";

foreach ($fileDetails as $file) {
    $date = date("Y-m-d H:i:s", $file['time']);
    echo "<tr>
        <td>" . htmlspecialchars($file['name']) . "</td>
        <td>$date</td>
        <td>" . number_format($file['size']) . "</td>
        <td><a href='/uploads/" . htmlspecialchars($file['name']) . "' target='_blank'>Valid Link?</a></td>
    </tr>";
}

echo "</tbody></table>";
echo "</body></html>";
?>