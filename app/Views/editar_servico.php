<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Serviço</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="login-container">
    <h2>Alterar Serviço</h2>

    <?php 
    // monstrando o erro se voltar do controller
    if(isset($_SESSION['mensagem_erro']) && $_SESSION['mensagem_erro'] != '') { 
    ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['mensagem_erro']; ?>
        </div>
        <?php unset($_SESSION['mensagem_erro']); ?>
    <?php } ?>

    <form action="/servico/atualizar" method="POST">
        
        <!-- mandando token e id escondidos pro post -->
        <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
        <input type="hidden" name="id_service" value="<?php echo $servico['id_service']; ?>">

        <div class="form-group">
            <input type="text" name="descricao" placeholder="descrição" value="<?php echo htmlspecialchars($servico['description']); ?>" required>
        </div>
        
        <div class="form-group">
             <!-- tem q formatar com ponto pro input number do html nao bugar -->
            <input type="number" step="0.01" name="preco" placeholder="preço" value="<?php echo number_format($servico['price'], 2, '.', ''); ?>" required>
        </div>
        
        <!-- botoes alinhados pra esquerda dps ver se jogo pro css -->
        <div class="actions" style="justify-content: flex-start; gap: 15px;">
            <button type="submit" class="btn-entrar">Salvar</button>
            <a href="/dashboard" class="btn-voltar">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>