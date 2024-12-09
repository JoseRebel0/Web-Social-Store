<?php

$role = htmlspecialchars($_SESSION["cargo"]);

?>

<header class="navbar-expand-md">
        <div class="collapse navbar-collapse" id="navbar-menu">
          <div class="navbar">
            <div class="container-xl">
              <ul class="navbar-nav">
                <li class="nav-item <?php if($page == 'index'){echo 'active';} ?> "> <!--active-->

                  <a class="nav-link" href="./" >
                    <span class="nav-link-icon d-md-none d-lg-inline-block"> <!--Download SVG icon from http://tabler-icons.io/i/home -->
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                    </span>
                    <span class="nav-link-title">
                      Home
                    </span>
                  </a>
                </li>

                <li class="nav-item dropdown <?php if($page == 'form-utentes'){echo 'active';} elseif($page == 'lista-utentes'){echo 'active';} elseif($page == 'edit-utente'){echo 'active';}?>">
                  <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
                    <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/lifebuoy -->
                      <svg  xmlns="http://www.w3.org/2000/svg" class="icon" width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                    </span>
                    <span class="nav-link-title active">
                      Utentes
                    </span>
                  </a>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="lista-utentes.php">
                      Listar utentes
                    </a>
                    <a class="dropdown-item" href="form-utentes.php">
                      Novo utente
                    </a>
    
                  </div>
                </li>

                <li class="nav-item dropdown <?php if($page == 'form-pedidos'){echo 'active';} elseif($page == 'lista-pedidos'){echo 'active';}elseif($page == 'edit-pedido'){echo 'active';}?> "> <!--active-->

                  <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
                    <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/package -->
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg>
                    </span>
                    <span class="nav-link-title">
                      Pedidos
                    </span>
                  </a>
                  <div class="dropdown-menu">
                    
                      <div class="dropdown-menu-column">

                        <a class="dropdown-item" href="lista-pedidos.php">
                          Listar pedidos
                        </a>
                        <a class="dropdown-item" href="form-pedidos.php">
                          Novo pedido
                        </a>
                          
                      </div>    
                  </div>
                </li>


                <?php if ($role == 'Administrador') : ?>
                        <li class="nav-item dropdown <?php echo ($page == 'form-utilizadores' || $page == 'lista-utilizadores' || $page == 'edit-utilizador') ? 'active' : ''; ?>">
                            <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-users"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                                </span>
                                <span class="nav-link-title">
                                    Entidades
                                </span>
                            </a>
                            <div class="dropdown-menu">
                                <div class="dropdown-menu-columns">
                                    <div class="dropdown-menu-column">
                                        <a class="dropdown-item" href="lista-utilizadores.php">
                                            Listar entidades
                                        </a>
                                        <a class="dropdown-item" href="form-utilizadores.php">
                                            Nova entidade
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endif; ?>




                <li class="nav-item dropdown">
                  <div class="dropdown-menu">
                    
                  </div>
                </li> 


                <li class="nav-item dropdown <?php if($page == 'form-agrefam'){echo 'active';} elseif($page == 'lista-agrefam'){echo 'active';} elseif($page == 'edit-agrefam'){echo 'active';}?>">
                  <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
                    <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/lifebuoy -->
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-hearts"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14.017 18l-2.017 2l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 0 1 8.153 5.784" /><path d="M15.99 20l4.197 -4.223a2.81 2.81 0 0 0 0 -3.948a2.747 2.747 0 0 0 -3.91 -.007l-.28 .282l-.279 -.283a2.747 2.747 0 0 0 -3.91 -.007a2.81 2.81 0 0 0 -.007 3.948l4.182 4.238z" /></svg>
                    </span>
                    <span class="nav-link-title">
                      Agregado familiar
                    </span>
                  </a>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="lista-agrefam.php">
                      Listar agregados
                    </a>
                    <a class="dropdown-item" href="form-agrefam.php">
                      Novo agregado
                    </a>
    
                  </div>
                </li>

                
                <?php if ($role == 'Administrador') : ?>
                        <li class="nav-item dropdown <?php echo ($page == 'form-artigos' || $page == 'lista-artigos' || $page == 'edit-artigo') ? 'active' : ''; ?>">
                            <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-archive"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10" /><path d="M10 12l4 0" /></svg>
                                </span>
                                <span class="nav-link-title">
                                    Artigos
                                </span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="lista-artigos.php">
                                    Listar artigos
                                </a>
                                <a class="dropdown-item" href="form-artigos.php">
                                    Novo artigo
                                </a>
                            </div>
                        </li>
                    <?php endif; ?>

                </ul>
                <div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 order-first order-md-last">
            
              </div>
            </div>
          </div>
        </div>
      </header>