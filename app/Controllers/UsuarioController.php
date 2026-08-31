<?php

namespace app\Controllers;

use app\Models\Usuario;

class UsuarioController 
{
    public function __construct() 
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function novo() 
    {
        // gera o token se não existir na sessao pra n dar erro no form
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = md5(time() . rand(0,999)); // alterado p/ ficar mais simples dps q o random_bytes deu pau no server antigo
        }
        require_once __DIR__ . '/../Views/cadastro_usuario.php';
    }

    public function salvar() 
    {
        $tokenEnviado = $_POST['csrf_token'] ?? '';
        $tokenSessao = $_SESSION['csrf_token'] ?? '';

        // verif se o token confere antes de salvar no banco
        if (empty($tokenEnviado) || $tokenSessao != $tokenEnviado) {
            die('Acesso inválido (CSRF Token Inválido)');
        }

        $nome = trim($_POST['nome_completo'] ?? '');
        $email = trim($_POST['email_usuario'] ?? '');
        $senha = $_POST['senha_usuario'] ?? '';

        if ($nome == '' || $email == '' || $senha == '') {
            $_SESSION['mensagem_erro'] = "Preencha todos os campos obrigatórios.";
            header("Location: /cadastro");
            exit;
        }

        $usuarioModel = new Usuario();
        $res_cad = $usuarioModel->criarUsuario($nome, $email, $senha);

        if ($res_cad) {
            $_SESSION['mensagem_sucesso'] = "Usuário cadastrado com sucesso! Faça o login.";
            header("Location: /login");
            exit;
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao cadastrar. O e-mail informado já pode estar em uso.";
              header("Location: /cadastro");
            exit;
        }
    }
}