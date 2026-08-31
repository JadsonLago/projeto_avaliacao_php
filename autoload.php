<?php

spl_autoload_register(function($classe) 
{
    $dir_base = __DIR__ . '/';
    
    // ajustando a barra p/ n dar problema de caminho
     $caminho_classe=str_replace('\\', '/', $classe);
    
    $arquivo = $dir_base . $caminho_classe . '.php';

    // echo $arquivo; die(); // testando o caminho dps tirar

    if (file_exists($arquivo)) {
        require_once $arquivo;
    } else {
        // meu debug pra qnd ele n achar o arquivo da classe (tipo qnd esquece de criar)
        echo "<div style='background: red; color: white; padding: 10px; z-index: 9999;'>";
        // coloquei htmlspecialchars por seguranca caso o nome venha bizarro
        echo "<strong>Erro no Autoload:</strong> O PHP tentou carregar a classe <b>" . htmlspecialchars($classe) . "</b>, mas não encontrou o arquivo no caminho:<br>";
        echo "<code>" . htmlspecialchars($arquivo) . "</code>";
        echo "</div>";
        
        // para a execucao senao da erro fatal zuando a tela inteira
        die();
    }
});