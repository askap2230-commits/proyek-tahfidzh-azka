<?php
// admin/api/admin_actions.php - Updated with registration management
header('Content-Type: application/json');
require_once '../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'get') {
        $type = $_GET['type'];
        $id = $_GET['id'];
        $table = $type === 'product' ? 'products' : ($type === 'service' ? 'services' : 'testimonials');
        $stmt = $db->prepare("SELECT * FROM $table WHERE id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        exit();
    }
    
    if ($action === 'get_registration') {
        $id = $_GET['id'];
        $stmt = $db->prepare("SELECT * FROM registrations WHERE id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        exit();
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    
    // Update registration status
    if ($action === 'update_status') {
        $id = $_POST['id'];
        $status = $_POST['status'];
        $notes = $_POST['notes'] ?? '';
        
        $stmt = $db->prepare("UPDATE registrations SET status = :status, notes = :notes WHERE id = :id");
        $result = $stmt->execute([
            ':status' => $status,
            ':notes' => $notes,
            ':id' => $id
        ]);
        
        echo json_encode(['success' => $result, 'message' => $result ? 'Status updated successfully' : 'Failed to update status']);
        exit();
    }
    
    // Delete registration
    if ($action === 'delete_registration') {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM registrations WHERE id = :id");
        $result = $stmt->execute([':id' => $id]);
        
        echo json_encode(['success' => $result, 'message' => $result ? 'Registration deleted successfully' : 'Failed to delete registration']);
        exit();
    }
    
    // Handle product/service/testimonial CRUD
    $type = $_POST['type'];
    $table = $type === 'product' ? 'products' : ($type === 'service' ? 'services' : 'testimonials');
    
    if ($action === 'add') {
        if ($type === 'product') {
            $query = "INSERT INTO products (image_url, title, badge, link_url, display_order) VALUES (:image_url, :title, :badge, :link_url, :display_order)";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                ':image_url' => $_POST['image_url'],
                ':title' => $_POST['title'],
                ':badge' => $_POST['badge'] ?? null,
                ':link_url' => $_POST['link_url'] ?? null,
                ':display_order' => $_POST['display_order'] ?? 0
            ]);
        } elseif ($type === 'service') {
            $query = "INSERT INTO services (icon_class, title, description, display_order) VALUES (:icon_class, :title, :description, :display_order)";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                ':icon_class' => $_POST['icon_class'],
                ':title' => $_POST['title'],
                ':description' => $_POST['description'],
                ':display_order' => $_POST['display_order'] ?? 0
            ]);
        } elseif ($type === 'testimonial') {
            $query = "INSERT INTO testimonials (author_name, author_avatar, content, rating, display_order) VALUES (:author_name, :author_avatar, :content, :rating, :display_order)";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                ':author_name' => $_POST['author_name'],
                ':author_avatar' => $_POST['author_avatar'] ?? null,
                ':content' => $_POST['content'],
                ':rating' => $_POST['rating'] ?? 5,
                ':display_order' => $_POST['display_order'] ?? 0
            ]);
        }
        echo json_encode(['success' => $result, 'message' => $result ? 'Added successfully' : 'Failed to add']);
        
    } elseif ($action === 'update') {
        $id = $_POST['id'];
        if ($type === 'product') {
            $query = "UPDATE products SET image_url = :image_url, title = :title, badge = :badge, link_url = :link_url, display_order = :display_order WHERE id = :id";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                ':image_url' => $_POST['image_url'],
                ':title' => $_POST['title'],
                ':badge' => $_POST['badge'] ?? null,
                ':link_url' => $_POST['link_url'] ?? null,
                ':display_order' => $_POST['display_order'] ?? 0,
                ':id' => $id
            ]);
        } elseif ($type === 'service') {
            $query = "UPDATE services SET icon_class = :icon_class, title = :title, description = :description, display_order = :display_order WHERE id = :id";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                ':icon_class' => $_POST['icon_class'],
                ':title' => $_POST['title'],
                ':description' => $_POST['description'],
                ':display_order' => $_POST['display_order'] ?? 0,
                ':id' => $id
            ]);
        } elseif ($type === 'testimonial') {
            $query = "UPDATE testimonials SET author_name = :author_name, author_avatar = :author_avatar, content = :content, rating = :rating, display_order = :display_order WHERE id = :id";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                ':author_name' => $_POST['author_name'],
                ':author_avatar' => $_POST['author_avatar'] ?? null,
                ':content' => $_POST['content'],
                ':rating' => $_POST['rating'] ?? 5,
                ':display_order' => $_POST['display_order'] ?? 0,
                ':id' => $id
            ]);
        }
        echo json_encode(['success' => $result, 'message' => $result ? 'Updated successfully' : 'Failed to update']);
        
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM $table WHERE id = :id");
        $result = $stmt->execute([':id' => $id]);
        echo json_encode(['success' => $result, 'message' => $result ? 'Deleted successfully' : 'Failed to delete']);
    }
}
?>