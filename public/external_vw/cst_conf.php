<?php
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); // pasta raiz do projeto
$dotenv->safeLoad();

use \App\Model\Entity\Conn;
use \App\Controller\Pages\ListConsulta; 
use \App\Utils\Push; 

$con = new Conn();

  if (!isset($_GET['id'])) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(403); 
    echo json_encode([
        'status' => 'error',
        'code' => 403,
        'message' => 'Requisição inválida.'
    ]);
    exit;
  }

  $consulta = \App\Controller\Pages\ListConsulta::getConsultasByHashUser($_GET['id']);

  if(empty($consulta["CON_STCONFIRMACAO_PRESENCA"]) && isset($_GET['opcao'])) {
    $updateStatusConfirmacao = \App\Controller\Pages\ListConsulta::updateConfirmacaoPresencaByHashUser($_GET['id'], $_GET['opcao']);
    
    if($updateStatusConfirmacao == "Status inválido") {
      header('Content-Type: application/json; charset=utf-8');
      http_response_code(403); 
      echo json_encode([
          'status' => 'error',
          'code' => 403,
          'message' => 'Requisição inválida. Status inválido.'
      ]);
      exit;
    }
  
    $msgErr = "1";

    $hora = $consulta["CON_HORACONSULTA"];
    $horaFormatada = substr($hora, 0, 5); 
    $horaFormatada = str_replace(":", "h", $horaFormatada); 
    $dataAmericana = $consulta["CON_DTCONSULTA"]; 
    $dt = new DateTime($dataAmericana);
    $dataBr = $dt->format('d/m/Y'); 

    $nomePacientePush = $consulta["PAC_DCNOME"];
    $dataConsultaPush = $dataBr;
    $horaConsultaPush = $horaFormatada ;
    $msgPush = "Paciente $nomePacientePush - Consulta dia $dataConsultaPush ás  $horaConsultaPush";
    $idDestinoPush = "1-1";

    if($_GET['opcao'] == "CANCELADA") {
       \App\Utils\Push::sendPushEncomendaByUserId("Consulta CANCELADA","$msgPush","Ver Mais","$idDestinoPush");
    }
    if($_GET['opcao'] == "CONFIRMADA") {
       \App\Utils\Push::sendPushEncomendaByUserId("Consulta CONFIRMADA","$msgPush","Ver Mais","$idDestinoPush");
    }
  }

  $consulta = \App\Controller\Pages\ListConsulta::getConsultasByHashUser($_GET['id']);


  $msg="";
  $msgErr="";

  if (empty($consulta)) {
    $msg = "Atenção: este link de confirmação já expirou.";
    $msgErr = "1";
  }elseif ($consulta["CON_STCONFIRMACAO_PRESENCA"] == "CONFIRMADA") {
    $msg = "A sua presença na consulta foi CONFIRMADA com sucesso!";
    $msgErr = "1";
  }elseif($consulta["CON_STCONFIRMACAO_PRESENCA"] == "CANCELADA") {
    $msg = "A sua presença na consulta foi CANCELADA.";
    $msgErr = "1";
  }


  $clinicaNome="";
  $horaFormatada="";
  $dataBr="";
  $diaSemana="";
  $dentista="";

  if(!empty($consulta) && $consulta["CON_STCONFIRMACAO_PRESENCA"] != "CANCELADA" && $consulta["CON_STCONFIRMACAO_PRESENCA"] != "CONFIRMADA") {
    $clinicaNome = $consulta["CFG_DCNOME_CLINICA"];
    $dentista = $consulta["DEN_DCNOME"];
    $hora = $consulta["CON_HORACONSULTA"];
    $horaFormatada = substr($hora, 0, 5); 
    $horaFormatada = str_replace(":", "h", $horaFormatada); 

    $dataAmericana = $consulta["CON_DTCONSULTA"]; 
    $dt = new DateTime($dataAmericana);
    $dataBr = $dt->format('d/m/Y'); 

    $diasSemana = [
        'domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'
    ];
    $numeroDia = (int)$dt->format('w'); // 0 (domingo) a 6 (sábado)
    $diaSemana = $diasSemana[$numeroDia];
  } 
?>


