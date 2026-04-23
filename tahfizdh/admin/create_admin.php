<?php
// admin/create_admin.php - Jalankan file ini sekali untuk membuat user admin
require_once '../config/database.php';

session_start();

// Proteksi - hanya bisa diakses dari localhost atau hapus file setelah digunakan
$database = new Database();
$db = $database->getConnection();

// Cek apakah sudah ada admin
$check = $db->query("SELECT COUNT(*) as total FROM users");
$result = $check->fetch(PDO::FETCH_ASSOC);

if ($result['total'] > 0) {
    echo "Admin already exists!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    exit();
}

// Buat user admin dengan password yang benar
$username = 'admin';
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$email = 'admin@pesantren.com';
$full_name = 'Admin Pesantren';
$role = 'admin';

$query = "INSERT INTO users (username, password, email, full_name, role) VALUES (:username, :password, :email, :full_name, :role)";
$stmt = $db->prepare($query);
$stmt->execute([
    ':username' => $username,
    ':password' => $hashed_password,
    ':email' => $email,
    ':full_name' => $full_name,
    ':role' => $role
]);

if ($stmt) {
    echo "Admin created successfully!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<br><a href='login.php'>Go to Login Page</a>";
} else {
    echo "Failed to create admin!";
}
?>