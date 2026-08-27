<?php
declare(strict_types=1);

session_start();

// Avisa o servidor embutido do PHP para entregar arquivos estáticos direto
if (php_sapi_name() === 'cli-server') {
    // Pegamos o caminho sem usar o realpath() para garantir que sempre será uma string
    $caminhoArquivo = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // O is_file() agora recebe uma string com certeza. Se não for um arquivo, ele ignora.
    if (is_file($caminhoArquivo)) {
        return false;
    }
}

require_once __DIR__ . '/../autoload.php';

use app\Controllers\LoginController;
use app\Controllers\DashboardController;
use app\Controllers\ServicoController;
use app\Controllers\UsuarioController;

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
    
    case '/servico/editar':
        (new ServicoController())->editar();
        break;
    case '/servico/atualizar':
        (new ServicoController())->atualizar();
        break;
    case '/servico/excluir':
        (new ServicoController())->excluir();
        break;
    case '/servico/finalizar':
        (new ServicoController())->finalizar();
        break;

    case '/cadastro':
        (new UsuarioController())->novo();
        break;

    case '/usuario/salvar':
        (new UsuarioController())->salvar();
        break;
            
    default:
        http_response_code(404);
        echo "Página não encontrada (Erro 404)";
        break;
}

