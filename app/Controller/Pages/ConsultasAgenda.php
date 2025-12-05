<?php
namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;
use \App\Model\Entity\Consultas;
use \App\Utils\Auth;

class ConsultasAgenda{

    public function getHorariosDisp($data, $duracao) {
        Auth::authCheck(); 
        try {
                $objConsultas = new Consultas();

                $dataObj = \DateTime::createFromFormat('d/m/Y', $data);
                $data = $dataObj->format('Y-m-d');

                $response = $objConsultas->getHorariosDisponiveis($data, $duracao, TENANCY_ID);           
                return $response;           

        } catch (PDOException $e) {   
            $erro = $e->getMessage();           
            //return ["success" => false, "message" => "Erro no servidor. Tente novamente mais tarde."];
        }
    }  
    
    public function updateConsulta($id, $start, $end) {
        Auth::authCheck();

        try {
                $objConsultas = new Consultas();

                $startDate = new \DateTime($start);
                $endDate   = $end ? new \DateTime($end) : null;

                $dataConsulta = $startDate->format('Y-m-d');

                $horaInicio = $startDate->format('H:i:s');

                $duracao = 0;
                if ($endDate) {
                    $intervalo = $startDate->diff($endDate);
                    $duracao = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;
                }

                if($duracao > 60) {
                    return json_encode(["success" => false, "message" => "Consulta não pode ter duração maior que 1 hora."]);
                }

                $response = $objConsultas->updateConsultaAgenda($id, $dataConsulta, $horaInicio, $duracao, TENANCY_ID);

            return json_encode(["success" => true, "message" => "Consulta atualizada com sucesso."]);
        
        } catch (PDOException $e) {   
            $erro = $e->getMessage();           
            return json_encode(["success" => false, "message" => "Erro no servidor. Tente novamente mais tarde."]);
        }
    } 

    public function insertConsulta($idDentista, $especialidade, $paciente, $observacao, $duracao, $data, $horario) {
        Auth::authCheck();

        try {
                $objConsultas = new Consultas();

                $response = $objConsultas->insertConsultaAgenda($idDentista, $especialidade, $paciente, $observacao, $duracao, $data, $horario, TENANCY_ID);

                return json_encode($response);
        
        } catch (PDOException $e) {   
            $erro = $e->getMessage();        
            return json_encode(["success" => false, "message" => "Houve um erro ao cadastrar a consulta."]);
        }
    } 
}