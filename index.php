<?php
require __DIR__.'/vendor/autoload.php';

$dotenvPath = __DIR__;

if (file_exists($dotenvPath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->load();
}

$cookie_domain = $_ENV['ENV_DOMAIN'] ?? getenv('ENV_DOMAIN') ?? '';

// Tempo de vida da sessão e do JWT (180 dias)
$lifetime = 60 * 60 * 24 * 180;

session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    //'domain' => $cookie_domain,
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

ini_set('session.gc_maxlifetime', $lifetime);
ini_set('session.cookie_lifetime', $lifetime);

session_start();

use \App\Http\Router;
use \App\Http\Response;
use \App\Controller\Pages\Home; 
use \App\Controller\Pages\EditPaciente; 
use \App\Controller\Pages\CadAnamnese; 
use \App\Controller\Pages\Agenda; 
use \App\Controller\Pages\ListPaciente; 
use \App\Controller\Pages\ListConsulta; 
use \App\Controller\Pages\Login; 
use \App\Controller\Pages\StreamEvents; 
use \App\Controller\Pages\S3Controller; 
use \App\Controller\Pages\EncryptDecrypt; 
use \App\Controller\Pages\ListModeloAnamnese; 
use \App\Controller\Pages\CadModeloAnamnese; 
use \App\Controller\Pages\EditModeloAnamnese; 
use \App\Controller\Pages\ListLog; 
use \App\Controller\Pages\LogSistema; 
use \App\Controller\Pages\Usuarios; 
use \App\Controller\Pages\LoginAtivacao; 
use \App\Controller\Pages\LoginAtivacaoCheck; 
use \App\Controller\Pages\SenhaAlterarSend;  
use \App\Controller\Pages\RedefinirSenha; 
use App\Core\Language;

// Inicia sistema de idiomas
Language::init();

define('URL','https://app.smilecopilot.com.br');
define('ASSETS_PATH', '/public/assets/');
define('UXCOMPONENTS_PATH', __DIR__ . '/UX_Components/');


$obRouter = new Router(URL);

// ROTA ROOT -> redireciona para /login
$obRouter->get('/', [
    function () {
        $response = new Response(302, ''); // código 302 + corpo vazio
        $response->addHeader('Location', '/login');
        return $response;
    }
]);

//ROTA HOME
$obRouter->get('/inicial',[
    function(){
        return new Response(200,Home::getHome());
    }
]);

//ROTA CAD PACIENTES
$obRouter->get('/editarpaciente',[
    function(){
        $id = $_GET['id'] ?? null; 
        return new Response(200,EditPaciente::editCadPaciente($id)); 
    }
]);

//ROTA LIST PACIENTES
$obRouter->get('/listapaciente',[
    function(){
        return new Response(200,ListPaciente::getPaciente());
    }
]);

//ROTA LIST MODELO ANAMNESE
$obRouter->get('/listmodeloanamnese',[
    function(){
        return new Response(200,ListModeloAnamnese::getModeloAnamnese());
    }
]);

//ROTA CAD MODELO ANAMNESE
$obRouter->get('/cadmodeloanamnese',[
    function(){
        return new Response(200,CadModeloAnamnese::getModeloAnamnese());
    }
]); 

//ROTA PROCESSA INSERT MODELO ANAMNESE
$obRouter->post('/cadmodeloanamneseProc', [
    function() {
        header('Content-Type: application/json');
        $dados = json_decode(file_get_contents('php://input'), true);

        $titulo = $dados['titulo'] ?? '';
        $modelo = $dados['modelo'] ?? null;
        $idioma = $dados['idioma'] ?? 'pt';

        $controller = new CadModeloAnamnese();
        return new Response(200, $controller->cadModeloAnemnese($modelo, $titulo, $idioma));
    }
]);

//ROTA PROCESSA UPDATE MODELO ANAMNESE
$obRouter->post('/updatemodeloanamneseProc', [
    function() {
        header('Content-Type: application/json');
        $dados = json_decode(file_get_contents('php://input'), true);

        $titulo = $dados['titulo'] ?? '';
        $modelo = $dados['modelo'] ?? null;
        $idioma = $dados['idioma'] ?? 'pt';
        $id = $dados['id'] ?? '';

        $controller = new EditModeloAnamnese();
        return new Response(200, $controller->updateModeloAnemnese($modelo, $titulo, $idioma, $id));
    }
]);

//ROTA CAD ANAMNESE
$obRouter->get('/anamnese',[
    function(){
        return new Response(200,CadAnamnese::getAnamnese());
    }
]);

//ROTA DELETE MODELO ANAMNESE
$obRouter->post('/deletemodeloanamnese',[
    function(){
        header('Content-Type: application/json');
        $id = $_POST['id'];
        return new Response(200,EditModeloAnamnese::deleteModeloAnemnese($id));
    }
]);

//ROTA UPDATE MODELO ANAMNESE
$obRouter->get('/editmodeloanamnese',[
    function(){
        $id = $_GET['id'] ?? null; 
        return new Response(200,EditModeloAnamnese::getModeloAnamnese($id));
    }
]);

//ROTA LIST CONSULTAS
$obRouter->get('/listaconsulta',[
    function(){
        return new Response(200,ListConsulta::getConsultas());
    }
]);

//ROTA CADASTRAR CONSULTA
$obRouter->post('/cadconsulta', [
    function() {
        $consultaController = new \App\Controller\Pages\ConsultasAgenda();

        $paciente = EncryptDecrypt::sanitize($_POST['paciente'] ?? '');
        $especialidade = EncryptDecrypt::sanitize($_POST['especialidade'] ?? '');
        $observacao = EncryptDecrypt::sanitize($_POST['observacao'] ?? '');
        $duracao = EncryptDecrypt::sanitize($_POST['duracao'] ?? '');
        $data = EncryptDecrypt::sanitize($_POST['data'] ?? '');
        $horario = EncryptDecrypt::sanitize($_POST['horarios'][0] ?? '');
        $idDentista = EncryptDecrypt::sanitize($_POST['idDentista'] ?? '');
        $convenio = EncryptDecrypt::sanitize($_POST['convenio'] ?? '');
        $id = EncryptDecrypt::sanitize($_POST['id'] ?? '');

        if(empty($data)){return new Response(200,json_encode(["success" => false, "message" => "Não foi informada a data da consulta."]));}
        if(empty($paciente)){return new Response(200,json_encode(["success" => false, "message" => "Não foi informado o paciente da consulta."]));}
        if(empty($horario)){return new Response(200,json_encode(["success" => false, "message" => "Não foi informado o horário da consulta."]));}
        if(empty($idDentista) || $idDentista == "all"){return new Response(200,json_encode(["success" => false, "message" => "Não foi informado o dentista que irá atender consulta."]));}

        if(!empty($id)) {
            return new Response(200, $consultaController->updateConsultaInfo($id, $convenio, $idDentista, $especialidade, $paciente, $observacao, $duracao, $data, $horario));
        } else {
            $tokenConfPresenca = bin2hex(random_bytes(16));
            return new Response(200, $consultaController->insertConsulta($convenio, $idDentista, $especialidade, $paciente, $observacao, $duracao, $data, $horario, $tokenConfPresenca));
        }
    }
]);

//ROTA ENCERRAR CONSULTA
$obRouter->post('/encerrarConsulta', [
    function() {
        $consultaController = new \App\Controller\Pages\ConsultasAgenda();
        $S3Controller = new \App\Controller\Pages\S3Controller();

        $id = EncryptDecrypt::sanitize($_POST['id'] ?? '');
        $procedimento = EncryptDecrypt::sanitize($_POST['procedimento'] ?? '');
        $observacao = EncryptDecrypt::sanitize($_POST['observacao'] ?? '');
        $dentesSelecionados = EncryptDecrypt::sanitize($_POST['dentesSelecionados'] ?? '');
        $tenancyid = EncryptDecrypt::sanitize($_POST['tenancyid'] ?? '');
        $foto1 = EncryptDecrypt::sanitize($_POST['foto1'] ?? '');
        $foto2 = EncryptDecrypt::sanitize($_POST['foto2'] ?? '');
        

        if(empty($id)){return new Response(200,json_encode(["success" => false, "message" => "Não foi informado ID da consulta."]));}
        if(empty($tenancyid)){return new Response(200,json_encode(["success" => false, "message" => "Não foi informado ID da clínica."]));}
        if(empty($procedimento)){return new Response(200,json_encode(["success" => false, "message" => "Não foi informado o procedimento da consulta."]));}


        // Loop para fotos 1 a 2
    for ($i = 1; $i <= 2; $i++) {
        $campo = 'foto' . $i;
        if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] === UPLOAD_ERR_OK && $_FILES[$campo]['size'] > 0) {
            $fotoTmp = $_FILES[$campo]['tmp_name'];
            $fotoNome = uniqid("foto{$i}_") . ".jpg"; // Nome único
        
            try {
                // Verifica se é uma imagem válida
                $imageInfo = getimagesize($fotoTmp);
                if ($imageInfo === false) {
                    echo json_encode(["success" => false, "message" => "O arquivo enviado no {$campo} não é uma imagem válida."]);
                    exit;
                }
            
                // Cria imagem a partir do tipo MIME
                switch ($imageInfo['mime']) {
                    case 'image/jpeg':
                        $image = imagecreatefromjpeg($fotoTmp);
                        break;
                    case 'image/png':
                        $image = imagecreatefrompng($fotoTmp);
                        break;
                    case 'image/gif':
                        $image = imagecreatefromgif($fotoTmp);
                        break;
                    default:
                        echo json_encode(["success" => false, "message" => "Formato não suportado em {$campo}."]);
                        exit;
                }
            
                // ROTACIONA PARA PAISAGEM (apenas JPEG suporta EXIF)
                if ($imageInfo['mime'] === 'image/jpeg' && function_exists('exif_read_data')) {
                    $exif = @exif_read_data($fotoTmp);
                    if (!empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 3:
                                $image = imagerotate($image, 180, 0);
                                break;
                            case 6:
                                $image = imagerotate($image, -90, 0);
                                break;
                            case 8:
                                $image = imagerotate($image, 90, 0);
                                break;
                        }
                    }
                }
            
                // Pega tamanho real da imagem após rotação
                $originalWidth = imagesx($image);
                $originalHeight = imagesy($image);
            
                // Se ainda estiver em retrato, gira para paisagem
                if ($originalHeight > $originalWidth) {
                    $image = imagerotate($image, -90, 0);
                    $originalWidth = imagesx($image);
                    $originalHeight = imagesy($image);
                }
            
                // Redimensiona proporcionalmente para largura 1280px
                $finalWidth = 1280;
                $finalHeight = intval(($originalHeight / $originalWidth) * $finalWidth);
            
                $finalImage = imagecreatetruecolor($finalWidth, $finalHeight);
                imagecopyresampled(
                    $finalImage, $image,
                    0, 0, 0, 0,
                    $finalWidth, $finalHeight,
                    $originalWidth, $originalHeight
                );
            
                

                // Salva imagem temporariamente
                $tempFilePath = __DIR__.'/tmp/' . $fotoNome;
                imagejpeg($finalImage, $tempFilePath, 60); // Qualidade 60
            
                // Libera memória
                imagedestroy($image);
                imagedestroy($finalImage);

                // Upload para S3 AWS
                $result = json_decode($S3Controller->uploadFile($tempFilePath, "consultas/clinica_$tenancyid/$id/$fotoNome"), true);

                if (isset($result['error'])) {
                    $response = json_encode(["success" => false, "message" => "Erro ao fazer upload das fotos."]);
                     return new Response(200, $response );
                }
            
                // Remove arquivo temporário
                unlink($tempFilePath);
                $nomesFotos[$campo] = $fotoNome;
            
            } catch (Exception $e) {
                $nomesFotos[$campo] = ''; 
            }
        } else {
            $nomesFotos[$campo] = ''; 
        }
    }


    for ($i = 1; $i <= 2; $i++) {
        if (!isset($nomesFotos["foto{$i}"])) {
            $nomesFotos["foto{$i}"] = '';
        }
    }
    
    $fotoTratada1 = $nomesFotos['foto1'];
    $fotoTratada2 = $nomesFotos['foto2'];
 

    






        $result = json_encode(["success" => true,"message" => "Consulta encerrada com sucesso."]);
        return new Response(200, $result );
        
    }
]);

