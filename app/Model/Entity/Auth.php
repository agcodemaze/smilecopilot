<?php 

namespace App\Model\Entity;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use \App\Model\Entity\Conn; 
use PDO;
use PDOException;
use \App\Controller\Pages\LogSistema; 

class Auth extends Conn { 

    public function autenticar($USU_DCEMAIL, $USU_DCSENHA, $TENANCY_ID) {

        $sql = "SELECT * FROM USU_USUARIO WHERE USU_DCEMAIL = :USU_DCEMAIL AND TENANCY_ID = :TENANCY_ID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":USU_DCEMAIL", $USU_DCEMAIL);
        $stmt->bindParam(":TENANCY_ID", $TENANCY_ID);
        $stmt->execute();
        $userinfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userinfo['USU_STVERIFICACAO_CADASTRO'] != "VERIFICADO" ) 
        {
            return json_encode(["success" => false, "message" => "Esta conta não está inativa."]);
        }

        if ($userinfo && password_verify($USU_DCSENHA, $userinfo['USU_DCSENHA'])) 
        {
            $this->GenJWT($userinfo);
            LogSistema::insertLog($userinfo['USU_DCNOME'],"NOTICE", \App\Core\Language::get('notice_insert_consulta'), $TENANCY_ID);
            return json_encode(["success" => true, "message" => "Credenciais válidas!"]);
        }
        else
            {           
                LogSistema::insertLog("SmileCopilot","WARNING", \App\Core\Language::get('warning_insert_consulta').$USU_DCEMAIL, $TENANCY_ID);     
                return json_encode(["success" => false, "message" => "Credenciais inválidas!"]);
            }
    }

    function GenJWT($userinfo) {
        $secretKey   = $_ENV['ENV_SECRET_KEY'] ?? getenv('ENV_SECRET_KEY') ?? '';

        // Tempo de vida do JWT e do refresh token
        $jwtLifetime = 60 * 60;          // 1 hora
        $refreshLifetime = 60 * 60 * 24 * 60; // 60 dias

        $payload = [
            "iss" => "smilecopilot",
            "iat" => time(),
            "exp" => time() + $jwtLifetime,
            "data" => [
                "id" => $userinfo['USU_IDUSUARIO'],
                "email" => $userinfo['USU_DCEMAIL'],
                "perfil" => $userinfo['USU_ENPERFIL'],
                "nome" => $userinfo['USU_DCNOME'],
                "tenancyid" => $userinfo['TENANCY_ID']
            ]
        ];

        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        // Cookie HTTP-only para JWT
        setcookie('token', $jwt, [
            'expires' => time() + $jwtLifetime,
            'path' => '/',
            //'domain' => 'smilecopilot.com',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        // Gerar refresh token aleatório
        $refreshToken = bin2hex(random_bytes(64));

        // Armazenar refresh token no banco
        $this->putRefreshToken($userinfo['USU_IDUSUARIO'], $refreshToken, $refreshLifetime);

        // Cookie HTTP-only para refresh token
        setcookie('refresh_token', $refreshToken, [
            'expires' => time() + $refreshLifetime,
            'path' => '/',
            //'domain' => 'smilecopilot.com',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    private function putRefreshToken($USU_IDUSUARIO, $RTK_DCTOKEN, $lifetime) {
        // Opcional: armazenar hash para mais segurança
        $hashedToken = password_hash($RTK_DCTOKEN, PASSWORD_DEFAULT);

        $sql = "INSERT INTO RTK_REFRESH_TOKEN (USU_IDUSUARIO, RTK_DCTOKEN, RTK_DTEXPIRE_AT, RTK_DTCREATE_AT, RTK_STREVOKED)
                VALUES (:USU_IDUSUARIO, :RTK_DCTOKEN, :RTK_DTEXPIRE_AT, NOW(), 0)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':USU_IDUSUARIO', $USU_IDUSUARIO);
        $stmt->bindParam(':RTK_DCTOKEN', $hashedToken);
        $RTK_DTEXPIRE_AT = date('Y-m-d H:i:s', time() + $lifetime);
        $stmt->bindParam(':RTK_DTEXPIRE_AT', $RTK_DTEXPIRE_AT);
        $stmt->execute();
    }

    function logoff() {
    
        // Remover cookie de sessão
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => 'Lax'
            ]);
        }
    
        // Remover JWT
        setcookie('token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            //'domain' => 'smilecopilot.com.br',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    
        // Revogar refresh token no banco
        if (isset($_COOKIE['refresh_token'])) {
            $refreshToken = $_COOKIE['refresh_token'];
        
            // Marca como revogado
            $stmt = $this->pdo->prepare("UPDATE RTK_REFRESH_TOKEN SET RTK_STREVOKED = 1 WHERE RTK_STREVOKED = 0");
            $stmt->execute();
        }
    
        // Remover cookie de refresh token
        setcookie('refresh_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            //'domain' => 'smilecopilot.com.br',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    
        // Limpar localStorage e redirecionar
        echo "<script>
                localStorage.removeItem('jwt');
                window.location.href = '/login';
                </script>";
        exit;
    }
}