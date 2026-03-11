<?php
/**
 * Controller per verifica sessione e logout.
 * GET /auth/me  → restituisce utente corrente se autenticato (cookie sessione).
 * POST /auth/logout → distrugge la sessione (gestito da index.php che chiama logout()).
 */

class AuthController {

    public function __construct() {
        // Nessuna connessione DB necessaria per auth/me e logout
    }

    public function getAll() {
        $this->methodNotAllowed();
    }

    /**
     * GET /auth/me → ritorna { authenticated: true, user: {...} } o 401
     */
    public function getById($id) {
        header('Content-Type: application/json');

        if ($id !== 'me') {
            $this->methodNotAllowed();
            return;
        }

        $this->ensureSession();

        if (empty($_SESSION['operatore'])) {
            http_response_code(401);
            echo json_encode(['authenticated' => false, 'user' => null]);
            return;
        }

        $op = $_SESSION['operatore'];
        $user = [
            'nome'       => $op['Nome'],
            'cognome'    => $op['Cognome'],
            'email'      => $op['Email'],
            'operatoreId' => (int) $op['OperatoreId'],
            'admin'      => (int) $op['Admin'],
        ];

        echo json_encode([
            'authenticated' => true,
            'user'          => $user,
        ]);
    }

    public function create($data) {
        $this->methodNotAllowed();
    }

    public function update($id, $data) {
        $this->methodNotAllowed();
    }

    /**
     * POST /auth/logout → distrugge sessione (chiamato da index.php quando $id === 'logout')
     */
    public function logout() {
        $this->ensureSession();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Logout effettuato']);
    }

    public function delete($id) {
        $this->methodNotAllowed();
    }

    private function ensureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $isSecure,
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
        }
    }

    private function methodNotAllowed() {
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Metodo non consentito']);
    }
}
