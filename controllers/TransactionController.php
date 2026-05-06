<?php

class TransactionController {
    private $model;
    
    public function __construct(Transaction $model) {
        $this->model = $model;
    }
    
    public function list() {
        try {
            $transacoes = $this->model->getAll();
            
            return [
                'success' => true,
                'message' => 'Transações carregadas com sucesso',
                'total' => count($transacoes),
                'data' => $transacoes
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function get($id) {
        try {
            if (empty($id)) {
                throw new Exception('ID é obrigatório');
            }
            
            if (!is_numeric($id)) {
                throw new Exception('ID deve ser um número');
            }
            
            $transacao = $this->model->getById($id);
            
            if (!$transacao) {
                throw new Exception('Transação não encontrada');
            }
            
            return [
                'success' => true,
                'message' => 'Transação encontrada',
                'data' => $transacao
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function summary() {
        try {
            $summary = $this->model->getSummary();
            
            return [
                'success' => true,
                'message' => 'Resumo carregado',
                'summary' => $summary
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function create($type, $category, $amount, $date, $description = null) {
        try {
            if (empty($type)) {
                throw new Exception('Type é obrigatório');
            }
            
            if (empty($category)) {
                throw new Exception('Category é obrigatória');
            }
            
            if (empty($amount)) {
                throw new Exception('Amount é obrigatório');
            }
            
            if (empty($date)) {
                throw new Exception('Date é obrigatória');
            }
            
            $amount = floatval($amount);
            
            $id = $this->model->create($type, $category, $amount, $date, $description);
            
            return [
                'success' => true,
                'message' => 'Transação criada com sucesso',
                'id' => $id,
                'data' => [
                    'id' => $id,
                    'type' => $type,
                    'category' => $category,
                    'description' => $description,
                    'amount' => $amount,
                    'date' => $date
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function update($id, $data) {
        try {
            if (empty($id)) {
                throw new Exception('ID é obrigatório');
            }
            
            if (empty($data)) {
                throw new Exception('Nenhum dado para atualizar');
            }
            
            $affected = $this->model->update($id, $data);
            
            if ($affected == 0) {
                throw new Exception('Transação não encontrada');
            }
            
            return [
                'success' => true,
                'message' => 'Transação atualizada com sucesso',
                'affected_rows' => $affected
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function delete($id) {
        try {
            if (empty($id)) {
                throw new Exception('ID é obrigatório');
            }
            
            $affected = $this->model->delete($id);
            
            if ($affected == 0) {
                throw new Exception('Transação não encontrada');
            }
            
            return [
                'success' => true,
                'message' => 'Transação deletada com sucesso'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function getByType($type) {
        try {
            if (empty($type)) {
                throw new Exception('Type é obrigatório');
            }
            
            if (!in_array($type, ['income', 'expense'])) {
                throw new Exception('Type deve ser "income" ou "expense"');
            }
            
            $transacoes = $this->model->getByType($type);
            
            $typeLabel = $type === 'income' ? 'Receitas' : 'Despesas';
            
            return [
                'success' => true,
                'message' => "$typeLabel carregadas",
                'type' => $type,
                'total' => count($transacoes),
                'data' => $transacoes
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function filter($filters) {
        try {
            if (!is_array($filters)) {
                throw new Exception('Filtros devem ser um array');
            }
            
            $transacoes = $this->model->getFiltered($filters);
            
            return [
                'success' => true,
                'message' => 'Transações filtradas',
                'total' => count($transacoes),
                'filters' => $filters,
                'data' => $transacoes
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

?>