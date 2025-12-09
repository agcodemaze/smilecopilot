<?php 

namespace App\Model\Entity;

use \App\Model\Entity\Conn;
use PDO;
use PDOException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use \App\Controller\Pages\LogSistema; 

/**
 * Classe responsável por gerenciar os eventos
 */
class EmailSend extends Conn { 


    public function emailSendNotificacao ($SUBJECT, $MSG, $EMAIL)  {

        $mail = new PHPMailer(true);

        try {
                $mail->isSMTP();
                $mail->Host = $_ENV['ENV_SMTP_HOST'] ?? getenv('ENV_SMTP_HOST'); 
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['ENV_SMTP_USER'] ?? getenv('ENV_SMTP_USER'); 
                $mail->Password = $_ENV['ENV_SMTP_PASS'] ?? getenv('ENV_SMTP_PASS'); 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
                $mail->Port = $_ENV['ENV_SMTP_PORT'] ?? getenv('ENV_SMTP_PORT'); 

                $mail->CharSet = 'UTF-8';
       
                // De / Para
                $mail->setFrom($mail->Username, 'SmileCopilot');
                $mail->addAddress($EMAIL);
            
                // Conteúdo
                $mail->isHTML(true);
                $mail->Subject = $SUBJECT;
                $mail->Body    = $MSG;
                $mail->AltBody = $MSG;
            
                $mail->send();
                return ["success" => true,"message" => "Confirmação de e-mail enviado com sucesso."];
        
        } catch (Exception $e) {
            return ["success" => false,"message" => $mail->ErrorInfo];
        }
    }
}