//ROTA DELETAR CONSULTA
$obRouter->post('/deleteConsulta',[
    function(){
        header('Content-Type: application/json');

        $consultaController = new \App\Controller\Pages\ConsultasAgenda();

        $id = $_POST['id'];
        return new Response(200,$consultaController->deleteConsulta($id));
    }
]);

//ROTA AGENDA
$obRouter->get('/agenda',[
    function(){
        return new Response(200,Agenda::getAgenda());
    }
]);

//ROTA CADASTRO NOVO USUÁRIO
$obRouter->post('/cadUsuario', [
    function() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);  

        $usuariosController = new \App\Controller\Pages\Usuarios();

        $email = EncryptDecrypt::sanitize($data['email'] ?? '');
        $senha = $data['senha'] ?? '';
        $nome = EncryptDecrypt::sanitize($data['nome'] ?? '');
        $telefone = EncryptDecrypt::sanitize($data['telefone'] ?? '');

        if (strlen($senha) < 8 || strlen($senha) > 200) {
            return json_encode([
                'success' => false,
                'message' => 'A senha deve ter no mínimo 8 caracteres.'
            ]);
        }

        if (!preg_match('/[A-Z]/', $senha)) {
            return json_encode([
                'success' => false,
                'message' => 'A senha deve conter pelo menos 1 letra maiúscula.'
            ]);
        }

        if (!preg_match('/[!@#$%^&*(),.?":{}|<>_\-]/', $senha)) {
            return json_encode([
                'success' => false,
                'message' => 'A senha deve conter pelo menos 1 caractere especial.'
            ]);
        }

        $usuariosController->insertUsuarioAssinante($email, $senha, $nome, $telefone);
        return new Response(200, "Assinante Cadastrado");

    }
]);

