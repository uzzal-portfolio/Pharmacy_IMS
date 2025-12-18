<?php
require_once __DIR__ . '/lib/fpdf/fpdf.php';

class Report
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function generateStockReport($start_date, $end_date)
    {
        $query = "SELECT name, code, quantity, expiry_date, location_code FROM medicines WHERE created_at BETWEEN :start_date AND :end_date ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function generateSalesReport($start_date, $end_date)
    {
        $query = "SELECT s.transaction_id, m.name as medicine_name, c.name as customer_name, s.quantity, s.total_price, s.discount, s.payment_method, (s.total_price - s.discount) as final_price, s.sale_date FROM sales s LEFT JOIN medicines m ON s.medicine_id = m.id LEFT JOIN customers c ON s.customer_id = c.id WHERE s.sale_date BETWEEN :start_date AND :end_date ORDER BY s.sale_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function generateExpiryReport($start_date, $end_date)
    {
        $query = "SELECT name, code, quantity, expiry_date, location_code FROM medicines WHERE expiry_date BETWEEN :start_date AND :end_date ORDER BY expiry_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function generateAuditReport($start_date, $end_date)
    {
        $query = "SELECT al.id, u.username, al.action, al.log_date FROM audit_log al LEFT JOIN users u ON al.user_id = u.id WHERE al.log_date BETWEEN :start_date AND :end_date ORDER BY al.log_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function generateProcurementReport($start_date, $end_date)
    {
        $query = "SELECT medicine_name, quantity, status, request_date FROM procurement WHERE request_date BETWEEN :start_date AND :end_date ORDER BY request_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>