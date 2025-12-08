<?php 

namespace App\Model\Entity;
use \App\Model\Entity\Conn;
use PDO;
use PDOException;

/**
 * Classe responsável por gerenciar as operações de consultas no banco de dados.
 */
class Consultas extends Conn { 

    /**
     * Busca todas as consultas agendadas para o dia atual com base no fuso horário de São Paulo (-03:00).
     *
     * @param int $TENANCY_ID O ID do inquilino (clínica).
     * @return array Retorna um array associativo com as consultas ou um array com a mensagem de erro.
     */
    public function getConsultasHoje($TENANCY_ID) {
        try{           
            $sql = "SELECT * FROM VW_CONSULTAS WHERE CON_DTCONSULTA = DATE(CONVERT_TZ(NOW(), '+00:00', '-03:00')) AND TENANCY_ID = :TENANCY_ID ORDER BY CON_DTCONSULTA, CON_HORACONSULTA ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }

    public function getEspecialidade($TENANCY_ID) {
        try{           
            $sql = "SELECT * FROM ESP_ESPECIALIDADE WHERE TENANCY_ID = :TENANCY_ID ORDER BY ESP_DCTITULO  ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }

    public function datasBloqueadasByIdProf($TENANCY_ID, $DEN_IDDENTISTA) {
        try{           
            $sql = "SELECT AGB_DTBLOQUEADA FROM AGB_AGENDA_BLOQUEIO WHERE TENANCY_ID = :TENANCY_ID AND DEN_IDDENTISTA = :DEN_IDDENTISTA AND AGB_DCDIA_TODO = 'SIM'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->bindParam(":DEN_IDDENTISTA", $DEN_IDDENTISTA);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }

    public function datasBloqueadasByDataProf($TENANCY_ID, $DEN_IDDENTISTA, $AGB_DTBLOQUEADA) {
        try{           
            $sql = "SELECT AGB_DTBLOQUEADA FROM AGB_AGENDA_BLOQUEIO 
            WHERE TENANCY_ID = :TENANCY_ID 
                AND DEN_IDDENTISTA = :DEN_IDDENTISTA 
                AND AGB_DCDIA_TODO = 'SIM' 
                AND AGB_DTBLOQUEADA = :AGB_DTBLOQUEADA";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->bindParam(":DEN_IDDENTISTA", $DEN_IDDENTISTA);
            $stmt->bindParam(":AGB_DTBLOQUEADA", $AGB_DTBLOQUEADA);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }

    public function getConvenioByIdPaciente($PAC_IDPACIENTE, $TENANCY_ID) {
        try{           
            $sql = "SELECT CNV.CNV_DCCONVENIO 
                    FROM PAC_PACIENTES PAC
                    INNER JOIN CNV_CONVENIO CNV ON (PAC.CNV_IDCONVENIO = CNV.CNV_IDCONVENIO AND TENANCY_ID = :TENANCY_ID)
                    WHERE PAC.PAC_IDPACIENTE = :PAC_IDPACIENTE";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->bindParam(":PAC_IDPACIENTE", $PAC_IDPACIENTE);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }

    public function insertConsultaAgenda($CON_DCCONVENIO, $DEN_IDDENTISTA, $CON_NMESPECIALIDADE, $PAC_IDPACIENTE, $CON_DCOBSERVACOES, $CON_NUMDURACAO, $CON_DTCONSULTA, $CON_HORACONSULTA, $CON_DCHASH_CONFIRMACAO_PRESENCA, $TENANCY_ID) {
        
        $dataObj = \DateTime::createFromFormat('d/m/Y', $CON_DTCONSULTA);
        $CON_DTCONSULTA = $dataObj->format('Y-m-d');

        $checkDataBloqueio = $this->datasBloqueadasByDataProf($TENANCY_ID, $DEN_IDDENTISTA, $CON_DTCONSULTA);
        if(!empty($checkDataBloqueio)) {
            return ["success" => false,"message" => "O dentista marcou esta data como indisponível para consultas. Por favor, escolha uma nova data!"];
        }

        try{           
            $sql = "
                INSERT INTO CON_CONSULTAS (
                    CON_DCCONVENIO,
                    DEN_IDDENTISTA,
                    CON_NMESPECIALIDADE,
                    PAC_IDPACIENTE,
                    CON_DCOBSERVACOES,
                    CON_NUMDURACAO,
                    CON_DTCONSULTA,
                    CON_HORACONSULTA,
                    CON_DCHASH_CONFIRMACAO_PRESENCA,
                    TENANCY_ID
                ) VALUES (
                    :CON_DCCONVENIO,
                    :DEN_IDDENTISTA,
                    :CON_NMESPECIALIDADE,
                    :PAC_IDPACIENTE,
                    :CON_DCOBSERVACOES,
                    :CON_NUMDURACAO,
                    :CON_DTCONSULTA,
                    :CON_HORACONSULTA,
                    :CON_DCHASH_CONFIRMACAO_PRESENCA,
                    :TENANCY_ID
                )
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':CON_DCCONVENIO', $CON_DCCONVENIO, PDO::PARAM_STR);
            $stmt->bindValue(':DEN_IDDENTISTA', $DEN_IDDENTISTA, PDO::PARAM_STR);
            $stmt->bindValue(':CON_NMESPECIALIDADE', $CON_NMESPECIALIDADE, PDO::PARAM_STR);
            $stmt->bindValue(':PAC_IDPACIENTE', $PAC_IDPACIENTE, PDO::PARAM_STR);
            $stmt->bindValue(':CON_DCOBSERVACOES', $CON_DCOBSERVACOES, PDO::PARAM_STR);
            $stmt->bindValue(':CON_NUMDURACAO', $CON_NUMDURACAO, PDO::PARAM_STR);
            $stmt->bindValue(':CON_DTCONSULTA', $CON_DTCONSULTA, PDO::PARAM_STR);
            $stmt->bindValue(':CON_HORACONSULTA', $CON_HORACONSULTA, PDO::PARAM_STR);
            $stmt->bindValue(':CON_DCHASH_CONFIRMACAO_PRESENCA', $CON_DCHASH_CONFIRMACAO_PRESENCA, PDO::PARAM_STR);
            $stmt->bindValue(':TENANCY_ID', $TENANCY_ID, PDO::PARAM_STR);

            $stmt->execute();

            return ["success" => true,"message" => "Consulta cadastrada com sucesso!"];

        } catch (PDOException $e) {
            //return ["success" => false,"message" => "Houve um erro ao cadastrar a consulta!"];
            return ["error" => $e->getMessage()];
        } 
    }

    public function updateConsultaAgendaInfo($CON_IDCONSULTA, $CON_DCCONVENIO, $DEN_IDDENTISTA, $CON_NMESPECIALIDADE, $PAC_IDPACIENTE, $CON_DCOBSERVACOES, $CON_NUMDURACAO, $CON_DTCONSULTA, $CON_HORACONSULTA, $TENANCY_ID) {
        
        $dataObj = \DateTime::createFromFormat('d/m/Y', $CON_DTCONSULTA);
        $CON_DTCONSULTA = $dataObj->format('Y-m-d');

        $checkDataBloqueio = $this->datasBloqueadasByDataProf($TENANCY_ID, $DEN_IDDENTISTA, $CON_DTCONSULTA);
        if(!empty($checkDataBloqueio)) {
            return ["success" => false, "message" => "O dentista marcou esta data como indisponível para consultas. Por favor, escolha uma nova data!"];
        }

        try {
            $sql = "
                UPDATE CON_CONSULTAS
                SET
                    CON_DCCONVENIO = :CON_DCCONVENIO,
                    DEN_IDDENTISTA = :DEN_IDDENTISTA,
                    CON_NMESPECIALIDADE = :CON_NMESPECIALIDADE,
                    PAC_IDPACIENTE = :PAC_IDPACIENTE,
                    CON_DCOBSERVACOES = :CON_DCOBSERVACOES,
                    CON_NUMDURACAO = :CON_NUMDURACAO,
                    CON_DTCONSULTA = :CON_DTCONSULTA,
                    CON_HORACONSULTA = :CON_HORACONSULTA,
                    TENANCY_ID = :TENANCY_ID
                WHERE CON_IDCONSULTA = :CON_IDCONSULTA
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':CON_DCCONVENIO', $CON_DCCONVENIO, PDO::PARAM_STR);
            $stmt->bindValue(':DEN_IDDENTISTA', $DEN_IDDENTISTA, PDO::PARAM_STR);
            $stmt->bindValue(':CON_NMESPECIALIDADE', $CON_NMESPECIALIDADE, PDO::PARAM_STR);
            $stmt->bindValue(':PAC_IDPACIENTE', $PAC_IDPACIENTE, PDO::PARAM_STR);
            $stmt->bindValue(':CON_DCOBSERVACOES', $CON_DCOBSERVACOES, PDO::PARAM_STR);
            $stmt->bindValue(':CON_NUMDURACAO', $CON_NUMDURACAO, PDO::PARAM_STR);
            $stmt->bindValue(':CON_DTCONSULTA', $CON_DTCONSULTA, PDO::PARAM_STR);
            $stmt->bindValue(':CON_HORACONSULTA', $CON_HORACONSULTA, PDO::PARAM_STR);
            $stmt->bindValue(':TENANCY_ID', $TENANCY_ID, PDO::PARAM_STR);
            $stmt->bindValue(':CON_IDCONSULTA', $CON_IDCONSULTA, PDO::PARAM_INT);

            $stmt->execute();

            return ["success" => true, "message" => "Consulta atualizada com sucesso!"];

        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }


    public function getConsultasByHash($CON_DCHASH_CONFIRMACAO_PRESENCA) {
        try{           
            $sql = "SELECT * FROM VW_CONSULTAS WHERE CON_DCHASH_CONFIRMACAO_PRESENCA = :CON_DCHASH_CONFIRMACAO_PRESENCA";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":CON_DCHASH_CONFIRMACAO_PRESENCA", $CON_DCHASH_CONFIRMACAO_PRESENCA);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }

    public function updateConfirmacaoPresencaByHashUser($CON_DCHASH_CONFIRMACAO_PRESENCA, $CON_STCONFIRMACAO_PRESENCA) {
        
        if($CON_STCONFIRMACAO_PRESENCA == "CONFIRMADA") {
            $CON_ENSTATUS = "CONFIRMADA";
        }elseif($CON_STCONFIRMACAO_PRESENCA == "CANCELADA") {
            $CON_ENSTATUS = "CANCELADA";
        }else {
            return "Status inválido";
        }

        try {
            $sql = "UPDATE CON_CONSULTAS 
                    SET 
                    CON_STCONFIRMACAO_PRESENCA = :CON_STCONFIRMACAO_PRESENCA,
                    CON_ENSTATUS = :CON_ENSTATUS 
                    WHERE CON_DCHASH_CONFIRMACAO_PRESENCA = :CON_DCHASH_CONFIRMACAO_PRESENCA";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":CON_STCONFIRMACAO_PRESENCA", $CON_STCONFIRMACAO_PRESENCA);
            $stmt->bindParam(":CON_DCHASH_CONFIRMACAO_PRESENCA", $CON_DCHASH_CONFIRMACAO_PRESENCA);
            $stmt->bindParam(":CON_ENSTATUS", $CON_ENSTATUS);
            $stmt->execute();
        
            return $stmt->rowCount();
        
        } catch (\PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function getConsultasByProfissional($TENANCY_ID, $DEN_IDDENTISTA) {
        try{           
            $sql = "SELECT * FROM VW_CONSULTAS WHERE TENANCY_ID = :TENANCY_ID 
            AND DEN_IDDENTISTA = :DEN_IDDENTISTA ORDER BY CON_DTCONSULTA, CON_HORACONSULTA ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->bindParam(":DEN_IDDENTISTA", $DEN_IDDENTISTA);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } 
    }

    public function getConsultasByProfissionalAndRange($TENANCY_ID, $DEN_IDDENTISTA, $CON_DTCONSULTAINI, $CON_DTCONSULTAEND) {
        try {
            // Monta o SQL base
            $sql = "SELECT * FROM VW_CONSULTAS 
                    WHERE TENANCY_ID = :TENANCY_ID
                    AND CON_DTCONSULTA BETWEEN :CON_DTCONSULTAINI AND :CON_DTCONSULTAEND";
    
            // Se for diferente de 'all', adiciona o filtro de dentista
            if ($DEN_IDDENTISTA !== "all") {
                $sql .= " AND DEN_IDDENTISTA = :DEN_IDDENTISTA";
            }
        
            $sql .= " ORDER BY CON_DTCONSULTA, CON_HORACONSULTA ASC";
        
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->bindParam(":CON_DTCONSULTAINI", $CON_DTCONSULTAINI);
            $stmt->bindParam(":CON_DTCONSULTAEND", $CON_DTCONSULTAEND);
        
            // Só vincula o dentista se o filtro for usado
            if ($DEN_IDDENTISTA !== "all") {
                $stmt->bindParam(":DEN_IDDENTISTA", $DEN_IDDENTISTA);
            }
        
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function getConsultasByDayProfPredef($TENANCY_ID, $DEN_IDDENTISTA, $diaSemana) {
        try {     
            $dataInicio = $dataFim = null;

            switch ($diaSemana) {
                case "1": // Hoje
                    $dataInicio = $dataFim = date('Y-m-d');
                    break;
                case "2": // Últimos 6 meses
                    $dataInicio = date('Y-m-d', strtotime('-6 months'));
                    $dataFim = date('Y-m-d');
                    break;
                case "3": // Próximos 6 meses
                    $dataInicio = date('Y-m-d');
                    $dataFim = date('Y-m-d', strtotime('+6 months'));
                    break;
                case "4": // Últimos 2 anos
                    $dataInicio = date('Y-m-d', strtotime('-12 months'));
                    $dataFim = date('Y-m-d');
                    break;
                default:
                    throw new Exception("Opção inválida para diaSemana.");
            }

            $sql = "SELECT * 
                    FROM VW_CONSULTAS 
                    WHERE TENANCY_ID = :TENANCY_ID
                      AND DEN_IDDENTISTA = :DEN_IDDENTISTA
                      AND CON_DTCONSULTA BETWEEN :dataInicio AND :dataFim
                    ORDER BY CON_DTCONSULTA, CON_HORACONSULTA ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->bindParam(":DEN_IDDENTISTA", $DEN_IDDENTISTA);
            $stmt->bindParam(":dataInicio", $dataInicio);
            $stmt->bindParam(":dataFim", $dataFim);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }


    public function getConsultasByDayPredef($TENANCY_ID, $diaSemana) {
        try {     
            $dataInicio = $dataFim = null;

            switch ($diaSemana) {
                case "1": // Hoje
                    $dataInicio = $dataFim = date('Y-m-d');
                    break;
                case "2": // Últimos 6 meses
                    $dataInicio = date('Y-m-d', strtotime('-6 months'));
                    $dataFim = date('Y-m-d');
                    break;
                case "3": // Próximos 6 meses
                    $dataInicio = date('Y-m-d');
                    $dataFim = date('Y-m-d', strtotime('+6 months'));
                    break;
                case "4": // Últimos 2 anos
                    $dataInicio = date('Y-m-d', strtotime('-2 years'));
                    $dataFim = date('Y-m-d');
                    break;
                default:
                    throw new Exception("Opção inválida para diaSemana.");
            }

            $sql = "SELECT * 
                    FROM VW_CONSULTAS 
                    WHERE TENANCY_ID = :TENANCY_ID
                      AND CON_DTCONSULTA BETWEEN :dataInicio AND :dataFim
                    ORDER BY CON_DTCONSULTA, CON_HORACONSULTA ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->bindParam(":dataInicio", $dataInicio);
            $stmt->bindParam(":dataFim", $dataFim);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function getHorariosDisponiveis($CON_DTCONSULTA, $CON_NUMDURACAO, $TENANCY_ID) { 
        try {
            $sql = "WITH RECURSIVE horarios_possiveis AS (
                        SELECT TIME('06:00:00') AS horario
                        UNION ALL
                        SELECT ADDTIME(horario, SEC_TO_TIME(:CON_NUMDURACAO*60))
                        FROM horarios_possiveis
                        WHERE ADDTIME(horario, SEC_TO_TIME(:CON_NUMDURACAO*60)) <= TIME('23:00:00')
                    )
                    SELECT h.horario
                    FROM horarios_possiveis h
                    WHERE NOT EXISTS (
                        SELECT 1
                        FROM CON_CONSULTAS c
                        WHERE c.CON_DTCONSULTA = :CON_DTCONSULTA
                          AND c.TENANCY_ID = :TENANCY_ID
                          AND c.CON_ENSTATUS IN ('AGENDADA','CONFIRMADA')
                          AND (
                              h.horario < ADDTIME(c.CON_HORACONSULTA, SEC_TO_TIME(c.CON_NUMDURACAO*60))
                              AND ADDTIME(h.horario, SEC_TO_TIME(:CON_NUMDURACAO*60)) > c.CON_HORACONSULTA
                          )
                    )
                    ORDER BY h.horario";
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":CON_DTCONSULTA", $CON_DTCONSULTA);
            $stmt->bindParam(":CON_NUMDURACAO", $CON_NUMDURACAO);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->execute();
        
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            //return ["error" => $e->getMessage()];
        }
    }

    public function getConsultasToCalendar($TENANCY_ID, $DEN_IDDENTISTA) {
        try {     
            // Intervalo de 12 meses centrado em hoje
            $dataInicio = date('Y-m-d', strtotime('-6 months'));
            $dataFim = date('Y-m-d', strtotime('+6 months'));

            // Monta SQL base
            $sql = "SELECT * 
                    FROM VW_CONSULTAS 
                    WHERE TENANCY_ID = :TENANCY_ID
                        AND CON_DTCONSULTA BETWEEN :dataInicio AND :dataFim";

            // Se não for "all", adiciona filtro do dentista
            if ($DEN_IDDENTISTA !== "all") {
                $sql .= " AND DEN_IDDENTISTA = :DEN_IDDENTISTA";
            }

            $sql .= " ORDER BY CON_DTCONSULTA, CON_HORACONSULTA ASC";

            $stmt = $this->pdo->prepare($sql);

            // Sempre vincula TENANCY e datas
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->bindParam(":dataInicio", $dataInicio);
            $stmt->bindParam(":dataFim", $dataFim);

            // Só vincula dentista se não for "all"
            if ($DEN_IDDENTISTA !== "all") {
                $stmt->bindParam(":DEN_IDDENTISTA", $DEN_IDDENTISTA);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function updateConsultaAgenda($CON_IDCONSULTA, $CON_DTCONSULTA, $CON_HORACONSULTA, $CON_NUMDURACAO, $TENANCY_ID)
    {
        try {
            $sql = "UPDATE CON_CONSULTAS 
                    SET 
                        CON_DTCONSULTA = :CON_DTCONSULTA,
                        CON_HORACONSULTA = :CON_HORACONSULTA,
                        CON_NUMDURACAO = :CON_NUMDURACAO
                    WHERE 
                        TENANCY_ID = :TENANCY_ID 
                        AND CON_IDCONSULTA = :CON_IDCONSULTA";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":CON_DTCONSULTA", $CON_DTCONSULTA);
            $stmt->bindParam(":CON_HORACONSULTA", $CON_HORACONSULTA);
            $stmt->bindParam(":CON_NUMDURACAO", $CON_NUMDURACAO, PDO::PARAM_INT);
            $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
            $stmt->bindParam(":CON_IDCONSULTA", $CON_IDCONSULTA);

            $stmt->execute();

            // Retorna sucesso ou falha
            if ($stmt->rowCount() > 0) {
                return [
                    "success" => true,
                    "message" => "Consulta atualizada com sucesso.",
                    "id" => $CON_IDCONSULTA,
                    "data" => $CON_DTCONSULTA,
                    "hora" => $CON_HORACONSULTA,
                    "duracao" => $CON_NUMDURACAO
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "Nenhuma linha foi atualizada (talvez os dados sejam idênticos)."
                ];
            }
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Erro: " . $e->getMessage()];
        }
    }





}