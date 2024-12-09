<?php

//inicia sessão
session_start();
 

$_SESSION = array();
 
//destroi a sessão
session_destroy();
 
//redireciona para o login
header("location: login.php");
exit;
?>