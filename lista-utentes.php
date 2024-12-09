<?php
session_start();

include 'check_status.php';

// Verifica se o utilizador está logado, se não retorna para a página login.php
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$role = htmlspecialchars($_SESSION["cargo"]);
$getid = htmlspecialchars($_SESSION["id"]);

$page = 'lista-utentes';

// Verifica se há um termo de pesquisa
$searchQuery = "";
if (isset($_GET["query"]) && !empty($_GET["query"])) {
    $searchQuery = htmlspecialchars($_GET["query"]);
}

$sql = "SELECT 
    utentes.id_utente, 
    utentes.nome_utente, 
    utentes.telef, 
    utentes.email AS umail, 
    utentes.morada, 
    utentes.localidade, 
    utentes.cpostal, 
    utentes.cidade, 
    utentes.nif, 
    utentes.niss, 
    utentes.datacriacao, 
    utentes.observacoes, 
    utentes.id_utilizador, 
    utilizadores.username, 
    utilizadores.id, 
    voucher.plafond, 
    voucher.validade
FROM 
    utentes
LEFT JOIN 
    utilizadores ON utentes.id_utilizador = utilizadores.id
LEFT JOIN 
    voucher ON utentes.id_utente = voucher.id_utente
    AND voucher.validade = (
        SELECT MAX(v.validade)
        FROM voucher v
        WHERE v.id_utente = utentes.id_utente
    )";

// Adiciona WHERE se o utilizador não for um administrador
if ($role != 'Administrador') {
    $sql .= " WHERE utentes.id_utilizador = '$getid'";
}

// Verifica se há uma query de pesquisa
if (!empty($searchQuery)) {
    if ($role == 'Administrador') {
        $sql .= " WHERE nome_utente LIKE '%$searchQuery%' OR utentes.nif LIKE '%$searchQuery%' OR utentes.cidade LIKE '%$searchQuery%' OR utentes.telef LIKE '%$searchQuery%'";
    } else {
        $sql .= " AND (nome_utente LIKE '%$searchQuery%' OR utentes.nif LIKE '%$searchQuery%' OR utentes.cidade LIKE '%$searchQuery%' OR utentes.telef LIKE '%$searchQuery%')";
    }
}

// Verifica se os filtros são aplicados
if (isset($_POST['apply_filters'])) {
    $id_utilizador = ($role == 'Administrador') ? $_POST['id_utilizador'] : $getid;
    $data_inicio = isset($_POST['data_inicio']) ? $_POST['data_inicio'] : '';
    $data_fim = isset($_POST['data_fim']) ? $_POST['data_fim'] : '';

    $conditions = [];

    if (!empty($id_utilizador)) {
        $conditions[] = "utentes.id_utilizador = '$id_utilizador'";
    }
    if (!empty($data_inicio) && !empty($data_fim)) {
        $conditions[] = "utentes.datacriacao BETWEEN '$data_inicio' AND '$data_fim'";
    }

    // Adiciona as condições à query
    if (count($conditions) > 0) {
        if (strpos($sql, 'WHERE') === false) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        } else {
            $sql .= " AND " . implode(' AND ', $conditions);
        }
    }
}

