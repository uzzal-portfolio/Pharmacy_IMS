<?php
class MedicineCatalog
{
    private $conn;
    private $table_name = "medicine_catalog";

    public $id;
    public $name;
    public $code;
    public $medicine_group;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create new catalog entry
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, code=:code, medicine_group=:medicine_group";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->medicine_group = htmlspecialchars(strip_tags($this->medicine_group));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":medicine_group", $this->medicine_group);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Check if exists
    public function exists()
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE name = ? OR code = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->name);
        $stmt->bindParam(2, $this->code);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Search for autocomplete
    public function search($term)
    {
        $query = "SELECT name, code, medicine_group FROM " . $this->table_name . " WHERE name LIKE ? OR medicine_group LIKE ? ORDER BY name ASC LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $term = "%" . $term . "%";
        $stmt->bindParam(1, $term);
        $stmt->bindParam(2, $term);
        $stmt->execute();
        return $stmt;
    }
}
?>