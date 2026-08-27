<?php 
// arrumar o titulo da pagina antes de chamar o head
$tituloPagina = "Dashboard - JM Informática";

// estilo do painel pra nao quebrar os filtros
$estilosExtras = '
    <style>
        .wf-filtros { flex-wrap: wrap; align-items: flex-end; }
        .wf-filtros .campo-filtro { display: flex; flex-direction: column; gap: 5px; }
        .wf-filtros .campo-filtro label { font-size: 12px; font-weight: bold; color: #555; }
        .wf-filtros input, .wf-filtros select { 
            border: 1px solid #111; padding: 10px; border-radius: 0; 
            font-size: 14px; width: 160px; height: 40px; box-sizing: border-box; background: #fff;
        }
        .wf-filtros button { height: 40px; margin-bottom: 0; }
        .btn-acao-form { background: none; border: none; padding: 0; font: inherit; cursor: pointer; text-decoration: underline; }
    </style>
';

require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/sidebar.php';
?>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="wf-main">
        
        <!-- checa se tem msg de sucesso pra mostrar -->
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

        <!-- BLOCOS SUPERIORES -->
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
                        // traz so os pends da lista rapida
                        foreach($servicosPendentes as $pend){ 
                        ?>
                            <li><?php echo $pend['id_service']; ?> - <?php echo htmlspecialchars($pend['description']); ?></li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        </div>
        
        <!-- FORMULARIO DE FILTROS -->
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
        // var_dump($lista_serv); die(); // conferir se o array ta vindo certo
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
                            // ve se ta pendente ou finalizado pra formatar a linha
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
                                    <form action="/servico/finalizar" method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                        <input type="hidden" name="id" value="<?php echo $serv['id_service']; ?>">
                                        <button type="submit" class="wf-link-acao btn-acao-form" style="color: green; font-weight: bold;">Finalizar</button>
                                    </form>
                                <?php }else{ ?>
                                    <span class="wf-link-acao" style="color: #999999; cursor: not-allowed; font-weight: bold;">Concluído</span>
                                <?php } ?>
                                
                                <a href="/servico/editar?id=<?php echo $serv['id_service']; ?>" class="wf-link-acao">Alterar</a>
                                
                                <form action="/servico/excluir" method="POST" style="display:inline;" onsubmit="return confirm('Excluir este serviço?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
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
<?php 
require_once __DIR__ . '/partials/footer.php'; 
?>