//ROTA PAGINA LINK ATIVACAO NOVO USUARIO
$obRouter->get('/assinanteLinkAtivacao', [ 
    function() {
         return new Response(200,LoginAtivacao::getLoginAtivacaoPage());
    }
]);

//ROTA PAGINA ALTERAR SENHA (LINK)
$obRouter->get('/sendEmailAlterarSenha', [ 
    function() {
         return new Response(200,SenhaAlterarSend::getLoginAtivacaoPage());
    }
]);

//ROTA PAGINA ALTERAR SENHA
$obRouter->get('/redefinicaoSenha', [
    function() {
        $id = $_GET['id'] ?? '';
        return new Response(200,RedefinirSenha::getRedefinirSenhaPage($id));
    }
]);

//ROTA ALTERAR SENHA SENHA EFETIVAR
$obRouter->post('/redefinirSenhaCheck', [
    function() {

        $usuariosController = new \App\Controller\Pages\Usuarios();
        $KEY = $_ENV['ENV_SECRET_KEY'] ?? getenv('ENV_SECRET_KEY') ?? '';

        $input = json_decode(file_get_contents('php://input'), true);
        $userid = $input['userid'] ?? '';
        $password = $input['password'] ?? '';

        if(empty($userid) || empty($password)) {
            $result = json_encode(["success" => false, "message" => "Dados inválidos"]);
            return new \App\Http\Response(200, $result);
        }

        if (strlen($password) < 8 || strlen($password) > 200) {
            return json_encode([
                'success' => false,
                'message' => 'A senha deve ter no mínimo 8 caracteres.'
            ]);
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return json_encode([
                'success' => false,
                'message' => 'A senha deve conter pelo menos 1 letra maiúscula.'
            ]);
        }

        if (!preg_match('/[!@#$%^&*(),.?":{}|<>_\-]/', $password)) {
            return json_encode([
                'success' => false,
                'message' => 'A senha deve conter pelo menos 1 caractere especial.'
            ]);
        }

        $userid = EncryptDecrypt::decrypt_id_token($userid, $KEY);

        if ($userid === false || !is_numeric($userid)) {
            $result = json_encode(["success" => false, "message" => "ID inválido ou corrompido."]);
            return new \App\Http\Response(200, $result);
        }

        $result = json_encode($usuariosController->updateSenhaAssinante($userid, $password));
        return new \App\Http\Response(200, $result);
    }
]);

