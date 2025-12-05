<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Utils\Auth;
use \App\Model\Entity\Organization;

/**
 * A classe Agenda é responsável por controlar a página de agendamentos/calendário
 * do sistema.
 */
class Agenda extends Page{  

    /**
    * Metodo responsavel por retornar o conteúdo da página de agenda.
    * @return string
    */
    public static function getAgenda () {

        Auth::authCheck(); //verifica se já tem login válido (jwt)
        $objOrganization = new Organization();

        /*
        //debug --------
        echo "<pre>";   
        print_r($convenios);
        echo "<pre>"; 
        exit;
        //debug --------
        */


        /**
         * Comonentes/Scripts que serão carregados na view
         */
        $componentsScriptsHeader = '
            <script src="'.ASSETS_PATH.'js/hyper-config.js"></script>
            <link href="'.ASSETS_PATH.'css/vendor.min.css" rel="stylesheet" type="text/css" />
            <link href="'.ASSETS_PATH.'css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
            <link href="'.ASSETS_PATH.'css/icons.min.css" rel="stylesheet" type="text/css" />
            <link rel="manifest" href="/manifest.json">
            <script src="'.ASSETS_PATH.'js/serviceworkerpwa.js"></script>
            
        ';

        $componentsScriptsFooter = '
            <script src="'.ASSETS_PATH.'js/vendor.min.js"></script>    
            <script src="'.ASSETS_PATH.'vendor/fullcalendar/index.global.min.js"></script>                   
            <script src="'.ASSETS_PATH.'js/app.min.js"></script>
            <script src="'.ASSETS_PATH.'vendor/jquery-mask-plugin/jquery.mask.min.js"></script>
            
        ';

        //VIEW DA HOME
        $content = ([
           // 'title' => $objOrganization->title,
           // 'description' => $objOrganization->description,
           // 'site' => $objOrganization->site,
            'componentsScriptsHeader' => $componentsScriptsHeader,
            'componentsScriptsFooter' => $componentsScriptsFooter
        ]); 

        //VIEW DA PAGINA
        return self::getPage('pages/vw_agenda', $content);
    }
}

