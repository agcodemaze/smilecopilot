<?php
date_default_timezone_set('America/Sao_Paulo');
$dataHoraServidor = date('Y-m-d H:i:s'); 

$lang = $_SESSION['lang'] ?? 'pt';

use \App\Controller\Pages\EncryptDecrypt; 
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
    <div class="row">
        <div class="col-12">
            <!-- Seção: Anamnese Médica -->
            <div class="card">
        <div class="card-body">
            <h4 class="header-title"><?= \App\Core\Language::get('lista_logs'); ?></h4> 
            <p class="text-muted font-14">  
                <?= \App\Core\Language::get('lista_logs_desc'); ?>
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
                                                    <th style="text-align: center;"><?= \App\Core\Language::get('id'); ?></th>
                                                    <th style="text-align: center;"><?= \App\Core\Language::get('data_hora'); ?></th>
                                                    <th style="text-align: center;"><?= \App\Core\Language::get('nome_executor'); ?></th>
                                                    <th style="text-align: center;"><?= \App\Core\Language::get('nivel'); ?></th>                                                    
                                                    <th style="text-align: center;"><?= \App\Core\Language::get('mensagem'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($listaLogs as $log): ?>
                                                    <?php

                                                        $log['LOS_DTCREATE_AT'] = (new DateTimeImmutable($log['LOS_DTCREATE_AT']))->format('d/m/Y H:i:s');

                                                        if($log['LOS_DCNIVEL'] == 'CRITICAL') {$classeBadge = 'danger';}
                                                        if($log['LOS_DCNIVEL'] == 'INFO') {$classeBadge = 'info';}
                                                        if($log['LOS_DCNIVEL'] == 'WARNING') {$classeBadge = 'warning';}
                                                        if($log['LOS_DCNIVEL'] == 'ERROR') {$classeBadge = 'danger';}
                                                        if($log['LOS_DCNIVEL'] == 'NOTICE') {$classeBadge = 'primary';}
                                                        if($log['LOS_DCNIVEL'] == 'DEBUG') {$classeBadge = 'secondary';}

                                                    ?>
                                                <tr>                                                    
                                                    <td class="text-truncate" style="text-align: center;"><?= htmlspecialchars($log['LOS_IDLOGSISTEMA']) ?></td>
                                                    <td style="text-align: center;"><?= htmlspecialchars(ucwords(strtolower((string)$log['LOS_DTCREATE_AT'])), ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td class="text-truncate" style="text-align: left;"><?= htmlspecialchars(ucwords(strtolower((string)$log['LOS_DCUSUARIO'])), ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td style="text-align: center;"><span class="badge badge-<?= $classeBadge; ?>-lighten"><?= htmlspecialchars(ucwords(strtolower((string)$log['LOS_DCNIVEL'])), ENT_QUOTES, 'UTF-8') ?></spam></td>
                                                    <td style="min-width: 250px; text-align: left; white-space: normal; word-wrap: break-word;"><?= htmlspecialchars($log['LOS_DCMSG']) ?></td>
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
    <script src="<?= ASSETS_PATH ?>utils/datatable-Init-ptbr_logs.js"></script>
<?php endif; ?>

<?php if ($lang  === "en"): ?>
    <script src="<?= ASSETS_PATH ?>utils/datatable-Init-en_logs.js"></script>
<?php endif; ?>

<?php if ($lang  === "es"): ?>
    <script src="<?= ASSETS_PATH ?>utils/datatable-Init-es_logs.js"></script>
<?php endif; ?>