//ROTA PROCESSAR LINK ATIVACAO NOVO USUARIO
$obRouter->post('/enviarLinkAlterarSenha', [
    function() {
        
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);  

        $usuariosController = new \App\Controller\Pages\Usuarios();
        $email = EncryptDecrypt::sanitize($data['email'] ?? '');
        $result = json_encode($usuariosController->sendEmailAlterarSenha($email));
        return new \App\Http\Response(200, $result);

    }
]);

//ROTA PROCESSAR LINK ATIVACAO NOVO USUARIO
$obRouter->post('/enviarAssinanteLinkAtivacao', [
    function() {
        
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);  

        $usuariosController = new \App\Controller\Pages\Usuarios();
        $email = EncryptDecrypt::sanitize($data['email'] ?? '');
        $result = json_encode($usuariosController->sendEmailAtivacao($email));
        return new \App\Http\Response(200, $result);

    }
]);

//ROTA LOGIN RENDER TELA
$obRouter->get('/login',[
    function(){
        return new Response(200,Login::getLogin());
    }
]);

//ROTA PAGINA LINK ATIVACAO NOVO USUARIO CHECAGEM
$obRouter->get('/assinanteLinkAtivacaoCheck', [
    function() {
        $id = $_GET['id'] ?? '';
        return new Response(200,LoginAtivacaoCheck::getLoginAtivacaoCheckPage($id));
    }
]);

