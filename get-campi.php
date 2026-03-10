<?php
/** Script che ritona l'elenco dei campi
 * 
 * 
 */
require "include/connect-db.php";

$result = mysqli_query($db,"
    SELECT CampoID, Nome, Tipo, Coperto, PrezzoOrario, Attivo
    From CampoSportivo
    order by CampoID asc
");

$output = [];
while ($row = mysqli_fetch_assoc($result)){
    $output[] = $row;
};

echo json_encode($output);
?>