<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - JM Informática</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<header class="topo-dashboard">
    <div class="info-usuario">
        <p>Logado como: <strong><?php echo htmlspecialchars($_SESSION['usuario']['name']); ?></strong></p>
        <p class="data-atual">Data de hoje: <?php echo $dataAtual; ?></p>
    </div>
    <div class="botoes-topo">
        <a href="/servico/novo" class="btn-entrar">Cadastrar Serviço</a>
        <a href="/sair" class="btn-sair">Sair</a>
    </div>
</header>

<main class="conteudo-dashboard">
    <h2>DASHBOARD</h2>
    
    <table class="tabela-servicos">
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
            <?php if(empty($servicos)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Nenhum serviço encontrado.</td>
                </tr>
            <?php else: ?>
                
            <?php endif; ?>
        </tbody>
    </table>
</main>

</body>
</html>