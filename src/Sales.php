<?php

class Sales {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function recordSale($medicine_id, $customer_id, $quantity, $total_price) {
        // Start transaction
        mysqli_begin_transaction($this->conn);

        try {
            // Deduct quantity from inventory
            $sql_deduct = "UPDATE medicines SET quantity = quantity - ? WHERE id = ? AND quantity >= ?";
            if ($stmt_deduct = mysqli_prepare($this->conn, $sql_deduct)) {
                mysqli_stmt_bind_param($stmt_deduct, "iii", $quantity, $medicine_id, $quantity);
                mysqli_stmt_execute($stmt_deduct);

                if (mysqli_stmt_affected_rows($stmt_deduct) == 0) {
                    throw new Exception("Not enough stock or medicine not found.");
                }
                mysqli_stmt_close($stmt_deduct);
            } else {
                throw new Exception("Error preparing statement: " . mysqli_error($this->conn));
            }

            // Record the sale
            $sql_sale = "INSERT INTO sales (medicine_id, customer_id, quantity, total_price) VALUES (?, ?, ?, ?)";
            if ($stmt_sale = mysqli_prepare($this->conn, $sql_sale)) {
                mysqli_stmt_bind_param($stmt_sale, "iiid", $medicine_id, $customer_id, $quantity, $total_price);
                mysqli_stmt_execute($stmt_sale);
                mysqli_stmt_close($stmt_sale);
            } else {
                throw new Exception("Error preparing statement: " . mysqli_error($this->conn));
            }

            // Commit transaction
            mysqli_commit($this->conn);
            return true;

        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($this->conn);
            error_log("Sale recording failed: " . $e->getMessage());
            return false;
        }
    }

    public function getAllSales() {
        $sql = "SELECT s.id, m.name as medicine_name, c.name as customer_name, s.quantity, s.total_price, s.sale_date 
                FROM sales s
                JOIN medicines m ON s.medicine_id = m.id
                LEFT JOIN customers c ON s.customer_id = c.id
                ORDER BY s.sale_date DESC";
        $result = mysqli_query($this->conn, $sql);
        $sales = [];

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $sales[] = $row;
            }
        }
        return $sales;
    }

    public function searchMedicines($search_term) {
        $sql = "SELECT id, name, code, quantity FROM medicines WHERE name LIKE ? OR code LIKE ? ORDER BY name ASC";
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