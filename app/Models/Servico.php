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

    public function calcularTotalPorUsuario(int $idUsuario): float {
        // Corrigido para {$this->table}
        $sql = "SELECT SUM(price) as total FROM {$this->table} WHERE user_id_user = :id_usuario";
        
        // Corrigido para $this->db
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total'] ? (float)$resultado['total'] : 0.0;
    }

    public function listarPendentesPorUsuario(int $idUsuario): array {
        // Corrigido para {$this->table}
        $sql = "SELECT id_service, description FROM {$this->table} 
                WHERE user_id_user = :id_usuario AND finished_at IS NULL 
                ORDER BY id_service DESC LIMIT 3";
                
        // Corrigido para $this->db
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

