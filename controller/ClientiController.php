<?php
// Classe controller per CRUD Tabella Clienti

class ClientiController {

    private $conn;

    public function __construct() {
        require __DIR__ . '/../include/connect-db.php';
        $this->conn = $db;
    }

    public function getAll() {

        $sql = "SELECT * FROM Cliente";
        $result = $this->conn->query($sql);

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

     echo json_encode($data);
    }

public function getById($id) {

    $stmt = $this->conn->prepare(
        "SELECT * FROM Cliente WHERE ClienteId=?"
    );

    $stmt->bind_param("i",$id);
    $stmt->execute();

    $result = $stmt->get_result();

    echo json_encode($result->fetch_assoc());
}

public function create($data) {

    $stmt = $this->conn->prepare(
        "INSERT INTO Cliente (Nome,Cognome,Email,Telefono,Note)
         VALUES (?,?,?,?,?)"
    );

    $stmt->bind_param(
        "sssss",
        $data['Nome'],
        $data['Cognome'],
        $data['Email'],
        $data['Telefono'],
        $data['Note']
    );

    $stmt->execute();

    echo json_encode(["ClienteId"=>$this->conn->insert_id]);
}

public function update($id,$data) {

    $stmt = $this->conn->prepare(
        "UPDATE Cliente
         SET Nome=?, Cognome=?, Email=?, Telefono=?, Note=?
         WHERE ClienteId=?"
    );

    $stmt->bind_param(
        "sssssi",
        $data['Nome'],
        $data['Cognome'],
        $data['Email'],
        $data['Telefono'],
        $data['Note'],
        $id
    );

    $stmt->execute();

    echo json_encode(["updated"=>true]);
}

public function delete($id) {

    $stmt = $this->conn->prepare(
        "DELETE FROM Cliente WHERE ClienteId=?"
    );

    $stmt->bind_param("i",$id);
    $stmt->execute();

    echo json_encode(["deleted"=>true]);
}
}

?>