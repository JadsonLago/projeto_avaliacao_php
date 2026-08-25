<?php
declare(strict_types=1);

namespace app\Models;

use config\Database;
use PDO;

class Servico {
    private PDO $db;
    private string $table = 'service';

    public function __construct() {
        $banco = new Database();
        $this->db = $banco->conectar();
    }

    public function listarTodos(): array {
        $sql = "SELECT s.*, u.name as nome_usuario 
                FROM {$this->table} s
                LEFT JOIN user u ON s.user_id_user = u.id_user
                ORDER BY s.id_service DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
