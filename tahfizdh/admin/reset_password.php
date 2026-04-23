<?php
// admin/reset_password.php - Untuk reset password jika lupa
require_once '../config/database.php';

session_start();

// Reset password
$username = 'admin';
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$database = new Database();
$db = $database->getConnection();

$query = "UPDATE users SET password = :password WHERE username = :username";
$stmt = $db->prepare($query);
$stmt->execute([
    ':password' => $hashed_password,
    ':username' => $username
]);

if ($stmt->rowCount() > 0) {
    echo "Password reset successfully!<br>";
    echo "Username: admin<br>";
    echo "New Password: admin123<br>";
    echo "<br><a href='login.php'>Go to Login Page</a>";
} else {
    echo "User not found! Please create admin first using create_admin.php";
    echo "<br><a href='create_admin.php'>Create Admin</a>";
}
?>