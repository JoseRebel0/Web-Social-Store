<?php
session_start();
require_once 'check_status.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$role = htmlspecialchars($_SESSION["cargo"]);
if ($role != 'Administrador') {
    header("location: index.php");
    exit;
}

$page = 'edit-utilizador';
$password = $confirm_password = "";
$password_err = $confirm_password_err = "";

require_once 'config.php';

if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Validar a palavra-passe
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, insira uma palavra-passe.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "A palavra-passe deve conter pelo menos 6 caracteres.";
        echo "<script type='text/javascript'> alert('$password_err') </script>";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validar a confirmação da palavra-passe
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Por favor, confirme a palavra-passe.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "As palavras-passe não coincidem.";
            echo "<script type='text/javascript'> alert('$confirm_password_err') </script>";
        }
    }

    // Verificar se não há erros antes de atualizar a base de dados
    if (empty($password_err) && empty($confirm_password_err)) {
        $sqlquery = "UPDATE utilizadores SET password = ? WHERE id = ?";

        if ($stmt = mysqli_prepare($conn, $sqlquery)) {
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $id = $_POST['id'];

            // Depuração: exibir parâmetros
            echo "<script type='text/javascript'> console.log('ID: " . $id . "'); console.log('Hashed Password: " . $param_password . "'); </script>";

            mysqli_stmt_bind_param($stmt, "si", $param_password, $id);
            
            if (mysqli_stmt_execute($stmt)) {
                echo "<script type='text/javascript'>
                        alert('Palavra-passe editada com sucesso!');
                        window.location.href = 'lista-utilizadores.php';
                      </script>";
                exit();
            } else {
                echo "<script type='text/javascript'> alert('Falha ao editar a palavra-passe. Erro: " . mysqli_error($conn) . "') </script>";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "<script type='text/javascript'> alert('Falha ao preparar a declaração SQL. Erro: " . mysqli_error($conn) . "') </script>";
        }
    } else {
        echo "<script type='text/javascript'> alert('Falha ao editar a palavra-passe.') </script>";
    }

    mysqli_close($conn);
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Editar - Entidade</title>
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

        i {
            margin-right: 10px;
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
                        <h2 class="page-title">Editar entidade</h2>
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
                                <h4 class="card-title">Editar palavra-passe</h4>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <input type="hidden" name="id" id="id" class="form-control" value="<?php echo trim($_GET['id']); ?>">
                                    <div class="col-sm-6 col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label required">Palavra-passe</label>
                                            <input type="password" name="password" class="form-control" placeholder="Palavra-passe" required <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>>
                                            <span class="invalid-feedback"><?php echo $password_err; ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label required">Confirmar Palavra-passe</label>
                                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirmar Palavra-passe" required <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>>
                                            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-end ">
                                <a href="lista-utilizadores.php" class="btn btn-light i">Voltar</a>
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
