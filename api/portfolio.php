<?php
require_once '../admin/config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $pdo = getDBConnection();
    
    $type = $_GET['type'] ?? '';
    
    switch ($type) {
        case 'projects':
            $stmt = $pdo->query("SELECT * FROM projects WHERE status IN ('completed', 'in-progress') ORDER BY created_at DESC");
            $data = $stmt->fetchAll();
            break;
            
        case 'education':
            $stmt = $pdo->query("SELECT * FROM education ORDER BY 
                CASE 
                    WHEN status = 'current' THEN 1 
                    WHEN status = 'completed' THEN 2 
                    ELSE 3 
                END, 
                created_at DESC");
            $data = $stmt->fetchAll();
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid type parameter. Use "projects" or "education"']);
            exit;
    }
    
    echo json_encode(['success' => true, 'data' => $data]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
