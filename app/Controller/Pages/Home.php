<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Utils\Auth;
use \App\Model\Entity\Organization;
use \App\Model\Entity\Consultas;
use \App\Model\Entity\Whatsapp;
use \App\Model\Entity\Paciente;
use \App\Controller\Pages\S3Controller;

class Home extends Page{
    /**
    * Metodo responsavel por retornar o conteúdo da Home
    * @return string
    */

    public static function getHome() {
        Auth::authCheck(); //verifica se já tem login válido (jwt)
        $objOrganization = new Organization();
        $objConsultas = new Consultas();
        $objWhatsapp = new Whatsapp();
        $objPaciente= new Paciente();
        $s3 = new S3Controller();

        $consultasHoje = $objConsultas->getConsultasHoje(TENANCY_ID);
        $especialidades = $objConsultas->getEspecialidade(TENANCY_ID);
        $getModeloMsgsWhatsapp = $objWhatsapp->getModelosMsgWhatsapp(TENANCY_ID); 
        $configuracoes = $objOrganization->getConfiguracoes(TENANCY_ID);
        $pacientes = $objPaciente->getPacientes(TENANCY_ID);
        
        /**
         * Comonentes/Scripts que serão carregados na view
         */
        $componentsScriptsHeader = '
            <link href="'.ASSETS_PATH.'css/vendor.min.css" rel="stylesheet" type="text/css" />
            <link href="'.ASSETS_PATH.'css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/pt-br.min.js"></script>
            <link href="https://unpkg.com/vis-timeline/styles/vis-timeline-graph2d.min.css" rel="stylesheet" />
            <script src="https://unpkg.com/vis-timeline/standalone/umd/vis-timeline-graph2d.min.js"></script>
            <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
            <script src="'.ASSETS_PATH.'vendor/ad_sweetalert/jquery-3.7.0.min.js"></script>
            <link href="'.ASSETS_PATH.'vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
            <link href="'.ASSETS_PATH.'vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
            <link href="'.ASSETS_PATH.'vendor/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css" rel="stylesheet" type="text/css" />
            <link href="'.ASSETS_PATH.'vendor/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css" rel="stylesheet" type="text/css" />
            <link href="'.ASSETS_PATH.'vendor/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
            <link href="'.ASSETS_PATH.'vendor/datatables.net-select-bs5/css/select.bootstrap5.min.css" rel="stylesheet" type="text/css" />
            <script src="'.ASSETS_PATH.'js/hyper-config.js"></script>
            <link href="'.ASSETS_PATH.'css/vendor.min.css" rel="stylesheet" type="text/css" />
            <link href="'.ASSETS_PATH.'css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
            <link href="'.ASSETS_PATH.'css/icons.min.css" rel="stylesheet" type="text/css" />
            <link href="'.ASSETS_PATH.'vendor/ad_sweetalert/sweetalert2.min.css" rel="stylesheet">
            <script src="'.ASSETS_PATH.'vendor/ad_sweetalert/sweetalert2.all.min.js"></script>
            <script src="'.ASSETS_PATH.'js/serviceworkerpwa.js"></script>
            <link rel="manifest" href="/manifest.json">

        ';

        $componentsScriptsFooter = '
            <script src="'.ASSETS_PATH.'js/vendor.min.js"></script>            
            <script src="'.ASSETS_PATH.'vendor/highlightjs/highlight.pack.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/clipboard/clipboard.min.js"></script>
            <script src="'.ASSETS_PATH.'js/hyper-syntax.js"></script>
            <script src="'.ASSETS_PATH.'vendor/datatables.net/js/jquery.dataTables.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/datatables.net-fixedcolumns-bs5/js/fixedColumns.bootstrap5.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/datatables.net-buttons/js/buttons.html5.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/datatables.net-buttons/js/buttons.flash.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/datatables.net-buttons/js/buttons.print.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/datatables.net-select/js/dataTables.select.min.js"></script>
            <script src="'.ASSETS_PATH.'js/app.min.js"></script>
            <script src="'.ASSETS_PATH.'utils/alertDelete.js"></script>     
        ';
//<script src="'.ASSETS_PATH.'utils/simple-timepicker-pt.js"></script>
        //VIEW DA HOME
        $content = ([
            'title' => $objOrganization->title,
            'nomeEmpresa' => $objOrganization->nameCompany,
            'description' => $objOrganization->description,
            'modeloMsgsWhatsapp' => $getModeloMsgsWhatsapp,
            'pacientes' => $pacientes,
            'configuracoes' => $configuracoes,
            'site' => $objOrganization->site,
            'consultasHoje' => $consultasHoje,
            'componentsScriptsHeader' => $componentsScriptsHeader,
            'componentsScriptsFooter' => $componentsScriptsFooter,
            'especialidades' => $especialidades,
            's3' => $s3
        ]); 

        //VIEW DA PAGINA
        return self::getPage('pages/vw_home', $content);
    }

    public static function getConsultasByProfissional($DEN_IDDENTISTA) {

        $objConsultas = new Consultas();
        $consultasByProfissional = $objConsultas->getConsultasByProfissional(TENANCY_ID, $DEN_IDDENTISTA);
        return $consultasByProfissional;
    }

    public static function getConsultasByDayProfPredef($DEN_IDDENTISTA, $DIA) {

        $objConsultas = new Consultas();
        $consultasByDayProfPredef = $objConsultas->getConsultasByDayProfPredef(TENANCY_ID, $DEN_IDDENTISTA, $DIA);
        return $consultasByDayProfPredef;
    }

    public static function getConsultasByDayPredef($DIA) {

        $objConsultas = new Consultas();
        $consultasByDayPredef = $objConsultas->getConsultasByDayPredef(TENANCY_ID, $DIA);
        return $consultasByDayPredef;
    }

    public static function getConsultasToCalendar($DEN_IDDENTISTA) {

        $objConsultas = new Consultas();
        $ConsultasToCalendar = $objConsultas->getConsultasToCalendar(TENANCY_ID, $DEN_IDDENTISTA);
        return $ConsultasToCalendar;
    }

    public static function getDatasBloqueadasByProfId($DEN_IDDENTISTA) {

        $objConsultas = new Consultas();
        $datasBloqueadas = $objConsultas->datasBloqueadasByIdProf(TENANCY_ID, $DEN_IDDENTISTA);
        return $datasBloqueadas;
    }
}

