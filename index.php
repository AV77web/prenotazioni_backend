<?php

// CORS: in sviluppo accetta qualsiasi porta di localhost (5173, 5175, 5177, …); in produzione usa l’origin configurato
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed = (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin))
    ? $origin
    : 'http://localhost:5179';
header('Access-Control-Allow-Origin: ' . $allowed);
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No Content
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// PATH_INFO è impostato da Apache (RewriteRule index.php/$1) o dal router PHP; altrimenti usa REQUEST_URI (Docker/Apache)
$pathInfo = $_SERVER['PATH_INFO'] ?? '';
if ($pathInfo === '' && !empty($_SERVER['REQUEST_URI'])) {
    $pathInfo = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $pathInfo = $pathInfo ?: '/';
}
// Rimuovi sempre il prefisso /server (Apache passa PATH_INFO = /server/clienti, non solo REQUEST_URI)
$base = '/server';
if ($base !== '' && strpos($pathInfo, $base) === 0) {
    $pathInfo = substr($pathInfo, strlen($base));
    $pathInfo = $pathInfo ?: '/';
}
$uri      = explode('/', trim($pathInfo, '/'));
$resource = $uri[0] ?? null;
$id       = $uri[1] ?? null;

// Mappa risorsa → file controller e classe
$routes = [
    'clienti' => [
        'file'  => 'controller/ClientiController.php',
        'class' => 'ClientiController',
    ],
    'campi'   => [
        'file'  => 'controller/CampiController.php',
        'class' => 'CampiController',
    ],
    'operatore' => [
        'file'  => 'controller/OperatoriController.php',
        'class' => 'OperatoreController',
    ],
    'login' => [
        'file'  => 'controller/LoginController.php',
        'class' => 'LoginController',
    ],
    'auth' => [
        'file'  => 'controller/AuthController.php',
        'class' => 'AuthController',
    ],
    'prenotazione' => [
        'file'  => 'controller/PrenotazioniController.php',
        'class' => 'PrenotazioniController',
    ],
    // Endpoint pubblico per verifica prenotazione tramite codice
    'verifica-prenotazione' => [
        'file'  => 'controller/PrenotazioniController.php',
        'class' => 'PrenotazioniController',
    ],
];

// Se la risorsa non è gestita, 404
if (!isset($routes[$resource])) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Risorsa non trovata']);
    exit;
}

// Carica il controller giusto
require_once $routes[$resource]['file'];
$controllerClass = $routes[$resource]['class'];
$c = new $controllerClass();

// Gestione unica dei metodi CRUD
switch ($method) {
    case 'GET':
        if ($resource === 'verifica-prenotazione') {
            $codice = $_GET['codice'] ?? '';
            $c->verificaPrenotazionePerCodice($codice);
        } else {
            $id ? $c->getById($id) : $c->getAll();
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($resource === 'login') {
            $c->login($data);
        } elseif ($resource === 'auth' && $id === 'logout') {
            $c->logout();
        } else {
            $c->create($data);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $c->update($id, $data);
        break;

    case 'DELETE':
        $c->delete($id);
        break;

    default:
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Metodo non consentito']);
        break;
}

?>