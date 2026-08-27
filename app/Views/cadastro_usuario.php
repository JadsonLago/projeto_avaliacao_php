<?php 
$tituloPagina = "Cadastro de Usuário - JM Informática";
require_once __DIR__ . '/partials/head.php';
?>

<main class="wf-main">
    <div class="login-container">
        <h2>Criar Usuário</h2>

        <?php if(isset($_SESSION['mensagem_erro']) && $_SESSION['mensagem_erro'] != '') { ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['mensagem_erro']; ?>
            </div>
            <?php unset($_SESSION['mensagem_erro']); ?>
        <?php } ?>

        <!-- form de cadastro modificado pq tava dando erro no post antes -->
        <form action="/usuario/salvar" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">

            <div class="form-group">
                <input type="text" name="nome_completo" placeholder="Nome Completo" required>
            </div>

              <div class="form-group">
                <input type="email" name="email_usuario" placeholder="E-mail" required>
            </div>

            <div class="form-group">
                <input type="password" name="senha_usuario" placeholder="Senha" required>
            </div>

            <div class="actions">
                <button type="submit" class="btn-entrar">Cadastrar</button>
                  <a href="/login" class="link-cadastrar">Voltar ao Login</a>
            </div>
        </form>
    </div>
</main>

<?php 
// testar dps se o footer ta carregando direito aqui
require_once __DIR__ . '/partials/footer.php'; 
?>