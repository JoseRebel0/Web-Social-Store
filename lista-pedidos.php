<?php

session_start();

include 'check_status.php';

// verifica se o utilizador está logado, se não retorna para a página login.php
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$role = htmlspecialchars($_SESSION["cargo"]);
$getid = intval($_SESSION["id"]);

$page = 'lista-pedidos';

// Verifica se há um termo de pesquisa
$searchQuery = "";
if (isset($_GET["query"]) && !empty($_GET["query"])) {
    $searchQuery = htmlspecialchars($_GET["query"]);
}

// Initialize base SQL query
$sql = "SELECT DISTINCT pedidos.observacoes, pedidos.id_utentep, pedidos.id_artigop, pedidos.valor_pedido, pedidos.id_tamanhop, pedidos.id_estado, estado.designacao, pedidos.id_pedido, utentes.nome_utente, utilizadores.username, utilizadores.nome_entidade, artigos.designacao_artigo, tamanhos.tamanho, pedidos.sexo, pedidos.estacao, pedidos.quantidade, pedidos.data_pedido, utentes.id_utilizador, pedidos.num_fatura
        FROM pedidos
        LEFT JOIN utentes ON pedidos.id_utentep = utentes.id_utente
        LEFT JOIN utilizadores ON pedidos.id_utilizador = utilizadores.id
        LEFT JOIN artigos ON pedidos.id_artigop = artigos.id_artigo
        LEFT JOIN tamanhos ON pedidos.id_tamanhop = tamanhos.id_tamanho
        INNER JOIN estado ON pedidos.id_estado = estado.id_estado";

// Adicionar WHERE para utilizadores que não são administradores
if ($role != 'Administrador') {
    $sql .= " WHERE pedidos.id_utilizador = '$getid'";
}

// Adicionar a condição de pesquisa
if (!empty($searchQuery)) {
    $sql .= " AND (utentes.nome_utente LIKE '%$searchQuery%'
             OR utilizadores.nome_entidade LIKE '%$searchQuery%'
             OR artigos.designacao_artigo LIKE '%$searchQuery%'
             OR tamanhos.tamanho LIKE '%$searchQuery%'
             OR estado.designacao LIKE '%$searchQuery%'
             OR pedidos.estacao LIKE '%$searchQuery%'
             OR pedidos.sexo LIKE '%$searchQuery%')";
}

