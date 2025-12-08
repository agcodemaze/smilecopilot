<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Utils\Auth;
use \App\Model\Entity\Organization;
use \App\Model\Entity\Paciente;
use Dompdf\Dompdf;

class EditPaciente extends Page {

    public static function editCadPaciente($id) {

        Auth::authCheck(); //verifica se já tem login válido (jwt)
        $objOrganization = new Organization();
        $clinicaInfo = $objOrganization->getConfiguracoes(TENANCY_ID);

        $pacientesObj = new Paciente();
        $convenios = $pacientesObj->getConvenios(); 

        $pacienteInfo = $pacientesObj->getPacientesById(TENANCY_ID, $id);  
        $pacienteInfoConsultas = $pacientesObj->getTimelinePacientesConsultasById(TENANCY_ID, $id);

        // --- COMPONENTES SCRIPTS ---
        $componentsScriptsHeader = '
            <script src="'.ASSETS_PATH.'js/hyper-config.js"></script>
            <link href="'.ASSETS_PATH.'css/vendor.min.css" rel="stylesheet" type="text/css" />
            <link href="'.ASSETS_PATH.'css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
            <link href="'.ASSETS_PATH.'css/icons.min.css" rel="stylesheet" type="text/css" />
            <script src="'.ASSETS_PATH.'js/serviceworkerpwa.js"></script>
            <link rel="manifest" href="/manifest.json">
            <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
        ';

        $componentsScriptsFooter = '
            <script src="'.ASSETS_PATH.'js/vendor.min.js"></script>            
            <script src="'.ASSETS_PATH.'js/app.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/jquery-mask-plugin/jquery.mask.min.js"></script>
        ';

        // --- VIEW ---
        $content = [
            'title' => $objOrganization->title,
            'description' => $objOrganization->description,
            'site' => $objOrganization->site,
            'componentsScriptsHeader' => $componentsScriptsHeader,
            'componentsScriptsFooter' => $componentsScriptsFooter,
            'convenios' => $convenios,
            'pacienteInfo' => $pacienteInfo,
            'pacienteInfoConsultas' => $pacienteInfoConsultas
        ]; 

        return self::getPage('pages/vw_editpaciente', $content);
    }

}