//ROTA PAGINA VERIFICAR SE EMAIL/USUARIO JA EXISTE ANTES (NOVO ASSINANTE)
$obRouter->get('/assinanteLinkAtivacaoEmailExistisCheck', [
    function() {
        $usuariosController = new \App\Controller\Pages\Usuarios();
        $email = EncryptDecrypt::sanitize($_GET['email'] ?? '');
        $result = json_encode($usuariosController->checkEmailUsuarioAssinanteExists($email));
        return new \App\Http\Response(200, $result);
    }
]);

//ROTA LOGOFF
$obRouter->get('/logoff',[
    function(){
        return new Response(200,Login::logoffUser());
    }
]);

// Rota POST para validar o login
$obRouter->post('/logincheck', [
    function() {

        $secret = $_ENV['CLOUDFLARE_SECRET_KEY'] ?? getenv('CLOUDFLARE_SECRET_KEY') ?? '';

        $loginController = new \App\Controller\Pages\Login();

        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $codigo = $input['codigo'] ?? '';
        $token = $input['cf-turnstile-response'] ?? '';

        $response = file_get_contents("https://challenges.cloudflare.com/turnstile/v0/siteverify", false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query([
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ]),
            ]
        ]));
        
        $result = json_decode($response, true);

        if (!$result['success']) {
            $result = json_encode(["success" => false,"message" => "Falha na validação do captcha."]);
            return new \App\Http\Response(200, $result);
        }

        $result = $loginController->validateUser($email, $password, $codigo);
        return new \App\Http\Response(200, $result);
    }
]);

// Rota POST update agenda
$obRouter->post('/updateagenda', [
    function() {
        $consultaController = new \App\Controller\Pages\ConsultasAgenda();
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? '';
        $start = $input['start'] ?? '';
        $end = $input['end'] ?? '';

        if(empty($id) || empty($start)) {
            return new \App\Http\Response(400, $id);
        }

        $result = $consultaController->updateConsulta($id, $start, $end);
        return new \App\Http\Response(200, $result);
    }
]);

