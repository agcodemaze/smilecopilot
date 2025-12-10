<?php

namespace App\Controller\Pages; 
use \App\Utils\View;

class PageSenhaAlterarSend {

    private static function getContent($vwPage,$content){
        return View::render($vwPage,$content);
    }

    public static function getPage($vwPage, $content) {

        return View::render('pages/vw_sendEmailAlterarSenha',[
            'title' => $content["title"],
            'description' => $content["description"],
            'site' => $content["description"],
            'keywords' => $content["description"]
        ]); 
    }
}

