<?php

include_once 'check_status.php';

function filterData(&$str)
{
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

$fileName = "lista-pedidos_" . date('Ymd') . ".xlsx";

$fields = array('ID', 'Utente', 'Entidade', 'Artigo', 'Tamanho', 'Sexo', 'Estação', 'Quantidade', 'Preço', 'Estado', 'Nº Fatura', 'Data');

$excelData = implode("\t", array_values($fields)) . "\n";

$query = $conn -> query("SELECT DISTINCT pedidos.observacoes, pedidos.id_utentep, pedidos.id_artigop, pedidos.valor_pedido, pedidos.id_tamanhop, pedidos.id_estado, estado.designacao, pedidos.id_pedido, utentes.nome_utente, utilizadores.username, utilizadores.nome_entidade, artigos.designacao_artigo, tamanhos.tamanho, pedidos.sexo, pedidos.estacao, pedidos.quantidade, pedidos.data_pedido, utentes.id_utilizador, pedidos.num_fatura
        FROM pedidos
        LEFT JOIN utentes ON pedidos.id_utentep = utentes.id_utente
        LEFT JOIN utilizadores ON pedidos.id_utilizador = utilizadores.id
        LEFT JOIN artigos ON pedidos.id_artigop = artigos.id_artigo
        LEFT JOIN tamanhos ON pedidos.id_tamanhop = tamanhos.id_tamanho
        INNER JOIN estado ON pedidos.id_estado = estado.id_estado");

if($query->num_rows > 0){
    while($row = $query->fetch_assoc()){
        $rowData = array($row['id_pedido'], $row['nome_utente'], $row['username'], $row['designacao_artigo'], $row['tamanho'], $row['sexo'], $row['estacao'], $row['quantidade'], $row['valor_pedido'], $row['designacao'], $row['num_fatura'], $row['data_pedido']);
        array_walk($rowData, 'filterData');
        $excelData .= implode("\t", array_values($rowData)) . "\n";
    }
} else {
    $excelData .= 'Sem resultados'."\n";
}

header("Content-type: application/xlsx");
header("Content-Disposition: attachment; filename=$fileName");

echo $excelData;
exit;
?>