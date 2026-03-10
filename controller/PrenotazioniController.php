<?php
// Classe controller per CRUD Tabella Prenotazioni

class PrenotazioniController {

    private $conn;

    public function __construct() {
        require __DIR__ . '/../include/connect-db.php';
        $this->conn = $db;
    }

    public function getAll() {

        $sql="SELECT * FROM Prenotazione";
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
    
}
?>