//ROTA BUSCAR HORARIOS DISPONIVEIS AGENDA
$obRouter->post('/horariosdisp', [
    function() {
        $consultaController = new \App\Controller\Pages\ConsultasAgenda();

        $data = $_POST['data'] ?? '';
        $duracao = $_POST['duracao'] ?? '';
        $iddentista = $_POST['iddentista'] ?? '';

        if(empty($iddentista)) {
            return new \App\Http\Response(200, "");
        }

        $result = json_encode($consultaController->getHorariosDisp($data, $duracao, $iddentista)); 
        
        //return new \App\Http\Response(200, json_encode($result), 'application/json');
        return new \App\Http\Response(200, $result);
    }
]);

//ROTA STREAM DE EVENTOS (CONFIRMAÇÃO DE CONSULTAS, ETC...)
$obRouter->get('/streamevents', [
    function() {
        $ultimoId = isset($_GET['ultimoId']) ? (int)$_GET['ultimoId'] : 0;
        $eventos = (new \App\Controller\Pages\StreamEvents())->getNovosEventos($ultimoId);

        header('Content-Type: application/json');
        echo json_encode($eventos);
        exit;
    }
]);

//ROTA S3 DOWNLOAD
$obRouter->get('/s3download', [
    function() {

        $input = json_decode(file_get_contents('php://input'), true);
        $file = $input['file'] ?? '';

        $controller = new S3Controller();
        return new Response(200, $controller->getDownloadLink($file));
    }
]);

//ROTA S3 UPLOAD
$obRouter->get('/s3upload', [
    function() {

        $input = json_decode(file_get_contents('php://input'), true);
        $file = $input['file'] ?? '';

        $controller = new S3Controller();
        return new Response(200, $controller->uploadFile($file));
    }
]);

//ROTA ANAMNESE DIRETA SEM CONTROLLER
$obRouter->get('/anamnese', function() {
    $_GET['tid'] = $_GET['tid'] ?? '';
    $_GET['id']  = $_GET['id'] ?? ''; 
    $_GET['lang']  = $_GET['lang'] ?? '';

    ob_start();
    include __DIR__ . '/public/external_vw/anm_anamnese.php';
    $html = ob_get_clean();

    return new \App\Http\Response(200, $html);
});

//ROTA ANAMNESE PROCESSA INPUT
$obRouter->post('/anamneseProc', [
    function() {
        $data = json_decode(file_get_contents('php://input'), true);
        $key = $_ENV['ENV_SECRET_KEY'] ?? getenv('ENV_SECRET_KEY') ?? '';

        $paciente_id = $data['paciente_id'] ?? null;
        $tenancy_id  = $data['tenancy_id'] ?? null;
        $modelo_id   = $data['modelo_id'] ?? null;
        $respostas   = $data['respostas'] ?? [];
        $modelo_id   = $data['modelo_id'] ?? null;
        $token   = $data['csrf_token'] ?? null;

        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('Ação não autorizada. Token CSRF inválido.');
        }

        //decript ids
        $tid = EncryptDecrypt::decrypt_id_token($tenancy_id, $key);
        $id = EncryptDecrypt::decrypt_id_token($paciente_id, $key);

        $controller = new CadAnamnese();
        return new Response(200, $controller->insertAnamneserespostas($tid, $id, $modelo_id, $respostas));
    }
]);

//ROTA ANAMNESE VERIFICAR AUTENTICIDADE
$obRouter->get('/verificar', function() {
    $_GET['c'] = $_GET['c'] ?? '';

    ob_start();
    include __DIR__ . '/public/external_vw/anm_verificar.php';
    $html = ob_get_clean();

    return new \App\Http\Response(200, $html);
});

//ROTA ANAMNESE AGRADECIMENTO
$obRouter->get('/anamneseFinal', function() {
    $_GET['target'] = $_GET['target'] ?? '';
    ob_start();
    include __DIR__ . '/public/external_vw/anm_anamnese_final.php';
    $html = ob_get_clean();

    return new \App\Http\Response(200, $html);
});

//ROTA LOG INFO RENDER TELA
$obRouter->get('/listLogInfo',[
    function(){
        return new Response(200,ListLog::getLog());
    }
]);

//IMPRIME RESPONSE NA PÁGINA
$obRouter->run()
            ->sendResponse();






