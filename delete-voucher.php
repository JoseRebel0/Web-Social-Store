<?php

include ('config.php');

$id = $_GET['id_voucher'];
$query = "DELETE FROM voucher WHERE id_voucher = $id";

$data = mysqli_query($conn, $query);

if($data) {
  
    echo "<script type='text/javascript'>
                    alert('Voucher apagado com sucesso!');
                    window.location.href = 'lista-utentes.php';
                  </script>";
}

else {

    echo "<script type='text/javascript'>
                    alert('Falha ao apagar voucher.');
                    window.location.href = 'lista-utentes.php';
                  </script>";
}

?>