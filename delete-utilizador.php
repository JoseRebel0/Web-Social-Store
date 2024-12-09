<?php

include ('config.php');

$id = $_GET['id'];
$query = "DELETE FROM utilizadores WHERE id = $id";

$data = mysqli_query($conn, $query);

if($data) {

    echo "<script type='text/javascript'>
                    alert('Entidade apagada com sucesso!');
                    window.location.href = 'lista-utilizadores.php';
                  </script>";
}

else {

    echo "<script type='text/javascript'>
                    alert('Falha ao apagar entidade.');
                    window.location.href = 'lista-utilizadores.php';
                  </script>";;
}

?>