<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Utils\Auth;
use \App\Model\Entity\Organization;
use \App\Model\Entity\EmailSend;
use App\Core\Language;





class Email{


    public static function emailConfirmacaoCadatroAssinante($EMAIL)  {

        $EmailSendObj = new EmailSend();

        $MSG = "Teste de email";
        $SUBJECT = "Ativar cadastro SmileCopilot";

        $result = $EmailSendObj->emailSendNotificacao($SUBJECT, $MSG, $EMAIL);
        var_dump($result);
    }

}

