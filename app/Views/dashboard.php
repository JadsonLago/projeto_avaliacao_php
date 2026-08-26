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
        <p>Logado como: <strong><?= htmlspecialchars((string)$_SESSION['usuario']['name'], ENT_QUOTES, 'UTF-8') ?></strong></p>
        <p class="data-atual">Data de hoje: <?= htmlspecialchars((string)$dataAtual, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="botoes-topo">
        <a href="/servico/novo" class="btn-entrar">Cadastrar Serviço</a>
        <a href="/sair" class="btn-sair">Sair</a>
    </div>
</header>

<main class="conteudo-dashboard">

    <div class="destaques">
        <div class="card-destaque">
            <h3>Valor Total Prestado</h3>
            <p class="valor">R$ <?= number_format((float)$valorTotal, 2, ',', '.') ?></p>
        </div>
        
        <div class="card-destaque">
            <h3>Últimos Pendentes</h3>
            <ul>
                <?php if (empty($servicosPendentes)): ?>
                    <li>Nenhum serviço pendente.</li>
                <?php else: ?>
                    <?php foreach ($servicosPendentes as $pendente): ?>
                        <li>ID <?= (int)$pendente['id_service'] ?> - <?= htmlspecialchars((string)$pendente['description'], ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
    <h2>DASHBOARD</h2>

    <?php if (!empty($_SESSION['mensagem_sucesso'])): ?>
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px;">
            <?= htmlspecialchars((string)$_SESSION['mensagem_sucesso'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['mensagem_sucesso']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['mensagem_erro'])): ?>
        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px;">
            <?= htmlspecialchars((string)$_SESSION['mensagem_erro'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['mensagem_erro']); ?>
    <?php endif; ?>
    
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
            <?php if (empty($servicos)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Nenhum serviço encontrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($servicos as $servico): ?>
                    <?php 
                        $isPendente = empty($servico['finished_at']);
                        $statusText = $isPendente ? "PENDENTE" : "FINALIZADO";
                        $statusColor = $isPendente ? "#d97706" : "#16a34a";
                    ?>
                    <tr>
                        <td><?= (int)$servico['id_service'] ?></td>
                        <td><?= htmlspecialchars((string)$servico['description'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="color: <?= $statusColor ?>; font-weight: bold;"><?= $statusText ?></td>
                        <td>R$ <?= number_format((float)$servico['price'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars((string)$servico['nome_usuario'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="/servico/editar?id=<?= (int)$servico['id_service'] ?>">Alterar</a> |
                            <a href="/servico/excluir?id=<?= (int)$servico['id_service'] ?>" onclick="return confirm('Deseja realmente excluir este serviço?');">Excluir</a>
                            <?php if ($isPendente): ?>
                                | <a href="/servico/finalizar?id=<?= (int)$servico['id_service'] ?>" style="color: green; font-weight: bold;">Finalizar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</main>

</body>
</html>
