<?php
/**
 * AJAX endpoint for location data
 * Returns cities, districts, or neighborhoods based on request
 */
require_once '../config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_cities':
            // Get all cities
            $stmt = $pdo->query("SELECT id, il_adi FROM iller ORDER BY il_adi");
            $cities = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $cities]);
            break;
            
        case 'get_districts':
            // Get districts for a specific city
            $city_id = $_GET['city_id'] ?? 0;
            if ($city_id) {
                $stmt = $pdo->prepare("SELECT id, ilce_adi FROM ilceler WHERE il_id = :city_id ORDER BY ilce_adi");
                $stmt->execute([':city_id' => $city_id]);
                $districts = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $districts]);
            } else {
                echo json_encode(['success' => false, 'message' => 'City ID required']);
            }
            break;
            
        case 'get_neighborhoods':
            // Get neighborhoods for a specific district
            $district_id = $_GET['district_id'] ?? 0;
            if ($district_id) {
                $stmt = $pdo->prepare("SELECT id, mahalle_adi FROM mahalleler WHERE ilce_id = :district_id ORDER BY mahalle_adi LIMIT 1000");
                $stmt->execute([':district_id' => $district_id]);
                $neighborhoods = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $neighborhoods]);
            } else {
                echo json_encode(['success' => false, 'message' => 'District ID required']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
