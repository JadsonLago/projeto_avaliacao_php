<?php
namespace config;
use PDO;
use PDOException;

// Classe que cuida da conexão com o banco de dados
class Database{
    // Dados para conectar no MySQL
    private $host = 'localhost';
    private $banco = 'jm_informatica';
    private $usuario = 'root';
    private $senha = '13052015';
    public $conexao; // A conexão ativa

    // Faz a conexão com o banco
    public function conectar(){
        $this->conexao = null;
        try {
            // Conecta no MySQL usando PDO
            $this->conexao = new PDO(
                "mysql:host=".$this->host.";dbname=".$this->banco.";charset=utf8mb4",
                $this->usuario,
                $this->senha
            );

            // Retorna os dados como array
            $this->conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            // Se tiver erro, lança uma exceção
            $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        } catch (PDOException $erro) {
            // Se der erro, mostra a mensagem
            echo "Erro na conexão: ".$erro->getMessage();
        }
        return $this->conexao;
    }

}