<!DOCTYPE html>
<html lang="en" data-layout="topnav">
<head>
    <meta charset="utf-8" />
    <title>SmileCopilot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SmileCopilot - O sistema completo para gestão de clínicas odontológicas. Controle consultas, pacientes, agenda, prescrição digital e muito mais.">
    <meta name="author" content="Codemaze Soluções de Mkt e Software">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://smilecopilot.com">
    <meta property="og:title" content="SmileCopilot - Gestão inteligente para clínicas odontológicas">
    <meta property="og:description" content="Otimize a gestão da sua clínica odontológica com SmileCopilot: agenda, pacientes, prescrições digitais e relatórios completos em um só lugar.">
    <meta property="og:image" content="https://app.smilecopilot.com/public/assets/images/img_meta.jpg">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="SmileCopilot - Gestão inteligente para clínicas odontológicas">
    <meta name="twitter:description" content="Otimize a gestão da sua clínica odontológica com SmileCopilot: agenda, pacientes, prescrições digitais e relatórios completos em um só lugar.">
    <meta name="twitter:image" content="https://app.smilecopilot.com/public/assets/images/img_meta.jpg">

    <link rel="shortcut icon" href="/public/assets/images/favicon.ico">  

    <link href="/public/assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <script src="/public/assets/vendor/ad_sweetalert/jquery-3.7.0.min.js"></script>
    <script src="/public/assets/js/hyper-config.js"></script>
    <link href="/public/assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="/public/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="/public/assets/vendor/ad_sweetalert/sweetalert2.min.css" rel="stylesheet">
    <script src="/public/assets/vendor/ad_sweetalert/sweetalert2.all.min.js"></script>
    <script src="/public/assets/utils/languageDetector.js"></script>
</head>

<style>
    .navbar-slim {
        background-color: #f5f5f5ff;
        border-top: 1px solid #ddd;
        height: 56px; /* altura reduzida */
        display: flex;
        align-items: center; /* centraliza verticalmente */
    }
    .navbar-slim .container-fluid {
        display: flex;
        align-items: center; /* garante centralização */
        gap: 0.5rem;
    }
    .navbar-slim .btn {
        font-size: 12px;
        padding: 2px 8px;
    }
</style>

<style>
    /* Preloader container */
    #preloader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(233, 244, 247, 0.8); /* Fundo levemente transparente */
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999; /* Fica acima de tudo */
    }

    /* Loader */
    .loader {
      width: 48px;
      height: 48px;
      display: inline-block;
      position: relative;
    }
    .loader::after,
    .loader::before {
      content: '';  
      box-sizing: border-box;
      width: 48px;
      height: 48px;
      border: 2px solid #38c6f1ff;
      position: absolute;
      left: 0;
      top: 0;
      animation: rotation 2s ease-in-out infinite alternate;
    }
    .loader::after {
      border-color: rgba(10, 110, 177, 1);
      animation-direction: alternate-reverse;
    }

    @keyframes rotation {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    } 
