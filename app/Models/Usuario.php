<?php

namespace app\Models;
use config\Database;
use PDO;

class Usuario {
    private $db;
    private $table = 'user';

    public function __construct() {
        $banco = new Database();
        $this->db = $banco->conectar();
    }

    public function criarUsuario($nome, $email, $senha) {
        $sql = "INSERT INTO {$this->table} (name, email, password) VALUES (:nome, :email, :senha)";
        $stmt = $this->db->prepare($sql);

        // Usando a criptografia segura e moderna do PHP
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $hash);

        return $stmt->execute();
    }

    public function autenticar($email, $senha) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            // Verifica se a senha bate com o hash seguro do banco
            if (password_verify($senha, $user['password'])) {
                unset($user['password']);
                return $user;
            }
        }
        
        return false;
    }
}
