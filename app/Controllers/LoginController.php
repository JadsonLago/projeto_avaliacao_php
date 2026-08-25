<?php
declare(strict_types=1);

namespace app\Controllers; 

use app\Models\Usuario;

class LoginController {
    private Usuario $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    public function index(): void {
        require_once __DIR__ . '/../Views/login.php';
    }

    public function login(): void {
        // Validação básica do CSRF Token se você quiser ativar a proteção que colocamos na View
        if (isset($_POST['csrf_token']) && $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            http_response_code(403);
            echo "Acesso inválido (CSRF Token Inválido)";
            exit;
        }

        $email = (string)($_POST['email'] ?? '');
        $senha = (string)($_POST['senha'] ?? '');

        $usuario = $this->usuarioModel->autenticar($email, $senha);
        
        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
            header("Location: /dashboard"); 
            exit; 
        }

        $erro = 'Ops, Email ou Senha inválido'; 
        require_once __DIR__ . '/../Views/login.php'; 
    }

    public function logout(): void {
        session_destroy();
        header("Location: /");
        exit;
    }
}
