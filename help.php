<?php

//inicia sessão
session_start();

require_once 'check_status.php';

// verifica se o utilizador está logado, se não retorna para a página login.php
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
  header("location: login.php");
  exit;
}

$page = 'ajuda';

?>


<!doctype html>

<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Ajuda - <?php echo $nome_do_site; ?></title>
    <!-- CSS files -->
    <link href="./dist/css/tabler.min.css?1684106062" rel="stylesheet"/>
    <link href="./dist/css/tabler-flags.min.css?1684106062" rel="stylesheet"/>
    <link href="./dist/css/tabler-payments.min.css?1684106062" rel="stylesheet"/>
    <link href="./dist/css/tabler-vendors.min.css?1684106062" rel="stylesheet"/>
    <link href="./dist/css/demo.min.css?1684106062" rel="stylesheet"/>
    <style>
    @import url('https://rsms.me/inter/inter.css');
    :root {
      --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }
    body {
      font-feature-settings: "cv03", "cv04", "cv11";
    }

    .centered {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 400%;
      font-weight: bold;
    }

    .container {
      position: relative;
      text-align: center;
      color: white;
      width: 100%;
    }

    .top {
      margin-top: 3%;
    }
  </style>
  </head>
  <body >
    <script src="./dist/js/demo-theme.min.js?1684106062"></script>
    <div class="page">

      <!-- Header -->
      <?php

        include 'header.php';
      ?>

      <!-- Navbar -->

      <?php

        include 'navbar.php';
      ?>

      <div class="container">
        <div class="centered">Como podemos ajudar?</div>
        <img src="images/help.jpg" style="width: 100%; height: 300px; object-fit:cover" class="img-fluid" alt="Responsive image">
      </div>

      <div class="page-wrapper top">
        <!-- Page header -->
        <div class="page-header d-print-none">
          <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col">
                <h2 class="page-title">
                  Perguntas Mais Frequentes
                </h2>
              </div>
          </div>
        </div>
        <!-- Page body -->
        <div class="page-body">
          <div class="container-xl">
            <div class="card card-lg">
              <div class="card-body">
                <div class="space-y-4">
                  <div>
                    <h2 class="mb-3">1. Pedidos</h2>
                    <div id="faq-1" class="accordion" role="tablist" aria-multiselectable="true">
                      <div class="accordion-item">
                        <div class="accordion-header" role="tab">
                          <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#faq-1-1">Como faço um pedido?</button>
                        </div>
                        <div id="faq-1-1" class="accordion-collapse collapse show" role="tabpanel" data-bs-parent="#faq-1">
                          <div class="accordion-body pt-0">
                            <div>
                              <p>Para fazer um pedido, na secção "Pedidos" clique em <a href="form-pedidos.php" style="text-decoration: none">"Novo pedido"</a>, onde será exibido um formulário para enviar um novo pedido.<br>
                            <strong>Se pretender realizar um pedido é obrigatório ter pelo menos um utente.</strong><br>
                            <strong>Os utentes podem conter um agregado familiar.</strong></p>
                              
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="accordion-item">
                        <div class="accordion-header" role="tab">
                          <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq-1-2">Como posso ver todos os meus pedidos?</button>
                        </div>
                        <div id="faq-1-2" class="accordion-collapse collapse" role="tabpanel" data-bs-parent="#faq-1">
                          <div class="accordion-body pt-0">
                            <div> 
                            <p>Para verificar todos os seus pedidos, na secção "Pedidos" clique em <a href="lista-pedidos.php" style="text-decoration: none">"Listar pedidos"</a>, onde será exibida uma lista com todos os seus pedidos.</p>
                              
                            </div>
                          </div>
                        </div>
                      </div>
                  
                      <div class="accordion-item">
                        <div class="accordion-header" role="tab">
                          <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq-1-3">É possível editar um pedido?</button>
                        </div>
                        <div id="faq-1-3" class="accordion-collapse collapse" role="tabpanel" data-bs-parent="#faq-1">
                          <div class="accordion-body pt-0">
                            <div> 
                            <p>Sim, é possível editar um pedido, na lista dos seus pedidos encontra-se um botão onde permite editar cada pedido individualmente. <br>
                              <strong>Os pedidos entregues não podem ser editados.</strong>
                            </p>
                              
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="accordion-item">
                        <div class="accordion-header" role="tab">
                          <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq-1-4">Quem pode fazer pedidos de atribuição gratuita de produtos do Ponto Vermelho?</button>
                        </div>
                        <div id="faq-1-4" class="accordion-collapse collapse" role="tabpanel" data-bs-parent="#faq-1">
                          <div class="accordion-body pt-0">
                            <div> 
                            <p>Técnicos/as de Ação Social que trabalham diretamente com a população mais vulnerável do concelho de Braga podem validar o acesso gratuito aos produtos da loja Ponto Vermelho. Isto inclui:</p>
                            <ul>
                              <li>SAAS</li>
                              <li>RLIS</li>
                              <li>Centros de acolhimento/emergência social</li>
                              <li>Organizações não-governamentais de apoio a pessoas em situação de fragilidade social</li>
                              <li>Instituto da Solidariedade e Segurança Social</li>
                              <li>Outras entidades da rede social de Braga</li>
                            </ul>  
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="accordion-item">
                        <div class="accordion-header" role="tab">
                          <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq-1-5">Oque pode ser pedido?</button>
                        </div>
                        <div id="faq-1-5" class="accordion-collapse collapse" role="tabpanel" data-bs-parent="#faq-1">
                          <div class="accordion-body pt-0">
                            <div> 
                            <p>O pedido deve ser detalhado com quantidades de cada artigo, sexo e tamanho, de acordo com a tabela abaixo:</p>
                            <ol>
                              <li><strong>Critérios de quantidade por beneficiário:</strong></li><br>
                            
                              <ul>
                                <li>Calças/Calções/Saias: 3 de cada</li>
                                <li>Calças desportivas/Leggins: 2 de cada</li>
                                <li>Camisolas/Camisas/T-shirt’s: 4 de cada</li>
                                <li>Vestidos: 2</li>
                                <li>Casacos (inverno, desportivos, malha): 2 de cada</li>
                                <li>Calçado (botas, sapatos, sapatilhas, sandálias): 2 pares no total</li>
                                <li>Cuecas/Boxers: 4</li>
                                <li>Meias: 4</li>
                                <li>Camisolas interiores: 3</li>
                                <li>Soutiens: 3</li>
                                <li>Pijamas: 2</li>
                                <li>Chinelos/Pantufas: 1</li>
                                <li>Babygrow/Body: 6 de cada </li>
                                <li>Toalhas (rosto e corpo): 2 de cada</li>
                              </ul><br>

                              <li><strong>Critérios de quantidade por cama:</strong></li><br>

                              <ul>
                                <li>Jogo de lençóis: 1</li>
                                <li>Cobertores: 1</li>
                                <li>Edredons: 1</li>
                                <li>Almofadas: 2</li>
                                <li>Cadeira de refeição: 1</li>
                                <li>Manta/Cobertor: 2</li>
                                <li>Babete/Fralda de tecido: 4</li>
                              </ul><br>

                              <li><strong>Critérios de quantidade para cada bebé:</strong></li><br>

                              <ul>
                                <li>Carrinho: 1</li>
                                <li>Babycock: 1</li>
                                <li>Alcofa: 1</li>
                                <li>Banheira: 1</li>
                                <li>Cadeira de refeição: 1</li>
                                <li>Manta/Cobertor: 2</li>
                                <li>Babete/Fralda de tecido: 4</li><br>
                                <li>Outros itens incluem acessórios de inverno (cachecol, gorro, luvas), acessórios (cinto, bolsa, bijuteria), artigos de casa, cozinha e bem-estar, e brinquedos/livros, com quantidades variáveis.</li>
                              </ul><br>

                              Nota: Em determinados períodos, as quantidades máximas permitidas podem sofrer alterações devido a rutura de stock.


                            </ol>  
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>     

                  <div class="top">
                    <h2 class="mb-3">2. Atendimento</h2>
                    <div id="faq-2" class="accordion" role="tablist" aria-multiselectable="true">

                    <div class="accordion-item">
                        <div class="accordion-header" role="tab">
                          <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq-2-1">Quem pode beneficiar dos produtos do Ponto Vermelho?</button>
                        </div>
                        <div id="faq-2-1" class="accordion-collapse collapse" role="tabpanel" data-bs-parent="#faq-2">
                          <div class="accordion-body pt-0">
                            <div> 
                            <p>Indivíduos e famílias em situação de pobreza, tais como:</p>
                            <li>Beneficiários do Rendimento Social de Inserção</li>
                            <li>Pessoas em situação de sem-abrigo</li>
                            <li>Famílias numerosas com baixos rendimentos</li>
                            <li>Desempregados</li>
                          </div>
                          </div>
                        </div>
                        </div> 

                        <div class="accordion-item">
                        <div class="accordion-header" role="tab">
                          <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq-2-2">Com que frequência um beneficiário pode ser encaminhado?</button>
                        </div>
                        <div id="faq-2-2" class="accordion-collapse collapse" role="tabpanel" data-bs-parent="#faq-2">
                          <div class="accordion-body pt-0">
                            <div> 
                            <p>Cada indivíduo ou agregado familiar pode ser encaminhado a cada 6 meses.</p>
                              
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="accordion-item">
                        <div class="accordion-header" role="tab">
                          <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq-2-3">Como é feito o atendimento ao beneficiário?</button>
                        </div>
                        <div id="faq-2-3" class="accordion-collapse collapse" role="tabpanel" data-bs-parent="#faq-2">
                          <div class="accordion-body pt-0">
                            <div> 
                            
                            <ul>
                              <li>O atendimento é feito nos horários disponibilizados pelo Ponto Vermelho, e é previamente agendado diretamente com o beneficiário.</li><br>
                              <li>Estando reunidos os artigos solicitados, o beneficiário receberá uma SMS do Ponto Vermelho: o beneficiário deverá responder à SMS, agendando a sua vida à loja (deverá informar dia e hora pretendidos); o agendamento será confirmado, novamente por SMS, pelo Ponto Vermelho.</li><br>                           
                              <li>O beneficiário deverá deslocar-se ao Ponto Vermelho no dia e hora agendados. Na impossibilidade de o fazer, deverá notificar o Ponto Vermelho e fazer o reagendamento, no prazo máximo de cinco dias úteis.</li><br>
                              <li>O beneficiário será atendido tal como um cliente regular, podendo escolher as peças que prefere, de acordo com os artigos e as quantidades solicitadas.</li><br>
                              <li>O beneficiário poderá optar por não levar algum(ns) artigo(s) que não sejam do seu agrado (esteticamente). Perante esta situação, ser-lhe-á entregue um documento, atestando que passa a ser da sua inteira responsabilidade proceder ao levantamento do(s) artigo(s), dentro do prazo de validade da requisição (6 meses).</li><br>
                              <li>Os artigos solicitados podem ser trocados a pedido do beneficiário, caso o tamanho não esteja adequado ou o beneficiário não goste do que foi apresentado.</li><br>
                            </ul>
                              
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="accordion-item">
                        <div class="accordion-header" role="tab">
                          <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq-2-4">Quais são as situações excecionais no atendimento?</button>
                        </div>
                        <div id="faq-2-4" class="accordion-collapse collapse" role="tabpanel" data-bs-parent="#faq-2">
                          <div class="accordion-body pt-0">
                            <div>
                            <ol>
                              <li>Se o beneficiário não tiver contacto telefónico ou não proceder ao agendamento e levantamento dos artigos no prazo estipulado de dez dias úteis após envio da SMS, a equipa de encaminhamento será acionada para agilizar a marcação e realizar o levantamento dos bens no prazo de dez dias úteis.</li><br>
                              <li>Feito o agendamento, se o beneficiário não comparecer e não proceder ao reagendamento no prazo de cinco dias úteis, a requisição será anulada e o beneficiário só poderá recorrer novamente ao serviço após 6 meses.</li><br>
                              <li>Perante uma demora anómala na reunião dos artigos solicitados (por falta de stock), o beneficiário poderá ser contactado antes dos seus artigos estarem totalmente reunidos. Pretende-se com isto que o beneficiário possa proceder ao levantamento parcial dos artigos solicitados, sendo, posteriormente, contactado para proceder ao levantamento dos artigos em falta.</li><br>
                            </ol> 
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Footer -->

        <?php

          include 'footer.php';

        ?>

      </div>
    </div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="./dist/js/tabler.min.js?1684106062" defer></script>
    <script src="./dist/js/demo.min.js?1684106062" defer></script>
  </body>
</html>