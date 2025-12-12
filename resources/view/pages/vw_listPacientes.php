<?php
date_default_timezone_set('America/Sao_Paulo');
$dataHoraServidor = date('Y-m-d H:i:s'); // hora atual do servidor

$lang = $_SESSION['lang'] ?? 'pt';

use \App\Controller\Pages\EncryptDecrypt; 
use \App\Model\Entity\Organization;

$objOrganization = new Organization();
$configuracoes = $objOrganization->getConfiguracoes(TENANCY_ID);

$nomeClinica = mb_convert_case(mb_strtolower($configuracoes["CFG_DCNOME_CLINICA"]), MB_CASE_TITLE, "UTF-8");

$key = $_ENV['ENV_SECRET_KEY'] ?? getenv('ENV_SECRET_KEY') ?? '';

?>

<style>
    #alternative-page-datatable td {
        padding-top: 4px;
        padding-bottom: 4px;
        vertical-align: middle; 
        }

    .table td, .table th {
        white-space: normal;
        word-break: break-word;
    }

    .action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        margin: 0 2px;
        border-radius: 6px;
        border: 1px solid #ccc;
        color: #555;
        transition: all 0.2s;
    }

    .action-icon:hover {
        background-color: #d2d5d6ff;
        border: 1px solid #aaa7a7ff;
        color: #000;
    }

    /* Botão delete */
    .action-icon i.mdi-delete {
        color: #f16a6a;
    }

    /* Tabela responsiva com hover */
    #alternative-page-datatable tbody tr:hover {
        background-color: #f9f9f9;
        cursor: pointer;
    }

    /* Truncar texto longo */
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>


