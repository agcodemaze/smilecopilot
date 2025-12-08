<?php 

namespace App\Model\Entity;
use \App\Model\Entity\Conn;
use PDO;
use PDOException;

/**
 * A classe Profissionais é responsável por gerenciar dados relacionados a profissionais,
 * como dentistas, na base de dados.
 *
 * Ela herda da classe Conn para estabelecer a conexão com o banco de dados.
 */
class Profissionais extends Conn { 

    /**
     * Retorna todos os profissionais (dentistas) cadastrados para um determinado tenant.
     *
     * @param int $TENANCY_ID O ID do tenant para filtrar os profissionais.
     * @return array Um array associativo contendo os profissionais ou um array com
     * uma mensagem de erro em caso de falha.
     */
    public function getProfissionais($TENANCY_ID) {
        try{           
            $sql = "SELECT * FROM DEN_DENTISTAS WHERE TENANCY_ID = :TENANCY_ID ORDER BY DEN_DCNOME ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':TENANCY_ID', $TENANCY_ID, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }

    public function getProfissionalById($TENANCY_ID, $DEN_IDDENTISTA) {
        try{           
            $sql = "SELECT * FROM DEN_DENTISTAS WHERE TENANCY_ID = :TENANCY_ID AND DEN_IDDENTISTA = :DEN_IDDENTISTA";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':TENANCY_ID', $TENANCY_ID, PDO::PARAM_STR);
            $stmt->bindValue(':DEN_IDDENTISTA', $DEN_IDDENTISTA, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }
}