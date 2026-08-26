<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - JM Informática</title>
    <link rel="stylesheet" href="/css/style.css">
    
    <style>
        /* Ajuste fino para os filtros híbridos caberem lado a lado */
        .wf-filtros { flex-wrap: wrap; align-items: flex-end; }
        .wf-filtros .campo-filtro { display: flex; flex-direction: column; gap: 5px; }
        .wf-filtros .campo-filtro label { font-size: 12px; font-weight: bold; color: #555; }
        .wf-filtros input, .wf-filtros select { 
            border: 1px solid #111; padding: 10px; border-radius: 0; 
            font-size: 14px; width: 160px; height: 40px; box-sizing: border-box; background: #fff;
        }
        .wf-filtros button { height: 40px; margin-bottom: 0; }
        
        /* css maroto pros botoes de post parecerem link */
        .btn-acao-form { background: none; border: none; padding: 0; font: inherit; cursor: pointer; text-decoration: underline; }
    </style>
</head>
<body>

<div class="wf-container">
    
    <!-- MENU LATERAL -->
    <aside class="wf-sidebar">
        <div class="user-info">
            Logado como: <strong><?php echo htmlspecialchars($_SESSION['usuario']['name']); ?></strong>
        </div>
        <div class="user-data">
            Data atual: <?php echo date('d/m/Y'); ?>
        </div>
        
        <a href="/servico/novo" class="link-cadastrar">Cadastrar Serviço</a>
        
        <!-- O margin-top: auto no CSS joga o Sair para o final -->
        <a href="/sair" class="link-sair">Sair do Sistema</a>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="wf-main">
        
        <!-- monstrando avisos na tela de suceso ou erro -->
        <?php if(isset($_SESSION['mensagem_sucesso']) && $_SESSION['mensagem_sucesso'] != '') { ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border: 1px solid #111; margin-bottom: 20px;">
                <?php echo $_SESSION['mensagem_sucesso']; ?>
            </div>
            <?php unset($_SESSION['mensagem_sucesso']); ?>
        <?php } ?>

        <?php if(isset($_SESSION['mensagem_erro']) && $_SESSION['mensagem_erro'] != '') { ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #111; margin-bottom: 20px;">
                <?php echo $_SESSION['mensagem_erro']; ?>
            </div>
            <?php unset($_SESSION['mensagem_erro']); ?>
        <?php } ?>

        <h1>DASHBOARD</h1>

        <!-- BLOCOS SUPERIORES (Nossos dados com a fonte do Wireframe) -->
        <div class="wf-lists-row">
            <div class="wf-list-col">
                <h2>Valor Total Prestado</h2>
                <p style="font-size: 28px; font-weight: bold; margin-top: 15px; color: #111;">
                    R$ <?= number_format((float)($valorTotal ?? 0), 2, ',', '.') ?>
                </p>
            </div>
            
            <div class="wf-list-col">
                <h2>Serviços Pendentes</h2>
                <ul>
                    <?php if(empty($servicosPendentes)) { ?>
                        <li>Nenhum serviço pendente.</li>
                    <?php } else { ?>
                        <?php 
                        // o model ja ta trazendo so os 3 ultimos, so rodar
                        foreach($servicosPendentes as $pend){ 
                        ?>
                            <li><?php echo $pend['id_service']; ?> - <?php echo htmlspecialchars($pend['description']); ?></li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        </div>
        
        <!-- FILTROS COM VALIDAÇÃO JS E ALINHAMENTO -->
        <form action="/dashboard" method="GET" class="wf-filtros" onsubmit="return validarFiltros()">
            
            <div class="wf-filtros-inputs">
                <div class="campo-filtro">
                    <label>Data Inicial</label>
                    <input type="date" name="data_inicial" id="data_inicial" value="<?php echo isset($_GET['data_inicial']) ? htmlspecialchars($_GET['data_inicial']) : ''; ?>">
                </div>
                <div class="campo-filtro">
                    <label>Data Final</label>
                    <input type="date" name="data_final" id="data_final" value="<?php echo isset($_GET['data_final']) ? htmlspecialchars($_GET['data_final']) : ''; ?>">
                </div>
                <div class="campo-filtro">
                    <label>Serviço</label>
                    <input type="text" name="nome_servico" placeholder="Ex: Limpeza" value="<?php echo isset($_GET['nome_servico']) ? htmlspecialchars($_GET['nome_servico']) : ''; ?>">
                </div>
                <div class="campo-filtro">
                    <label>Usuário</label>
                    <input type="text" name="nome_usuario" placeholder="Nome" value="<?php echo isset($_GET['nome_usuario']) ? htmlspecialchars($_GET['nome_usuario']) : ''; ?>">
                </div>
                <div class="campo-filtro">
                    <label>Status</label>
                    <select name="status">
                        <option value="">Todos</option>
                        <option value="PENDENTE" <?php if(isset($_GET['status']) && $_GET['status'] == 'PENDENTE') echo 'selected'; ?>>Pendentes</option>
                        <option value="FINALIZADO" <?php if(isset($_GET['status']) && $_GET['status'] == 'FINALIZADO') echo 'selected'; ?>>Finalizados</option>
                    </select>
                </div>
            </div>
            
            <div class="wf-filtros-actions">
                <button type="submit" class="btn-filtrar">Filtrar</button>
                <a href="/dashboard" class="btn-limpar">Limpar</a>
            </div>
        </form>            
        
        <?php
        // var_dump($servicos); die(); // debug da grid de listagem
        ?>

        <table class="wf-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>DESCRIÇÃO</th>
                    <th>STATUS</th>
                    <th>VALOR</th>
                    <th>NOME USUÁRIO</th>
                    <th>AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($lista_serv)) { ?>
                    <tr><td colspan="6" style="text-align: center; font-weight: normal;">Nenhum serviço encontrado.</td></tr>
                <?php } else { ?>
                    <?php foreach($lista_serv as $serv) { ?>
                        
                        <?php 
                            // checa se ja finalizou pra montar o texto do status
                            $ta_pendente = false;
                            
                            if($serv['finished_at'] == null || $serv['finished_at'] == ""){
                                $txt_status = "PENDENTE";
                                $ta_pendente = true;
                            }else{
                                 $data_f = date('d/m/Y', strtotime($serv['finished_at']));
                                 $txt_status = "FINALIZADO <span style='font-size: 11px; color: #666; font-weight: normal;'>&bull; " . $data_f . "</span>";
                            }
                        ?>
                        
                        <tr>
                            <td><?php echo $serv['id_service']; ?></td>
                            <td><?php echo htmlspecialchars($serv['description']); ?></td>
                            <td><?php echo $txt_status; ?></td>
                            <td>R$ <?php echo number_format($serv['price'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($serv['nome_usuario']); ?></td>
                            <td>
                                <?php if($ta_pendente){ ?>
                                    <!-- coloquei form aqui p mandar post com csrf e n dar vulnerabilidade no get -->
                                    <form action="/servico/finalizar" method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="id" value="<?php echo $serv['id_service']; ?>">
                                        <button type="submit" class="wf-link-acao btn-acao-form" style="color: green; font-weight: bold;">Finalizar</button>
                                    </form>
                                <?php }else{ ?>
                                     <span class="wf-link-acao" style="color: #999999; cursor: not-allowed; font-weight: bold;">Concluído</span>
                                <?php } ?>
                                
                                <a href="/servico/editar?id=<?php echo $serv['id_service']; ?>" class="wf-link-acao">Alterar</a>
                                
                                 <!-- mudei pra post tb pq deletar no get os caras apagam pela url -->
                                 <form action="/servico/excluir" method="POST" style="display:inline;" onsubmit="return confirm('Excluir este serviço?');">
                                     <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                     <input type="hidden" name="id" value="<?php echo $serv['id_service']; ?>">
                                     <button type="submit" class="wf-link-acao btn-acao-form" style="color: #cc0000;">Excluir</button>
                                 </form>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>

    </main>
</div>
<script src="/js/scripts.js"></script>
</body>
</html>