<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Portfolio Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f8f9fa;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn {
            background: #007f73;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 10px 0;
        }
        .btn:hover {
            background: #00c2a8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Portfolio Admin Database Setup</h1>
        
        <?php
        try {
            require_once 'config/database.php';
            
            echo '<div class="success">';
            echo '<h3>✅ Database Setup Successful!</h3>';
            echo '<p>The database and tables have been created successfully.</p>';
            
            // Show table information
            $pdo = getDBConnection();
            
            $projectCount = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
            $educationCount = $pdo->query("SELECT COUNT(*) FROM education")->fetchColumn();
            
            echo '<ul>';
            echo '<li>Database: <strong>' . DB_NAME . '</strong> ✅</li>';
            echo '<li>Projects table: <strong>' . $projectCount . ' records</strong> ✅</li>';
            echo '<li>Education table: <strong>' . $educationCount . ' records</strong> ✅</li>';
            echo '</ul>';
            
            echo '</div>';
            
        } catch (Exception $e) {
            echo '<div class="error">';
            echo '<h3>❌ Database Setup Failed!</h3>';
            echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p>Please make sure:</p>';
            echo '<ul>';
            echo '<li>XAMPP MySQL service is running</li>';
            echo '<li>MySQL credentials are correct in config/database.php</li>';
            echo '<li>MySQL user has permission to create databases</li>';
            echo '</ul>';
            echo '</div>';
        }
        ?>
        
        <h3>Next Steps:</h3>
        <ol>
            <li>If setup was successful, you can now use the admin panel</li>
            <li>Go to <a href="login.php" class="btn">Admin Login</a></li>
            <li>Use credentials: <strong>admin</strong> / <strong>admin123</strong></li>
        </ol>
        
        <h3>Database Structure:</h3>
        <h4>Projects Table:</h4>
        <ul>
            <li>id (Primary Key)</li>
            <li>title (VARCHAR 255)</li>
            <li>description (TEXT)</li>
            <li>image (VARCHAR 255)</li>
            <li>link (VARCHAR 255)</li>
            <li>status (ENUM: completed, in-progress, planned)</li>
            <li>created_at, updated_at (TIMESTAMPS)</li>
        </ul>
        
        <h4>Education Table:</h4>
        <ul>
            <li>id (Primary Key)</li>
            <li>degree (VARCHAR 255)</li>
            <li>institution (VARCHAR 255)</li>
            <li>year (VARCHAR 50)</li>
            <li>description (TEXT)</li>
            <li>status (ENUM: current, completed, planned)</li>
            <li>created_at, updated_at (TIMESTAMPS)</li>
        </ul>
    </div>
</body>
</html>
