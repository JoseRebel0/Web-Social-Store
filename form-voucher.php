<?php
session_start();
require_once 'config.php'; // Inclua o seu arquivo de configuração para a conexão com o banco de dados

// Verifica se o utilizador está logado, se não retorna para a página login.php
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$page = "form-utentes";

$role = htmlspecialchars($_SESSION["cargo"]);

if ($role != 'Administrador') {
    header("location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = trim($_POST['id']);
    $plafon = trim($_POST['plafon']);
    $validade = trim($_POST['validade']);

    $validade = date('Y-m-d', strtotime($validade));

    if (!empty($plafon)) {
        // Iniciar uma transação
        mysqli_begin_transaction($conn);

        try {
            // Consulta para obter o plafond atual do voucher ativo
            $select_query = "SELECT plafond FROM voucher WHERE id_utente = ? AND estado = 'Ativo'";
            if ($stmt_select = mysqli_prepare($conn, $select_query)) {
                mysqli_stmt_bind_param($stmt_select, "i", $id);
                mysqli_stmt_execute($stmt_select);
                mysqli_stmt_bind_result($stmt_select, $old_plafon);
                mysqli_stmt_fetch($stmt_select);
                mysqli_stmt_close($stmt_select);
                
                // Atualizar o estado do voucher ativo para 'Inativo'
                if ($old_plafon !== null) {
                    $update_query = "UPDATE voucher SET estado = 'Inativo' WHERE id_utente = ? AND estado = 'Ativo'";
                    if ($stmt_update = mysqli_prepare($conn, $update_query)) {
                        mysqli_stmt_bind_param($stmt_update, "i", $id);
                        mysqli_stmt_execute($stmt_update);
                        mysqli_stmt_close($stmt_update);
                    } else {
                        throw new Exception("Erro na preparação da consulta de atualização.");
                    }
                    
                    // Somar o valor do antigo ao novo
                    $plafon += $old_plafon;
                }

                // Inserir o novo voucher
                $insert_query = "INSERT INTO voucher (id_utente, plafond, validade, estado) VALUES (?, ?, ?, 'Ativo')";
                if ($stmt_insert = mysqli_prepare($conn, $insert_query)) {
                    mysqli_stmt_bind_param($stmt_insert, "ids", $id, $plafon, $validade);
                    mysqli_stmt_execute($stmt_insert);
                    mysqli_stmt_close($stmt_insert);
                    
                    // Confirmar a transação
                    mysqli_commit($conn);
                    echo "<script type='text/javascript'>alert('Voucher atualizado com sucesso!'); window.location.href = 'lista-utentes.php';</script>";
                } else {
                    throw new Exception("Erro na preparação da consulta de inserção.");
                }
            } else {
                throw new Exception("Erro na preparação da consulta de seleção.");
            }
        } catch (Exception $e) {
            // Reverter a transação em caso de erro
            mysqli_rollback($conn);
            echo "<script type='text/javascript'>alert('Erro: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script type='text/javascript'>alert('Por favor, preencha todos os campos obrigatórios.')</script>";
    }
} else {
    $id_utente = isset($_GET['id_utente']) ? $_GET['id_utente'] : '';
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Form - Voucher</title>
    <!-- CSS files -->
    <link href="./dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="./dist/css/tabler-flags.min.css" rel="stylesheet"/>
    <link href="./dist/css/tabler-payments.min.css" rel="stylesheet"/>
    <link href="./dist/css/tabler-vendors.min.css" rel="stylesheet"/>
    <link href="./dist/css/demo.min.css" rel="stylesheet"/>
</head>
<body>
    <script src="./dist/js/demo-theme.min.js"></script>

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
                            <h2 class="page-title">Criar novo voucher</h2>
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
                                    <h4 class="card-title">Novo voucher</h4>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <?php 
                                        $id_utente = isset($_GET['id_utente']) ? $_GET['id_utente'] : ''; // pega o id_utente do GET
                                        ?>
                                        <input type="hidden" name="id" id="id" class="form-control" value="<?php echo htmlspecialchars($id_utente); ?>">
                                        
                                        <div class="col-sm-6 col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label required">Plafon (€)</label>
                                                <input type="text" name="plafon" class="form-control" placeholder="ex. 100" required>
                                            </div>
                                        </div>

                                        <div class="mb-3 col-sm-8 col-md-3">
                                            <label class="form-label required">Data de validade</label>
                                            <input id="validade" name="validade" type="date" class="form-control" required></input>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer text-end ">
                                    <a href="edit-utente.php" class="btn btn-light i">Voltar</a>
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
    <?php include 'footer.php'; ?>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

    <!-- Libs JS -->
    <script src="./dist/libs/nouislider/dist/nouislider.min.js" defer></script>
    <script src="./dist/libs/litepicker/dist/litepicker.js" defer></script>
    <script src="./dist/libs/tom-select/dist/js/tom-select.base.min.js" defer></script>
    <!-- Tabler Core -->
    <script src="./dist/js/tabler.min.js" defer></script>
    <script src="./dist/js/demo.min.js" defer></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-states'), {
                copyClassesToDropdown: false,
                dropdownParent: 'body',
                controlInput: '<input>',
                render: {
                    item: function (data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function (data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    }
                },
                onChange: function (value) {
                    el.closest('form').dispatchEvent(new Event('input'));
                },
            }));
        });
    </script>
</body>
</html>
