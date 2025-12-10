<?php
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    
    if (isset($_COOKIE['token'])) {
        $secretKey = $_ENV['ENV_SECRET_KEY'] ?? getenv('ENV_SECRET_KEY') ?? '';
    
        try {
            JWT::decode($_COOKIE['token'], new Key($secretKey, 'HS256'));
            header('Location: /inicial');
            exit;
            
        } catch (\Exception $e) {
        
        }
    }
?>
<style>
    .steps .step-item {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #6c757d;
    }
    .steps .active {
        background: #0dcaf0;
        color: #fff;
        border: 2px solid #0aa4c5;
    }
</style>
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
                                <h4 class="text-dark-50 text-center pb-0 fw-bold"><?= \App\Core\Language::get('login'); ?></h4>
                                <p class="text-muted mb-4"  style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#novocliente-modal"><?= \App\Core\Language::get('login_desc'); ?></p>
                            </div>

                            <form id="loginForm">

                                <div class="mb-3">
                                    <label for="codigo" class="form-label"><?= \App\Core\Language::get('codigo_clinica'); ?></label>
                                    <input class="form-control" type="number" id="codigo" required="" placeholder="<?= \App\Core\Language::get('codigo_clinica_placeholder'); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="emaillogin" class="form-label"><?= \App\Core\Language::get('email'); ?></label>
                                    <input class="form-control" type="email" id="emaillogin" required="" placeholder="<?= \App\Core\Language::get('email_placeholder'); ?>">
                                    <div id="loginError" class="text-danger mt-1" style="display:none; font-size: 0.9rem;"></div>
                                </div>

                                <div class="mb-3">
                                    <a href="pages-recoverpw.html" class="text-muted float-end"><small><?= \App\Core\Language::get('senha_esqueceu'); ?></small></a>
                                    <label for="password" class="form-label"><?= \App\Core\Language::get('senha'); ?></label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" class="form-control" placeholder="<?= \App\Core\Language::get('senha_placeholder'); ?>">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 mb-0 text-center">
                                    <button class="btn btn-info" type="submit"> <?= \App\Core\Language::get('entrar'); ?> </button> 
                                </div>
                            </form>
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

<div id="novocliente-modal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body"> 
                <div class="card">
                    <div class="card-header py-4 text-center">
                        <img src="/public/assets/images/logo_bright.png" alt="logo" style="height:28px;">
                    </div>

                    <div class="card-body">

                        <p class="mb-4">
                            <strong>Teste grátis por 7 dias.</strong> 
                            Sem necessidade de cartão de crédito e sem compromisso, pague apenas se gostar.
                        </p>

                        <!-- INÍCIO DO NOVO WIZARD -->
                        <form id="wizardForm">

                            <!-- Indicadores -->
                            <div class="steps mb-4 d-flex justify-content-between">
                                <div class="step-item active" data-step="1">1</div>
                                <div class="step-item" data-step="2">2</div>
                                <div class="step-item" data-step="3">3</div>
                            </div>

                            <!-- STEP 1 -->
                            <div class="step" data-step="1">
                                <h5 class="mb-3">Dados da Conta</h5>

                                <div class="mb-3">
                                    <label class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="email" required>
                                    <div class="invalid-feedback">Por favor, insira um e-mail válido.</div>
                                    <div id="email-feedback" class="invalid-feedback"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Senha</label>
                                    <input type="password" class="form-control" id="senha1" required>
                                    <div class="invalid-feedback">A senha é obrigatória.</div>
                                </div>

                                <div class="small text-muted mb-3 ms-1">
                                    <strong>A senha deve conter:</strong>
                                    <ul class="mb-2 mt-2 ps-3">
                                        <li class="rule-tamanho">8 a 10 caracteres</li>
                                        <li class="rule-maiuscula">Pelo menos 1 letra maiúscula</li>
                                        <li class="rule-numero">Pelo menos 1 número</li>
                                        <li class="rule-especial">Pelo menos 1 caractere especial (!@#...)</li>
                                    </ul>
                                </div>

                                <div class="mb-3 mt-3">
                                    <label class="form-label">Repita a Senha</label>
                                    <input type="password" class="form-control" id="senha2" required>
                                    <div class="invalid-feedback">As senhas não conferem.</div>
                                </div>

                                <button type="button" class="btn btn-info w-100 next">Avançar</button>
                            </div>

                            <!-- STEP 2 -->
                            <div class="step d-none" data-step="2">
                                <h5 class="mb-3">Dados Pessoais</h5>

                                <div class="mb-3">
                                    <label class="form-label">Nome Completo</label>
                                    <input type="text" class="form-control" id="nome" required>
                                    <div class="invalid-feedback">O nome é obrigatório.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">DDD + Telefone</label>
                                    <input type="text" class="form-control" id="telefone" required>
                                    <div class="invalid-feedback">O telefone é obrigatório.</div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-light prev">Voltar</button>
                                    <button type="button" class="btn btn-info next">Continuar</button>
                                </div>
                            </div>

                            <!-- STEP 3 -->
                            <div class="step d-none" data-step="3">
                                <div class="text-center py-4">
                                    <h2 class="mt-0">
                                        <i class="mdi mdi-check-all text-info"></i>
                                    </h2>

                                    <h3 class="mt-0">Obrigado!</h3>

                                    <p class="w-75 mx-auto">
                                        Para ativar sua conta, <strong>clique no link</strong> de verificação que enviamos para o seu e-mail.
                                    </p>
                                </div>

                                <button type="button" class="btn btn-info w-100" data-bs-dismiss="modal">
                                    Fechar
                                </button>
                            </div>

                        </form>
                        <!-- FIM DO WIZARD -->

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault();
    
        const email = document.getElementById('emaillogin').value;
        const password = document.getElementById('password').value;
        const codigo = document.getElementById('codigo').value;
    
        const errorDiv = document.getElementById('loginError');
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';
    
        const payload = { email, codigo, password };
    
        fetch('/logincheck', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/inicial';
            } else {
                // Exibe o erro abaixo do email
                errorDiv.innerHTML = data.message;
                errorDiv.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            errorDiv.textContent = 'Erro inesperado. Tente novamente.';
            errorDiv.style.display = 'block';
        });
    });
</script>

    <script src="/public/assets/js/cadNovoAssinante.js"></script>
    <script src="/public/assets/js/vendor.min.js"></script>
    <script src="/public/assets/vendor/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
    <script src="/public/assets/js/app.min.js"></script>
</body>
</html>