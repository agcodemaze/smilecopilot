<?php 

namespace App\Model\Entity;
use \App\Model\Entity\Conn;
use PDO;
use PDOException;

class Log extends Conn { 

    public function insertLog($LOS_DCUSUARIO, $LOS_DCNIVEL, $LOS_DCMSG, $TENANCY_ID) {

        $levels = ['CRITICAL','INFO','WARNING','ERROR','NOTICE','DEBUG'];
        if (!in_array($LOS_DCNIVEL, $levels)) {
            $LOS_DCNIVEL = 'NOTICE'; 
        }

        try {
            $sql = "INSERT INTO LOS_LOGSISTEMA (LOS_DCUSUARIO, LOS_DCNIVEL, LOS_DTCREATE_AT, LOS_DCMSG, TENANCY_ID) 
                    VALUES (:LOS_DCUSUARIO, :LOS_DCNIVEL, NOW(), :LOS_DCMSG, :TENANCY_ID)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":LOS_DCUSUARIO", $LOS_DCUSUARIO, PDO::PARAM_STR);
            $stmt->bindParam(":LOS_DCNIVEL", $LOS_DCNIVEL, PDO::PARAM_STR);  
            $stmt->bindParam(":LOS_DCMSG", $LOS_DCMSG, PDO::PARAM_STR); 
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID, PDO::PARAM_INT); 
        
            $stmt->execute();
        
            return json_encode([
                "success" => true,
                "message" => "Log gravado com sucesso."
            ]);
        } catch (PDOException $e) {
            return json_encode([
                "success" => false,
                "message" => "Falha ao gravar o log."
            ]);
        } 
    }

    public function getLog($TENANCY_ID) {

        try {
            $sql = "SELECT * FROM LOS_LOGSISTEMA WHERE TENANCY_ID = :TENANCY_ID ORDER BY LOS_DTCREATE_AT DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID, PDO::PARAM_INT); 
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return json_encode([
                "success" => false,
                "message" => "Falha ao buscar os logs do sistema."
            ]);
        } 
    }
}
