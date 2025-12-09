<?php 

namespace App\Model\Entity;
use \App\Model\Entity\Conn;
use PDO;
use PDOException;
use \App\Controller\Pages\LogSistema; 
use \App\Controller\Pages\Email; 

class Usuario extends Conn { 


    public function insertUsuarioAssinanteInfo($USU_DCNOME, $USU_DCEMAIL, $USU_DCSENHA, $USU_DCTELEFONE) {
        try {   
            $topTenancyId = $this->getMaxTenancyId();
            $TENANCY_ID = (int)$topTenancyId["TENANCY_ID"] + 1;

            $USU_DCSENHA_HASH = password_hash($USU_DCSENHA, PASSWORD_DEFAULT);

            $USU_DTCADASTRO = date('Y-m-d H:i:s');
            $USU_STSTATUS = "0";
            $USU_ENPERFIL = "ADMIN";
            $USU_DCVERIFICACAO_CADASTRO_HASH = bin2hex(random_bytes(32));
            $USU_STVERIFICACAO_CADASTRO = "NAO VERIFICADO";

            $sql = "INSERT INTO USU_USUARIO (USU_DCNOME, USU_DCEMAIL, USU_DCSENHA, USU_DCTELEFONE, USU_DTCADASTRO, USU_STSTATUS, USU_ENPERFIL, USU_STVERIFICACAO_CADASTRO, USU_DCVERIFICACAO_CADASTRO_HASH, TENANCY_ID) 
                    VALUES (:USU_DCNOME, :USU_DCEMAIL, :USU_DCSENHA, :USU_DCTELEFONE, :USU_DTCADASTRO, :USU_STSTATUS, :USU_ENPERFIL, :USU_STVERIFICACAO_CADASTRO, :USU_DCVERIFICACAO_CADASTRO_HASH, :TENANCY_ID)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":USU_DCNOME", $USU_DCNOME);
            $stmt->bindParam(":USU_DCEMAIL", $USU_DCEMAIL);
            $stmt->bindParam(":USU_DCSENHA", $USU_DCSENHA_HASH);
            $stmt->bindParam(":USU_DCTELEFONE", $USU_DCTELEFONE);
            $stmt->bindParam(":USU_DTCADASTRO", $USU_DTCADASTRO);
            $stmt->bindParam(":USU_STSTATUS", $USU_STSTATUS);
            $stmt->bindParam(":USU_ENPERFIL", $USU_ENPERFIL);
            $stmt->bindParam(":USU_STVERIFICACAO_CADASTRO", $USU_STVERIFICACAO_CADASTRO);
            $stmt->bindParam(":USU_DCVERIFICACAO_CADASTRO_HASH", $USU_DCVERIFICACAO_CADASTRO_HASH);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->execute();

            
            $lastId = $this->pdo->lastInsertId();

            if ($lastId) {
                LogSistema::insertLog("SmileCopilot","INFO", \App\Core\Language::get('notice_insert_usuario_assinante') . "Nome do Assinante: $USU_DCNOME | E-mail: $USU_DCEMAIL | Telefone: $USU_DCTELEFONE", $TENANCY_ID);
                Email::emailConfirmacaoCadatroAssinante($USU_DCEMAIL);
            } else {
                LogSistema::insertLog("SmileCopilot","ERROR", \App\Core\Language::get('erro_insert_usuario_assinante') . "Nome do Assinante: $USU_DCNOME | E-mail: $USU_DCEMAIL | Telefone: $USU_DCTELEFONE", $TENANCY_ID);
            }
            return $lastId;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }

    public function getMaxTenancyId()
    {
        try {
            $sql = "SELECT MAX(TENANCY_ID) AS TENANCY_ID FROM USU_USUARIO";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [["error" => $e->getMessage()]];
        }
    }
}