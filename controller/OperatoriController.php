<?php
// Classe controller per CRUD Tabella Operatore

class OperatoreController {

    private $conn;

    public function __construct() {
        require __DIR__ . '/../include/connect-db.php';
        $this->conn = $db;
    }

    public function getAll() {

        $sql = "SELECT * FROM Operatore";
        $result = $this->conn->query($sql);

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode($data);
    }

    public function getById($id) {

        $stmt = $this->conn->prepare(
            "SELECT * FROM Operatore WHERE OperatoreId=?"
        );

        $stmt->bind_param("i",$id);
        $stmt->execute();

        $result = $stmt->get_result();

        echo json_encode($result->fetch_assoc());
    }

    public function create($data) {

        $passwordHash = password_hash($data['Password'], PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare(
            "INSERT INTO Operatore (Nome,Cognome,Email,Password,Admin)
             VALUES (?,?,?,?,?)"
        );

        $stmt->bind_param(
            "ssssi",
            $data['Nome'],
            $data['Cognome'],
            $data['Email'],
            $passwordHash,
            $data['Admin']
        );

        $stmt->execute();

        echo json_encode(["OperatoreId"=>$this->conn->insert_id]);
    }

    public function update($id,$data) {

        $updatePassword = isset($data['Password']) && $data['Password'] !== '';

        if ($updatePassword) {
            $passwordHash = password_hash($data['Password'], PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare(
                "UPDATE Operatore
                 SET Nome=?, Cognome=?, Email=?, Password=?, Admin=?
                 WHERE OperatoreId=?"
            );
            $stmt->bind_param(
                "sssssi",
                $data['Nome'],
                $data['Cognome'],
                $data['Email'],
                $passwordHash,
                $data['Admin'],
                $id
            );
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE Operatore
                 SET Nome=?, Cognome=?, Email=?, Admin=?
                 WHERE OperatoreId=?"
            );
            $stmt->bind_param(
                "sssi",
                $data['Nome'],
                $data['Cognome'],
                $data['Email'],
                $data['Admin'],
                $id
            );
        }

        $stmt->execute();

        echo json_encode(["updated"=>true]);
    }

    public function delete($id) {

        $stmt = $this->conn->prepare(
            "DELETE FROM Operatore WHERE OperatoreId=?"
        );

        $stmt->bind_param("i",$id);
        $stmt->execute();

        echo json_encode(["deleted"=>true]);
    }
}

?>