// Verificação da validade do voucher, elimina o voucher se passar da data de validade
$t = time();
$query = "SELECT * FROM voucher WHERE estado != 'Inativo'";
$result = mysqli_query($conn, $query) or die('error');

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['validade'] < date("Y-m-d H:i:s", $t)) {
        $updateQuery = "UPDATE voucher SET estado = 'Inativo' WHERE id_utente = " . $row['id_utente'] . " AND validade < CURRENT_TIMESTAMP();";
        mysqli_query($conn, $updateQuery) or die('error');
        echo "<script type='text/javascript'> alert('A validade deste voucher expirou.') </script>";
    }
}
?>


    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title>Lista - Utentes</title>
        <!-- CSS files -->
        <link href="./dist/css/tabler.min.css?1684106062" rel="stylesheet" />
        <link href="./dist/css/tabler-flags.min.css?1684106062" rel="stylesheet" />
        <link href="./dist/css/tabler-payments.min.css?1684106062" rel="stylesheet" />
        <link href="./dist/css/tabler-vendors.min.css?1684106062" rel="stylesheet" />
        <link href="./dist/css/demo.min.css?1684106062" rel="stylesheet" />
        <style>
            @import url('https://rsms.me/inter/inter.css');

            :root {
                --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
            }

            body {
                font-feature-settings: "cv03", "cv04", "cv11";
            }

            .title-butt {
                width: 100%;
                height: auto;
                display: flex;
                justify-content: space-between;
            }

            * {
                padding: 0;
                margin: 0;
            }

            .bottom-field {
                width: 100%;
                padding: 20px;
                margin-top: 20px;
            }

            .pagination {
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .pagination li {
                list-style: none;
                padding: 2px;
                margin: 10px;
                flex-shrink: 0;
                text-align: center;
                border-radius: 5px;
                border: 1px solid #999;
                color: #999;
            }

            .pagination li.active {
                background: #264096;
                text-decoration: none;
                color: white;
                border-color: #264096;
                padding: 3px 8px;
                display: block;
            }

            .pagination li a {
                text-decoration: none;
                color: inherit;
                padding: 3px 8px;
                display: block;
            }

            .items-controler {
                width: 100%;
                display: flex;
                flex-shrink: 0;
                justify-content: flex-start;
                align-items: center;
                margin-top: 1%;
            }

            select {
                padding: 2px;
                margin: 0 10px;
                outline: none;
                border: none;
                cursor: pointer;
                background-color: transparent;
            }

            .delete {
                color: red;
                cursor: pointer;
            }
        </style>
    </head>

    <body>
        <script src="./dist/js/demo-theme.min.js?1684106062"></script>
        <div class="page">
            <!-- Header -->
            <?php include 'header.php'; ?>
            <!-- Navbar -->
            <?php include 'navbar.php'; ?>
            <div class="page-wrapper">
                <!-- Page header -->
                <div class="page-header d-print-none">
                    <div class="container-xl">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <br>
                                <div class="title-butt">
                                    <h2 class="page-title">Lista de utentes</h2>


                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" autocomplete="off" novalidate>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/search -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                        </span>
                                        <input type="text" name="query" value="<?php echo $searchQuery; ?>" class="form-control" placeholder="Pesquisar" aria-label="Search in website">
                                    </div>
                                </form>

                                </div>
                            </div>
                        </div>

                        <br>

                        <form id="filterForm" method="POST">
                            <?php
                                if($role == 'Administrador'){
                            ?>
                            <p>Filtrar por:</p>

                            <?php
                                }
                            ?>
                            <div style="width: 100%, height: auto; display: flex;   ">

                                <?php
                                if ($role == 'Administrador') {
                                    echo '
                                    <div class="mb-3 col-sm-8 col-md-2" style="margin-right: 43px"  >
                                        <select id="id_utilizador" name="id_utilizador" class="form-select">
                                            <option value="">Entidade</option>';

                                            $query = "SELECT DISTINCT utentes.id_utilizador, utilizadores.username FROM utentes INNER JOIN utilizadores ON utentes.id_utilizador = utilizadores.id";
                                            $result = mysqli_query($conn, $query) or die('error');
                                            if (mysqli_num_rows($result) > 0) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo '<option value="' . htmlspecialchars(trim($row['id_utilizador'])) . '">' . htmlspecialchars(trim($row['username'])) . '</option>';
                                                }
                                            }
                                            
                                    echo '
                                        </select>
                                    </div>';
                                }
                                ?>
                    </div>
                    <?php
                        if($role == 'Administrador'){
                    ?>
                    <button class="btn btn-primary" type="submit" name="apply_filters">Aplicar Filtros</button>
                    <a class="btn btn-secondary" href="lista-utentes.php">Limpar Filtros</a>

                    <?php
                        }
                    ?>
                </form>

                <br>
                <div class="items-controler">
                <h5>Mostrar</h5>
                <select id="itemperpage">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
                <h5>items por página</h5>
                </div>

                </div>
                
            </div>
            <div class="page-body">
            </div>
                <div class="container-xl">
                    <div class="card">
                        <div class="card-body">
                            <div id="table-default" class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                        <th><button class="table-sort" data-sort="sort-nome">Nome</button></th>
                                        <th><button class="table-sort" data-sort="sort-entidade">Entidade</button></th>
                                        <th><button class="table-sort" data-sort="sort-contacto">Contacto</button></th>  
                                        <th><button class="table-sort" data-sort="sort-email">Email</button></th>
                                        <th><button class="table-sort" data-sort="sort-plafond">Plafond</button></th>             
                                        <th><button class="table-sort" data-sort="sort-data">Data</button></th>
                                        <th><button class="table-sort">Ações</button></th>
                                        <th></th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-tbody">
                                    <?php
                                    $result = mysqli_query($conn, $sql) or die('error');
                                    while ($row = mysqli_fetch_assoc($result)) {
            
                                        $nome_utente = $row['nome_utente'];
                                        $username = $row['username'];
                                        $telef = $row['telef'];
                                        $umail = $row['umail'];
                                        $datacriacao = $row['datacriacao'];
                                        $voucher_plafond = $row['plafond'];
                                        $voucher_validade = $row['validade'];
                                    ?>
                                        <tr>
                                            <td class="sort-nome"><?php echo htmlspecialchars($nome_utente); ?></td>
                                            <td class="sort-entidade"><?php echo htmlspecialchars($username); ?></td>
                                            <?php
                                              if($row['telef'] == null or $row['telef'] == '0'){
                                                $telef = 'Sem contacto';
                                              }
                                            ?>
                                            <td class="sort-contacto"><?php echo htmlspecialchars($telef); ?></td>
                                            <td class="sort-email"><?php echo htmlspecialchars($umail); ?></td>
                                            <?php
                                            $query = "SELECT DISTINCT plafond FROM voucher WHERE id_utente = " . $row['id_utente'];
                                            if($row['id_utilizador'] == 36){
                                                if($row['plafond'] != null){
                                                    if($row['validade'] < date("Y-m-d H:i:s", $t)){
                                                        echo'Vouncher expirado';
                                                    }
                                                    else{
                                                        echo "<td class='sort-plafond'>" . number_format($row['plafond'], 2, ',', '.') . "€</td>";
                                                    }
                                                }
                                                else{
                                                    echo "<td class='sort-data'>Sem plafond</td>";
                                                }
                                            }
                                            else{
                                                echo "<td class='sort-data'>Sem plafond</td>";
                                            }
                                            ?>

                                            <td><?php echo htmlspecialchars($datacriacao); ?></td>

                                            <?php
                                            echo '<td>
                                            <a href="edit-utente.php?id_utente=' . urlencode($row['id_utente']) . 
                                            '&nome_utente=' . urlencode($row['nome_utente']) . 
                                            '&telef=' . urlencode($row['telef']) . 
                                            '&email=' . urlencode($row['umail']) . 
                                            '&morada=' . urlencode($row['morada']) . 
                                            '&localidade=' . urlencode($row['localidade']) . 
                                            '&cpostal=' . urlencode($row['cpostal']) . 
                                            '&cidade=' . urlencode($row['cidade']) . 
                                            '&nif=' . urlencode($row['nif']) . 
                                            '&niss=' . urlencode($row['niss']) . 
                                            '&observacoes=' . urlencode($row['observacoes']) . 
                                            '&id=' . urlencode($row['id']) . 
                                            '&username=' . urlencode($row['username']) . 
                                            '&id_utilizador ='.urlencode($row['id_utilizador']).'">
                                            
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-pencil i">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                    <path d="M13.5 6.5l4 4" />
                                                </svg>
                                                Editar
                                            </a>
                                        </td>';

                                        $squery = "SELECT DISTINCT utilizadores.id, utentes.id_utilizador, utentes.id_utente, pedidos.id_utentep, agrefam.id_utente
                                                FROM utentes
                                                INNER JOIN utilizadores ON utentes.id_utilizador = utilizadores.id
                                                LEFT JOIN pedidos ON utentes.id_utente = pedidos.id_utentep
                                                LEFT JOIN agrefam ON utentes.id_utente = agrefam.id_utente
                                                WHERE utentes.id_utente = " . $row['id_utente'];
                                        $sresult = mysqli_query($conn, $squery) or die(mysqli_error($conn));

                                        $pedido_utente_id = null;
                                        $utilizador_id = null;
                                        $agrefam_id = null;

                                        if ($srow = mysqli_fetch_assoc($sresult)) {

                                            $utilizador_id = $srow['id_utilizador'];
                                            $pedido_utente_id = $srow['id_utentep'];
                                            $agrefam_id = $srow['id_utente'];
                                        }

                                        if($role == 'Administrador'){

                                            if($pedido_utente_id === null && $utilizador_id === null && $agrefam_id == null){

                                                $i_u = $row['id_utente'];
                                                echo '<td><div class="delete">
                                                <a href="delete-utente.php?id_utente=' . urlencode($i_u) . '" onclick="return confirm(\'Tem a certeza que deseja apagar este utente?\')" style="color: red;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0" />
                                                        <path d="M10 11l0 6" />
                                                        <path d="M14 11l0 6" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                    </svg>
                                                    Eliminar
                                                </a>
                                                </div></td>';
                                            }else{
                                                
                                                echo '<td><div class="delete">
                                                <a onclick="return alert(\'Este utente não pode ser eliminado,pois possui pedidos, entidades e/ou agregados familiares relacionados.\')" style="color: grey;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="grey " stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0" />
                                                        <path d="M10 11l0 6" />
                                                        <path d="M14 11l0 6" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                    </svg>
                                                    Eliminar
                                                </a>
                                                </div></td>';
                                            }
                                        }

                                 
                                        if($row['id_utilizador'] == 36){

                                            echo '<td><a href="lista-voucher.php?id_utente=' . urlencode($row['id_utente']) . '">Listar vouchers</a></td>';

                                        } else {
                                            echo '';
                                        }
                                
                                        ?>

                                        
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                </div>
            </div>
            <div class="bottom-field">
                            <ul class="pagination">
                                <li class="prev"><a href="#" id="prev">&#139;</a></li>
                                <li class="next"><a href="#" id="next">&#155;</a></li>
                            </ul>
                        </div>
        </div>
        
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>


    <!-- Libs JS -->
    <script src="./dist/libs/list.js/dist/list.min.js?1684106062" defer></script>
        <!-- Tabler Core -->
        <script src="./dist/js/tabler.min.js?1684106062" defer></script>
        <script src="./dist/js/demo.min.js?1684106062" defer></script>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
        const list = new List('table-default', {
            sortClass: 'table-sort',
            listClass: 'table-tbody',
            valueNames: [ 'sort-nome', 'sort-entidade', 'sort-contacto', 'sort-email', 'sort-plafond',
                { attr: 'data-date', name: 'sort-data' },
            ]
        });
        })
        </script>
        <script>
    var tbody = document.querySelector("tbody");
            var pageUl = document.querySelector(".pagination");
            var itemShow = document.querySelector("#itemperpage");
            var tr = tbody.querySelectorAll("tr");
            var emptyBox = [];
            var index = 1;
            var itemPerPage = 10;

            for(let i=0; i<tr.length; i++){ emptyBox.push(tr[i]);}

            itemShow.onchange = giveTrPerPage;
            function giveTrPerPage(){
                itemPerPage = Number(this.value);
                // console.log(itemPerPage);
                displayPage(itemPerPage);
                pageGenerator(itemPerPage);
                getpagElement(itemPerPage);
            }

            function displayPage(limit){
                tbody.innerHTML = '';
                for(let i=0; i<limit; i++){
                    tbody.appendChild(emptyBox[i]);
                }
                const  pageNum = pageUl.querySelectorAll('.list');
                pageNum.forEach(n => n.remove());
            }
            displayPage(itemPerPage);

            function pageGenerator(getem){
                const num_of_tr = emptyBox.length;
                if(num_of_tr <= getem){
                    pageUl.style.display = 'none';
                }else{
                    pageUl.style.display = 'flex';
                    const num_Of_Page = Math.ceil(num_of_tr/getem);
                    for(i=1; i<=num_Of_Page; i++){
                        const li = document.createElement('li'); li.className = 'list';
                        const a =document.createElement('a'); a.href = '#'; a.innerText = i;
                        a.setAttribute('data-page', i);
                        li.appendChild(a);
                        pageUl.insertBefore(li,pageUl.querySelector('.next'));
                    }
                }
            }
            pageGenerator(itemPerPage);
            let pageLink = pageUl.querySelectorAll("a");
            let lastPage =  pageLink.length - 2;
            
            function pageRunner(page, items, lastPage, active){
                for(button of page){
                    button.onclick = e=>{
                        const page_num = e.target.getAttribute('data-page');
                        const page_mover = e.target.getAttribute('id');
                        if(page_num != null){
                            index = page_num;

                        }else{
                            if(page_mover === "next"){
                                index++;
                                if(index >= lastPage){
                                    index = lastPage;
                                }
                            }else{
                                index--;
                                if(index <= 1){
                                    index = 1;
                                }
                            }
                        }
                        pageMaker(index, items, active);
                    }
                }

            }
            var pageLi = pageUl.querySelectorAll('.list'); pageLi[0].classList.add("active");
            pageRunner(pageLink, itemPerPage, lastPage, pageLi);

            function getpagElement(val){
                let pagelink = pageUl.querySelectorAll("a");
                let lastpage =  pagelink.length - 2;
                let pageli = pageUl.querySelectorAll('.list');
                pageli[0].classList.add("active");
                pageRunner(pagelink, val, lastpage, pageli);
                
            }
        
            
            
            function pageMaker(index, item_per_page, activePage){
                const start = item_per_page * index;
                const end  = start + item_per_page;
                const current_page =  emptyBox.slice((start - item_per_page), (end-item_per_page));
                tbody.innerHTML = "";
                for(let j=0; j<current_page.length; j++){
                    let item = current_page[j];					
                    tbody.appendChild(item);
                }
                Array.from(activePage).forEach((e)=>{e.classList.remove("active");});
                activePage[index-1].classList.add("active");
            }


        </script>
    </body>
    </html>