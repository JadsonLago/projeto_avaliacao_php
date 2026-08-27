<div class="wf-container">
    <!-- MENU LATERAL -->
    <aside class="wf-sidebar">
        <a href="/dashboard" class="link-cadastrar" style="margin-bottom: 25px;">JM Informática</a>
        
        <?php
        // pega os dados do usuario da sessao pra mostrar na tela
        // arrumar isso aqui dps pq as vezes o array vem vazio e da notice
        $nome_usuario = "Usuário";
        if(isset($_SESSION['usuario']['name'])) {
            $nome_usuario = $_SESSION['usuario']['name'];
        }
        // var_dump($_SESSION); die(); // so pra testar se a sessao ta vindo cheia
        ?>

        <div class="user-info">
            Logado como: <strong><?php echo htmlspecialchars($nome_usuario); ?></strong>
        </div>
        
        <?php
          // formatando a data no padrao brasileiro padrao
          $data_hoje = date('d/m/Y');
        ?>
        <div class="user-data">
            Data atual: <?php echo $data_hoje; ?>
        </div>
        
        <a href="/servico/novo" class="link-cadastrar">Cadastrar Serviço</a>
        
        <a href="/sair" class="link-sair">Sair do Sistema</a>
    </aside>
