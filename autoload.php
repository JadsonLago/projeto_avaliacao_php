<?php

spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/';
    
    // Força a barra normal (/) no lugar da invertida (\) para evitar bugs de caminho no Windows
    $class_path = str_replace('\\', '/', $class);
    
    $file = $base_dir . $class_path . '.php';

    if (file_exists($file)) {
        require_once $file;
    } else {
        // O NOSSO ESPIÃO: Se ele não achar o arquivo, vai estourar na tela o caminho exato que ele tentou buscar
        echo "<div style='background: red; color: white; padding: 10px; z-index: 9999;'>";
        echo "<strong>Erro no Autoload:</strong> O PHP tentou carregar a classe <b>{$class}</b>, mas não encontrou o arquivo no caminho:<br>";
        echo "<code>{$file}</code>";
        echo "</div>";
    }
});