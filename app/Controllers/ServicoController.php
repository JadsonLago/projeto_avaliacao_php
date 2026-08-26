<?php
declare(strict_types=1);

namespace app\Controllers;

use app\Models\Servico;

class ServicoController {
    
    public function novo(): void {
        require_once __DIR__ . '/../Views/cadastro_servico.php';
    }

    public function salvar(): void {
        $descricao = trim((string)($_POST['descricao'] ?? ''));
        $precoRaw = trim((string)($_POST['preco'] ?? ''));
        $idUsuario = (int)($_SESSION['usuario']['id_user'] ?? 0);

        if (empty($descricao) || empty($precoRaw) || $idUsuario === 0) {
            $_SESSION['mensagem_erro'] = "Falha ao adicionar novo serviço. Preencha todos os campos.";
            header("Location: /dashboard");
            exit;
        }

        // Remove tudo que não for número, vírgula ou ponto, e depois padroniza para decimal
        $precoLimpo = preg_replace('/[^0-9,.]/', '', $precoRaw);
        $precoFormatado = (float)str_replace(',', '.', $precoLimpo);

        $servicoModel = new Servico();
        
        if ($servicoModel->criarServico($descricao, $precoFormatado, $idUsuario)) {
            $_SESSION['mensagem_sucesso'] = "Serviço adicionado com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Ocorreu um erro ao salvar no banco.";
        }
        
        header("Location: /dashboard");
        exit;
    }
    public function finalizar(): void {
        $idServico = (int)($_GET['id'] ?? 0);

        if ($idServico === 0) {
            $_SESSION['mensagem_erro'] = "Serviço inválido.";
            header("Location: /dashboard");
            exit;
        }

        // 1. SEGURANÇA: Busca o serviço no banco para pegar o preço real e o e-mail
        $servicoModel = new Servico();
        $servico = $servicoModel->buscarPorId($idServico);

        if (!$servico || !empty($servico['finished_at'])) {
            $_SESSION['mensagem_erro'] = "Serviço não encontrado ou já finalizado.";
            header("Location: /dashboard");
            exit;
        }

        $preco = (float)$servico['price'];
        
        // 2. CALCULO DA COMISSÃO
        $porcentagem = 0.05; // Até 1000
        if ($preco > 10000.00) $porcentagem = 0.20; // Acima de 10000
        elseif ($preco > 1000.00) $porcentagem = 0.10; // Acima de 1000

        $comissao = $preco * $porcentagem;
        $dataFinalizacao = date('Y-m-d H:i:s');

        // Atualiza no banco
        if ($servicoModel->finalizarServico($idServico, $comissao, $dataFinalizacao)) {
            
            // 3. ENVIO DE E-MAIL
            $para = $servico['email_usuario'];
            $assunto = "Serviço Finalizado";
            $mensagem = "O serviço foi finalizado. Sua comissão: R$ " . number_format($comissao, 2, ',', '.');
            $cabecalhos = "From: sistema@jminformatica.com\r\n";
            @mail($para, $assunto, $mensagem, $cabecalhos);

            $_SESSION['mensagem_sucesso'] = "Serviço finalizado! Comissão: R$ " . number_format($comissao, 2, ',', '.');
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao finalizar o serviço.";
        }

        header("Location: /dashboard");
        exit;
    }

}
