<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Auto-logout after 30 minutes of inactivity
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 1800) {
    session_destroy();
    setcookie('admin_remember', '', time() - 3600, '/'); // Clear remember me cookie
    header('Location: login.php?timeout=1');
    exit();
}

// Update last activity time
$_SESSION['login_time'] = time();

$current_page = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
 <header class="admin-header">
                <div class="header-left">
                    <h1>
                        <?php 
                        switch($current_page) {
                            case 'projects': echo 'Projects Management'; break;
                            case 'education': echo 'Education Management'; break;
                            default: echo 'Dashboard'; break;
                        }
                        ?>
                    </h1>
                </div>
                
                <div class="header-right">
                    <!-- Navigation Links -->
                    <nav class="header-nav">
                        <a href="admin.php?page=dashboard" class="nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="admin.php?page=projects" class="nav-link <?php echo $current_page === 'projects' ? 'active' : ''; ?>">
                            <i class="fas fa-project-diagram"></i> Projects
                        </a>
                        <a href="admin.php?page=education" class="nav-link <?php echo $current_page === 'education' ? 'active' : ''; ?>">
                            <i class="fas fa-graduation-cap"></i> Education
                        </a>
                    </nav>
                    
             
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <?php
                switch($current_page) {
                    case 'projects':
                        include 'sections/projects.php';
                        break;
                    case 'education':
                        include 'sections/education.php';
                        break;
                    default:
                        include 'sections/dashboard.php';
                        break;
                }
                ?>
            </div>
       
    

    <script src="admin-script.js"></script>
</body>
</html>
