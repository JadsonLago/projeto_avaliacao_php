<?php
declare(strict_types=1);

namespace app\Controllers;

use app\Models\Servico;

class DashboardController {
    
    public function index(): void {
        $idUsuario = (int)($_SESSION['usuario']['id_user'] ?? 0);
        
        $servicoModel = new Servico();
        $servicos = $servicoModel->listarTodos();

        $valorTotal = $servicoModel->calcularTotalPorUsuario($idUsuario);
        $servicosPendentes = $servicoModel->listarPendentesPorUsuario($idUsuario);

        $dataAtual = date('d/m/Y'); 
        
        require_once __DIR__ . '/../Views/dashboard.php';
    }
}

