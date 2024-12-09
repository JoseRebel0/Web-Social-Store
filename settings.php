<?php
session_start();

require_once 'check_status.php';
require_once 'config.php'; // Arquivo de configuração do banco de dados

// Verifica se o utilizador está logado, se não, redireciona para a página login.php
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$page = 'settings';

$nome = htmlspecialchars($_SESSION["username"]);
$id = htmlspecialchars($_SESSION["id"]);

// Consulta para buscar dados do utilizador
$query = "SELECT * FROM utilizadores WHERE id = ?";

if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "Erro ao buscar dados do utilizador.";
        exit;
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Erro ao preparar a consulta.";
    exit;
}

// Processamento do formulário quando submetido
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $nome_entidade = $_POST['nome_entidade'];
    $nome_de_contacto = $_POST['nome_de_contacto'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Verifica se os campos obrigatórios estão preenchidos
    if (!empty($nome_entidade) && !empty($nome_de_contacto) && !empty($username) && !empty($email)) {

        // Verifica se foi enviado um arquivo de avatar
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatar_temp = $_FILES['avatar']['tmp_name'];
            $avatar_path = 'images/' . basename($_FILES['avatar']['name']); // Caminho onde será salvo o avatar

            // Move o arquivo para o diretório desejado
            if (move_uploaded_file($avatar_temp, $avatar_path)) {
                $sqlquery = "UPDATE utilizadores SET 
                    username = ?, 
                    nome_entidade = ?, 
                    nome_de_contacto = ?,  
                    email = ?, 
                    avatar = ?";
                
                // Inclui o campo password se ele foi preenchido
                if ($password) {
                    $sqlquery .= ", password = ?";
                }

                $sqlquery .= " WHERE id = ?";

                if ($stmt = mysqli_prepare($conn, $sqlquery)) {
                    if ($password) {
                        mysqli_stmt_bind_param($stmt, "ssssssi", 
                            $username,  
                            $nome_entidade, 
                            $nome_de_contacto, 
                            $email, 
                            $avatar_path,
                            $password,
                            $id
                        );
                    } else {
                        mysqli_stmt_bind_param($stmt, "sssssi", 
                            $username,  
                            $nome_entidade, 
                            $nome_de_contacto, 
                            $email, 
                            $avatar_path,
                            $id
                        );
                    }

                    if (mysqli_stmt_execute($stmt)) {
                        if ($password) {
                            echo "<script type='text/javascript'>
                                    alert('Palavra-passe alterada com sucesso!');
                                    window.location.href = 'logout.php';
                                  </script>";
                            exit();
                        } else {
                            echo "<script type='text/javascript'>
                                    alert('Entidade editada com sucesso!');
                                    window.location.href = 'index.php';
                                  </script>";
                            exit();
                        }
                    } else {
                        echo "<script type='text/javascript'> alert('Falha ao editar a entidade.') </script>";
                    }

                    mysqli_stmt_close($stmt);
                }
            } else {
                echo "<script type='text/javascript'> alert('Falha ao fazer upload do avatar.') </script>";
            }
        } else {
            $sqlquery = "UPDATE utilizadores SET 
                username = ?, 
                nome_entidade = ?, 
                nome_de_contacto = ?,  
                email = ?";
            
            if ($password) {
                $sqlquery .= ", password = ?";
            }

            $sqlquery .= " WHERE id = ?";

            if ($stmt = mysqli_prepare($conn, $sqlquery)) {
                if ($password) {
                    mysqli_stmt_bind_param($stmt, "sssssi", 
                        $username,  
                        $nome_entidade, 
                        $nome_de_contacto, 
                        $email, 
                        $password,
                        $id
                    );
                } else {
                    mysqli_stmt_bind_param($stmt, "ssssi", 
                        $username,  
                        $nome_entidade, 
                        $nome_de_contacto, 
                        $email, 
                        $id
                    );
                }

                if (mysqli_stmt_execute($stmt)) {
                    if ($password) {
                        echo "<script type='text/javascript'>
                                alert('Dados alterados com sucesso!');
                                window.location.href = 'logout.php';
                              </script>";
                        exit();
                    } else {
                        echo "<script type='text/javascript'>
                                alert('Dados editados com sucesso!');
                                window.location.href = 'index.php';
                              </script>";
                        exit();
                    }
                } else {
                    echo "<script type='text/javascript'> alert('Falha ao editar os dados.') </script>";
                }

                mysqli_stmt_close($stmt);
            }
        }
    } else {
        echo "<script type='text/javascript'> alert('Por favor preencha todos os campos obrigatórios.') </script>";
    }
}

mysqli_close($conn);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Definições</title>
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
    <?php require_once 'header.php'; ?>


    <!-- Navbar -->
    <?php require_once 'navbar.php'; ?>

    <div class="page-wrapper">
        <!-- Page header -->
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                            Definições da conta
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page body -->
        <div class="page-body">
            <div class="container-xl">
                <div class="card">
                    <div class="row g-0">
                        <div class="col d-flex flex-column">
                            <form class="card" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" name="userform" enctype="multipart/form-data">
                                <div class="card-body">
                                    <h2 class="mb-4">A minha conta</h2>
                                    <h3 class="card-title">Detalhes do perfil</h3>
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="avatar avatar-xl" style="background-image: url('<?php echo !empty($row['avatar']) ? $row['avatar'] : 'blankprofile.png'; ?>')"></span>
                                        </div>
                                        <div class="col-auto">
                                            <input type="file" id="avatar" name="avatar">
                                        </div>
                                        <div class="col-auto"><a href="delete-avatar.php" class="btn btn-ghost-danger">
                                            Apagar avatar
                                        </a></div>
                                    </div>
                                    <h3 class="card-title mt-4">Sobre ti</h3>
                                    <div class="row g-3">
                                        <div class="col-md">
                                            <div class="form-label">Nome de utilizador</div>
                                            <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($nome) ? trim($nome) : ''; ?>">
                                        </div>
                                        <div class="col-md">
                                            <div class="form-label" for="nome_entidade">Nome de entidade</div>
                                            <input type="text" id="nome_entidade" name="nome_entidade" class="form-control" value="<?php echo isset($row['nome_entidade']) ? trim($row['nome_entidade']) : ''; ?>">
                                        </div>
                                        <div class="col-md">
                                            <div class="form-label" for="nome_de_contacto">Nome de contacto</div>
                                            <input type="text" name="nome_de_contacto" id="nome_de_contacto" class="form-control" value="<?php echo isset($row['nome_de_contacto']) ? trim($row['nome_de_contacto']) : ''; ?>">
                                        </div>
                                    </div>

                                    <h3 class="card-title mt-4" for="email">Email</h3>
                                    <div>
                                        <div class="row g-2">
                                            <div class="col-auto">
                                                <input type="email" class="form-control w-auto" value="<?php echo isset($row['email']) ? trim($row['email']) : ''; ?>" placeholder="Email" id="email" name="email" required>
                                            </div>
                                        </div>

                                        <h3 class="card-title mt-4">Palavra-passe</h3>
                                        <div>
                                            <input type="password" placeholder="Palavra-passe" name="password" id="password">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent mt-auto">
                                    <div class="btn-list justify-content-end">
                                        <a href="index.php" class="btn">
                                            Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary" value="Submit">Submeter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php require_once 'footer.php'; ?>

    </div>
</div>
<!-- Libs JS -->
<!-- Tabler Core -->
<script src="./dist/js/tabler.min.js?1684106062" defer></script>
<script src="./dist/js/demo.min.js?1684106062" defer></script>
</body>
</html>
