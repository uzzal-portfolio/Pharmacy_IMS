<?php

class Customer {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function addCustomer($name, $phone, $email) {
        $sql = "INSERT INTO customers (name, phone, email) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $name, $phone, $email);

            if (mysqli_stmt_execute($stmt)) {
                return true;
            } else {
                return false;
            }
            mysqli_stmt_close($stmt);
        }
        return false;
    }

    public function getAllCustomers() {
        $sql = "SELECT id, name, phone, email, created_at FROM customers ORDER BY name ASC";
        $result = mysqli_query($this->conn, $sql);
        $customers = [];

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $customers[] = $row;
            }
        }
        return $customers;
    }

    public function getCustomerById($id) {
        $sql = "SELECT id, name, phone, email, created_at FROM customers WHERE id = ?";

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

    public function updateCustomer($id, $name, $phone, $email) {
        $sql = "UPDATE customers SET name = ?, phone = ?, email = ? WHERE id = ?";

        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssi", $name, $phone, $email, $id);

            if (mysqli_stmt_execute($stmt)) {
                return true;
            } else {
                return false;
            }
            mysqli_stmt_close($stmt);
        }
        return false;
    }

    public function deleteCustomer($id) {
        $sql = "DELETE FROM customers WHERE id = ?";

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

    public function getCustomerPurchaseHistory($customer_id) {
        $sql = "SELECT s.id as sale_id, m.name as medicine_name, s.quantity, s.total_price, s.sale_date 
                FROM sales s
                JOIN medicines m ON s.medicine_id = m.id
                WHERE s.customer_id = ? ORDER BY s.sale_date DESC";
        
        $history = [];
        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $customer_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $history[] = $row;
                }
            }
            mysqli_stmt_close($stmt);
        }
        return $history;
    }
}

?>