// Verifica se os filtros foram aplicados
if (isset($_POST['apply_filters'])) {
    $id_utente = $_POST['id_utente'];
    $id_utilizador = ($role == 'Administrador') ? $_POST['id_utilizador'] : $getid;
    $id_artigo = $_POST['id_artigo'];
    $tamanho = $_POST['tamanho'];
    $estacao = $_POST['estacao'];
    $sexo = $_POST['sexo'];
    $estado = $_POST['estado'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];

    $conditions = [];

    if (!empty($id_utente)) {
        $conditions[] = "pedidos.id_utentep = '$id_utente'";
    }

    if (!empty($id_utilizador)) {
        $conditions[] = "pedidos.id_utilizador = '$id_utilizador'";
    }

    if (!empty($id_artigo)) {
        $conditions[] = "pedidos.id_artigop = '$id_artigo'";
    }

    if (!empty($tamanho)) {
        $conditions[] = "pedidos.id_tamanhop = '$tamanho'";
    }

    if (!empty($estacao)) {
        $conditions[] = "pedidos.estacao = '$estacao'";
    }

    if (!empty($sexo)) {
        $conditions[] = "pedidos.sexo = '$sexo'";
    }

    if (!empty($estado)) {
        $conditions[] = "pedidos.id_estado = '$estado'";
    }

    if (!empty($data_inicio) && !empty($data_fim)) {
        $conditions[] = "pedidos.data_pedido BETWEEN '$data_inicio' AND '$data_fim'";
    }

    // Adicionar as condições à query
    if (count($conditions) > 0) {
        $sql .= " AND " . implode(' AND ', $conditions);
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Lista - Pedidos</title>
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
        * {
            padding: 0;
            margin: 0;
        }
        .bottom-field {
            width: 100%;
            padding: 20px;
            margin-top: 20px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .pagination li {
            list-style: none;
            padding: 2px;
            margin: 10px;
            flex-shrink: 0;
            text-align: center;
            border-radius: 5px;
            border: 1px solid #999;
            color: #999;
        }
        .pagination li.active {
            background: #264096;
            text-decoration: none;
            color: white;
            border-color: #264096;
            padding: 3px 8px;
            display: block;
        }
        .pagination li a {
            text-decoration: none;
            color: inherit;
            padding: 3px 8px;
            display: block;
        }
        .items-controler {
            width: 100%;
            display: flex;
            flex-shrink: 0;
            justify-content: flex-start;
            align-items: center;
            margin-top: 1%;
        }
        select {
            padding: 2px;
            margin: 0 10px;
            outline: none;
            border: none;
            cursor: pointer;
            background-color: transparent;
        }
        .delete {
            color: red;
            cursor: pointer;
        }
    </style>
</head>
<body>
<script src="./dist/js/demo-theme.min.js?1684106062"></script>
<div class="page">
    <!-- Header -->
    <?php include 'header.php'; ?>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>
    <div class="page-wrapper">
        <!-- Page header -->
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <br>
                        <div class="title-butt">    
                            <h2 class="page-title">Lista de pedidos</h2>

                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" autocomplete="off" novalidate>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                      <!-- Download SVG icon from http://tabler-icons.io/i/search -->
                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                    </span>
                                    <input type="text" name="query" value="<?php echo $searchQuery; ?>" class="form-control" placeholder="Pesquisar" aria-label="Search in website">
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                <br>

                <form id="filterForm" method="POST">
                <p>Filtrar por:</p>
                <div style="width: 100%, height: auto; display: flex; justify-content: space-between;">
                <div class="mb-3 col-sm-8 col-md-2">
                    <select id="id_utente" name="id_utente" class="form-select">
                            <option value="">Utente</option>
                            <?php
                            if($role == 'Administrador'){
                            $query = "SELECT DISTINCT pedidos.id_utentep, utentes.nome_utente 
                                      FROM pedidos 
                                      INNER JOIN utentes ON pedidos.id_utentep = utentes.id_utente";
                            } else {
                            $query = "SELECT DISTINCT pedidos.id_utentep, utentes.nome_utente 
                                      FROM pedidos 
                                      INNER JOIN utentes ON pedidos.id_utentep = utentes.id_utente
                                      WHERE pedidos.id_utilizador = '$getid'";
                            }
                            $result = mysqli_query($conn, $query) or die ('error');
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . trim($row['id_utentep']) . '">' . trim($row['nome_utente']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                </div>

                <?php
                  if ($role == 'Administrador') {
                      echo '
                      <div class="mb-3 col-sm-8 col-md-2">
                          <select id="id_utilizador" name="id_utilizador" class="form-select">
                              <option value="">Entidade</option>';
                              
                              $query = "SELECT DISTINCT pedidos.id_utilizador, utilizadores.nome_entidade 
                                        FROM pedidos 
                                        INNER JOIN utilizadores ON pedidos.id_utilizador = utilizadores.id";
                              $result = mysqli_query($conn, $query) or die ('error');
                              if (mysqli_num_rows($result) > 0) {
                                  while ($row = mysqli_fetch_assoc($result)) {
                                      echo '<option value="' . htmlspecialchars(trim($row['id_utilizador'])) . '">' . htmlspecialchars(trim($row['nome_entidade'])) . '</option>';
                                  }
                              }
                              
                      echo '
                          </select>
                      </div>';
                  }
                  ?>

                    <div class="mb-3 col-sm-8 col-md-2">
                        <select id="id_artigo" name="id_artigo" class="form-select">
                            <option value="">Artigo</option>
                            <?php
                            if($role == 'Administrador'){
                              $query = "SELECT DISTINCT pedidos.id_artigop, artigos.designacao_artigo 
                                        FROM pedidos 
                                        INNER JOIN artigos ON pedidos.id_artigop = artigos.id_artigo";
                            }
                            else {
                              $query = "SELECT DISTINCT pedidos.id_artigop, artigos.designacao_artigo 
                                        FROM pedidos 
                                        INNER JOIN artigos ON pedidos.id_artigop = artigos.id_artigo
                                        WHERE pedidos.id_utilizador = '$getid'";
                            }
                            $result = mysqli_query($conn, $query) or die ('error');
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . trim($row['id_artigop']) . '">' . trim($row['designacao_artigo']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3 col-sm-8 col-md-2">
                        <select id="tamanho" name="tamanho" class="form-select">
                            <option value="">Tamanho</option>
                            <?php

                            if($role == 'Administrador'){
                              $query = "SELECT DISTINCT pedidos.id_tamanhop, tamanhos.tamanho 
                                        FROM pedidos 
                                        INNER JOIN tamanhos ON pedidos.id_tamanhop = tamanhos.id_tamanho";
                            }
                            else {

                              $query = "SELECT DISTINCT pedidos.id_tamanhop, tamanhos.tamanho 
                                        FROM pedidos 
                                        INNER JOIN tamanhos ON pedidos.id_tamanhop = tamanhos.id_tamanho
                                        WHERE pedidos.id_utilizador = '$getid'";
                            }
                            $result = mysqli_query($conn, $query) or die ('error');
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . trim($row['id_tamanhop']) . '">' . trim($row['tamanho']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3 col-sm-8 col-md-1">
                        <select id="estacao" name="estacao" class="form-select">
                            <option value="">Estação</option>
                            <?php 
                            
                            if($role == 'Administrador'){
                            $query = "SELECT DISTINCT estacao FROM pedidos";
                            }

                            else {
                              $query = "SELECT DISTINCT estacao FROM pedidos WHERE pedidos.id_utilizador = '$getid'";
                            }
                            $result = mysqli_query($conn, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                                <option value="<?php echo trim($row['estacao']); ?>"><?php echo trim($row['estacao']); ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3 col-sm-8 col-md-1">
                        <select id="sexo" name="sexo" class="form-select">
                            <option value="">Sexo</option>
                            <?php 
                            
                            if($role == 'Administrador'){
                            $query = "SELECT DISTINCT sexo FROM pedidos";
                            }

                            else {
                              $query = "SELECT DISTINCT sexo FROM pedidos WHERE pedidos.id_utilizador = '$getid'";
                            }
                            $result = mysqli_query($conn, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {

                            ?>
                                <option value="<?php echo trim($row['sexo']); ?>"><?php echo trim($row['sexo']); ?></option>
                            <?php 
                                } 
                            }    
                            ?>
                        </select>
                    </div>

                    <div class="mb-3 col-sm-8 col-md-1">
                        <select id="estado" name="estado" class="form-select">
                            <option value="">Estado</option>
                            <?php 
                            
                            if($role == 'Administrador'){
                            $query = "SELECT DISTINCT pedidos.id_estado, estado.designacao FROM pedidos
                            INNER JOIN estado ON pedidos.id_estado = estado.id_estado";
                            }

                            else {
                              $query = "SELECT DISTINCT pedidos.id_estado, estado.designacao FROM pedidos
                              INNER JOIN estado ON pedidos.id_estado = estado.id_estado
                              WHERE pedidos.id_utilizador = '$getid'";
                            }
                            $result = mysqli_query($conn, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {

                            ?>
                                <option value="<?php echo trim($row['id_estado']); ?>"><?php echo trim($row['designacao']); ?></option>

                            <?php 
                                } 
                            }    
                            ?>
                        </select>
                    </div>
                </div>



                <div style="width: 100%, height: auto; display: flex;">
                    <div class="mb-3 col-sm-8 col-md-2" style="margin-right: 42px;">
                        <label>Data de começo</label>
                        <input id="data_inicio" name="data_inicio" type="date" class="form-control"></input>
                    </div>


                    <div class="mb-3 col-sm-8 col-md-2">
                        <label>Data de fim</label>
                        <input id="data_fim" name="data_fim" type="date" class="form-control"></input>
                    </div>
                </div>

                <button class="btn btn-primary" type="submit" name="apply_filters">Aplicar Filtros</button>
                <a class="btn btn-secondary" href="lista-pedidos.php">Limpar Filtros</a>

               <!-- <a class="btn btn-success" style="margin-left: 25px;" href="export_excel.php">Exportar</a>-->
            </form>

            <br>
            <div class="items-controler">
              <h5>Mostrar</h5>
              <select id="itemperpage">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
              </select>
              <h5>items por página</h5>
            </div>

            </div>
            
        </div>
        <div class="page-body">
          </div>
            <div class="container-xl">
                <div class="card">
                    <div class="card-body">
                        <div id="table-default" class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                    <th><button class="table-sort" data-sort="sort-id">ID</button></th>
                                    <th><button class="table-sort" data-sort="sort-utente">Utente</button></th>
                                    <th><button class="table-sort" data-sort="sort-entidade">Entidade</button></th>
                                    <th><button class="table-sort" data-sort="sort-artigo">Artigo</button></th>
                                    <th><button class="table-sort" data-sort="sort-tamanho">Tamanho</button></th>
                                    <th><button class="table-sort" data-sort="sort-sexo">Sexo</button></th>
                                    <th><button class="table-sort" data-sort="sort-estacao">Estação</button></th>
                                    <th><button class="table-sort" data-sort="sort-quantidade">Quantidade</button></th>
                                    <th><button class="table-sort" data-sort="sort-preco">Preço</button></th>
                                    <th><button class="table-sort" data-sort="sort-estado">Estado</button></th>
                                    <?php
                                    if($role == 'Administrador' or  $getid == 36){
                                    ?>
                                    <th><button class="table-sort" data-sort="sort-numfat">Nº Fatura</button></th>
                                    <?php
                                    }
                                    ?>
    
                                    <th><button class="table-sort" data-sort="sort-data">Data</button></th>
                                    <th><button class="table-sort">Ações</button></th>
                                    <th></th>
                                    </tr>
                                </thead>
                                <tbody class="table-tbody">
                                <?php
                                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td class='sort-id'>" . htmlspecialchars($row['id_pedido']) . "</td>";
                                            echo "<td class='sort-utente'>" . htmlspecialchars($row['nome_utente']) . "</td>";
                                            echo "<td class='sort-entidade'>" . htmlspecialchars($row['nome_entidade']) . "</td>";
                                            echo "<td class='sort-artigo'>" . htmlspecialchars($row['designacao_artigo']) . "</td>";
                                            echo "<td class='sort-tamanho'>" . htmlspecialchars($row['tamanho']) . "</td>";
                                            echo "<td class='sort-sexo'>" . htmlspecialchars($row['sexo']) . "</td>";
                                            echo "<td class='sort-estacao'>" . htmlspecialchars($row['estacao']) . "</td>";
                                            echo "<td class='sort-quantidade'>" . htmlspecialchars($row['quantidade']) . "</td>";

                                    
                                            
                                            if($row['id_utilizador'] == 36){
                                                echo "<td class='sort-preco'>" . htmlspecialchars(number_format($row['valor_pedido'], 2, ',', '.')) . "€</td>";
                                            } else {
                                                echo '<td class="sort-preco">'.number_format(0, 2, ',', '.').'</td>';
                                            }
                                            switch ($row['id_estado']) {
                                                case '1':
                                                    echo '<td class="sort-estado" style="width: 80px; color:#daa520"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-loader"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6l0 -3" /><path d="M16.25 7.75l2.15 -2.15" /><path d="M18 12l3 0" /><path d="M16.25 16.25l2.15 2.15" /><path d="M12 18l0 3" /><path d="M7.75 16.25l-2.15 2.15" /><path d="M6 12l-3 0" /><path d="M7.75 7.75l-2.15 -2.15" /></svg>' . htmlspecialchars($row['designacao']) . "</td>";
                                                    break;
                                                case '2':
                                                    echo '<td class="sort-estado" style="width: 80px; color:#109010"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>' . htmlspecialchars($row['designacao']) . "</td>";
                                                    break;
                                                case '3':
                                                    echo '<td class="sort-estado" style="width: 80px; color:red"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg> ' . htmlspecialchars($row['designacao']) . "</td>";
                                                    break;
                                                case '4':
                                                    echo '<td class="sort-estado" style="width: 80px; color:grey"> <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-hourglass"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6.5 7h11" /><path d="M6.5 17h11" /><path d="M6 20v-2a6 6 0 1 1 12 0v2a1 1 0 0 1 -1 1h-10a1 1 0 0 1 -1 -1z" /><path d="M6 4v2a6 6 0 1 0 12 0v-2a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1z" /></svg>' . htmlspecialchars($row['designacao']) . "</td>";
                                                    break;
                                                case '5':
                                                    echo '<td class="sort-estado" style="width: 80px; color:blue"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-box"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /></svg>' . htmlspecialchars($row['designacao']) . "</td>";
                                                    break;
                                                default:
                                                    echo '<td class="sort-estado" style="width: 80px;">' . htmlspecialchars($row['designacao']) . "</td>";
                                            }
                                            
                                            if($role == 'Administrador' or  $getid == 36){

                                                echo "<td class='sort-numfat'>" . htmlspecialchars($row['num_fatura']) . "</td>";

                                                echo "<td class='sort-data' data-date='" . htmlspecialchars($row['data_pedido']) . "'>" . htmlspecialchars($row['data_pedido']) . "</td>";
                                            }

                                            if($row['id_estado'] != '2'){
                                            echo '<td><a href="edit-pedido.php?' . http_build_query($row) . '">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-pencil i">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                        <path d="M13.5 6.5l4 4" />
                                                    </svg>
                                                    Editar
                                                </a></td>';
                                            } else{

                                                echo '<td><a onclick="return(\'O pedido não pode ser apagado.\')" style="color: grey;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-pencil i">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                        <path d="M13.5 6.5l4 4" />
                                                    </svg>
                                                    Editar
                                                </a></td>';
                                            }


                                            $squery = "SELECT DISTINCT pedidos.id_utentep AS pedido_utente_id, pedidos.id_utilizador AS pedido_utilizador_id, utilizadores.id AS utilizador_id, utentes.id_utente AS utente_id
                                            FROM pedidos 
                                            LEFT JOIN utentes ON pedidos.id_utentep = utentes.id_utente
                                            LEFT JOIN utilizadores ON pedidos.id_utilizador = utilizadores.id
                                            WHERE pedidos.id_pedido = " . $row['id_pedido'];

                                $sresult = mysqli_query($conn, $squery) or die(mysqli_error($conn));

                                if ($srow = mysqli_fetch_assoc($sresult)) {
                                    $pedido_utilizador_id = $srow['pedido_utilizador_id'];
                                    $utilizador_id = $srow['utilizador_id'];
                                    $pedido_utente_id = $srow['pedido_utente_id'];
                                    $utente_id = $srow['utente_id'];
                                }

                                if ($role == 'Administrador') {
                                    if ($utilizador_id != $pedido_utilizador_id || $utente_id != $pedido_utente_id) {
                                        echo '<td><div class="delete">
                                                <a href="delete-pedido.php?id_pedido=' . urlencode($row['id_pedido']) . '" onclick="return confirm(\'Tem a certeza que deseja apagar este pedido?\')" style="color: red;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0" />
                                                        <path d="M10 11l0 6" />
                                                        <path d="M14 11l0 6" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                    </svg>
                                                    Eliminar
                                                </a>
                                            </div></td>';
                                    } else {
                                        echo '<td><div class="delete">
                                                <a onclick="return alert(\'O pedido não pode ser apagado, pois possui entidades e/ou utentes relacionados.\')" style="color: grey;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="grey" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0" />
                                                        <path d="M10 11l0 6" />
                                                        <path d="M14 11l0 6" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                    </svg>
                                                    Eliminar
                                                </a>
                                            </div></td>';
                                    }
                                }

                                echo "</tr>";
                                        }
                                } else {
                                    echo "<tr><td colspan='10'>Nenhum pedido encontrado</td></tr>";
                                }

                                ?>

                                </tbody>
                            </table>
                            <br>
                            
                        </div>
                        <!-- Paginação -->
                </div>

            </div>
        </div>
        <div class="bottom-field">
                            <ul class="pagination">
                                <li class="prev"><a href="#" id="prev">&#139;</a></li>
                                <li class="next"><a href="#" id="next">&#155;</a></li>
                            </ul>
                        </div>
    </div>
    
</div>

<!-- Footer -->
<?php include 'footer.php'; ?>


<!-- Libs JS -->
<script src="./dist/libs/list.js/dist/list.min.js?1684106062" defer></script>
    <!-- Tabler Core -->
    <script src="./dist/js/tabler.min.js?1684106062" defer></script>
    <script src="./dist/js/demo.min.js?1684106062" defer></script>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        const list = new List('table-default', {
            sortClass: 'table-sort',
            listClass: 'table-tbody',
            valueNames: [
                'sort-artigo',
                'sort-cargo',
                'sort-utente',
                'sort-entidade',
                'sort-tamanho',
                'sort-id',
                'sort-quantidade',
                'sort-estacao',
                'sort-sexo',
                'sort-observacao',
                'sort-preco',
                'sort-estado',
                'sort-numfat',                
                { attr: 'data-date', name: 'sort-data' }
            ]
        });
    });
</script>

<script>
    var tbody = document.querySelector("tbody");
    var pageUl = document.querySelector(".pagination");
    var itemShow = document.querySelector("#itemperpage");
    var tr = tbody.querySelectorAll("tr");
    var emptyBox = [];
    var index = 1;
    var itemPerPage = parseInt(itemShow.value);

    for(let i = 0; i < tr.length; i++) { 
        emptyBox.push(tr[i]);
    }

    itemShow.onchange = giveTrPerPage;

    function giveTrPerPage() {
        itemPerPage = Number(this.value);
        displayPage(itemPerPage);
        pageGenerator(itemPerPage);
        getpagElement(itemPerPage);
    }

    function displayPage(limit) {
        tbody.innerHTML = '';
        for(let i = 0; i < limit; i++) {
            if(emptyBox[i]) {
                tbody.appendChild(emptyBox[i]);
            }
        }
        const pageNum = pageUl.querySelectorAll('.list');
        pageNum.forEach(n => n.remove());
    }
    displayPage(itemPerPage);

    function pageGenerator(getem) {
        const num_of_tr = emptyBox.length;
        if(num_of_tr <= getem) {
            pageUl.style.display = 'none';
        } else {
            pageUl.style.display = 'flex';
            const num_Of_Page = Math.ceil(num_of_tr/getem);
            for(let i = 1; i <= num_Of_Page; i++) {
                const li = document.createElement('li');
                li.className = 'list';
                const a = document.createElement('a');
                a.href = '#';
                a.innerText = i;
                a.setAttribute('data-page', i);
                li.appendChild(a);
                pageUl.insertBefore(li, pageUl.querySelector('.next'));
            }
        }
    }
    pageGenerator(itemPerPage);

    let pageLink = pageUl.querySelectorAll("a");
    let lastPage = pageLink.length - 2;

    function pageRunner(page, items, lastPage, active) {
        for(let button of page) {
            button.onclick = e => {
                e.preventDefault();
                const page_num = e.target.getAttribute('data-page');
                const page_mover = e.target.getAttribute('id');
                if(page_num != null) {
                    index = page_num;
                } else {
                    if(page_mover === "next") {
                        index++;
                        if(index >= lastPage) {
                            index = lastPage;
                        }
                    } else {
                        index--;
                        if(index <= 1) {
                            index = 1;
                        }
                    }
                }
                pageMaker(index, items, active);
            }
        }
    }
    
    var pageLi = pageUl.querySelectorAll('.list');
    if(pageLi.length > 0) {
        pageLi[0].classList.add("active");
    }
    
    pageRunner(pageLink, itemPerPage, lastPage, pageLi);

    function getpagElement(val) {
        let pagelink = pageUl.querySelectorAll("a");
        let lastpage = pagelink.length - 2;
        let pageli = pageUl.querySelectorAll('.list');
        pageli[0].classList.add("active");
        pageRunner(pagelink, val, lastpage, pageli);
    }

    function pageMaker(index, item_per_page, activePage) {
        const start = item_per_page * (index - 1);
        const end  = start + item_per_page;
        const current_page = emptyBox.slice(start, end);
        tbody.innerHTML = "";
        for(let j = 0; j < current_page.length; j++) {
            let item = current_page[j];
            tbody.appendChild(item);
        }
        Array.from(activePage).forEach((e) => { e.classList.remove("active"); });
        activePage[index - 1].classList.add("active");
    };
</script>
</body>
</html>