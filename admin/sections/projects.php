<?php
require_once __DIR__ . '/../config/database.php';

$pdo = getDBConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add_project':
                    $stmt = $pdo->prepare("INSERT INTO projects (title, description, image, link, status) VALUES (?, ?, ?, ?, ?)");
                    $image = $_POST['image'] ?? 'cse-mini-projects.webp'; // Default image
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['description'],
                        $image,
                        $_POST['link'],
                        $_POST['status']
                    ]);
                    // Redirect to prevent form resubmission
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
                    // Redirect to prevent form resubmission
                    header("Location: admin.php?page=projects&success=updated");
                    exit;
                    break;
                    
                case 'delete_project':
                    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
                    $stmt->execute([$_POST['project_id']]);
                    // Redirect to prevent form resubmission
                    header("Location: admin.php?page=projects&success=deleted");
                    exit;
                    break;
            }
        } catch (PDOException $e) {
            // Redirect with error message
            header("Location: admin.php?page=projects&error=" . urlencode($e->getMessage()));
            exit;
        }
    }
}

// Handle success/error messages from URL parameters
$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $success_message = "Project added successfully!";
            break;
        case 'updated':
            $success_message = "Project updated successfully!";
            break;
        case 'deleted':
            $success_message = "Project deleted successfully!";
            break;
    }
}

if (isset($_GET['error'])) {
    $error_message = "Database error: " . htmlspecialchars($_GET['error']);
}

// Fetch all projects from database
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error fetching projects: " . $e->getMessage();
    $projects = [];
}
?>

<div class="projects-section">
    <?php if ($success_message): ?>
        <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    
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
                <img src="../images/cse-mini-projects.webp" alt="<?php echo htmlspecialchars($project['title']); ?>">
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
                    <a href="<?php echo $project['link']; ?>" class="btn btn-info" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Project Modal -->
<div id="projectModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Project</h3>
            <span class="close" onclick="closeProjectModal()">&times;</span>
        </div>
        
        <form id="projectForm" method="POST" enctype="multipart/form-data">
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
                <label for="projectImage">Project Image:</label>
                <input type="file" id="projectImage" name="image" accept="image/*">
            </div>
            
            <div class="form-group">
                <label for="projectLink">Project Link:</label>
                <input type="url" id="projectLink" name="link">
            </div>
            
            <div class="form-group">
                <label for="projectStatus">Status:</label>
                <select id="projectStatus" name="status" required>
                    <option value="completed">Completed</option>
                    <option value="in-progress">In Progress</option>
                    <option value="planned">Planned</option>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeProjectModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Project</button>
            </div>
        </form>
    </div>
</div>
