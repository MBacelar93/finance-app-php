<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'config/database.php';
require_once 'models/Transaction.php';
require_once 'controllers/TransactionController.php';

try {
    $model = new Transaction($conn);
    $controller = new TransactionController($model);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao inicializar: ' . $e->getMessage()
    ]);
    exit;
}


$action = $_GET['action'] ?? 'list';
$method = $_SERVER['REQUEST_METHOD'];

require_once 'routes/routes.php';

http_response_code($statusCode);
echo json_encode($resposta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>