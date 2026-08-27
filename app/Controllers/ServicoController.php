<?php

namespace app\Controllers;

use app\Models\Servico;

class ServicoController 
{
    private $model;
    private $id_logado;

    public function __construct() 
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // pega o id do usuario logado na sessao
        $this->id_logado = isset($_SESSION['usuario']['id_user']) ? (int)$_SESSION['usuario']['id_user'] : 0;

        if ($this->id_logado === 0) {
            header("Location: /login");
            exit;
        }

        $this->model = new Servico();
    }

    private function validarCsrf(): void 
    {
        $tokenEnviado = $_POST['csrf_token'] ?? '';
        $tokenSessao = $_SESSION['csrf_token'] ?? '';
        
        if (empty($tokenEnviado) || !hash_equals($tokenSessao, $tokenEnviado)) {
            die('Acesso inválido (CSRF Token Inválido)');
        }
    }

    private function formatarPreco(string $precoRaw): float 
    {
        $precoLimpo = preg_replace('/[^0-9,.]/', '', $precoRaw);
        return (float)str_replace(',', '.', $precoLimpo);
    }

    public function novo() 
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require_once __DIR__ . '/../Views/cadastro_servico.php';
    }

    public function salvar() 
    {
        $this->validarCsrf();

        $desc = trim($_POST['descricao'] ?? '');
        $precoRaw = trim($_POST['preco'] ?? '');

        // confere se ta vazio msm
        if ($desc == '' || $precoRaw == '') {
            $_SESSION['mensagem_erro'] = "Falha ao adicionar novo serviço. Preencha todos os campos.";
            header("Location: /dashboard");
            exit;
        }

        $val_formatado = $this->formatarPreco($precoRaw);

        // var_dump($val_formatado); die(); // so pra testar o valor

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
        $this->validarCsrf();

        $idServico = (int)($_POST['id'] ?? 0);

        if ($idServico === 0) {
            $_SESSION['mensagem_erro'] = "Serviço inválido.";
            header("Location: /dashboard");
            exit;
        }

        $dados_serv = $this->model->buscarPorId($idServico);

        if (!$dados_serv || (int)$dados_serv['user_id_user'] !== $this->id_logado || !empty($dados_serv['finished_at'])) {
            $_SESSION['mensagem_erro'] = "Serviço não encontrado, já finalizado ou você não tem permissão.";
            header("Location: /dashboard");
            exit;
        }

        $val = (float)$dados_serv['price'];
        $comissao = 0;

        // calculo da comissao do funcionario
        if ($val > 10000.00) {
            $comissao = $val * 0.20; 
        } else {
            if ($val > 1000.00) {
                $comissao = $val * 0.10;
            } else {
                $comissao = $val * 0.05;
            }
        }

        $data_hj = date('Y-m-d H:i:s');

        if ($this->model->finalizarServico($idServico, $data_hj)) {
            $email_cli = $dados_serv['email_usuario'] ?? '';
            
            if ($email_cli != '') {
                $assunto = "Serviço Finalizado";
                $msg = "O serviço foi finalizado. Sua comissão: R$ " . number_format($comissao, 2, ',', '.');
                $headers = "From: sistema@jminformatica.com\r\n";
                @mail($email_cli, $assunto, $msg, $headers);
            }

            $_SESSION['mensagem_sucesso'] = "Serviço finalizado! E-mail enviado. Comissão: R$ " . number_format($comissao, 2, ',', '.');
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao gravar a finalização no banco de dados.";
        }

        header("Location: /dashboard");
        exit;
    }

    public function editar() 
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $servico = $this->model->buscarPorId($id);
       /* // --- Debuguinho ---
        echo "<pre>";
        echo "ID recebido via GET: " . $id . "\n";
        echo "Resultado do buscarPorId: \n";
        print_r($servico);
        echo "</pre>";
        die();
        // -------------------*/
        if (!$servico || (int)$servico['user_id_user'] !== $this->id_logado) {
            $_SESSION['mensagem_erro'] = "Você não tem permissão para alterar o serviço de outro usuário.";
            header("Location: /dashboard");
            exit;
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require_once __DIR__ . '/../Views/editar_servico.php';
    }

    public function atualizar() 
    {
        $this->validarCsrf();

        $id_serv = (int)($_POST['id_service'] ?? 0);
        $desc = trim($_POST['descricao'] ?? '');
        $precoRaw = trim($_POST['preco'] ?? '');

        $val_formatado = $this->formatarPreco($precoRaw);

        $dados = $this->model->buscarPorId($id_serv);
        
        if (!$dados || (int)$dados['user_id_user'] !== $this->id_logado) {
            $_SESSION['mensagem_erro'] = "Acesso negado: você não pode alterar o serviço de outro usuário.";
            header("Location: /dashboard");
            exit;
        }

        if ($id_serv === 0 || $desc === '' || $val_formatado <= 0) {
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
        $this->validarCsrf();
        
        $idServico = (int)($_POST['id'] ?? 0);

        if ($idServico > 0) {
            $servico = $this->model->buscarPorId($idServico);
            
            if ($servico && (int)$servico['user_id_user'] === $this->id_logado) {
                if ($this->model->excluir($idServico)) {
                    $_SESSION['mensagem_sucesso'] = "Serviço excluído com sucesso!";
                } else {
                    $_SESSION['mensagem_erro'] = "Erro ao excluir o serviço.";
                }
            } else {
                $_SESSION['mensagem_erro'] = "Você não tem permissão para excluir o serviço de outro usuário.";
            }
        }

        header("Location: /dashboard");
        exit;
    }
}