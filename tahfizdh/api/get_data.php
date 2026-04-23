<?php
// api/get_data.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch($action) {
    case 'get_services':
        $stmt = $db->prepare("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;
        
    case 'get_products':
        $stmt = $db->prepare("SELECT * FROM products WHERE is_active = 1 ORDER BY display_order ASC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;
        
    case 'get_testimonials':
        $stmt = $db->prepare("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;
        
    case 'get_settings':
        $stmt = $db->prepare("SELECT setting_key, setting_value FROM settings");
        $stmt->execute();
        $result = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[$row['setting_key']] = $row['setting_value'];
        }
        echo json_encode($result);
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>