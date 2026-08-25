<?php
declare(strict_types=1);

namespace app\Controllers; 

use app\Models\Usuario;

class LoginController 
{
    private $usuarioModel;

    public function __construct() 
    {
        // Instancia o model de usuarios para as validacoes
        $this->usuarioModel = new Usuario();
    }

    public function index(): void 
    {
        // Carrega a tela de login padrao
        require_once __DIR__ . '/../Views/login.php';
    }

    public function login(): void 
    {
        $tokenPost = $_POST['csrf_token'] ?? '';
        $tokenSessao = $_SESSION['csrf_token'] ?? '';

        // Bloqueia se o token csrf nao bater
        if (empty($tokenPost) || $tokenPost !== $tokenSessao) {
            http_response_code(403);
            echo "Acesso inválido (CSRF Token Inválido)";
            exit;
        }

        // Sanitizacao simples das entradas
        $email = trim((string)($_POST['email'] ?? ''));
        $senha = (string)($_POST['senha'] ?? '');

        // Tenta logar o usuario no banco
        $usuario = $this->usuarioModel->autenticar($email, $senha);
        
        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
            header("Location: /dashboard"); 
            exit; 
        }

        // Se falhar, volta pra tela com a mensagem de erro
        $erro = 'Ops, Email ou Senha inválido'; 
        require_once __DIR__ . '/../Views/login.php'; 
    }

    public function logout(): void 
    {
        session_destroy();
        header("Location: /");
        exit;
    }
}
