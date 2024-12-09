<?php

include ('config.php');

$id = $_GET['id_pedido'];
$query = "DELETE FROM pedidos WHERE id_pedido = $id";

$data = mysqli_query($conn, $query);

if($data) {
  
    echo "<script type='text/javascript'>
                    alert('Pedido apagado com sucesso!');
                    window.location.href = 'lista-pedidos.php';
                  </script>";
}

else {

    echo "<script type='text/javascript'>
                    alert('Falha ao apagar pedido.');
                    window.location.href = 'lista-pedidos.php';
                  </script>";
}

?>