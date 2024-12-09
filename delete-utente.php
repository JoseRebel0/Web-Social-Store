<?php

include ('config.php');

$id = $_GET['id_utente'];
$query = "DELETE FROM utentes WHERE id_utente = $id";

$data = mysqli_query($conn, $query);

if($data) {

    echo "<script type='text/javascript'>
                    alert('Utente apagado com sucesso!');
                    window.location.href = 'lista-utentes.php';
                  </script>";
}

else {

    echo "<script type='text/javascript'>
                    alert('Falha ao apagar utente.');
                    window.location.href = 'lista-utentes.php';
                  </script>";;
}

?>