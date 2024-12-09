<?php
// Inicia sessão
session_start();

require_once 'check_status.php';

// Verifica se o utilizador está logado, se não retorna para a página login.php
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
  header("location: login.php");
  exit;
}

$id = htmlspecialchars($_SESSION["id"]);
$page = 'form-pedidos';


if($id == 36){

  if($_SERVER['REQUEST_METHOD'] == "POST"){

    $utente = $_POST['utente'];

    $voucher_query = mysqli_query($conn, "SELECT * FROM voucher WHERE id_utente = '$utente' AND estado = 'Ativo'");
    $voucher_row = mysqli_fetch_array($voucher_query);

    if (!$voucher_row) {
      echo "<script type='text/javascript'> alert('Nenhum voucher ativo encontrado para este utente.') </script>";
      exit;
    }

    // Consulta os artigos
    $artigos_query = mysqli_query($conn, "SELECT * FROM artigos WHERE id_artigo = '{$_POST['artigo']}'");
    $artigo_row = mysqli_fetch_array($artigos_query);

    // Dados do pedido
    $artigo = $_POST['artigo'];
    $tamanho = $_POST['tamanho'];
    $quantidade = $_POST['quantidade'];
    $estacao = $_POST['estacao'];
    $sexo = $_POST['sexo'];
    $observacoes = $_POST['observacoes'];
    $preco = $_POST['valor_peca'];

    $num_fatura = $_POST['num_fatura'];

    $valor_pedido = $preco * $quantidade;
    $valor_plafon = $voucher_row['plafond'] - $valor_pedido;

    $id_utilizador = htmlspecialchars($_SESSION["id"]);

    if(!empty($utente)){

      mysqli_begin_transaction($conn);
      
      if ($voucher_row['plafond'] < $valor_pedido) {
        echo "<script type='text/javascript'> alert('O valor deste pedido ultrapassa o valor do plafond deste utente.') </script>";
      }
      else{

        try {
          $sqlquery1 = "INSERT INTO pedidos (id_utentep, id_artigop, id_tamanhop, id_estado, quantidade, estacao, sexo, observacoes, valor_pedido, id_utilizador, num_fatura, data_pedido) 
                        VALUES ('$utente', '$artigo', '$tamanho', '5', '$quantidade', '$estacao', '$sexo', '$observacoes', '$valor_pedido', '$id_utilizador', $num_fatura, now())";
          mysqli_query($conn, $sqlquery1);

          $sqlquery2 = "UPDATE voucher SET plafond = '$valor_plafon' WHERE id_utente = '$utente' AND estado = 'Ativo'";
          mysqli_query($conn, $sqlquery2);

          mysqli_commit($conn);

          echo "<script type='text/javascript'> alert('Pedido efetuado com sucesso!') </script>";
        } catch (Exception $e) {
          mysqli_rollback($conn);
          echo "<script type='text/javascript'> alert('Falha ao efetuar o pedido.') </script>";
        }
      }

    } else {
      echo "<script type='text/javascript'> alert('Falha ao efetuar o pedido.') </script>";
    }
    
  }

} else {

  if($_SERVER['REQUEST_METHOD'] == "POST"){

    $utente = $_POST['utente'];
    $voucher_query = mysqli_query($conn, "SELECT * FROM voucher WHERE id_utente = '$utente' AND estado = 'Ativo'");
    $voucher_row = mysqli_fetch_array($voucher_query);

    if (!$voucher_row) {
      echo "<script type='text/javascript'> alert('Nenhum voucher ativo encontrado para este utente.') </script>";
      exit;
    }

    $artigos_query = mysqli_query($conn, "SELECT * FROM artigos WHERE id_artigo = '{$_POST['artigo']}'");
    $artigo_row = mysqli_fetch_array($artigos_query);

    $artigo = $_POST['artigo'];
    $tamanho = $_POST['tamanho'];
    $quantidade = $_POST['quantidade'];
    $estacao = $_POST['estacao'];
    $sexo = $_POST['sexo'];
    $observacoes = $_POST['observacoes'];
    $preco = $_POST['valor_peca'];

    $num_fatura = $_POST['num_fatura'];

    $valor_pedido = $preco * $quantidade;
    $valor_plafon = $voucher_row['plafond'] - $valor_pedido;
    $validade = $voucher_row['validade'];

    $id_utilizador = htmlspecialchars($_SESSION["id"]);
    
    if(!empty($utente) && !empty($artigo) && !empty($tamanho) && !empty($quantidade) && !empty($estacao) && !empty($sexo)){

      mysqli_begin_transaction($conn);
      if($validade < date('Y-m-d')){
        echo "<script type='text/javascript'> alert('O voucher deste utente está expirado.') </script>";
      }
      else{
        if ($voucher_row['plafond'] < $valor_pedido) {
          echo "<script type='text/javascript'> alert('O valor deste pedido ultrapassa o valor do plafond deste utente.') </script>";
        }
        else{

          try {
            $sqlquery1 = "INSERT INTO pedidos (id_utentep, id_artigop, id_tamanhop, id_estado, quantidade, estacao, sexo, observacoes, valor_pedido, id_utilizador, num_fatura, data_pedido) 
                          VALUES ('$utente', '$artigo', '$tamanho', '5', '$quantidade', '$estacao', '$sexo', '$observacoes', '$valor_pedido', '$id_utilizador', $num_fatura, now())";
            mysqli_query($conn, $sqlquery1);

            $sqlquery2 = "UPDATE voucher SET plafond = '$valor_plafon' WHERE id_utente = '$utente' AND estado = 'Ativo'";
            mysqli_query($conn, $sqlquery2);

            mysqli_commit($conn);

            echo "<script type='text/javascript'> alert('Pedido efetuado com sucesso!') </script>";
          } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<script type='text/javascript'> alert('Falha ao efetuar o pedido.') </script>";
          }
        }
      }
    } else {
      echo "<script type='text/javascript'> alert('Falha ao efetuar o pedido.') </script>";
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
    <title>Form - Pedidos</title>
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
                  Criar novo pedido
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
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="card">
                  <div class="card-header">
                    <h4 class="card-title">Novo pedido</h4>
                  </div>
                  <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="row-cards">

                        <?php

                          if ($role == 'Administrador'){
                            $utentes = mysqli_query($conn,"SELECT * FROM utentes");
                          }
                          else {
                          $utentes = mysqli_query($conn,"SELECT * FROM utentes where id_utilizador = '$id'");
                          }
                          $utcount = mysqli_num_rows($utentes);

                          $artigos = mysqli_query($conn, 'SELECT * FROM artigos');
                          $artcount = mysqli_num_rows($artigos);


                          $tamanhos = mysqli_query($conn, 'SELECT * FROM tamanhos');
                          $tamcount = mysqli_num_rows($tamanhos);

                          $estados = mysqli_query($conn, 'SELECT * FROM estado');
                          $estcount = mysqli_num_rows($estados);

                          $queryz = mysqli_query($conn, "SELECT * FROM utentes 
                          INNER JOIN voucher ON voucher.id_utente = utentes.id_utente
                          WHERE voucher.estado = 'Ativo'");

                        ?>
                        
                        <?php
                          if ($role == 'Administrador' or $_SESSION["id"] == 36) {
                              
                              if ($queryz) {
                                  ?>
                                  <div id="table-default" class="table-responsive">
                                      <table class="table">
                                          <tr>
                                              <th><button class="table-sort">Utente</button></th>
                                              <th><button class="table-sort">Plafond</button></th>
                                          </tr>
                                          <tbody class="table-tbody">
                                              <?php
                                              while ($row = mysqli_fetch_array($queryz)) {
                                                  ?>
                                                  <tr>
                                                    <?php
                                                      if(mysqli_num_rows($queryz) == 0){
                                                    ?>

                                                    <td class="sort-id">Nenhum utente com voucher encontrado</td>
                                                    <td class="sort-plafond"></td>

                                                    <?php
                                                      }
                                                      else{
                                                    ?>
                                                      <td class="sort-id"><?php echo htmlspecialchars($row['nome_utente']); ?></td>
                                                      <?php
                                                      echo "<td class='sort-plafond'>" . number_format($row['plafond'], 2, ',', '.') . "€</td>";
                                                      ?>
                                                  </tr>
                                        
                                                  <?php
                                                } 
                                              }
                                              ?>
                                          </tbody>
                                      </table>
                                  </div>
                                  <?php
                              } else {
                                  echo "Erro na consulta: " . mysqli_error($conn);
                              }
                          }

                          echo "<br>";
                          echo "<br>";
                          ?>

                        <div class="mb-3 col-sm-4 col-md-3">
                          <label class="form-label required">Utente</label>
                          <select name="utente" id="utente" class="form-select" required>

                          <option value="">Selecione um utente</option>

                            <?php
                            
                              for($i = 1; $i <= $utcount; $i++) {

                                $row = mysqli_fetch_array($utentes);
                              
                            ?>

                            <option value="<?php echo $row['id_utente'] ?>"><?php echo $row['nome_utente'] ?></option>
                              <?php
                                }
                              ?>
                          </select>
                        </div>

                        <br>

                        <div class="mb-3 col-sm-8 col-md-5">
                          <label class="form-label <?php if($id != 36){ echo'required';} ?>">Artigo</label>
                          <select id="artigo" name="artigo" class="form-select" <?php if($id != 36){ echo'required';} ?>>
                          
                          <option value="">Selecione um artigo</option>

                          <?php
                            
                            for($i = 1; $i <= $artcount; $i++) {

                              $row = mysqli_fetch_array($artigos);
                            
                          ?>
                          
                          <option value="<?php echo $row['id_artigo'] ?>"><?php echo $row['designacao_artigo'] ?></option>
                            <?php
                              }
                            ?>
                          </select>
                        </div>

                        <br>

                        <div class="mb-3 col-sm-8 col-md-3">
                          <label class="form-label <?php if($id != 36){ echo'required';} ?>">Tamanho</label>
                          <select id="tamanho" name="tamanho" class="form-select" <?php if($id != 36){ echo'required';} ?>>

                          <option value="">Selecione um tamanho</option>

                          <?php
                            
                            for($i = 1; $i <= $tamcount; $i++) {

                              $row = mysqli_fetch_array($tamanhos);
                          ?>

                            <option value="<?php echo $row['id_tamanho'] ?>"><?php echo $row['tamanho'] ?></option>
                            <?php
                              }
                            ?>
                          </select>
                        </div>

                        <br>

                        <div class="mb-3 col-sm-8 col-md-2">
                          <label class="form-label <?php if($id != 36){ echo'required';} ?>">Quantidade</label>
                          <input name="quantidade" id="quantidade" type="number" class="form-control" placeholder="Quantidade dos artigos" autocomplete="off" <?php if($id != 36){ echo'required';} ?>>
                        </div>

                        <br>

                        <div class="mb-3 col-sm-8 col-md-2">
                          <label class="form-label <?php if($id != 36){ echo'required';} ?>">Estação</label>
                          <select id="estacao" name="estacao" class="form-select" <?php if($id != 36){ echo'required';} ?>>
                            <option value="">Selecione a estação</option>
                            <option value="Primavera">Primavera</option>
                            <option value="Verão">Verão</option>
                            <option value="Outono">Outono</option>
                            <option value="Inverno">Inverno</option>
                          </select>
                        </div>

                        <br>

                        <div class="mb-3 col-sm-8 col-md-2">
                          <label class="form-label <?php if($id != 36){ echo'required';} ?>">Sexo</label>
                          <select id="sexo" name="sexo" class="form-select" <?php if($id != 36){ echo'required';} ?>>
                            <option value="">Selecione o sexo</option>
                            <option>Masculino</option>
                            <option>Feminino</option>
                          </select>
                        </div>

                        <br>

                        <div class="mb-3 col-sm-8 col-md-2">
                          <label class="form-label <?php if($id != 36){ echo'required';} ?>">Preço do artigo (€)</label>
                          <input name="valor_peca" id="valor_peca" type="text" class="form-control" placeholder="Coloca aqui o preço" autocomplete="off" <?php if($id != 36){ echo'required';} ?>>
                        </div>

                        <br>

                        <?php
                        if ($role == 'Administrador'){
                        ?>
                        <div class="mb-3 col-sm-8 col-md-2">
                          <label class="form-label">Número da fatura</label>
                          <input name="num_fatura" id="num_fatura" type="text" class="form-control" placeholder="Número da fatura" autocomplete="off">
                        </div>

                        <?php
                        }
                        ?>


                        <br>

                        <div class="mb-3 col-sm-8 col-md-10">
                          <label class="form-label ">Observações</label>
                          <textarea name="observacoes" id="observacoes" type="text" class="form-control" placeholder="Esceve aqui observações sobre este pedido."></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer text-end">
                      <button type="submit" class="btn btn-primary">Submeter</button>
                    </div>
                  </div>
                </form>
                </div>
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
  <script>
    const utente = document.getElementById('utente');
  const artigo = document.getElementById('artigo');
  const tamanho = document.getElementById('tamanho');
  const quantidade = document.getElementById('quantidade');
  const estacao = document.getElementById('estacao');
  const sexo = document.getElementById('sexo');
  const observacoes = document.getElementById('observacoes');
  </script>
</body>
</html>