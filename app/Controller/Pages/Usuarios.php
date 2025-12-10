<?php
namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;
use \App\Model\Entity\Usuario;
use \App\Utils\Auth;

class Usuarios{ 

    public function insertUsuarioAssinante($email, $senha, $nome, $telefone) {
        try {
                $objUsuario = new Usuario();
                $response = $objUsuario->insertUsuarioAssinanteInfo($nome, $email, $senha, $telefone);                  

        } catch (PDOException $e) {   
            $erro = $e->getMessage();           
        }
    }  

    public function sendEmailAtivacao($email) {
        try {
                $objUsuario = new Usuario();
                $response = $objUsuario->sendEmailAtivacaoAssinante($email); 
                return $response;               

        } catch (PDOException $e) {   
            $erro = $e->getMessage();           
        }
    }  

    public function sendEmailAlterarSenha($email) {
        try {
                $objUsuario = new Usuario();
                $response = $objUsuario->sendEmailLinkAlteracaoSenha($email); 
                return $response;               

        } catch (PDOException $e) {   
            $erro = $e->getMessage();           
        }
    } 

    public function checkEmailUsuarioAssinanteExists($email) {
        try {
                $objUsuario = new Usuario();
                $response = $objUsuario->checkUsuarioExistsByEmail($email); 
                return $response;               

        } catch (PDOException $e) {   
            $erro = $e->getMessage();           
        }
    } 

    public function updateSenhaAssinante($userid, $password) {
        try {
                $objUsuario = new Usuario();
                $response = $objUsuario->updateSenhaAssinanteInfo($userid, $password);                  

        } catch (PDOException $e) {   
            $erro = $e->getMessage();           
        }
    }  
}