<?php
require __DIR__.'/vendor/autoload.php';

$dotenvPath = __DIR__;

if (file_exists($dotenvPath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->load();
}

// Tempo de vida da sessão e do JWT (180 dias)
$lifetime = 60 * 60 * 24 * 180;

session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    //'domain' => 'meluza.com.br',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

ini_set('session.gc_maxlifetime', $lifetime);
ini_set('session.cookie_lifetime', $lifetime);

session_start();

/**
 * nome do arquivo .php deve ser o mesmo nome da classe
 * por isso o \Home e na pasta do controler existe um Home.php. Isso é padrão Composer
 */

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
use App\Core\Language;

// Inicia sistema de idiomas
Language::init();

define('URL','https://cliente.meluza.com.br');
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

        if(empty($data)){return new Response(200,json_encode(["success" => false, "message" => "Não foi informada a data da consulta."]));}
        if(empty($paciente)){return new Response(200,json_encode(["success" => false, "message" => "Não foi informado o paciente da consulta."]));}
        if(empty($horario)){return new Response(200,json_encode(["success" => false, "message" => "Não foi informado o horário da consulta."]));}
        if(empty($idDentista)){return new Response(200,json_encode(["success" => false, "message" => "Não foi informado o dentista que irá atender consulta."]));}

        return new Response(200, $consultaController->insertConsulta($convenio, $idDentista, $especialidade, $paciente, $observacao, $duracao, $data, $horario));
    }
]);

//ROTA AGENDA
$obRouter->get('/agenda',[
    function(){
        return new Response(200,Agenda::getAgenda());
    }
]);

//ROTA LOGIN RENDER TELA
$obRouter->get('/login',[
    function(){
        return new Response(200,Login::getLogin());
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
        $loginController = new \App\Controller\Pages\Login();

        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $codigo = $input['codigo'] ?? '';

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

        $result = json_encode($consultaController->getHorariosDisp($data, $duracao)); 
        
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

//IMPRIME RESPONSE NA PÁGINA
$obRouter->run()
            ->sendResponse();






