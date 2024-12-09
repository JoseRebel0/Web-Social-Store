<?php

// Inicia sessão
session_start();

require_once 'check_status.php';

// Verifica se o utilizador está logado, se não retorna para a página login.php
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
  header("location: login.php");
  exit;
}

$role = htmlspecialchars($_SESSION["cargo"]);

if($role != 'Administrador'){
  header("location: index.php");
  exit;
}

$page = 'form-utilizadores';

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){

  // Valida o username
  if(empty(trim($_POST["username"]))){
      echo "<script type='text/javascript'> alert('Por favor introduza o nome da entidade.') </script>";
      $username_err = "Por favor, introduza um username.";
  } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
      echo "<script type='text/javascript'> alert('O nome da entidade só pode conter letras, números e sublinhados!') </script>";
      $username_err = "O username só pode conter letras, números e sublinhados.";
  } else{
     
      $sql = "SELECT id FROM utilizadores WHERE username = ?";
      if($stmt = mysqli_prepare($conn, $sql)){
          mysqli_stmt_bind_param($stmt, "s", $param_username);
          $param_username = trim($_POST["username"]);
          if(mysqli_stmt_execute($stmt)){
              mysqli_stmt_store_result($stmt);
              if(mysqli_stmt_num_rows($stmt) == 1){
                  $username_err = "Este nome de entidade já está em uso.";
                  echo "<script type='text/javascript'> alert('Nome de entidade em uso.') </script>";
              } else{
                  $username = trim($_POST["username"]);
              }
          } else{
              echo "Oops! Algo deu errado. Por favor, tente novamente mais tarde.";
          }
          mysqli_stmt_close($stmt);
      }
  }

  // Valida a password
  if(empty(trim($_POST["password"]))){
      echo "<script type='text/javascript'> alert('Por favor, introduza uma palavra-passe.') </script>";
      $password_err = "Por favor, introduza uma password.";     
  } elseif(strlen(trim($_POST["password"])) < 6){
      echo "<script type='text/javascript'> alert('A palavra-passe deve conter pelo menos 6 caracteres.') </script>";
      $password_err = "A password deve ter pelo menos 6 caracteres.";
  } else{
      $password = trim($_POST["password"]);
  }

  // Valida a confirmação da password
  if(empty(trim($_POST["confirm_password"]))){
      echo "<script type='text/javascript'> alert('Por favor, confirme a palavra-passe.') </script>";
      $confirm_password_err = "Por favor, confirme a password.";     
  } else{
      $confirm_password = trim($_POST["confirm_password"]);
      if(empty($password_err) && ($password != $confirm_password)){
          echo "<script type='text/javascript'> alert('As palavras-passe não coincidem.') </script>";
          $confirm_password_err = "As passwords não coincidem.";
      }
  }

  // Verifica se há erros antes de inserir no banco de dados
  if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

      $param_password = password_hash($password, PASSWORD_DEFAULT);
      $email = $_POST['email'];
      $contacto = $_POST['contacto'];
      $nome_entidade = $_POST['nome_entidade'];
      $nome_de_contacto = $_POST['nome_de_contacto'];
      $morada = $_POST['morada'];
      $localidade = $_POST['localidade'];
      $cpostal = $_POST['cpostal']; 
      $cidade = $_POST['cidade'];
      $observacoes = $_POST['observacoes'];

      if(!empty($nome_entidade) && !empty($nome_de_contacto)){
          $sqlquery = "INSERT INTO utilizadores (username, password, nome_entidade, nome_de_contacto, contacto, email, morada, localidade, cpostal, cidade, observacoes, id_status, cargo, created_at)
          VALUES ('$username', '$param_password', '$nome_entidade', '$nome_de_contacto', '$contacto', '$email', '$morada', '$localidade', '$cpostal', '$cidade', '$observacoes', '1', 'Entidade', now())";
          
          if(mysqli_query($conn, $sqlquery)){
              echo "<script type='text/javascript'> alert('Entidade registada com sucesso!') </script>";
          } else {
              echo "<script type='text/javascript'> alert('Falha ao registar a entidade.') </script>";
          }
      } else {
          echo "<script type='text/javascript'> alert('Por favor, preencha todos os campos obrigatórios.') </script>";
      }
  }
}
?>


