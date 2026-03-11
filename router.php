<?php
/**
 * Router per il server PHP integrato (php -S).
 * Inoltra ogni richiesta a index.php così che CORS e routing funzionino per tutti i path
 * (es. /auth/me, /login, /clienti) invece di restituire 404.
 */

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$_SERVER['PATH_INFO'] = $path;

require __DIR__ . '/index.php';
