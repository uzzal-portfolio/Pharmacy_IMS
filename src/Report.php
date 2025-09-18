<?php

class Report {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function getStockReport() {
        $sql = "SELECT name, code, quantity, expiry_date, location_code FROM medicines ORDER BY name ASC";
        $result = mysqli_query($this->conn, $sql);
        $stock_data = [];

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $stock_data[] = $row;
            }
        }
        return $stock_data;
    }

    public function getExpiryReport($start_date = null, $end_date = null) {
        $sql = "SELECT name, code, quantity, expiry_date, location_code FROM medicines WHERE expiry_date < CURDATE()";
        if ($start_date && $end_date) {
            $sql = "SELECT name, code, quantity, expiry_date, location_code FROM medicines WHERE expiry_date BETWEEN ? AND ? ORDER BY expiry_date ASC";
            if ($stmt = mysqli_prepare($this->conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $start_date, $end_date);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
            }
        } else {
            $sql = "SELECT name, code, quantity, expiry_date, location_code FROM medicines WHERE expiry_date < CURDATE() ORDER BY expiry_date ASC";
            $result = mysqli_query($this->conn, $sql);
        }

        $expiry_data = [];
        if (isset($result) && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $expiry_data[] = $row;
            }
        }
        return $expiry_data;
    }

    public function getSalesReport($start_date, $end_date) {
        $sql = "SELECT s.id as sale_id, m.name as medicine_name, c.name as customer_name, s.quantity, s.total_price, s.sale_date 
                FROM sales s
                JOIN medicines m ON s.medicine_id = m.id
                LEFT JOIN customers c ON s.customer_id = c.id
                WHERE s.sale_date BETWEEN ? AND ? ORDER BY s.sale_date DESC";
        
        $sales_data = [];
        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $start_date, $end_date);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $sales_data[] = $row;
                }
            }
            mysqli_stmt_close($stmt);
        }
        return $sales_data;
    }

    public function getProcurementReport($start_date, $end_date) {
        $sql = "SELECT id, medicine_name, quantity, status, request_date FROM procurement WHERE request_date BETWEEN ? AND ? ORDER BY request_date DESC";
        
        $procurement_data = [];
        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $start_date, $end_date);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $procurement_data[] = $row;
                }
            }
            mysqli_stmt_close($stmt);
        }
        return $procurement_data;
    }

    public function getCustomerReport() {
        $sql = "SELECT id, name, phone, email, created_at FROM customers ORDER BY name ASC";
        $result = mysqli_query($this->conn, $sql);
        $customer_data = [];

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $customer_data[] = $row;
            }
        }
        return $customer_data;
    }
}

?>