<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;
use \App\Model\Entity\Auth;
use \App\Model\Entity\Usuario;

class LoginAtivacaoCheck extends PageLoginAtivacaoCheck{

    public static function getLoginAtivacaoCheckPage($id) {

        $objOrganization = new Organization();
        $dadosUsuarioObj = new Usuario();

        $dadosUsuario = $dadosUsuarioObj->checkEmailAtivacaoById($id);
        
        $content = ([
            'title' => $objOrganization->title,
            'description' => $objOrganization->description,
            'site' => $objOrganization->site,
            'keywords' => $objOrganization->keywords,
            'dadosUsuario' => $dadosUsuario
        ]); 

        return self::getPage('pages/vw_emailAtivacaoCheck', $content);
    } 
}