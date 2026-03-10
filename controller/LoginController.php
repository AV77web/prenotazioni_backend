<?php
/**
 * Controller per login operatore.
 * POST body: { "Email": "...", "Password": "..." }
 * In caso di successo: sessione PHP + cookie HttpOnly, risposta JSON con operatore (senza password).
 */

class LoginController {

    private $conn;

    public function __construct() {
        require __DIR__ . '/../include/connect-db.php';
        $this->conn = $db;
    }

    public function getAll() {
        $this->methodNotAllowed();
    }

    public function getById($id) {
        $this->methodNotAllowed();
    }

    public function create($data) {
        $this->login($data);
    }

    public function update($id, $data) {
        $this->methodNotAllowed();
    }

    public function delete($id) {
        $this->methodNotAllowed();
    }

    /**
     * Verifica credenziali, crea sessione e cookie HttpOnly, restituisce operatore (senza password).
     */
    public function login($data) {

        header('Content-Type: application/json');

        $email    = $data['Email'] ?? '';
        $password = $data['Password'] ?? '';

        if ($email === '' || $password === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Email e password obbligatorie']);
            return;
        }

        $stmt = $this->conn->prepare(
            "SELECT OperatoreId, Nome, Cognome, Email, Password, Admin FROM Operatore WHERE Email = ?"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $operatore = $result->fetch_assoc();

        if (!$operatore || !password_verify($password, $operatore['Password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Credenziali non valide']);
            return;
        }

        // Cookie sessione HttpOnly (e Secure in produzione)
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

        $_SESSION['operatore_id'] = (int) $operatore['OperatoreId'];
        $_SESSION['operatore']   = [
            'OperatoreId' => (int) $operatore['OperatoreId'],
            'Nome'        => $operatore['Nome'],
            'Cognome'     => $operatore['Cognome'],
            'Email'       => $operatore['Email'],
            'Admin'       => (int) $operatore['Admin'],
        ];

        unset($operatore['Password']);
        echo json_encode([
            'ok'        => true,
            'operatore' => $_SESSION['operatore'],
        ]);
    }

    private function methodNotAllowed() {
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Metodo non consentito']);
    }
}
