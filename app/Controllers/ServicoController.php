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
}
