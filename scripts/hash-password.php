<?php
/**
 * Script per generare hash di password.
 *
 * IN PRODUZIONE (consigliato) – via SSH:
 *   ssh user@tuoserver
 *   cd /var/www/html/server   # oppure il path della tua app
 *   php scripts/hash-password.php "LaTuaPassword"
 *   # Copia l'hash e fai UPDATE Operatore SET Password='...' nel DB.
 *
 * Uso locale: php scripts/hash-password.php "LaTuaPassword"
 */

$password = $argv[1] ?? null;

if ($password === null || $password === '') {
    fwrite(STDERR, "Uso: php hash-password.php \"password\"\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

if ($hash === false) {
    fwrite(STDERR, "Errore nella generazione dell'hash.\n");
    exit(1);
}

echo $hash . "\n";