<!doctype html>

<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Form - Utilizadores</title>
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

      .title-butt {
        width: 100%;
        height: auto;
        display: flex;
        justify-content: space-between;
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

      <div class="page-wrapper">
        <!-- Page header -->
        <div class="page-header d-print-none">
          <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col">
                <h2 class="page-title">
                  Criar nova entidade
                </h2>
              </div>
            </div>
          </div>
        </div>
        <!-- Page body -->
        <div class="page-body">
          <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <form class="card" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" name="userform">

                        <div class="card-header">
                            <h4 class="card-title">Nova entidade</h4>
                        </div>
                      <div class="card-body">

                         <br>
                        
                         <div class="row row-cards">

                        <!-- Nestes 3 mantem-se o processo normal de registo -->
                         
                          <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                              <label class="form-label required">Nome de utilizador</label>
                              <input type="text" name="username" id="username" class="form-control" placeholder="Nome de utilizador" required <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                              <span class="invalid-feedback"><?php echo $username_err; ?></span>
                            </div>
                          </div>


                          <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                              <label class="form-label required">Palavra-passe</label>
                              <input type="password" name="password" id="password" class="form-control" placeholder="Palavra-passe" required <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                              <span class="invalid-feedback"><?php echo $password_err; ?></span>
                            </div>
                          </div>


                          <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                              <label class="form-label required">Confirmar Palavra-passe</label>
                              <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirmar Palavra-passe" required <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                              <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                            </div>
                          </div>

                        </div>

                        <hr>

                        <br>


                        <div class="row row-cards">

                          <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                              <label class="form-label required" for="nome_entidade">Nome de entidade</label>
                              <input type="text" class="form-control" placeholder="Nome de entidade" id="nome_entidade" name="nome_entidade" required>
                            </div>
                          </div>


                          <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                              <label class="form-label required" for="nome_de_contacto">Nome de contacto</label>
                              <input type="text" class="form-control" placeholder="Nome de contacto" id="nome_de_contacto" name="nome_de_contacto" required>
                            </div>
                          </div>

                          <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                              <label class="form-label " for="contacto">Contacto</label>
                              <input type="tel" maxlength="9" minlength="9" class="form-control" placeholder="Contacto" id="contacto" name="contacto" >
                            </div>
                          </div>

                        </div>

                        <br>



                        <div class="row row-cards">

                          <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                              <label class="form-label " for="email">Email</label>
                              <input type="email" class="form-control" placeholder="Email" id="email" name="email" >
                            </div>
                          </div>


                        <div class="col-md-12">


                        <hr>

                        <br>


                        <div class="row row-cards">

                            <div class="col-sm-6 col-md-3">
                              <div class="mb-3">
                                <label class="form-label " for="morada">Morada</label>
                                <input type="text" class="form-control" placeholder="Morada" id="morada" name="morada" >
                              </div>
                            </div>
  
  
                            <div class="col-sm-6 col-md-3">
                              <div class="mb-3">
                                <label class="form-label " for="localidade">Localidade</label>
                                <input type="text" class="form-control" placeholder="Localidade" id="localidade" name="localidade" >
                              </div>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                <label class="form-label " for="cpostal">Código postal</label>
                                <input type="text" pattern="[0-9]{4}-[0-9]{3}" class="form-control" placeholder="Exemplo: 1234-123" id="cpostal" name="cpostal" >
                                </div>
                            </div>
  
  
                        <div class="col-md-12">


                            <br>


                        <div class="row row-cards">
        
                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                <label class="form-label" for="cidade">Cidade</label>
                                <input type="text" class="form-control" placeholder="Cidade" id="cidade" name="cidade" >
                                </div>
                            </div>
    

                            <hr>
    
                        <div class="col-md-12">

                        <div class="mb-3 mb-0">
                        <label class="form-label" for="observacoes">Observações</label>
                        <textarea rows="5" class="form-control" placeholder="Escreve aqui observações sobre este utilizador." id ="observacoes" name="observacoes"></textarea>
                        </div>
                        </div>
                        
                      </div>
                      <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary" value="Submit">Submeter</button>
                      </div>
                    </form>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Footer -->

      <?php

        include 'footer.php';

      ?>


      <script>
        if (window.history.replaceState){

          window.history.replaceState(null, null, window.location.href);
        }
      </script>

    </div>
  </div>

  <!-- Libs JS -->
  <script src="./dist/libs/nouislider/dist/nouislider.min.js?1684106062" defer></script>
  <script src="./dist/libs/litepicker/dist/litepicker.js?1684106062" defer></script>
  <script src="./dist/libs/tom-select/dist/js/tom-select.base.min.js?1684106062" defer></script>
  <!-- Tabler Core -->
  <script src="./dist/js/tabler.min.js?1684106062" defer></script>
  <script src="./dist/js/demo.min.js?1684106062" defer></script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-states'), {
    		copyClassesToDropdown: false,
    		dropdownParent: 'body',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	 window.noUiSlider && (noUiSlider.create(document.getElementById('range-simple'), {
    			  start: 20,
    			  connect: [true, false],
    			  step: 10,
    			  range: {
    				  min: 0,
    				  max: 100
    			  }
    	 }));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	 window.noUiSlider && (noUiSlider.create(document.getElementById('range-connect'), {
    			  start: [60, 90],
    			  connect: [false, true, false],
    			  step: 10,
    			  range: {
    				  min: 0,
    				  max: 100
    			  }
    	 }));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	 window.noUiSlider && (noUiSlider.create(document.getElementById('range-color'), {
    			  start: 40,
    			  connect: [true, false],
    			  step: 10,
    			  range: {
    				  min: 0,
    				  max: 100
    			  }
    	 }));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	window.Litepicker && (new Litepicker({
    		element: document.getElementById('datepicker-default'),
    		buttonText: {
    			previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
    			nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
    		},
    	}));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	window.Litepicker && (new Litepicker({
    		element: document.getElementById('datepicker-icon'),
    		buttonText: {
    			previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
    			nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
    		},
    	}));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	window.Litepicker && (new Litepicker({
    		element: document.getElementById('datepicker-icon-prepend'),
    		buttonText: {
    			previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
    			nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
    		},
    	}));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	window.Litepicker && (new Litepicker({
    		element: document.getElementById('datepicker-inline'),
    		buttonText: {
    			previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
    			nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
    		},
    		inlineMode: true,
    	}));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-tags'), {
    		copyClassesToDropdown: false,
    		dropdownParent: 'body',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-users'), {
    		copyClassesToDropdown: false,
    		dropdownParent: 'body',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-optgroups'), {
    		copyClassesToDropdown: false,
    		dropdownParent: 'body',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-people'), {
    		copyClassesToDropdown: false,
    		dropdownParent: 'body',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-countries'), {
    		copyClassesToDropdown: false,
    		dropdownParent: 'body',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-labels'), {
    		copyClassesToDropdown: false,
    		dropdownParent: 'body',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-countries-valid'), {
    		copyClassesToDropdown: false,
    		dropdownParent: 'body',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-countries-invalid'), {
    		copyClassesToDropdown: false,
    		dropdownParent: 'body',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });
    // @formatter:on
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
    	let sliderTriggerList = [].slice.call(document.querySelectorAll("[data-slider]"));
    	sliderTriggerList.map(function (sliderTriggerEl) {
    		let options = {};
    		if (sliderTriggerEl.getAttribute("data-slider")) {
    			options = JSON.parse(sliderTriggerEl.getAttribute("data-slider"));
    		}
    		let slider = noUiSlider.create(sliderTriggerEl, options);
    		if (options['js-name']) {
    			window[options['js-name']] = slider;
    		}
    	});
    });
  </script>
</body>
</html>