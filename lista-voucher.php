<?php

session_start();

include 'check_status.php';

// Verifica se o utilizador está logado, se não retorna para a página login.php
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$role = htmlspecialchars($_SESSION["cargo"]);
$getid = intval($_SESSION["id"]);

$page = 'lista-utentes';

// Verifica se há um termo de pesquisa
$searchQuery = "";
if (isset($_GET["query"]) && !empty($_GET["query"])) {
    $searchQuery = htmlspecialchars($_GET["query"]);
}

$id_utente = $_GET['id_utente'];


$sql = "SELECT DISTINCT voucher.id_voucher, voucher.id_utente as vid_utente , voucher.plafond, voucher.validade, voucher.estado, utentes.nome_utente
        FROM voucher
        INNER JOIN utentes ON voucher.id_utente = utentes.id_utente
        WHERE voucher.id_utente = $id_utente";

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Lista - Artigos</title>
    <!-- CSS files -->
    <link href="./dist/css/tabler.min.css?1684106062" rel="stylesheet"/>
    <link href="./dist/css/tabler-flags.min.css?1684106062" rel="stylesheet"/>
    <link href="./dist/css/tabler-payments.min.css?1684106062" rel="stylesheet"/>
    <link href="./dist/css/tabler-vendors.min.css?1684106062" rel="stylesheet"/>
    <link href="./dist/css/demo.min.css?1684106062" rel="stylesheet"/>
    <style>
        /* Estilos conforme fornecidos */
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
                            <h2 class="page-title">Lista de vouchers</h2>

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
                                    <th><button class="table-sort" data-sort="sort-id">ID</button></th>
                                    <th><button class="table-sort" data-sort="sort-utente">Utente</button></th>
                                    <th><button class="table-sort" data-sort="sort-plafond">Plafond</button></th>
                                    <th><button class="table-sort" data-sort="sort-validade">Validade</button></th>
                                    <th><button class="table-sort" data-sort="sort-estado">Estado</button></th>
                                    <th><button class="table-sort">Ações</button></th>
                                    <th></th>
                                    </tr>
                                </thead>
                                <tbody class="table-tbody">
                                <?php
                                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td class='sort-id'>" . htmlspecialchars($row['id_voucher']) . "</td>";
                                        echo "<td class='sort-utente'>" . htmlspecialchars($row['nome_utente']) . "</td>";
                                        echo "<td class='sort-plafond'>" . number_format($row['plafond'], 2, ',', '.') . "€</td>";
                                        echo "<td class='sort-validade'>" . htmlspecialchars($row['validade']) . "</td>";
                                        if($row['estado'] == 'Ativo') {
                                            echo "<td class='sort-estado' style='color: green' >" . htmlspecialchars($row['estado']) . "</td>";
                                        } else {
                                            echo "<td class='sort-estado' style='color: red'>" . htmlspecialchars($row['estado']) . "</td>";
                                        }

                                        if($row['vid_utente'] == $id_utente) {
                                        
                                            echo '<td><div class="delete">
                                                <a onclick="return alert(\'Este voucher não pode ser eliminado, pois existe um utente relacionado.\')" style="color: grey;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="grey" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0"/>
                                                        <path d="M10 11l0 6"/>
                                                        <path d="M14 11l0 6"/>
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                    </svg>
                                                    Eliminar
                                                </a>
                                            </div></td>';

                                        } else {


                                            echo '<td><div class="delete">
                                                <a href="delete-voucher.php?id_voucher=' . urlencode($row['id_voucher']) . '" onclick="return confirm(\'Tem a certeza que deseja apagar este voucher?\')" style="color: red;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0"/>
                                                        <path d="M10 11l0 6"/>
                                                        <path d="M14 11l0 6"/>
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                    </svg>
                                                    Eliminar
                                                </a>
                                            </div></td>';

                                        }
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>Nenhum voucher encontrado</td></tr>";
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Paginação -->
                        <?php
                        // Definir o número de items por página
                        $items_per_page = 10;

                        // Calcular o número total de items
                        $total_items_query = "SELECT COUNT(*) as total FROM voucher";
                        $total_items_result = mysqli_query($conn, $total_items_query);
                        $total_items_row = mysqli_fetch_assoc($total_items_result);
                        $total_items = $total_items_row['total'];
                        $total_pages = ceil($total_items / $items_per_page);

                        // Obter o número da página atual
                        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        if ($current_page < 1) {
                            $current_page = 1;
                        } elseif ($current_page > $total_pages && $total_pages > 0) {
                            $current_page = $total_pages;
                        }

                        $offset = ($current_page - 1) * $items_per_page;

                        $sql = "SELECT DISTINCT * FROM voucher ORDER BY id_voucher ASC LIMIT $items_per_page OFFSET $offset";
                        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        ?>

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const list = new List('table-default', {
            sortClass: 'table-sort',
            listClass: 'table-tbody',
            valueNames: ['sort-id', 'sort-utente', 'sort-plafond', 'sort-estado',{ attr: 'data-date', name: 'sort-validade' }]
        });
    });
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