<?php
session_start();
require_once 'config/database.php';

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
$pdo = getDBConnection();

// Handle form submissions for all sections
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                // Project operations
                case 'add_project':
                    $stmt = $pdo->prepare("INSERT INTO projects (title, description, image, link, status) VALUES (?, ?, ?, ?, ?)");
                    $image = $_POST['image'] ?? 'cse-mini-projects.webp';
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['description'],
                        $image,
                        $_POST['link'],
                        $_POST['status']
                    ]);
                    header("Location: admin.php?page=projects&success=added");
                    exit;
                    break;
                    
                case 'edit_project':
                    $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, image = ?, link = ?, status = ? WHERE id = ?");
                    $image = $_POST['image'] ?? 'cse-mini-projects.webp';
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['description'],
                        $image,
                        $_POST['link'],
                        $_POST['status'],
                        $_POST['project_id']
                    ]);
                    header("Location: admin.php?page=projects&success=updated");
                    exit;
                    break;
                    
                case 'delete_project':
                    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
                    $stmt->execute([$_POST['project_id']]);
                    header("Location: admin.php?page=projects&success=deleted");
                    exit;
                    break;

                // Education operations
                case 'add_education':
                    $stmt = $pdo->prepare("INSERT INTO education (degree, institution, year, description, status) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['degree'],
                        $_POST['institution'],
                        $_POST['year'],
                        $_POST['description'],
                        $_POST['status']
                    ]);
                    header("Location: admin.php?page=education&success=added");
                    exit;
                    break;
                    
                case 'edit_education':
                    $stmt = $pdo->prepare("UPDATE education SET degree = ?, institution = ?, year = ?, description = ?, status = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['degree'],
                        $_POST['institution'],
                        $_POST['year'],
                        $_POST['description'],
                        $_POST['status'],
                        $_POST['education_id']
                    ]);
                    header("Location: admin.php?page=education&success=updated");
                    exit;
                    break;
                    
                case 'delete_education':
                    $stmt = $pdo->prepare("DELETE FROM education WHERE id = ?");
                    $stmt->execute([$_POST['education_id']]);
                    header("Location: admin.php?page=education&success=deleted");
                    exit;
                    break;
            }
        } catch (PDOException $e) {
            header("Location: admin.php?page={$current_page}&error=" . urlencode($e->getMessage()));
            exit;
        }
    }
}

// Handle success/error messages
$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $success_message = ucfirst($current_page === 'projects' ? 'Project' : 'Education record') . " added successfully!";
            break;
        case 'updated':
            $success_message = ucfirst($current_page === 'projects' ? 'Project' : 'Education record') . " updated successfully!";
            break;
        case 'deleted':
            $success_message = ucfirst($current_page === 'projects' ? 'Project' : 'Education record') . " deleted successfully!";
            break;
    }
}

if (isset($_GET['error'])) {
    $error_message = "Database error: " . htmlspecialchars($_GET['error']);
}

