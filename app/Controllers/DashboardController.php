<?php
declare(strict_types=1);

namespace app\Controllers;

use app\Models\Servico;

class DashboardController {
    
    public function index(){
        $servicoModel = new Servico();
        $servicos = $servicoModel->listarTodos();

        $dataAtual = date('d/m/Y'); 

        // Pega o ID do usuário logado na sessão
        $idUsuario = $_SESSION['usuario']['id_user'];
        
        // Busca os destaques exigidos pelo teste usando as novas funções
        $valorTotal = $servicoModel->calcularTotalPorUsuario($idUsuario);
        $servicosPendentes = $servicoModel->listarPendentesPorUsuario($idUsuario);
        
        require_once __DIR__ . '/../Views/dashboard.php';
    }
}
