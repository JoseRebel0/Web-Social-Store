    <?php

    // Inicia sessão
    session_start();

    require_once 'check_status.php';

    // Verifica se o utilizador está logado, se não, retorna para a página login.php
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: login.php");
        exit;
    }

    $role = htmlspecialchars($_SESSION["cargo"]);
    $getid = intval($_SESSION["id"]);

    $page = 'index';


    if ($role == 'Administrador') {
        $sql = "SELECT COUNT(*) AS total_pedidos FROM pedidos";

        $sql1 = "SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE id_estado = '2'";
        $sql2 = "SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE id_estado = '1'";
        $sql3 = "SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE id_estado = '3'";
        $sql4 = "SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE id_estado = '4'";
        $sql5 = "SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE id_estado = '5'";

        $sqlv = "SELECT SUM(valor_pedido) AS total_valor_pedido FROM pedidos WHERE id_estado = '2'";
        $sqlv1 = "SELECT SUM(valor_pedido) AS total_valor_pedido FROM pedidos WHERE id_estado = '1'";
        $sqlv2 = "SELECT SUM(valor_pedido) AS total_valor_pedido FROM pedidos WHERE id_estado = '3'";
        $sqlv3 = "SELECT SUM(valor_pedido) AS total_valor_pedido FROM pedidos WHERE id_estado = '4'";
        $sqlv4 = "SELECT SUM(valor_pedido) AS total_valor_pedido FROM pedidos WHERE id_estado = '5'";

        $sqlagre = "SELECT COUNT(*) AS total_agregados FROM agrefam";

    } else {
        $sql = "SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE id_utilizador = $getid";

        $sql1 = "SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE id_utilizador = $getid AND id_estado = '2'";
        $sql2 = "SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE id_utilizador = $getid AND id_estado = '1'";
        $sql3 = "SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE id_utilizador = $getid AND id_estado = '3'";
        $sql4 = "SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE id_utilizador = $getid AND id_estado = '4'";
        $sql5 = "SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE id_utilizador = $getid AND id_estado = '5'";

        $sqlv = "SELECT SUM(valor_pedido) AS total_valor_pedido FROM pedidos WHERE id_estado = '2' AND id_utilizador = $getid";
        $sqlv1 = "SELECT SUM(valor_pedido) AS total_valor_pedido FROM pedidos WHERE id_estado = '1' AND id_utilizador = $getid";
        $sqlv2 = "SELECT SUM(valor_pedido) AS total_valor_pedido FROM pedidos WHERE id_estado = '3' AND id_utilizador = $getid";
        $sqlv3 = "SELECT SUM(valor_pedido) AS total_valor_pedido FROM pedidos WHERE id_estado = '4' AND id_utilizador = $getid";
        $sqlv4 = "SELECT SUM(valor_pedido) AS total_valor_pedido FROM pedidos WHERE id_estado = '5' AND id_utilizador = $getid";

        $sqlagre = "SELECT COUNT(*) AS total_agregados FROM agrefam
        INNER JOIN utentes ON agrefam.id_utente = utentes.id_utente
        WHERE utentes.id_utilizador = $getid";
    }

    $result = $conn->query($sql);

    $result1 = $conn->query($sql1);
    $result2 = $conn->query($sql2);
    $result3 = $conn->query($sql3);
    $result4 = $conn->query($sql4);
    $result5 = $conn->query($sql5);

    $resultv = $conn->query($sqlv);
    $resultv1 = $conn->query($sqlv1);
    $resultv2 = $conn->query($sqlv2);
    $resultv3 = $conn->query($sqlv3);
    $resultv4 = $conn->query($sqlv4);

    $resultagre = $conn->query($sqlagre);

    $total_valor_pedido = 0;
    $total_valor_pedido1 = 0;
    $total_valor_pedido2 = 0;
    $total_valor_pedido3 = 0;
    $total_valor_pedido4 = 0;


    $total_pedidos = 0;
    $total_pedidos1 = 0;
    $total_pedidos2 = 0;
    $total_pedidos3 = 0;
    $total_pedidos4 = 0;
    $total_pedidos5 = 0;

    $total_agregados = 0;

    if ($resultv->num_rows > 0) {
        $row = $resultv->fetch_assoc();
        $total_valor_pedido = $row['total_valor_pedido'];
    }
    if ($resultv1->num_rows > 0) {
        $row = $resultv1->fetch_assoc();
        $total_valor_pedido1 = $row['total_valor_pedido'];
    }
    if ($resultv2->num_rows > 0) {
        $row = $resultv2->fetch_assoc();
        $total_valor_pedido2 = $row['total_valor_pedido'];
    }
    if ($resultv3->num_rows > 0) {
        $row = $resultv3->fetch_assoc();
        $total_valor_pedido3 = $row['total_valor_pedido'];
    }
    if ($resultv4->num_rows > 0) {
        $row = $resultv4->fetch_assoc();
        $total_valor_pedido4 = $row['total_valor_pedido'];
    }


    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_pedidos = $row['total_pedidos'];
    }

    if ($result1->num_rows > 0) {
        $row = $result1->fetch_assoc();
        $total_pedidos1 = $row['total_pedidos'];
    }
    if ($result2->num_rows > 0) {
        $row = $result2->fetch_assoc();
        $total_pedidos2 = $row['total_pedidos'];
    }
    if ($result3->num_rows > 0) {
        $row = $result3->fetch_assoc();
        $total_pedidos3 = $row['total_pedidos'];
    }
    if ($result4->num_rows > 0) {
        $row = $result4->fetch_assoc();
        $total_pedidos4 = $row['total_pedidos'];
    }
    if ($result5->num_rows > 0) {
        $row = $result5->fetch_assoc();
        $total_pedidos5 = $row['total_pedidos'];
    }

    if ($resultagre->num_rows > 0) {
        $row = $resultagre->fetch_assoc();
        $total_agregados = $row['total_agregados'];
    }

    if ($role == 'Administrador') {
        $sql2 = "SELECT COUNT(*) AS total_utilizadores FROM utilizadores";
        $sqlact = "SELECT COUNT(*) AS total_utilizadoresact FROM utilizadores WHERE id_status=1";
        $result2 = $conn->query($sql2);
        $resultact = $conn->query($sqlact);

        $total_utilizadores = 0;
        if ($result2->num_rows > 0) {
            $row = $result2->fetch_assoc();
            $total_utilizadores = $row['total_utilizadores'];
        }

        $total_utilizadoresact = 0;
        if ($resultact->num_rows > 0) {
            $row = $resultact->fetch_assoc();
            $total_utilizadoresact = $row['total_utilizadoresact'];
        }
    }

    $sql3 = $role == 'Administrador' ?
        "SELECT COUNT(*) AS total_utentes FROM utentes" :
        "SELECT COUNT(*) AS total_utentes FROM utentes WHERE id_utilizador = $getid";

    $result3 = $conn->query($sql3);

    $total_utentes = 0;
    if ($result3->num_rows > 0) {
        $row = $result3->fetch_assoc();
        $total_utentes = $row['total_utentes'];
    }

    ?>

    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
        <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
        <title>Home - <?php echo $nome_do_site; ?></title>
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
            .datagrid h3 {
                font-size: 0.9rem;
                margin-bottom: 1rem;
            }
            .datagrid {
                display: flex;
                flex-wrap: wrap;
                gap: 2rem;
            }
            @media (min-width: 576px) {
                .datagrid h3 {
                    font-size: 0.9rem;
                }
            }
            @media (min-width: 768px) {
                .datagrid h3 {
                    font-size: 1.1rem;
                }
            }
            iframe {
                width: 90%;
                height: 300px;
                border: none;
            }


            .container-relative {
                position: relative;
                margin-top: 30px;
                background-color: transparent; 
            }

            .overlay-image {
                position: absolute;
                top: 100px;
                height: 700px;
                left: 1200px; 
                width: 600px;
                z-index: 10; 
            }

            @media (max-width: 1024px) {
                .responsive-image {
                    display: none;
                }
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
                        <div class="col">

                            
                            <div class="page-pretitle">
                            Home
                            </div>
                            <h2 class="page-title">
                            Estatísticas
                            </h2>

                        </div> 
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    
                <div style="display: flex; justify-content: space-between;">

                
                    <!--Tabela dos pedidos-->
                    <div class="col-md-6 col-lg-4">
                    <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pedidos (<?php echo $total_pedidos; ?>)</h3>
                    </div>
                    <table class="table card-table table-vcenter">
                        <thead>
                        <tr>
                            <th>Tipo</th>
                            <th colspan="2">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Novos pedidos</td>
                            <td><?php echo $total_pedidos5 ?></td>
                            <td class="w-50">
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-primary" style="width:<?php echo $total_pedidos5; ?>%;"></div>
                            </div>
                            </td>
                        </tr>
                        <tr>
                        <td class="w-50">Pedidos entregues</td>
                            <td><?php echo $total_pedidos1; ?></td>
                            <td>
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-success" style="width:<?php echo $total_pedidos1; ?>%;"></div>
                            </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Pedidos a preparar</td>
                            <td><?php echo $total_pedidos2 ?></td>
                            <td class="w-50">
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-warning" style="width:<?php echo $total_pedidos2; ?>%;"></div>
                            </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Pedidos cancelados</td>
                            <td><?php echo $total_pedidos3 ?></td>
                            <td class="w-50">
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-danger" style="width:<?php echo $total_pedidos3; ?>%;"></div>
                            </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Pedidos a aguardar</td>
                            <td><?php echo $total_pedidos4 ?></td>
                            <td class="w-50">
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-secondary" style="width:<?php echo $total_pedidos4; ?>%;"></div>
                            </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                </div>


                <div style="width: 100%; margin-left: 30px; background-color: transparent;">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                            <div class="subheader">Total vendido</div>
                            </div>
                            <div class="h1 mb-3"><?php echo number_format($total_valor_pedido, 2, ',', '.'); ?>€</div>
                            <div class="d-flex mb-2">
                            <div>Alcance</div>
                            
                            </div>
                            <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" style="width:<?php echo $total_valor_pedido/2; ?>%;" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" aria-label="75% Complete">
                                
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>


                    <?php

                        if($role == 'Administrador'){

                    ?>

                    <div class="col-sm-6 col-lg-3" style="margin-top: 30px;">
                            <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-blue text-white avatar" style="margin-right: 10px;"><!-- Download SVG icon from http://tabler-icons.io/i/shopping-cart -->
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                
                                    <a href="lista-utilizadores.php" style="color: black">Entidades (<?php echo "$total_utilizadores"; ?>)</a>
                        
                                    </div>
                                    <div class="text-secondary">
                                    
                                    Ativos (<?php echo "$total_utilizadoresact"; ?>)

                                    </div>
                                </div>
                                </div> 
                            </div>
                            </div>
                        </div>

                        <?php
                            }
                        ?>  

                        <div class="col-sm-6 col-lg-3" style="margin-top: 30px;">
                            <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-purple text-white avatar" style="margin-right: 10px;"><!-- Download SVG icon from http://tabler-icons.io/i/shopping-cart -->
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                
                                    <a href="lista-utilizadores.php" style="color: black">Utentes (<?php echo "$total_utentes"; ?>)</a>
                        
                                    </div>
                                
                                </div>
                                </div>
                            </div>
                            </div>
                        </div>


                    <div class="col-sm-6 col-lg-3" style="margin-top: 30px">
                        <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-pink text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/brand-twitter -->
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-hearts"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14.017 18l-2.017 2l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 0 1 8.153 5.784" /><path d="M15.99 20l4.197 -4.223a2.81 2.81 0 0 0 0 -3.948a2.747 2.747 0 0 0 -3.91 -.007l-.28 .282l-.279 -.283a2.747 2.747 0 0 0 -3.91 -.007a2.81 2.81 0 0 0 -.007 3.948l4.182 4.238z" /></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    <a href="lista-agrefam.php" style="color: black">Agregados familiares (<?php echo "$total_agregados"; ?>)</a>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                        </div>                  
    <!-- SELECT *
    FROM pedidos
    ORDER BY data_pedido DESC
    LIMIT 1;   query do pedido mais recente-->                

                    </div>

                </div>
                
            </div>
            
            <img src="20945520.png" class="overlay-image responsive-image" alt="Imagem sobreposta">


            <!-- Footer -->
            <?php include 'footer.php'; ?>

        </div>
    </div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="./dist/js/tabler.min.js?1684106062" defer></script>
    <script src="./dist/js/demo.min.js?1684106062" defer></script>
    </body>
    </html>
