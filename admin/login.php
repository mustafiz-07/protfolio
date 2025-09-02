<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

// Check for remember me cookie
if (isset($_COOKIE['admin_remember']) && !isset($_SESSION['admin_logged_in'])) {
    $stored_hash = $_COOKIE['admin_remember'];
    $username = 'admin'; // Default username
    $expected_hash = hash('sha256', $username . 'remember_token_salt');
    
    if (hash_equals($expected_hash, $stored_hash)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['login_time'] = time();
        header('Location: dashboard.php');
        exit();
    }
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember_me = isset($_POST['remember_me']);
    
    // Simple authentication (you should use a database in production)
    $valid_username = 'admin';
    $valid_password = 'admin123'; // In production, use hashed passwords
    
    if ($username === $valid_username && $password === $valid_password) {
        // Start session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['login_time'] = time();
        
        // Set remember me cookie if checked
        if ($remember_me) {
            $cookie_hash = hash('sha256', $username . 'remember_token_salt');
            setcookie('admin_remember', $cookie_hash, time() + (30 * 24 * 60 * 60), '/'); // 30 days
        }
        
        header('Location: dashboard.php');
        exit();
    } else {
        $error_message = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Portfolio</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-form">
            <h2>Admin Login</h2>
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="remember_me" name="remember_me">
                    <label for="remember_me">Remember me for 30 days</label>
                </div>
                
                <button type="submit" class="login-btn">Login</button>
            </form>
            
            <div class="demo-credentials">
                <p><strong>Demo Credentials:</strong></p>
                <p>Username: admin</p>
                <p>Password: admin123</p>
            </div>
        </div>
    </div>
</body>
</html>
