<?php 
$tituloPagina = "Novo Serviço - JM Informática";
require_once __DIR__ . '/partials/head.php';
//require_once __DIR__ . '/partials/sidebar.php';

// criandoum token meio basico pra n deixar sem protecao
if(empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = md5(time());
}
?>

    <main class="wf-main">
<div class="login-container">
    <h2>Sistema de Controle de Serviços</h2>
    <?php if(isset($_SESSION['mensagem_sucesso']) && $_SESSION['mensagem_sucesso'] != '') { ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; border: 1px solid #111; margin-bottom: 20px;">
            <?php echo $_SESSION['mensagem_sucesso']; ?>
        </div>
        <?php unset($_SESSION['mensagem_sucesso']); ?>
    <?php } ?>
    
    <?php 
    // mostra a msg de erro na tela caso erre os dados
    if (isset($erro) && $erro != '') { 
    ?>
        <div class="alert"><?php echo htmlspecialchars($erro); ?></div>
    <?php } ?>

    <form action="/logar" method="POST">
        
        <?php 
        // mandando o token de seguranca q ta na sessao pro post
        if(isset($_SESSION['csrf_token'])){ 
        ?>
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <?php } ?>

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
</main>

<?php 
// var_dump($_POST); die(); // so p ver se o form ta postando os dados certinho
require_once __DIR__ . '/partials/footer.php'; 
?>