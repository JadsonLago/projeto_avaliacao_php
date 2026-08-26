<?php
declare(strict_types=1);

session_start(); 

require_once __DIR__ . '/../autoload.php';

use app\Controllers\LoginController;
use app\Controllers\DashboardController;
use app\Controllers\ServicoController;

// Pega a rota atual da url
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$url = parse_url($requestUri, PHP_URL_PATH);

// Tratamento simples de rotas (Router manual)
switch ($url) {
    case '/':
    case '/login':
        $controller = new LoginController();
        $controller->index();
        break;
        
    case '/logar':
        $controller = new LoginController();
        $controller->login();
        break;

    case '/sair':
        $controller = new LoginController();
        $controller->logout();
        break;

    case '/dashboard':
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }
        (new DashboardController())->index();
        break;

    case '/servico/novo':
        (new ServicoController())->novo();
        break; // <-- Faltava este break;

    case '/servico/salvar':
        (new ServicoController())->salvar();
        break; // <-- Faltava este break;

    default:
        http_response_code(404);
        echo "Página não encontrada (Erro 404)";
        break;
}

