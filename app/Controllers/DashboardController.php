<?php
declare(strict_types=1);

namespace app\Controllers;

use app\Models\Servico;

class DashboardController {
    
    public function index(): void {
        $servicoModel = new Servico();
        $servicos = $servicoModel->listarTodos();

        $dataAtual = date('d/m/Y'); 

        require_once __DIR__ . '/../Views/dashboard.php';
    }
}
