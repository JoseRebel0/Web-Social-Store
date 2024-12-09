<?php
session_start();

require_once 'check_status.php';

// Verifica se o utilizador está logado, se não, redireciona para a página login.php
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$id = $_SESSION["id"];


$query = "UPDATE utilizadores SET avatar = NULL WHERE id = ?";

if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {

        header("Location: settings.php");
        exit();
    } else {
        echo "Erro ao apagar o avatar.";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Erro ao preparar a query.";
}

mysqli_close($conn);
?>
