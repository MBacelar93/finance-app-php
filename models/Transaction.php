<?php 
class Transaction {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    //MÉTODO 1: BUSCAR TODAS AS TRANSAÇÕES

    public function getAll() {
        $sql = "SELECT * FROM transactions  ORDER BY date DESC";
        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            throw new Exception("Erro ao buscar transações: ".mysqli_error($this->conn));
        }

        $transacoes = [];

        while($row = mysqli_fetch_assoc($result)){
            $transacoes [] = $row;
        }
        return $transacoes;
        
    }

    //MÉTODO 2: BUSCAR TRANSAÇÃO POR ID

    public function getById($id) {
        if (!is_numeric($id)) {
            throw new Exeception("ID deve ser númerico");
        }
    
        $sql = "SELECT * FROM transactions WHERE id = $id";

        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            throw new Exception("Erro ao buscar: ".mysqli_error($this->conn));
        }
        return mysqli_fetch_assoc($result);
    }

    //MÉTODO 3: BUSCAR POR TIPO

    public function getByType($type) {
        if (!in_array($type, ['income', 'expense'])) {
            throw new Exception("Type deve ser 'income' ou 'expense'");
        }

        $sql = "SELECT * FROM transactions WHERE type = '$type' ORDER BY date DESC";

        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            throw new Exeception("Erro ao buscar: ".mysqli_error($this->conn));
        }

        $transacoes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $transacoes[] = $row;
        }
        return $transacoes;
    }

    //MÉTODO 4: SOMAR RECEITAS E DESPESAS

    public function sumByType($type) {
        if (!in_array($type, ['income', 'expense'])) {
            throw new Exeception("Tipo inválido");
        }

        $sql = "SELECT SUM(amount) as total FROM transactions WHERE type = '$type' ";

        $result = mysqli_query($this->conn, $sql);

        if (!$result) {
            throw new Exeception("Erro ao somar: ".mysqli_erro($this->conn));
        }

        $row = mysqli_fetch_assoc($result);

        return floatval($row['total'] ?? 0);
    }


    //MÉTODO 5: RESUMO FINANCEIRO
    
    public function getSummary() {
        $totalIncome = $this->sumByType('income');
        $totalExpense = $this->sumByType('expense');

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense
        ];
        
    }



    //MÉTODO 6: CRIAR NOVA TRANSAÇÃO

    public function create ($type, $category, $amount, $date, $description = null) {
        
        if (empty($category)) {
            throw new Exception("Category é obrigatória");
        }

        if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
            throw new Exception("Amount deve ser um número positivo");
        }

        if (empty($date)) {
            throw new Exception("Date é obrigatório");
        }

        //ESCAPAR STRING
        $type = mysqli_real_escape_string($this->conn, $type);
        $category = mysqli_real_escape_string($this->conn, $category);
        $description = $description ? mysqli_real_escape_string($this->conn, $description) : '';
        $date = mysqli_real_escape_string($this->conn, $date);
        
        $sql = "INSERT INTO transactions (type, category, description, amount, date) VALUES ('$type', '$category', '$description', '$amount', '$date')";

        if (!mysqli_query($this->conn, $sql)) {
            throw new Exception("Erro ao inserir: ".mysqli_error($this->conn));
        }

    }

    //MÉTODO 7: ATUALIZAR TRANSAÇÕES

    public function update ($id, $data) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("ID Inválido");
        }

        if(empty($data)) {
            throw new Exception("Nenhum dado para atualizar");
        }

        $update = [];

        if (isset($data['category'])) {
            if (empty($data['category'])) {
                throw new Exception("Category não pode ser vazia");
            }
            $category = mysqli_real_escape_string($this->conn, $data['category']);
            $updates[] = "category = '$category'"; 
        }

        if (isset($data['amount'])) {
            if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
                throw new Exception("Amount Inválido");
            }
            $updates[] = "amount = ".floatval($data['amount']);
        }

        if (isset($data['date'])) {
            $date = mysqli_real_escape_string($this->conn, $data['date']);
            $updates[] = "date = '$date'";
        }

         $sql = "UPDATE transactions SET " . implode(', ', $updates) . " WHERE id = $id";

        if (!mysqli_query($this->conn, $sql)) {
            throw new Exception("Erro ao atualizar: " . mysqli_error($this->conn));
        }
        return mysqli_affected_rows($this->conn);
    }


    // MÉTODO 8: DELETAR TRANSAÇÃO

    public function delete($id) {
        
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("ID inválido");
        }
        
        $sql = "DELETE FROM transactions WHERE id = $id";
        

        if (!mysqli_query($this->conn, $sql)) {
            throw new Exception("Erro ao deletar: " . mysqli_error($this->conn));
        }

        return mysqli_affected_rows($this->conn);
    }
    
}
?>