<?php

define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vendas');
   
$conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

if($conn === false){
    die("Falha na conexão: " . mysqli_connect_error());
}

$nome_do_site="Ecomendas - Ponto Vermelho";

?>