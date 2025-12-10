<?php
    use \App\Model\Entity\Profissionais;

    $profissionalId = "";

if (isset($_GET['profissional_id'])) {
    $profissionalId = $_GET['profissional_id'];
    $_SESSION['PROFISSIONAL_ID'] = $profissionalId;
} elseif (isset($_SESSION['PROFISSIONAL_ID'])) {
    $profissionalId = $_SESSION['PROFISSIONAL_ID'];
}
    $objProfissionais = new Profissionais();
    $profissionais = $objProfissionais->getProfissionais(TENANCY_ID);
?>

<div id="preloader">
  <span class="loader"></span>
</div>

<style>
    /* --------------------------------------------------------------------------
    | PRELOADER (MANTIDO INALTERADO)
    -------------------------------------------------------------------------- */
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

    /* --------------------------------------------------------------------------
    | TOPBAR (BARRA ÚNICA) E ALINHAMENTO
    -------------------------------------------------------------------------- */
    /* Estilo para a barra unificada, garantindo que o menu seja centralizado */
    .topbar-unified {
        width: 100%;
        height: 70px; /* Altura padrão para o topbar */
        display: flex;
        align-items: center;
        padding: 0 1rem; /* Espaçamento lateral */
    }

    /* Conteúdo da esquerda (Logo e Hamburguer Mobile) */
    .topbar-left {
        flex-shrink: 0; /* Não encolhe */
    }

    /* Centralização do Menu (Desktop) */
    .menu-central {
        /* Permite que o menu ocupe o espaço restante */
        flex-grow: 1; 
        /* Centraliza o conteúdo (ul.navbar-nav) horizontalmente */
        justify-content: center;
        margin: 0 15px; 
        display: flex; /* Garante que o menu possa ser centralizado internamente */
    }

    /* Conteúdo da direita (Form e Usuário) */
    .topbar-right {
        flex-shrink: 0; /* Não encolhe */
        /* Alinha o form e o usuário à direita */
        display: flex; 
        align-items: center;
    }

    /* CORREÇÃO 1: Ícone do Sanduíche (Hamburguer) - Torna-o visível no fundo azul */
    .navbar-toggler-icon {
        /* Inverte as cores e aumenta o brilho para ficar claro/branco */
        filter: invert(1) grayscale(100%) brightness(200%); 
    }

    /* CORREÇÃO 2: Garante que os itens do menu fiquem na horizontal (display flex no li) */
    .menu-central .navbar-nav {
        /* CORREÇÃO: Usa Flex para alinhar os itens horizontalmente (default do navbar-nav do Bootstrap) */
        flex-direction: row; 
    }


    /* --------------------------------------------------------------------------
    | ESTILOS DO MENU DE NAVEGAÇÃO
    -------------------------------------------------------------------------- */
    
    /* Itens principais do menu */
    .navbar-custom .nav-item > a {
        color: #fff; /* Mantido em branco para contraste com o fundo azul */
        text-decoration: none;
        font-weight: 500;
        padding: 8px 14px;
        /* CORREÇÃO: O nav-item já é um flex item, mantendo o display aqui */
        display: inline-flex; 
        align-items: center;
        gap: 6px;
        position: relative;
        transition: color 0.2s;
        cursor: pointer;
    }

    /* Ícones principais */
    .navbar-custom .nav-item > a i {
        color: var(--bs-info); 
        font-size: 1.1rem;
        transition: transform 0.2s;
    }

    /* Hover do item principal */
    .navbar-custom .nav-item > a:hover {
        color: var(--bs-info); 
    }

    .navbar-custom .nav-item:hover > a i {
        transform: scale(1.1);
    }

    /* ======== SUBMENU (DESKTOP) ======== */
    .navbar-custom .dropdown-menu {
      display: none;
      position: absolute;
      top: 100%;
      /* CORREÇÃO: Alinhamento correto do submenu no item do menu */
      left: 50%; 
      transform: translateX(-50%);
      background: #fff;
      border: 1px solid #dee2e6;
      border-radius: 6px;
      min-width: 200px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      z-index: 1000;
      padding: 6px 0;
    }
    
    /* CORREÇÃO: Para o submenu abrir corretamente, o item pai deve ser position: relative; */
    .menu-central .navbar-nav .nav-item.dropdown {
        position: relative;
    }

    /* Mostra submenu no hover (desktop) */
    @media (min-width: 992px) {
      .navbar-custom .nav-item.dropdown:hover > .dropdown-menu {
        display: block;
      }
    }

    /* Itens do submenu */
    .navbar-custom .dropdown-menu a {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      color: #555;
      text-decoration: none;
      transition: background-color 0.2s, color 0.2s;
    }

    /* Ícones do submenu */
    .navbar-custom .dropdown-menu a i {
      color: var(--bs-info);
      font-size: 1rem;
    }

    /* Hover: fundo claro e texto azul */
    .navbar-custom .dropdown-menu a:hover {
      background-color: var(--bs-info);
      color: #fff;
    }

    /* --------------------------------------------------------------------------
    | MOBILE (OFFCANVAS) - (MANTIDO INALTERADO)
    -------------------------------------------------------------------------- */
    .offcanvas-body .menu-mobile h6 {
      font-weight: 600;
      font-size: 0.9rem;
      color: #333;
      margin-top: 1rem;
      margin-bottom: 0.4rem;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .offcanvas-body .menu-mobile a {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 0 6px 1.5rem;
      color: #555;
      text-decoration: none;
      font-size: 0.9rem;
      transition: color 0.2s;
    }

    .offcanvas-body .menu-mobile a:hover {
      color: var(--bs-info);
    }

    .offcanvas-body .menu-mobile i {
      color: var(--bs-info);
      font-size: 1rem;
    }

    .offcanvas-body ul li ul {
      margin-left: 0.5rem;
    }

    .navbar-toggler-icon {
    /* Força o ícone sanduíche a ser branco (ou muito claro) para aparecer no fundo azul. 
       Isso substitui o SVG padrão do Bootstrap por uma versão branca. */
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}
</style>

<!-- ========== Topbar Start ========== -->
<div class="navbar-custom">
    <div class="topbar-unified d-flex align-items-center justify-content-between px-3">

        <div class="topbar-left d-flex align-items-center gap-3">
            <button class="navbar-toggler border-0 d-lg-none" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="logo-topbar">
                <a href="/inicial" class="logo-light">
                    <span class="logo-lg">
                        <img src="../../../public/assets/images/logo_bright.png" alt="logo" style="height:28px; width:auto;">
                    </span>
                    <span class="logo-sm">
                        <img src="../../../public/assets/images/SmileCopilot-LogoMin_43x28.png" alt="small logo">
                    </span>
                </a>
                <a href="/inicial" class="logo-dark">
                    <span class="logo-lg">
                        <img src="../../../public/assets/images/logo_bright.png" alt="dark logo" style="height:28px; width:auto;">
                    </span>
                    <span class="logo-sm">
                        <img src="../../../public/assets/images/SmileCopilot-LogoMin_43x28.png" alt="small logo">
                    </span>
                </a>
            </div>
        </div>
        
        <div class="menu-central d-none d-lg-flex justify-content-center flex-grow-1">
            <ul class="navbar-nav gap-3 mb-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark d-flex align-items-center gap-1" href="#" role="button">
                        <i class="ri-user-3-line text-info"></i> <?= \App\Core\Language::get('pacientes'); ?>
                    </a>
                    <ul class="dropdown-menu border-0 shadow-sm">
                        <li><a class="dropdown-item text-dark" href="/listapaciente"><i class="ri-list-check me-1 text-info"></i> Lista de Pacientes</a></li>
                        <li><a class="dropdown-item text-dark" href="/cadastropaciente"><i class="ri-user-add-line me-1 text-info"></i> Novo Paciente</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark d-flex align-items-center gap-1" href="#" role="button">
                        <i class="ri-file-list-3-line text-info"></i> <?= \App\Core\Language::get('consultas'); ?>
                    </a>
                    <ul class="dropdown-menu border-0 shadow-sm">
                        <li><a class="dropdown-item text-dark" href="/listaconsulta"><i class="ri-calendar-line me-1 text-info"></i> Lista de Consultas</a></li>
                        <li><a class="dropdown-item text-dark" href="/historico"><i class="ri-time-line me-1 text-info"></i> Histórico</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark d-flex align-items-center gap-1" href="#" role="button">
                        <i class="ri-calendar-2-line text-info"></i> <?= \App\Core\Language::get('agenda'); ?>
                    </a>
                    <ul class="dropdown-menu border-0 shadow-sm">
                        <li><a class="dropdown-item text-dark" href="/agenda"><i class="ri-eye-line me-1 text-info"></i> Ver Agenda</a></li>
                        <li><a class="dropdown-item text-dark" href="/novaagenda"><i class="ri-add-line me-1 text-info"></i> Nova Agenda</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark d-flex align-items-center gap-1" href="#" role="button">
                        <i class="ri-question-answer-line text-info"></i> <?= \App\Core\Language::get('ajuda'); ?>
                    </a>
                    <ul class="dropdown-menu border-0 shadow-sm">
                        <li><a class="dropdown-item text-dark" href="#"><i class="ri-customer-service-2-line me-1 text-info"></i> Suporte</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark d-flex align-items-center gap-1" href="#" role="button">
                        <i class="ri-settings-3-line text-info"></i> Sistema
                    </a>
                    <ul class="dropdown-menu border-0 shadow-sm">
                        <li><a class="dropdown-item text-dark" href="#"><i class="ri-computer-line me-1 text-info"></i> <?= \App\Core\Language::get('configuracoes'); ?></a></li>
                        <li><a class="dropdown-item text-dark" href="/listmodeloanamnese"><i class="ri-file-list-3-line me-1 text-info"></i> <?= \App\Core\Language::get('modelo_anamnese'); ?>'s</a></li>
                        <li><a href="/listLogInfo" class="text-decoration-none text-dark"><i class="ri-history-line me-1 text-info"></i> <?= \App\Core\Language::get('registro_atividades'); ?></a></li>
                        <li><a href="/logoff" class="text-decoration-none text-dark"><i class="ri-logout-box-r-line me-1 text-info"></i> <?= \App\Core\Language::get('sair'); ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="topbar-right d-flex align-items-center gap-2 mb-0 list-unstyled">
            <form method="GET" id="formProfissional">
                <li class="dropdown list-unstyled">
<select 
    name="profissional_id" 
    id="profissional" 
    class="form-select" 
    style="width: 200px;" 
    onchange="document.getElementById('formProfissional').submit()">                
    <option value="all">-- <?= \App\Core\Language::get('escolha_dentista'); ?> --</option>
    <?php foreach ($profissionais as $profissional): 
        
        // Nome completo do profissional
        $nome_completo = $profissional['DEN_DCNOME'];
        $limite = 18;
        
        // Verifica se o nome excede o limite de 30 caracteres
        if (mb_strlen($nome_completo, 'UTF-8') > $limite) {
            // Se exceder, corta o nome e adiciona reticências
            $nome_exibicao = mb_substr($nome_completo, 0, $limite, 'UTF-8') . '...';
        } else {
            // Se não exceder, usa o nome completo
            $nome_exibicao = $nome_completo;
        }

    ?>
        <option 
            value="<?= htmlspecialchars($profissional['DEN_IDDENTISTA'], ENT_QUOTES, 'UTF-8') ?>" 
            <?= ($profissional['DEN_IDDENTISTA'] == $profissionalId) ? 'selected' : '' ?>>
            <?= htmlspecialchars($nome_exibicao, ENT_QUOTES, 'UTF-8') ?>
        </option>
    <?php endforeach; ?>
</select>
                </li>
            </form>

            <ul class="topbar-menu d-flex align-items-center gap-2 mb-0 list-unstyled">
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="account-user-avatar">
                            <img src="../../../public/assets/images/users/avatar_sc.jpg" alt="user-image" width="32" class="rounded-circle">
                        </span>
                        <span class="d-lg-flex flex-column gap-1 d-none">
                            <h5 class="my-0">Codemaze da silva junior</h5>
                            <h6 class="my-0 fw-normal">Suporte</h6>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
    <div class="offcanvas-header">
        <span class="logo-lg">
            <img src="../../../public/assets/images/logo_bright.png" alt="logo" style="height:28px; width:auto;">
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>



    <div class="offcanvas-body menu-mobile">
        <ul class="list-unstyled d-flex flex-column gap-2">
            <li>
                <div class="fw-semibold d-flex align-items-center gap-1 mb-1">
                    <i class="ri-user-3-line text-info"></i> <?= \App\Core\Language::get('pacientes'); ?>
                </div>
                <ul class="list-unstyled ps-4 d-flex flex-column gap-1">
                    <li><a href="/listapaciente" class="text-decoration-none text-dark"><i class="ri-list-check me-1 text-info"></i> Lista de Pacientes</a></li>
                    <li><a href="/cadastropaciente" class="text-decoration-none text-dark"><i class="ri-user-add-line me-1 text-info"></i> Novo Paciente</a></li>
                </ul>
            </li>

            <li>
                <div class="fw-semibold d-flex align-items-center gap-1 mb-1">
                    <i class="ri-file-list-3-line text-info"></i> <?= \App\Core\Language::get('consultas'); ?>
                </div>
                <ul class="list-unstyled ps-4 d-flex flex-column gap-1">
                    <li><a href="/listaconsulta" class="text-decoration-none text-dark"><i class="ri-calendar-line me-1 text-info"></i> Lista de Consultas</a></li>
                    <li><a href="/historico" class="text-decoration-none text-dark"><i class="ri-time-line me-1 text-info"></i> Histórico</a></li>
                </ul>
            </li>

            <li>
                <div class="fw-semibold d-flex align-items-center gap-1 mb-1">
                    <i class="ri-calendar-2-line text-info"></i> <?= \App\Core\Language::get('agenda'); ?>
                </div>
                <ul class="list-unstyled ps-4 d-flex flex-column gap-1">
                    <li><a href="/agenda" class="text-decoration-none text-dark"><i class="ri-eye-line me-1 text-info"></i> Ver Agenda</a></li>
                    <li><a href="/novaagenda" class="text-decoration-none text-dark"><i class="ri-add-line me-1 text-info"></i> Nova Agenda</a></li>
                </ul>
            </li>

            <li>
                <div class="fw-semibold d-flex align-items-center gap-1 mb-1">
                    <i class="ri-question-answer-line text-info"></i> <?= \App\Core\Language::get('ajuda'); ?>
                </div>
                <ul class="list-unstyled ps-4 d-flex flex-column gap-1">
                    <li><a href="#" class="text-decoration-none text-dark"><i class="ri-customer-service-2-line me-1 text-info"></i> Suporte</a></li>
                </ul>
            </li>

            <li>
                <div class="fw-semibold d-flex align-items-center gap-1 mb-1">
                    <i class="ri-settings-3-line text-info"></i> Sistema
                </div>
                <ul class="list-unstyled ps-4 d-flex flex-column gap-1">
                    <li><a href="#" class="text-decoration-none text-dark"><i class="ri-computer-line me-1 text-info"></i> <?= \App\Core\Language::get('configuracoes'); ?></a></li>
                    <li><a href="/listmodeloanamnese" class="text-decoration-none text-dark"><i class="ri-file-list-3-line me-1 text-info"></i> <?= \App\Core\Language::get('modelo_anamnese'); ?>'s</a></li>
                    <li><a href="/listLogInfo" class="text-decoration-none text-dark"><i class="ri-history-line me-1 text-info"></i> <?= \App\Core\Language::get('registro_atividades'); ?></a></li>
                    <li><a href="/logoff" class="text-decoration-none text-dark"><i class="ri-logout-box-r-line me-1 text-info"></i> <?= \App\Core\Language::get('sair'); ?></a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>

