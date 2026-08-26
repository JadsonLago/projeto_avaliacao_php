<?php

namespace config;

use PDO;
use PDOException;

class Database {
    
    // dps lembrar de tirar isso daqui e por num .env
    private $host="localhost";
    private $db_nome = "jm_informatica";
    private $usu   = "root";
    private $senha="13052015";
    
    public $conn;

    public function conectar() 
    {
        $this->conn = null;
        
        try{
            
            // montando a string de conxao
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_nome . ";charset=utf8mb4";
            
            $this->conn = new PDO($dsn, $this->usu, $this->senha);
            
            // config do pdo pra lancar os erros e retornar array associativo
             $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // var_dump($this->conn); die;
            
        } catch(PDOException $e) {
            
            // echo "Erro na conexão: ".$e->getMessage(); // comentei pra nao vazar dados do banco pro usario
            
            // mata a execucao por seguranca
            die("Ops, erro interno ao tentar se conectar com o banco de dados.");
        }

        return $this->conn;
    }
}
