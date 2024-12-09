<?php
session_start();

include 'check_status.php';

// verifica se o utilizador está logado, se não retorna para a página login.php
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$role = htmlspecialchars($_SESSION["cargo"]);
$getid = htmlspecialchars($_SESSION["id"]);

$page = 'lista-utentes';

// Initialize SQL query
if ($role == 'Administrador') {
    $sql = "SELECT utentes.id_utente, utentes.nome_utente, utentes.telef, utentes.email as umail, utentes.morada, utentes.localidade, utentes.cpostal, utentes.cidade, utentes.nif, utentes.niss, utentes.datacriacao, utentes.observacoes, utentes.id_utilizador, utilizadores.username, utilizadores.id
            FROM utentes
            LEFT JOIN utilizadores ON utentes.id_utilizador = utilizadores.id";
} else {
    $sql = "SELECT utentes.id_utente, utentes.nome_utente, utentes.telef, utentes.email, utentes.morada, utentes.localidade, utentes.cpostal, utentes.cidade, utentes.nif, utentes.niss, utentes.datacriacao, utentes.observacoes, utentes.id_utilizador, utilizadores.username, utilizadores.id
            FROM utentes
            LEFT JOIN utilizadores ON utentes.id_utilizador = utilizadores.id
            WHERE utentes.id_utilizador = '$getid'";
}

// Check if filters are applied
if (isset($_POST['apply_filters'])) {
    $nome_utente = $_POST['nome_utente'];
    $id_utilizador = $_POST['id_utilizador'];
    $telef = $_POST['telef'];

    $conditions = [];

    if (!empty($nome_utente)) {
        $conditions[] = "utentes.nome_utente = '$nome_utente'";
    }
    if (!empty($id_utilizador)) {
        $conditions[] = "utentes.id_utilizador = '$id_utilizador'";
    }
    if (!empty($telef)) {
        $conditions[] = "utentes.telef = '$telef'";
    }

    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Lista - Pedidos</title>
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
                                <h2 class="page-title">Lista de pedidos</h2>
                            </div>
                        </div>
                    </div>

                    <br>

                    <form id="filterForm" method="POST">
                        <p>Filtrar por:</p>
                        <div style="width: 100%, height: auto; display: flex; justify-content: space-between;">
                            <div class="mb-3 col-sm-8 col-md-2">
                                <select id="nome_utente" name="nome_utente" class="form-select">
                                    <option value="">Nome</option>
                                    <?php
                                    if ($role == 'Administrador') {
                                        $query = "SELECT DISTINCT nome_utente FROM utentes";
                                    } else {
                                        $query = "SELECT DISTINCT nome_utente FROM utentes INNER JOIN utilizadores ON utentes.id_utilizador = utilizadores.id WHERE id_utilizador = '$getid'";
                                    }
                                    $result = mysqli_query($conn, $query) or die('error');
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo '<option value="' . htmlspecialchars(trim($row['nome_utente'])) . '">' . htmlspecialchars(trim($row['nome_utente'])) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <?php
                            if ($role == 'Administrador') {
                                echo '
                                <div class="mb-3 col-sm-8 col-md-2">
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

                            <div class="mb-3 col-sm-8 col-md-2">
                                <select id="telef" name="telef" class="form-select">
                                    <option value="">Contacto</option>
                                    <?php
                                    if ($role == 'Administrador') {
                                        $query = "SELECT DISTINCT telef FROM utentes";
                                    } else {
                                        $query = "SELECT DISTINCT telef FROM utentes INNER JOIN utilizadores ON utentes.id_utilizador = utilizadores.id WHERE id_utilizador = '$getid'";
                                    }
                                    $result = mysqli_query($conn, $query) or die('error');
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo '<option value="' . trim($row['telef']) . '">' . trim($row['telef']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                </div>
                <button class="btn btn-primary" type="submit" name="apply_filters">Aplicar Filtros</button>
                <a class="btn btn-secondary" href="lista-pedidos.php">Limpar Filtros</a>
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
                                    <th><button class="table-sort" data-sort="sort-data">Data</button></th>
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
                                        echo "<td>" . $row['nome_utente'] . "</td>";
                                        echo "<td>" . $row['username'] . "</td>";
                                        echo "<td>" . $row['telef'] . "</td>";
                                        echo "<td>" . $row['umail'] . "</td>";
                                        echo "<td>" . $row['datacriacao'] . "</td>";
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
                                        '&username=' . urlencode($row['username']) . '">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-pencil i">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                <path d="M13.5 6.5l4 4" />
                                            </svg>
                                            Editar
                                        </a>
                                    </td>';


                                    if($role == 'Administrador'){
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
                                    
                                    }
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10'>Nenhum pedido encontrado</td></tr>";
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Paginação -->
                        <?php
                        // Definir o número de itens por página
                        $items_per_page = 10;

                    // Calcular o número total de páginas
                    $total_items_query = "SELECT COUNT(*) as total FROM utentes";
                    $total_items_result = mysqli_query($conn, $total_items_query);
                    $total_items_row = mysqli_fetch_assoc($total_items_result);
                    $total_items = $total_items_row['total'];
                    $total_pages = ceil($total_items / $items_per_page);

                    // Obter o número da página atual
                    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    if ($current_page < 1) {
                        $current_page = 1;
                    } elseif ($current_page > $total_pages) {
                        $current_page = $total_pages;
                    }

                    // Calcular o deslocamento para a consulta SQL
                    $offset = ($current_page - 1) * $items_per_page;

                    // Consultar os itens para a página atual
                    $sql .= " LIMIT $offset, $items_per_page";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                    ?>
                </div>

            </div>
        </div>
        <div class="bottom-field">
                        <ul class="pagination">
                            <?php
                            if ($current_page > 1) {
                                echo "<li class='prev'><a href='?page=" . ($current_page - 1) . "'>&#139;</a></li>";
                            } else {
                                echo "<li class='disabled'>&#139;</li>";
                            }

                            for ($i = 1; $i <= $total_pages; $i++) {
                                if ($i == $current_page) {
                                    echo "<li class='active'>$i</li>";
                                } else {
                                    echo "<li><a href='?page=$i'>$i</a></li>";
                                }
                            }

                            if ($current_page < $total_pages) {
                                echo "<li class='next'><a href='?page=" . ($current_page + 1) . "'>&#155</a></li>";
                            } else {
                                echo "<li class='disabled'>&#155</li>";
                            }
                            ?>
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

<script>
      document.addEventListener("DOMContentLoaded", function() {
      const list = new List('table-default', {
      	sortClass: 'table-sort',
      	listClass: 'table-tbody',
      	valueNames: [ 'sort-artigo', 'sort-utente', 'sort-tamanho', 'sort-estado', 'sort-id',
      		{ attr: 'data-date', name: 'sort-data' },
      		'sort-quantidade', 'sort-estacao', 'sort-sexo', 'sort-observacao'
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