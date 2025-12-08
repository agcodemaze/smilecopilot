<?php 

namespace App\Model\Entity;
use \App\Model\Entity\Conn;
use PDO;
use PDOException;

/**
 * A classe Paciente é responsável por gerenciar dados relacionados a pacientes,
 * incluindo a obtenção de convênios e a listagem de pacientes.
 * Ela herda da classe Conn para estabelecer a conexão com o banco de dados.
 */
class Paciente extends Conn { 

    /**
     * Retorna todos os convênios cadastrados no banco de dados.
     * @return array Um array associativo contendo os convênios ou um array com uma
     * mensagem de erro em caso de falha.
     */
    public function getConvenios() {
        try{           
            $sql = "SELECT * FROM CNV_CONVENIO ORDER BY CNV_DCCONVENIO ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }

    /**
     * Retorna todos os pacientes cadastrados para um determinado tenant.
     *
     * @param int $TENANCY_ID O ID do tenant para filtrar os pacientes.
     * @return array Um array associativo contendo os pacientes ou um array com
     * uma mensagem de erro em caso de falha.
     */
    public function getPacientes($TENANCY_ID) {
        try{           
            $sql = "SELECT * FROM VW_PACIENTES WHERE TENANCY_ID = :TENANCY_ID ORDER BY PAC_DCNOME ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    } 

    public function getPacientesById($TENANCY_ID, $PAC_IDPACIENTE) {
        try{           
            $sql = "SELECT * FROM VW_PACIENTES WHERE TENANCY_ID = :TENANCY_ID AND PAC_IDPACIENTE = :PAC_IDPACIENTE";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->bindParam(":PAC_IDPACIENTE", $PAC_IDPACIENTE);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }

    public function getTimelinePacientesConsultasById($TENANCY_ID, $PAC_IDPACIENTE) {
        try{           
            $sql = "SELECT * FROM VW_CONSULTAS WHERE CON_DTCONSULTA <= CURDATE() AND TENANCY_ID = :TENANCY_ID AND PAC_IDPACIENTE = :PAC_IDPACIENTE ORDER BY CON_DTCONSULTA DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->bindParam(":PAC_IDPACIENTE", $PAC_IDPACIENTE);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }



}