</style>

 <style>
    :root{
      --card-bg: linear-gradient(135deg,#f8fbff 0%, #ffffff 100%);
      --accent: #e9f7fb;
    }

    body {
      background: linear-gradient(180deg,#f1f6fb 0%, #eef5fb 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }

    .confirm-card{
      width: 100%;
      max-width: 720px;
      background: var(--card-bg);
      border-radius: 14px;
      box-shadow: 0 8px 30px rgba(28,40,70,0.12);
      padding: 22px;
      border: 1px solid rgba(20,40,70,0.04);
    }

    .avatar-circle{
      width: 64px;
      height: 64px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(180deg,#eef9ff,#dff6fc);
      border: 1px solid rgba(23,162,184,0.12);
      font-weight: 700;
      color: #0b7285;
      font-size: 24px;
    }

    .title {
      font-size: 20px;
      font-weight: 700;
      color: #0f1724;
    }

    .lead-text {
      color: #475569;
      font-size: 15px;
      margin-top: 6px;
    }

    .btn-wide {
      min-width: 120px;
      padding-left: 18px;
      padding-right: 18px;
    }

    .actions {
      gap: 12px;
    }

    .meta {
      font-size: 13px;
      color: #687179;
    }

    /* small subtle shadow on hover for buttons */
    .btn:hover { transform: translateY(-1px); transition: .12s ease; }

    /* feedback badge */
    #feedback {
      display: none;
    }
  </style>

<body>
    <!-- Begin page -->
    <div class="wrapper">



            <!-- ========== Topbar Start ========== -->
            <div class="navbar-custom">
              <div class="topbar d-flex align-items-center justify-content-between">

                <!-- Logo + Ícones -->
                <div class="d-flex align-items-center gap-3">
                  <!-- Topbar Brand Logo -->
                  <div class="logo-topbar">
                    <a href="/inicial" class="logo-light">
                      <span class="logo-lg">
                        <img src="../../../public/assets/images/SmileCopilot-Logo_139x28.png" alt="logo" style="height:28px; width:auto;">
                      </span>
                      <span class="logo-sm">
                        <img src="../../../public/assets/images/SmileCopilot-LogoMin_43x28.png" alt="small logo">
                      </span>
                    </a>
                    <a href="/inicial" class="logo-dark">
                      <span class="logo-lg">
                        <img src="../../../public/assets/images/SmileCopilot-Logo_139x28.png" alt="dark logo" style="height:28px; width:auto;">
                      </span>
                      <span class="logo-sm">
                        <img src="../../../public/assets/images/SmileCopilot-LogoMin_43x28.png" alt="small logo">
                      </span>
                    </a>
                  </div>
                </div>
                      </div>
                </div>
    

                <div class="content-page">
                    <div class="content">                
                      


<?php if (!empty($consulta) && $msgErr != "1") : ?>
  <div class="confirm-card">
    <div class="d-flex align-items-center gap-3 mb-3">
      
      <div>
        <div class="title">Confirmação de Consulta</div>
        <div class="meta">Nome da Clínica: <?= $clinicaNome ?></div>
      </div>
    </div>

    <div class="py-2">
      <p class="lead-text mb-3">
        Você tem uma consulta dia <strong><?= $dataBr ?> (<?= $diaSemana ?>) </strong> às <strong><?= $horaFormatada ?></strong> com o(a) doutor(a) <strong><?= $dentista ?></strong>.
      </p>
      <p class="lead-text mb-4">Você deseja confirmar esta consulta?</p>
      <div class="d-flex actions">
        <button id="btn-nao" class="btn btn-danger btn-wide shadow-sm">
          <svg width="16" height="16" viewBox="0 0 16 16" class="me-2" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle">
            <path d="M8 1.333c3.681 0 6.667 2.986 6.667 6.667S11.681 14.667 8 14.667 1.333 11.681 1.333 8 4.319 1.333 8 1.333zm0 2.334a.667.667 0 00-.667.667v4a.667.667 0 101.334 0v-4A.667.667 0 008 3.667zm0 7.333a.833.833 0 110-1.666.833.833 0 010 1.666z" fill="white"/>
          </svg>
          NÃO
        </button>

          <button id="btn-sim" class="btn btn-info btn-wide shadow-sm">
          <!-- ícone simples -->
          <svg width="16" height="16" viewBox="0 0 16 16" class="me-2" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle">
            <path d="M6 10.5L3.5 8L2.75 8.75L6 12L14 4L13.25 3.25L6 10.5Z" fill="white"/>
          </svg>
          SIM
        </button>
      </div>      
    </div>
  </div>
<?php endif; ?>

<?php if (empty($consulta) || $msgErr == "1") : ?>
  <div class="confirm-card">
    <div class="d-flex align-items-center gap-3 mb-3">
      
      <div>
        <div class="title">Confirmação de consulta</div>
      </div>
    </div>

    <div class="py-2">
      <p class="lead-text mb-3">
        <?= $msg ?>
      </p>
    </div>
  </div>
<?php endif; ?>



                    </div>
                </div>
            </div>

<script>
  document.getElementById('btn-sim').addEventListener('click', function() {
    const url = new URL(window.location.href);
    url.searchParams.set('opcao', 'CONFIRMADA');
    window.location.href = url.toString();
  });
</script>
<script>
  document.getElementById('btn-nao').addEventListener('click', function() {
    const url = new URL(window.location.href);
    url.searchParams.set('opcao', 'CANCELADA');
    window.location.href = url.toString();
  });
</script>
</body>
</html>