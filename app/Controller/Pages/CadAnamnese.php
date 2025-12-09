<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Utils\Auth;
use \App\Model\Entity\Organization;
use \App\Model\Entity\Anamnese;
use \App\Model\Entity\Paciente;
use \App\Controller\Pages\S3Controller;
use \App\Controller\Pages\LogSistema;
use Dompdf\Dompdf;
use App\Core\Language;

class CadAnamnese extends Page{

    public static function getAnamnese () {

        Auth::authCheck(); 
        $objOrganization = new Organization();

        $componentsScriptsHeader = '
            <script src="'.ASSETS_PATH.'js/hyper-config.js"></script>
            <link href="'.ASSETS_PATH.'css/vendor.min.css" rel="stylesheet" type="text/css" />
            <link href="'.ASSETS_PATH.'css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
            <link href="'.ASSETS_PATH.'css/icons.min.css" rel="stylesheet" type="text/css" />
            <script src="'.ASSETS_PATH.'js/serviceworkerpwa.js"></script>   
            <link rel="manifest" href="/manifest.json">  
                
        ';

        $componentsScriptsFooter = '
            <script src="'.ASSETS_PATH.'js/vendor.min.js"></script>            
            <script src="'.ASSETS_PATH.'js/app.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/jquery-mask-plugin/jquery.mask.min.js"></script>
        ';

        //VIEW DA HOME
        $content = ([
            'title' => $objOrganization->title,
            'description' => $objOrganization->description,
            'site' => $objOrganization->site,
            'componentsScriptsHeader' => $componentsScriptsHeader,
            'componentsScriptsFooter' => $componentsScriptsFooter
        ]); 

        //VIEW DA PAGINA
        return self::getPage('pages/vw_anamnese', $content);
    }

    public static function getAnamneseModel ($TENANCY_ID) {

        $anamneseModel = new Anamnese();
        $model = $anamneseModel->getAnamneseModel($TENANCY_ID);
        return $model;
    }

    public static function insertAnamneserespostas ($tid, $id, $modelo_id, $respostas) {

        Language::init();

        $anamneseRespostas = new Anamnese();
        $respostas = $anamneseRespostas->insertAnamneseRespostas($tid, $id, $modelo_id, $respostas);
        $pacientesObj = new Paciente();
        $pacienteInfo = $pacientesObj->getPacientesById($tid, $id); 
        $objOrganization = new Organization();
        $clinicaInfo = $objOrganization->getConfiguracoes($tid);
        

        $returninsert = json_decode($respostas, true);
        if($returninsert['success'] != true){

            LogSistema::insertLog("SmileCopilot", 'ERROR', Language::get('erro_insert_anamnese_erro').' '.$pacienteInfo['PAC_DCNOME'].' e:'.$returninsert['message'], $tid);
            return $respostas;
        }

        LogSistema::insertLog("SmileCopilot", 'INFO', Language::get('insert_anamnese_sucesso').' '.$pacienteInfo['PAC_DCNOME'], $tid);

        $pdfResponse = self::createAnamnesePdf($tid,$id);
        return $pdfResponse;
    }


