<?php
class Inventory
{
    private $conn;
    private $table_name = "medicines";

    public $id;
    public $name;
    public $code;
    public $quantity;
    public $price;
    public $expiry_date;
    public $location_code;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, code=:code, quantity=:quantity, price=:price, expiry_date=:expiry_date, location_code=:location_code";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->expiry_date = htmlspecialchars(strip_tags($this->expiry_date));
        $this->location_code = htmlspecialchars(strip_tags($this->location_code));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":expiry_date", $this->expiry_date);
        $stmt->bindParam(":location_code", $this->location_code);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function read()
    {
        $query = "SELECT id, name, code, quantity, price, expiry_date, location_code, created_at FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function readOne()
    {
        $query = "SELECT id, name, code, quantity, price, expiry_date, location_code, created_at FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $row['name'];
        $this->code = $row['code'];
        $this->quantity = $row['quantity'];
        $this->price = $row['price'];
        $this->expiry_date = $row['expiry_date'];
        $this->location_code = $row['location_code'];
        $this->created_at = $row['created_at'];
    }

    function update()
    {
        $query = "UPDATE " . $this->table_name . " SET name = :name, code = :code, quantity = :quantity, price = :price, expiry_date = :expiry_date, location_code = :location_code WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->expiry_date = htmlspecialchars(strip_tags($this->expiry_date));
        $this->location_code = htmlspecialchars(strip_tags($this->location_code));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':expiry_date', $this->expiry_date);
        $stmt->bindParam(':location_code', $this->location_code);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function search($keywords)
    {
        $query = "SELECT id, name, code, quantity, price, expiry_date, location_code, created_at FROM " . $this->table_name . " WHERE name LIKE ? OR code LIKE ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);

        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->execute();
        return $stmt;
    }

    function updateQuantity($medicine_id, $new_quantity)
    {
        $query = "UPDATE " . $this->table_name . " SET quantity = :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':quantity', $new_quantity);
        $stmt->bindParam(':id', $medicine_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>