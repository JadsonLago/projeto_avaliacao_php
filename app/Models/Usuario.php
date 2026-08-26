<?php

namespace app\Models;

use config\Database;
use PDO;

class Usuario 
{
    private $db;
    private $tabela = 'user';

    public function __construct() 
    {
        $banco = new Database();
        $this->db = $banco->conectar();
    }

    public function criarUsuario($nome, $email, $senha) {
        $sql="INSERT INTO ".$this->tabela." (name, email, password) VALUES (:nome, :email, :senha)";
        $cmd = $this->db->prepare($sql);

        // gerando hash da senha pro banco n ficar exposto
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $cmd->bindParam(':nome', $nome);
         $cmd->bindParam(':email', $email);
        $cmd->bindParam(':senha', $hash);

        // var_dump($cmd); die;

        return $cmd->execute();
    }

    public function autenticar($email, $senha) 
    {
        $sql = "SELECT * FROM ".$this->tabela." WHERE email = :email LIMIT 1";
        
        $cmd = $this->db->prepare($sql);
        $cmd->bindParam(':email', $email);
        $cmd->execute();

        $dados_usu = $cmd->fetch(PDO::FETCH_ASSOC);

        // print_r($dados_usu); die; // testando dps tirar
        
        // verifica se achou o usario e dps bate a senha
        if ($dados_usu && password_verify($senha, $dados_usu['password'])) {
            
            // limpa a senha do array p n retornar pra sessao
            unset($dados_usu['password']); 
            
            return $dados_usu;
        }else{
             return null;
        }
    }
}