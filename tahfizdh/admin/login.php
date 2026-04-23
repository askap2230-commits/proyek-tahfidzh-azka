<?php
// admin/login.php - VERSION FIXED
require_once '../config/database.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Please fill in both username and password!";
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        // Query untuk mendapatkan user
        $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['login_time'] = time();
                
                // Log activity
                try {
                    $log_query = "INSERT INTO admin_logs (admin_id, action, ip_address) VALUES (:admin_id, :action, :ip)";
                    $log_stmt = $db->prepare($log_query);
                    $log_stmt->execute([
                        ':admin_id' => $user['id'],
                        ':action' => 'Login',
                        ':ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                    ]);
                } catch (Exception $e) {
                    // Log error tapi tetap lanjutkan login
                    error_log("Failed to log admin activity: " . $e->getMessage());
                }
                
                // Redirect ke dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Invalid password! Please check your password.";
                // Debug: Hapus komentar untuk cek hash
                // error_log("Password hash in DB: " . $user['password']);
            }
        } else {
            $error = "Username not found! Please check your username.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Rumah Tahfidzh Hikmah</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Space Grotesk', sans-serif;
            background: linear-gradient(135deg, #0f1a24, #1a2a36);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text x="50" y="50" text-anchor="middle" font-size="80" fill="rgba(255,255,255,0.03)">۞</text></svg>') repeat;
            opacity: 0.1;
            pointer-events: none;
        }
        .login-container {
            background: rgba(15, 26, 36, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 50px;
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(146, 235, 52, 0.3);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .login-header i {
            font-size: 3rem;
            color: #92eb34;
            margin-bottom: 15px;
            display: inline-block;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .login-header h2 {
            background: linear-gradient(135deg, #92eb34, #11ad5d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2rem;
            font-weight: 700;
        }
        .login-header p {
            color: #aaa;
            margin-top: 10px;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #ccc;
            font-size: 0.9rem;
        }
        .input-group {
            position: relative;
        }
        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #92eb34;
            font-size: 1rem;
        }
        input {
            width: 100%;
            padding: 15px 20px 15px 45px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 50px;
            color: white;
            font-size: 1rem;
            transition: 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #92eb34;
            box-shadow: 0 0 20px rgba(146,235,52,0.2);
        }
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #92eb34, #11ad5d);
            border: none;
            border-radius: 50px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(146,235,52,0.4);
        }
        button:active {
            transform: translateY(0);
        }
        .error {
            background: rgba(255,68,68,0.15);
            border: 1px solid #ff4444;
            color: #ff8888;
            padding: 12px 20px;
            border-radius: 50px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 0.9rem;
        }
        .info-box {
            background: rgba(146,235,52,0.1);
            border: 1px solid rgba(146,235,52,0.3);
            padding: 15px;
            border-radius: 15px;
            margin-top: 25px;
            text-align: center;
        }
        .info-box p {
            color: #92eb34;
            font-size: 0.85rem;
            margin: 5px 0;
        }
        ::placeholder {
            color: rgba(255,255,255,0.4);
        }
        .demo-credentials {
            font-size: 0.8rem;
            color: #888;
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .demo-credentials span {
            color: #92eb34;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-quran"></i>
            <h2>Admin Login</h2>
            <p>Rumah Tahfidzh Hikmah</p>
        </div>
        
        <?php if($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Enter your username" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>
            <button type="submit">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="demo-credentials">
            <i class="fas fa-info-circle"></i> Demo Credentials:<br>
            <span>Username: admin</span> | <span>Password: admin123</span>
        </div>
        
        <div class="info-box">
            <p><i class="fas fa-shield-alt"></i> Secure Admin Access</p>
            <p style="font-size: 0.75rem; color:#666;">© 2024 Rumah Tahfidzh Hikmah</p>
        </div>
    </div>
    
    <script>
        // Clear error message when typing
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                const errorDiv = document.querySelector('.error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>