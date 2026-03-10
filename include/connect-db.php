<?php
//=============================================
// File: conect-db.php
// Script per creare la connessione al database
// @author "villari.andra@libero.it"
// version: "1.0.0 2025-01-24"
//==============================================

mysqli_report(MYSQLI_REPORT_OFF);

$isLocal = (
    $_SERVER["SERVER_NAME"] == "127.0.0.1" ||
    $_SERVER["SERVER_NAME"] == "localhost" ||
    $_SERVER["SERVER_PORT"] == "8081" ||
    $_SERVER["SERVER_PORT"] == "8080"
    );

if (!$isLocal) {
    $db = @mysqli_connect(
        "localhost",
        "nome_utente_remote",
        "nome_database_remoto",
        "nome_password_remoto"
    );
} else {
    $db = @mysqli_connect(
        "mysql", // nome del serivzio mysql di docker
        "root", // utente mysql
        "root", // password mysql
        "prenotazioni"  // database docker   
    );
}

if (!$db) {
    if(ob_get_level() > 0 ) {
        ob_end_clean();
    }
    http_response_code(500);
    header('Content-Type: application/json');
    $error_msg = mysqli_connect_error();
    if (empty($error_msg)) {
        $error_msg =  "Impossibile connettersi al database MySQL. Verificare che il servizio sia avviato";
    }
    echo json_encode(["error" => "Errore di connessione: ".$error_msg]);
    exit;
} else {
    // echo "Connessione al database MySQL stabilita"; // Rimosso per non rompere il JSON
}

// comunixazione in utf8
mysqli_set_charset($db, "utf8");
?>