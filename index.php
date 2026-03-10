<?php

$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

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
    'prenotazione' => [
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
        $id ? $c->getById($id) : $c->getAll();
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($resource === 'login') {
            $c->login($data);
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