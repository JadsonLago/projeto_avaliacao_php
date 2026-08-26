<?php

namespace app\Models;

use config\Database;
use PDO;

class Servico {
    
    private $db;
    private $tabela = 'service';

    public function __construct() 
    {
        $banco = new Database();
        $this->db = $banco->conectar();
    }

    public function listarTodos($filtros = []) 
    {
        // gambiarra do 1=1 pra facilitar o concat dos filtros kk
        $sql = "SELECT s.*, u.name as nome_usuario 
                FROM ".$this->tabela." s 
                LEFT JOIN user u ON s.user_id_user = u.id_user 
                WHERE 1=1 ";
        
        $binds = [];

        // Filtro a partir da Data Inicial
        if(isset($filtros['data_inicial']) && $filtros['data_inicial'] != '') {
            $sql .= " AND DATE(s.created_at) >= :data_inicial";
             $binds['data_inicial'] = $filtros['data_inicial'];
        }

        // data final
        if(isset($filtros['data_final']) && $filtros['data_final'] != '') {
            $sql .= " AND DATE(s.created_at) <= :data_final";
            $binds['data_final'] = $filtros['data_final'];
        }

        if(isset($filtros['nome_servico']) && $filtros['nome_servico'] != "") {
            $sql .= " AND s.description LIKE :nome_servico";
             $binds['nome_servico'] = '%' . $filtros['nome_servico'] . '%';
        }

        if(isset($filtros['nome_usuario']) && $filtros['nome_usuario'] != "") {
             $sql .= " AND u.name LIKE :nome_usuario";
             $binds['nome_usuario'] = '%' . $filtros['nome_usuario'] . '%';
        }

        // ver status
        if(isset($filtros['status']) && $filtros['status'] != "") {
            if($filtros['status'] == 'PENDENTE'){
                $sql .= " AND s.finished_at IS NULL";
            }else{
                 $sql .= " AND s.finished_at IS NOT NULL";
            }
        }

        $sql .= " ORDER BY s.id_service DESC";
        
        // echo $sql; print_r($binds); die; // descomentar pra ver a query
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($binds);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calcularTotalPorUsuario($id_usu) 
    {
        $sql = "SELECT SUM(price) as total FROM ".$this->tabela." WHERE user_id_user = :id_usuario";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usu);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($resultado && $resultado['total'] != null){
             return (float)$resultado['total'];
        }else{
             return 0.0;
        }
    }

    public function atualizar($id, $desc, $preco) 
    {
        $sql = "UPDATE ".$this->tabela." SET description = :descricao, price = :preco WHERE id_service = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':descricao', $desc);
        $stmt->bindParam(':preco', $preco); 
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function excluir($id) {
        $sql = "DELETE FROM ".$this->tabela." WHERE id_service = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function listarPendentesPorUsuario($id_usu) 
    {
        $sql = "SELECT id_service, description FROM ".$this->tabela." 
                WHERE user_id_user = :id_usuario AND finished_at IS NULL 
                ORDER BY id_service DESC LIMIT 3";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usu);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criarServico($desc, $preco, $id_usu) 
    {
        $sql = "INSERT INTO ".$this->tabela." (description, price, user_id_user) 
                VALUES (:descricao, :preco, :id_usuario)";
                
        $cmd = $this->db->prepare($sql);
        $cmd->bindParam(':descricao', $desc);
        $cmd->bindParam(':preco', $preco);
        $cmd->bindParam(':id_usuario', $id_usu);
        
        return $cmd->execute();
    }

    public function buscarPorId($id) 
    {
        // tras o servico e os dados de quem cadastrou
        $sql = "SELECT s.*, u.email as email_usuario, u.name as nome_usuario 
                FROM ".$this->tabela." s
                INNER JOIN user u ON s.user_id_user = u.id_user
                WHERE s.id_service = :id LIMIT 1";
                
        $cmd = $this->db->prepare($sql);
        $cmd->bindParam(':id', $id);
        $cmd->execute();
        
        $res = $cmd->fetch(PDO::FETCH_ASSOC);
        
        // se achou devolve senao devolve nulo
        if($res){
            return $res;
        }else{
            return null;
        }
    }

    public function finalizarServico($id, $data_finalizacao) 
    {
        // atualiza a data p finalizar
        $sql = "UPDATE ".$this->tabela." SET finished_at = :data WHERE id_service = :id";
                
        $cmd = $this->db->prepare($sql);
        $cmd->bindParam(':data', $data_finalizacao);
        $cmd->bindParam(':id', $id);
        
        return $cmd->execute();
    }
}
