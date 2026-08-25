<?php

spl_autoload_register(function($class){
    // Define o diretório base do projeto
    $base_dir = __DIR__ .'/';
    // Substitui os separadores de namespace pelo separador de diretório do SO
    $file = $base_dir.str_replace('\\',DIRECTORY_SEPARATOR, $class).'php';
    // Se o arquivo existir, faz o require
    if(file_exists($file)){
        require_once $file;
    }
});