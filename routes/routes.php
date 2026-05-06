<?php

$action = $_GET['action'] ?? 'list';
$method = $_SERVER['REQUEST_METHOD'];

$resposta = null;
$statusCode = 200;

try {
    // ========== ROTA 1: LISTAR ==========
    if ($action === 'list' && $method === 'GET') {
        $resposta = $controller->list();
        $statusCode = $resposta['success'] ? 200 : 400;
    }
    
    // ========== ROTA 2: OBTER UMA ==========
    elseif ($action === 'get' && $method === 'GET') {
        $id = $_GET['id'] ?? null;
        $resposta = $controller->get($id);
        $statusCode = $resposta['success'] ? 200 : 404;
    }
    
    // ========== ROTA 3: RESUMO ==========
    elseif ($action === 'summary' && $method === 'GET') {
        $resposta = $controller->summary();
        $statusCode = $resposta['success'] ? 200 : 400;
    }
    
    // ========== ROTA 4: LISTAR POR TIPO ==========
    elseif ($action === 'type' && $method === 'GET') {
        $type = $_GET['type'] ?? null;
        $resposta = $controller->getByType($type);
        $statusCode = $resposta['success'] ? 200 : 400;
    }
    
    // ========== ROTA 5: CRIAR (POST) ==========
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $resposta = $controller->create(
            $input['type'] ?? null,
            $input['category'] ?? null,
            $input['amount'] ?? null,
            $input['date'] ?? date('Y-m-d'),
            $input['description'] ?? null
        );
        
        $statusCode = $resposta['success'] ? 201 : 400;
    }
    
    // ========== ROTA 6: ATUALIZAR (PUT) ==========
    elseif ($method === 'PUT') {
        $id = $_GET['id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        
        $resposta = $controller->update($id, $input ?? []);
        $statusCode = $resposta['success'] ? 200 : 400;
    }
    
    // ========== ROTA 7: DELETAR (DELETE) ==========
    elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        $resposta = $controller->delete($id);
        $statusCode = $resposta['success'] ? 200 : 404;
    }
    
    // ========== ROTA 8: FILTRAR ==========
    elseif ($action === 'filter' && $method === 'GET') {
        $filters = [
            'type' => $_GET['type'] ?? null,
            'category' => $_GET['category'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null
        ];
        
        $filters = array_filter($filters);
        
        $resposta = $controller->filter($filters);
        $statusCode = $resposta['success'] ? 200 : 400;
    }
    
    // ========== AÇÃO PADRÃO: INVÁLIDA ==========
    else {
        $statusCode = 400;
        $resposta = [
            'success' => false,
            'error' => 'Ação inválida',
            'ações_disponíveis' => [
                'GET /api.php?action=list' => 'Listar todas as transações',
                'GET /api.php?action=get&id=1' => 'Obter uma transação',
                'GET /api.php?action=summary' => 'Ver resumo financeiro',
                'GET /api.php?action=type&type=expense' => 'Listar por tipo',
                'GET /api.php?action=filter&type=expense&date_from=2026-04-01' => 'Buscar com filtros',
                'POST /api.php' => 'Criar nova transação',
                'PUT /api.php?id=1' => 'Atualizar transação',
                'DELETE /api.php?id=1' => 'Deletar transação'
            ]
        ];
    }
    
} catch (Exception $e) {
    $statusCode = 500;
    $resposta = [
        'success' => false,
        'error' => 'Erro inesperado: ' . $e->getMessage()
    ];
}

?>