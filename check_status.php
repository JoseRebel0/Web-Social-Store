<?php

include 'config.php';

if (isset($_SESSION['id'])) {
    $id_user = $_SESSION['id'];

    $sql = "SELECT id_status FROM utilizadores WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("Erro ao preparar a consulta: " . $conn->error);
        exit;
    }

    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $stmt->bind_result($id_status);
    $stmt->fetch();
    $stmt->close();

    if ($id_status == '2') {
        error_log("Redirecionando para inativo.php devido ao utilizador estar inativo");
        header("Location: inativo.php");
        exit;
    }
} else {
    error_log("Redirecionando para login.php porque id_utilizador não está na sessão");
    header("Location: login.php");
    exit;
}
?>
