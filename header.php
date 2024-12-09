<?php

$nome = htmlspecialchars($_SESSION["username"]);
$id = htmlspecialchars($_SESSION["id"]);
$cargo = htmlspecialchars($_SESSION["cargo"]);

$query = "SELECT * FROM utilizadores WHERE id = ?";

// Verificar página e executar a consulta apenas se necessário
if ($page != 'settings') {
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
        } else {
            echo "Erro ao buscar dados do utilizador.";
            exit;
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Erro ao preparar a consulta.";
        exit;
    }
}

$profie = !empty($row['avatar']) ? $row['avatar'] : 'blankprofile.png';

if($page == 'ajuda') {
    $title = "Centro de ajuda";
} elseif($page == 'creditos') {
    $title = "Créditos";
} else {
    $title = "Encomendas - Ponto Vermelho";
}

echo '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .navbar-brand img {
      width: 32.5px;
      height: auto;
    }
    .navbar-nav .nav-item .avatar {
      width: 30px;
      height: 30px;
    }
    .navbar-nav .nav-item .nav-link p {
      margin: 0;
      font-size: 13px;
    }
    @media (max-width: 768px) {
      .navbar-brand span {
        font-size: 1.25rem;
        margin-left: 10px;
      }
    }
  </style>
</head>
<body>
<header class="navbar navbar-expand-md d-print-none">
  <div class="container-xl">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand d-flex align-items-center" href="index.php" style="margin-right: 10px;">
      <img src="cvp3.png" alt="Tabler" class="navbar-brand-image">
      <span class="d-none d-md-inline">' . $title . '</span>
    </a>
    <div class="collapse navbar-collapse" id="navbar-menu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
            <span class="avatar" style="background-image: url(' . $profie . ')"></span>
            <div class="d-none d-xl-block ps-2">
              <div>' . $nome . '</div>
              <div class="mt-1 small text-muted">' . $cargo . '</div>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
            <a href="settings.php" class="dropdown-item">Definições</a>
            <a href="logout.php" class="dropdown-item">Terminar sessão</a>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center" href="help.php">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#52637e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-help">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
              <path d="M12 17l0 .01"/>
              <path d="M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4"/>
            </svg>
            <p class="ms-1" style="color: #52637e;">Ajuda</p>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center" href="creditos.php">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#52637e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
              <path d="M12 9h.01"/>
              <path d="M11 12h1v4h1"/>
            </svg>
            <p class="ms-1" style="color: #52637e;">Créditos</p>
          </a>
        </li>
      </ul>
    </div>
  </div>
</header>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>
</html>';
?>
