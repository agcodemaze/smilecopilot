<?php
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use \App\Controller\Pages\EncryptDecrypt;

    $KEY = $_ENV['ENV_SECRET_KEY'] ?? getenv('ENV_SECRET_KEY') ?? '';

    $token = $dadosUsuario["USU_DCTOKEN_ALTERAR_SENHA"] ?? '';
    $exp = $dadosUsuario["USU_DTEXPTOKEN_ALTERAR_SENHA"] ?? '';
    $userId = $dadosUsuario["USU_IDUSUARIO"] ?? '';

    $invalido = 0;
    if (strtotime($exp) < time() || empty($token)) {
       $msg = "Link expirado!";
       $invalido = 1;
    }


    $userId = !empty($userId) ? EncryptDecrypt::encrypt_id_token($userId, $KEY) : null;

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
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
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
                            <a href="/login">
                                <span><img src="/public/assets/images/logo_bright.png" alt="logo" style="height:28px; width:auto;"></span>
                            </a>
                        </div>

                        <div class="card-body p-4">

                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center pb-0 fw-bold"><?= \App\Core\Language::get('redefinir_senha_page'); ?></h4>
                                <p class="text-muted mb-4"  style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#novocliente-modal"><?= \App\Core\Language::get('redefinir_senha_page_desc'); ?></p>
                            </div>

                            <form id="loginForm">

                                <div id="loginError" class="alert alert-danger mt-2" style="display:none"></div>

                                <input type="hidden" class="form-control" id="userid" name="userid" value="<?= $userId ?>">

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

                                <div class="mb-3 mb-0 text-center">
                                    <button class="btn btn-info" type="submit"> <?= \App\Core\Language::get('enviar'); ?> </button> 
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


<div id="loadingOverlay"
     style="
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(2px);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
     ">
    <div class="spinner-border text-info" role="status" style="width: 3rem; height: 3rem;">
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const userid  = document.getElementById('userid').value;
    const senha1  = document.getElementById('senha1').value;
    const senha2  = document.getElementById('senha2').value;

    const errorDiv = document.getElementById('loginError');
    const overlay  = document.getElementById('loadingOverlay');
    const btn = this.querySelector('button[type="submit"]');

    errorDiv.style.display = 'none';
    errorDiv.textContent = '';

    // 🔎 Validação básica antes de enviar
    if (senha1 !== senha2) {
        errorDiv.textContent = "As senhas não conferem.";
        errorDiv.style.display = "block";
        return;
    }

    if (senha1.length < 8) {
        errorDiv.textContent = "A senha deve ter no mínimo 8 caracteres.";
        errorDiv.style.display = "block";
        return;
    }

    // 🔄 Loading
    overlay.style.display = 'flex';
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Enviando...`;

    const payload = { userid, password: senha1 };

    fetch('/redefinirSenhaCheck', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        overlay.style.display = 'none';
        btn.disabled = false;
        btn.innerHTML = "Enviar";

        if (data.success) {
            // ✅ Apenas mostrar mensagem — sem redirecionamento
            errorDiv.classList.remove("text-danger");
            errorDiv.classList.add("text-success");
            errorDiv.textContent = "Senha alterada com sucesso!";
            errorDiv.style.display = "block";

            // opcional: limpar campos
            document.getElementById('senha1').value = "";
            document.getElementById('senha2').value = "";
        } else {
            errorDiv.classList.remove("text-success");
            errorDiv.classList.add("text-danger");
            errorDiv.textContent = data.message;
            errorDiv.style.display = "block";
        }
    })
    .catch(error => {
        overlay.style.display = 'none';
        btn.disabled = false;
        btn.innerHTML = "Enviar";

        console.error('Erro:', error);
        errorDiv.classList.remove("text-success");
        errorDiv.classList.add("text-danger");
        errorDiv.textContent = "Erro inesperado. Tente novamente.";
        errorDiv.style.display = "block";
    });
});
</script>



    <script src="/public/assets/js/vendor.min.js"></script>
    <script src="/public/assets/vendor/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
    <script src="/public/assets/js/app.min.js"></script>
</body>
</html>