<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Controle de Serviços</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="login-container">
    <h2>Sistema de Controle de Serviços</h2>
    
    <?php if (!empty($erro)): ?>
        <div class="alert"><?php echo htmlspecialchars((string)$erro, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form action="/logar" method="POST">
        
        <?php if (isset($_SESSION['csrf_token'])): ?>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <?php endif; ?>

        <div class="form-group">
            <input type="email" name="email" placeholder="email@email.com" required>
        </div>
        
        <div class="form-group">
            <input type="password" name="senha" placeholder="*************" required>
        </div>
        
        <div class="actions">
            <button type="submit" class="btn-entrar">Entrar</button>
            <a href="/cadastro" class="link-cadastrar">Cadastrar usuário</a>
        </div>
    </form>
</div>

</body>
</html>