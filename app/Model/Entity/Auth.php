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

        if ($userinfo && password_verify($USU_DCSENHA, $userinfo['USU_DCSENHA'])) 
        {
            if ($userinfo['USU_STVERIFICACAO_CADASTRO'] != "VERIFICADO" ) 
            {
                return json_encode([
                    "success" => false,
                    "message" => "Sua conta ainda não foi ativada. <a href=\"/assinanteLinkAtivacao\">Clique aqui</a> para gerar um novo e-mail de verificação."
                ]);
            }

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

        $cookie_domain = $_ENV['ENV_DOMAIN'] ?? getenv('ENV_DOMAIN') ?? '';

        // Cookie HTTP-only para JWT
        setcookie('token', $jwt, [
            'expires' => time() + $jwtLifetime,
            'path' => '/',
            //'domain' => $cookie_domain ,
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        // Gerar refresh token aleatório
        $refreshToken = bin2hex(random_bytes(64));

        // Armazenar refresh token no banco
        $this->putRefreshToken($userinfo['USU_IDUSUARIO'], $refreshToken, $refreshLifetime);

        // Limita tokens ativos (mantém apenas os 5 mais recentes)
        $stmt = $this->pdo->prepare("
            DELETE FROM RTK_REFRESH_TOKEN 
            WHERE USU_IDUSUARIO = :userid 
              AND RTK_IDREFRESH_TOKEN NOT IN (
                  SELECT RTK_IDREFRESH_TOKEN FROM (
                      SELECT RTK_IDREFRESH_TOKEN 
                      FROM RTK_REFRESH_TOKEN 
                      WHERE USU_IDUSUARIO = :userid 
                      ORDER BY RTK_DTCREATE_AT DESC 
                      LIMIT 5
                  ) x
              )
        ");
        $stmt->bindParam(':userid', $userinfo['USU_IDUSUARIO']);
        $stmt->execute();


        // Cookie HTTP-only para refresh token
        setcookie('refresh_token', $refreshToken, [
            'expires' => time() + $refreshLifetime,
            'path' => '/',
            //'domain' => $cookie_domain ,
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    function getRealIpAddr() {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            // Cloudflare
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        }
        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    private function putRefreshToken($USU_IDUSUARIO, $RTK_DCTOKEN, $lifetime) {

        $hashedToken = password_hash($RTK_DCTOKEN, PASSWORD_DEFAULT);
        //$RTK_DCIP = $_SERVER['REMOTE_ADDR'] ?? '';
        $RTK_DCIP = $this->getRealIpAddr();
        $RTK_DCUSERAGENT = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $sql = "INSERT INTO RTK_REFRESH_TOKEN (RTK_DCIP, RTK_DCUSERAGENT, USU_IDUSUARIO, RTK_DCTOKEN, RTK_DTEXPIRE_AT, RTK_DTCREATE_AT, RTK_STREVOKED)
                VALUES (:RTK_DCIP, :RTK_DCUSERAGENT, :USU_IDUSUARIO, :RTK_DCTOKEN, :RTK_DTEXPIRE_AT, NOW(), 0)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':USU_IDUSUARIO', $USU_IDUSUARIO);
        $stmt->bindParam(':RTK_DCTOKEN', $hashedToken);
        $stmt->bindParam(':RTK_DCIP', $RTK_DCIP);
        $stmt->bindParam(':RTK_DCUSERAGENT', $RTK_DCUSERAGENT);
        $RTK_DTEXPIRE_AT = date('Y-m-d H:i:s', time() + $lifetime);
        $stmt->bindParam(':RTK_DTEXPIRE_AT', $RTK_DTEXPIRE_AT);
        $stmt->execute();
    }

    function logoff() {

        $cookie_domain = $_ENV['ENV_DOMAIN'] ?? getenv('ENV_DOMAIN') ?? '';
    
        // Remover cookie de sessão
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                //'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => 'Lax'
            ]);
        }
    
        // Remover JWT
        setcookie('token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            //'domain' => $cookie_domain,
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    
        // Revogar refresh token no banco
        if (isset($_COOKIE['refresh_token'])) {
            $refreshToken = $_COOKIE['refresh_token'];
        
            // Pegar todos os tokens ativos do usuário (ou todos)
            $stmt = $this->pdo->prepare("SELECT * FROM RTK_REFRESH_TOKEN WHERE RTK_STREVOKED = 0 AND RTK_DTEXPIRE_AT > NOW()");
            $stmt->execute();
            $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tokens as $token) {
                // Verifica se o cookie corresponde ao hash do banco
                if (password_verify($refreshToken, $token['RTK_DCTOKEN'])) {
                    // Revoga apenas este token
                    $stmt = $this->pdo->prepare("UPDATE RTK_REFRESH_TOKEN SET RTK_STREVOKED = 1 WHERE RTK_IDREFRESH_TOKEN = :RTK_IDREFRESH_TOKEN");
                    $stmt->bindParam(':RTK_IDREFRESH_TOKEN', $token['RTK_IDREFRESH_TOKEN']);
                    $stmt->execute();
                    break; // já encontramos e revogamos
                }
            }
        }
    
        // Remover cookie de refresh token
        setcookie('refresh_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            //'domain' => $cookie_domain,
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

    public function verifyOrRefreshJWT() {
        $secretKey = $_ENV['ENV_SECRET_KEY'] ?? getenv('ENV_SECRET_KEY') ?? '';

        // Se JWT existir
        if (isset($_COOKIE['token'])) {
            try {
                $decoded = JWT::decode($_COOKIE['token'], new Key($secretKey, 'HS256'));
                return $decoded; // JWT válido
            } catch (\Firebase\JWT\ExpiredException $e) {
                // JWT expirou → tentar renovar com refresh token
            } catch (\Exception $e) {
                return false; // JWT inválido
            }
        }

        // Se refresh token existir
        if (isset($_COOKIE['refresh_token'])) {
            $refreshToken = $_COOKIE['refresh_token'];

            // Pegar todos tokens ativos do usuário
            $stmt = $this->pdo->prepare("SELECT * FROM RTK_REFRESH_TOKEN WHERE RTK_STREVOKED = 0 AND RTK_DTEXPIRE_AT > NOW()");
            $stmt->execute();
            $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tokens as $token) {
                if (password_verify($refreshToken, $token['RTK_DCTOKEN'])) {
                    // Token válido → gerar novo JWT
                    $stmtUser = $this->pdo->prepare("SELECT * FROM USU_USUARIO WHERE USU_IDUSUARIO = :id");
                    $stmtUser->bindParam(':id', $token['USU_IDUSUARIO']);
                    $stmtUser->execute();
                    $userinfo = $stmtUser->fetch(PDO::FETCH_ASSOC);

                    if ($userinfo) {
                        $this->GenJWT($userinfo); // gera novo JWT e renova cookie
                        return JWT::decode($_COOKIE['token'], new Key($secretKey, 'HS256'));
                    }
                }
            }
        }

        return false;
    }

}