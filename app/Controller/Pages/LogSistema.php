<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Utils\Auth;
use \App\Model\Entity\Organization;
use \App\Model\Entity\Log;

class LogSistema{
    
    public static function insertLog($LOS_DCUSUARIO, $LOS_DCNIVEL, $LOS_DCMSG, $TENANCY_ID) {
       
        $TENANCY_ID = $TENANCY_ID ?? 0;

        $log = new Log();
        return $log->insertLog($LOS_DCUSUARIO, $LOS_DCNIVEL, $LOS_DCMSG, $TENANCY_ID);
    }

}