<!-- Start Content-->
<div class="container-fluid" style="max-width:100% !important; padding-left:10px; padding-right:10px;">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box"> 
                <div class="page-title-right">
                </div>
                <h4 class="page-title"><?= \App\Core\Language::get('consultas'); ?> <?= \App\Core\Language::get($textTitulo); ?> </h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card widget-inline">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="row g-0">

                            <div class="col-6 col-lg-3 mb-3">
                                <div class="card rounded-0 shadow-none m-0" style="cursor:pointer;" onclick="toggleRow()">
                                    <div class="card-body d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3 py-2 w-100">
                                        <img src="/public/assets/images/paciente.png" 
                                             alt="ícone" 
                                             style="width:55px; height:55px; object-fit:contain; opacity:0.9;">
                                        <div class="text-center text-sm-start">
                                            <h2 class="fw-bold mb-0" style="font-size: 32px; line-height: 1;">
                                                <span>157</span><span style="font-size: 20px;">Pacientes</span>
                                            </h2>
                                            <p class="text-muted font-15 mb-0" style="line-height: 1.1;">
                                                Cadastrados
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-lg-3 mb-3">
                                <div class="card rounded-0 shadow-none m-0 border-start border-light" style="cursor:pointer;" onclick="toggleRow()">
                                    <div class="card-body d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3 py-2 w-100">
                                        <img src="/public/assets/images/convenio.png" 
                                             alt="ícone" 
                                             style="width:55px; height:55px; object-fit:contain; opacity:0.9;">
                                        <div class="text-center text-sm-start">
                                            <h2 class="fw-bold mb-0" style="font-size: 32px; line-height: 1;">
                                                <span>28</span><span style="font-size: 20px;"> Convênios</span>
                                            </h2>
                                            <p class="text-muted font-15 mb-0" style="line-height: 1.1;">
                                                 Atendidos
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TOTAL CONSULTAS -->
                            <div class="col-6 col-lg-3 mb-3">
                                <div class="card rounded-0 shadow-none m-0 border-start border-light">
                                    <div class="card-body d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3 py-2 w-100">

                                        <img src="/public/assets/images/etaria.png" 
                                             alt="ícone" 
                                             style="width:55px; height:55px; object-fit:contain; opacity:0.9;">

                                        <div class="text-center text-sm-start">
                                            <h2 class="fw-bold mb-0" style="font-size: 32px; line-height: 1;">
                                                <span>12 <span style="font-size: 20px;">Anos</span></span>
                                            </h2>
                                            <p class="text-muted font-15 mb-0" style="line-height: 1.1;">
                                                Média Etária Geral
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- CONFIRMADAS -->
                            <div class="col-6 col-lg-3 mb-3">
                                <div class="card rounded-0 shadow-none m-0 border-start border-light">
                                    <div class="card-body d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3 py-2 w-100">

                                        <img src="/public/assets/images/anamneses.png" 
                                             alt="ícone" 
                                             style="width:55px; height:55px; object-fit:contain; opacity:0.9;">

                                        <div class="text-center text-sm-start">
                                            <h2 class="fw-bold mb-0" style="font-size: 32px; line-height: 1;">
                                                <span>7 <span style="font-size: 20px;">Anamneses</span></span>
                                            </h2>
                                            <p class="text-muted font-15 mb-0" style="line-height: 1.1;">
                                                Pendentes
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end row -->
                </div>
            </div> <!-- end card-box-->
        </div> <!-- end col-->
    </div>



    <div class="row">
        <div class="col-12">
            <!-- Seção: Anamnese Médica -->
            <div class="card">
        <div class="card-body">
            <h4 class="header-title"><?= \App\Core\Language::get('lista_pacientes'); ?></h4> 
            <p class="text-muted font-14">  
                <?= \App\Core\Language::get('paciente_desc'); ?>
            </p>

            <div class="tab-content">
                <div class="tab-pane show active" id="input-types-preview">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <table id="alternative-page-datatable" class="table dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th><?= \App\Core\Language::get('nome_completo'); ?></th>
                                                    <th><?= \App\Core\Language::get('telefone'); ?></th>
                                                    <th><?= \App\Core\Language::get('cpfrg'); ?></th>
                                                    <th><?= \App\Core\Language::get('nome_convenio'); ?></th>
                                                    <th><?= \App\Core\Language::get('ultima_consulta'); ?></th> 
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($listaPacientes as $listaPaciente): ?>
                                                    <?php

                                                        
                                                        $anemLink="";
                                                        if(!empty($listaPaciente["ANR_DCCOD_AUTENTICACAO"])) {
                                                            $file = $listaPaciente["TENANCY_ID"]."_".$listaPaciente["ANR_DCCOD_AUTENTICACAO"].".pdf";
                                                            $TENANCY_ID = TENANCY_ID;
                                                            $result = json_decode($s3->getDownloadLink("anamneses/clinica_$TENANCY_ID/$file"), true);
                                                            $link = $result['link'] ?? '';
                                                            $anemLink = "href='{$link}' target='_blank' style='cursor: pointer;'";
                                                        }

                                                        $idPacienteCrypt = EncryptDecrypt::encrypt_id_token($listaPaciente["PAC_IDPACIENTE"], $key);
                                                        $idTenancyCrypt = EncryptDecrypt::encrypt_id_token($listaPaciente["TENANCY_ID"], $key);

                                                        $anamneseDispEnv = empty($listaPaciente["ANR_DCCOD_AUTENTICACAO"]) ? true : false;

                                                        $pdfUrl = "https://app.smilecopilot.com/anamnese?tid=$idTenancyCrypt&id=$idPacienteCrypt&lang=$lang";
                                                        $telefone = $listaPaciente["PAC_DCTELEFONE"];
                                                        $phoneDigits = preg_replace('/\D+/', '', $telefone);
                                                        $phoneIntl = $phoneDigits; 
                                                        $paciente = $listaPaciente["PAC_DCNOME"];
                                                        $mensagem = "*$nomeClinica*\n\n"
                                                                   ."Olá {$paciente}!\n\n"
                                                                   . "Pedimos que, por gentileza, preencha o forumário no *link* abaixo antes da sua consulta:\n\n"
                                                                   . "{$pdfUrl}\n\n"
                                                                   . "Agradecemos sua colaboração e estamos à disposição!";

                                                        $msgEncoded = rawurlencode($mensagem);

                                                        $waLink = "https://wa.me/{$phoneIntl}?text={$msgEncoded}";

                                                    ?>
                                                <tr style="cursor: pointer;" onclick="if(event.target.closest('td.dtr-control')) return false; window.location='/editarpaciente?id=<?= htmlspecialchars($listaPaciente['PAC_IDPACIENTE']) ?>';">
                                                    <td>
                                                        <div class="avatar-xs d-table">
                                                            <span class="avatar-title bg-info-lighten rounded-circle text-info" style="border: 1px solid #4d55c5ff;">
                                                                <i class='uil uil-user font-16'></i>
                                                            </span>
                                                        </div>
                                                    </td> 
                                                    <td class="text-truncate" style="max-width: 250px;"><?= htmlspecialchars(ucwords(strtolower((string)$listaPaciente['PAC_DCNOME'])), ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= htmlspecialchars(ucwords(strtolower((string)$listaPaciente['PAC_DCTELEFONE'])), ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= htmlspecialchars(ucwords(strtolower((string)$listaPaciente['PAC_DCCPF'])), ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td class="text-truncate" style="max-width: 150px;">Sulamérica Odonto</td>
                                                    <td>05/08/2025</td>
                                                    <td>  
                                                        <a href="javascript: void(0);" class="action-icon" onclick="event.stopPropagation();"> <i class="mdi mdi-clock-outline" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="<?= \App\Core\Language::get('agendar_consulta'); ?>"></i></a>
                                                       
                                                        
                                                        <?php if ($anamneseDispEnv) : ?>
                                                        <a href="<?= $waLink  ?>" target="blank" style="cursor: pointer;" class="action-icon" onclick="event.stopPropagation();"> <i class="mdi mdi-whatsapp" style="color: #789ef1ff;" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="<?= \App\Core\Language::get('enviar_anemnese'); ?>"></i></a> 
                                                        <a <?= $anemLink ?> class="action-icon" style="cursor: default; opacity: 0.4;" onclick="event.stopPropagation();"> <i class="ri-file-list-3-line" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="<?= \App\Core\Language::get('ver_anamneses_paciente'); ?>"></i></a> 
                                                        <?php endif; ?>
                                                        <?php if (!$anamneseDispEnv) : ?>
                                                        <a href="javascript: void(0);" style="cursor: default; opacity: 0.4;" class="action-icon" onclick="event.stopPropagation();"> <i class="mdi mdi-whatsapp" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="<?= \App\Core\Language::get('enviar_anemnese'); ?>"></i></a> 
                                                        <a <?= $anemLink ?> class="action-icon" onclick="event.stopPropagation();"> <i class="ri-file-list-3-line" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="<?= \App\Core\Language::get('ver_anamneses_paciente'); ?>"></i></a> 
                                                        <?php endif; ?>

                                                        <a href="javascript:void(0);"  
                                                            class="action-icon"
                                                            data-id="<?= htmlspecialchars((string)$listaPaciente['PAC_IDPACIENTE'], ENT_QUOTES, 'UTF-8') ?>"    
                                                            data-dialogTitle="<?= \App\Core\Language::get('lista_pacientes'); ?>"    
                                                            data-dialogMessage="<?= \App\Core\Language::get('tem_certeza_excluir_paciente'); ?> <?= htmlspecialchars((string)$listaPaciente['PAC_DCNOME'], ENT_QUOTES, 'UTF-8') ?>?"   
                                                            data-dialogUriToProcess="/deleteTaskProc"   
                                                            data-dialogUriToRedirect="/listapaciente"   
                                                            data-dialogConfirmButton="<?= \App\Core\Language::get('confirmar'); ?>"
                                                            data-dialogCancelButton="<?= \App\Core\Language::get('cancelar'); ?>" 
                                                            data-dialogErrorMessage="<?= \App\Core\Language::get('erro_ao_excluir'); ?>"
                                                            data-dialogErrorTitle="<?= \App\Core\Language::get('erro'); ?>"    
                                                            data-dialogCancelTitle="<?= \App\Core\Language::get('Cancelado'); ?>"                                                          
                                                            data-dialogCancelMessage="<?= \App\Core\Language::get('cancelado_nenhuma_alteracao'); ?>"     
                                                            data-dialogSuccessTitle="<?= \App\Core\Language::get('sucesso'); ?>"                                                             
                                                            data-dialogProcessTitle="<?= \App\Core\Language::get('aguarde'); ?>" 
                                                            data-dialogProcessMessage="<?= \App\Core\Language::get('processando_solicitacao'); ?>"                                                             
                                                            onclick="event.stopPropagation(); confirmDeleteAttr(this);"> <!-- Chama o método js confirmDeleteAttr com sweetalert -->
                                                            <i class="mdi mdi-delete" data-bs-toggle="popover" style="color: #f16a6aff;" data-bs-trigger="hover" data-bs-content="<?= \App\Core\Language::get('excluir_paciente'); ?>"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div> <!-- end row -->
                                </div> <!-- end card body-->
                            </div> <!-- end card -->
                        </div>
                        <!-- end col-12 -->
                    </div> <!-- end row -->               
                </div> <!-- end preview-->
            </div> <!-- end tab-content-->
        </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->
</div>
<br>

<?php if ($lang  === "pt" || empty($lang)): ?>
    <script src="<?= ASSETS_PATH ?>utils/datatable-Init-ptbr_pacientes.js"></script>
<?php endif; ?>

<?php if ($lang  === "en"): ?>
    <script src="<?= ASSETS_PATH ?>utils/datatable-Init-en_paciente.js"></script>
<?php endif; ?>

<?php if ($lang  === "es"): ?>
    <script src="<?= ASSETS_PATH ?>utils/datatable-Init-es_paciente.js"></script>
<?php endif; ?>


