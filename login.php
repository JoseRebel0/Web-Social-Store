<?php
// Inicia sessão
session_start();

// Verifica se o utilizador já está logado, se sim redireciona para a página index.php 
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('location: index.php');
    exit;
}

// Ficheiro de ligação à base de dados 
require_once 'config.php';

$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

   // Verifica se o username está vazio
   if(empty(trim($_POST['username']))){
         $username_err = 'Por favor introduz o nome de utilizador';
    } else {
         $username = trim($_POST['username']);
   }

    // Verifica se a password está vazia
    if(empty(trim($_POST['password']))){
        $password_err = 'Por favor introduz a palavra-passe';
    } else {
        $password = trim($_POST['password']);
    }

    // Valida as credenciais
    if(empty($username_err) && empty($password_err)){

        // Preparar o statement
        $sql = "SELECT id, username, password, cargo FROM utilizadores WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $cargo);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Inicia nova sessão se a autenticação for bem-sucedida
                            session_start();
                            
                            $_SESSION['loggedin'] = true;
                            $_SESSION['id'] = $id;
                            $_SESSION['username'] = $username;
                            $_SESSION['cargo'] = $cargo; // Armazena o cargo na sessão
                            
                            header("location: index.php");
                        } else{
                            $login_err = 'Nome de utilizador ou palavra-passe inválidos';
                        }
                    }
                } else{
                    $login_err = 'Nome de utilizador ou palavra-passe inválidos';
                }
            } else{
                echo "Algo correu mal. Por favor tente novamente.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    // Fechar a conexão
    mysqli_close($conn);
}
?>



<!DOCTYPE html>

<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Login</title>
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

      .card-body {
    align-content: center;
  }
    </style>
  </head>
  <body  class=" d-flex flex-column">
    <script src="./dist/js/demo-theme.min.js?1684106062"></script>
    <div class="page page-center">
      <div class="container container-tight py-4">

        <div class="card card-md">
          <div class="card-body i">

          <div style="width: 100%; text-align: center;">
            <img src="pontovermelho.jpg" alt="logo" class="mb-4" style="width: 300px;">
          </div>
        
            <h2 class="h2 text-center mb-4">Inicia sessão na tua conta</h2>

            <br>


            <?php
              if (!empty($showError)) {
                  echo $showError;
              }
            ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off" >
              
              <div class="mb-3">
                <label class="form-label" for="username">Nome de utilizador</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Nome de utilizador" autocomplete="off" >
              </div>

              <span><?php echo $username_err; ?></span>

              <br>

              <div class="mb-2">
                <label class="form-label" for="password">
                  Palavra-passe
                  <!--<span class="form-label-description">
                    <a href="./forgot-password.html">I forgot password</a>
                  </span>-->
                </label>
                <div class="input-group input-group-flat">
                  <input type="password" class="form-control" id="password" name="password" placeholder="Palavra-passe"  autocomplete="off" >
                  <span class="input-group-text">
                    <a class="link-secondary" title="Show password" data-bs-toggle="tooltip"><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                      <svg xmlns="http://www.w3.org/2000/svg" id="eyeicon" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                    </a>
                  </span>
                </div>
              </div>
              <span><?php echo $password_err; ?></span>
              <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100" value="Login">Iniciar sessão</button>
              </div>
            </form>
          </div>

        </div>
        <h4 class="h4 text-center mb-4" style="margin-top: 50px; color: grey;">Site desenvolvido por José Rebelo</h4>

      </div>
    </div>

    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="./dist/js/tabler.min.js?1684106062" defer></script>
    <script src="./dist/js/demo.min.js?1684106062" defer></script>

    <script>

      const eyeicon = document.getElementById('eyeicon');
      const password = document.getElementById('password')


      eyeicon.addEventListener('click', function(){
        if(password.type === 'password'){
          password.type = 'text';
        }else{
          password.type = 'password';
        }
      })

    </script>
  </body>
</html>