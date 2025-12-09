<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Utils\Auth;
use \App\Model\Entity\Organization;
use \App\Model\Entity\EmailSend;
use App\Core\Language;





class Email{


    public static function emailConfirmacaoCadatroAssinante($EMAIL, $HASH)  {

        $EmailSendObj = new EmailSend();
        $ALTBODY = "Ative seu cadastro acessando: https://app.smilecopilot.com/ativacaoAssinante?id=$HASH";
        $SUBJECT = "SmileCopilot – Ative sua conta para continuar";


        $MSG = <<<HTML
                <!DOCTYPE html>
                <html lang="pt-br">
                <head>
                <meta charset="UTF-8">
                <title>Ative seu Cadastro - SmileCopilot</title>
                <style>
                    body {
                        background-color: #f4f7fb;
                        font-family: Arial, Helvetica, sans-serif;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        max-width: 650px;
                        margin: auto;
                        background: #ffffff;
                        border-radius: 10px;
                        padding: 30px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
                    }
                    .logo {
                        text-align: center;
                        margin-bottom: 25px;
                    }
                    .title {
                        font-size: 22px;
                        font-weight: bold;
                        color: #333;
                        text-align: center;
                        margin-bottom: 10px;
                    }
                    .text {
                        font-size: 15px;
                        color: #555;
                        line-height: 1.6;
                        margin-bottom: 25px;
                        text-align: center;
                    }
                    .btn {
                        display: inline-block;
                        background: #4a7df0;
                        color: #ffffff !important;
                        padding: 14px 28px;
                        border-radius: 50px;
                        text-decoration: none;
                        font-size: 16px;
                        font-weight: bold;
                        transition: 0.3s;
                    }
                    .btn:hover {
                        background: #315fcc;
                    }
                    .card {
                        text-align: center;
                        padding: 10px 0;
                    }
                    .footer {
                        margin-top: 30px;
                        text-align: center;
                        font-size: 12px;
                        color: #999;
                    }
                </style>
                </head>
                <body>
                
                <div class="container">
                    <div class="logo">
                        <img src="https://app.smilecopilot.com/public/assets/images/logo_bright.png" width="180" alt="SmileCopilot">
                    </div>
                
                    <div class="title">Ativação de Cadastro</div>
                
                    <div class="text">
                        Olá! 😊<br><br>
                        Obrigado por se cadastrar no <strong>SmileCopilot</strong>.<br>
                        Para mantermos sua conta segura, precisamos confirmar que este e-mail é realmente seu.
                        <br><br>
                        Clique no botão abaixo para ativar seu cadastro:
                    </div>
                
                    <div class="card">
                        <a href="https://app.smilecopilot.com/ativacaoAssinante?id=$HASH" class="btn">Ativar Meu Cadastro</a>
                    </div>
                
                    <div class="text">
                        Se você não fez este cadastro, basta ignorar este e-mail.
                    </div>
                
                    <div class="footer">
                        © SmileCopilot – Sistema inteligente para clínicas e consultórios odontológicos.<br>
                        Este é um e-mail automático. Por favor, não responda.
                    </div>
                </div>
                
                </body>
                </html>
                HTML;


        $result = $EmailSendObj->emailSendNotificacao($SUBJECT, $MSG, $EMAIL);

    }

}

