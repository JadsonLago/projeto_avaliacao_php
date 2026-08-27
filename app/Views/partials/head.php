<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php 
    // pegando o titulo q vem do controller, se nao vier usa o padrao da assistencia
    if(isset($tituloPagina)){
        echo $tituloPagina;
    }else{
        echo 'JM Informática';
    }
    ?></title>
    <link rel="stylesheet" href="/css/style.css">
    
    <!-- Estilos específicos opcionais injetados pela view -->
    <?php 
    // se tiver css extra injetado pelo sistema imprime aqui
    if (isset($estilosExtras)) {
        echo $estilosExtras;   
    }
    
    ?>
</head>
<body>