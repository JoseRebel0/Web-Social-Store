<?php

include ('config.php');

$id = $_GET['id_artigo'];
$query = "DELETE FROM artigos WHERE id_artigo = $id";

$data = mysqli_query($conn, $query);

if($data) {

    echo "<script type='text/javascript'>
                    alert('Artigo apagado com sucesso!');
                    window.location.href = 'lista-artigos.php';
                  </script>";
}

else {

    echo "<script type='text/javascript'>
                    alert('Falha ao apagar artigo.');
                    window.location.href = 'lista-artigos.php';
                  </script>";;
}

?>