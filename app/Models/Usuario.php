<?php

namespace app\Models;
use config\Database;
use PDO;

class Usuario {
    private $conexao;
    private $tabela = 'user';

    public function __construct() {
        $banco = new Database();
        $this->conexao = $banco->conectar();
    }

    public function criarUsuario($nome, $email, $senha) {
        $sql = "INSERT INTO {$this->tabela} (name, email, password) VALUES (:nome, :email, :senha)";
        $stmt = $this->conexao->prepare($sql);

        $senhaCriptografada = md5($senha); // Criptografa a senha usando MD5

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaCriptografada);

        return $stmt->execute();
    }

    public function autenticar($email, $senha) {
        $senha = md5($senha); // Criptografa a senha usando MD5

            $sql = "SELECT * FROM {$this->tabela} WHERE email = :email AND password = :senha";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senha);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                // Usuário autenticado com sucesso
                return true;
            } else {
                // Falha na autenticação
                return false;
            }
        }
    }
}