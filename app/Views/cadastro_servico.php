<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Serviço</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="login-container">
    <h2>Cadastrar Novo Serviço</h2>
    <form action="/servico/salvar" method="POST">
        <div class="form-group">
            <input type="text" name="descricao" placeholder="descrição">
        </div>
        <div class="form-group">
            <!-- step="0.01" para o HTML aceitar centavos -->
            <input type="number" step="0.01" name="preco" placeholder="preço">
        </div>
        <div class="actions">
            <button type="submit" class="btn-entrar" style="width: 100%;">Cadastrar</button>
        </div>
    </form>
</div>
</body>
</html>