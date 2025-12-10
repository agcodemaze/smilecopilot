<?php

if(!empty($dadosUsuario)) {
    $tenancy = $dadosUsuario["TENANCY_ID"];
    $textoTitulo = "Sua conta foi ativada com sucesso!";
    $textoDescricao = "O código da sua clínica é <strong style='color:red;'>$tenancy</strong>. Clique no botão abaixo para acessar o sistema.";
    $botao = "<a href='/login' class='btn btn-info'>Ir para Login</a>";
} else {
    $textoTitulo = "Ativação Falhou!";
    $textoDescricao = "Não foi possível localizar sua assinatura.";
    $botao = "<a href='/assinanteLinkAtivacao' class='btn btn-info'>Gerar Novo Link de Ativação</a>";
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?= $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?= $description; ?>" name="description" />
    <meta name="keywords" content="<?= $keywords; ?>" />

    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= $site; ?>/login">
    <meta property="og:title" content="<?= $title; ?>">
    <meta property="og:description" content="<?= $description; ?>">
    <meta property="og:image" content="https://app.smilecopilot.com/public/assets/images/img_meta.jpg">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= $site; ?>/login">
    <meta property="twitter:title" content="<?= $title; ?>">
    <meta property="twitter:description" content="<?= $description; ?>">
    <meta property="twitter:image" content="https://app.smilecopilot.com/public/assets/images/img_meta.jpg">

    <link rel="shortcut icon" href="/public/assets/images/favicon.ico">  
    <script src="/public/assets/js/hyper-config.js"></script>
    <link href="/public/assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="/public/assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="/public/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <script src="/public/assets/utils/languageDetector.js"></script>
    <script src="/public/assets/js/serviceworkerpwa.js"></script>
    <link rel="manifest" href="/manifest.json">
</head>

<style>
    .login-glow {
        position: absolute;
        width: 950px;
        height: 950px;
        top: -50px;
        left: 50%;
        transform: translateX(-50%);
        background: radial-gradient(circle, #ffffff80 0%, #00c3ff40 40%, transparent 70%);
        filter: blur(20px);
        z-index: 0;
    }
    .card {
        border-radius: 1.2rem !important;
    }
</style>

<body class="authentication-bg position-relative">
    <div class="position-absolute start-0 end-0 bottom-0 w-100 h-100" 
         style="
            background-image: url('/public/assets/images/loginbg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
         ">
    </div>
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="login-glow"></div>
                    <div class="card">

                        <!-- Logo -->
                        <div class="card-header py-4 text-center">
                            <a href="index.html">
                                <span><img src="/public/assets/images/logo_bright.png" alt="logo" style="height:28px; width:auto;"></span>
                            </a>
                        </div>

                        <div class="card-body p-4">

                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center pb-0 fw-bold"><?= $textoTitulo ?></h4>
                                <p class="text-muted mb-4"><?= $textoDescricao ?></p>
                            </div>

                                <div class="mb-3 mb-0 text-center">
                                    <button class="btn btn-info" type="submit"> <?= $botao ?> </button> 
                                </div>
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->
                    <!-- end row -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

    <footer class="footer footer-alt" style="color: #fff;">
            <a href="https://codemaze.com.br" target="_blank" style="color: #fff;"><script>document.write(new Date().getFullYear())</script> Codemaze Soluções de Mkt e Software </a>
    </footer>

    <script src="/public/assets/js/vendor.min.js"></script>
    <script src="/public/assets/vendor/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
    <script src="/public/assets/js/app.min.js"></script>
</body>
</html>