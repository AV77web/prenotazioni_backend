<?php
/* Script che ritorna l'elenco dei clienti
* 
* 
*/
require "include/connect-db.php";

$result = mysqli_query($db,"
    SELECT ClienteId, Nome, Cognome, Email, Telefono, Note
    From Cliente
    order by cognome, nome asc
");

$output = [];

while ($row = mysqli_fetch_assoc($result)) {
    $output[] = $row;
};

echo json_encode($output);
?>