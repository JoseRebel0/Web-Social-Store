<?php

include ('config.php');

$id = $_GET['id'];
$query = "DELETE FROM agrefam WHERE id = $id";

$data = mysqli_query($conn, $query);

if($data) {

    echo "<script type='text/javascript'>
                    alert('Agregado apagado com sucesso!');
                    window.location.href = 'lista-agrefam.php';
                  </script>";
}

else {

    echo "<script type='text/javascript'>
                    alert('Falha ao apagar agregado.');
                    window.location.href = 'lista-agrefam.php';
                  </script>";;
}

?>