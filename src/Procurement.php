<?php
class Procurement {
    private $conn;
    private $table_name = "procurement";

    public $id;
    public $medicine_name;
    public $quantity;
    public $status;
    public $request_date;

    public function __construct($db){
        $this->conn = $db;
    }

    function create(){
        $query = "INSERT INTO " . $this->table_name . " SET medicine_name=:medicine_name, quantity=:quantity, status=:status";
        $stmt = $this->conn->prepare($query);

        $this->medicine_name=htmlspecialchars(strip_tags($this->medicine_name));
        $this->quantity=htmlspecialchars(strip_tags($this->quantity));
        $this->status=htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(":medicine_name", $this->medicine_name);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function read(){
        $query = "SELECT id, medicine_name, quantity, status, request_date FROM " . $this->table_name . " ORDER BY request_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function readOne(){
        $query = "SELECT id, medicine_name, quantity, status, request_date FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->medicine_name = $row['medicine_name'];
        $this->quantity = $row['quantity'];
        $this->status = $row['status'];
        $this->request_date = $row['request_date'];
    }

    function update(){
        $query = "UPDATE " . $this->table_name . " SET medicine_name = :medicine_name, quantity = :quantity, status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->medicine_name=htmlspecialchars(strip_tags($this->medicine_name));
        $this->quantity=htmlspecialchars(strip_tags($this->quantity));
        $this->status=htmlspecialchars(strip_tags($this->status));
        $this->id=htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':medicine_name', $this->medicine_name);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function delete(){
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
?>