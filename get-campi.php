<?php
/** Script che ritona l'elenco dei campi
 * 
 * 
 */
require "include/connect-db.php";

$result = mysqli_query($db,"
    SELECT 
        CampoID, 
        Nome, 
        Tipo, 
        CASE WHEN Coperto = 1 THEN 'SI' ELSE 'NO' END AS Coperto,
        PrezzoOrario, 
        CASE WHEN Attivo = 1 THEN 'SI' ELSE 'NO' END AS Attivo
    FROM CampoSportivo
    ORDER BY CampoID ASC
");

$output = [];
while ($row = mysqli_fetch_assoc($result)){
    $output[] = $row;
};

echo json_encode($output);
?>