    public static function createAnamnesePdf ($tid,$id) {

        Language::init();

        $objOrganization = new Organization();
        $clinicaInfo = $objOrganization->getConfiguracoes($tid);

        $pacientesObj = new Paciente();
        $convenios = $pacientesObj->getConvenios(); 

        $pacienteInfo = $pacientesObj->getPacientesById($tid, $id); 

        // --- BUSCA ANAMNESE ---
        $anamneseObj = new Anamnese();
        $anamneses = $anamneseObj->getAnamnesesRespostaModeloByIdPaciente($tid,$id);
        $modelo = json_decode($anamneses["ANM_JSON_MODELO"], true);
        $respostas = json_decode($anamneses["ANR_JSON_RESPOSTAS"], true);

        // Junta todas as perguntas
        $todas_perguntas = [];
        foreach ($modelo['secoes'] as $secao) {
            $todas_perguntas = array_merge($todas_perguntas, $secao['perguntas']);
        }

        // --- MONTA HTML DO PDF ---
        $logoFile = __DIR__ . '/../../../public/assets/images/SmileCopilot-Logo_139x28.png';
        if (file_exists($logoFile)) {
            $logoData = base64_encode(file_get_contents($logoFile));           
        }

        // Rodapé com infos da assinatura eletrônica
        $assinaturaData = date('d/m/Y \à\s H:i'); 
        $codigoFormatado = $pacienteInfo['ANR_DCCOD_AUTENTICACAO'];
        $verificarUrl = 'https://app.smilecopilot.com/verificar?c=' . urlencode($codigoFormatado);

        //cabecalho informações
        $nomeClinica = mb_convert_case(mb_strtolower($clinicaInfo["CFG_DCNOME_CLINICA"]), MB_CASE_TITLE, "UTF-8");
        $enderecoClinica = $clinicaInfo["CFG_DCENDERECO_CLINICA"]." ".$clinicaInfo["CFG_DCNUMERO_CLINICA"] ." - ".$clinicaInfo["CFG_DCBAIRRO_CLINICA"];
        $cidadeEstadoClinica = $clinicaInfo["CFG_DCCIDADE_CLINICA"]."/".$clinicaInfo["CFG_DCESTADO_CLINICA"]." - ".$clinicaInfo["CFG_DCPAIS_CLINICA"];

        $html = '
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: Arial, sans-serif; color: #333; margin: 20px; line-height: 1.5; }

                    /* Cabeçalho */
                    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
                    .clinic-info { text-align: left; }
                    .clinic-name { font-size: 20px; font-weight: bold; color: #000; margin-bottom: 2px; }
                    .clinic-address { font-size: 14px; color: #555; }
                    .logo { max-height: 50px; }

                    /* Título principal */
                    h1 { background-color: #6b6d6dff; color: #fff; padding: 15px; border-radius: 5px; text-align: center; margin-bottom: 20px; font-size: 18px; }

                    /* Info do paciente e data */
                    .info { margin-bottom: 20px; background-color: #f9f9f9; padding: 10px 15px; border-radius: 5px; border: 1px solid #ddd; }
                    .info div { margin-bottom: 5px; font-size: 14px; }
                    .info span.label { font-weight: bold; color: #0d6efd; }

                    /* Seções e perguntas */
                    .section { margin-bottom: 20px; padding: 10px; border-left: 4px solid #0dcaf0; background-color: #fefefe; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
                    .question { font-weight: bold; margin-top: 5px; color: #0d6efd; }
                    .answer { background-color: #e7f7ff; padding: 8px 12px; border-radius: 4px; margin-bottom: 8px; display: block; }

                    /* Layout em colunas */
                    .row { display: flex; flex-wrap: wrap; margin-top: 10px; }
                    .col { flex: 1; min-width: 250px; padding-right: 15px; }

                    /* Rodapé */
                    .footer { text-align: center; font-size: 12px; color: #888; margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px; }
                    .signature { margin-top: 40px; font-size: 14px; font-weight: bold; }
                </style>
            </head>
            <body>
                <!-- Cabeçalho -->
            <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #ccc; padding-bottom: 10px;">
                <!-- Bloco da clínica à esquerda -->
                <div class="clinic-info" style="text-align: left;">
                    <div class="clinic-name" style="font-size: 20px; font-weight: bold; color: #000; margin-bottom: 2px;">'.$nomeClinica.'</div>
                    <div class="clinic-address" style="font-size: 14px; color: #555;">'.$enderecoClinica.'</div>
                    <div class="clinic-address" style="font-size: 14px; color: #555;">'.$cidadeEstadoClinica.'</div>
                </div>

                <!-- Logo do sistema à direita -->
                <div class="logo-container" style="text-align: right; flex-shrink: 0;">
                    <img src="data:image/png;base64,'.$logoData.'" class="logo" alt="Logo do Sistema" style="max-height: 50px;">
                </div>
            </div>

                <!-- Título -->
                <h1>Anamnese</h1>

                <!-- Informações do paciente e data -->
                <div class="info">
                    <div><span class="label">Paciente:</span> '.$pacienteInfo['PAC_DCNOME'].'</div>
                    <div><span class="label">Data de Nascimento:</span> '.$pacienteInfo["PAC_DTDATANASC"].'</div>
                    <div><span class="label">CPF:</span> '.($pacienteInfo["PAC_DCCPF"] ?? "Não informado").'</div>
                    <div><span class="label">Data de Geração:</span> '.date("d/m/Y H:i").'</div>
                </div>

                <!-- Perguntas e respostas -->
                <div class="row">
            ';

            $metade = ceil(count($todas_perguntas) / 2);
            $colunas = array_chunk($todas_perguntas, $metade);
            foreach ($colunas as $col) {
                $html .= '<div class="col">';
                foreach ($col as $p) {
                    $id = $p['id'];
                    $resposta = $respostas[$id] ?? 'Não informado';
                    $html .= '<div class="section">';
                    $html .= '<div class="question">'.htmlspecialchars($p['pergunta']).'</div>';
                    $html .= '<div class="answer">'.htmlspecialchars($resposta).'</div>';
                    $html .= '</div>';
                }
                $html .= '</div>';
            }

            $html .= '
                </div>

                <!-- Rodapé -->
                <div style="margin-top:30px; padding-top:10px; border-top:1px solid #ddd; text-align:center; font-family: Arial, sans-serif; font-size:11px; color:#555;">
                  <div>Documento assinado digitalmente em '.$assinaturaData.'</div>
                  <div style="margin-top:6px;">
                    <strong>Código de autenticação:</strong>
                    <span style="display:inline-block; background:#f5f5f7; border:1px solid #e1e1e5; padding:4px 8px; border-radius:4px; font-family:monospace; color:#222;">
                      '.$codigoFormatado.'
                    </span>
                  </div>
                  <div style="margin-top:6px; color:#777;">
                    A autenticidade deste documento pode ser verificada em: <a href="'.$verificarUrl.'" style="color:#0d6efd; text-decoration:none;">'.$verificarUrl.'</a>
                  </div>
                </div>
            </body>
            </html>
            ';

        // --- GERA PDF ---
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfOutput = $dompdf->output();
        
        $pdfDir = __DIR__ . '/../../../public/tmp';
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0777, true);
        }

        $pdfName = $tid."_".$anamneses["ANR_DCCOD_AUTENTICACAO"].".pdf";
        $pdfPath = $pdfDir . '/'.$pdfName;
        file_put_contents($pdfPath, $pdfOutput);

        $s3Obj = new S3Controller();

        $result = json_decode($s3Obj->uploadFile($pdfPath, "anamneses/clinica_$tid/$pdfName"), true);

        if (isset($result['success'])) {
            unlink($pdfPath);
            LogSistema::insertLog("SmileCopilot", 'INFO', Language::get('nova_anamnese_respondida_criado_pdf').' '.$pacienteInfo['PAC_DCNOME'], $tid);
            return json_encode(['success' => true, 'link' => $result['link']]);
        } else {

            LogSistema::insertLog("SmileCopilot", 'ERROR', Language::get('erro_cadastro_anamnese_criado_pdf').' '.$pacienteInfo['PAC_DCNOME'].' err: '.$result['error'], $tid);
            return json_encode(['error' => $result['error'] ?? 'Erro desconhecido']);
        }
    }

    public static function getAnamneseByCodAuth($ANR_DCCOD_AUTENTICACAO) {

        $anamneseModel = new Anamnese();
        $check = $anamneseModel->getAnamneseCheckByCodAuth($ANR_DCCOD_AUTENTICACAO);
        return $check;
    }
}

