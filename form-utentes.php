<?php

//inicia sessão
session_start();

require_once 'check_status.php';

// verifica se o utilizador está logado, se não retorna para a página login.php
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$page = 'form-utentes';
$role = htmlspecialchars($_SESSION["cargo"]);

// Verifica se o email é válido
if(isset($_GET['email']) && !empty($_GET['email'])){
    if(filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)){
        $email = $_POST['email'];
    } else {
        echo "<script type='text/javascript'> alert('Email inválido! Tente novamente.') </script>";
    }
}

if($_SERVER['REQUEST_METHOD'] == "POST"){

    $id_utilizador = $_POST['id_utilizador'];
    $nome_utente = $_POST['nome_utente'];
    $email = $_POST['email'];
    $telef = $_POST['telef'];
    $morada = $_POST['morada'];
    $localidade = $_POST['localidade'];
    $cpostal = $_POST['cpostal'];
    $cidade = $_POST['cidade'];
    $nif = $_POST['nif'];
    $niss = $_POST['niss'];
    $observacoes = $_POST['observacoes'];

    global $conn; 

    if(!empty($nome_utente) && !empty($nif)){

        $sqlquery = "INSERT INTO utentes (nome_utente, telef, email, morada, localidade, cpostal, cidade, nif, niss, observacoes, id_utilizador, datacriacao) 
        VALUES ('$nome_utente', '$telef', '$email', '$morada', '$localidade', '$cpostal', '$cidade', '$nif', '$niss', '$observacoes', '$id_utilizador', now())";

        if(mysqli_query($conn, $sqlquery)){
            echo "<script type='text/javascript'> alert('Utente registado com sucesso!') </script>";
        } else {
            echo "<script type='text/javascript'> alert('Falha ao registar utente: " . mysqli_error($conn) . "') </script>";
        }
    } else {
        echo"<script type='text/javascript'> alert('Falha ao registar utente. Todos os campos são obrigatórios.') </script>";
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    if(empty(trim($_POST["nome_utente"]))){
        echo "<script type='text/javascript'> alert('Por favor, introduz o nome do utente!') </script>";
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["nome_utente"]))){
        echo "<script type='text/javascript'> alert('O nome do utente só pode conter letras, números e sublinhados!') </script>";
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        global $conn; 

        $sql = "SELECT id_utente FROM utentes WHERE nome_utente = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["nome_utente"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else {
                    $nome_utente = trim($_POST["nome_utente"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
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
    <title>Form - Utentes</title>
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
                  Criar novo utente
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
                    <form class="card" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                        <div class="card-header">
                            <h4 class="card-title">Novo utente</h4>
                        </div>
                      <div class="card-body">
                         <br>
                        
                         <div class="row row-cards">

                          <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                              <label class="form-label required" for="nome_utente">Nome de utente</label>
                              <input type="text" class="form-control" placeholder="Nome de utente" id="nome_utente" name="nome_utente" required>
                            </div>
                          </div>

                          <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                              <label class="form-label " for="telef">Contacto</label>
                              <input type="tel" maxlength="9" minlength="9" class="form-control" placeholder="Contacto" id="telef" name="telef" >
                            </div>
                          </div>

                          <div class="col-sm-6 col-md-3">
                            <div class="mb-3">
                              <label class="form-label " for="email">Email</label>
                              <input type="email" class="form-control" placeholder="Email" id="email" name="email" >
                            </div>
                          </div>

                        </div>

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
                                <label class="form-label " for="cidade">Cidade</label>
                                <input type="text" class="form-control" placeholder="Cidade" id="cidade" name="cidade" >
                                </div>
                            </div>

                            <?php

                              $utilizadores = mysqli_query($conn, 'SELECT * FROM utilizadores');
                              $uscount = mysqli_num_rows($utilizadores);


                              if($role == 'Administrador'){

                              echo '
                              <div class="mb-3 col-sm-4 col-md-3">
                                <label class="form-label required">Entidade</label>
                                <select name="id_utilizador" id="id_utilizador" class="form-select" required>
                                  <option value="">Selecione uma entidade</option>';

                              for($i = 1; $i <= $uscount; $i++) {
                                $row = mysqli_fetch_array($utilizadores);
                                echo '<option value="' . $row['id'] . '">' . $row['username'] . '</option>';
                              }

                              echo '
                                </select>
                              </div>';
                              }
                              else {

                                echo'
                                <input type="hidden" name="id_utilizador" id="id_utilizador" class="form-control" value="'.htmlspecialchars($_SESSION["id"]).'" required>
                                ';
                              }
                              ?>


                        </div>

                        <div class="col-md-12">

                        <hr>

                        <br>

                        <div class="row row-cards">
    
                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                <label class="form-label required" for="nif">NIF</label>
                                <input type="tel" maxlength="9" minlength="9" class="form-control" placeholder="NIF" id="nif" name="nif" required>
                                </div>
                            </div>


                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                <label class="form-label " for="niss">NISS</label>
                                <input type="tel" maxlength="11" minlength="11" class="form-control" placeholder="NISS" id="niss" name="niss" >
                                </div>
                            </div>

                        <div class="col-md-12">

                        <div class="mb-3 mb-0">
                        <label class="form-label" for="observacoes">Observações</label>
                        <textarea rows="5" class="form-control" placeholder="Escreve aqui observações sobre este utente." id ="observacoes" name="observacoes"></textarea>
                        </div>
                        </div>
                        
                      </div>
                      <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Submeter</button>
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