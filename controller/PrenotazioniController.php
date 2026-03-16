<?php
// Classe controller per CRUD Tabella Prenotazioni

class PrenotazioniController {

    private $conn;

    public function __construct() {
        require __DIR__ . '/../include/connect-db.php';
        $this->conn = $db;
    }

    public function getAll() {

        $sql = "SELECT 
                    p.Data,
                    p.OraInizio,
                    p.OraFine,
                    p.Stato,
                    p.CodicePrenotazione,
                    cl.Nome,
                    cl.Cognome,
                    cp.Nome AS NomeCampo
                FROM Prenotazione p
                JOIN Cliente cl ON cl.ClienteID = p.ClienteID
                JOIN CampoSportivo cp ON cp.CampoID = p.CampoID";

        $result = $this->conn->query($sql);

        $data = [];

        while ($row = $result->fetch_assoc()){
            $data[] = $row;
        }

        echo json_encode($data);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM Prenotazione WHERE PrenotazioneId=?"
        );

        $stmt->bind_param("i",$id);
        $stmt->execute();

        $result = $stmt->get_result();

        echo json_encode($result->fetch_assoc());

    }
    
    public function create($data) {

        $stmt = $this->conn->prepare(
            "INSERT INTO Prenotazione (Data, OraInizio, OraFine, Stato, CodicePrenotazione, ClienteID, CampoID)
             VALUES (?,?,?,?,?,?,?)"
        );
    
        $stmt->bind_param(
            "sssssss",
            $data['Data'],
            $data['OraInizio'],
            $data['OraFine'],
            $data['Stato'],
            $data['CodicePrenotazione'],
            $data['ClienteID'],
            $data['CampoID']
        );
    
        $stmt->execute();
    
        echo json_encode(["PrenotazioneId" => $this->conn->insert_id]);
    }

    public function update($id,$data) {

        $stmt = $this->conn->prepare(
            "UPDATE Prenotazione
             SET Data=?, OraInizio=?, OraFine=?, Stato=?, CodicePrenotazione=?, ClienteID=?, CampoID=?
             WHERE PrenotazioneID=?"
        );
    
        $stmt->bind_param(
            "ssssssii",
            $data['Data'],
            $data['OraInizio'],
            $data['OraFine'],
            $data['Stato'],
            $data['CodicePrenotazione'],
            $data['ClienteID'],
            $data['CampoID'],
            $id
        );
    
        $stmt->execute();
    
        echo json_encode(["updated" => true]);
    }

    public function delete($id) {

        $stmt = $this->conn->prepare(
            "DELETE FROM Prenotazione WHERE PrenotazioneId=?"
        );
    
        $stmt->bind_param("i",$id);
        $stmt->execute();
    
        echo json_encode(["deleted"=>true]);
    }

    /**
     * Endpoint pubblico per verificare una prenotazione tramite codice.
     * GET /verifica-prenotazione?codice=XXXXX
     * Restituisce: data, orario, campo, stato prenotazione.
     */
    public function verificaPrenotazionePerCodice(string $codice): void
    {
        header('Content-Type: application/json');

        if ($codice === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Codice prenotazione mancante']);
            return;
        }

        // Adatta i nomi di tabella/campi se diversi nel tuo schema
        $sql = "SELECT 
                    p.Data      AS data,
                    p.OraInizio AS orario,
                    c.NomeCampo AS campo,
                    p.Stato     AS stato
                FROM Prenotazione p
                JOIN Campo c ON p.CampoID = c.CampoID
                WHERE p.CodicePrenotazione = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $codice);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            http_response_code(404);
            echo json_encode(['error' => 'Prenotazione non trovata']);
            return;
        }

        echo json_encode($row);
    }
    
}
?>