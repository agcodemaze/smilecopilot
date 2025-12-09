<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;
use \App\Model\Entity\Auth;

class LoginAtivacao extends PageLoginAtivacao{

    public static function getLoginAtivacaoPage() {

        $objOrganization = new Organization();
        
        $content = ([
            'title' => $objOrganization->title,
            'description' => $objOrganization->description,
            'site' => $objOrganization->site,
            'keywords' => $objOrganization->keywords
        ]); 

        return self::getPage('pages/vw_sendEmailAtivacao', $content);
    } 
}