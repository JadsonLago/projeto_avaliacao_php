<?php

namespace app\Controllers;

use app\Models\Servico;

class ServicoController 
{
    private $model;
    private $id_logado;

    public function __construct() 
    {
        // verifica a sessao
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // pega id do usario
        if(isset($_SESSION['usuario']['id_user'])){
            $this->id_logado = $_SESSION['usuario']['id_user'];
        }else{
            $this->id_logado = 0;
        }

        if ($this->id_logado == 0) {
            header("Location: /login");
            exit;
        }

        $this->model = new Servico();
    }

    public function novo() 
    {
        // gerando token do form pro cara nao mandar req de fora
        if(empty($_SESSION['csrf_token'])){
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require_once __DIR__ . '/../Views/cadastro_servico.php';
    }

    public function salvar() 
    {
        // checa csrf de seguranca
        $token_post = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if($token_post == '' || $token_post != $_SESSION['csrf_token']) {
            die('Acesso inválido (CSRF Token Inválido)');
        }

        $desc = isset($_POST['descricao']) ? trim($_POST['descricao']) : "";
        $preco = isset($_POST['preco']) ? trim($_POST['preco']) : "";

        if ($desc == "" || $preco == "") {
            $_SESSION['mensagem_erro'] = "Falha ao adicionar novo serviço. Preencha todos os campos.";
            header("Location: /dashboard");
            exit;
        }

        // limpando o valor do preco q vem com virgula do form
        $preco = preg_replace('/[^0-9,.]/', '', $preco);
        $val_formatado = str_replace(',', '.', $preco);
        $val_formatado = (float)$val_formatado;

        if ($this->model->criarServico($desc, $val_formatado, $this->id_logado)) {
            $_SESSION['mensagem_sucesso'] = "Serviço adicionado com sucesso!";
        } else {
             $_SESSION['mensagem_erro'] = "Ocorreu um erro ao salvar no banco.";
        }
       
        header("Location: /dashboard");
        exit;
    }

    public function finalizar(): void 
    {
        // validacao do token dnv pq mudou pra post
        $tokenEnviado = $_POST['csrf_token'] ?? '';
        $tokenSessao = $_SESSION['csrf_token'] ?? '';
        if (empty($tokenEnviado) || !hash_equals($tokenSessao, $tokenEnviado)) {
            die('Acesso inválido (CSRF Token Inválido)');
        }

        $idServico = (int)($_POST['id'] ?? 0);

        if ($idServico === 0) {
            $_SESSION['mensagem_erro'] = "Serviço inválido.";
            header("Location: /dashboard");
            exit;
        } // CORREÇÃO 1: Removida a chave dupla aqui

        $dados_serv = $this->model->buscarPorId($idServico);

        // verifica se e dele msm e se n ta finalizado ja (usando user_id_user que é o padrão do seu banco)
        if(!$dados_serv || $dados_serv['user_id_user'] != $this->id_logado || $dados_serv['finished_at'] != ""){
             $_SESSION['mensagem_erro'] = "Serviço não encontrado, já finalizado ou você não tem permissão.";
             header("Location: /dashboard");
             exit;
        }

        $val = $dados_serv['price'];
       
        $comissao = 0;
       
        // calc de comissao dependendo do valor
        if($val > 10000.00){
             $comissao = $val * 0.20; 
        } else {
             if($val > 1000.00){
                  $comissao = $val * 0.10;
             } else {
                 $comissao = $val * 0.05;
             }
        }

        $data_hj = date('Y-m-d H:i:s');

        // CORREÇÃO 2: Passando a variável correta ($idServico em vez de $id_serv)
        if($this->model->finalizarServico($idServico, $data_hj)){
            
            $email_cli = isset($dados_serv['email_usuario']) ? $dados_serv['email_usuario'] : '';
            
            if($email_cli != ""){
                $assunto = "Serviço Finalizado";
                $msg = "O serviço foi finalizado. Sua comissão: R$ " . number_format($comissao, 2, ',', '.');
                $headers = "From: sistema@jminformatica.com\r\n";
                @mail($email_cli, $assunto, $msg, $headers);
            }

            $_SESSION['mensagem_sucesso'] = "Serviço finalizado! E-mail enviado. Comissão: R$ " . number_format($comissao, 2, ',', '.');
        }else{
            $_SESSION['mensagem_erro'] = "Erro ao gravar a finalização no banco de dados.";
        }

        header("Location: /dashboard");
        exit;
    }

    public function editar() 
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $servico = $this->model->buscarPorId($id);

        $idLogado = (int)($this->id_logado ?? 0);
        $idDonoServico = (int)($servico['user_id_user'] ?? 0);

        // Bloqueia se o serviço não for do dono
        if (!$servico || $idDonoServico !== $idLogado) {
            $_SESSION['mensagem_erro'] = "Serviço não encontrado ou acesso negado.";
            header("Location: /dashboard");
            exit;
        }

        if(empty($_SESSION['csrf_token'])){
             $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require_once __DIR__ . '/../Views/editar_servico.php';
    }

    public function atualizar() 
    {
        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if($token == '' || $token != $_SESSION['csrf_token']){
             die('Acesso negado');
        }

        $id_serv = isset($_POST['id_service']) ? $_POST['id_service'] : 0;
        $desc = isset($_POST['descricao']) ? trim($_POST['descricao']) : "";
        $preco = isset($_POST['preco']) ? trim($_POST['preco']) : "";

        // formatar o preco igual fiz no salvar
        $preco = preg_replace('/[^0-9,.]/', '', $preco);
        $val_formatado = str_replace(',', '.', $preco);
        $val_formatado = (float)$val_formatado;

        $dados = $this->model->buscarPorId($id_serv);
        
        // --- ADICIONE ESTAS 3 LINHAS PARA TESTAR ---
        echo "<pre>";
        echo "ID Logado na Sessão: " . $this->id_logado . "\n";
        echo "Dados do Banco do Serviço: \n";
        print_r($dados);
        echo "</pre>";
        die();
        // ------------------------------------------
        // Converte explicitamente ambos para inteiro (int) para evitar erro de tipo na comparação
        $idLogado = (int)($this->id_logado ?? 0);
        $idDonoServico = (int)($dados['user_id_user'] ?? 0);

        if (!$dados || $idDonoServico !== $idLogado) {
            $_SESSION['mensagem_erro'] = "Acesso negado para alteração deste registro.";
            header("Location: /dashboard");
            exit;
        }

        if(!$dados || $dados['id_user'] != $this->id_logado){
            $_SESSION['mensagem_erro'] = "Acesso negado para alteração deste registro.";
            header("Location: /dashboard");
            exit;
        }

        if($id_serv == 0 || $desc == "" || $val_formatado <= 0){
            $_SESSION['mensagem_erro'] = "Preencha todos os campos corretamente.";
            header("Location: /servico/editar?id=" . $id_serv);
            exit;
        }

        if ($this->model->atualizar($id_serv, $desc, $val_formatado)) {
            $_SESSION['mensagem_sucesso'] = "Serviço atualizado com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao atualizar o serviço.";
        }

        header("Location: /dashboard");
        exit;
    }

    public function excluir(): void 
    {
        // token no delete p/ previnir merda
        $tokenEnviado = $_POST['csrf_token'] ?? '';
        $tokenSessao = $_SESSION['csrf_token'] ?? '';
        if (empty($tokenEnviado) || !hash_equals($tokenSessao, $tokenEnviado)) {
            die('Acesso inválido (CSRF Token Inválido)');
        }
        
        // Agora pegamos o ID via POST
        $idServico = (int)($_POST['id'] ?? 0);

        if ($idServico > 0) {
            
            // CORREÇÃO 3: Faltou buscar o serviço no banco para poder testar o dono!
            $servico = $this->model->buscarPorId($idServico);
            
            // so deixa apagar se for o dono do registro
            if ($servico && $servico['user_id_user'] == $this->id_logado) {
                // CORREÇÃO 4: Passando a variável correta ($idServico em vez de $id_serv)
                if ($this->model->excluir($idServico)) {
                    $_SESSION['mensagem_sucesso'] = "Serviço excluído com sucesso!";
                } else {
                    $_SESSION['mensagem_erro'] = "Erro ao excluir o serviço.";
                }
            }else{
                 $_SESSION['mensagem_erro'] = "Você não tem permissão para excluir este serviço.";
            }
        }

        header("Location: /dashboard");
        exit;
    }
}