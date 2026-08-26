<?php

namespace app\Controllers;

use app\Models\Servico;

class DashboardController {
    
    public function index()
    {
        // verifica se tem sessao aberta
        if(!isset($_SESSION)){
            session_start();
        }

        $id_usu = 0;
        
        // pega id do usario
        if(isset($_SESSION['usuario']['id_user'])){
            $id_usu = $_SESSION['usuario']['id_user'];
        }

        if($id_usu == 0){
            header('Location: /login');
            exit;
        }

        $filtros = [];

        // tratando os get pra nao dar problema de injecao
        if(isset($_GET['data_inicial'])){
            $filtros['data_inicial'] = htmlspecialchars(trim($_GET['data_inicial']));
        }else{
             $filtros['data_inicial'] = '';
        }

        if(isset($_GET['data_final'])) {
            $filtros['data_final'] = htmlspecialchars(trim($_GET['data_final']));
        } else {
            $filtros['data_final'] = '';
        }

        $filtros['nome_servico'] = isset($_GET['nome_servico']) ? htmlspecialchars(trim($_GET['nome_servico'])) : "";
        $filtros['nome_usuario'] = isset($_GET['nome_usuario']) ? htmlspecialchars(trim($_GET['nome_usuario'])) : "";

         if(isset($_GET['status'])){
             $filtros['status'] = htmlspecialchars(trim($_GET['status']));
         } else {
            $filtros['status'] = "";
         }

        // echo "<pre>"; print_r($filtros); die; // dps tirar

        $servico_model = new Servico();
        
        $lista_serv = $servico_model->listarTodos($filtros);
        
        // faz o calc do total
        $valorTotal = $servico_model->calcularTotalPorUsuario($id_usu);
        
          $servicosPendentes = $servico_model->listarPendentesPorUsuario($id_usu);
        
        $dataHoje=date('d/m/Y');

        require_once __DIR__ . '/../Views/dashboard.php';
    }
}
