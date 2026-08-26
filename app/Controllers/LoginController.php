<?php

namespace app\Controllers; 

use app\Models\Usuario;

class LoginController 
{
    public function __construct() 
    {
        // verifica se sessao ta rodando pra n dar warning
        if(!isset($_SESSION)){
            session_start();
        }
    }

    public function index() 
    {   
        // se o usuario ja tiver logado manda pro dashboard, se n mostra a tela de login
        if(isset($_SESSION['usuario'])) {
            header('Location: /dashboard');
            exit;
        }

        // cria um token pra previnir csrf no form 
        if(empty($_SESSION['csrf_token'])){
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require_once __DIR__ . '/../Views/login.php';
    }

    public function login() 
    {   
        // print_r($_POST); die; // debug pra ver os dados vindo do form
        
        $token_enviado = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        $token_sessao  = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';

        // checa o token pra evitar q mandem req falsa de outro site
        if(empty($token_enviado) || !hash_equals($token_sessao, $token_enviado)){
            http_response_code(403);
            die('Acesso inválido (CSRF Token Inválido)');
        }

        // pegando os dados
        $email = "";
        if(isset($_POST['email'])){
             $email = trim($_POST['email']);
        }
        
        $senha = isset($_POST['senha']) ? $_POST['senha'] : "";

        // instanciando direto o model do user
        $model_usu = new Usuario();
        
        $usu_valido = $model_usu->autenticar($email, $senha);
        
        if ($usu_valido) {
            // renova id da sessao p/ seguranca
            session_regenerate_id(true); 
            
            $_SESSION['usuario'] = $usu_valido;
            
             header("Location: /dashboard"); 
             exit; 
        } else {
             // falhou no login, exibe erro na view
             $erro = 'Ops, Email ou Senha inválido'; 
             require_once __DIR__ . '/../Views/login.php'; 
        }
    }

    public function logout() 
    {
        // zera o array da sessao inteira
        $_SESSION = [];

        // removendo os cookies de sessao do navegador tb
        if (ini_get("session.use_cookies")) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p["path"], $p["domain"],
                $p["secure"], $p["httponly"]
            );
        }

        session_destroy();

        header("Location: /");
        exit;
    }
}
