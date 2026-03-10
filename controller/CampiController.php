<?php
// Classe Controller per CRUD tabella Campi

class CampiController {

    private $conn;

    public function __construct(){
        require __DIR__.'/../include/connect-db.php';
        $this->conn = $db;
    }

    public function getAll() {
        
        $sql = "SELECT * FROM CampoSportivo";
        $result = $this->conn->query($sql);

        $data = [];

        while ($row = $result->fetch_assoc())
        {
            $data[] = $row;
        }
        
    echo json_encode($data);
    }


    public function getById($id){

        $stmt = $this->conn->prepare(
            "SELECT * FROM CampoSportivo WHERE CampoID=?"
        );

        $stmt->bind_param("i",$id);
        $stmt->execute();
        $result = $stmt->get_result();
        echo json_encode($result->fetch_assoc());
    }

    public function create($data){
        
        $stmt = $this->conn->prepare(
            "INSERT INTO CampoSportivo (Nome, Tipo, Coperto, PrezzoOrario, Attivo)
            VAlUES (?,?,?,?,?)"
        );

        $stmt->bind_param(
            "sssss",
            $data["Nome"],
            $data["Tipo"],
            $data["Coperto"],
            $data["PrezzoOrario"],
            $data["Attivo"]
        );

        $stmt->execute();

        echo json_encode(["CampoID"=>$this->conn->insert_id]);

    }

    public function update($id,$data){

        $stmt = $this->conn->prepare(
            "UPDATE CampoSportivo
             SET Nome=?, Tipo=?, Coperto=?, PrezzoOrario=?, Attivo=?
             WHERE CampoID=?"
        );

        $stmt->bind_param(
            "sssssi",
            $data["Nome"],
            $data["Tipo"],
            $data["Coperto"],
            $data["PrezzoOrario"],
            $data["Attivo"],
            $id
        );

        $stmt->execute();

        echo json_encode(["update"=>true]);

    }

    public function delete($id){

        $stmt = $this->conn->prepare(
            "DELETE FROM CampoSportivo WHERE CampoID=?"
        );

        $stmt->bind_param("i",$id);
        $stmt->execute();

        echo json_encode(["deleted"=>true]);
    }
}

?>