<?php
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['type']) || !isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$pdo = getDBConnection();

try {
    if ($_GET['type'] === 'project') {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $data = $stmt->fetch();
    } elseif ($_GET['type'] === 'education') {
        $stmt = $pdo->prepare("SELECT * FROM education WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $data = $stmt->fetch();
    } else {
        throw new Exception('Invalid type');
    }
    
    if (!$data) {
        http_response_code(404);
        echo json_encode(['error' => 'Record not found']);
        exit;
    }
    
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
