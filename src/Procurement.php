<?php

class Procurement {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function requestPurchase($medicine_name, $quantity) {
        $sql = "INSERT INTO procurement (medicine_name, quantity) VALUES (?, ?)";

        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $medicine_name, $quantity);

            if (mysqli_stmt_execute($stmt)) {
                return true;
            } else {
                return false;
            }
            mysqli_stmt_close($stmt);
        }
        return false;
    }

    public function getAllRequests() {
        $sql = "SELECT id, medicine_name, quantity, status, request_date FROM procurement ORDER BY request_date DESC";
        $result = mysqli_query($this->conn, $sql);
        $requests = [];

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $requests[] = $row;
            }
        }
        return $requests;
    }

    public function updateRequestStatus($request_id, $status) {
        $sql = "UPDATE procurement SET status = ? WHERE id = ?";

        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $status, $request_id);

            if (mysqli_stmt_execute($stmt)) {
                return true;
            } else {
                return false;
            }
            mysqli_stmt_close($stmt);
        }
        return false;
    }
}

?>