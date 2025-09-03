<?php
require_once __DIR__ . '/../config/database.php';

$pdo = getDBConnection();

// Get real counts from database
try {
    $projectCount = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    $educationCount = $pdo->query("SELECT COUNT(*) FROM education")->fetchColumn();
    $currentEducation = $pdo->query("SELECT COUNT(*) FROM education WHERE status = 'current'")->fetchColumn();
    $completedProjects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'completed'")->fetchColumn();
} catch (PDOException $e) {
    $projectCount = 0;
    $educationCount = 0;
    $currentEducation = 0;
    $completedProjects = 0;
}
?>

<div class="dashboard-overview">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $projectCount; ?></h3>
                <p>Total Projects</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $educationCount; ?></h3>
                <p>Education Records</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $completedProjects; ?></h3>
                <p>Completed Projects</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo date('M d'); ?></h3>
                <p>Today's Date</p>
            </div>
        </div>
    </div>
    
    <div class="quick-actions">
        <h3>Quick Actions</h3>
        <div class="actions-grid">
            <a href="admin.php?page=projects" class="action-btn">
                <i class="fas fa-plus"></i>
                <span>Add New Project</span>
            </a>
            <a href="admin.php?page=education" class="action-btn">
                <i class="fas fa-graduation-cap"></i>
                <span>Update Education</span>
            </a>
            <a href="../index.html" target="_blank" class="action-btn">
                <i class="fas fa-external-link-alt"></i>
                <span>View Website</span>
            </a>
        </div>
    </div>
</div>
