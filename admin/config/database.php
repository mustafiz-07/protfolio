<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP MySQL password is empty
define('DB_NAME', 'portfolio_admin');

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Initialize database and tables
function initializeDatabase() {
    try {
        // First, connect without specifying database to create it
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Now connect to the specific database
        $pdo = getDBConnection();
        
        // Create projects table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS projects (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                image VARCHAR(255) DEFAULT NULL,
                link VARCHAR(255) DEFAULT NULL,
                status ENUM('completed', 'in-progress', 'planned') DEFAULT 'planned',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        // Create education table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS education (
                id INT AUTO_INCREMENT PRIMARY KEY,
                degree VARCHAR(255) NOT NULL,
                institution VARCHAR(255) NOT NULL,
                year VARCHAR(50) NOT NULL,
                description TEXT NOT NULL,
                status ENUM('current', 'completed', 'planned') DEFAULT 'completed',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        // Insert sample data if tables are empty
        $projectCount = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
        if ($projectCount == 0) {
            $pdo->exec("
                INSERT INTO projects (title, description, image, link, status) VALUES
                ('Portfolio Website', 'Personal portfolio website built with HTML, CSS, and JavaScript', 'cse-mini-projects.webp', '#', 'completed'),
                ('E-commerce Platform', 'Full-stack e-commerce solution with payment integration', 'cse-mini-projects.webp', '#', 'in-progress'),
                ('Task Management App', 'React-based task management application with real-time updates', 'cse-mini-projects.webp', '#', 'completed')
            ");
        }
        
        $educationCount = $pdo->query("SELECT COUNT(*) FROM education")->fetchColumn();
        if ($educationCount == 0) {
            $pdo->exec("
                INSERT INTO education (degree, institution, year, description, status) VALUES
                ('Bachelor of Science in Computer Science', 'University Name', '2023 - Present', 'Currently pursuing a degree in Computer Science with focus on Software Engineering and Web Development. Maintaining a strong academic record with key coursework in Data Structures, Algorithms, and Web Technologies.', 'current'),
                ('Higher Secondary Certificate (HSC)', 'College Name', '2021 - 2023', 'Completed HSC with focus on Science and Mathematics. Achieved excellent academic results and participated in various programming competitions.', 'completed'),
                ('Secondary School Certificate (SSC)', 'School Name', '2019 - 2021', 'Completed SSC with distinction. Developed strong foundation in sciences and mathematics. Active participant in school\'s computer club.', 'completed')
            ");
        }
        
        return true;
    } catch (PDOException $e) {
        die("Database initialization failed: " . $e->getMessage());
    }
}

// Initialize database on first run
initializeDatabase();
?>