// Fetch data based on current page
try {
    if ($current_page === 'projects') {
        $projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();
    } elseif ($current_page === 'education') {
        $education_records = $pdo->query("SELECT * FROM education ORDER BY 
            CASE 
                WHEN status = 'current' THEN 1 
                WHEN status = 'completed' THEN 2 
                ELSE 3 
            END, 
            created_at DESC")->fetchAll();
    } else {
        // Dashboard data
        $projectCount = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
        $educationCount = $pdo->query("SELECT COUNT(*) FROM education")->fetchColumn();
        $currentEducation = $pdo->query("SELECT COUNT(*) FROM education WHERE status = 'current'")->fetchColumn();
        $completedProjects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'completed'")->fetchColumn();
    }
} catch (PDOException $e) {
    $error_message = "Error fetching data: " . $e->getMessage();
    $projects = [];
    $education_records = [];
    $projectCount = 0;
    $educationCount = 0;
    $currentEducation = 0;
    $completedProjects = 0;
}
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
                <?php if ($success_message): ?>
                    <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <?php if ($current_page === 'dashboard'): ?>
                    <!-- Dashboard Section -->
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
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo date('H:i'); ?></h3>
                                    <p>Current Time</p>
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
                                    <span>View Portfolio</span>
                                </a>
                               
                            </div>
                        </div>
                    </div>

                <?php elseif ($current_page === 'projects'): ?>
                    <!-- Projects Section -->
                    <div class="projects-section">
                        <div class="section-header">
                            <h2>Projects Management</h2>
                            <button class="btn btn-primary" onclick="openProjectModal()">
                                <i class="fas fa-plus"></i> Add New Project
                            </button>
                        </div>
                        
                        <div class="projects-grid">
                            <?php foreach ($projects as $project): ?>
                            <div class="project-card">
                                <div class="project-image">
                                    <img src="../images/<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                                    <div class="project-status status-<?php echo $project['status']; ?>">
                                        <?php echo ucfirst(str_replace('-', ' ', $project['status'])); ?>
                                    </div>
                                </div>
                                
                                <div class="project-content">
                                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($project['description']); ?></p>
                                    
                                    <div class="project-actions">
                                        <button class="btn btn-secondary" onclick="editProject(<?php echo $project['id']; ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteProject(<?php echo $project['id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                        <?php if ($project['link']): ?>
                                        <a href="<?php echo htmlspecialchars($project['link']); ?>" target="_blank" class="btn btn-info">
                                            <i class="fas fa-external-link-alt"></i> View
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                <?php elseif ($current_page === 'education'): ?>
                    <!-- Education Section -->
                    <div class="education-section">
                        <div class="section-header">
                            <h2>Education Management</h2>
                            <button class="btn btn-primary" onclick="openEducationModal()">
                                <i class="fas fa-plus"></i> Add Education Record
                            </button>
                        </div>
                        
                        <div class="education-list">
                            <?php foreach ($education_records as $record): ?>
                            <div class="education-card">
                                <div class="education-header">
                                    <div class="education-year"><?php echo htmlspecialchars($record['year']); ?></div>
                                    <div class="education-status status-<?php echo $record['status']; ?>">
                                        <?php echo ucfirst($record['status']); ?>
                                    </div>
                                </div>
                                
                                <div class="education-content">
                                    <h3><?php echo htmlspecialchars($record['degree']); ?></h3>
                                    <p class="institution"><?php echo htmlspecialchars($record['institution']); ?></p>
                                    <p class="description"><?php echo htmlspecialchars($record['description']); ?></p>
                                    
                                    <div class="education-actions">
                                        <button class="btn btn-secondary" onclick="editEducation(<?php echo $record['id']; ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteEducation(<?php echo $record['id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

    <!-- Project Modal -->
    <div id="projectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="projectModalTitle">Add New Project</h3>
                <span class="close" onclick="closeProjectModal()">&times;</span>
            </div>
            
            <form id="projectForm" method="POST">
                <input type="hidden" name="action" value="add_project">
                <input type="hidden" id="projectId" name="project_id" value="">
                
                <div class="form-group">
                    <label for="projectTitle">Project Title:</label>
                    <input type="text" id="projectTitle" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="projectDescription">Description:</label>
                    <textarea id="projectDescription" name="description" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="projectImage">Image URL:</label>
                    <input type="text" id="projectImage" name="image" placeholder="e.g., project-image.jpg">
                </div>
                
                <div class="form-group">
                    <label for="projectLink">Project Link:</label>
                    <input type="url" id="projectLink" name="link" placeholder="https://...">
                </div>
                
                <div class="form-group">
                    <label for="projectStatus">Status:</label>
                    <select id="projectStatus" name="status" required>
                        <option value="planned">Planned</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeProjectModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Project</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Education Modal -->
    <div id="educationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="educationModalTitle">Add Education Record</h3>
                <span class="close" onclick="closeEducationModal()">&times;</span>
            </div>
            
            <form id="educationForm" method="POST">
                <input type="hidden" name="action" value="add_education">
                <input type="hidden" id="educationId" name="education_id" value="">
                
                <div class="form-group">
                    <label for="educationDegree">Degree/Certificate:</label>
                    <input type="text" id="educationDegree" name="degree" required>
                </div>
                
                <div class="form-group">
                    <label for="educationInstitution">Institution:</label>
                    <input type="text" id="educationInstitution" name="institution" required>
                </div>
                
                <div class="form-group">
                    <label for="educationYear">Year/Duration:</label>
                    <input type="text" id="educationYear" name="year" placeholder="e.g., 2020 - 2024" required>
                </div>
                
                <div class="form-group">
                    <label for="educationDescription">Description:</label>
                    <textarea id="educationDescription" name="description" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="educationStatus">Status:</label>
                    <select id="educationStatus" name="status" required>
                        <option value="current">Current</option>
                        <option value="completed">Completed</option>
                        <option value="planned">Planned</option>
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeEducationModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Record</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin-script.js"></script>
</body>
</html>
