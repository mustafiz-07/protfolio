<?php
require_once __DIR__ . '/../config/database.php';

$pdo = getDBConnection();
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add_education':
                    $stmt = $pdo->prepare("INSERT INTO education (degree, institution, year, description, status) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['degree'],
                        $_POST['institution'],
                        $_POST['year'],
                        $_POST['description'],
                        $_POST['status']
                    ]);
                    // Redirect to prevent form resubmission
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
                    // Redirect to prevent form resubmission
                    header("Location: admin.php?page=education&success=updated");
                    exit;
                    break;
                    
                case 'delete_education':
                    $stmt = $pdo->prepare("DELETE FROM education WHERE id = ?");
                    $stmt->execute([$_POST['education_id']]);
                    // Redirect to prevent form resubmission
                    header("Location: admin.php?page=education&success=deleted");
                    exit;
                    break;
            }
        } catch (PDOException $e) {
            // Redirect with error message
            header("Location: admin.php?page=education&error=" . urlencode($e->getMessage()));
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
            $success_message = "Education record added successfully!";
            break;
        case 'updated':
            $success_message = "Education record updated successfully!";
            break;
        case 'deleted':
            $success_message = "Education record deleted successfully!";
            break;
    }
}

if (isset($_GET['error'])) {
    $error_message = "Database error: " . htmlspecialchars($_GET['error']);
}

// Fetch all education records from database
try {
    $stmt = $pdo->query("SELECT * FROM education ORDER BY 
        CASE 
            WHEN status = 'current' THEN 1 
            WHEN status = 'completed' THEN 2 
            ELSE 3 
        END, 
        created_at DESC");
    $education_records = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error fetching education records: " . $e->getMessage();
    $education_records = [];
}
?>

<div class="education-section">
    <?php if ($success_message): ?>
        <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    
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
