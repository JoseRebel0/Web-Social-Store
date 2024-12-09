<?php

//inicia sessão
session_start();

require_once 'check_status.php';

// verifica se o utilizador está logado, se não retorna para a página login.php
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
  header("location: login.php");
  exit;
}

$page = 'creditos';

?>


<!doctype html>

<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Créditos - <?php echo $nome_do_site; ?></title>
    <!-- CSS files -->
    <link href="./dist/css/tabler.min.css?1684106062" rel="stylesheet"/>
    <link href="./dist/css/tabler-flags.min.css?1684106062" rel="stylesheet"/>
    <link href="./dist/css/tabler-payments.min.css?1684106062" rel="stylesheet"/>
    <link href="./dist/css/tabler-vendors.min.css?1684106062" rel="stylesheet"/>
    <link href="./dist/css/demo.min.css?1684106062" rel="stylesheet"/>
    <style>
      @import url('https://rsms.me/inter/inter.css');
      :root {
      	--tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
      }
      body {
      	font-feature-settings: "cv03", "cv04", "cv11";
      }
    </style>
  </head>
  <body >
    <script src="./dist/js/demo-theme.min.js?1684106062"></script>
    <div class="page">

      <!-- Header -->
      <?php

        include 'header.php';
      ?>

      <!-- Navbar -->

      
      <?php

        include 'navbar.php';
      ?>

        <!-- Page body -->
        
        <div class="page-body">
          <div class="container-xl">
            <div class="card">
              <div class="card-body">
               
                 <p>Este site foi desenvolvido por <a style="text-decoration:none" href="https://www.github.com/JoseRebel0">José Rebelo</a> no ambito do seu estágio curricular no departamento de informática da Cruz Vermelha de Braga.</p>
                 <p>Todas as instruções para a construção deste projeto foram fornecidos pelo Ponto Vermelho, o destinatário do mesmo.</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer -->

        <?php

          include 'footer.php';

        ?>

      </>
    </div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="./dist/js/tabler.min.js?1684106062" defer></script>
    <script src="./dist/js/demo.min.js?1684106062" defer></script>
  </body>
</html>