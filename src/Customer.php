<?php
class Customer {
    private $conn;
    private $table_name = "customers";

    public $id;
    public $name;
    public $phone;
    public $email;
    public $created_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function create(){
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, phone=:phone, email=:email";
        $stmt = $this->conn->prepare($query);

        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->phone=htmlspecialchars(strip_tags($this->phone));
        $this->email=htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(":name", $this->name);
        
        // Bind parameters, handle empty values as NULL or empty string
        $phone_val = !empty($this->phone) ? $this->phone : "";
        $email_val = !empty($this->email) ? $this->email : "";

        $stmt->bindParam(":phone", $phone_val);
        $stmt->bindParam(":email", $email_val);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function read(){
        $query = "SELECT id, name, phone, email, created_at FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function readOne(){
        $query = "SELECT id, name, phone, email, created_at FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $row['name'];
        $this->phone = $row['phone'];
        $this->email = $row['email'];
        $this->created_at = $row['created_at'];
    }

    function update(){
        $query = "UPDATE " . $this->table_name . " SET name = :name, phone = :phone, email = :email WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->phone=htmlspecialchars(strip_tags($this->phone));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->id=htmlspecialchars(strip_tags($this->id));

        $phone_val = !empty($this->phone) ? $this->phone : "";
        $email_val = !empty($this->email) ? $this->email : "";

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':phone', $phone_val);
        $stmt->bindParam(':email', $email_val);
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

    function search($keywords){
        $query = "SELECT id, name, phone, email, created_at FROM " . $this->table_name . " WHERE name LIKE ? OR phone LIKE ? OR email LIKE ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);

        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->execute();
        return $stmt;
    }

    // New search method for POS autocomplete
    function searchCustomers($term){
        $query = "SELECT id, name, phone FROM " . $this->table_name . " WHERE name LIKE ? OR phone LIKE ? LIMIT 10";
        $stmt = $this->conn->prepare($query);

        $term = htmlspecialchars(strip_tags($term));
        $term = "%{$term}%";

        $stmt->bindParam(1, $term);
        $stmt->bindParam(2, $term);
        $stmt->execute();
        return $stmt;
    }

}
?>