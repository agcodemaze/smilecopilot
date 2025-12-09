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
                                    <label for="email" class="form-label"><?= \App\Core\Language::get('email'); ?></label>
                                    <input class="form-control" type="email" id="email" required="" placeholder="<?= \App\Core\Language::get('email_placeholder'); ?>">
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

    <div id="novocliente-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body"> 
                <div class="card">
                    <div class="card-header py-4 text-center">
                        <a href="index.html">
                            <span><img src="/public/assets/images/logo_bright.png" alt="logo" style="height:28px; width:auto;"></span>
                        </a>
                    </div>
                    <div class="card-body">

                        <p class="mb-4"><strong>Teste grátis por 7 dias.</strong> Sem necessidade de cartão de crédito e sem compromisso, pague apenas se gostar.</p>

                        <form id="wizardForm" class="needs-validation" novalidate>
                            <div id="progressbarwizard">

                                <ul class="nav nav-pills nav-justified form-wizard-header mb-3">
                                    <li class="nav-item">
                                        <a href="#account-2"  data-toggle="tab" class="nav-link rounded-0 py-2 active">
                                            <i class="mdi mdi-account-circle font-18 align-middle me-1"></i>
                                            <span class="d-none d-sm-inline">Conta</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#profile-tab-2"  data-toggle="tab" class="nav-link rounded-0 py-2">
                                            <i class="mdi mdi-face-man-profile font-18 align-middle me-1"></i>
                                            <span class="d-none d-sm-inline">Perfil</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#finish-2"  data-toggle="tab" class="nav-link rounded-0 py-2">
                                            <i class="mdi mdi-checkbox-marked-circle-outline font-18 align-middle me-1"></i>
                                            <span class="d-none d-sm-inline">Validar</span>
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content b-0 mb-0">

                                    <div id="bar" class="progress mb-3" style="height: 7px;">
                                        <div class="bar progress-bar progress-bar-striped progress-bar-animated bg-success"></div>
                                    </div>

                                    <!-- Aba de Conta -->
                                    <div class="tab-pane active" id="account-2">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="row mb-3">
                                                    <label class="col-md-3 col-form-label" for="email">E-mail</label>
                                                    <div class="col-md-9">
                                                        <input type="email" class="form-control" id="email" name="email" required>
                                                        <!-- Mensagem de erro -->
                                                        <div class="invalid-feedback">
                                                            Por favor, insira um e-mail válido.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label class="col-md-3 col-form-label" for="senha1"> Senha</label>
                                                    <div class="col-md-9">
                                                        <input type="password" id="senha1" name="senha1" class="form-control" required>
                                                        <!-- Mensagem de erro -->
                                                        <div class="invalid-feedback">
                                                            A senha é obrigatória.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label class="col-md-3 col-form-label" for="senha2"> Repita a Senha</label>
                                                    <div class="col-md-9">
                                                        <input type="password" id="senha2" name="senha2" class="form-control" required>
                                                        <!-- Mensagem de erro -->
                                                        <div class="invalid-feedback">
                                                            As senhas não conferem.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="list-inline wizard mb-0">
                                            <li class="next list-inline-item float-end">
                                                <a href="javascript:void(0);" class="btn btn-info" id="continueToProfile">Avançar <i class="mdi mdi-arrow-right ms-1"></i></a>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Aba de Perfil -->
                                    <div class="tab-pane" id="profile-tab-2">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="row mb-3">
                                                    <label class="col-md-3 col-form-label" for="nome"> Nome Completo</label>
                                                    <div class="col-md-9">
                                                        <input type="text" id="nome" name="nome" class="form-control" required>
                                                        <!-- Mensagem de erro -->
                                                        <div class="invalid-feedback">
                                                            O nome é obrigatório.
                                                        </div>
                                                    </div>
                                                </div>                                               
                                                
                                                <div class="row mb-3">
                                                    <label class="col-md-3 col-form-label" for="telefone"> DDD + Telefone</label>
                                                    <div class="col-md-9">
                                                        <input type="text" id="telefone" name="telefone" class="form-control" required>
                                                        <!-- Mensagem de erro -->
                                                        <div class="invalid-feedback">
                                                            O telefone é obrigatório.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="pager wizard mb-0 list-inline">
                                            <li class="previous list-inline-item">
                                                <a href="javascript:void(0);" class="btn btn-light" id="backtoaccount"><i class="mdi mdi-arrow-left me-1"></i> Voltar para Conta</a>
                                            </li>
                                            <li class="next list-inline-item float-end">
                                                <button type="submit" 
                                                class="btn btn-info" 
                                                id="submitForm"
                                                >Cadastrar-se</button>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Aba de Finalização -->
                                    <div class="tab-pane" id="finish-2">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="text-center">
                                                    <h2 class="mt-0"><i class="mdi mdi-check-all"></i></h2>
                                                    <h3 class="mt-0">Obrigado!</h3>

                                                    <p class="w-75 mb-2 mx-auto">Para ativar sua conta, <strong>clique no link</strong> de verificação que enviamos para o seu e-mail.</p>

                                                </div>
                                            </div>
                                        </div>
                                        <ul class="pager wizard mb-0 list-inline mt-1 d-flex justify-content-center">
                                            <li class="list-inline-item">
                                                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Fechar</button>
                                            </li>
                                        </ul>
                                    </div>
                                </div> <!-- tab-content -->
                            </div> <!-- end #progressbarwizard-->
                        </form>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div>



<script>
    document.addEventListener('DOMContentLoaded', function() {

        function validateTab(tabElement) {
            let isValid = true;
            const requiredFields = tabElement.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.checkValidity()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            return isValid;
        }

        function validatePasswords() {
            const senha1 = document.getElementById('senha1');
            const senha2 = document.getElementById('senha2');
            if (senha1.value !== senha2.value || senha1.value === '') {
                senha2.classList.add('is-invalid');
                senha2.nextElementSibling.textContent = 'As senhas não conferem.';
                return false;
            } else {
                senha2.classList.remove('is-invalid');
            }
            return true;
        }

        function canProceed(tabIds) {
            return tabIds.every(id => {
                let tab = document.getElementById(id);
                let valid = validateTab(tab);
                if (id === 'account-2') valid = valid && validatePasswords();
                return valid;
            });
        }

        function showTab(nextTabId) {
            const tabLink = document.querySelector(`.nav-link[href="#${nextTabId}"]`);
            const nextTab = new bootstrap.Tab(tabLink);
            nextTab.show();
        }

        document.getElementById('continueToProfile').addEventListener('click', function(e) {
            e.preventDefault();
            if (canProceed(['account-2'])) {
                showTab('profile-tab-2');
            }
        });

        document.getElementById('backtoaccount').addEventListener('click', function(e) {
            e.preventDefault();
            showTab('account-2');
        });

        document.getElementById('wizardForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!canProceed(['account-2','profile-tab-2'])) {
                this.classList.add('was-validated');
                return;
            }

            let formData = new FormData(this);

            fetch('/cadUsuario', {
                method: 'POST',
                body: formData
            });

            showTab('finish-2');
        });

        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', e => e.preventDefault());
        });

    });
</script>


    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const codigo = document.getElementById('codigo').value;

            const payload = {
                email: email,
                codigo: codigo,
                password: password
            };

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
                    alert(data.message);
                }
            })
            .catch(error => console.error('Erro:', error));
        });
    </script>

    <script src="/public/assets/js/vendor.min.js"></script>
    <script src="/public/assets/vendor/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
    <script src="/public/assets/js/app.min.js"></script>
</body>
</html>