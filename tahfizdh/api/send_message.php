<?php
// api/send_message.php
header('Content-Type: application/json');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(strip_tags($_POST['name']));
    $email = htmlspecialchars(strip_tags($_POST['email']));
    $message = htmlspecialchars(strip_tags($_POST['message']));
    
    $errors = [];
    
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (empty($message)) $errors[] = 'Message is required';
    
    if (empty($errors)) {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)";
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>