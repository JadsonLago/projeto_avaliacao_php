<?php
declare(strict_types=1);

session_start(); 

require_once __DIR__ . '/../autoload.php';

// Criamos o atalho aqui no topo!
use app\Controllers\LoginController;

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($url) {
    case '/':
    case '/login':
        // Olha como o código fica mais limpo e curto sem aquele caminho todo:
        (new LoginController())->index();
        break;
        
    case '/logar':
        (new LoginController())->login();
        break;

    case '/sair':
        (new LoginController())->logout();
        break;

    case '/dashboard':
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }

        echo "<h1>Você está no Dashboard, " . htmlspecialchars($_SESSION['usuario']['name']) . "!</h1>";
        echo "<a href='/sair'>Sair</a>";
        break;

    default:
        http_response_code(404);
        echo "Página não encontrada (Erro 404)";
        break;
}
