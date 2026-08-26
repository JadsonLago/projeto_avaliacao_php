<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Serviço</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="login-container">
    <h2>Cadastrar Novo Serviço</h2>
    
    <form action="/servico/salvar" method="POST">
        <!-- 1. CAMPO OCULTO CSRF: Essencial para o ServicoController aceitar o envio -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

        <div class="form-group">
            <label for="descricao" style="display:none;">Descrição</label>
            <input 
                type="text" 
                id="descricao"
                name="descricao" 
                placeholder="Descrição do serviço" 
                required 
                maxlength="255"
            >
        </div>

        <div class="form-group">
            <label for="preco" style="display:none;">Preço</label>
            <!-- 2. Alterado para text para aceitar a formatação de vírgula que a sua regex limpa -->
            <input 
                type="text" 
                id="preco"
                name="preco" 
                placeholder="Preço (Ex: 150,00)" 
                required
            >
        </div>

        <div class="actions" style="justify-content: flex-start; gap: 15px;">
            <button type="submit" class="btn-entrar">Salvar</button>
            <a href="/dashboard" class="btn-voltar">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
