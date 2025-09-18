<?php

class Inventory {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function addMedicine($name, $code, $quantity, $expiry_date, $location_code) {
        $sql = "INSERT INTO medicines (name, code, quantity, expiry_date, location_code) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssiss", $name, $code, $quantity, $expiry_date, $location_code);

            if (mysqli_stmt_execute($stmt)) {
                return true;
            } else {
                return false;
            }
            mysqli_stmt_close($stmt);
        }
        return false;
    }

    public function getAllMedicines() {
        $sql = "SELECT id, name, code, quantity, expiry_date, location_code, created_at FROM medicines ORDER BY name ASC";
        $result = mysqli_query($this->conn, $sql);
        $medicines = [];

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $medicines[] = $row;
            }
        }
        return $medicines;
    }

    public function getMedicineById($id) {
        $sql = "SELECT id, name, code, quantity, expiry_date, location_code, created_at FROM medicines WHERE id = ?";

        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) == 1) {
                    return mysqli_fetch_assoc($result);
                }
            }
            mysqli_stmt_close($stmt);
        }
        return null;
    }

    public function updateMedicine($id, $name, $code, $quantity, $expiry_date, $location_code) {
        $sql = "UPDATE medicines SET name = ?, code = ?, quantity = ?, expiry_date = ?, location_code = ? WHERE id = ?";

        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssissi", $name, $code, $quantity, $expiry_date, $location_code, $id);

            if (mysqli_stmt_execute($stmt)) {
                return true;
            } else {
                return false;
            }
            mysqli_stmt_close($stmt);
        }
        return false;
    }

    public function deleteMedicine($id) {
        $sql = "DELETE FROM medicines WHERE id = ?";

        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                return true;
            } else {
                return false;
            }
            mysqli_stmt_close($stmt);
        }
        return false;
    }

    public function searchMedicines($search_term) {
        $sql = "SELECT id, name, code, quantity, expiry_date, location_code FROM medicines WHERE name LIKE ? OR code LIKE ? ORDER BY name ASC";
        $search_param = "%" . $search_term . "%";
        $medicines = [];

        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $search_param, $search_param);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $medicines[] = $row;
                }
            }
            mysqli_stmt_close($stmt);
        }
        return $medicines;
    }
}

?>